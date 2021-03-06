<?php

class SessionController extends Zend_Controller_Action {
    
    protected $auth;
    protected $accounts;
    protected $places;
    
    public function init()
    {
        $this->places = new WS_PlacesService();
        
        $this->view->lang = $this->_getParam('lang');
    }
    
    public function logoutAction()
    {
        $this->auth = Zend_Auth::getInstance();
        if($this->auth->hasIdentity()){
            $user = $this->auth->getIdentity();
            WS_Log::info($user->id . ' has logged out');
            
            $this->auth->clearIdentity();
        }
        $this->_redirect('/provider/signup');
    }
    
    public function loginAction()
    {
        $this->auth = Zend_Auth::getInstance();
        if($this->auth->hasIdentity()){
            $user = $this->auth->getIdentity();
            $role = $user->role_id;
            switch($role){
                case 1:
                    $this->_redirect('/'); break;
                case 2:
                    $this->_redirect('/user'); break;
                case 3:
                    $this->_redirect('/provider'); break;
                case 4:
                    $this->_redirect('/admin'); break;
                default:
                    $this->_redirect('/'); break;
            }
        } else {
            if($this->getRequest()->isPost()){
                $errors = $this->loginPostHandler();
                $this->view->errors = $errors;
                $this->view->email  = $_POST['email'];
            }
        }
        
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->alerts = $flashMessenger->getMessages();
    }
    
    public function loginPostHandler()
    {
        try {
            if(empty($_POST['email']))
                throw new Exception('Email cannot be empty');
            if(empty($_POST['password']))
                throw new Exception('Password cannot be empty');

            $email = new Zend_Validate_EmailAddress();
            if(!$email->isValid($_POST['email']))
                throw new Exception('Invalid Emil Address');

            $this->accounts = new WS_AccountService();
            $result = $this->accounts->login($_POST['email'], $_POST['password']);
            if($result === true){
                $user = $this->auth->getIdentity();
                
                WS_Log::info($user->id . ' has logged in');
                
                $role = $user->role_id;
                if(!empty($_POST['return_url']))
                    $this->_redirect ($_POST['return_url']);            
                switch($role){
                    case 1:
                        $this->_redirect('/'); break;
                    case 2:
                        $this->_redirect('/'); break;
                    case 3:
                        $this->_redirect('/provider'); break;
                    case 4:
                        $this->_redirect('/admin'); break;
                    default:
                        $this->_redirect('/'); break;
                }
            }
        } 
        catch (Exception $e) {
            $result[] = $e->getMessage();
        }
        return $result;
    }
    
