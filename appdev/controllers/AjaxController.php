<?php

class AjaxController extends Zend_Controller_Action
{
    
    const CLIENT_ID     = 'UAZO1NP00B4BHVHJGONXBEKHMARCCDXUCIBRYIKVXJIYDWQU';
    const CLIENT_SECRET = 'C5DMAZR3H4PKNSYD4DHGVV0HV2HWBU2R3EW2FHXZPKI0PIRX';
    
    protected $places_db;
    protected $landscapes_db;
    protected $pictures_db;
    protected $listings_db;
    protected $trips;
    /**
     *
     * @var WS_ListingService
     */
    protected $listings;
    protected $user;
    protected $accounts;
    
    /**
     *
     * @var WS_UsersService
     */
    protected $users;
    protected $reviewes;
    protected $vendors;
    protected $reservations;
    
    /**
     *
     * @var WS_PlacesService
     */
    protected $places;


    public function init()
    {
        $this->places_db = new Model_Places();
        $this->landscapes_db = new Model_Landscapes();
        $this->pictures_db = new Model_ListingPictures();
        $this->listings_db = new Model_Listings();
        
        $this->places = new WS_PlacesService();
        $this->listings = new WS_ListingService();
        
        $this->trips = new WS_TripsService();
        
        $this->accounts = new WS_AccountService();
        $this->users = new WS_UsersService();
        $this->reviewes = new WS_ReviewsService();
        $this->vendors = new WS_VendorService();
        
        $this->reservations = new WS_ReservationsService();
        
        $this->user = null;
        
        $headers = getallheaders();
        if((array_key_exists('X-Requested-With', $headers) === false) and 
                (array_key_exists('x-requested-with', $headers) === false)){
            error_log('Wrong request');
            echo 'Wrong request'; die;
        }
    }
    
