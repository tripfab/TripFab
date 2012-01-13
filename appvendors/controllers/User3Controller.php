<?php

class UserController extends Zend_Controller_Action 
{
    /**
     *
     * @var WS_User
     */
    protected $user;
    protected $messages;
    
    /**
     *
     * @var WS_ReservationsService
     */
    protected $reservations;
    protected $feeds;
    protected $listings;
    protected $places;
    protected $public_questions;
    protected $reviews;
    
    /**
     *
     * @var WS_UsersService
     */
    protected $users;
    
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
        /**
        $new_messages     = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countConfirmed($this->user->getId());
        $new_answers      = $this->public_questions->countNew($this->user->getId());
        
        $feeds = $this->feeds->getFeedsFor($this->user->getId());
        
        $show_alerts = false;
        if($new_messages > 0 || $new_reservations > 0 || $new_answers > 0)
            $show_alerts = true;
        
        $this->view->alerts           = $show_alerts;
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->new_answers      = $new_answers;
        $this->view->user             = $this->user->getData();
        $this->view->feeds            = $feeds;
         * 
         */
        $user = $this->user->getData();
        
        $this->view->user = $user;
        
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
        
        $places = $this->users->getCountries($user->id);
        $this->view->places = $places;
        
        $albums = $this->users->getAlbumsOf($user->id);
        if(!is_null($albums))
            $this->view->albums = $albums;
        
        $itineraries = $this->users->getItineraries($user->id, 3);
        if(!is_null($itineraries))
            $this->view->itineraries = $itineraries;
        
        $friends = $this->user->getFriends();
        if(count($friends) > 0)
            $this->view->friends = $friends;
        