    public function signupAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $user = $auth->getIdentity();
            $role = $user->role_id;
            switch($role){
                case 1:
                    $this->_redirect('/'); break;
                case 2:
                    $this->_redirect('/user'); break;
                case 3:
                    $this->_redirect('/provider'); break;
                case 4:
                    $this->_redirect('/admin'); break;
                default:
                    $this->_redirect('/'); break;
            }
        }
        /**
        $facebook = new Fb_Facebook(array(
            'appId' => '197692283624882',
            'secret'=> 'e106317fb868e53208973867a190dbb2'
        ));
        
        $user = $facebook->getUser();
        if ($user or $this->getRequest()->isPost()) {
          $this->signupPostHandler();
        }
        
        $loginUrl = $facebook->getLoginUrl(array('scope'=>'email,publish_stream'));
         * 
         */
        
        $this->view->fbLoginUrl = '/signup/facebook';
        $this->view->countries  = $this->places->getPlaces(2);
        
        if($this->getRequest()->isPost()) {
            $errors = $this->signupPostHandler();
            $this->view->errors = $errors;
            
            if(!empty($_POST['name']) && $_POST['name'] != 'Name')
                $this->view->name = $_POST['name'];
            if(!empty($_POST['lname']) && $_POST['lname'] != 'Last Name')
                $this->view->lname = $_POST['lname'];
            if(!empty($_POST['email']) && $_POST['email'] != 'E-Mail')
                $this->view->email = $_POST['email'];
            
            if(!empty($_POST['country_id']))
                $this->view->country = $_POST['country_id'];
            
        }
    }
    
    public function signupPostHandler()
    {
        try {
            $errors = array();
            //var_dump($_POST); die;
            if(empty($_POST['name']))
                $errors[] = 'Company name cannot be empty';
                
            if(empty($_POST['email']))
                $errors[] = 'Email cannot be empty';
                
            if(empty($_POST['phone']))
                $errors[] = 'Phone cannot be empty';
                
            if(empty($_POST['website']))
                $errors[] = 'Website cannot be empty';
                
            if(empty($_POST['country']))
                $errors[] = 'Select a country';
                

            $email = new Zend_Validate_EmailAddress();
            if(!$email->isValid(trim($_POST['email'])))
                $errors[] = 'Invalid Emil Address';
            
            if(!Zend_Uri::check(trim($_POST['website'])))
                $errors[] = 'Invalid Website try adding http://';
            
            if(count($errors) > 0) {
                $notifier = new WS_Notifier();
                $to      = 'cristian@tripfab.com; ricardo@tripfab.com';
                $subject = 'New Invitation Request';
                $message = 'New signup from: '.$_POST['company']."\n\n";
                $message.= 'Contact: '.$_POST['contact']."\n";
                $message.= 'Company: '.$_POST['name']."\n";
                $message.= 'Email: '.$_POST['email']."\n";
                $message.= 'Phone: ('.$_POST['code'].') '.$_POST['phone']."\n";
                $message.= 'Website: '.$_POST['website']."\n";
                $message.= 'Business: '.$_POST['business']."\n";
                $message.= 'Country: '.$_POST['country']."\n\n";
                $message.= 'The account for this company wasnt created beacuase there is information missing';

                $notifier->sendEmail($to, $subject, $message);
                WS_Log::info($_POST['email'] . ' has requested an Invitation');
            } else {
                $this->accounts = new WS_AccountService();
                if($this->accounts->validateEmail(trim($_POST['email']))){
                    $data = $_POST;
                    $country = explode($_POST['country'],'-');
                    $data['country_id'] = trim($country[1]);
                    $data['password']   = substr(md5($_POST['email']), 3, 12);
                    $this->accounts->signupVendor($data, false);
                    $password = 'The password for this account is '. $data['password'];
                } else {
                    $password = 'The account for this company wasnt created beacuase seems like it already exists in the database';
                }
                
                $notifier = new WS_Notifier();
                $to      = 'cristian@tripfab.com; ricardo@tripfab.com';
                $subject = 'New Invitation Request';
                $message = 'New signup from: '.$_POST['company']."\n\n";
                $message.= 'Contact: '.$_POST['contact']."\n";
                $message.= 'Company: '.$_POST['name']."\n";
                $message.= 'Email: '.$_POST['email']."\n";
                $message.= 'Phone: ('.$_POST['code'].') '.$_POST['phone']."\n";
                $message.= 'Website: '.$_POST['website']."\n";
                $message.= 'Business: '.$_POST['business']."\n";
                $message.= 'Country: '.$_POST['country']."\n\n";
                $message.= $password;

                $notifier->sendEmail($to, $subject, $message);
                WS_Log::info($_POST['email'] . ' has requested an Invitation');
            }
        } catch(Exception $e) {
            return array($e->getMessage());
        }
        /*
        $errors = array();
        if(empty($_POST['name']) || $_POST['name'] == 'Name')
            $errors[] = 'Name cannot be empty';
        if(empty($_POST['lname']) || $_POST['lname'] == 'Last Name')
            $errors[] = 'Lastname cannot be empty';
        if(empty($_POST['email']) || $_POST['email'] == 'E-Mail')
            $errors[] = 'Email cannot be empty';
        if(empty($_POST['password']) || $_POST['password'] == 'Password')
            $errors[] = 'Password cannot be empty';
        if($_POST['password2'] != $_POST['password'])
            $errors[] = 'Password confirmation fallure';
        if(empty($_POST['country_id']))
            $errors[] = 'Select a country';
        
        $email = new Zend_Validate_EmailAddress();
        if(!$email->isValid($_POST['email']))
            $errors[] = 'Invalid Email Address';
        
        if(count($errors) > 0)
            return $errors;
        
        $this->accounts = new WS_AccountService();
        if($this->accounts->validateEmail($_POST['email'])){
            $this->accounts->signup($_POST);
            $this->_redirect('/');
        }
        
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $flashMessenger->addMessage('Seem like you already have an account');
        $this->_redirect('/login');
        
        /**
        $facebook = new Fb_Facebook(array(
            'appId' => '197692283624882',
            'secret'=> 'e106317fb868e53208973867a190dbb2'
        ));
        
        $user = $facebook->getUser();
        if($user){
            $user_profile = $facebook->api('/me');
            if($this->accounts->validateEmail($user_profile['email'])){
                $this->accounts->signup($user_profile, true);
                $this->_redirect('/signup/friends');
            }
            else
                $this->view->errorMessage = 'This email already exists';
        } else {
            if($this->accounts->validateEmail($user_profile['email'])){
                $this->accounts->signup($user_profile);
                $this->_redirect('/signup/friends');
            }
            else
                $this->view->errorMessage = 'This email already exists';
        }
         * 
         */
    }
    
    public function signupfacebookAction(){
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
                $this->_redirect('user');

        $this->accounts = new WS_AccountService();
        
        $facebook = new Fb_Facebook(array(
            'appId' => '197692283624882',
            'secret'=> 'e106317fb868e53208973867a190dbb2'
        ));
        
        $user = $facebook->getUser();
        
        if($user){
            $user_profile = $facebook->api('/me');
            if($this->accounts->validateEmail($user_profile['email'])){
                $this->accounts->signup($user_profile, true);
                $this->_redirect('/');
            } else {
                $this->accounts->login($user_profile, null, true);
                $this->_redirect('/');
            }
        } else {
            //echo "al fin"; die;
            $loginUrl = $facebook->getLoginUrl(array('scope'=>'email,publish_stream'));
            $this->_redirect($loginUrl);
        }        
    }
    
    
    
    public function vendorAction()
    {
        
    }
    
    public function vendorsignupAction()
    {
        
    }
}

?>
