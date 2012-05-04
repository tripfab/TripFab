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
        $userRole = 0;
        $this->auth = Zend_Auth::getInstance();
        if($this->auth->hasIdentity()){
            $userInfo = Zend_Auth::getInstance()->getStorage()->read();
            $userRole = $userInfo->role_id;
            $accounts = new WS_AccountService();
            $accounts->logUserAccessTime(true);
            $this->auth->clearIdentity();
        }
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            header("Location: http://" . $_SERVER["SERVER_NAME"]);
            exit();
        }
    }
    
    public function loginAction()
    {
        $this->_requireSSL();
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
        
        $returnUrl = "";
        if(isset($_GET['b']))
            $returnUrl = $_GET['b'];
        elseif(isset($_POST['returnURL'])) 
            $returnUrl = $_POST['returnURL'];
        
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->alerts = $flashMessenger->getMessages();
        
        $this->view->returnUrl = $returnUrl;
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
                $role = $user->role_id;
                if(!empty($_POST['returnURL']))
                    $this->_redirect ($_POST['returnURL']);            
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
            $result[] = 'Incorrect Email or Password';
        }
        return $result;
    }
    
    public function signupAction()
    {
        $this->_requireSSL();
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
            $this->_redirect('/thanks');
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
        
        $conf = Zend_Registry::get('facebook');
        
        $facebook = new Fb_Facebook(array(
            'appId' => $conf['id'],
            'secret'=> $conf['secret'],
        ));
        
        $user = $facebook->getUser();
        
        if($user){
            $user_profile = $facebook->api('/me');
            if($this->accounts->validateEmail($user_profile['email'], true)){
                $this->accounts->signup($user_profile, true);
                $this->_redirect('/thanks');
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
    
    public function thanksAction()
    {
        
    }
    
    public function confirmemailAction()
    {
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $email = $this->_getParam('email','default');
        if($email == 'default') 
                throw new Exception('No email provided');
        
        $_token = $this->_getParam('token','default');
        if($_token == 'default') 
                throw new Exception('No token provided');
        
        $users = new Model_Users();
        $select = $users->select();
        $select->where('email = ?', $email);
        $select->where('active = ?', 0);
        $user = $users->fetchRow($select);
        
        if(is_null($user)) 
                throw new Exception('User not found or already activated');
        
        $tokens = new Zend_Db_Table('activation_tokens');
        $select = $tokens->select();
        $select->where('user_id = ?', $user->id);
        $select->where('token = ?', $_token);
        $token = $tokens->fetchRow($select);
        
        if(is_null($token)) 
                throw new Exception('Token not found');
        
        $user->active = 1;
        $user->save();

        $token->updated = date('Y-m-d H:i:s');
        $token->save();

        setcookie('alert','Your account has been activated');
        $this->_redirect('/login');
    }
    
    private function _requireSSL()
    {
        if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
            exit();
        }
    }
}

?>