        $stats = $this->users->getStats($user->id);
        $this->view->stats = $stats;
    }
    
    /**
    public function inboxAction()
    {
        if($this->getRequest()->isPost())
            $this->inboxPostHandler();
        
        switch ($this->_getParam('task')){
            case 'unread':
                $messages = $this->messages->getMessagesOf($this->user->getId(),'unread');
                $this->view->active_tab = 'unread';
                break;
            case 'starred':
                $messages = $this->messages->getMessagesOf($this->user->getId(),'starred');
                $this->view->active_tab = 'starred';
                break;
            case 'sent':
                $messages = $this->messages->getMessagesOf($this->user->getId(),'sent');
                $this->view->active_tab = 'sent';
                break;
            case 'trash':
                $messages = $this->messages->getMessagesOf($this->user->getId(),'trash');
                $this->view->active_tab = 'trash';
                break;
            default:
                $messages = $this->messages->getMessagesOf($this->user->getId());
                $this->view->active_tab = 'all';
                break;
        }
        
        $new_messages     = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countConfirmed($this->user->getId());
        $new_answers      = $this->public_questions->countNew($this->user->getId());
        
        $this->view->messages         = $messages;
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->new_answers      = $new_answers;
    }
    
    public function reservationsAction()
    {
        $new_messages     = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countConfirmed($this->user->getId());
        $new_answers      = $this->public_questions->countNew($this->user->getId());
        
        $template = 'reservations';
        switch($this->_getParam('task')){
            case 'pending':
                $_reservs   = $this->reservations->getUserPending($this->user->getId());
                $template = 'reservations-pending';
                break;
            case 'history':
                $_reservs   = $this->reservations->getUserHistory($this->user->getId());
                $template = 'reservations-history';
                break;
            case 'default':
                $_reservs   = $this->reservations->getUserConfirmed($this->user->getId());
                $template = 'reservations';
                break;
            default:
                throw new Exception();
        }
        
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->new_answers      = $new_answers;
        $_reservs   = $this->reservations->getUserConfirmed($this->user->getId());
        $this->view->reservations     = $_reservs;
        
        $this->render($template);
    }
    
    public function reviewsAction()
    {
        $new_messages     = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countConfirmed($this->user->getId());
        $new_answers      = $this->public_questions->countNew($this->user->getId());
        
        $reviews = $this->reviews->getReviewsBy($this->user->getId());
        
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->new_answers      = $new_answers;
        $this->view->reviews          = $reviews;
    }
    
    public function questionsAction()
    {
        $new_messages     = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countConfirmed($this->user->getId());
        $new_answers      = $this->public_questions->countNew($this->user->getId());
        
        $template = 'questions';
        switch($this->_getParam('task')){
            case 'pending':
                $questions = $this->public_questions->getPostedBy($this->user->getId(), true);
                $template = 'questions-pending';
                break;
            case 'default':
                $questions = $this->public_questions->getPostedBy($this->user->getId());
                $template = 'questions';
                break;
            default:
                throw new Exception();
        }
        
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->new_answers      = $new_answers;
        $this->view->questions        = $questions;
        $this->view->user             = $this->user->getData();
        
        $this->render($template);
    }
    
    public function accountAction()
    {
        $new_messages     = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countConfirmed($this->user->getId());
        $new_answers      = $this->public_questions->countNew($this->user->getId());
        
        $template = 'questions';
        switch($this->_getParam('task')){
            case 'notifications':
                $this->accountNotificationsTask();
                $template = 'account-notifications';
                break;
            case 'payments':
                $template = 'account-payments';
                break;
             case 'purchases':
                $template = 'account-purchases';
                break;
             case 'default':
                $this->accountSettingsTask();
                $template = 'account';
                break;
            default:
                throw new Exception();
        }
        
        
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->new_answers      = $new_answers;
        $this->view->user             = $this->user->getData();
        
        $this->render($template);
    }
    
    private function accountSettingsTask(){
        if($this->getRequest()->isPost())
            $this->accountSettingsPostHandler();
        
        $questions = $this->user->getDefaultQuestions();
        $countries = $this->places->getPlaces(2);
        $cities    = $this->places->getPlaces(3, $this->user->getCountryId());
        
        $this->view->sq        = $questions;
        $this->view->countries = $countries;
        $this->view->cities    = $cities;
    }
    
    private function accountSettingsPostHandler()
    {
        switch ($_POST['_task']){
            case md5('update_photo'):
                $this->validatePostData('user_form_photo');
                if (!empty($_FILES)) {
                    $tempFile   = $_FILES['picture']['tmp_name'];
                    $targetPath = realpath(APPLICATION_PATH.'/../public_html/images/users/');
                    $targetFile = str_replace('//','/',$targetPath) .'/'. $this->user->getId().substr(md5($this->user->getToken()), 0, 10) . '.jpg';
                    mkdir(str_replace('//','/',$targetPath), 0755, true);
                    move_uploaded_file($tempFile,$targetFile);
                    $this->user->setImage($this->user->getId().substr(md5($this->user->getToken()), 0, 10) . '.jpg');
                }
                break;
            case md5('update_name'):
                $this->validatePostData('user_form_name');
                if(!empty($_POST['name']))
                    $this->user->setName($_POST['name']);
                else
                    throw new Exception();
                break;
            case md5('update_email'):
                $this->validatePostData('user_form_email');
                if(!empty($_POST['email']))
                    $this->user->setEmail($_POST['email']);
                else
                    throw new Exception();
                break;
            case md5('update_password'):
                $this->validatePostData('user_form_password');
                if(!empty($_POST['password']) &&
                        !empty($_POST['new_password']) &&
                            $_POST['new_password'] == $_POST['new_password2'])
                    $this->user->setPasword($_POST);                    
                else
                    throw new Exception();
                break;
            case md5('update_phone'):
                $this->validatePostData('user_form_phone');
                $this->user->setPhone($_POST['phone']);
                break;
            case md5('update_gender'):
                $this->validatePostData('user_form_gender');
                $this->user->setGender($_POST['gender']);
                break;
            case md5('update_birthdate'):
                $this->validatePostData('user_form_birthdate');
                $this->user->setBirthDate($_POST['birthdate']);
                break;
            case md5('update_location'):
                $this->validatePostData('user_form_location');
                $this->user->setLocation($_POST);
                break;
            case md5('update_work'):
                $this->validatePostData('user_form_work');
                $this->user->setWork($_POST['work']);
                break;
            case md5('update_languages'):
                $this->validatePostData('user_form_languages');
                $this->user->setLanguages($_POST['languages']);
                break;
            case md5('update_question'):
                $this->validatePostData('user_form_question');
                $this->user->setQuestion($_POST);
                break;
            default:
                throw new Exception();
        }
    }
    
    private function validatePostData($fid)
    {
        $data = $_POST;
        if($data['user_token'] != $this->user->getToken())
            throw new Exception();
        if($data['form_id'] != md5($this->user->getToken().$fid))
            throw new Exception();
    }
    
    private function accountNotificationsTask()
    {
        $notifications = $this->user->getDefaultNotifications();
        if($this->getRequest()->isPost())
            $this->accountNotificationsPostHandler();
            
        $usersettings  = $this->user->getNotificationsSettings();
        //echo '<pre>'; var_dump($notifications); echo '</pre>'; die;
        $this->view->notifications = $notifications;
        $this->view->usersettings  = $usersettings;
    }
    
    private function accountNotificationsPostHandler()
    {
        $this->validatePostData('user_form_notifications');
        $this->user->setNotifications($_POST);
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
    }
     * 
     */
    
    public function messagesAction()
    {
        $new_messages = $this->messages->countNew($this->user->getId());
        $conversations = $this->messages->getConversationOf($this->user->getId());
        
        //var_dump($conversations->toArray()); die;
        
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
        
        $this->view->new_messages  = $new_messages;
        $this->view->conversations = $conversations;
        $this->view->user          = $this->user->getData();
    }
    
    public function photosAction()
    {
        $template = 'photos';
        
        if($this->_getParam('task') == 'albums'){
            if($this->_getParam('id','default') != 'default') {
                $this->displayAlbumTask();
                $template = 'albums-single';
            } else {
                $this->photoAlbumsTask();
                $template = 'albums';
            }
        } else {
            $pictures = $this->users->getPicturesBy($this->user->getId());
            $this->view->photos = $pictures;
        }
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
        
        $this->view->user = $this->user->getData();
        
        $this->render($template);
    }
    
    private function photoAlbumsTask()
    {
        if($this->getRequest()->isPost()){
            switch($_POST['_task']){
                case md5('create'): 
                    $this->createNewAlbum();
                    break;
                default:
                    break;
            }
        }

        $albums = $this->users->getAlbumsOf($this->user->getId());
        $this->view->albums = $albums;
    }
    
    private function createNewAlbum()
    {
        $data = $_POST;
        if($data['userid'] != $this->user->getId())
                throw new Exception('Userid, Form corrupted');
        if($data['usertoken'] != $this->user->getToken())
                throw new Exception('User token, Form corrupted');
        
        $id = $this->users->createAlbum($data);
        $stats = $this->users->getStats($this->user->getId());
        $stats->albums = $stats->albums + 1;
        $stats->save();
        $this->_redirect('/user/photos/albums/'.$id);
    }
    
    private function displayAlbumTask()
    {
        $albumid = $this->_getParam('id');
        $album = $this->users->getAlbum($albumid, $this->user->getId());
        
        if($this->getRequest()->isPost())
        {
            switch($_POST['_task']){
                case md5('rename'):
                    if($_POST['userid'] != $this->user->getId())
                            throw new Exception('Userid, Form corrupted');
                    if($_POST['usertoken'] != $this->user->getToken())
                            throw new Exception('User token, Form corrupted');
                    if($_POST['albumid'] != $album->id)
                            throw new Exception('Album id, Form corrupted');
                    
                    $album->name = $_POST['name'];
                    $album->updated = date('Y-m-d g:i:s');
                    $album->save();
                    break;
                case md5('delete'):
                    if($_POST['userid'] != $this->user->getId())
                            throw new Exception('Userid, Form corrupted');
                    if($_POST['usertoken'] != $this->user->getToken())
                            throw new Exception('User token, Form corrupted');
                    if($_POST['albumid'] != $album->id)
                            throw new Exception('Album id, Form corrupted');
                    
                    $album->delete();
                    $this->_redirect('/user/photos/albums');
                    break;
            }
        }
        
        $pictures = $this->users->getPicturesBy($this->user->getId(), $album->id);
        $this->view->photos = $pictures;
        
        $this->view->album = $album;
    }
    
    public function reservationsAction()
    {
        $template = 'reservations';
        
        switch($this->_getParam('task')){
            case 'default':
                $_reservs = $this->reservations->getUserConfirmed($this->user->getId());
                break;
            case 'pending':
                $_reservs = $this->reservations->getUserPending($this->user->getId());
                $template = 'reservations-pending';
                break;
            case 'history':
                $_reservs = $this->reservations->getUserHistory($this->user->getId());
                $template = 'reservations-history';
                break;
            default:
                throw new Exception('Page not found');
                break;
        }
        
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
        
        $user = $this->user->getData();
        $this->view->user = $user;
        
        $this->view->reservations = $_reservs;
        $this->render($template);
    }
    
    public function reviewsAction()
    {
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
        
        $user = $this->user->getData();
        $this->view->user = $user;
        
        $reviews = $this->reviews->getReviewsBy($this->user->getId());
        $this->view->reviews = $reviews;
        
        
    }
    
    public function questionsAction()
    {
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
        
        $user = $this->user->getData();
        $this->view->user = $user;
        
        $template = 'questions';
        if($this->_getParam('task') == 'unreplied'){
            $template = 'questions-unreplied';
            $questions = $this->public_questions->getPostedBy($this->user->getId(), true);
        } else {
        $questions = $this->public_questions->getPostedBy($this->user->getId());
        }
        $this->view->questions = $questions;
        
        $this->render($template);
    }
}