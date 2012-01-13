<?php

class AccountController extends Zend_Controller_Action {
 
    /**
     *
     * @var WS_User
     */
    protected $user;
    
    /**
     *
     * @var WS_MessagesService
     */
    protected $messages;
    
    /**
     *
     * @var WS_ReservationsService
     */
    protected $reservations;
    
    /**
     *
     * @var WS_FeedsService
     */
    protected $feeds;
    
    /**
     *
     * @var WS_ListingService
     */
    protected $listings;
    
    /**
     *
     * @var WS_PlacesService
     */
    protected $places;
    
    /**
     *
     * @var WS_PublicQuestionsService
     */
    protected $public_questions;
    
    /**
     *
     * @var WS_ReviewsService
     */
    protected $reviews;
    
    /**
     *
     * @var WS_UsersService
     */
    protected $users;
    
    /**
     *
     * @var WS_AccountService
     */
    protected $accounts;
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            $this->_redirect('login');
            //var_dump($_SESSION); die;
        } else {
            $this->user = new WS_User($auth->getStorage()->read());
            if($this->user->getRole() != 'user'){
                throw new Exception('no access allowed');
            } else {
                $this->messages         = new WS_MessagesService();
                $this->reservations     = new WS_ReservationsService();
                $this->feeds            = new WS_FeedsService();
                $this->listings         = new WS_ListingService();
                $this->places           = new WS_PlacesService();
                $this->public_questions = new WS_PublicQuestionsService();
                $this->reviews          = new WS_ReviewsService();
                $this->users            = new WS_UsersService();
                $this->accounts         = new WS_AccountService();
            }
        }
    }
    
    public function indexAction()
    {
        if($this->getRequest()->isPost())
            try{
                $this->indexPostHandler();
                $this->view->successmessage = 'Your changes has been applied';}
            catch(Exception $e){
                $this->view->errormessage = $e->getMessage();}
        
        $user             = $this->user->getData(true);
        $new_messages     = $this->messages->countNew($this->user->getId());
        $stats            = $this->users->getStats($this->user->getId());
        $countries        = $this->places->getPlaces(2);
        $questions        = $this->user->getDefaultQuestions();
        
        if($user->city_id)
                $cities   = $this->places->getPlaces (3, $user->country_id);
        
        $this->view->new_messages = $new_messages;
        $this->view->user         = $user;
        $this->view->stats        = $stats;
        $this->view->countries    = $countries;
        $this->view->sq           = $questions;
        if($cities)
            $this->view->cities = $cities;
        
    }
    
    private function indexPostHandler()
    {
        $data = $_POST;
        $user = $this->user->getData();
        if($user->id != $data['userids'])
            throw new Exception ('Form corrupted');
        if($user->token != $data['usertokens'])
            throw new Exception ('Form corrupted');
        if(md5($user->token.'update_user') != $data['formid'])
            throw new Exception ('Form corrupted');
        if(md5('update_user') != $data['_task'])
            throw new Exception ('Form corrupted');
        
        $this->validateData($_POST, $user);
        
        unset($data['file_upload']);
        unset($data['opassword']);
        unset($data['npassword']);
        unset($data['rpassword']);
        unset($data['userids']);
        unset($data['usertokens']);
        unset($data['formid']);
        unset($data['_task']);
        
        if(!empty($data['country_id'])){
            $country = $this->places->getPlaceById($data['country_id']);
            if(!empty($data['city_id'])){
                $city = $this->places->getPlaceById($data['city_id']);
                if($city->parent_id != $country->id)
                    throw new Exception();
                $data['location'] = $city->title . ', ' . $country->title;
            } else 
                $data['location'] = $country->title;
        } else 
            $data['location'] = '';
        if(!empty($data['birthdate']))
            $data['birthdate'] = date('Y-m-d', strtotime($data['birthdate']));
        
        $completeFacts = array('location', 'languages', 'work', 'birthdate', 'phone','gender');
        $complete = $user->complete;
        $user_arr = $user->toArray();
        
        foreach($completeFacts as $fact){
            if($data[$fact] != ''){
                if($user_arr[$fact]==''){
                    $complete = $complete + 10;
                }
            }
            else{
                if($user_arr[$fact] != ''){
                    $complete = $complete - 10;
                }
            }
        }
        
        $data['complete'] = $complete;
        if($complete==100){
            if($user->complete != 100){
                $data['points'] = $user->points + 10;
            }
        }
        else{
            if($user->complete == 100){
                $data['points'] = $user->points - 10;
            }
        }
        
        $users = new Model_Users();
        $users->update($data, "id = {$user->id}");
    }
    
    private function validateData($data, $user)
    {
        if(empty($data['name']))
            throw new Exception ('Name cannot be empty');
        if(empty($data['email']))
            throw new Exception ('Email cannot be empty');
        if(empty($data['username']))
            throw new Exception ('Username cannot be empty');            
        
        $email = new Zend_Validate_EmailAddress();
        if(!$email->isValid($data['email'])){
            $messages = $email->getMessages();
            foreach($messages as $msg)
                throw new Exception($msg);
        }
        
        if($user->email != $data['email'])
            if(!$this->accounts->validateEmail($data['email']))
                throw new Exception ('This email its being user for another user');
            
        if(!ctype_alnum($data['username']))
            throw new Exception ('Username may contain just numbers and letters');
        
        if($user->username != $data['username'])
            if(!$this->accounts->validateUsername($data['username']))
                throw new Exception ('This username its being user for another user');
        
        $gender    = array('','Male','Female');
        $languages = array('','English', 'Spanish');
        
        if(!in_array($data['gender'], $gender))
            throw new Exception ('Invalid Gender');
        
        if(!in_array($data['languages'], $languages))
            throw new Exception ('Invalid Language');
        
    }
    
    public function connectionsAction()
    {
        $user             = $this->user->getData(true);
        $new_messages     = $this->messages->countNew($this->user->getId());
        $stats            = $this->users->getStats($this->user->getId());
        $countries        = $this->places->getPlaces(2);
        
        $template = 'connections';
        
        switch($this->_getParam('task')){
            case 'facebook':
                $this->connectionsFacebookTask();
                $template = 'connections-facebook';
                break;
            case 'twitter':
                break;
            case 'default':
                break;
            default:
                throw new Exception('Page not found');
                break;
        }
                
        $this->view->new_messages = $new_messages;
        $this->view->user         = $user;
        $this->view->stats        = $stats;
        $this->view->countries    = $countries;
        
        $this->render($template);
    }
    
    private function connectionsFacebookTask()
    {
        $tf_user = $this->user->getData(true);
        if(!empty($tf_user->facebook_id))
                $this->_redirect ('/user/account/connections');
        
        $stats = $this->users->getStats($tf_user->id);
        //echo "Hola";
        $facebook = new Fb_Facebook(array(
            'appId' => '197692283624882',
            'secret'=> 'e106317fb868e53208973867a190dbb2'
        ));
        
        $logout = $facebook->getLogoutUrl();
        //$this->_redirect($logout);
        
        $user = $facebook->getUser();
        //echo $facebook->getAccessToken();
        //var_dump($user);
        $users = new Model_Users();
        if($user){
            $user_profile = $facebook->api('/me');
            $tf_user->facebook_id = $user_profile['id'];
            $tf_user->points = $tf_user->points + 10;
            $tf_user->save();
            $friends      = $facebook->api('/me/friends');
            //var_dump($user_profile); die;
            //var_dump($friends); die;
            $friends = $friends['data'];
            $ontripfab = array();
            $yourfriends = array();
            $invite = array();
            foreach($friends as $f){
                $u = $users->fetchRow('facebook_id = '.$f['id']);
                if(is_null($u)){
                    $invite[] = $f;
                } else {
                    $userfriends = new Model_UserFriends();
                    $where = "(user1_id = {$this->user->getId()} and user2_id = {$u->id}) or (user2_id = {$this->user->getId()} and user1_id = {$u->id})";
                    $relation = $userfriends->fetchRow($where);
                    if(is_null($relation)){
                        $userfriends->insert(array(
                            'user1_id'=>$this->user->getId(),
                            'user2_id'=>$u->id,
                            'state'=>1,
                            'created'=>date('Y-m-d g:i:s'),
                            'updated'=>date('Y-m-d g:i:s')
                        ));
                        $stats->friends = $stats->friends + 1;
                        $yourfriends[] = $f;
                    } else {
                        $yourfriends[] = $f;
                    }
                }
            }
            
            $stats->save();
            
            $this->view->already_friends = $yourfriends;
            $this->view->already_ontripfab = $ontripfab;
            $this->view->send_invites   = $invite;
        } else {
            $loginUrl = $facebook->getLoginUrl(array('scope'=>'email,publish_stream'));
            $this->_redirect($loginUrl);
        }
    }
    
    public function notificationsAction()
    {
        $user             = $this->user->getData(true);
        $new_messages     = $this->messages->countNew($this->user->getId());
        $stats            = $this->users->getStats($this->user->getId());
        $countries        = $this->places->getPlaces(2);
        
        $notifications = $this->user->getDefaultNotifications();
        if($this->getRequest()->isPost())
            try{
                $this->notificationsPostHandler();
                $this->view->successmessage = 'Your changes has been applied';
            } catch(Exception $e){
                $this->view->errorsmessage  = 'Something went wrong';
            }
            
            
        $usersettings  = $this->user->getNotificationsSettings();
        //echo '<pre>'; var_dump($notifications); echo '</pre>'; die;
        $this->view->notifications = $notifications;
        $this->view->usersettings  = $usersettings;
                
        $this->view->new_messages = $new_messages;
        $this->view->user         = $user;
        $this->view->stats        = $stats;
        $this->view->countries    = $countries;
    }
    
    private function notificationsPostHandler()
    {
        $data = $_POST;
        $user = $this->user->getData();
        if($user->id != $data['userids'])
            throw new Exception ('Form corrupted');
        if($user->token != $data['usertokens'])
            throw new Exception ('Form corrupted');
        if(md5($user->token.'user_form_notifications') != $data['formid'])
            throw new Exception ('Form corrupted');
        if(md5('update_user_notifications') != $data['_task'])
            throw new Exception ('Form corrupted');
        
        $this->user->setNotifications($data);
    }
    
    public function passwordAction()
    {
        if($this->getRequest()->isPost())
            try{
                $this->passwordPostHandler();
                $this->view->successmessage = 'Your changes has been applied';}
            catch(Exception $e){
                $this->view->errormessage = $e->getMessage();}
                
        $user             = $this->user->getData(true);
        $new_messages     = $this->messages->countNew($this->user->getId());
        $stats            = $this->users->getStats($this->user->getId());
        $questions        = $this->user->getDefaultQuestions();
        
        $this->view->new_messages = $new_messages;
        $this->view->user         = $user;
        $this->view->stats        = $stats;
        $this->view->sq           = $questions;
    }
    
    private function passwordPostHandler()
    {
        $data = $_POST;
        $user = $this->user->getData();
        if($user->id != $data['userids'])
            throw new Exception ('Form corrupted');
        if($user->token != $data['usertokens'])
            throw new Exception ('Form corrupted');
        if(md5($user->token.'change_password') != $data['formid'])
            throw new Exception ('Form corrupted');
        if(md5('change_password') != $data['_task'])
            throw new Exception ('Form corrupted');
        
        if(!empty($data['opassword'])){
        
            if(md5($data['opassword']) != $user->password)
                throw new Exception ('Wrong password, try again');
            
            if(empty($data['npassword']))
                throw new Exception ('New Password cannot be empty');
            
            if($data['npassword'] != $data['rpassword'])
                throw new Exception ('New password is different that Retype Password');

            $user->password = md5($data['npassword']);
        }
        
        if(empty($data['question']))
            throw new Exception ('Select a Security Question');
        if(empty($data['answer']))
            throw new Exception ('Write an answer for your security question');
        
        $user->question = $data['question'];
        $user->answer   = $data['answer'];
        
        $user->save();
    }
}
