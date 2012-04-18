<?php


class ProviderController extends Zend_Controller_Action
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
     * @var WS_AccountService
     */
    protected $accounts;
 
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            if($this->_getParam('action') != 'signup' && $this->_getParam('task') != 'preview' )
                $this->_redirect('/en-US/login?b='.$_SERVER['REQUEST_URI']);
            else {
                $this->messages     = new WS_MessagesService();
                $this->reservations = new WS_ReservationsService();
                $this->feeds        = new WS_FeedsService();
                $this->listings     = new WS_ListingService(false);
                $this->places       = new WS_PlacesService();
                $this->accounts     = new WS_AccountService();
            }
        } else {
            $this->user = new WS_User($auth->getStorage()->read());
            if($this->user->getRole() != 'provider'){
                throw new Exception();
            } else {
                $this->messages     = new WS_MessagesService();
                $this->reservations = new WS_ReservationsService();
                $this->feeds        = new WS_FeedsService();
                $this->listings     = new WS_ListingService(false);
                $this->places       = new WS_PlacesService();
                $this->accounts     = new WS_AccountService();
                
                
                $usersettings  = $this->user->getNotificationsSettings();
                if(count($usersettings) == 0)
                    $this->view->pendingSettings = true;
            }
            $this->view->user = $this->user->getData();
            $this->view->vendor = $this->user->getVendorData();
            $this->view->help   = $this->listings->getHelpSettings($this->user->getId());
        }
        $this->places = new WS_PlacesService();
    }
    
    public function listingsAction()
    {
        $template = 'listings';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->listingsDefaultTask();
                $this->render($template);
                break;
             case 'active':
                $this->listingsDefaultTask();
                $this->render($template);
                break;
             case 'inactive':
                $this->listingsDefaultTask();
                $this->render($template);
                break;
            case 'calendar':
                $this->listingsCalendarTask();
                $template = 'listings-calendar';
                $this->render($template);
                break;
            case 'details':
                $this->listingsDetailsTask();
                $template = 'listings-details';
                $this->render($template);
                break;
            case 'edit':
                $this->listingsEditTask();
                $template = 'listings-edit';
                $this->render($template);
                break;
            case 'faqs':
                $this->listingsFAQsTask();
                $template = 'listings-faqs';
                $this->render($template);
                break;
            case 'newtab':
                $this->listingsNewTabTask();
                $template = 'listings-newtab';
                $this->render($template);
                break;
            case 'overview':
                $this->listingsOverviewTask();
                $template = 'listings-overview';
                $this->render($template);
                break;
            case 'photos':
                $this->listingsPhotosTask();
                $template = 'listings-photos';
                $this->render($template);
                break;
            case 'pricing':
                $this->listingsPricingTask();
                $template = 'listings-pricing';
                $this->render($template);
                break;
            case 'tab':
                $this->listingsTabTask();
                $template = 'listings-tab';
                $this->render($template);
                break;
            case 'tips':
                $this->listingsTipsTask();
                $template = 'listings-tips';
                $this->render($template);
                break;
            case 'new':
                $this->listingsNewTask();
                break;
            case 'activate':
                $this->listingsActiveteTask();
                $template = 'listings-activate';
                $this->render($template);
                break;
            case 'desactivate':
                $this->listingsDesactivateTask();
                break;
            case 'preview':
                $this->listingPreviewTask();
                break;
            case 'location':
                $this->listingLocationTask();
                $template = 'listings-location';
                $this->render($template);
                break;
            case 'rooms':
                $this->listingRoomsTask();
                $template = 'listings-rooms';
                $this->render($template);
                break;
            case 'room':
                $this->listingRoomTask();
                $template = 'listings-room';
                $this->render($template);
                break;
            case 'specialities':
                $this->listingSpecialitiesTask();
                $template = 'listings-specialities';
                $this->render($template);
                break;
            case 'newroom':
                $this->listingNewroomTask();
                $template = 'listings-newroom';
                $this->render($template);
                break;
            case 'types':
                $this->listingTypesTask();
                $template = 'listings-types';
                $this->render($template);
                break;
            case 'type':
                $this->listingTypeTask();
                $template = 'listings-type';
                $this->render($template);
                break;
            case 'newtype':
                $this->listingNewtypeTask();
                $template = 'listings-newtype';
                $this->render($template);
                break;
            default:
                throw new Exception(); break;
        }
    }
    
    public function listingTypesTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing  = $this->listings->getListing($ids, $this->user->getVendorId());
            $types    = $this->listings->getActivityTypes($listing->id);
            if($this->getRequest()->isPost()){
                $type = $_POST['typeid'];
                $this->listings->deleteActivityType($type);
                setcookie('alert', 'Your changes have been saved');
                $this->_redirect('provider/listings/types/'.$listing->id);
            }
            
            $this->view->listing  = $listing;
            $this->view->types    = $types;
        }
    }
    
    public function listingTypeTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $type = $this->listings->getActivityType($ids);
            $listing = $this->listings->getListing($type->listing_id, $this->user->getVendorId());
            $tips = $this->listings->getActivityTypeTips($type->id);
            
            $data = array(
                'type' => $type,
                'tips' => $tips,
            );
            
            if($this->getRequest()->isPost())
            {
                $data = $_POST;
                $errors = array();
                if(empty($data['name']))
                    $errors[] = 'The name cannot be empty';
                if(empty($data['description']))
                    $errors[] = 'The description cannot be empty';
                
                if(count($errors) > 0)
                    $this->view->errors = $errors;
                else {
                    $type->name = $data['name']; 
                    $type->description = $data['description'];
                    $type->min = $data['min'];
                    $type->max = $data['max'];
                    $type->kids = $data['kids'];
                    $type->kid_age_max = $data['kid_age_max'];
                    $type->kid_age_min = 1;
                    $type->updated = date('Y-m-d H:i:s');
                    
                    $type->save();
                    
                    foreach($tips as $tip){
                        if(isset($data['tips'][$tip->id]) and !empty($data['tips'][$tip->id]['text'])){
                            $tip->text = $data['tips'][$tip->id]['text'];
                            $tip->save();
                        } else {
                            $tip->delete();
                        }
                    }
                    
                    $type_tips = new Zend_Db_Table('types_tips');
                    foreach($data['tip'] as $_tip){
                        if(!empty($_tip['text'])){
                            $tip = $type_tips->fetchNew();
                            $tip->text = $_tip['text'];
                            $tip->type_id = $type->id;
                            $tip->save();
                        }
                    }
                    setcookie('alert', 'Your changes have been saved');
                    $this->_redirect('provider/listings/type/'.$type->id);
                }
            }
            
            $this->view->listing = $listing;
            $this->view->data    = $data;
            
        }
    }
    
    public function listingNewtypeTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing  = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost()){
                $data = $_POST;
                
                $errors = array();
                if(empty($data['name']))
                    $errors[] = 'The name cannot be empty';
                if(empty($data['description']))
                    $errors[] = 'The description cannot be empty';
                
                if(count($errors) > 0)
                    $this->view->errors = $errors;
                else {
                    $activity_types = new Zend_Db_Table('activity_types');
                    $type = $activity_types->fetchNew();
                    $type->name = $data['name']; 
                    $type->description = $data['description'];
                    $type->min = $data['min'];
                    $type->max = $data['max'];
                    $type->kids = $data['kids'];
                    $type->kid_age_max = $data['kid_age_max'];
                    $type->kid_age_min = 1;
                    $type->listing_id = $listing->id;
                    $type->created = date('Y-m-d H:i:s');
                    $type->updated = date('Y-m-d H:i:s');
                    $type->save();
                    
                    $type_tips = new Zend_Db_Table('types_tips');
                    foreach($data['tip'] as $_tip){
                        if(!empty($_tip['text'])){
                            $tip = $type_tips->fetchNew();
                            $tip->text = $_tip['text'];
                            $tip->type_id = $type->id;
                            $tip->save();
                        }
                    }
                    setcookie('alert', 'Your changes have been saved');
                    $this->_redirect('provider/listings/types/'.$listing->id);
                }
            }
            
            $this->view->listing  = $listing;
        }
    }
    
    public function listingSpecialitiesTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing  = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost()){
                //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
                $errors = array();
                
                if(!empty($_POST['title']) || !empty($_POST['description'])){
                    if(empty($_POST['title']))
                        $errors[] = 'The Speciality title cannot be empty';
                    if(empty($_POST['description']))
                        $errors[] = 'The Speciality description cannot be empty';
                }
                
                if(count($errors) > 0)
                    $this->view->errors = $errors;
                else {
                    $this->listings->updateSpecialsOf($listing->id, $_POST);
                    
                    if(!empty($_POST['title']) && !empty($_POST['description'])){
                        $this->listings->addSpecialTo($listing->id, $_POST);
                    }
                    
                    $this->_redirect('/provider/listings/specialities/'.$listing->id);
                }
            }
            $specials = $this->listings->getSpecials($listing->id);
            
            $this->view->specials = $specials;
            $this->view->listing  = $listing;
        }
    }
    
    public function listingRoomsTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost())
            {
                //var_dump($_POST); die;
                $sch  = $this->listings->getSchedule($_POST['roomid']);
                $room = $this->listings->getRoom($sch->id);
                
                $db = Zend_Db_Table::getDefaultAdapter();
                $db->delete('beds', 'room_id = '.$room->id);
                $db->delete('room_amenities', 'room_id = '.$room->id);
                
                $sch->delete();
                $room->delete();
                
                setcookie('alert', 'Your changes have been saved');
                $this->_redirect('/provider/listings/rooms/'.$listing->id);
            }
            
            $rooms = $this->listings->getSchedulesOf($listing->id);
            
            $this->view->rooms   = $rooms;
            $this->view->listing = $listing;
        }
    }
    
    public function listingRoomTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $sch     = $this->listings->getSchedule($ids);
            $listing = $this->listings->getListing($sch->listing_id, $this->user->getVendorId());
            
            if($this->getRequest()->isPost()){
                //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
                
                $errors = array();
                if(empty($_POST['name'])) 
                    $errors[] = 'Room name cannot be empty';
                if(empty($_POST['room']['description']))
                    $errors[] = 'Room Description cannot be empty';
                
                if(count($_POST['amenities']) == 0)
                    $errors[] = 'Specify at least one ammenity';
                
                if(count($errors) > 0)
                    $this->view->errors = $errors;
                else {
                    $this->listings->updateRoom($listing->id, $_POST);
                    setcookie('alert', 'Your changes have been saved');
                    $this->_redirect('/provider/listings/room/'.$sch->id);
                }
                
            }
            
            $room = $this->listings->getRoom($sch->id);
            $beds = $this->listings->getBeds($room->id);
            $ammm = $this->listings->getAmenities($room->id);
            
            $amenities = $this->listings->getDefaultAmenities();
            
            $data = array(
                'sch'  => $sch,
                'room' => $room,
                'beds' => $beds,
                'ammm' => $ammm
            );
            
            $this->view->amenities = $amenities;
            $this->view->data = $data;
            $this->view->listing = $listing;
        }
    }
    
    public function listingNewroomTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            $amenities = $this->listings->getDefaultAmenities();
            
            if($this->getRequest()->isPost()){
                //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
                $errors = array();
                if(empty($_POST['name'])) 
                    $errors[] = 'Room name cannot be empty';
                if(empty($_POST['room']['description']))
                    $errors[] = 'Room Description cannot be empty';
                
                if(count($_POST['amenities']) == 0)
                    $errors[] = 'Specify at least one ammenity';
                
                if(count($errors) > 0)
                    $this->view->errors = $errors;
                else {
                    $this->listings->addScheduleTo($listing, $_POST);
                    setcookie('alert', 'Your changes have been saved');
                    $this->_redirect('/provider/listings/rooms/'.$listing->id);
                }
            }
            
            $this->view->amenities = $amenities;
            $this->view->listing   = $listing;
        }
    }
    
    public function listingsTipsTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            $tips    = $this->listings->getDetails($listing->id);
            
            if($this->getRequest()->isPost())
            {
                //die;
                
                foreach($tips as $detail){
                    if($detail->type == 4) {
                        if(isset($_POST['tip'][$detail->id])){
                            $detail->text = $_POST['tip'][$detail->id]['text'];
                            $detail->save();
                        } else $detail->delete();
                    }
                }
                
                $details_db = new Zend_Db_Table('listing_details');
                foreach($_POST['tips'] as $tip){
                    if(!empty($tip['text'])){
                        $row = $details_db->fetchNew();
                        $row->listing_id = $listing->id;
                        $row->text       = $tip['text'];
                        $row->type       = 4;
                        $row->created    = date('Y-m-d H:i:s');
                        $row->updated    = date('Y-m-d H:i:s');

                        $row->save();
                    }
                }
                
                $tips = $this->listings->getDetails($listing->id);
                setcookie('alert', 'Your changes have been saved');
                $this->_redirect('provider/listings/tips/'.$listing->id);
            }
            
            $this->view->tips    = $tips;
            $this->view->listing = $listing;
        }
    }
    
    public function listingLocationTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            $getthere = $this->listings->getLocationOf($listing->id);
            if($this->getRequest()->isPost()){
                if($_POST['_task'] === md5('add_location')){
                    $getthere->$_POST['location'] = ' ';
                    $getthere->save();
                    setcookie('alert', 'Your changes have been saved');
                    $this->_redirect('provider/listings/location/'.$listing->id);
                } else {
                    //echo '<pre>'; print_r($_POST); echo '</pre>'; die;
                
                    $getthere->plane = trim($_POST['plane']);
                    $getthere->car   = trim($_POST['car']);
                    $getthere->train = trim($_POST['train']);
                    $getthere->boat  = trim($_POST['boat']);
                    $getthere->bus   = trim($_POST['bus']);

                    $getthere->save();

                    $errors = array();
                    if(empty($_POST['country_id']))
                        $errors[] = 'Select the country';
                    if(empty($_POST['city_id']))
                        $errors[] = 'Select the city';
                    //if(empty($_POST['address']))
                        //$errors[] = 'Address can not be empty';
                    //if(empty($_POST['lat']) || empty($_POST['lng']))
                        //$errors[] = 'You need to localize the listing in the map';

                    if(count($errors) > 0){
                        $this->view->errors = $errors;
                    } else {
                        $data = $_POST;
                        if($data['country_id'] != $listing->country_id){
                            $db = Zend_Db_Table::getDefaultAdapter();

                            if($listing->country_id != 0 && !is_null($listing->country_id))
                                $db->query('Update places set listings = listings - 1 where id = '.$listing->country_id);

                            $db->query('Update places set listings = listings + 1 where id = '.$data['country_id']);
                            $listing->country_id = $data['country_id'];
                        }
                        if($data['city_id'] != $listing->city_id){
                            $db = Zend_Db_Table::getDefaultAdapter();

                            if($listing->city_id != 0 && !is_null($listing->city_id))
                                $db->query('Update places set listings = listings - 1 where id = '.$listing->city_id);

                            $db->query('Update places set listings = listings + 1 where id = '.$data['city_id']);
                            $listing->city_id    = $data['city_id']; 
                        }

                        $listing->address = $data['address'];
                        $listing->lat     = (!empty($data['lat'])) ? $data['lat'] : null;
                        $listing->lng     = (!empty($data['lng'])) ? $data['lng'] : null;

                        $listing->save();
                        
                        //var_dump($listing->toArray()); die;
                        
                        setcookie('alert', 'Your changes have been saved');
                        $this->_redirect('provider/listings/location/'.$listing->id);
                    }
                }                
            }
            
            $countries = $this->places->getPlaces(2);
            if($this->getRequest()->isPost()){
                if(!empty($_POST['country_id']))
                    $cities = $this->places->getPlaces (3, $_POST['country_id']);
            }
            else {
                if($listing->country_id)
                    $cities = $this->places->getPlaces (3, $listing->country_id);
            }
            
            $this->view->countries = $countries;
            $this->view->cities    = $cities;
            
            $this->view->listing = $listing;
            $this->view->location = $getthere;
        }
    }
    
    public function listingsOverviewTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            $overview = $this->listings->getOverviewOf($listing->id);
            $details = $this->listings->getDetails($listing->id);
            
            if($this->getRequest()->isPost()){
                //echo '<pre>'; print_r($_POST); echo '</pre>'; die;
                $overview->about   = $_POST['about'];
                $overview->expect  = $_POST['expect'];
                $overview->love    = $_POST['love'];
                $overview->updated = date('Y-m-d H:i:s');
                $overview->save();
                
                if(!empty($_POST['about'])){
                    if(strlen($_POST['about']) > 200){
                        $listing->description = substr(str_replace("\n","",$_POST['about']), 0, 197).'...';
                    } else {
                        $listing->description = $_POST['about'];
                    }
                    $listing->save();
                }
                
                foreach($details as $detail){
                    if($detail->type != 4) {
                        if(isset($_POST['detail'][$detail->id])){
                            $detail->text = $_POST['detail'][$detail->id]['text'];
                            $detail->save();
                        } else $detail->delete();
                    }
                }
                
                $details_db = new Zend_Db_Table('listing_details');
                foreach($_POST['details'] as $type => $ds){
                    foreach($ds as $d){
                        if(!empty($d['text'])) {
                            $row = $details_db->fetchNew();
                            $row->listing_id = $listing->id;
                            $row->text       = $d['text'];
                            $row->type       = $type;
                            $row->created    = date('Y-m-d H:i:s');
                            $row->updated    = date('Y-m-d H:i:s');

                            $row->save();
                        }
                    }
                }
                $details = $this->listings->getDetails($listing->id);
                setcookie('alert','Your changes have been saved');
                $this->_redirect('provider/listings/overview/'.$listing->id);
            }
            $this->view->overview = $overview;
            $this->view->listing = $listing;
            $this->view->details = $details;
        }
    }
    
    public function listingPreviewTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $this->vendors   = new WS_VendorService();
            $this->reviews   = new WS_ReviewsService();
            $this->questions = new WS_PublicQuestionsService();
            
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            $country = $this->places->getPlaceById($listing->country_id);
            if(!is_null($country))
                $city = $this->places->getPlaceById($listing->city_id);
            
            $template    = 'listing';

            $faqs        = $this->listings->getFAQsOf($listing->id);
            $vendor      = $this->vendors->getVendorById($listing->vendor_id);

            $attributes  = $this->listings->getAttributesOf($listing->id);

            $pictures    = $this->listings->getPictures($listing->id);

            switch($listing->main_type){
                case 6: 
                    $template = 'listing-activity';      
                    $options = $this->listings->getSchedulesOf($listing->id);
                    $this->view->options = $options;
                    
                    $overview = $this->listings->getOverviewOf2($listing->id);
					if($listing->departure)
	                    $departure_city = $this->places->getPlaceById($listing->departure);
                    if($listing->departure != $listing->return){
						if($listing->return)
	                        $return_city = $this->places->getPlaceById($listing->return);
                    }
                    
                    $details = $this->listings->getDetails($listing->id);
                    
                    $getthere = $this->listings->getLocationOf($listing->id);
                    
                    $this->view->overview       = $overview;
                    $this->view->departure_city = $departure_city;
                    $this->view->return_city    = $return_city;
                    $this->view->details        = $details;
                    $this->view->getthere       = $getthere;
                    break;
                case 7  : 
                    $template = 'listing-entertaiment';  
                    $overview = $this->listings->getOverviewOf2($listing->id);
                    $details = $this->listings->getDetails($listing->id);
                    
                    $this->view->overview = $overview;
                    $this->view->details  = $details;
                    break;
                case 5: 
                    $template = 'listing-hotel';         
                    $options = $this->listings->getSchedulesOf($listing->id);
                    $this->view->options = $options;
                    
                    $overview = $this->listings->getOverviewOf2($listing->id);
                    $details = $this->listings->getDetails($listing->id);
                    $getthere = $this->listings->getLocationOf($listing->id);
                    
                    $rooms = $this->listings->getHotelRooms($listing->id);
                    $rooms2 = $this->listings->getHotelRooms($listing->id, true);
                    $amenities = $this->listings->getDefaultAmenities(true);
                    $beds = array();
                    foreach($rooms as $room){
                        $beds[$room->id] = $this->listings->getBeds($room->id);
                    }
                    $room_amenities = array();
                    foreach($rooms as $room){
                        $room_amenities[$room->id] = $this->listings->getAmenities($room->id);
                    }
                    
                    $listing_amenities = $this->listings->getGenAmenities($listing->id);
                    $def_amenities     = $this->listings->getDefaultGenAmenities(true);
                    
                    $this->view->overview = $overview;
                    $this->view->details  = $details;
                    $this->view->getthere = $getthere;
                    $this->view->rooms    = $rooms;
                    $this->view->rooms2   = $rooms2;
                    $this->view->beds     = $beds;
                    $this->view->room_amenities = $room_amenities;
                    $this->view->amenities = $amenities;
                    
                    $this->view->def_amenities = $def_amenities;
                    $this->view->listing_amenities = $listing_amenities;
                    
                    
                    
                    break;
                case 2: 
                    $template = 'listing-restaurant';    
                    $overview = $this->listings->getOverviewOf2($listing->id);
                    
                    $this->view->overview = $overview;
                    break;
                case 4: 
                    $template = 'listing-touristsight'; 
                    $overview = $this->listings->getOverviewOf2($listing->id);
                    
                    $this->view->overview = $overview;
                    break;
                default: break;
            }

            $disabledDates = $this->listings->getDisabledDays($listing->id);
            if(count($disabledDates) > 0){
                $disabledDates = $disabledDates->toArray();
                foreach($disabledDates as $k => $date){
                    $disabledDates[$k] = date('n-j-Y', strtotime($date['day']));
                }
            }
            $this->view->disabledDates = $disabledDates;

            $reviews = $this->reviews->getReviewsFor($listing->id);
            if(count($reviews) > 0)
                $this->view->reviews = $reviews;

            if(!is_null($this->user)){
                $this->view->form_action = '/cart/add';
                $fields = array(
                    'userids'       => $this->user->getId(),
                    'userstoken'    => md5($this->user->getToken()),
                    'formids'       => md5($this->user->getToken().'bookit'),
                    '_task'         => md5('bookit'),
                    'listingids'    => $listing->id,
                    'listingstoken' => md5($listing->token),
                    'listingprice'  => $listing->price,
                    'cityid'        => $city->id,
                    'countryid'     => $country->id,
                );
                $this->view->form_fields = $fields;
            }
            else{
                $this->view->form_action = '/login';
                $fields = array(
                    'redirect[task]'          => md5('bookit'),
                    'redirect[listingids]'    => $listing->id,
                    'redirect[listingstoken]' => md5($listing->token),
                    'redirect[cityid]'        => $city->id,
                    'redirect[countryid]'     => $country->id,
                    'redirect[url]'           => '/cart/checkout',
                    'listingids'              => $listing->id,
                    'listingstoken'           => $listing->token,
                    'listingprice'            => $listing->price
                );
                $this->view->form_fields = $fields;
            }

            $prices = $this->listings->getBasicPrice($listing->id);
            if(!is_null($prices))
                $prices = $prices->toArray();

            if($listing->main_type == 5){
                $prices = $this->listings->getSchPrices($listing);
                if(!is_null($prices))
                    $prices = $prices[0];
            }

            if($this->user)
                $favorites = $this->user->getFavotites();


            //var_dump($listing); die;
            $this->view->region          = $region;
            $this->view->country         = $country;
            $this->view->city            = $city;
            $this->view->listing         = $listing;
            $this->view->faqs            = $faqs;
            $this->view->vendor          = $vendor;
            $this->view->attributes      = $attributes;
            $this->view->prices          = $prices;
            $this->view->pictures        = $pictures;
            $this->view->trips           = $trips;
            $this->view->favorites       = $favorites;

            if(!is_null($this->user))
                    $this->view->user = $this->user->getData();

            $this->render($template);
        }
    }
    
    public function listingsActiveteTask()
    {
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
        
        
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
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
                    
                    $schedules = $this->listings->getSchedulesOf($listing->id);
                    if(count($schedules) > 0)
                            $validate['options']['done'] = true;
                    
                    break;
                case 6:
                    $validate['price'] = array(
                        'url' => '/provider/listings/pricing/',
                        'label' => 'Listing Pricing',
                        'desription' => 'Add the listing pricing',
                        'done' => false,
                    );
                    $price = $this->listings->getBasicPrice($listing->id);
                    if(!is_null($price) and $price->price != 0)
                            $validate['price']['done'] = true;                    
                    
                    break;
                default: break;
            }
            
            $this->view->listing = $listing;
            $errors = array();
            foreach($validate as $val){
                if(!$val['done'])
                    $errors[] = 1;
            }
            
            if(count($errors) > 0){
                $this->view->validate = $validate;
            }
            else {
                $this->view->validate = $validate;
                $this->view->ready = true;
            }            
        }
    }
    
    public function listingsDesactivateTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            $listing->status = 0;
            $listing->save();
            
            $this->_redirect('provider/listings');
        }
    }
    
    public function listingsNewTask()
    {
        $template = 'listings-new';
        
        switch($this->_getParam('tab')){
            case 'default':
                $listings = $this->listings->countListings(null, $this->user->getVendorId());
                $this->view->title = ($listings == 0) ? 'Create your first listing' : 'Create a new listing';
                if($this->getRequest()->isPost()){
                    if($_POST['vendor'] != $this->user->getVendorId())
                            throw new Exception('vendor_id');
                    if($_POST['vendortoken'] != $this->user->getVendorToken())
                            throw new Exception('tokne');
                    $main_cat = $_POST['type'];
                    $main_cat = $this->listings->getCategory($main_cat);
                    if(is_null($main_cat))
                            throw new Exception('type');

                    $vendor = $this->user->getVendorData();
                    $user   = $this->user->getData();
                    
                    $listing = $this->listings->createNew();
                    $listing->title      = 'Untitled Listing';
                    $listing->country_id = $user->country_id;
                    if($listings > 0){
                        $dft_listing = $this->listings->getDefaultListing($this->user->getVendorId());
                        if(!is_null($dft_listing))
                            $listing->city_id = $dft_listing->city_id;
                    }
                    $listing->vendor_id  = $this->user->getVendorId();
                    $listing->main_type  = $main_cat->id;
                    $listing->created    = date('Y-m-d H:i:s');
                    $listing->updated    = date('Y-m-d H:i:s');
                    $listing->status     = 0;
                    $listing->phone      = $vendor->phone;
                    $listing->email      = $vendor->email;
                    $listing->website    = $vendor->website;
                    $listing->save();

                    $listing->token     = md5($listing->id.$this->user->getVendorToken());
                    $listing->save();
                    switch($listing->main_type){
                        case 2:
                            $listing->image = '/images2/ico-99.png';
                            break;
                        case 4:
                            $listing->image = '/images2/ico-101.png';
                            break;
                        case 5:
                            $listing->image    = '/images2/ico-98.png';
                            $listing->checkin  = '14:00:00';
                            $listing->checkout = '12:00:00';
                            break;
                        case 6:
                            $listing->image = '/images2/ico-97.png';
                            $listing->departure_country_id = $listing->country_id;
                            $listing->return_country_id = $listing->country_id;
                            $listing->departure = $listing->city_id;
                            $listing->return = $listing->city_id;
                            break;
                        case 7:
                            $listing->image = '/images2/ico-100.png';
                            break;
                    }
                    $listing->save();
                    $counter = $this->listings->countListings(null, $this->user->getVendorId());
                    if($counter > 1)
                        $this->_redirect('/provider/listings/edit/'.$listing->id); 
                    else
                        $this->_redirect('/provider/listings/new/'.$listing->id.'/step2');
                }
                break;
            case 'step2':
                $template = 'listings-new2';
                $ids = $this->_getParam('id','default');
                if($this->isValidId($ids)){
                    $listing = $this->listings->getListing($ids, $this->user->getVendorId());

                    if($this->getRequest()->isPost()){
                        $data = $_POST;
                        if($data['ids'] != $listing->id)
                            throw new Exception();
                        if($data['listing_token'] != $listing->token)
                            throw new Exception();
                        if($data['form_id'] != md5($listing->token.'edit-listing'))
                            throw new Exception();
                        
                        $errors = array();
                        if(empty($data['title']) || $data['title'] == 'Untitle Listing')
                            $errors[] = 'Title cannot be empty';
                        if(empty($data['country_id']))
                            $errors[] = 'Select the country';
                        if(empty($data['city_id']))
                            $errors[] = 'Select the city';
                        if(empty($data['address']))
                            $errors[] = 'Specify the location';
                        if(empty($data['lat']))
                            $errors[] = 'PLease localize the listing in the map';
                        if(count($data['types']) == 0)
                            $errors[] = 'Select some categories';
                        
                        if(count($errors) > 0){
                            $this->view->errors = $errors;
                        } else {
                            $listing->title = $data['title'];
                            $listing->identifier = str_replace(' ','-',strtolower($data['title']));
                            
                            //echo $listing->identifier; die;

                            if($data['country_id'] != $listing->country_id){
                                $db = Zend_Db_Table::getDefaultAdapter();
                                $db->query('Update places set listings = listings - 1 where id = '.$listing->country_id);
                                $db->query('Update places set listings = listings + 1 where id = '.$data['country_id']);
                                $listing->country_id = $data['country_id'];

                            }
                            if($data['city_id'] != $listing->city_id){
                                $db = Zend_Db_Table::getDefaultAdapter();
                                $db->query('Update places set listings = listings - 1 where id = '.$listing->city_id);
                                $db->query('Update places set listings = listings + 1 where id = '.$data['city_id']);
                                $listing->city_id    = $data['city_id'];
                            }

                            $this->listings->saveTypes($listing->id, $data['types']);
                            $this->listings->saveTags($listing->id, $data['tags']);

                            $listing->address = $data['address'];
                            $listing->lat = $data['lat'];
                            $listing->lng = $data['lng'];

                            $listing->save(); 
                            
                            if($listing->main_type == 4 or $listing->main_type == 7)
                                $this->_redirect('provider/listings/new/'.$listing->id.'/step5');
                            else
                                $this->_redirect('provider/listings/new/'.$listing->id.'/step4');
                        }
                    }

                    $main_cat   = $this->listings->getCategory($listing->main_type);
                    $categories = $this->listings->getSubCategoriesOf($main_cat->id);
                    $tabs       = $this->listings->getTabsOf($listing->id);
                    $attrs      = $this->listings->getAttributesOf($listing->id);
                    $tags       = $this->listings->getTagsFor($main_cat);
                    $countries  = $this->places->getPlaces(2);
                    if($listing->country_id != 0)
                        $cities     = $this->places->getPlaces(3, $listing->country_id);

                    $schedules  = $this->listings->getSchedulesOf($listing->id);

                    $_listing_types = $this->listings->getListingTypesOf($listing->id);
                    $l_types = array();
                    foreach($_listing_types as $lt)
                        $l_types[] = $lt->listingtype_id;

                    $_tags = $this->listings->getTagsOf($listing->id);
                    $l_tags = array();
                    foreach($_tags as $t)
                        $l_tags[] = $t->tag_id;

                    $this->view->listing        = $listing;
                    $this->view->main_category  = $main_cat;
                    $this->view->categories     = $categories;
                    $this->view->listing_types  = $l_types;
                    $this->view->tabs           = $tabs;
                    $this->view->attributes     = $attrs;
                    $this->view->tags           = $tags;
                    $this->view->listing_tags   = $l_tags;
                    $this->view->countries      = $countries;
                    $this->view->cities         = $cities;
                    $this->view->schedules      = $schedules;
                }
                break;
            case 'step4':
                $template = 'listings-new4';
                $ids = $this->_getParam('id','default');
                if($this->isValidId($ids)){
                    $listing = $this->listings->getListing($ids, $this->user->getVendorId());
                    if($this->getRequest()->isPost()){
                        //var_dump($_POST); die;
                        $data = $_POST;
                        if($data['ids'] != $listing->id)
                            throw new Exception();
                        if($data['listing_token'] != $listing->token)
                            throw new Exception();
                        if($data['form_id'] != md5($listing->token.'form_sch_new'))
                            throw new Exception();
                        
                        $errors = array();
                        if($listing->main_type != 5){
                            if($listing->main_type == 2){
                                foreach($data['sch'] as $sch){
                                    if(empty($sch['start']['hour']))
                                        $errors[] = $sch['name'] . ', Open hour cannot be empty';
                                    if(empty($sch['end']['hour']))
                                        $errors[] = $sch['name'] . ', Close hour cannot be empty';
                                }
                            } 
                        }
                        if(!isset($data['schs'])) {
                            if(empty($data['sch'][0]['name']) || $data['sch'][0]['name'] == 'Morning, Afternoon....'){
                                if($listing->main_type == 5)
                                    $errors[] = 'You need to add at least one room';
                            }
                        }
                        
                        if(count($errors) > 0){
                            $this->view->errors = $errors;
                            $this->view->sch    = $data['sch'];
                        } else {
                            $schedules = $this->listings->getSchedulesOf($listing->id);
                            if(count($schedules) > 0){
                                foreach($schedules as $sch){
                                    if(isset($data['schs'][$sch->id])){
                                        $data2 = $data['schs'][$sch->id];
                                        if(isset($data2['same'])){
                                            $data2['duration'] = 1;
                                            $data2['duration_lb'] = 'days';
                                        }
                                        $sch->listing_id = $listing->id;
                                        $sch->name       = $data2['name'];
                                        if($listing->main_type == 6){
                                            $sch->duration = $data2['duration'];
                                            $sch->duration_label = $data2['duration_lb'];
                                        }
                                        if($listing->main_type != 5){
                                            $starting = $data2['start']['hour'].':00 '.$data['start']['time'];
                                            $ending   = $data2['end']['hour'].':00 '.$data['end']['time'];
                                            $starting = date('H:i:s',strtotime($starting));
                                            $ending   = date('H:i:s',strtotime($ending));
                                            $sch->starting   = $starting;
                                            $sch->ending     = $ending;
                                        }
                                        $sch->save();
                                    } else {
                                        $sch->delete();
                                    }
                                }
                            }
                            
                            foreach($data['sch'] as $sch){
                                if(!empty($sch['name'])){
                                    if(isset($sch['same'])){
                                        $sch['duration'] = 1;
                                        $sch['duration_lb'] = 'days';
                                        $this->listings->addScheduleTo2($listing, $sch);
                                    } else {
                                        $this->listings->addScheduleTo2($listing, $sch);
                                    }
                                }
                            }
                            
                            $this->_redirect('provider/listings/new/'.$listing->id.'/step5');
                        }
                    }
                    $schedules = $this->listings->getSchedulesOf($listing->id);
                    $this->view->schedules = $schedules;
                    $this->view->listing = $listing;
                }
                break;
            case 'step5':
                $template = 'listings-new5';
                $ids = $this->_getParam('id','default');
                if($this->isValidId($ids)){
                    $listing = $this->listings->getListing($ids, $this->user->getVendorId());
                    if($this->getRequest()->isPost()){
                        if($listing->main_type == 5 || $listing->main_type == 6)
                            $this->_redirect ('provider/listings/new/'.$listing->id.'/step6');
                        else {
                            $listings = $this->listings->countListings(null, $this->user->getVendorId());
                            if($listings == 1){
                                $this->view->congrats = true;
                            } else {
                                $this->_redirect ('provider/listings/edit/'.$listing->id);
                            }
                        }
                    }
                    $this->view->listing = $listing;
                }
                break;
            case 'step6':
                $template = 'listings-new6';
                $ids = $this->_getParam('id','default');
                if($this->isValidId($ids)){
                    $listing = $this->listings->getListing($ids, $this->user->getVendorId());
                    if($listing->main_type == 5){
                        $schedules = $this->listings->getSchedulesOf($listing->id);
                        //var_dump($schedules); die;
                        $this->view->schedules = $schedules;
                    }
                    if($this->getRequest()->isPost()){
                        $data = $_POST;
                        if($data['ids'] != $listing->id)
                            throw new Exception('Listing not found');
                        if($data['listing_token'] != $listing->token)
                            throw new Exception('Listing Token Violated');
                        if($data['form_id'] != md5($listing->token .'form_pricing'))
                            throw new Exception('Form id violated');

                        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
                        $errors = array();
                        if($listing->main_type == 5){
                            foreach($schedules as $sch){
                                if($data['sch'][0][$sch->id]['price'] == '0.00' 
                                        || empty($data['sch'][0][$sch->id]['price']))
                                    $errors[] = $sch->name . ' Room: Rate per night cannot be 0';
                            }
                            foreach($data['sch'] as $i => $sch){
                                foreach($sch as $j => $room){
                                    $_room = $this->listings->getRoom($j);
                                    $data['sch'][$i][$j]['min'] = 1;
                                    $data['sch'][$i][$j]['max'] = $_room->people;
                                }
                            }
                        } else {
                            if($data['price'] == '0.00' || empty($data['price']))
                                $errors[] = 'Rate cannot be 0';
                        }
                        if(count($errors) > 0) {
                            $listings = 1;//$this->listings->countListings(null, $this->user->getVendorId());
                            if($listings == 1){
                                $this->view->congrats = true;
                            } else {
                                $this->_redirect ('/provider/listings/');
                            }
                        }
                        else {
							$data['additional'] = $data['price'];
							$data['additional_after'] = 1;
                            $this->listings->updatePricesOf($listing, $data);
                            $listings = $this->listings->countListings(null, $this->user->getVendorId());
                            if($listings == 1){
                                $this->view->congrats = true;
                            } else {
                                $this->_redirect ('/provider/listings/');
                            }
                        }
                    }
                    $this->view->listing = $listing;
                }
                break;
            default: throw new Exception('Page not found'); break;
        }

        $this->render($template);
    }
    
    public function listingsDefaultTask()
    {
        if($this->getRequest()->isPost())
        {
            switch($_POST['_task']){
                case md5('delete_listing'):
                    $listing = $this->listings->getListing($_POST['ids'], $this->user->getVendorId());
                    $listing->status = 3;
                    $listing->save();
                    
                    switch($this->_getParam('task')){
                        case 'default':
                            $this->_redirect('provider/listings/');
                            break;
                        case 'active':
                            $this->_redirect('provider/listings/active');
                            break;
                        case 'inactive':
                            $this->_redirect('provider/listings/inactive');
                            break;
                        default: throw new Exception('Page not found');
                    }
                    break;
            }
        }
    }
    
    public function listingsEditTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            
            if($this->getRequest()->isPost()){
                $this->editListingPostHandler($listing);
                $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            }
            
            $main_cat   = $this->listings->getCategory($listing->main_type);            
            $categories = $this->listings->getSubCategoriesOf($main_cat->id);
            $tabs       = $this->listings->getTabsOf($listing->id);
            $attrs      = $this->listings->getAttributesOf($listing->id);
            $tags       = $this->listings->getTagsFor($main_cat);
            $countries  = $this->places->getPlaces(2);
            
            if($this->getRequest()->isPost()){
                if(!empty($_POST['departure_country_id']))
                    $cities2 = $this->places->getPlaces (3, $_POST['departure_country_id']);
                if(!empty($_POST['return_country_id']))
                    $cities3 = $this->places->getPlaces (3, $_POST['return_country_id']);
            } else {
                if($listing->country_id != 0)
                    $cities  = $this->places->getPlaces(3, $listing->country_id);
                if($listing->departure_country_id != 0)
                    $cities2 = $this->places->getPlaces (3, $listing->departure_country_id);
                if($listing->return_country_id != 0)
                    $cities3 = $this->places->getPlaces (3, $listing->return_country_id);
            }
            
            $schedules  = $this->listings->getSchedulesOf($listing->id);
            if($listing->main_type == 2 and count($schedules) == 0)
                    $schedules = $this->listings->createSchedules($listing);
            
            $landscapes = $this->listings->getDefaultLandscapes();
            $listing_lands = $this->listings->getLandscapesOf($listing->id);
            $l_lands = array();
            foreach($listing_lands as $ls)
                $l_lands[] = $ls->landscape_id;
            
            
            $_listing_types = $this->listings->getListingTypesOf($listing->id);
            $l_types = array();
            foreach($_listing_types as $lt)
                $l_types[] = $lt->listingtype_id;
            
            $_tags = $this->listings->getTagsOf($listing->id);
            $l_tags = array();
            foreach($_tags as $t)
                $l_tags[] = $t->tag_id;
            
            $this->view->listing        = $listing;
            $this->view->main_category  = $main_cat;
            $this->view->categories     = $categories;
            $this->view->listing_types  = $l_types;
            $this->view->tabs           = $tabs;
            $this->view->attributes     = $attrs;
            $this->view->tags           = $tags;
            $this->view->listing_tags   = $l_tags;
            $this->view->countries      = $countries;
            $this->view->cities         = $cities;
            $this->view->cities2        = $cities2;
            $this->view->cities3        = $cities3;
            $this->view->schedules      = $schedules;
            $this->view->landscapes     = $landscapes;
            $this->view->l_lands        = $l_lands;
        }
    }
    
    private function editListingPostHandler($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;   
        switch($_POST['_task']){
            case md5('edit_tab'):
                $this->editListingUpdateTab($listing);
                break;
            case md5('add_tab'):
                $this->editListingAddTab($listing);
                break;
            case md5('edit_attr'):
                $this->editListingUpdateAttr($listing);
                break;
            case md5('add_attr'):
                $this->editListingAddAttr($listing);
                break;
            case md5('edit_sch'):
                $this->editListingUpdateSch($listing);
                break;
            case md5('add_sch'):
                $this->editListingAddSch($listing);
                break;
            case md5('edit_listing'):
                $this->saveListing($listing);
                break;
            case md5('delete_tab'):
                $this->editListingDeleteTab($listing);
                break;
            case md5('delete_attr'):
                $this->editListingDeleteAttr($listing);
                break;
            case md5('delete_sch'):
                $this->editListingDeleteSch($listing);
                break;
            default:
                throw new Exception();
        }
    }
           
    private function editListingAddAttr($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;   
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_attr_new'))
            throw new Exception();
        if(empty ($data['name']))
            throw new Exception();
        if(empty ($data['value']))
            throw new Exception();
        
        $this->listings->addAttrTo($listing->id, $data);        
    }
    
    private function editListingUpdateAttr($listing){
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;   
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_attr_'.$data['attr_id']))
            throw new Exception();
        $attr = $this->listings->getAttr($data['attr_id']);
        if(is_null($attr))
            throw new Exception();
        if(empty ($data['name']))
            throw new Exception();
        if(empty ($data['value']))
            throw new Exception();
        
        $attr->name   = $data['name'];
        $attr->value  = $data['value'];
        $attr->save();
    }
    
    private function editListingDeleteAttr($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_attr_delete_'.$data['attr_id']))
            throw new Exception();
        $attr = $this->listings->getAttr($data['attr_id']);
        if(is_null($attr))
            throw new Exception();
        
        $attr->delete();
    }
    
    private function editListingUpdateSch($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('id');
        if($data['listing_token'] != $listing->token)
            throw new Exception('listing_token');
        if($data['form_id'] != md5($listing->token.'form_sch_'.$data['sch_id']))
            throw new Exception('form_id');
        $sch = $this->listings->getSchedule($data['sch_id']);
        if(is_null($sch))
            throw new Exception('sch null');
        
        if(empty ($data['name']))
            throw new Exception();
        
        $sch->name = $data['name'];
        
        $starting = $data['start_hour'] .':'.$data['start_min'] .':00 '.$data['start_time'];
        $ending   = $data['end_hour']   .':'.$data['end_min']   .':00 '.$data['end_time'];
        
        $starting = date('H:i:s',strtotime($starting));
        $ending   = date('H:i:s',strtotime($ending));        
        
        $sch->starting   = $starting;
        $sch->ending     = $ending;
        
        $sch->save();
    }
    
    private function editListingDeleteSch($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_sch_delete_'.$data['sch_id']))
            throw new Exception();
        $sch = $this->listings->getSchedule($data['sch_id']);
        if(is_null($sch))
            throw new Exception();
        
        $sch->delete();
    }
    
    private function editListingAddSch($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_sch_new'))
            throw new Exception();
        if($listing->main_type != 5){
            if(empty ($data['start_hour']))
                throw new Exception();
            if(empty ($data['end_hour']))
                throw new Exception();
        }
        if(empty ($data['name']))
            throw new Exception();
        
        $this->listings->addScheduleTo($listing, $data);
    }
    
    private function saveListing($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'edit-listing'))
            throw new Exception();
        
        $errors = array();
        if($data['title'] == 'Untitle Listing' || empty($data['title']))
            $errors[] = 'Title cannot be empty';
        
        //if(empty($data['description']))
            //$errors[] = 'Description cannot be empty';
        
        if($listing->main_type == 6) {
            if(empty($data['departure']))
                $errors[] = 'Select a departure city';
            if(empty($data['departure_country_id']))
                $errors[] = 'Select a departure country';
            if(!isset($data['returnEqual']) or $data['returnEqual'] != 1){
                if(empty($data['return']))
                    $errors[] = 'Select a return city';

                if(empty($data['return_country_id']))
                    $errors[] = 'Select a return country';
            }
            if($data['schedules'] == 'custom') {
                if(count($data['schs']) == 0 && count($data['sch']) == 0)
                    $errors[] = 'Please add at least one schedule';
            }
        }
        
        if(count($data['types']) == 0)
            $errors[] = 'Select at least one category';
        
        if(empty($data['identifier']))
            $data['identifier'] = $data['title'];
        
        
        if(count($errors) > 0){
            $this->view->errors = $errors;
        } else {
            
            //echo '<pre>'; print_r($data); echo '</pre>'; die;
            
            $listing->title = $data['title'];
            
            /**
            if($data['country_id'] != $listing->country_id){
                $db = Zend_Db_Table::getDefaultAdapter();
                $db->query('Update places set listings = listings - 1 where id = '.$listing->country_id);
                $db->query('Update places set listings = listings + 1 where id = '.$data['country_id']);
                $listing->country_id = $data['country_id'];

            }
            if($data['city_id'] != $listing->city_id){
                $db = Zend_Db_Table::getDefaultAdapter();
                $db->query('Update places set listings = listings - 1 where id = '.$listing->city_id);
                $db->query('Update places set listings = listings + 1 where id = '.$data['city_id']);
                $listing->city_id    = $data['city_id'];
            }
             * 
             */

            $this->listings->saveTypes($listing->id, $data['types']);
            $this->listings->saveTags($listing->id, $data['tags']);
            $this->listings->saveLandscapes($listing->id, $data['lands']);

            /**
            $listing->address = $data['address'];
            $listing->lat = $data['lat'];
            $listing->lng = $data['lng'];
             * 
             */
            if($listing->main_type == 6) {
                $listing->departure = $data['departure'];
                $listing->departure_country_id = $data['departure_country_id'];
                
                $listing->min = (isset($data['min'])) ? $data['min'] : null;
                $listing->max = (isset($data['max'])) ? $data['max'] : null;
                
                $listing->kids = $data['kids'];
                $listing->kid_age_max = $data['kid_age_max'];
                $listing->kid_age_min = 1;

                if(!isset($data['returnEqual']) or $data['returnEqual'] != 1){
                    $listing->return = $data['return'];
                    $listing->return_country_id = $data['return_country_id'];
                } else {
                    $listing->return = $data['departure'];
                    $listing->return_country_id = $data['departure_country_id'];
                }
                
                $schedules = $this->listings->getSchedulesOf($listing->id);
                if($data['schedules'] == 'custom') {
                    foreach($schedules as $sch){
                        if(isset($data['schs'][$sch->id])){
                            $data2 = $data['schs'][$sch->id];
                            if(!empty($data2['name']))
                                $sch->name = $data2['name'];

                            $starting = $data2['start_hour'].':00 '.$data2['start_time'];
                            $ending   = $data2['end_hour'].':00 '.$data2['end_time'];

                            $starting = date('H:i:s',strtotime($starting));
                            $ending   = date('H:i:s',strtotime($ending));

                            $sch->starting   = $starting;
                            $sch->ending     = $ending;
                            if(isset($data2['duration']))
                                $sch->duration   = $data2['duration'];
                            else 
                                $sch->duration   = 1;
                            $sch->duration_label = 'days';

                            $sch->save();
                        } else 
                            $sch->delete();
                    }

                    foreach($data['sch'] as $data2){
                        if(!empty($data2['name']))
                            $this->listings->addScheduleTo($listing, $data2);
                    }
                } 
                else {
                    foreach($schedules as $sch) {
                        $sch->delete();
                    }
                }
            } elseif($listing->main_type == 2) {
                $schedules = $this->listings->getSchedulesOf($listing->id);
                foreach($schedules as $sch){
                    if(isset($data['schs'][$sch->id])){
                        $data2 = $data['schs'][$sch->id];
                        if(!empty($data2['name']))
                            $sch->name = $data2['name'];

                        $starting = $data2['start_hour'] .':'.$data2['start_min'] .':00 '.$data2['start_time'];
                        $ending   = $data2['end_hour']   .':'.$data2['end_min']   .':00 '.$data2['end_time'];

                        $starting = date('H:i:s',strtotime($starting));
                        $ending   = date('H:i:s',strtotime($ending));

                        $sch->starting   = $starting;
                        $sch->ending     = $ending;
                        //$sch->duration   = $data2['duration'];
                        //$sch->duration_label = $data2['duration_lb'];  

                        $sch->save(); 
                    } else {
                        //var_dump($data); die;
                        if(isset($data['close'][$sch->id])){
                            $sch->starting   = '00:00:00';
                            $sch->ending     = '00:00:00';
                            $sch->save(); 
                        } else {
                            $sch->delete();
                        }
                    }
                }
            }
            
            if(isset($data['contactEqual'])){
                $vendor = $this->user->getVendorData();
                $listing->phone   = $vendor->phone;
                $listing->email   = $vendor->email;
                $listing->website = $vendor->website;
            } else {
                $listing->phone   = $data['phone'];
                $listing->email   = $data['email'];
                $listing->website = $data['website'];
            }
            
            if($listing->main_type == 5){
                $listing->checkin = date('H:i:s', strtotime($data['checkin_hour'].' '.$data['checkin_time']));
                $listing->checkout = date('H:i:s', strtotime($data['checkout_hour'].' '.$data['checkout_time']));
            }

            $listing->identifier = str_replace(' ','_',strtolower($data['identifier']));

            $listing->save();
            
            setcookie('alert','Your changes have been saved');
            
            $this->_redirect('provider/listings/edit/'.$listing->id);
        }
    }
    
    private function isValidId($ids)
    {
        if($ids == 'default')
            $this->_redirect ('provider/listings');
        
        $ids = (int) $ids;
        if(!is_int($ids) or $ids <= 0)
            throw new Exception();
        
        return true;
    }
    
    public function listingsPhotosTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            $this->view->listing = $listing;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            if($this->getRequest()->isPost())
                $this->listingPhotosPostHandler($listing);
            
            $pictures = $this->listings->getPictures($listing->id);
            $this->view->main_category  = $main_cat;
            $this->view->pictures       = $pictures;
        }
    }
    
    private function listingPhotosPostHandler($listing)
    {
        switch($_POST['_task']){
            case md5('edit_picture'):
                $this->listingPhotosUpdatePic($listing);
                break;
            case md5('delete_picture'):
                $this->listingPhotosDeletePic($listing);
                break;
            default:
                throw new Exception();
        }
    }
    
    private function listingPhotosUpdatePic($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;        
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        
        foreach($data['pic'] as $pic_id => $pic){
            $img = $this->listings->getPhoto($listing->id, $pic_id);
            if(is_null($img))
                throw new Exception('Img not found');

            if($data['main'] == $img->id){
                $db = Zend_Db_Table::getDefaultAdapter();
                $db->query('Update listing_pictures set main = 0 where listing_id = '.$listing->id);
                $img->main = 1;
                $listing->image = $img->url;
                $listing->save();
            }
            
            $img->location = $pic['location'];
            $img->save();
        }
        
        $this->view->activephoto = $img->id;
        setcookie('alert','Your changes have been saved');
        $this->_redirect('provider/listings/photos/'.$listing->id);
    }
    
    private function listingPhotosDeletePic($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_picture_delete_'.$data['img_id']))
            throw new Exception('Form id violated');
        $img = $this->listings->getPhoto($listing->id, $data['img_id']);
        if(is_null($img))
            throw new Exception('Img not found');

        unlink(realpath(APPLICATION_PATH.'/../../'.$img->url));
        $img->delete();
        
        $this->_redirect('provider/listings/photos/'.$listing->id);
    }
    
    public function listingsDetailsTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            $details = $this->listings->getDetails($listing->id);
            if($this->getRequest()->isPost()){
                //die;
                
                foreach($details as $detail){
                    if($detail->type != 4) {
                        if(isset($_POST['detail'][$detail->id])){
                            $detail->text = $_POST['detail'][$detail->id]['text'];
                            $detail->save();
                        } else $detail->delete();
                    }
                }
                
                $details_db = new Zend_Db_Table('listing_details');
                foreach($_POST['details'] as $type => $ds){
                    foreach($ds as $d){
                        if(!empty($d['text'])) {
                            $row = $details_db->fetchNew();
                            $row->listing_id = $listing->id;
                            $row->text       = $d['text'];
                            $row->type       = $type;
                            $row->created    = date('Y-m-d H:i:s');
                            $row->updated    = date('Y-m-d H:i:s');

                            $row->save();
                        }
                    }
                }
                
                if($listing->main_type == 5) {
                    $listing_amenities = new Zend_Db_Table('listing_amenities');
                    $listing_amenities->delete("listing_id = {$listing->id}");
                    foreach($_POST['amm'] as $amm) {
                        $row = $listing_amenities->fetchNew();
                        $row->listing_id = $listing->id;
                        $row->amenitie_id = $amm;
                        $row->save();
                    }
                }
                
                $details = $this->listings->getDetails($listing->id);
                setcookie('alert', 'Your changes have been saved');
                $this->_redirect('provider/listings/details/'.$listing->id);
            }
            
            //$tabs    = $this->listings->getTabsOf($listing->id);
            
            if($listing->main_type == 5) 
            {
                $amenities = $this->listings->getDefaultGenAmenities();
                $list_amenities = $this->listings->getGenAmenities($listing->id);
                
                $this->view->amenities = $amenities;
                $this->view->list_amenities = $list_amenities;
            }
            
            $this->view->listing = $listing;
            $this->view->details = $details;
        }
    }
    
    public function listingsDetailsPostHandler($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_tab_new'))
            throw new Exception('Form id violated');
        
        switch($data['_task']){
            case md5('add_tab'):
                if(empty($data['title']))
                    throw new Exception('Title cannot be empty');
                $this->listings->addTabTo($listing->id, $data);
                $this->_redirect('provider/listings/tab/'.$listing->id.'/'.$data['title']);
                break;
            case md5('delete_tab'):
                $tab = $this->listings->getTab($data['tab_id']);                
                if(is_null($tab))
                    throw new Exception('asdasd');
                if($tab->listing_id != $listing->id)
                    throw new Exception('asd');
                $tab->delete();
                break;
            default: throw new Exception(); break;
        }
        
    }
    
    public function listingsFAQsTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost())
                $this->listingFAQsPostHandler($listing);
            
            $faqs = $this->listings->getFAQsOf($listing->id);
            $this->view->faqs    = $faqs;
            $this->view->listing = $listing;
        }
    }
    
    private function listingFAQsPostHandler($listing)
    {        
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_faq'))
            throw new Exception('Form id violated');
        
        $this->listings->updateFAQsOf($listing->id, $data);
        
        if(!empty($data['question']) && !empty($data['answer'])){
            $this->listings->addFAQsTo($listing->id, $data);
        }
        setcookie('alert', 'Your changes have been saved');
        $this->_redirect('/provider/listings/faqs/'.$listing->id);
        
    }
    
    public function listingsTabTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost())
                    $this->editListingUpdateTab($listing);
            
            $tab = $this->_getParam('tab');
            $tab = $this->listings->getTab(null, $tab, $listing->id);
            
            $this->view->tab = $tab;
            $this->view->listing = $listing;
            
        }
    }
    
    private function editListingUpdateTab($listing){
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_tab_'.$data['tab_id']))
            throw new Exception();
        $tab = $this->listings->getTab($data['tab_id']);
        if(is_null($tab))
            throw new Exception();
        if($data['tab_token'] != $tab->token)
            throw new Exception();
        if(empty ($data['text']))
            throw new Exception();
        
        $tab->text    = $data['text'];
        $tab->updated = date('Y-m-d H:i:s');
        $tab->save();
        
        $this->_redirect('provider/listings/details/'.$listing->id);
    }
    
    public function listingsCalendarTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost())
                $this->listingCalendarPostHandler($listing);
            
            
            $this->createCalendar($listing);
            
            $this->view->listing = $listing;
        }
    }
    
    private function createCalendar($listing)
    {
        $less     = array(0,1,2,3,4,5,6,0);
        $p_months = array(0,12,1,2,3,4,5,6,7,8,9,10,11);
        $n_months = array(0,2,3,4,5,6,7,8,9,10,11,12,1);
        $labels = array('','January','February','March','April','May','June','July','August','September','October','November','December');
        
        $year  = $this->_getParam('year', date('Y'));
        $month = $this->_getParam('month', date('n'));
        
        $start_at = (strtotime($year.'-'.$month.'-01')) - (86400 * $less[(date('N', (strtotime($year.'-'.$month.'-01'))))]);
        $today    = date('Y-m-d');
        
        $days = array();
        for($i=1;$i<43;$i++){
            
            $aux = $this->listings->getDayFromCalendar($listing->id,  date('Y-m-d', $start_at));
            
            if(date('Y-m-d', $start_at) == '2011-11-30'){
                //echo '<pre>'; var_dump($aux); echo '</pre>'; die;
            }
            
            if(is_null($aux))
                $aux1 = '';
            else
                $aux1 = $aux->state;
            
            $class = (date('n', $start_at) != $month) ? 'disabled' : $aux1;
            $class = ($start_at < strtotime($today)) ? 'disabled' : $class;
            $arr = array(
                'class' => $class,
                'num'   => date('j', $start_at),
                'date'  => date('Y-m-d', $start_at)
            );
            
                $schedules = $this->listings->getSchedulesOf($listing->id);
                
                foreach($schedules as $sch){
                    $aux2 = $this->listings->getDayFromCalendar($listing->id, date('Y-m-d', $start_at), $sch->id);
                    if(is_null($aux2))
                        $aux3 = '';
                    else 
                        $aux3 = $aux2->state;
                    
                    $arr[$sch->id]['class'] = $aux3;
                }
                //echo '<pre>'; var_dump($arr); echo '</pre>'; die;
            
            if(!in_array($arr, $days)){
                $days[] = $arr;
            }
            $start_at = $start_at + 86400;
            //echo $start_at.'<br>';
            //echo date('Y-m-d', $start_at).'<br>';
        }
       
        $schedules = $this->listings->getSchedulesOf($listing->id);
        $this->view->schedules = $schedules;

        
        $this->view->prevmonth    = $p_months[$month];
        $this->view->prevmonth_lb = $labels[$p_months[$month]];
        $this->view->nextmonth    = $n_months[$month];
        $this->view->nextmonth_lb = $labels[$n_months[$month]];
        $this->view->prevyear     = ($month == 1) ? $year - 1 : $year;
        $this->view->nextyear     = ($month == 12) ? $year + 1 : $year;
        $this->view->currentMonth = date('F Y', strtotime($year.'-'.$month.'-01'));
        $this->view->days         = $days;
    }
    
    private function listingCalendarPostHandler($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_calendar_'.$data['day']))
            throw new Exception('Form id violated');
        
        $this->listings->saveDayInCalendar($listing, $data);
        setcookie('alert', 'Your changes have been saved');
        $this->_redirect('/provider/listings/calendar/'.$listing->id);
    }
    
    public function listingsPricingTask()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost())
                $this->listingPricingPostHandler($listing);
            
            $schedules = $this->listings->getSchedulesOf($listing->id);
            $seasons   = $this->listings->getSeasonsOf($listing->id);
            $termtypes = array(
                'Flexible: Full refund 1 day prior to arrival, except fees',
                'Moderate: Full refund 5 days prior to arrival, except fees',
                'Strict: 50% refund up until 1 week prior to arrival, except fees'
            );
            
            if($listing->main_type != 5){
                if($listing->main_type == 6){
                    $types = $this->listings->getActivityTypes($listing->id);
                    if((is_null($listing->min) and is_null($listing->max)) and (count($types) > 0)){
                        $sch    = $this->listings->getSchPrices($listing);
                        $season = $this->listings->getSeasonPrices($listing);
                        //echo '<pre>'; var_dump($season); echo '</pre>'; die;
                    } else {
                        $basic_price = $this->listings->getBasePrice($listing);
                        $season      = $this->listings->getSeasonPrices($listing);                        
                    }
                } else {
                    $basic_price = $this->listings->getBasePrice($listing);
                    $season      = $this->listings->getSeasonPrices($listing);
                }
            } else {
                $season      = $this->listings->getSeasonPrices($listing);
                $sch         = $this->listings->getSchPrices($listing);
            }
            
            $persons = array('1','2','3','4','5','6','7','8','9','10','15','20','30','40','50');
            
            //var_dump($season); die;
            
            $this->view->listing        = $listing;
            $this->view->schedules      = $schedules;
            $this->view->seasons        = $seasons;
            $this->view->types          = $types;
            $this->view->termtypes      = $termtypes;
            $this->view->basic_price    = $basic_price;
            $this->view->season         = $season;
            $this->view->sch            = $sch;
            $this->view->persons        = $persons;
        }
    }
    
    private function listingPricingPostHandler($listing)
    {
        switch($_POST['_task']){
            case md5('update_pricing'):
                $this->listingPricingUpdate($listing);
                break;
            case md5('add_season'):
                $this->listingAddSeason($listing);
                break;
            case md5('edit_season'):
                $this->listingEditSeason($listing);
                break;
            case md5('delete_season'):
                $this->listingDeleteSeason($listing);
                break;
            default:
                throw new Exception();
        }
    }
    
    public function listingAddSeason($listing)
    {
        //var_dump($_POST); die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_pricing'))
            throw new Exception('Form id violated');
        
        $errors = array();
        if(empty($data['name']) || $data['name'] == 'Ej: Green Season')
            $errors[] = 'You forgot the season name';
        
        $starting = date('M d, Y', strtotime($data['starting']));
        $ending   = date('M d, Y', strtotime($data['ending']));
        
        if($starting != $data['starting'])
            $errors[] = 'We couldn\'t understand the Start Date Fornat, Please user the calendar';
        if($ending != $data['ending'])
            $errors[] = 'We couldn\'t understand the End Date Fornat, Please user the calendar';
        
        if(count($errors) > 0)
            $this->view->errors = $errors;
        else {
            $seasons = new Model_Seasons();
            $season = $seasons->fetchNew();
            $season->listing_id = $listing->id;
            $season->name = $data['name'];
            $season->starting = date('Y-m-d', strtotime($data['starting']));
            $season->ending = date('Y-m-d', strtotime($data['ending']));
            $season->save();
            
            setcookie('alert', 'Your changes have been saved');
            setcookie('season','active');
            $this->_redirect('provider/listings/pricing/'.$listing->id);
        }
    }
    
    private function listingEditSeason($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_pricing'))
            throw new Exception('Form id violated');
        
        $errors = array();
        if(empty($data['name']) || $data['name'] == 'Ej: Green Season')
            $errors[] = 'You forgot the season name';
        
        $starting = date('M d, Y', strtotime($data['starting']));
        $ending   = date('M d, Y', strtotime($data['ending']));
        
        if($starting != $data['starting'])
            $errors[] = 'We couldn\'t understand the Start Date Fornat, Please user the calendar';
        if($ending != $data['ending'])
            $errors[] = 'We couldn\'t understand the End Date Fornat, Please user the calendar';
        
        if(count($errors) > 0)
            $this->view->errors = $errors;
        else {
            $seasons = new Model_Seasons();
            $select  = $seasons->select();
            $select->where('id = ?', $data['season_id']);
            $select->where('listing_id = ?', $listing->id);
            
            $season = $seasons->fetchRow($select);
            if(is_null($season))
                    throw new Exception();
            
            $season->name = $data['name'];
            $season->starting = date('Y-m-d', strtotime($data['starting']));
            $season->ending = date('Y-m-d', strtotime($data['ending']));
            $season->save();
            
            setcookie('alert', 'Your changes have been saved');
            setcookie('season','active');
            $this->_redirect('provider/listings/pricing/'.$listing->id);
        }
    }
    
    private function listingDeleteSeason($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_pricing'))
            throw new Exception('Form id violated');
        
        $seasons = new Model_Seasons();
        $select  = $seasons->select();
        $select->where('id = ?', $data['season_id']);
        $select->where('listing_id = ?', $listing->id);

        $season = $seasons->fetchRow($select);
        if(is_null($season))
                throw new Exception();

        $season->delete();
        
        setcookie('alert', 'Your changes have been saved');
        setcookie('season','active');
        $this->_redirect('provider/listings/pricing/'.$listing->id);
    }
    
    private function listingPricingUpdate($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_pricing'))
            throw new Exception('Form id violated');
        
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        if($listing->main_type==6){
            $types = $this->listings->getActivityTypes($listing->id);
            if((is_null($listing->min) and is_null($listing->max)) and (count($types) > 0)){
                //echo '<pre>'; 
                foreach($data['sch'] as $sid => $types){
                    foreach($types as $tid => $price){
                        //var_dump($price); 
                        $data['sch'][$sid][$tid]['additional'] = $price['price'];
                        $data['sch'][$sid][$tid]['additional_after'] = 1;
                        if(!isset($price['kids']))
                            $data['sch'][$sid][$tid]['kids'] = $price['price'];
                    }
                }
                //echo '</pre>'; die;
            } else {
                $data['additional'] = $data['price'];
                $data['additional_after'] = 1;
                $data['kids'] = $data['price'];
                foreach($data['sch'] as $sid => $_price){
                    $data['sch'][$sid][0]['additional_after'] = 1;
                    $data['sch'][$sid][0]['additional'] = $data['sch'][$sid][0]['price'];
                }
            }
            //var_dump($data); die;
        }
        $this->listings->updatePricesOf($listing, $data);
        
        $listing->policy = $data['policy'];
        $listing->save();
        
        setcookie('alert', 'Your changes have been saved');
        if($data['s'] == 1){
            setcookie('season','active');
        }
        $this->_redirect('provider/listings/pricing/'.$listing->id);
    }
    
    
    
    
    public function messagesAction()
    {
        $template = 'messages';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->messagesDefaultTask();
                break;
            case 'starred':
                $this->messagesStarredTask();
                $template = 'messages-starred';
                break;
            case 'unreplied':
                $this->messagesUnrepliedTask();
                $template = 'messages-unreplied';
                break;
            default:
                throw new Exception(); break;
        }
        
        $this->render($template);
    }
    
    public function messagesDefaultTask()
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
    
    public function messagesStarredTask()
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
    
    public function messagesUnrepliedTask()
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
    
    
    
    
    public function accountAction()
    {
        $template = 'account';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->accountDefaultTask();
                break;
            case 'password':
                $this->accountPasswordTask();
                $template = 'account-password';
                break;
            case 'payments':
                $template = $this->accountPaymentsTask();
                break;
            case 'notifications':
                $this->accountNotificationsTask();
                $template = 'account-notifications';
                break;
            default:
                throw new Exception(); break;
        }
        
        $this->render($template);
    }
    
    public function accountDefaultTask()
    {
        if($this->getRequest()->isPost()){
            try {
                switch($_POST['_task']){
                    case md5('add_contact'):
                        //var_dump($_POST); die;
                        $data = $_POST;
                        $this->user->addContact($data);
                        setcookie('alert','Your changes have been saved');
                        $this->_redirect('/provider/account');
                        break;
                    case md5('update_contact'):
                        $data = $_POST;
                        $id = $data['id'];
                        unset($data['id']);
                        $this->user->updateContact($id, $data);
                        setcookie('alert','Your changes have been saved');
                        $this->_redirect('/provider/account');
                        break;
                    default:
                        $this->accountDefaultPostHandler();
                        setcookie('alert','Your changes have been saved');
                        $this->_redirect('/provider/account');
                        break;
                }}
            catch(Exception $e){
                $this->view->errormessage = $e->getMessage();}
        }
        
        $this->view->user = $this->user->getData(true);
        $this->view->vendor = $this->user->getVendorData(true);
        $this->view->contacts = $this->user->getContacts();
        
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
    }
    
    public function accountDefaultPostHandler()
    {
        $this->validatePostData('provider_form  ');
        $data = $_POST;
        if(empty($data['name']))
            throw new Exception ('Name cannot be empty');
        if(empty($data['email']))
            throw new Exception ('Email cannot be empty');
        if(empty($data['place_id']))
            throw new Exception ('Country cannot be empty');   
        
        $email = new Zend_Validate_EmailAddress();
        if(!$email->isValid($data['email'])){
            $messages = $email->getMessages();
            foreach($messages as $msg)
                throw new Exception($msg);
        }
        
        $vendor = $this->user->getVendorData(true);
        if($vendor->email != $data['email'])
            if(!$this->accounts->validateEmail($data['email']))
                throw new Exception ('This email its being used for another user');
            
        //if(!ctype_alnum($data['username']))
        //    throw new Exception ('Username may contain just numbers and letters');
        
        //if($user->username != $data['username'])
        //    if(!$this->accounts->validateUsername($data['username']))
        //        throw new Exception ('This username its being user for another user');
        
        $this->user->updateContacts($data['contacts']);           
            
        $vendor->name = $data['name'];
        $vendor->email = $data['email'];
        $vendor->place_id = $data['place_id'];
        $vendor->phone  = $data['phone'];
        $vendor->address = $data['address'];
        $vendor->website = $data['website'];
        
        $user = $this->user->getData(true);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->lang  = $data['lang'];
        
        $vendor->image = $user->image;
        
        $user->save();
        $vendor->save();
    }
    
    private function validatePostData($fid)
    {
        $data = $_POST;
        if($data['ids'] != $this->user->getId())
            throw new Exception('ids From Corrupted');
        if($data['vids'] != $this->user->getVendorId())
            throw new Exception('vids From Corrupted');
        if($data['user_token'] != $this->user->getToken())
            throw new Exception('user_token From Corrupted');
    }
    
    public function accountPasswordTask()
    {
        if($this->getRequest()->isPost())
            try{
                $this->accountPasswordPostHandler();
                $this->view->successmessage = 'Your changes has been applied';}
            catch(Exception $e){
                $this->view->errormessage = $e->getMessage();}
                
        $user             = $this->user->getData(true);
        $questions        = $this->user->getDefaultQuestions();
        
        $this->view->user         = $user;
        $this->view->sq           = $questions;
    }
    
    public function accountPasswordPostHandler()
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
            $user->password_hint = substr(md5(md5($data['npassword'])), 3, 10).$data['npassword'].substr(md5(md5($data['npassword'])), 0, 7);
        }
        
        $user->save();
    }


    public function accountPaymentsTask()
    {
        switch($this->_getParam('id','default')) {
            case 'default':
                $user = $this->user->getData(true);
                
                if($this->getRequest()->isPost())
                {
                    $id = $_POST['account'];
                    $account = $this->user->getBankAccount($id);
                    $account->delete();
                    
                    setcookie('alert', 'The bank account has been deleted');
                    $this->_redirect('/provider/account/payments');
                }

                $accounts = $this->user->getBankAccounts();
                
                $this->view->banks = $this->_getBanks($user->country_id, true);

                $this->view->user         = $user;
                $this->view->accounts     = $accounts;
                
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
        
        $user = $this->user->getData(true);
        
        $this->view->user  = $user;
        $this->view->banks = $this->_getBanks($user->country_id);
        
        return 'account-newpayment';
    }
    
    public function addPaymentsAccountPostHandler()
    {   
        $bankaccounts = new Zend_Db_Table('bankaccounts');
        $account = $bankaccounts->fetchNew();
        $account->bank_id   = $_POST['bank_id'];
        $account->legalid   = $_POST['legalid'];
        $account->holder    = $_POST['holder'];
        $account->number    = $_POST['number'];
        $account->vendor_id = $this->user->getId();
        $account->created   = date('Y-m-d H:i:s');
        $account->updated   = date('Y-m-d H:i:s');
        
        $account->save();
        
        setcookie('alert', 'The new bank account has been added');
        $this->_redirect('/provider/account/payments');
    }
    
    public function editPaymentsAccountTask()
    {
        if($this->getRequest()->isPost())
                $this->editPaymentsAccountPostHandler();
        
        $user = $this->user->getData(true);
        
        $id = $this->_getParam('id');
        $account = $this->user->getBankAccount($id);
        
        $this->view->user    = $user;
        $this->view->account = $account;
        $this->view->banks   = $this->_getBanks($user->country_id);
        
        return 'account-editpayment';
    }
    
    public function editPaymentsAccountPostHandler()
    {
        $id = $this->_getParam('id');
        $account = $this->user->getBankAccount($id);
        $account->bank_id   = $_POST['bank_id'];
        $account->legalid   = $_POST['legalid'];
        $account->holder    = $_POST['holder'];
        $account->number    = $_POST['number'];
        $account->updated   = date('Y-m-d H:i:s');
        
        $account->save();
        
        setcookie('alert', 'The bank account has been updated');
        $this->_redirect('/provider/account/payments');
    }
    
    private function _getBanks($country, $assoc = false) 
    {
        if($assoc) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from('banks');
            $select->where('country_id = ?', $country);
            $select->order('name ASC');
            
            $banks = $db->fetchAssoc($select);
        } else {
            $accounts = new Zend_Db_Table('banks');
            $select = $accounts->select();
            $select->where('country_id = ?', $country);
            $select->order('name ASC');
            
            $banks = $accounts->fetchAll($select);
        }
        
        return $banks;
    }
    
    public function accountNotificationsTask()
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
        
        $this->view->pendingSettings = false;
        if(count($usersettings) == 0)
            $this->view->pendingSettings = true;
        
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
    
    
    public function questionsAction()
    {
        $template = 'questions';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->questionsDefaultTask();
                break;
            case 'view':
                $this->questionsViewTask();
                $template = 'questions-view';
                break;
            default:
                throw new Exception(); break;
        }
        
        $this->render($template);
    }
    
    public function questionsDefaultTask()
    {
        $listings = $this->listings->getListingsOf($this->user->getVendorId());
        $this->view->listings = $listings;
    }
    
    public function questionsViewTask()
    {
        
    }
    
    
    
    
    public function reviewsAction()
    {
        $template = 'reviews';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->reviewsDefaultTask();
                break;
            case 'view':
                $this->reviewsViewTask();
                $template = 'reviews-view';
                break;
            default:
                throw new Exception(); break;
        }
        
        $this->render($template);
    }
    
    public function reviewsDefaultTask()
    {
        $listings = $this->listings->getReviewedListings($this->user->getVendorId());
        $this->view->listings = $listings;
    }
    
    public function reviewsViewTask()
    {
        $id = $this->_getParam('id');
        if($this->isValidId($id)){
            $listing = $this->listings->getListing($id);
            $service = new WS_ReviewsService();
            $reviews = $service->getReviewsFor($listing->id);
            
            $this->view->reviews = $reviews;
            
            $service->markReviewsAsRead($listing->id);
        }
    }
    
    
    
    
    public function reservationsAction()
    {
        $template = 'reservations';
        
        switch($this->_getParam('task'))
        {
            case 'default':
                $this->reservationsDefaultTask();
                break;
            case 'pending':
                $this->reservationsPendingTask();
                $template = 'reservations-pending';
                break;
            case 'history':
                $this->reservationsHistoryTask();
                $template = 'reservations-history';
                break;
            case 'transactions': 
                $this->transactionsTask();
                $template = 'reservations-transactions';
                break;
            default:
                throw new Exception(); break;
        }
        
        $this->render($template);
    }
    
    public function reservationsDefaultTask()
    {
        $_reservs   = $this->reservations->getIncoming($this->user->getVendorId());
        $this->view->reservations = $_reservs;
    }
    
    public function reservationsPendingTask()
    {
        $_reservs   = $this->reservations->getPending($this->user->getVendorId());
        $this->view->reservations = $_reservs;
    }
    
    public function reservationsHistoryTask()
    {
        $_reservs   = $this->reservations->getHistory($this->user->getVendorId());
        $this->view->reservations = $_reservs;
    }
    
    public function transactionsTask()
    {
        
    }
    
    
    
    
    public function signupAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
                $this->_redirect ('/logout');
        
        if($this->getRequest()->isPost())
        {
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
                
            if(empty($_POST['password']))
                $errors[] = 'Password cannot be empty';
                
            if($_POST['password2'] != $_POST['password'])
                $errors[] = 'Password confirmation fallure';
                
            if(empty($_POST['country_id']))
                $errors[] = 'Select a country';
                

            $email = new Zend_Validate_EmailAddress();
            if(!$email->isValid(trim($_POST['email'])))
                $errors[] = 'Invalid Emil Address';
            
            if(!Zend_Uri::check(trim($_POST['website'])))
                $errors[] = 'Invalid Website try adding http://';
            
            if(count($errors) > 0)
                $this->view->errors = $errors;
            else {
                $this->accounts = new WS_AccountService();
                if($this->accounts->validateEmail(trim($_POST['email']))){
                    $this->accounts->signupVendor($_POST);
                    $this->_redirect('/provider/listings/new');
                }

                $this->_helper->flashMessenger('Seems like you already have an account.');
                $this->_redirect('/login');
            }
        }
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
    }
    
    public function withdrawAction()
    {
        
    }
    
    public function cancellationPoliciesAction()
    {
        $template = '';
        switch($this->_getParam('task')){
            case 'activities':
                $template = 'cancellation-policiesAct';
                break;
            case 'hotels':
                $template = 'cancellation-policiesHot';
                break;
            default:
                throw new Exception('Page not found');
                break;
        }
        $this->render($template);
    }
    
    public function offersAction()
    {
        switch($this->_getParam('task')){
            case 'default':
                $db = Zend_Db_Table::getDefaultAdapter();
                $select = $db->select();
                $select->from('itineraries',array('id','country_id','start','end','adults'));
                $select->join('itinerary_cities', 'itinerary_cities.itinerary_id = itineraries.id', array('city_id'));
                $select->join('users','itineraries.user_id = users.id', array('name','image'));
                $select->where('itinerary_cities.city_id = ?', '25');
                $result = $db->fetchAll($select, array(), 5);

                $this->view->offers = $result;

                //var_dump($result); die;
                $listings = $this->listings->getListingsOf2($this->user->getVendorId());
                $this->view->listings = $listings;
                break;
            case 'new':
                if($this->getRequest()->isPost())
                {    
                    if($_POST['_task'] == md5('create')){
                        $offers = new Zend_Db_Table('offers');
                        $offer  = $offers->fetchNew();
                        $offer->listing_id = $_POST['listing_id'];
                        $offer->room       = (!empty($_POST['room']) and $_POST['room'] != 'null') ? $_POST['room'] : null;
                        $offer->price      = $_POST['price'];
                        $offer->start      = date('Y-m-d', strtotime($_POST['start']));
                        $offer->end        = date('Y-m-d', strtotime($_POST['end']));
                        $offer->regular    = $_POST['regular'];
                        $offer->people     = $_POST['people'];
                        $offer->people_ammount = $_POST['people_ammount'];
                        $offer->created    = date('Y-m-d H:i:s');
                        
                        $offer->save();
                        
                        $this->_redirect('/en-US/provider/offers?success');
                    } else {
                        $ids = $_POST['listing'];
                        if($this->isValidId($ids))
                        {
                            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
                            if(is_null($listing))
                                $this->_redirect('/provider/offers');

                            $this->view->listing = $listing;

                            $price = $this->listings->getBasicPrice($listing->id);
                            $this->view->price = $price;

                            if($listing->main_type == 6)
                                $this->render('offers-newact');
                            else {
                                $rooms = $this->listings->getSchedulesOf($listing->id);
                                $this->view->rooms = $rooms;
                                $this->render('offers-newhot');
                            }
                        } 
                        else 
                            $this->_redirect('/provider/offers');
                    }
                }
                else 
                {
                    $this->_redirect('/provider/offers');
                }
                
                break;
            default: throw new Exception('Page Not Found');
        }
    }
}
