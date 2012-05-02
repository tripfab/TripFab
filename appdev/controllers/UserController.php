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
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $cart;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $cartitems;
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            $this->_redirect('/en-US/login?b='.$_SERVER['REQUEST_URI']);
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
                
                $this->cart = new Model_Cart();
                $this->cartitems = new Zend_Db_Table('cartitems');
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
                $template = $this->paymentsAccountTask();
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
        
        $user->save();
    }
    
    public function paymentsAccountTask()
    {
        switch($this->_getParam('id','default')) {
            case 'default':
                $user             = $this->user->getData(true);
                $new_messages     = $this->messages->countNew($this->user->getId());
                
                if($this->getRequest()->isPost())
                {
                    $id = $_POST['card'];
                    $account = $this->user->getAccount($id);
                    $account->delete();
                    
                    setcookie('alert', 'The credit card has been deleted');
                    $this->_redirect('/user/account/payments');
                }

                $accounts = $this->user->getAccounts();
                
                $creditcards = array(
                    'Visa'             => '/images2/checkout-card1.png',
                    'MasterCard'       => '/images2/checkout-card2.png',
                    'American Express' => '/images2/checkout-card3.png',
                    'Diners Club'      => '/images2/checkout-card4.png',
                    'Discover'         => '/images2/checkout-card5.png',
                    'JCB'              => '/images2/checkout-card6.png',
                );

                $this->view->new_messages = $new_messages;
                $this->view->user         = $user;
                $this->view->accounts     = $accounts;
                
                $this->view->cards        = $creditcards;
                
                return 'account-payments';
                break;
            case 'new':
                return $this->addPaymentAccountTask();
                break;
            default:
                return $this->editPaymentsAccountTask();
                break;
        }
    }
    
    public function addPaymentAccountTask()
    {
        if($this->getRequest()->isPost())
                $this->addPaymentsAccountPostHandler();
        
        $this->view->user = $this->user->getData();
        
        $keys = Zend_Registry::get('stripe');
        $this->view->pkey = $keys['public_key'];
        
        return 'account-newpayment';
    }
    
    public function addPaymentsAccountPostHandler()
    {
        require_once "Stripe/Stripe.php";
        // set your secret key: remember to change this to your live secret key in production
        // see your keys here https://manage.stripe.com/account
        $keys = Zend_Registry::get('stripe');
        Stripe::setApiKey($keys['secret_key']);

        // get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // create a Customer
        $customer = Stripe_Customer::create(array(
          "card" => $token,
          "description" => $this->user->email)
        );
        
        $strip_accounts = new Zend_Db_Table('stripe_accounts');
        $account = $strip_accounts->fetchNew();
        $account->stripe_id = $customer->id;
        $account->user_id   = $this->user->getId();
        $account->type      = $customer->active_card->type;
        $account->last4     = $customer->active_card->last4;
        $account->created   = date('Y-m-d H:i:s');
        $account->updated   = date('Y-m-d H:i:s');

        $account->save();
        
        setcookie('alert', 'The new credit card has been added');
        $this->_redirect('/user/account/payments');
    }
    
    public function editPaymentsAccountTask()
    {
        if($this->getRequest()->isPost())
                $this->editPaymentsAccountPostHandler();
        
        $id = $this->_getParam('id');
        $account = $this->user->getAccount($id);
        
        $this->view->user    = $this->user->getData();
        $this->view->account = $account;
        
        $keys = Zend_Registry::get('stripe');
        $this->view->pkey = $keys['public_key'];
        
        return 'account-editpayment';
    }
    
    public function editPaymentsAccountPostHandler()
    {
        $id = $this->_getParam('id');
        $account = $this->user->getAccount($id);
        
        require_once "Stripe/Stripe.php";
        // set your secret key: remember to change this to your live secret key in production
        // see your keys here https://manage.stripe.com/account
        $keys = Zend_Registry::get('stripe');
        Stripe::setApiKey($keys['secret_key']);

        // get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // create a Customer
        $customer = Stripe_Customer::create(array(
          "card" => $token,
          "description" => $this->user->email)
        );
        
        
        $account->stripe_id = $customer->id;
        $account->type      = $customer->active_card->type;
        $account->last4     = $customer->active_card->last4;
        $account->updated   = date('Y-m-d H:i:s');
        
        $account->save();
        
        setcookie('alert', 'The credit card has been updated');
        $this->_redirect('/user/account/payments');
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
            
            if($_POST['_task'] == md5('recalculate')) {
                $trip = $this->trips->getItn($id);
                if($trip->token != $_POST['token'])
                        throw new Exception();

                $listings = $this->trips->getItnListingOf($trip->id, false, 'null', true, true);

                $bookings = $_POST['bookings'];
            } elseif($_POST['_task'] == md5('proceed')) {
                $this->purchaseItinerary();
            } elseif($_POST['_task'] == md5('replace')){
                $trip = $this->trips->getItn($id);
                
                $this->trips->replaceListing($trip->id, $_POST['triplisting'], $_POST['listing']);
                
                $listings = $this->trips->getItnListingOf($trip->id, false, 'null', true, true);
                
                $bookings = array();
            
                foreach($listings as $list) {
                    if($list->main_type == 6 || $list->main_type == 5){
                        $bookings[$list->id] = array(
                            'adults' => 1,
                            'child'  => 0
                        );
                    }
                }
                
            } else {
                throw new Exception('Form corrupted');
            }
        }
        else {
            $id  = $this->_getParam('id');
            $trip = $this->trips->getItn($id);
            
            $listings = $this->trips->getItnListingOf($trip->id, false, 'null', true, true);
            
            $bookings = array();
            
            foreach($listings as $list) {
                if($list->main_type == 6 || $list->main_type == 5){
                    $bookings[$list->id] = array(
                        'adults' => 1,
                        'child'  => 0
                    );
                }
            }
        }

        $date = date('Y-m-d G:i:s', strtotime($trip->start));
        
        $main_types = $this->listings->getMainCategories(true);
        $this->view->types = $main_types;

        $items  = array();
        $items2 = array();
        $options = array();
        
        $caculated = array();
        
        foreach($listings as $u => $listing){
            if($listing->main_type == 6 || $listing->main_type == 5){
                if($listing->main_type == 5) {
                    if(!@in_array($listing->triplisting_id, $caculated)) {
                        $j = 2;
                        for($i = $listing->day; $i <= $trip->days; $i++){
                            $_diff = false; $found = false;
                            foreach($listings as $list2) {
                                if($list2->day == ($i+1) && $list2->main_type == 5) {
                                    if($list2->listing_id == $listing->listing_id) {
                                        $j++;
                                        $caculated[] = $list2->triplisting_id;
                                        $found = true;
                                    } else {
                                        $_diff = true;
                                    } break;
                                }
                            }
                            if($_diff) break;
                            elseif(!$found and ($trip->days - 1) > $i) $j++;
                        }
                        
                        $checkin = ($listing->day > 1) ? (strtotime($date) + (($listing->day - 1) * 86400)) : strtotime($date);
                        $checkin = date('Y-m-d', $checkin);

                        $adults = (isset($bookings[$listing->id]['adults'])) ? $bookings[$listing->id]['adults'] : null;
                        $kids   = (isset($bookings[$listing->id]['kids'])) ? $bookings[$listing->id]['kids'] : null;

                        $option = (isset($bookings[$listing->id]['option'])) ? $bookings[$listing->id]['option'] : null;
                        $capacity = (isset($bookings[$listing->id]['capacity'])) ? $bookings[$listing->id]['capacity'] : null;

                        $item = $this->listings->getQuote($listing, $adults, $kids, $checkin, null, $j, $option, $capacity);

                        $item->day = $listing->day;
                        $item->listing_city = $listing->city;
                        $item->listing_country = $listing->country;
                        $item->triplistingid = $listing->triplisting_id;

                        $items[] = $item;
                        
                        $options[$listing->id]['options'] = $this->listings->getHotelRooms($listing->id);

                        $prices = $this->listings->getSchPrices($listing);
                        $options[$listing->id]['prices'] = (isset($prices[0])) ? $prices[0] : $prices;
                        
                        $caculated[] = $listing->id;
                    }
                } 
                else {
                    $checkin = ($listing->day > 1) ? (strtotime($date) + (($listing->day - 1) * 86400)) : strtotime($date);
                    $checkin = date('Y-m-d', $checkin);

                    $adults = (isset($bookings[$listing->id]['adults'])) ? $bookings[$listing->id]['adults'] : null;
                    $kids   = (isset($bookings[$listing->id]['kids'])) ? $bookings[$listing->id]['kids'] : null;

                    $option = (isset($bookings[$listing->id]['option'])) ? $bookings[$listing->id]['option'] : null;
                    $capacity = (isset($bookings[$listing->id]['capacity'])) ? $bookings[$listing->id]['capacity'] : null;

                    $item = $this->listings->getQuote($listing, $adults, $kids, $checkin, null, $trip->days, $option, $capacity);

                    $item->day = $listing->day;
                    $item->listing_city = $listing->city;
                    $item->listing_country = $listing->country;
                    $item->triplistingid = $listing->triplisting_id;

                    $items[] = $item;

                    $options[$listing->id]['options'] = $this->listings->getSchedulesOf($listing->id);

                    if(is_null($listing->min) or is_null($listing->max)){
                        $options[$listing->id]['capacity'] = $this->listings->getActivityTypes($listing->id);

                        $prices = $this->listings->getSchPrices($listing);
                        $options[$listing->id]['prices'] = (isset($prices[0])) ? $prices[0] : $prices;
                    }
                }
            }
        }
        
        foreach($listings as $listing){
            if($listing->main_type != 6 && $listing->main_type != 5){
                $item = new stdClass();
                $item->available = true;
                $item->listing_title = $listing->title;
                $item->listing_image = $listing->image;
                $item->listing_type  = $listing->main_type;
                $item->listing_city = $listing->city;
                $item->listing_country = $listing->country;
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
        //print_r($items); die;
        $this->view->items2 = $items2;
        $this->view->bigtotal = $total;
        
        $country = $this->places->getPlaceById($trip->country_id);
        $this->view->country = $country;
        
        $this->view->options = $options;
    }
    
    protected function purchaseItinerary()
    {
        $id    = $_POST['ids'];

        $trip = $this->trips->getItn($id);
        if($trip->token != $_POST['token'])
                throw new Exception();
        
        $user = $this->user->getData(true);
        
        $date = date('Y-m-d', strtotime($trip->start));

        $cart = $this->cart->fetchNew();
        $cart->user_id  = $user->id;
        $cart->listing_id = $trip->id;
        $cart->checkin  = $date;
        $cart->adults   = 0;
        $cart->kids     = 0;
        $cart->rate     = 0;
        $cart->rate_description = $trip->title . ' - Itinerary Purchase';
        $cart->subtotal = 0;
        $cart->taxes    = 0;
        $cart->total    = 0;
        $cart->token    = md5($user->token . time());
        $cart->type     = 3;
        $cart->created  = date('Y-m-d H:i:s');

        $cart->save();

        $cartitems = new Zend_Db_Table('cartitems');            

        $listings = $this->trips->getItnListingOf($trip->id, false, 'null', true, true);

        $items  = array();
        
        $caculated = array();
        
        foreach($listings as $listing){
            if($listing->main_type == 6){
                $checkin   = ($listing->day > 1) ? (strtotime($date) + (($listing->day - 1) * 86400)) : strtotime($date);
                $checkin   = date('Y-m-d', $checkin);
                
                $data = $_POST['bookings'][$listing->id];
                
                $rowR = $this->listings->getQuote(
                            $listing, 
                            $data['adults'], 
                            $data['kids'], 
                            $checkin,
                            null,
                            $trip->days,
                            $data['option'],
                            $data['capacity']);
                
                if($rowR->available) {
                    $row = $this->cartitems->fetchNew();
                    $row->cart_id     = $cart->id;
                    $row->checkin     = date('Y-m-d', strtotime($rowR->checkin));
                    $row->listing_id  = $listing->id;
                    $row->option_id   = $rowR->option_id;
                    $row->adults      = $rowR->adults;
                    $row->kids        = $rowR->kids;
                    $row->rate        = $rowR->rate;
                    $row->additional  = $rowR->additional;
                    $row->subtotal    = $rowR->subtotal;
                    $row->taxes       = $rowR->taxes;
                    $row->total       = $rowR->total;
                    $row->created     = date('Y-m-d H:i:s');
                    $row->triplisting = $listing->triplisting_id;

                    $row->rate_description       = $rowR->rate_description;
                    $row->additional_description = $rowR->additional_description;

                    $row->save();
                    
                    $items[] = $row;
                }
            }
            elseif($listing->main_type == 5)
            {
                if(!@in_array($listing->triplisting_id, $caculated)) {
                    $j = 2;
                    for($i = $listing->day; $i <= $trip->days; $i++){
                        $_diff = false; $found = false;
                        foreach($listings as $list2) {
                            if($list2->day == ($i+1) && $list2->main_type == 5) {
                                if($list2->listing_id == $listing->listing_id) {
                                    $j++;
                                    $caculated[] = $list2->triplisting_id;
                                    $found = true;
                                } else {
                                    $_diff = true;
                                } break;
                            }
                        }
                        if($_diff) break;
                        elseif(!$found and ($trip->days - 1) > $i) $j++;
                    }
                    
                    $checkin   = ($listing->day > 1) ? (strtotime($date) + (($listing->day - 1) * 86400)) : strtotime($date);
                    $checkin   = date('Y-m-d', $checkin);
                
                    $data = $_POST['bookings'][$listing->id];

                    $rowR = $this->listings->getQuote(
                                $listing, 
                                $data['adults'], 
                                $data['kids'], 
                                $checkin,
                                null, 
                                $j, $data['option']);

                    if($rowR->available) {

                        $row = $this->cartitems->fetchNew();
                        $row->cart_id     = $cart->id;
                        $row->checkin     = date('Y-m-d', strtotime($rowR->checkin));
                        $row->checkout    = date('Y-m-d', strtotime($rowR->checkout));
                        $row->listing_id  = $listing->id;
                        $row->option_id   = $rowR->option_id;
                        $row->adults      = $rowR->adults;
                        $row->kids        = $rowR->kids;
                        $row->rate        = $rowR->rate;
                        $row->additional  = $rowR->additional;
                        $row->rooms       = $rowR->rooms;
                        $row->nights      = $rowR->nights;
                        $row->subtotal    = $rowR->subtotal;
                        $row->taxes       = $rowR->taxes;
                        $row->total       = $rowR->total;
                        $row->created     = date('Y-m-d H:i:s');
                        $row->triplisting = $listing->triplisting_id;

                        $row->rate_description       = $rowR->rate_description;
                        $row->additional_description = $rowR->additional_description;

                        $row->save();

                        $items[] = $row;

                    }
                }
            }
        }
        
        $items2 = array();

        foreach($listings as $listing){
            if($listing->main_type != 6 && $listing->main_type != 5){                    
                $checkin = ($listing->day > 1) ? (strtotime($date) + (($listing->day - 1) * 86400)) : strtotime($date);
                $cartitem = $this->cartitems->fetchNew();
                $cartitem->cart_id      = $cart->id;
                $cartitem->checkin      = date('Y-m-d',$checkin);
                $cartitem->listing_id   = $listing->id;
                $cartitem->rate         = 0;
                $cartitem->additional   = 0;
                $cartitem->subtotal     = 0;
                $cartitem->taxes        = 0;
                $cartitem->total        = 0;
                $cartitem->created      = date('Y-m-d H:i:s');
                $cartitem->rate_description = 'Price not charget';
                $cartitem->save();

                $items2[] = $cartitem;
            }
        }

        $total = 0; $subtotal = 0; $taxes  = 0;
        
        foreach($items as $c){
            $total = $total + $c->total;
            $subtotal = $subtotal + $c->subtotal;
            $taxes = $c->taxes;
        }

        $cart->total    = $total;
        $cart->subtotal = $subtotal;
        $cart->taxes    = $taxes;
        
        $cart->save();

        $this->_redirect('cart/checkout/'.$cart->id);
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
        
        $missed = array();
        $missedIds = array();
        $showProximity = false;
        foreach($trips as $trip) {
            $start = strtotime($trip->start);
            if($start <= time()) {
                $missed[] = $trip;
                $missedIds[$trip->id] = 'yes';
            } elseif(($start - time()) <= 1296000) {
                $showProximity = true;
            }
        }
        
        $this->view->missed = $missed;
        $this->view->missedIds = $missedIds;
        
        $this->view->showProximity = $showProximity;
        
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
            case md5('update_dates'):
                $trip = $this->trips->getItn($_POST['ids'], true);
                if($trip->token != $_POST['token'])
                    throw new Exception('Form Corrupted');
                if($trip->user_id != $this->user->getId())
                    throw new Exception("The trip you're trying to delete isn't yours");
                if(empty($_POST['date']))
                    throw new Exception('Please select the dates');
                
                $start = date('Y-m-d', strtotime($_POST['date']));
                $days = $trip->days;
                $checkout = strtotime($start);
                $checkout = $checkout + (86400 * ($days - 1));
                
                $end = date('Y-m-d', $checkout);
                    
                $trip->start = date('Y-m-d', strtotime($start));
                $trip->end   = date('Y-m-d', strtotime($end));
                
                $trip->save();
                
                setcookie('alert','Your changes have been saved');
                $this->_redirect('/user/trips/');
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
        
        $labels = array('','Morning','Afternoon', 'Night','Hotel');
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
            case 'view':
                $this->viewReservationTask();
                $template = 'reservations-view';
                break;
            case 'review':
                $this->reviewReservationTask();
                $template = 'reservations-review';
                break;
            default:
                throw new Exception('Page not found'); break;
        }
        
        $this->render($template);
    }
    
    public function reviewReservationTask()
    {
        $id = $this->_getParam('id','default');
        if($id == 'default') 
            throw new Exception('Page not found');
        
        $listing = $this->listings->getListing($id);
        $reservation = $this->reservations->getUserReservation($this->user->getId(), $listing->id, 3);
        if(is_null($reservation)) 
            throw new Exception('Page not found');
        
        $review = $this->reviews->getReview($this->user->getId(), $listing->id);
        
        $vendor = $this->users->getVendor($listing->vendor_id);
        
        if($this->getRequest()->isPost())
        {
            $text = $_POST['text'];
            if(is_null($review)) {
                $review = $this->reviews->save($listing->id, $this->user->getId(), $text);
            } else {
                $review->text    = $text;
                $review->title   = substr($text, 0, 10).'...';
                $review->updated = date('Y-m-d H:i:s');
                $review->new     = 1;
                
                $review->save();
            }
            
            $notifier = new WS_Notifier($vendor->user_id);
            $notifier->newReview($listing, $this->user->getData());
            
            setcookie('alert', 'Your changes have been saved');
            $this->_redirect('/user/reservations/history');
        }
        
        $this->view->review = (!is_null($review)) ? $review : false;
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
    
    public function viewReservationTask()
    {
        $id = $this->_getParam('id');
        $reservation = $this->reservations->get($id, $this->user->getId());
        $listing = $this->listings->getListing($reservation->listing_id);

        $this->transactions = new Model_Transactions();
        $select = $this->transactions->select();
        $select->where('id = ?', $reservation->transaction_id);
        $transaction = $this->transactions->fetchRow($select);

        $this->view->transaction = $transaction;
        $this->view->listing     = $listing;
        $this->view->user        = $this->user->getData();
        $this->view->reservation = $reservation;
    }
}