    public function activateAction()
    {
        if($this->getRequest()->isPost()){
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity()){
                $this->user = new WS_User($auth->getIdentity());
                $validate = array(
                    'profile' => array(
                        'url' => '/provider/account',
                        'label' => 'Set company picture/logo',
                        'description' => 'Add your company\'s logo, so people can recognize you',
                        'done'  => false,
                    ),
                    'title' => array(
                        'url' => '/provider/listings/edit/',
                        'label' => 'Listing Title',
                        'desription' => 'Change the listing title or Assign one',
                        'done' => false,
                    ),
                    'description' => array(
                        'url' => '/provider/listings/overview/',
                        'label' => 'Listing Description',
                        'desription' => 'Add a listing short description',
                        'done' => false,
                    ),
                    'country' => array(
                        'url' => '/provider/listings/location/',
                        'label' => 'Listing Country',
                        'desription' => 'Assign a country to the listing',
                        'done' => false,
                    ),
                    'city' => array(
                        'url' => '/provider/listings/location/',
                        'label' => 'Listing City',
                        'desription' => 'Assign a city to the listing',
                        'done' => false,
                    ),
                    'location' => array(
                        'url' => '/provider/listings/location/',
                        'label' => 'Listing Map',
                        'desription' => 'Localize your listing in the map',
                        'done' => false,
                    ),
                    'photos' => array(
                        'url' => '/provider/listings/photos/',
                        'label' => 'Listing Photos',
                        'desription' => 'Add photos to the listing',
                        'done' => false,
                    ),
                    'overview' => array(
                        'url' => '/provider/listings/overview/',
                        'label' => 'Listing Overview',
                        'desription' => 'Add the Overview Information',
                        'done' => false,
                    ),
                );


                $ids = $_POST['listing'];

                $listing = $this->listings->getListing($ids, $this->user->getVendorId());

                $user = $this->user->getData();
                if(!empty($user->image))
                        $validate['profile']['done'] = true;
                if($listing->title != "Untitle Listing" and !empty($listing->title))
                        $validate['title']['done'] = true;
                if($listing->description != "")
                        $validate['description']['done'] = true;
                if($listing->country_id != 0)
                        $validate['country']['done'] = true;
                if($listing->city_id != 0)
                        $validate['city']['done'] = true;
                if(!is_null($listing->lat))
                        $validate['location']['done'] = true;

                $photos = $this->listings->getPictures($listing->id);
                if(count($photos) > 0)
                        $validate['photos']['done'] = true;

                $overview = $this->listings->getOverviewOf($listing->id);
                if(!empty($overview->about))
                        $validate['overview']['done'] = true;

                switch($listing->main_type)
                {
                    case 5:
                        $validate['price'] = array(
                            'url' => '/provider/listings/pricing/',
                            'label' => 'Listing Pricing',
                            'desription' => 'Add the listing pricing',
                            'done' => false,
                        );
                        $validate['options'] = array(
                            'url' => '/provider/listings/rooms/',
                            'label' => 'Listing Rooms',
                            'desription' => 'Add at leaat one room type',
                            'done' => false,
                        );

                        $price = $this->listings->getBasicPrice($listing->id);
                        if(!is_null($price) and $price->price != 0)
                                $validate['price']['done'] = true;

                        $rooms = $this->listings->getHotelRooms($listing->id);
                        $done = true;
                        if(count($rooms) > 0) {
                            foreach($rooms as $room) {
                                if($room->people == 0 and $done) {
                                    $done = false;
                                }
                            }
                            if($done) {
                                $validate['options']['done'] = true;
                            } else {
                                $validate['options']['desription'] = 'Some of the roooms do not have the maximun of people allowed on the room';
                            }
                        }

                        break;
                    case 6:
                        $validate['price'] = array(
                            'url' => '/provider/listings/pricing/',
                            'label' => 'Listing Pricing',
                            'desription' => 'Add the listing pricing',
                            'done' => false,
                        );
                        $validate['capacity'] = array(
                            'url' => '/admin/listings/edit/',
                            'label' => 'Activity Capacity',
                            'description' => 'Define the activity maximun and minimun capacity required',
                            'done' => false
                        );
                        $price = $this->listings->getBasicPrice($listing->id);
                        if(!is_null($price) and $price->price != 0)
                                $validate['price']['done'] = true;  
                        
                        if(!is_null($listing->min) and !is_null($listing->max)) {
                            $validate['capacity']['done'] = true;
                        } else {
                            $capacities = $this->listings->getActivityTypes($listing->id);
                            $done=true;
                            if(count($capacities) > 0) {
                                foreach($capacities as $c) {
                                    if(($c->min == 0 or $c->max ==0) and $done) {
                                        $done = false;
                                    }
                                }
                                if($done) {
                                    $validate['capacity']['done'] = true;
                                } else {
                                    $validate['capacity']['description'] = 'Some of the Activity Types do not have a minimun or maximun capacity defined';
                                    $validate['capacity']['url'] = 'admin/listings/types/';
                                }
                            }
                        }

                        break;
                    default: break;
                }

                $errors = array();
                foreach($validate as $val){
                    if(!$val['done'])
                        $errors[] = 1;
                }

                if(count($errors) > 0){
                    $response = array(
                        'success' => 1,
                        'status'  => 'warning',
                        'text'    => count($errors).' step(s) remaining before activating',
                        'missing' => 'missing',
                        'id'      => $listing->id,
                    );
                }
                else {
                    $response = array(
                        'success' => 1,
                        'status'  => 'approve',
                        'text'    => 'This listing is ready to be activated!',
                        'missing' => '',
                        'id'      => $listing->id,
                    );
                }
                header('Content-type: application/json');
                echo json_encode($response); die;
            }
        }
    }
    
    public function activate2Action()
    {
        if($this->getRequest()->isPost()){
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity()){
                $this->user = new WS_User($auth->getIdentity());
                $validate = array(
                    'profile' => array(
                        'url' => '/provider/account',
                        'label' => 'Set company picture/logo',
                        'description' => 'Add your company\'s logo, so people can recognize you',
                        'done'  => false,
                    ),
                    'title' => array(
                        'url' => '/provider/listings/edit/',
                        'label' => 'Listing Title',
                        'desription' => 'Change the listing title or Assign one',
                        'done' => false,
                    ),
                    'description' => array(
                        'url' => '/provider/listings/overview/',
                        'label' => 'Listing Description',
                        'desription' => 'Add a listing short description',
                        'done' => false,
                    ),
                    'country' => array(
                        'url' => '/provider/listings/location/',
                        'label' => 'Listing Country',
                        'desription' => 'Assign a country to the listing',
                        'done' => false,
                    ),
                    'city' => array(
                        'url' => '/provider/listings/location/',
                        'label' => 'Listing City',
                        'desription' => 'Assign a city to the listing',
                        'done' => false,
                    ),
                    'location' => array(
                        'url' => '/provider/listings/location/',
                        'label' => 'Listing Map',
                        'desription' => 'Localize your listing in the map',
                        'done' => false,
                    ),
                    'photos' => array(
                        'url' => '/provider/listings/photos/',
                        'label' => 'Listing Photos',
                        'desription' => 'Add photos to the listing',
                        'done' => false,
                    ),
                    'overview' => array(
                        'url' => '/provider/listings/overview/',
                        'label' => 'Listing Overview',
                        'desription' => 'Add the Overview Information',
                        'done' => false,
                    ),
                );


                $ids = $_POST['listing'];

                $listing = $this->listings->getListing($ids);
                
                if($listing->status == 1) {
                    $response = array(
                        'success' => 1,
                        'status'  => 'active',
                        'text'    => '',
                        'missing' => '',
                        'id'      => $listing->id,
                    );
                    
                    header('Content-type: application/json');
                    echo json_encode($response); die;
                }

                $user = $this->users->getVendor($listing->vendor_id);
                if(!empty($user->image))
                        $validate['profile']['done'] = true;
                if($listing->title != "Untitle Listing" and !empty($listing->title))
                        $validate['title']['done'] = true;
                if($listing->description != "")
                        $validate['description']['done'] = true;
                if($listing->country_id != 0)
                        $validate['country']['done'] = true;
                if($listing->city_id != 0)
                        $validate['city']['done'] = true;
                if(!is_null($listing->lat))
                        $validate['location']['done'] = true;

                $photos = $this->listings->getPictures($listing->id);
                if(count($photos) > 0)
                        $validate['photos']['done'] = true;

                $overview = $this->listings->getOverviewOf($listing->id);
                if(!empty($overview->about))
                        $validate['overview']['done'] = true;

                switch($listing->main_type)
                {
                    case 5:
                        $validate['price'] = array(
                            'url' => '/provider/listings/pricing/',
                            'label' => 'Listing Pricing',
                            'desription' => 'Add the listing pricing',
                            'done' => false,
                        );
                        $validate['options'] = array(
                            'url' => '/provider/listings/rooms/',
                            'label' => 'Listing Rooms',
                            'desription' => 'Add at leaat one room type',
                            'done' => false,
                        );

                        $price = $this->listings->getBasicPrice($listing->id);
                        if(!is_null($price) and $price->price != 0)
                                $validate['price']['done'] = true;

                        $rooms = $this->listings->getHotelRooms($listing->id);
                        $done = true;
                        if(count($rooms) > 0) {
                            foreach($rooms as $room) {
                                if($room->people == 0 and $done) {
                                    $done = false;
                                }
                            }
                            if($done) {
                                $validate['options']['done'] = true;
                            } else {
                                $validate['options']['desription'] = 'Some of the roooms do not have the maximun of people allowed on the room';
                            }
                        }

                        break;
                    case 6:
                        $validate['price'] = array(
                            'url' => '/provider/listings/pricing/',
                            'label' => 'Listing Pricing',
                            'desription' => 'Add the listing pricing',
                            'done' => false,
                        );
                        $validate['capacity'] = array(
                            'url' => '/admin/listings/edit/',
                            'label' => 'Activity Capacity',
                            'description' => 'Define the activity maximun and minimun capacity required',
                            'done' => false
                        );
                        $price = $this->listings->getBasicPrice($listing->id);
                        if(!is_null($price) and $price->price != 0)
                                $validate['price']['done'] = true;  
                        
                        if(!is_null($listing->min) and !is_null($listing->max)) {
                            $validate['capacity']['done'] = true;
                        } else {
                            $capacities = $this->listings->getActivityTypes($listing->id);
                            $done=true;
                            if(count($capacities) > 0) {
                                foreach($capacities as $c) {
                                    if(($c->min == 0 or $c->max ==0) and $done) {
                                        $done = false;
                                    }
                                }
                                if($done) {
                                    $validate['capacity']['done'] = true;
                                } else {
                                    $validate['capacity']['description'] = 'Some of the Activity Types do not have a minimun or maximun capacity defined';
                                    $validate['capacity']['url'] = 'admin/listings/types/';
                                }
                            }
                        }
                        break;
                    default: break;
                }

                $errors = array();
                foreach($validate as $val){
                    if(!$val['done'])
                        $errors[] = 1;
                }

                if(count($errors) > 0){
                    $response = array(
                        'success' => 1,
                        'status'  => 'warning',
                        'text'    => count($errors).' step(s) remaining',
                        'missing' => 'missing',
                        'id'      => $listing->id,
                    );
                }
                else {
                    $response = array(
                        'success' => 1,
                        'status'  => 'approve',
                        'text'    => 'This listing is ready',
                        'missing' => '',
                        'id'      => $listing->id,
                    );
                }
                header('Content-type: application/json');
                echo json_encode($response); die;
            }
        }
    }
    
    public function sendrequestAction()
    {
        if($this->getRequest()->isPost())
        {
            try {
                $notifier = new WS_Notifier();
                $to      = 'cristian@tripfab.com';
                $subject = 'New Invitation Request';
                $message = 'New signup from: '.$_POST['company']."\n\n";
                $message.= 'Company: '.$_POST['company']."\n";
                $message.= 'Email: '.$_POST['email']."\n";
                $message.= 'Phone: ('.$_POST['code'].') '.$_POST['phone']."\n";
                $message.= 'Website: '.$_POST['website']."\n";
                $message.= 'Business: '.$_POST['business']."\n";
                $message.= 'Country: '.$_POST['country']."\n";

                $notifier->sendEmail($to, $subject, $message);

                echo 'Success';
            } catch(Exception $e) {
                echo $e->getMessage();
            }
        }
        die;
    }
    
    public function getcontactAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $user = new WS_User($auth->getIdentity());
            if($user->getRole() == 'provider'){
                $vendor = $user->getVendorData();
                $result = array(
                    'phone' => $vendor->phone,
                    'email' => $vendor->email,
                    'website' => $vendor->website
                );
                
                header('Content-type:text/json');
                echo json_encode($result); die;
            } elseif($user->getRole() == 'admin') {
                $vendor = $this->users->getVendor($this->_getParam('provider'));
                $result = array(
                    'phone' => $vendor->phone,
                    'email' => $vendor->email,
                    'website' => $vendor->website
                );
                
                header('Content-type:text/json');
                echo json_encode($result); die;
            }
            throw new Exception();
        }
        throw new Exception();
    }
    
    public function hidehelpmessageAction()
    {
        if($this->getRequest()->isPost())
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity()){
                $user = $auth->getIdentity();
                $colu = $this->_getParam('msg');
                $help = $this->listings->getHelpSettings($user->id);
                $help->$colu = 1;
                $help->save();
            }
        }
        die;
    }
    
    public function citiesAction()
    {
        if($this->_request->isPost()){
            $_landscape = $_POST['landscape_id'];
            $_country   = $_POST['country_id'];
            $_page      = $_POST['page'];
            
            $limit = 12;
            $offset = 12 * $_page;
            
            $landscape = ucwords($_landscape);
            
            if($landscape == 'All'){
                $cities = $this->places_db->fetchAll('parent_id = '.$_country, 'loves DESC',$limit,$offset);
            } else {
                $db = $this->places_db->getDefaultAdapter();
                $landscape = $this->landscapes_db->fetchRow("name = '".$landscape."'");

                $select = $db->select();
                $select->from('places');
                $select->join('places_landscapes', 'places.id = places_landscapes.place_id');
                $select->join('landscapes', 'places_landscapes.landscape_id = landscapes.id');
                $select->where('places.parent_id = ?', $_country);
                $select->where('landscapes.id = ?', $landscape->id);
                $select->order('places.loves DESC');
                $select->limit($limit, $offset);

                $cities = $db->fetchAll($select->assemble(), array(), Zend_Db::FETCH_OBJ);
            }
            if(!count($cities))
                throw new Exception();

            $this->view->cities  = $cities;
            $this->view->country = $this->places_db->fetchRow('id = '.$_country);
            $this->view->active_land = $_landscape;
        }
    }
    
    public function getplacesoptionsAction()
    {
        if($this->getRequest()->isPost()){
            $parent_id = $_POST['parent_id'];
            $select = $this->places_db->select();
            $select->where('parent_id = ?', $parent_id);
            $select->order('title ASC');
            $places = $this->places_db->fetchAll($select);
            if(!count($places))
                $places = array();
            
            $this->view->places = $places;
        }
    }
    
    public function getlistingimagesAction()
    {
        if($this->getRequest()->isPost()){
            $listing_id = $_POST['listing_id'];
            $select = $this->listings_db->select();
            $select->where('id = ?', $listing_id);
            $listing = $this->listings_db->fetchRow($select);
            
            $select = $this->pictures_db->select();
            $select->where('listing_id = ?', $listing->id);
            $pictures = $this->pictures_db->fetchAll($select);
            
            $this->view->pictures = $pictures;
            $this->view->listing  = $listing;
        }
    }
    
    public function getfriendsAction(){
        //var_dump($_POST); die;        
        if($this->getRequest()->isPost()){
            $auth = Zend_Auth::getInstance();
            if(!$auth->hasIdentity())
                    throw new Exception();
            
            $user = $auth->getStorage()->read();
            if($_POST['user_token'] != $user->token)
                    throw new Exception();
            if($_POST['form_id'] != md5('get_friends_'.$_POST['provider']))
                    throw new Exception('form_id violates');
            
            $inviter = new OpenInviter_openinviter();
            $oi_services = $inviter->getPlugins();
            
            $provider = '';
            switch($_POST['provider']){
                case 'gmail':
                    $provider = 'gmail';
                    break;
                case 'hotmail':
                    $provider = 'hotmail';
                    break;
                case 'yahoo':
                    $provider = 'yahoo';
                    break;
                case 'other':
                    $provider = $_POST['_provider'];
                    break;
                default:
                    throw new Exception('Provider not found');
            }
            
            $inviter->startPlugin($provider);
            $internal = $inviter->getInternalError();
            
            if($internal)
                    throw new Exception('Invalid Provider');
            
            $login = $inviter->login($_POST['email'], $_POST['password']);
            
            if(!$login)
                    throw new Exception('Login Failed');
            
            $_contacts = $inviter->getMyContacts();
            $contacts = array();
            foreach($_contacts as $k=>$c)
                $contacts[] = array($k=>$c);
            
            //echo '<pre>'; print_r($contacts); echo '</pre>'; die;
            $this->view->contacts = $contacts;
            $this->view->user     = $user;
            
        } else {
            throw new Exception('Just Post');
        }
        //echo 'Hola';
    }
    
    public function getsignupplacesAction(){
        if($this->getRequest()->isPost()){
            $places = new WS_PlacesService();
            $country = $places->getPlaceById($_POST['place']);
            $cities  = $places->getPlaces(3, $country->id);
            
            $this->view->country = $country;
            $this->view->cities  = $cities->toArray();
            
            //echo 'Success'; die;
        }
    }
    
    public function setestateAction(){
        if($this->getRequest()->isPost()){
            $auth = Zend_Auth::getInstance();
            if(!$auth->hasIdentity())
                    throw new Exception ('Access denied');            
            if(!empty($_POST['estate'])){
                $user = $auth->getIdentity();
                $users_db = new Model_Users();
                $user = $users_db->fetchRow('id = '.$user->id);
                if($_POST['user_token'] != $user->token)
                        throw new Exception('Invalid Token');
                if($_POST['form_id'] != md5($user->token.'set_estate'))
                        throw new Exception('Invalid From Id');
                
                $user->estate = addslashes(strip_tags($_POST['estate']));
                if(isset($_POST['show_estate_location']) && $_POST['show_estate_location'] == 1){
                    $user->estate_location = addslashes(strip_tags($_POST['estate_location']));
                } else {
                    $user->estate_location = '';
                }
                $user->save();
                echo 'Status updated!!'; die;
            }
        }
    }
    
    public function getconversationAction()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception();
        
        $id = (int) $this->_getParam('id');
        if(!is_int($id) || $id <= 0)
                throw new Exception();
        
        $user = $auth->getIdentity();
        $messages = new WS_MessagesService();
        $con = $messages->getConversationById($id, $user->id);
        
        $users = new WS_UsersService();
        
        if($con->starter == $user->id){
            //$con->snew = 0;
            $_sender = $users->get($con->wwith);
            if(is_null($_sender))
                throw new Exception();
            
            $con->snew = 0;
            $con->save();
            $image  = $_sender->image;
        }else {
            //$con->wnew = 0;
            $_sender = $users->get($con->starter);
            $con->wnew = 0;
            $con->save();
            $image  = $_sender->image;
        }
        
        $_messages = $messages->getConversationMessages($con->id);
        //var_dump($_messages); die;
        //$con->save();
        
        $result = array();
        $result['conversation'] = $con->toArray();
        $result['user'] = $user->id;
        foreach($_messages as $m){
            $image2 = '';
            if($m->user_id == $user->id) {
                $sender = 'Me';
                $image2  = $user->image;
            }
            else {
                $sender = $_sender->name;
                $image2  = $image;
            }
                
            $result['messages'][] = array(
                'sender' => $sender,
                'text'   => nl2br(stripslashes($m->text)),
                'date'   => date('M jS \a\t g:i a', strtotime($m->created)),
                'image'  => $image2
            );
        }
        
        header('Content-type: application/json');
        echo json_encode($result); die;
    }
    
    public function sendmsgvendorAction()
    {
        $result = array();
        $result['type'] = 'error';
        $result['message'] = 'Wrong Request';
        if($this->getRequest()->isPost()){
            try {
                $auth = Zend_Auth::getInstance();
                if(!$auth->hasIdentity())
                        throw new Exception ('You need to login before sending a message');
                
                $user = $auth->getIdentity();
                if($user->role_id != 2)
                        throw new Exception ('You dont have anough permissions to contact vendors');
                
                $message = $_POST['message'];
                if(empty($message))
                        throw new Exception('Message cannot be empty');
                
                $vendors = new Model_Vendors();
                $select = $vendors->select();
                $select->where('id = ?', $_POST['vendor']);
                $vendor = $vendors->fetchRow($select);
                if(is_null($vendor))
                        throw new Exception('Vendor Not Found');
                
                if(is_null($vendor->user_id))
                        throw new Exception($vendor->name . ' is not taking messages at this momment');
                
                $users = new Model_Users();
                $select = $users->select();
                $select->where('id = ?', $vendor->user_id);
                $vendor_user = $users->fetchRow($select);
                if(is_null($vendor_user))
                        throw new Exception($vendor->name . ' is not taking messages at this momment');
                
                $conversations = new Model_Conversations();
                $con = $conversations->fetchNew();
                $con->starter   = $user->id;
                $con->sname     = $user->name;
                $con->simage    = $user->image;
                $con->wwith     = $vendor->user_id;
                $con->wname     = $vendor->name;
                $con->wimage    = $vendor->image;
                $con->wnew      = 1;
                $con->msgcount  = 1;
                $con->created   = date('Y-m-d H:i:s');
                $con->updated   = date('Y-m-d H:i:s');
                
                $con->save();
                
                $messages = new Model_Messages();
                $msg = $messages->fetchNew();
                $msg->user_id           = $user->id;
                $msg->conversation_id   = $con->id;
                $msg->text              = $message;
                $msg->created           = $con->created;
                $msg->save();
                
                $notifier = new WS_Notifier($vendor->user_id);
                $notifier->newMessage($user);
                
                $result['type'] = 'success';
                $result['message'] = 'Your message to '.$vendor->name.' has been sent';
            } catch(Exception $e) {
                //throw $e;
                $result['type'] = 'error';
                $result['message'] = $e->getMessage();
            }
        }
        
        header('Content-type: application/json');
        echo json_encode($result); die;
    }
    
    public function sendmsguserAction()
    {
        $result = array();
        $result['type'] = 'error';
        $result['message'] = 'Wrong Request';
        if($this->getRequest()->isPost()){
            try {
                $auth = Zend_Auth::getInstance();
                if(!$auth->hasIdentity())
                        throw new Exception ('You need to login before sending a message');
                
                $vendor = $auth->getIdentity();
                if($vendor->role_id != 3)
                        throw new Exception ('You dont have anough permissions to contact vendors');
                
                $message = $_POST['message'];
                if(empty($message))
                        throw new Exception('Message cannot be empty');
                
                $users = new Model_Users();
                $select = $users->select();
                $select->where('id = ?', $_POST['user']);
                $user = $users->fetchRow($select);
                if(is_null($user))
                        throw new Exception('User Not Found');
                
                $conversations = new Model_Conversations();
                $con = $conversations->fetchNew();
                $con->starter   = $vendor->id;
                $con->sname     = $vendor->name;
                $con->simage    = $vendor->image;
                $con->wwith     = $user->id;
                $con->wname     = $user->name;
                $con->wimage    = $user->image;
                $con->wnew      = 1;
                $con->msgcount  = 1;
                $con->created   = date('Y-m-d H:i:s');
                $con->updated   = date('Y-m-d H:i:s');
                
                $con->save();
                
                $messages = new Model_Messages();
                $msg = $messages->fetchNew();
                $msg->user_id           = $vendor->id;
                $msg->conversation_id   = $con->id;
                $msg->text              = $message;
                $msg->created           = $con->created;
                $msg->save();
                
                $notifier = new WS_Notifier($user->id);
                $notifier->newMessage($vendor);
                
                $result['type'] = 'success';
                $result['message'] = 'Your message to '.$user->name.' has been sent';
            } catch(Exception $e) {
                //throw $e;
                $result['type'] = 'error';
                $result['message'] = $e->getMessage();
            }
        }
        
        header('Content-type: application/json');
        echo json_encode($result); die;
    }
    
    public function resetpasswordAction()
    {
        $result = array();
        $result['type'] = 'error';
        $result['message'] = 'Wrong Request';
        if($this->getRequest()->isPost()){
            try {
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity())
                        throw new Exception ('You dont have permissions to do this');
                
                $email = new Zend_Validate_EmailAddress();
                if(!$email->isValid($_POST['email']))
                        throw new Exception('Invalid Emil Address');
                
                $accounts = new WS_AccountService();
                if($accounts->validateEmail($_POST['email']))
                        throw new Exception("We couldn't find this email address");
                
                $users = new Model_Users();
                $select = $users->select();
                $select->where('email = ?', $_POST['email']);
                $user = $users->fetchRow($select);
                
                $tokens = new Zend_Db_Table('resetpass_tokens');
                $token  = $tokens->fetchNew();
                $token->user_id = $user->id;
                $token->token   = md5($user->email.time());
                $token->created = date('Y-m-d H:i:s');
                $token->save();
                
                $url = "https://".$_SERVER['HTTP_HOST']."/reset/".$user->email."/".$token->token;
                
                $to = $user->email;
                $subject = 'Password Reset Request';
                
                $message = "Hi ".$user->name."\n\n"
                         . "We have receive a new request to reset your passowrd\n"
                         . "If you made the request please confirm by clicking in trhe link\n\n"
                         . $url . "\n\n"
                         . "If you did not request this change please ognore this message";
                        
                
                $notifier = new WS_Notifier($user->id);
                $notifier->passwordReset($url);
                
                $result['type'] = 'success';
                $result['message'] = 'We have sent you confirmation message to the provided email address';
            } catch (Exception $e) {
                $result['type'] = 'error';
                $result['message'] = $e->getMessage();
            }
        }
        header('Content-type: application/json');
        echo json_encode($result); die;
    }
    
    public function postquestionAction(){
        $result = array();
        $result['type'] = 'error';
        $result['message'] = 'Wrong Request';
        if($this->getRequest()->isPost()){
            try {
                $auth = Zend_Auth::getInstance();
                if(!$auth->hasIdentity())
                        throw new Exception ('You need to login before asking a question');
                
                $user = $auth->getIdentity();
                if($user->role_id != 2)
                        throw new Exception ('You dont have anough permissions to ask questions');
                
                $question = $_POST['question'];
                if(empty($question))
                        throw new Exception('Question cannot be empty');
                
                $listings = new Model_Listings();
                $select = $listings->select();
                $select->where('id = ?', $_POST['listing']);
                $listing = $listings->fetchRow($select);
                if(is_null($listing))
                        throw new Exception('We could find the listing');
                
                $questions = new Model_PublicQuestions();
                $q = $questions->fetchNew();
                $q->user_id    = $user->id;
                $q->listing_id = $listing->id;
                $q->question   = $question;
                $q->replied    = 0;
                $q->trashed    = 0;
                $q->created    = date('Y-m-d H:i:s');
                $q->updated    = date('Y-m-d H:i:s');
                
                $q->save();
                
                $this->view->question = $q;
                $this->view->user     = $user->name;
                
                $result['type']     = 'success';
                $result['message']  = 'Your question has been posted';
                $result['html']     = $this->view->render('ajax/postquestion.phtml');
            } catch(Exception $e) {
                $result['type'] = 'error';
                $result['message'] = $e->getMessage();
            }
        }
        header('Content-type: application/json');
        echo json_encode($result); die;
    }


    public function addtofavoritesAction()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
            die;
        
        $id = $this->_getParam('id','default');
        if($id == 'default')
            die;
        
        $user = $auth->getIdentity();
        $favorites = new Zend_Db_Table('favorites');
        $select = $favorites->select();
        $select->where('listing_id = ?', $id);
        $select->where('user_id = ?', $user->id);
        $row = $favorites->fetchRow($select);
        if(!is_null($row)){
            $row->delete();
        } else {
            $row = $favorites->fetchNew();
            $row->listing_id = $id;
            $row->user_id    = $user->id;
            $row->save();
        }
    }
    
    public function addtotripAction()
    {
        if($this->getRequest()->isPost()){
            $auth = Zend_Auth::getInstance();
            if(!$auth->hasIdentity())
                die;
            
            $data = $_POST;
            $user = $auth->getIdentity();
            
            $trips = new Model_Trips();
            $listings = new Model_TripListings();
            $trip  = $trips->fetchRow("id = {$data['trip']}");
            
            if($trip->token != $data['token']) die;
            
            $fday = strtotime($trip->start);
            $eday = strtotime($trip->end);
            $listing_date = date('Y-m-d', strtotime($data['start']));
            $lday = strtotime($listing_date);
            $hour = date('G', strtotime($data['start']));
            
            $time = 0;
            if($hour >= 0 and $hour <= 5)
                $time = 3;
            if($hour > 5 and $hour <= 12)
                $time = 1;
            if($hour > 12 and $hour <= 18)
                $time = 2;
            if($hour > 18)
                $time = 3;
            
            if($eday < $lday) die;
            
            $dif = $lday - $fday;
            
            if($dif < 0) die;
            
            $day = $dif / 86400;
            $day++;
            
            //echo $time."\n"; die;
            
            $row = $listings->fetchNew();
            $row->itinerary_id = $trip->id;
            $row->listing_id   = $data['listing'];
            $row->day          = $day;
            $row->time         = $time;
            $row->city_id      = $data['city'];
            $row->country_id   = $data['country'];
            $row->start        = date('Y-m-d G:i:s', strtotime($data['start']));
            $row->end          = date('Y-m-d G60:i:s', strtotime($data['start']) + 7200);
            
            $country = $listings->fetchAll("itinerary_id = {$trip->id} and country_id = {$row->country_id}");
            if(is_null($country)){
                $trip->countries = $trip->countries + 1;
                $trip->save();
            }
            $city = $listings->fetchAll("itinerary_id = {$trip->id} and city_id = {$row->city_id}");
            if(is_null($country)){
                $trip->cities = $trip->cities + 1;
                $trip->save();
            }
            
            $aux5 = $listings->fetchAll("itinerary_id = {$trip->id} and day = {$day} and time = {$time}");
            if(count($aux5) > 0){
                $aux  = $aux5->toArray();
                $aux[]= $row->toArray();
                $aux2 = array();
                $limit= count($aux);

                while(count($aux) > 0){
                    for($i=0;$i<$limit; $i++){ if(isset($aux[$i])){ $crr = $i; $i=$limit;}}

                    foreach($aux as $ixx => $auxixx){
                        if($ixx != $crr){
                            $aux_crr = strtotime($aux[$crr]['start']);
                            $aux_ixx = strtotime($auxixx['start']);
                            if($aux_crr > $aux_ixx){ $crr = $ixx; }
                        }
                    }
                    if(isset($aux5[$crr]))
                        $aux2[] = $aux5[$crr];
                    else
                        $aux2[] = $row;
                    
                    unset($aux[$crr]);
                }

                foreach($aux2 as $sort => $r){
                    $r->sort = $sort + 1;
                    $r->save();
                }
            } else {
                $row->sort = 1;
                $row->save();
            }
            
            $trip->listings = $trip->listings + 1;
            $trip->save();
            
            echo $row->id; die;
            
        } else die;
    }
    
    public function addtotrip2Action()
    {
        $result = array();
        $result['type']     = 'error';
        $result['message']  = 'Wrong Request';
        
        if($this->getRequest()->isPost())
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity()){
                if($_POST['trip'] == 'new'){
                    try {
                        $user = $auth->getIdentity();

                        $trips = new Model_Itineraries();
                        $trip_listings = new Zend_Db_Table('itinerary_listings');
                        
                        if(empty($_POST['title']) || $_POST['title'] == 'Trip Name')
                                throw new Exception('You need to add a name to the trip');
                        
                        $listings = new Model_Listings();
                        $select = $listings->select();
                        $select->where('id = ?', $_POST['listing']);
                        $listing = $listings->fetchRow($select);
                        if(is_null($listing))
                                throw new Exception('Listing not found');
						
						$trip = $trips->fetchNew();
						$trip->title = $_POST['title'];
                        $trip->user_id = $user->id;
                        $trip->country_id = $listing->country_id;
						
						if((isset($_POST['start']) and !empty($_POST['start']) and $_POST['start'] != 'Starting Date') and
								(isset($_POST['end']) and !empty($_POST['end']) and $_POST['end'] != 'Ending Date')){
							
							$start = date('M j Y', strtotime($_POST['start']));
							$end   = date('M j Y', strtotime($_POST['end']));
							if($start != $_POST['start'] || $end != $_POST['end'])
								throw new Exception("Sorry, we couldn't understand the date Formats");
							
							$trip->start = date('Y-m-d', strtotime($start));;
	                        $trip->end = date('Y-m-d', strtotime($end));
							
							$fday = strtotime($start);
							$lday = strtotime($end);
							$days = $lday - $fday;
							$days = $days / 86400;
							
							$trip->days         = $days + 1;
						} else {
							$trip->start = '0000-00-00';
							$trip->end   = '0000-00-00';
						}
						
                        $trip->created = date('Y-m-d H:i:s');
                        $trip->updated = date('Y-m-d H:i:s');
                        $trip->save();
						
                        $trip->token = md5($trip->id.$user->id.time());
                        $trip->save();

                        $row = $trip_listings->fetchNew();
                        $row->user_id       = $user->id;
                        $row->itinerary_id  = $trip->id;
                        $row->listing_id    = $listing->id;
                        $row->city_id       = $listing->city_id;
                        $row->country_id    = $listing->country_id;
                        $row->save();

                        $trip->listings = $trip->listings + 1;

                        $cities = new Zend_Db_Table('itinerary_cities');
                        $select = $cities->select();
                        $select->where('itinerary_id = ?', $trip->id);
                        $select->where('city_id = ?', $row->city_id);
                        $city = $cities->fetchRow($select);
                        if(is_null($city)){
                            $select = $cities->select();
                            $select->where('itinerary_id = ?', $trip->id);
                            $select->where('country_id = ?', $row->country_id);
                            $country = $cities->fetchRow($select);
                            if(is_null($country)){
                                $trip->countries = $trip->countries + 1;
                            }

                            $city = $cities->fetchNew();
                            $city->itinerary_id = $trip->id;
                            $city->city_id = $row->city_id;
                            $city->country_id = $row->country_id;
                            $city->save();

                            $trip->cities = $trip->cities + 1;
                        }

                        if(!$trip->image)
                            $trip->image = $listing->image;

                        $trip->save();

                        $result['type']     = 'newtrip';
                        $result['message']  = $trip->title.' trip has been created and listing '.$listing->title.' has been added to it';
                        $result['tripid']   = $trip->id;
                        $result['triptitle']= $trip->title;
                    } catch(Exception $e){
                        $result['type']     = 'error';
                        $result['message']  = $e->getMessage();
                    }
                } else {
                    try {
                        $user = $auth->getIdentity();

                        $trips = new Model_Itineraries();
                        $trip_listings = new Zend_Db_Table('itinerary_listings');

                        $select = $trips->select();
                        $select->where('id = ?', $_POST['trip']);
                        $select->where('user_id = ?', $user->id);
                        $trip = $trips->fetchRow($select);
                        if(is_null($trip))
                                throw new Exception('Trip not found');

                        $listings = new Model_Listings();
                        $select = $listings->select();
                        $select->where('id = ?', $_POST['listing']);
                        $listing = $listings->fetchRow($select);
                        if(is_null($listing))
                                throw new Exception('Listing not found');

                        $select = $trip_listings->select();
                        $select->where('listing_id = ?', $listing->id);
                        $select->where('itinerary_id = ?', $trip->id);
                        $select->where('user_id = ?', $user->id);
                        $fail = $trip_listings->fetchRow($select);
                        if(!is_null($fail))
                                throw new Exception($listing->title . ' already exists in '.$trip->title);

                        $row = $trip_listings->fetchNew();
                        $row->user_id       = $user->id;
                        $row->itinerary_id  = $trip->id;
                        $row->listing_id    = $listing->id;
                        $row->city_id       = $listing->city_id;
                        $row->country_id    = $listing->country_id;
                        $row->save();

                        $trip->listings = $trip->listings + 1;

                        $cities = new Zend_Db_Table('itinerary_cities');
                        $select = $cities->select();
                        $select->where('itinerary_id = ?', $trip->id);
                        $select->where('city_id = ?', $row->city_id);
                        $city = $cities->fetchRow($select);
                        if(is_null($city)){
                            $select = $cities->select();
                            $select->where('itinerary_id = ?', $trip->id);
                            $select->where('country_id = ?', $row->country_id);
                            $country = $cities->fetchRow($select);
                            if(is_null($country)){
                                $trip->countries = $trip->countries + 1;
                            }

                            $city = $cities->fetchNew();
                            $city->itinerary_id = $trip->id;
                            $city->city_id = $row->city_id;
                            $city->country_id = $row->country_id;
                            $city->save();

                            $trip->cities = $trip->cities + 1;
                        }

                        if(!$trip->image)
                            $trip->image = $listing->image;

                        $trip->save();

                        $result['type']     = 'success';
                        $result['message']  = 'The listing '.$listing->title.' has been added to '.$trip->title;
                    } catch(Exception $e){
                        $result['type']     = 'error';
                        $result['message']  = $e->getMessage();
                    }
                }
            }
            else {
                $result['type'] = 'error';
                $result['message'] = 'You need to login as a user';
            }
        }
        else 
        {
            $result['message'] = 'Wrong Request';
        }
        
        header('Content-type: application/json');
        echo json_encode($result); die;
    }
    
    public function getlistingsAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $user = $auth->getIdentity();
            if($user->role_id == 2){
                $trips = $this->trips->getFutureTripsBy($user->id, true);
                $this->view->user  = $user;
                $this->view->trips = $trips; 
            }
         
        }
        
        $cat      = $this->_getParam('category', 'activities');
        $subcat   = $this->_getParam('cats', 'all');
        $sort     = $this->_getParam('sort', 'newest');
        $stars    = $this->_getParam('stars', 'all');
        $pricemax = $this->_getParam('pricemax', 3000);
        $pricemin = $this->_getParam('pricemin', 0);
        $page = $this->_getParam('page', 1);
        
        $images = array(
            'all' => 'All-Small-0',
            'activities' => 'All-Activities-0',
            'entertaiment' => 'Entertainment-Small-0',
            'tourist-sights' => 'Sights-Small-0',
            'restaurants' => 'Restaurant-Small-0',
            'hotels' => 'Hotels-Small-0'
        );
        
        $this->view->image = $images[$cat];
        
        $countries   = $this->places->getPlaces(2);
        
        $country_idf = $this->getRequest()->getParam('country');
        $city_idf    = $this->getRequest()->getParam('city');
        $country     = $this->places->getPlaceByIdf($country_idf);
        $region      = $this->places->getPlaceById($country->parent_id);
        
        $city = null;
        if($city_idf != 'default')
            $city = $this->places->getPlaceByIdf($city_idf, 3, $country->id);
        
        $categories  = $this->listings->getMainCategories(true);
        
        $sortOptions = array(
            'newest' => 'Newest',
            'popular' => 'Most Popular',
            'name' => 'Name',
            'free' => 'Free',
            'lowest' => 'Lowest Price',
            'highest' => 'Highest Price',
            'rating' => 'Rating' 
        );
        
        $subcategories = null;
        $category = null;
        if($cat != 'all'){
            $category = $this->listings->getCategoryByIdf($cat);
            $subcategories = $this->listings->getSubCategoriesOf($category->id);
        }
        
        $this->view->categ = ($cat == 'all') ? 'things to do' : $category->name; 
        
        if(!is_null($city))
            $ls_count = $this->listings->countListings($city);
        else
            $ls_count = $this->listings->countListings(null, null, $country->id);
        
        if(!is_null($city)) {
            $listings = $this->listings->getListings2($city, $cat, $subcat, $sort, $stars, $pricemin, $pricemax, null, $page);
            $this->view->listing_count = $this->listings->getListingsCount($city, $cat, $subcat, $sort, $stars, $pricemin, $pricemax, null, $page);
        }
        else {
            $listings = $this->listings->getListings2(null, $cat, $subcat, $sort, $stars, $pricemin, $pricemax, $country->id,$page);
            $this->view->listing_count = $this->listings->getListingsCount(null, $cat, $subcat, $sort, $stars, $pricemin, $pricemax, $country->id,$page);
        }
        
        //var_dump($this->view->listing_count); die;
        
        $this->view->class  = 'landscape-1';
        $this->view->cat    = $cat;
        $this->view->subcat = $subcat;
        $this->view->sort   = $sort;
        $this->view->stars  = $stars;
        
        $this->view->countries = $countries;
        
        $this->view->region  = $region;
        $this->view->country = $country;
        $this->view->city    = $city;
        
        $this->view->categories    = $categories;
        $this->view->sortOptions   = $sortOptions;
        $this->view->subcategories = $subcategories;
        $this->view->ls_count      = $ls_count;
        
        $this->view->listings = $listings;
    }
    
    public function getlistings2Action()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $user = $auth->getIdentity();
            if($user->role_id == 2){
                $trips = $this->trips->getFutureTripsBy($user->id, true);
                $this->view->user  = $user;
                $this->view->trips = $trips; 
            }
         
        }
        
        $cat      = $this->_getParam('category', 'activities');
        $subcat   = $this->_getParam('cats', 'all');
        $sort     = $this->_getParam('sort', 'newest');
        $stars    = $this->_getParam('stars', 'all');
        $pricemax = $this->_getParam('pricemax', 3000);
        $pricemin = $this->_getParam('pricemin', 0);
        $page = $this->_getParam('page', 1);
        
        $images = array(
            'all' => 'All-Small-0',
            'activities' => 'All-Activities-0',
            'entertaiment' => 'Entertainment-Small-0',
            'tourist-sights' => 'Sights-Small-0',
            'restaurants' => 'Restaurant-Small-0',
            'hotels' => 'Hotels-Small-0'
        );
        
        $this->view->image = $images[$cat];
        
        $countries   = $this->places->getPlaces(2);
        
        $country_idf = $this->getRequest()->getParam('country');
        $city_idf    = $this->getRequest()->getParam('city');
        $country     = $this->places->getPlaceByIdf($country_idf);
        $region      = $this->places->getPlaceById($country->parent_id);
        
        $city = null;
        if($city_idf != 'default')
            $city = $this->places->getPlaceByIdf($city_idf, 3, $country->id);
        
        $categories  = $this->listings->getMainCategories(true);
        
        $sortOptions = array(
            'newest' => 'Newest',
            'popular' => 'Most Popular',
            'name' => 'Name',
            'free' => 'Free',
            'lowest' => 'Lowest Price',
            'highest' => 'Highest Price',
            'rating' => 'Rating' 
        );
        
        $subcategories = null;
        $category = null;
        if($cat != 'all'){
            $category = $this->listings->getCategoryByIdf($cat);
            $subcategories = $this->listings->getSubCategoriesOf($category->id);
        }
        
        $this->view->categ = ($cat == 'all') ? 'things to do' : $category->name; 
        
        if(!is_null($city))
            $ls_count = $this->listings->countListings($city);
        else
            $ls_count = $this->listings->countListings(null, null, $country->id);
        
        if(!is_null($city)) 
            $listings = $this->listings->getListings2($city, $cat, $subcat, $sort, $stars, $pricemin, $pricemax, null, $page);
        else
            $listings = $this->listings->getListings2(null, $cat, $subcat, $sort, $stars, $pricemin, $pricemax, $country->id,$page);
        
        
        if(count($listings) == 0){
            echo ''; die;
        }
            
        $this->view->listing_count = count($listings);
        //var_dump($this->view->listing_count); die;
        
        $this->view->class  = 'landscape-1';
        $this->view->cat    = $cat;
        $this->view->subcat = $subcat;
        $this->view->sort   = $sort;
        $this->view->stars  = $stars;
        
        $this->view->countries = $countries;
        
        $this->view->region  = $region;
        $this->view->country = $country;
        $this->view->city    = $city;
        
        $this->view->categories    = $categories;
        $this->view->sortOptions   = $sortOptions;
        $this->view->subcategories = $subcategories;
        $this->view->ls_count      = $ls_count;
        
        $this->view->listings = $listings;
        
        //echo 'asd'; die;
    }
    
    public function gettripsAction()
    {
        $places = new WS_PlacesService();
        $trips  = new WS_TripsService();
        
        $id = $this->_getParam('country');
        $country = $places->getPlaceById($id);
        if(is_null($country))
                throw new Exception();
        
        $price = array(
            'min' => $this->_getParam('pricemin'),
            'max' => $this->_getParam('pricemax'),
        );
        $days = array(
            'min' => $this->_getParam('daymin'),
            'max' => $this->_getParam('daymax'),
        );
        $people   = $this->_getParam('people');
        
        $category = $this->_getParam('category');
        
        $trips = $trips->getTripsOf($country->id, $price, $days, $people, $category);
        $this->view->country = $country;
        $this->view->trips   = $trips;
    }
    
    public function getsearchtagsAction()
    {
        $search = new Model_Search();
        $tags = $search->fetchAll();
        $tags = $tags->toArray();
        
        header('Content-type: application/json');
        echo json_encode($tags); die;
    }
    
    public function gettripsearchtagsAction()
    {
        $json = new WS_JsonService();
        $tags = $json->getCountryTerms();
        
        header('Content-type: application/json');
        echo $tags; die;
    }
    
    public function getindexcitiesAction()
    {
        $id = $_GET['id'];
        $country   = $this->places->getPlaceById($id);
        $cities    = $this->places->getPlaces(3, $country->id);
        
        $this->view->cities    = $cities;
        $this->view->country   = $country;
    }
    
    public function testAction()
    {
        $q = $_GET['q'];
        
        $google = new WS_Googlemap();
        $address = $google->findAddress($q);
        
        $place = $address->Placemark[0];
        
        $north = $place->ExtendedData->LatLonBox->north;
        $south = $place->ExtendedData->LatLonBox->south;
        $west  = $place->ExtendedData->LatLonBox->west;
        $east  = $place->ExtendedData->LatLonBox->east;
        
        $ratio = ($east - $west) * 111;
        if($ratio < 10){
            $west  = $place->Point->coordinates[0] - (10 / 111);
            $east  = $place->Point->coordinates[0] + (10 / 111);
        }
        
        $ratio = ($north - $south) * 111;
        if($ratio < 5){
            $north  = $place->Point->coordinates[1] + (5 / 111);
            $south  = $place->Point->coordinates[1] - (5 / 111);
        }
    
        
        $listings = new Model_Listings();
        $select = $listings->select();
        $select->where("lat BETWEEN {$south} and {$north}");
        $select->where("lng BETWEEN {$west} and {$east}");
        //$select->where('main_type = 4');
        
        $bounds = array($north, $south, $east, $west);
        
        $result = $listings->fetchAll($select);
        $result = $result->toArray();
        
        $this->view->listings = json_encode($result);
        $this->view->bounds   = json_encode($bounds);
    }
    
    public function getquoteAction(){
        try {
            if(!isset($_GET['option']))
                throw new Exception ("Option Missing", 1);
            else {
                if(!isset($_GET['checkin']) || empty($_GET['checkin']))
                    throw new Exception ("Checking Missing", 1);

                $id = $_GET['id'];
                $listings = new WS_ListingService();
                $listing = $listings->getListing($id);
                if($listing->token != $_GET['token']){
                    if(md5($listing->token) != $_GET['token'])
                        throw new Exception ("Error, try later", 1);
                }

                if($listing->main_type == 5){
                    if(!isset($_GET['checkout']) || empty($_GET['checkout']))
                        throw new Exception ("Checkout Missing", 1);
                }
                
                if($listing->main_type == 6) {
                    if((is_null($listing->min) || is_null($listing->max)) && 
                            (!isset($_GET['capacity']) || empty($_GET['capacity']))){
                        throw new Exception ("Capacity Missing", 1);
                    }
                }
                $adults   = $_GET['adults'];
                $kids     = $_GET['kids'];
                $checkin  = $_GET['checkin'];
                if($listing->main_type == 5)
                    $checkout = $_GET['checkout'];
                else
                    $checkout = null;
                
                $option = $_GET['option'];
                
                if($listing->main_type == 6)
                    $capacity = $_GET['capacity'];
                else
                    $capacity = null;

                $quote  = $listings->getQuote(
                             $listing, $adults, $kids, $checkin, $checkout, null, $option, $capacity);
                
                if($quote->available)
                    echo '$'.$quote->subtotal;
                else
                    throw new Exception($quote->error, 1);
                die;
            }
            die;  
        } catch(Exception $e) {
            //if($e->getCode() == 1){
                
            //} else {
                
            //}
            throw $e;
        }
    }
    
    public function postmessageAction()
    {
        if($this->getRequest()->isPost())
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $user = $auth->getIdentity();
                $conv = $_POST['conversation'];
                $msag = $_POST['text'];
                
                $conversations = new Model_Conversations();
                $select = $conversations->select();
                $select->where('id = ?', $conv);
                
                $conversation = $conversations->fetchRow($select);
                if(is_null($conversation))
                        throw new Exception();
                
                if($conversation->starter != $user->id && $conversation->wwith != $user->id)
                        throw new Exception();
                
                $users = new WS_UsersService();
                if($conversation->starter == $user->id)
                {
                    $conversation->wnew     = 1;
                    $conversation->updated  = date('Y-m-d G:i:s');
                    $conversation->msgcount = $conversation->msgcount + 1;
                    
                    $conversation->save();                    
                    
                    $messages = new Model_Messages();
                    $message  = $messages->fetchNew();
                    
                    $message->user_id         = $user->id;
                    $message->conversation_id = $conversation->id;
                    $message->text            = strip_tags($msag);
                    $message->created         = date('Y-m-d G:i:s');
                    
                    $message->save();
                    
                    $notifier = new WS_Notifier($conversation->wwith);
                    $notifier->newMessage($user);
                    
                    $_sender = $users->get($conversation->wwith);
                    $image   = $_sender->image;
                } 
                elseif($conversation->wwith == $user->id) 
                {
                    $conversation->snew     = 1;
                    $conversation->updated  = date('Y-m-d G:i:s');
                    $conversation->msgcount = $conversation->msgcount + 1;
                    
                    $conversation->save();
                    
                    $messages = new Model_Messages();
                    $message  = $messages->fetchNew();
                    
                    $message->user_id         = $user->id;
                    $message->conversation_id = $conversation->id;
                    $message->text            = strip_tags($msag);
                    $message->created         = date('Y-m-d G:i:s');
                    
                    $message->save();
                    
                    $notifier = new WS_Notifier($conversation->starter);
                    $notifier->newMessage($user);
                    
                    $_sender = $users->get($conversation->starter);
                    $image   = $_sender->image;
                }
                else 
                {
                    throw new Exception();
                }
                
                
                $messagesService = new WS_MessagesService();
                $_messages = $messagesService->getConversationMessages($conversation->id);

                $result = array();
                $result['conversation'] = $conversation->toArray();
                $result['user'] = $user->id;
                foreach($_messages as $m){
                    $result['messages'][] = array(
                        'sender' => ($m->user_id == $user->id) ? 'Me' : $_sender->name,
                        'text'   => nl2br(stripslashes($m->text)),
                        'date'   => date('M jS \a\t g:i a', strtotime($m->created)),
                        'image'  => ($m->user_id == $user->id) ? $user->image : $_sender->image
                    );
                }

                header('Content-type: application/json');
                echo json_encode($result); die;
                        
            }
        }    
        die;
    }
    
    public function removemessagesAction()
    {
        if($this->getRequest()->isPost())
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $user = $auth->getIdentity();
                $id   = $_POST['message'];
                
                $conversations = new Model_Conversations();
                $select = $conversations->select();
                $select->where('id = ?', $id);
                $con = $conversations->fetchRow($select);
                if(is_null($con))
                        throw new Exception('Conversation not found');
                
                if($con->starter == $user->id){
                    $con->sdelete = 1;
                    $con->save();
                } else {
                    $con->wdelete = 1;
                    $con->save();
                }
                
                echo $id;
            }
        }
        die;
    }
    
    public function updatetriplistingAction()
    {
        if($this->getRequest()->isPost()){
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity()){
                $user = $auth->getIdentity();
                
                $trips = new Model_Itineraries();
                $select = $trips->select();
                $select->where('id = ?', $_POST['trip']);
                $select->where('user_id = ?', $user->id);
                $trip = $trips->fetchRow($select);
                if(is_null($trip))
                        throw new Exception('Trip not found');
                
                $triplistings = new Zend_Db_Table('itinerary_listings');
                $select = $triplistings->select();
                $select->where('id = ?', $_POST['listing']);
                $select->where('itinerary_id = ?', $trip->id);
                $select->where('user_id = ?', $user->id);
                $listing = $triplistings->fetchRow($select);
                if(is_null($listing))
                        throw new Exception();
                
                $times = array(
                    'Morning'   => 1,
                    'Afternoon' => 2,
                    'Night'     => 3,
                    'stay'      => 4,
                );
                
                $updatePrice = false;
                if(is_null($listing->day) || is_null($listing->time))
                    $updatePrice = true;                
                
                if($_POST['time'] == 'stay'){
                    $listin2 = $triplistings->fetchNew();
                    $listin3 = $listing->toArray();
                    unset($listin3['id']);
                    $listin2->setFromArray($listin3);
                    
                    $listin2->day  = $_POST['day'];
                    $listin2->time = $times[$_POST['time']];
                    
                    $listin2->save();
                } else {
                    $listing->day  = $_POST['day'];
                    $listing->time = $times[$_POST['time']];
                    
                    $listing->save();
                }
                
                if($updatePrice){
                    $listings = new Model_Listings();
                    $list = $listings->fetchRow("id = {$listing->listing_id}");

                    $trip->price = $trip->price + $list->price;
                    $trip->save();
                }
                
                echo $trip->price; die;
            } else {
                throw new Exception('No access allowed');
            }
        } else {
            throw new Exception('Wrong request');
        }
    }
    
    public function updatesortingAction()
    {
        if($this->getRequest()->isPost()){
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity()){
                $user = $auth->getIdentity();
                
                $trips = new Model_Itineraries();
                $select = $trips->select();
                $select->where('id = ?', $_POST['data'][0]['trip']);
                $select->where('user_id = ?', $user->id);
                $trip = $trips->fetchRow($select);
                if(is_null($trip))
                        throw new Exception();
                
                $triplistings = new Zend_Db_Table('itinerary_listings');
                foreach($_POST['data'] as $i => $data){
                    $select = $triplistings->select();
                    $select->where('id = ?', $data['listingid']);
                    $select->where('itinerary_id = ?', $trip->id);
                    $select->where('user_id = ?', $user->id);
                    $listing = $triplistings->fetchRow($select);
                    if(is_null($listing))
                            throw new Exception('Listing not found');
                    
                    $listing->sort = $data['sort'];
                    $listing->save();
                }
                
                echo 'Success'; die;
            } else {
                throw new Exception('No access allowed');
            }
        } else {
            throw new Exception('Wrong Request');
        }
    }
    
    public function removefromtripAction()
    {
        if($this->getRequest()->isPost()){
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity()){
                $user = $auth->getIdentity();
                
                $trips = new Model_Itineraries();
                $select = $trips->select();
                $select->where('id = ?', $_POST['trip']);
                $select->where('user_id = ?', $user->id);
                $trip = $trips->fetchRow($select);
                if(is_null($trip))
                        throw new Exception('Trip not found');
                
                $triplistings = new Zend_Db_Table('itinerary_listings');
                $select = $triplistings->select();
                $select->where('id = ?', $_POST['listing']);
                $select->where('itinerary_id = ?', $trip->id);
                $select->where('user_id = ?', $user->id);
                $listing = $triplistings->fetchRow($select);
                if(is_null($listing))
                        throw new Exception('Listing not found');
                
                $times = array(
                    'Morning'   => 1,
                    'Afternoon' => 2,
                    'Night'     => 3,
                );
                
                $listings = new Model_Listings();
                $list = $listings->fetchRow("id = {$listing->listing_id}");
                
                $trip->price = $trip->price - $list->price;
                $trip->save();
                
                if($listing->time == 4) {
                    $listing->delete();
                } else {
                    $listing->day = NULL;
                    $listing->time = NULL;

                    $listing->save();
                }
                
                echo $trip->price; die;
            } else {
                throw new Exception('No access allowed');
            }
        } else {
            throw new Exception('Wrong request');
        }
    }
    
    public function placesAction() {
        switch (@$_GET['type']) {
            case 'region':
                $regions = $this->places->getPlaces(1);
                self::jsonEcho(json_encode(array('attempt' => 'success', 'error_code' => '0', 'description' => '', 'data' => $regions->toArray())));
                break;
            case 'country':
                $countries = $this->places->getPlaces(2);
                self::jsonEcho(json_encode(array('attempt' => 'success', 'error_code' => '0', 'description' => '', 'data' => $countries->toArray())));
                break;
            case 'city':
                $countryId = $_GET['c'];
                $cities = $this->places->getPlaces(3, $countryId);
                self::jsonEcho(json_encode(array('attempt' => 'success', 'error_code' => '0', 'description' => '', 'data' => $cities->toArray())));
                break;
            default:
                self::jsonEcho(json_encode(array('attempt' => 'fail', 'error_code' => '404', 'description' => 'Invalid API call')));
        }
    }

    public function listingsAction() {
        switch (@$_GET['type']) {
            case 'type':
                $listingTypes = $this->listings->getMainCategories(true);
                self::jsonEcho(json_encode(array('attempt' => 'success', 'error_code' => '0', 'description' => '', 'data' => $listingTypes)));
                break;
            default:
                self::jsonEcho(json_encode(array('attempt' => 'fail', 'error_code' => '404', 'description' => 'Invalid API call')));
        }
    }

    public function travellerAction() {
        $userId = $_GET['user'];
        $panel = $_GET['page'];
        switch ($panel) {
            case 1:
                $user = $this->users->getFull($userId);
                $this->view->user = $user;
                $this->render('admin/travellerview1');
                break;
            case 2:
                $trips = $this->trips->getTripsBy($userId);
                $this->view->trips = $trips;
                $this->render('admin/travellerview2');
                break;
            case 3:
                $reservations = $this->reservations->getUserHistory($userId);
                $this->view->reservations = $reservations;
                $this->render('admin/travellerview3');
                break;
            case 4:
                $reviewes = $this->reviewes->getReviewsBy($userId);
                $this->view->reviewes = $reviewes;
                $this->render('admin/travellerview4');
                break;
            default:
                throw new Exception("Invalid panel type");
        }
    }

    public function partnerAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());

        if($user->getRole() != 'admin')
                throw new Exception('No access allowed');

        $userId = $_GET['user'];
        $panel = $_GET['page'];
        switch ($panel) {
            case 1:
                $user = $this->vendors->getVendorDetailsById($userId);
                $this->view->user = $user;
				$countries = $this->places->getPlaces(2);
				$this->view->countries = $countries;
                $this->render('admin/partnerview1');
                break;
            case 2:
                $listings = $this->listings->getVendorListings($userId);
                $this->view->listings = $listings;
                $this->render('admin/partnerview2');
                break;
            case 3:
                $reservations = $this->reservations->getHistory($userId);
                $this->view->reservations = $reservations;
                $this->render('admin/partnerview3');
                break;
            case 4:
                $offers = $this->vendors->getOffersBy($userId);
                $this->view->offers = $offers;
                $this->render('admin/partnerview4');
                break;
            case 5:
                $banks = $this->vendors->getBanksBy($userId);
                $this->view->banks = $banks;
                $this->render('admin/partnerview5');
                break;
            default:
                throw new Exception("Invalid panel type");
        }
    }
	
	public function saveAction(){
	$name=$_POST['name'];
	echo($name);die;
	
	}

    static function jsonEcho($jsonString) {
        header("content-type:text/json");
        echo $jsonString;
        exit;
    }
    
    private function _getTrip($var = 'id', $user = false, $obj = false)
    {
        $id   = $this->_getParam('id');
        $trip = $this->trips->getItn($id, $obj);
        if($user){
            if($trip->user_id != $this->user->getId()){
                throw new Exception();
        }}
        return $trip;
    }
    
    public function loaditineraryAction()
    {
        $trip = $this->_getTrip($obj = true);
        $this->view->trip = $trip;
        
        //var_dump($trip); die;
        
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
    
    public function acceptreservationAction()
    {
        if(!$this->getRequest()->isPost())
                throw new Exception('Wrong Request');
        
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());

        if($user->getRole() != 'provider')
                throw new Exception('No access allowed');

        $reservation  = $this->reservations->get($_POST['id']);
        if($reservation->vendor_id != $user->getVendorId())
                throw new Exception('Reservation not found');
        
        if($reservation->status_id != 1) 
                throw new Exception('Reservation not found');
        
        $lising = $this->listings->getListing($reservation->listing_id);
        
        $transaction = $this->reservations->getTransaction($reservation->transaction_id);
        $account = $this->users->getAccount($transaction->paywith);
        
        require "Stripe/Stripe.php";
        
        $keys = Zend_Registry::get('stripe');
        Stripe::setApiKey($keys['secret_key']);
        
        try {
            $charge = Stripe_Charge::create(array(
                'amount'      => $transaction->ammount * 100,
                'currency'    => 'usd',
                'customer'    => $account->stripe_id,
                'description' => $lising->title . ' Reservation'
            ));

            $transaction->status = 1;
            $transaction->charge_id = $charge->id;

            $transaction->save();

            $reservation->status_id = 2;
            $reservation->save();
            
            $notifier = new WS_Notifier($reservation->user_id);
            $notifier->reservationApproved($lising, $user->getData());
            
        } catch(Exception $e) {

            echo $e->getMessage();

        }
        
        
        echo 'success'; die;        
    }
    
    public function cancelreservationAction()
    {
        if(!$this->getRequest()->isPost())
                throw new Exception('Wrong Request');
        
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());

        if($user->getRole() != 'user')
                throw new Exception('No access allowed');

        $reservation  = $this->reservations->get($_POST['id']);
        if($reservation->user_id != $user->getId())
                throw new Exception('No access allowed');
        
        $listing = $this->listings->getListing($reservation->listing_id);
        
        $vendor = $this->users->getVendor($reservation->vendor_id);
        
        $transaction = $this->reservations->getTransaction($reservation->transaction_id);
        
        $transaction->delete();
        $reservation->delete();
        
        $notifier = new WS_Notifier($vendor->user_id);
        $notifier->reservationCancelled($listing, $user->getData());
        
        echo 'success'; die;
    }
    
    public function newssignupAction()
    {
        if($this->getRequest()->isPost()){
            $emails = new Zend_Db_Table(array(
                'name' => 'emails',
                'db'   => 'newsletter_db'
            ));
            
            $email = $emails->fetchNew();
            $email->email = $_POST['email'];
            $email->date  = date('Y-m-d H:i:s');
            $email->save();
            
            echo 1; die;
        }
        throw new Exception();
    }
	
	public function tripitemAction(){
       
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());
        if($user->getRole() != 'admin')
                throw new Exception('No access allowed');
		
		$this->trips->deleteListing(@$_GET['item']);
        self::jsonEcho(json_encode(array('attempt' => 'success', 'error_code' => '0', 'description' => '', 'data' => '')));
		
	}
	
	public function ttitleAction(){
       
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());
        if($user->getRole() != 'admin')
                throw new Exception('No access allowed');
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$tripId = $_POST['trip'];
		$day = $_POST['day'];
		$title = addslashes($_POST['text']);
		$sql = "INSERT INTO trip_days (trip_id, day, title) VALUES ($tripId, $day, '$title') ON DUPLICATE KEY UPDATE title='$title'";
		$db->query($sql);

        self::jsonEcho(json_encode(array('attempt' => 'success', 'error_code' => '0', 'description' => '', 'data' => '')));
		
	}


	public function requestinfoAction(){
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());
        if($user->getRole() != 'admin')
                throw new Exception('No access allowed');
		
		// Listing ID is here inside  $_POST['listing_id']
		// Get Request info as an array using $_POST['info']
		
                $listing = $this->listings->getListing($_POST['listing_id']);
                $vendor = $this->users->getVendor($listing->vendor_id);

                $notifier = new WS_Notifier($vendor->user_id);
		$notifier->listingPendingInformation($listing, $_POST['info']);
                
		echo "success";
		die;
	}
	
	public function listingstatusAction()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());
        if($user->getRole() != 'admin')
                throw new Exception('No access allowed');

        $id = $_POST['id'];
        $action = $_POST['act'];
        $listing = $this->listings->getListing($id);
        $listing->status = $action;
        $listing->save();
        
        $vendor = $this->users->getVendor($listing->vendor_id);
        
        if($action == 1) {
            $notifier = new WS_Notifier($vendor->user_id);
            $notifier->listingApproved($listing);
        }
        echo "success"; die;
    }

	public function usertatusAction()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());
        if($user->getRole() != 'admin')
                throw new Exception('No access allowed');

        $id = $_POST['id'];
        $action = $_POST['act'];
		$user = $this->users->get($id);
		if($user){
			$user->active = $action;
			$user->save();
                        
                        if($action == 1 and $user->role_id == 3) {
                            $notifier = new WS_Notifier($user->id);
                            $notifier->welcomePartner();
                        }
                        
			echo "success";
		}
		else{
			echo "fail";
		}
		die;
   }
    
    public function getrecomendationsAction()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $id = $this->_getParam('id', 'default');
        if($id == "default")
                throw new Exception('Listing not found');
        
        $listings = $this->listings->getAlternativesTo($id);
        
        $this->view->listings = $listings;
        $this->view->trip     = $this->_getParam('trip');
        $this->view->triplisting = $id;
    }
    
    public function getvendorsoptionsAction(){
        $vendors = new Zend_Db_Table('users');
        $select = $vendors->select();
        $select->where('city_id = ?', $this->_getParam('city'));
        $select->where('role_id = ?', 3);
        $select->order('name ASC');
        $results = $vendors->fetchAll($select);
        $this->view->results = $results;
    }
    
    public function getpartnerlistingsAction()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                throw new Exception('No access allowed');
        
        $user = new WS_User($auth->getIdentity());
        
        $status = $this->_getParam('get','all');
        $page   = $this->_getParam('page','1');
        $sort   = $this->_getParam('sort','created');
        $order  = $this->_getParam('order','');
        
        if($sort == 'created' and empty($order)) {
            $order = 'DESC';
        } elseif($sort == 'title' and empty($order)) {
            $order = 'ASC';
        }
        
        $this->view->selected = ($sort == 'created') ? (($order == 'DESC') ? 1 : 2) : (($order == 'ASC') ? 3 : 4);
        
        switch($status){
            case 'all':
                $listings = $this->listings->getListingsOf($user->getVendorId(), null, $page, $sort, $order);
                $this->view->listings = $listings;
                $this->view->title = "All Listings";
                $this->view->count = $this->listings->countListingsOf($user->getVendorId());
                break;
            case 'active':
                $listings = $this->listings->getListingsOf($user->getVendorId(), 1, $page, $sort, $order);
                $this->view->listings = $listings;
                $this->view->title = "Active Listings";
                $this->view->count = $this->listings->countListingsOf($user->getVendorId(), 1);
                break;
            case 'inactive':
                $listings = $this->listings->getListingsOf($user->getVendorId(), 0, $page, $sort, $order);
                $this->view->listings = $listings;
                $this->view->title = "Inactive Listings";
                $this->view->count = $this->listings->countListingsOf($user->getVendorId(), 0);
                break;
            default: throw new Exception('Page not found');
        }
    }
    
    public function restrictionsAction()
    {
        $id = $this->_getParam('id');
        $details = $this->listings->getDetails($id);
        
        $this->view->details = $details;
    }
    
    public function getadminplacesAction(){
        $places = $this->fqrequest('/venues/search', array(
            'll'         => $this->_getParam('center'),
            'intent'     =>'browse',
            'radius'     => 4000,
            'categoryId' => $this->_getParam('cat')
        ));
        
        $places = $places->response->venues;
        
        $fqplaces = new Zend_Db_Table('fq');
        foreach($places as $place) {
            $select = $fqplaces->select();
            $select->where('fq_id = ?', $place->id);
            $fq = $fqplaces->fetchRow($select);
            if(!is_null($fq)) {
                if($fq->added) {
                    $place->done = true;
                } else {
                    $place->done = false;
                }
            } else {
                $place->done = false;
            }
        }
        
        $this->view->places = $places;
        $this->view->city   = $this->_getParam('city');
    }
    
    private function fqrequest($url, $data) {
        $data['client_id'] = self::CLIENT_ID;
        $data['client_secret'] = self::CLIENT_SECRET;
        $data['v'] = '20120321';
        
        $url = 'https://api.foursquare.com/v2'.$url;
        
        $client = new Zend_Http_Client($url);
        foreach($data as $param => $value) {
            $client->setParameterGet($param, $value);
        }
        
        $response = $client->request();
        
        return json_decode($response->getBody());
    }
    
    public function saveplaceAction(){
        $result = array();
        try {
            
            $id = $_GET['id'];
            $city = $_GET['city'];
            $type = 2;

            $fqs = new Zend_Db_Table('fq');
            $select = $fqs->select();
            $select->where('fq_id = ?',$id);
            $fq = $fqs->fetchRow($select);
            if(is_null($fq)) {
                $client = new Zend_Http_Client('https://api.foursquare.com/v2/venues/'.$id);
                $client->setParameterGet('client_id', self::CLIENT_ID);
                $client->setParameterGet('client_secret', self::CLIENT_SECRET);
                $client->setParameterGet('v', date('Ymd'));

                $response = $client->request();
                $fq = $fqs->fetchNew();
                $fq->fq_id = $id;
                $fq->fq_data = $response->getBody();
                $fq->created = date('Y-m-d H:i:s');
                $fq->city    = $city;
                $fq->type    = $type;
                $fq->country = 18;

                $fq->save();
            }
            
            $fq->fb_page = $_GET['url'];

            $url = parse_url($this->_getParam('url'));
            $url = @split('/', $url['path']);
            $id = ($url[1] == 'pages') ? $url[3] : $url[1];

                            //echo $id; die;

            $client = new Zend_Http_Client('https://graph.facebook.com/'.$id);
            $client->setParameterGet('access_token', $_GET['token']);

            $response = $client->request();

            $body = json_decode($response->getBody());

            if(isset($body->error)) 
                throw new Exception($body->error->message);
            
            $client = new Zend_Http_Client('https://graph.facebook.com/'.$id.'/albums');
            $client->setParameterGet('access_token', $_GET['token']);

            $response = $client->request();

            $albums = json_decode($response->getBody());

            if(isset($albums->error)) 
                throw new Exception($albums->error->message);

            $photos = array();

            foreach($albums->data as $alb) {
                $client = new Zend_Http_Client('https://graph.facebook.com/'.$alb->id.'/photos');
                $client->setParameterGet('access_token', $_GET['token']);

                $response = $client->request();

                $pics = json_decode($response->getBody());

                if(!isset($pics->error)) {

                    foreach($pics->data as $pic) {
                        $_pic = new stdClass();
                        $_pic->thumb = $pic->picture;
                        $_pic->url = $pic->source;

                        $photos[] = $_pic;
                    }

                }
            }

            $body->photos = $photos;

            $fq->fb_data = json_encode($body);
            $fq->save();
            
            $__fq = json_decode($fq->fq_data);
            $venue = $__fq->response->venue;
            $page  = json_decode($fq->fb_data);
            
            //var_dump($fq->fq_data); die;
            
            $data = array(
                'country_id'=>18,
                'city_id'=>$fq->city,
                'main_type'=>$type,
                'title'=>$venue->name,
                'description' => (isset($page->about)) ? $page->about : '',
                'address'=>(isset($venue->location->address)) ? $venue->location->address : '',
                'lat'=>$venue->location->lat,
                'lng'=>$venue->location->lng,
                'phone'=>(isset($venue->contact->phone)) ? $venue->contact->phone : (isset($page->phone) ? $page->phone : ''),
                'email'=>(isset($venue->contact->email)) ? $venue->contact->email : (isset($page->email) ? $page->email : ''),
                'website'=>(isset($venue->contact->website)) ? $venue->contact->website : (isset($page->website) ? $page->website : ''),
                'sch'=>array(
                    0 => array(
                        'starting' => $page->hours->mon_1_open,
                        'ending'   => $page->hours->mon_1_close,
                        'name'     => 'Monday'
                    ),
                    1 => array(
                        'starting' => $page->hours->tue_1_open,
                        'ending'   => $page->hours->tue_1_close,
                        'name'     => 'Tuesday'
                    ),
                    2 => array(
                        'starting' => $page->hours->wed_1_open,
                        'ending'   => $page->hours->wed_1_close,
                        'name'     => 'Wednesday'
                    ),
                    3 => array(
                        'starting' => $page->hours->thu_1_open,
                        'ending'   => $page->hours->thu_1_close,
                        'name'     => 'Thursday'
                    ),
                    4 => array(
                        'starting' => $page->hours->fri_1_open,
                        'ending'   => $page->hours->fri_1_close,
                        'name'     => 'Friday'
                    ),
                    5 => array(
                        'starting' => $page->hours->sat_1_open,
                        'ending'   => $page->hours->sat_1_close,
                        'name'     => 'Saturday'
                    ),
                    6 => array(
                        'starting' => $page->hours->sun_1_open,
                        'ending'   => $page->hours->sun_1_close,
                        'name'     => 'Sunday'
                    ),
                ),
                'place' => array(
                    'facebook' => $page->link,
                    'twitter' => (isset($venue->contact->twitter)) ? $venue->contact->twitter : '',
                    'info' => (isset($page->description)) ? $page->description : '',
                    'specials',
                    'tips',
                    'tags' => (isset($venue->tags[0])) ? $venue->tags[0] : '',
                    'services'
                ),
                'fqid'
            );
            
            $data['cards'] = array();
            if(isset($page->payment_options->cash_only) and $page->payment_options->cash_only == 1)
                $data['cards'][] = 'cash';
            if(isset($page->payment_options->visa) and $page->payment_options->visa == 1)
                $data['cards'][] = 'visa';
            if(isset($page->payment_options->amex) and $page->payment_options->amex == 1)
                $data['cards'][] = 'amex';
            if(isset($page->payment_options->mastercard) and $page->payment_options->mastercard == 1)
                $data['cards'][] = 'mastercard';
            if(isset($page->payment_options->discover) and $page->payment_options->discover == 1)
                $data['cards'][] = 'discover';
            
            $services = '';
            $__services = array(
                'breakfast' => 'Breakfast',
                'lunch'     => 'Lunch',
                'dinner'    => 'Dinner',
                'coffee'    => 'Coffee Break',
                'drinks'    => 'Drinks',
            );
            $_services = get_object_vars($page->restaurant_specialties);
            foreach($_services as $k => $v)
                if($v == 1)
                    $services .= $__services[$k]."\n";

            $data['place']['specials'] = $services;
            
            $info = "";
            foreach($venue->tips->groups as $g)
                foreach($g->items as $t)
                    $info .= $t->text ."\n";
            
            $data['place']['tips'] = $info;
            
            $services = '';
            $__services = array(
                'reserve'  => 'Take reservations',
                'walkins'  => 'No reservation needed',
                'groups'   => 'Good for groups',
                'kids'     => 'Good for kids',
                'takeout'  => 'Able to take out',
                'delivery' => 'Express service',
                'catering' => 'Catering Service',
                'waiter'   => 'Waiter Service',
                'outdoor'  => 'Outdoor Tables'
            );
            $_services = get_object_vars($page->restaurant_services);
            foreach($_services as $k => $v)
                if($v == 1)
                    $services .= $__services[$k]."\n";
                
            $data['place']['services'] = $services;
            
            $listings = new Zend_Db_Table('listings');
            
            $listing  = $listings->fetchNew();
            
            $listing->country_id  = $data['country_id'];
            $listing->city_id     = $data['city_id'];
            $listing->vendor_id   = 0;
            $listing->main_type   = $data['main_type'];
            $listing->title       = $data['title'];
            $listing->description = $data['description'];
            $listing->identifier  = str_replace(' ','_',strtolower($data['title']));
            $listing->address     = $data['address'];
            $listing->lat         = $data['lat'];
            $listing->lng         = $data['lng'];
            $listing->status      = 0;
            $listing->phone       = $data['phone'];
            $listing->email       = $data['email'];
            $listing->website     = $data['website'];
            $listing->created     = date('Y-m-d H:i:s');
            $listing->updated     = date('Y-m-d H:i:s');
            $listing->save();
            $listing->token = md5($listing->id.time().rand(0,100000));
            $listing->save();
            
            $schedules = new Zend_Db_Table('listing_schedules');
            foreach($data['sch'] as $sch){
                if(!empty($sch['starting'])) {
                    $schedule = $schedules->fetchNew();
                    $schedule->listing_id = $listing->id;
                    $schedule->name = $sch['name'];
                    $schedule->starting = $sch['starting'];
                    $schedule->ending = $sch['ending'];
                    $schedule->save();
                }
            }
            
            $cards = implode(', ', $data['cards']);
            if(is_null($cards)) $cards = '';
            
            $places = new Zend_Db_Table('listing_place');
            $place  = $places->fetchNew();
            $place->setFromArray($data['place']);
            
            $place->cards = $cards;
            $place->created = date('Y-m-d H:i:s');
            $place->updated = date('Y-m-d H:i:s');
            $place->listing_id = $listing->id;
            $place->fqid = $fq->id;
            
            $place->save();
            
            $fq->added = 1;
            $fq->save();
            
            $result = array(
                'status' => 'success',
                'id'     => $listing->id
            );
            
        } catch(Exception $e) {
            $result = array(
                'status' => 'error',
                'error'  => $e->getMessage()
            );
        }
        
        header('Content-type: application/json');
        echo json_encode($result); die;
    }
}