<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserController
 *
 * @author magentodeveloper
 */
class UserController extends Zend_Controller_Action 
{
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
    
    /**
     *
     * @var WS_TripsService
     */
    protected $trips;
    
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
                $this->trips            = new WS_TripsService();
                
                $this->view->user       = $this->user->getData();
            }
        }
    }
    
    
    /**
     *  Account Action is incharge of the core user information
     *  payment methos and login information, and Notifications
     */
    public function accountAction()
    {
        $template = 'account';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->defaultAccountTask();
                break;
            case 'password':
                $this->passwordAccountTask();
                $template = 'account-password';
                break;
            case 'payments':
                $this->paymentsAccountTask();
                $template = 'account-payments';
                break;
            case 'notifications':
                $this->notificationsAccountTask();
                $template = 'account-notifications';
                break;
            default:
                throw new Exception('Page not found'); break;
        }
        
        $this->render($template);
    }
    
    public function defaultAccountTask()
    {
        if($this->getRequest()->isPost())
            try{
                $this->defaultAccountPostHandler();
                $this->view->successmessage = 'Your changes has been applied';}
            catch(Exception $e){
                $this->view->errormessage = $e->getMessage();}
        
        $user             = $this->user->getData(true);
        $new_messages     = $this->messages->countNew($this->user->getId());
        $countries        = $this->places->getPlaces(2);
        
        if($user->city_id)
                $cities   = $this->places->getPlaces (3, $user->country_id);
        
        $this->view->new_messages = $new_messages;
        $this->view->user         = $user;
        $this->view->countries    = $countries;
        if($cities)
            $this->view->cities = $cities;
    }
    
    public function defaultAccountPostHandler()
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
        
        $this->_validateData($_POST, $user);
        
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
    
    private function _validateData($data, $user)
    {
        if(empty($data['name']))
            throw new Exception ('Name cannot be empty');
        if(empty($data['email']))
            throw new Exception ('Email cannot be empty');
        //if(empty($data['username']))
        //    throw new Exception ('Username cannot be empty');            
        
        $email = new Zend_Validate_EmailAddress();
        if(!$email->isValid($data['email'])){
            $messages = $email->getMessages();
            foreach($messages as $msg)
                throw new Exception($msg);
        }
        
        if($user->email != $data['email'])
            if(!$this->accounts->validateEmail($data['email']))
                throw new Exception ('This email its being used for another user');
            
        //if(!ctype_alnum($data['username']))
        //    throw new Exception ('Username may contain just numbers and letters');
        
        //if($user->username != $data['username'])
        //    if(!$this->accounts->validateUsername($data['username']))
        //        throw new Exception ('This username its being user for another user');
        
        $gender    = array('','Male','Female');
        $languages = array('','English', 'Spanish');
        
        if(!in_array($data['gender'], $gender))
            throw new Exception ('Invalid Gender');
        
        if(!in_array($data['languages'], $languages))
            throw new Exception ('Invalid Language');
        
    }
    
    public function passwordAccountTask()
    {
        if($this->getRequest()->isPost())
            try{
                $this->passwordAccountPostHandler();
                $this->view->successmessage = 'Your changes has been applied';}
            catch(Exception $e){
                $this->view->errormessage = $e->getMessage();}
                
        $user             = $this->user->getData(true);
        $new_messages     = $this->messages->countNew($this->user->getId());
        $questions        = $this->user->getDefaultQuestions();
        
        
        $this->view->new_messages = $new_messages;
        $this->view->user         = $user;
        $this->view->sq           = $questions;
    }
    
    public function passwordAccountPostHandler()
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
    
    public function paymentsAccountTask()
    {
        $user             = $this->user->getData(true);
        $new_messages     = $this->messages->countNew($this->user->getId());
        
        $this->view->new_messages = $new_messages;
        $this->view->user         = $user;
    }
    
    public function notificationsAccountTask()
    {
        $user = $this->user->getData(true);
        
        $notifications = $this->user->getDefaultNotifications();
        if($this->getRequest()->isPost())
            try{
                $this->notificationsAccountPostHandler();
                $this->view->successmessage = 'Your changes has been applied';
            } catch(Exception $e){
                $this->view->errorsmessage  = 'Something went wrong';
            }
            
            
        $usersettings  = $this->user->getNotificationsSettings();
        //echo '<pre>'; var_dump($notifications); echo '</pre>'; die;
        $this->view->notifications = $notifications;
        $this->view->usersettings  = $usersettings;
        $this->view->user          = $user;
    }
    
    public function notificationsAccountPostHandler()
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
    
    
    /**
     *  Messages action is in charge of tracking
     *  all the conversations between the user and the 
     *  tour operators and hotels
     */
    public function messagesAction()
    {
        $template = 'messages';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->defaultMessagesTask();
                break;
            case 'starred':
                $this->staredMessagesTask();
                $template = 'messages-stared';
                break;
            case 'unreplied':
                $this->unrepliedMessagesTask();
                $template = 'messages-unreplied';
                break;
            default:
                throw new Exception('Page not found'); break;
        }
        
        $this->render($template);
    }
    
    public function defaultMessagesTask()
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
    
    public function staredMessagesTask()
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
    
    public function unrepliedMessagesTask()
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
    
    
    /**
     *  trips action is in charge of the administration
     *  of all the tasks related to an user trip
     *  
     *  These tasks are
     *      - Keep and archive of future and past trips
     *      - View/edit trip informations
     *      - Trip scheduling or better known as itineraries
     *      - Keep track of offers recived from providers
     */
    public function tripsAction()
    {
        $template = 'trips';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->defaultTripsTask();
                break;
            case 'past':
                $this->pastTripsTask();
                $template = 'trips-past';
                break;
            case 'view':
                $this->viewTripsTask();
                $template = 'trips-view';
                break;
            case 'information':
                $this->informationTripsTask();
                $template = 'trips-information';
                break;
            case 'itinerary':
                $this->itineraryTripsTask();
                $template = 'trips-itinerary';
                break;
            case 'offers':
                $this->offersTripsTask();
                $template = 'trips-offers';
                break;
            case 'activateoffers':
                $this->activateoffersTripsTask();
                $template = 'trips-offers';
                break;
            case 'checkprices':
                $this->checkpricesTripsTask();
                $template = 'trips-checkprices';
                break;
            case 'new':
                $this->newTripsTask();
                $template = 'trips-new';
                break;
            default:
                throw new Exception('Page not found'); break;
        }
        
        $this->render($template);
    }
    
    public function newTripsTask()
    {
        if($this->getRequest()->isPost()){
            try {
                if(empty($_POST['title']) || $_POST['title'] == 'A name for your trip')
                    throw new Exception('Provide a name for your trip');
                if(empty($_POST['start']) || $_POST['start'] == 'Starting Date')
                    throw new Exception('Provide a starting date for your trip');
                if(empty($_POST['end']) || $_POST['end'] == 'Ending Date')
                    throw new Exception('Provide an ending date for your trip');
                
                $start = date('D M j Y', strtotime($_POST['start']));
                $end   = date('D M j Y', strtotime($_POST['end']));
                
                if($start != $_POST['start'] || $end != $_POST['end'])
                    throw new Exception("Sorry, we couldn't understand the date Formats");
                
                $fday = strtotime($start);
                $lday = strtotime($end);
                $days = $lday - $fday;
                $days = $days / 86400;
                
                $itineraries = new Model_Itineraries();
                $trip = $itineraries->fetchNew();
                
                $trip->title = $_POST['title'];
                $trip->user_id = $this->user->getId();
                $trip->country_id = 18;
                $trip->start = date('Y-m-d', strtotime($start));
                $trip->end = date('Y-m-d', strtotime($end));
                $trip->created = date('Y-m-d H:i:s');
                $trip->updated = date('Y-m-d H:i:s');
                $trip->save();
                $trip->token = md5($trip->id.$user->id.time());
                $trip->save();
                
                $trip->days = $days + 1;
                
                $trip->save();
                
                $this->_redirect('/user/trips');
            } catch(Exception $e) {
                $this->view->error = $e->getMessage();
            }
        }
        $countries = $this->places->getPlaces(2);
        $country   = $this->places->getPlaceById(18);
        $cities    = $this->places->getPlaces(3, $country->id);
        
        $this->view->countries = $countries;
        $this->view->cities    = $cities;
        $this->view->country   = $country;
    }
    
    public function checkpricesTripsTask()
    {
        if($this->getRequest()->isPost()){            
            $ids = $_POST['ids'];
            $id  = $this->_getParam('id');
            if($ids != $id)
                    throw new Exception();
            
            $trip = $this->trips->getItn($id);
            if($trip->token != $_POST['token'])
                    throw new Exception();
            
            $adults = $_POST['adultss'];
            $kids   = $_POST['kids'];
        }
        else {
            $id  = $this->_getParam('id');
            $trip = $this->trips->getItn($id);
            $adults = 1;
            $kids   = 0;
        }
            
            
        $listings = $this->trips->getItnListingOf($trip->id, false, 'null', true);

        $date = date('Y-m-d G:i:s', strtotime($trip->start));

        $items  = array();
        $items2 = array();
        foreach($listings as $listing){
            if($listing->main_type == 6 || $listing->main_type == 5){
                $checkin = ($listing->day > 1) ? (strtotime($date) + (($listing->day - 1) * 86400)) : strtotime($date);
                $checkin = date('Y-m-d', $checkin);
                $item = $this->listings->getQuote($listing, $adults, $kids, $checkin, null, $trip->days);
                $item->day = $listing->day;
                $items[] = $item;
            }
        }
        foreach($listings as $listing){
            if($listing->main_type != 6 && $listing->main_type != 5){
                $item = new stdClass();
                $item->available = true;
                $item->listing_title = $listing->title;
                $item->listing_image = $listing->image;
                $items2[] = $item;
            }
        }

        $total = 0;
        foreach($items as $cart){
            if($cart->available)
                $total = $total + $cart->total;
        }

        $this->view->trip = $trip;
        $this->view->items = $items;
        $this->view->items2 = $items2;
        $this->view->bigtotal = $total;
    }
    
    public function activateoffersTripsTask()
    {
        $trip = $this->_getTrip('id', true, true);
        $trip->offers = 1;
        $trip->save();
        $this->_redirect('/user/trips/itinerary/'.$trip->id);
        
    }
    
    public function defaultTripsTask()
    {
        if($this->getRequest()->isPost()){
            try {
                $message = $this->defaultTripPostHandler();
                $this->view->alert = $message;
            } catch(Exception $e){
                $this->view->error = $e->getMessage();
            }
        }
        $trips     = $this->trips->getFutureTripsBy($this->user->getId());
        $pastTrips = $this->trips->getPastTripsBy($this->user->getId(), true);
        
        $listingCounter = array();
        foreach($trips as $trip){
            $listingCounter[$trip->id] = $this->trips->countListings($trip->id);
        }
        
        $this->view->listingCounter = $listingCounter;
        $this->view->trips     = $trips;
        $this->view->pastTrips = $pastTrips;
        
        $flash = $this->_helper->getHelper('FlashMessenger');
        $messages = $flash->getMessages();
        if(count($messages) > 0)
            $this->view->error = $messages[0];
    }
    
    public function defaultTripPostHandler()
    {
        switch($_POST['_task'])
        {
            case md5('delete_trip'):
                $trip = $this->trips->getItn($_POST['ids'], true);
                if($trip->token != $_POST['token'])
                    throw new Exception('Form Corrupted');
                if($trip->user_id != $this->user->getId())
                    throw new Exception("The trip you're trying to delete isn't yours");
                $this->trips->deleteItn($trip->id);
                
                return $trip->title . ' was succesfully deleted';
                
                break;
            case md5('assign_dates'):
                $trip = $this->trips->getItn($_POST['ids'], true);
                if($trip->token != $_POST['token'])
                    throw new Exception('Form Corrupted');
                if($trip->user_id != $this->user->getId())
                    throw new Exception("The trip you're trying to delete isn't yours");
                if(empty($_POST['start']) || empty($_POST['end']))
                    throw new Exception('Please Insert the dates');
                
                $start = date('D M j Y', strtotime($_POST['start']));
                $end   = date('D M j Y', strtotime($_POST['end']));
                
                if($start != $_POST['start'] || $end != $_POST['end'])
                    throw new Exception("Sorry, we couldn't understand the date Formats");
                    
                $trip->start = date('Y-m-d', strtotime($start));
                $trip->end   = date('Y-m-d', strtotime($end));
                
                $fday = strtotime($start);
                $lday = strtotime($end);
                $days = $lday - $fday;
                $days = $days / 86400;
                
                $trip->days         = $days + 1;
                
                $trip->save();
                
                
                $this->_redirect('/user/trips/itinerary/'.$trip->id);
                break;
            default:
                throw new Exception('Form Corrupted');
                break;
        }
    }
    
    public function pastTripsTask()
    {
        if($this->getRequest()->isPost()){
            try {
                $message = $this->defaultTripPostHandler();
                $this->view->alert = $message;
            } catch(Exception $e){
                $this->view->error = $e->getMessage();
            }
        }
        $trips     = $this->trips->getPastTripsBy($this->user->getId());
        $pastTrips = $this->trips->getFutureTripsBy($this->user->getId(), true);
        
        $listingCounter = array();
        foreach($trips as $trip){
            $listingCounter[$trip->id] = $this->trips->countListings($trip->id);
        }
        
        $this->view->listingCounter = $listingCounter;
        $this->view->trips     = $trips;
        $this->view->pastTrips = $pastTrips;
        
        $flash = $this->_helper->getHelper('FlashMessenger');
        $messages = $flash->getMessages();
        if(count($messages) > 0)
            $this->view->error = $messages[0];
    }
    
    public function viewTripsTask()
    {
        $trip = $this->_getTrip('id', true);
        $this->view->trip = $trip;

        //$listings = $this->trips->getItnListingOf($trip->id, false);
        $listings = $this->trips->getItnListingOf($trip->id, false);
        $this->view->listings = $listings;
        
        $images = array();
        foreach($listings as $list){
            if(!empty($list->image)){
                if(!in_array($list->image, $images))
                    $images[] = $list->image;
            }
        }
        
        $labels = array('','Morning','Afternoon', 'Night');
        $times = array();
        $days  = array();
        $results = array();
        foreach($listings as $day){
          if(!in_array($day->day, $days)){
            $days[] = $day->day;
            $times[$day->day] = array();
            foreach($listings as $time){
              if($time->day == $day->day){
                if(!in_array($time->time, $times[$day->day])){
                  $times[$day->day][] = $time->time;
                  $results[$day->day][$labels[$time->time]] = array();
                  foreach($listings as $listing){
                    if(($listing->day == $day->day) and ($listing->time == $time->time)){
                      $results[$day->day][$labels[$time->time]][] = $listing;
        }}}}}}}
        
        $this->view->listingsbyday = $results;
        
        $listingtypes = array(2,4,5,6,7);
        $counter      = array();
        foreach($listingtypes as $type){
            $count = 0;
            foreach($listings as $list){
                if($list->main_type == $type){
                    $count++;
                }
            }
            $counter[$type] = $count;
        }
        
        $this->view->counter = $counter;
        
        //var_dump($results); die; 
        
        
        $this->view->images = $images;
    }
    
    private function _getTrip($var = 'id', $user = false, $obj = false)
    {
        $id   = $this->_getId($var);
        $trip = $this->trips->getItn($id, $obj);
        if($user){
            if($trip->user_id != $this->user->getId()){
                throw new Exception();
        }}
        return $trip;
    }
    
    private function _getId($var = 'id')
    {
        $id = $this->_getParam($var);
        if(!$this->_isValidId($id))
                $this->_redirect('user/trips');
        
        return $id;
    }
    
    private function _isValidId($var)
    {
        $id = (int) $var;
        if(!is_int($id))
            return false;
        if($id<=0)
            return false;
        
        return true;
    }
    
    public function informationTripsTask()
    {
        if($this->getRequest()->isPost()){
            try{
                $this->informationTripPostHandler();
                $this->view->successmessage = 'Your changes has been applied';} 
            catch(Exception $e){
                $this->view->errormessage = $e->getMessage();}
        }
        $trip = $this->_getTrip('id', true);
        $this->view->trip = $trip;
        
        $listings = $this->trips->getItnListingOf($trip->id, false);
        $this->view->listings = $listings;
        
        $listingtypes = array(2,4,5,6,7);
        $counter      = array();
        foreach($listingtypes as $type){
            $count = 0;
            foreach($listings as $list){
                if($list->main_type == $type){
                    $count++;
                }
            }
            $counter[$type] = $count;
        }
        
        $this->view->counter = $counter;
    }
    
    public function informationTripPostHandler()
    {
        $data = $_POST;
        if(empty($data['title']))
                throw new Exception('Title cannot be empty');
        if(empty($data['start']))
                throw new Exception('Start cannot be empty');
        if(empty($data['end']))
                throw new Exception('End cannot be empty');
        if(empty($data['description']))
                throw new Exception('Description cannot be empty');
        
        $start = date('M j Y', strtotime($data['start']));
        $end   = date('M j Y', strtotime($data['end']));
        
        if(($start != $data['start']) || ($end != $data['end']))
                throw new Exception('Incorrect date formats');
        
        $trip = $this->_getTrip('id', true, true);
        if($trip->id != $data['tripid'])
                throw new Exception('Form corrupted');
        if($trip->user_id != $data['userid'])
                throw new Exception('Form corrupted');
        if(md5($trip->token) != $data['triptoken'])
                throw new Exception('Form corrupted');
        if($this->user->getId() != $data['userid'])
                throw new Exception('Form corrupted');
        if(md5($this->user->getToken()) != $data['usertoken'])
                throw new Exception('Form corrupted');
        if(md5('update_trip') != $data['_task'])
                throw new Exception('Form corrupted');
        
        $fday = strtotime($data['start']);
        $lday = strtotime($data['end']);
        $days = $lday - $fday;
        $days = $days / 86400;
        
        $trip->title        = $data['title'];
        $trip->end          = date('Y-m-d',$lday);
        $trip->start        = date('Y-m-d',$fday);
        $trip->kids         = $data['kids'];
        $trip->adults       = $data['adults'];
        $trip->description  = $data['description'];
        $trip->days         = $days + 1;
        
        $trip->save();
    }
    
    public function itineraryTripsTask()
    {
        $trip = $this->_getTrip('id', true);
        $this->view->trip = $trip;
        
        if($trip->start == '0000-00-00' || $trip->end == '0000-00-00'){
            $flash = $this->_helper->getHelper('FlashMessenger');
            $flash->addMessage('You need to setup the dates before planing your itinerary');
            $this->_redirect('/user/trips/');
        }
        
        $listings = $this->trips->getItnListingOf($trip->id, false, 'notnull');
        $days     = $this->trips->getItnListingOf($trip->id, true, 'null');
        
        $ids = array(2=>4,4=>3,5=>5,6=>1,7=>2); $used = array(); $result = array();
        foreach($listings as $listing){
            if(!in_array($listing->main_type, $used)){
                $used[] = $listing->main_type;
                $id     = $ids[$listing->main_type];
                $result[$id] = array();
                foreach($listings as $list){
                    if($list->main_type == $listing->main_type){
                        $result[$id][] = $list;
        }}}}
        
        $this->view->favorites = $result;
        $this->view->days     = $days;
    }
    
    public function offersTripsTask()
    {
        $trip = $this->_getTrip('id', true);
        $this->view->trip = $trip;
    }
    
    
    /**
     *  reservations action is in charge of track the status
     *  of the reservations made by the user, it shows the pending,
     *  and confirmed reservations as well as a history of all the
     *  reservations enyoyed by the user.
     */
    public function reservationsAction()
    {
        $template = "reservations";
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->defaultReservationsTask();                
                break;
            case 'pending':
                $this->pendingReservationsTask();
                $template = 'reservations-pending';
                break;
            case 'history':
                $this->historyReservationsTask();
                $template = 'reservations-history';
                break;
            default:
                throw new Exception('Page not found'); break;
        }
        
        $this->render($template);
    }
    
    public function defaultReservationsTask()
    {
        $_reservs  = $this->reservations->getUserConfirmed($this->user->getId());
        $confirmed = $this->reservations->countConfirmed($this->user->getId());
        
        $user = $this->user->getData();
        $this->view->user = $user;
        
        $this->view->reservations = $_reservs;
        $this->view->confirmed    = $confirmed;
    }
    
    public function pendingReservationsTask()
    {
        $_reservs  = $this->reservations->getUserPending($this->user->getId());
        $confirmed = $this->reservations->countConfirmed($this->user->getId());
        
        $user = $this->user->getData();
        $this->view->user = $user;
        
        $this->view->reservations = $_reservs;
        $this->view->confirmed    = $confirmed;
    }
    
    public function historyReservationsTask()
    {
        $_reservs  = $this->reservations->getUserHistory($this->user->getId());
        $confirmed = $this->reservations->countConfirmed($this->user->getId());
        
        $user = $this->user->getData();
        $this->view->user = $user;
        
        $this->view->reservations = $_reservs;
        $this->view->confirmed    = $confirmed;
    }
}