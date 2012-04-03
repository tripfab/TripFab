<?php

class AdminController extends Zend_Controller_Action {
    /**
     * 
     */
    const ITEMS_PER_PAGE = 20;
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
    /**
     *
     * @var WS_UsersService
     */
    protected $users;
    /**
     *
     * @var WS_TripService
     */
    protected $trips;
    /**
     *
     * @var WS_ReviewsService
     */
    protected $reviewes;
    /**
     *
     * @var WS_VendorService
     */
    protected $vendors;



    public function init() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->_getParam('action') != 'signup')
                $this->_redirect('login');
        } else {
            $this->user = new WS_User($auth->getStorage()->read());
            if ($this->user->getRole() != 'admin') {
                throw new Exception();
            } else {
                $this->messages = new WS_MessagesService();
                $this->reservations = new WS_ReservationsService();
                $this->feeds = new WS_FeedsService();
                $this->listings = new WS_ListingService(false);
                $this->places = new WS_PlacesService();
                $this->accounts = new WS_AccountService();
                $this->users = new WS_UsersService();
                $this->trips = new WS_TripsService();
                $this->reviewes = new WS_ReviewsService();
                $this->vendors = new WS_VendorService();


                $usersettings = $this->user->getNotificationsSettings();
                if (count($usersettings) == 0)
                    $this->view->pendingSettings = true;
            }
            $this->view->user = $this->user->getData();
            $this->view->help = $this->listings->getHelpSettings($this->user->getId());
            
            $nonEditTasks = array('all','activities','hotels','restaurants','entertainments','tourist');
            if($this->_getParam('action') == 'listings'){
                if(!in_array($this->_getParam('task'),$nonEditTasks)){
                    $this->view->vendor = $this->listings->getVendorOfListing($this->_getParam('page'));
                }
            }
        }
    }

    public function listingsAction() {
        $GLOBALS['menuContext'] = 1;

        $this->view->listingContext = $this->_getParam('task');
        $this->view->baseUrl = '/admin/listings/' . $this->_getParam('task') . '/1/default/default';
        switch ($this->_getParam('task')) {
            case 'all':
            case 'activities':
            case 'hotels':
            case 'restaurants':
            case 'entertainments':
            case 'tourist':
                $this->listingsTask();
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
            case 'delete':
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
                throw new Exception('Page not found');
        }
    }

    private function listingsTask() {
        $page = $this->_getParam('page', 1);
        $this->view->paramSort = (int) $this->_getParam('sort');
        $this->view->paramSequence = $this->_getParam('seq');
        $seq = $this->view->paramSequence == 'desc' ? ' desc' : '';
		$this->view->searchText = $this->_getParam('q');
        $listingType = 0;
        $listingFields = array('title', 'type_name', 'type_name', 'status', 'city_name', 'country_name');
        switch ($this->_getParam('task')) {
            case 'all':
                $this->view->title = "All Listings";
                break;
            case 'activities':
                $listingType = 6;
                $this->view->title = "Activities";
                break;
            case 'hotels':
                $listingType = 5;
                $this->view->title = "Hotels";
                break;
            case 'restaurants':
                $listingType = 2;
                $this->view->title = "Restaurants";
                break;
            case 'entertainments':
                $listingType = 7;
                $this->view->title = "Entertainments";
                break;
            case 'tourist':
                $listingType = 4;
                $this->view->title = "Tourist Sights";
                break;
            default:
                $this->view->title = "All Listings";
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();
        $select->from('listings', array('*'));
        $select->join('listing_types', 'listings.main_type = listing_types.id', array("type_name" => "name"));
        $select->join('places', 'listings.city_id = places.id', array("city_name" => "title"));
        $select->join(array('country' => 'places'), 'country.id = listings.country_id', array("country_name" => "title"));
        if ($listingType) {
            $select->where("listings.main_type=?", $listingType);
        }
        if ($this->view->searchText) {
            $select->where("listings.title like '{$this->view->searchText}%'");
        }
		
		if($this->_getParam('co')){
            $select->where("listings.country_id = " . $this->_getParam('co'));
		}
		if($this->_getParam('ct')){
            $select->where("listings.city_id = " . $this->_getParam('ct'));
		}
        $select->order(array_key_exists($this->view->paramSort, $listingFields) ? $listingFields[$this->view->paramSort] . "$seq" : $listingFields[0]);
        //$select->order('listings.loves DESC');
        $this->view->paramSort = $this->_getParam('sort');
        $this->view->paramSequence = $this->_getParam('seq');
        $this->view->paramQuery = $this->_getParam('q');
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
        $this->view->numCount = $paginator->getTotalItemCount();
        
        $this->view->main_categories = $this->listings->getMainCategories();
        $this->view->countries = $this->places->getPlaces(2);
    }
    
    public function listingTypesTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing  = $this->listings->getListing($ids);
            $types    = $this->listings->getActivityTypes($listing->id);
            if($this->getRequest()->isPost()){
                $type = $_POST['typeid'];
                $this->listings->deleteActivityType($type);
                setcookie('alert', 'Your changes have been saved');
                $this->_redirect('/admin/listings/types/'.$listing->id);
            }
            
            $this->view->listing  = $listing;
            $this->view->types    = $types;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingTypeTask()
    {
        $ids = $this->_getParam('page','default');
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
                    $this->_redirect('/admin/listings/type/'.$type->id);
                }
            }
            
            $this->view->listing = $listing;
            $this->view->data    = $data;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
            
        }
    }
    
    public function listingNewtypeTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing  = $this->listings->getListing($ids);
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
                    $this->_redirect('/admin/listings/types/'.$listing->id);
                }
            }
            
            $this->view->listing  = $listing;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingSpecialitiesTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing  = $this->listings->getListing($ids);
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
                    
                    $this->_redirect('/admin/listings/specialities/'.$listing->id);
                }
            }
            $specials = $this->listings->getSpecials($listing->id);
            
            $this->view->specials = $specials;
            $this->view->listing  = $listing;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingRoomsTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids);
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
                $this->_redirect('/admin/listings/rooms/'.$listing->id);
            }
            
            $rooms = $this->listings->getSchedulesOf($listing->id);
            
            $this->view->rooms   = $rooms;
            $this->view->listing = $listing;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingRoomTask()
    {
        $ids = $this->_getParam('page','default');
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
                    $this->_redirect('/admin/listings/room/'.$sch->id);
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
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingNewroomTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids);
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
                    $this->_redirect('/admin/listings/rooms/'.$listing->id);
                }
            }
            
            $this->view->amenities = $amenities;
            $this->view->listing   = $listing;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingsTipsTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids);
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
                $this->_redirect('/admin/listings/tips/'.$listing->id);
            }
            
            $this->view->tips    = $tips;
            $this->view->listing = $listing;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingLocationTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids);
            $getthere = $this->listings->getLocationOf($listing->id);
            if($this->getRequest()->isPost()){
                if($_POST['_task'] === md5('add_location')){
                    $getthere->$_POST['location'] = ' ';
                    $getthere->save();
                    setcookie('alert', 'Your changes have been saved');
                    $this->_redirect('/admin/listings/location/'.$listing->id);
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
                        $this->_redirect('/admin/listings/location/'.$listing->id);
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
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingsOverviewTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids);
            $overview = $this->listings->getOverviewOf($listing->id);
            
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
                setcookie('alert','Your changes have been saved');
                $this->_redirect('/admin/listings/overview/'.$listing->id);
            }
            $this->view->overview = $overview;
            $this->view->listing = $listing;
            $this->view->details = $details;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
        }
    }
    
    public function listingPreviewTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $this->vendors   = new WS_VendorService();
            $this->reviews   = new WS_ReviewsService();
            $this->questions = new WS_PublicQuestionsService();
            
            $listing = $this->listings->getListing($ids);
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
            
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
                'url' => '/admin/listings/edit/',
                'label' => 'Listing Title',
                'desription' => 'Change the listing title or Assign one',
                'done' => false,
            ),
            'description' => array(
                'url' => '/admin/listings/overview/',
                'label' => 'Listing Description',
                'desription' => 'Add a listing short description',
                'done' => false,
            ),
            'country' => array(
                'url' => '/admin/listings/location/',
                'label' => 'Listing Country',
                'desription' => 'Assign a country to the listing',
                'done' => false,
            ),
            'city' => array(
                'url' => '/admin/listings/location/',
                'label' => 'Listing City',
                'desription' => 'Assign a city to the listing',
                'done' => false,
            ),
            'location' => array(
                'url' => '/admin/listings/location/',
                'label' => 'Listing Map',
                'desription' => 'Localize your listing in the map',
                'done' => false,
            ),
            'photos' => array(
                'url' => '/admin/listings/photos/',
                'label' => 'Listing Photos',
                'desription' => 'Add photos to the listing',
                'done' => false,
            ),
            'overview' => array(
                'url' => '/admin/listings/overview/',
                'label' => 'Listing Overview',
                'desription' => 'Add the Overview Information',
                'done' => false,
            ),
        );
        
        
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids);
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
            
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
                        'url' => '/admin/listings/pricing/',
                        'label' => 'Listing Pricing',
                        'desription' => 'Add the listing pricing',
                        'done' => false,
                    );
                    $validate['options'] = array(
                        'url' => '/admin/listings/rooms/',
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
                        'url' => '/admin/listings/pricing/',
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
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){    
            $listing = $this->listings->getListing($ids);
            $listing->delete();
            
            $this->_redirect('/admin/listings');
        }
    }
    
    public function listingsNewTask()
    {
        $template = 'listings-new';
        
        switch($this->_getParam('page','default')){
            case '1':
                if($this->getRequest()->isPost()){
                    
                    $main_cat = $_POST['main_type'];
                    $main_cat = $this->listings->getCategory($main_cat);
                    if(is_null($main_cat))
                            throw new Exception('type');

                    $vendor = $this->users->getVendorByUserId($_POST['vendor_id']);
                    
                    $listing = $this->listings->createNew();
                    $listing->title      = 'Untitled Listing';
                    $listing->country_id = $_POST['country_id'];
                    $listing->city_id    = $_POST['city_id'];
                    $listing->vendor_id  = $vendor->id;
                    $listing->main_type  = $main_cat->id;
                    $listing->created    = date('Y-m-d H:i:s');
                    $listing->updated    = date('Y-m-d H:i:s');
                    $listing->status     = 0;
                    $listing->phone      = $vendor->phone;
                    $listing->email      = $vendor->email;
                    $listing->website    = $vendor->website;
                    $listing->save();

                    $listing->token     = md5($listing->id.time().rand(0,100000));
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
                    $this->_redirect('/admin/listings/edit/'.$listing->id);
                }
                break;
            case 'step2':
                $template = 'listings-new2';
                $ids = $this->_getParam('page','default');
                if($this->isValidId($ids)){
                    $listing = $this->listings->getListing($ids);

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
                                $this->_redirect('/admin/listings/new/'.$listing->id.'/step5');
                            else
                                $this->_redirect('/admin/listings/new/'.$listing->id.'/step4');
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
                $ids = $this->_getParam('page','default');
                if($this->isValidId($ids)){
                    $listing = $this->listings->getListing($ids);
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
                            
                            $this->_redirect('/admin/listings/new/'.$listing->id.'/step5');
                        }
                    }
                    $schedules = $this->listings->getSchedulesOf($listing->id);
                    $this->view->schedules = $schedules;
                    $this->view->listing = $listing;
            
                    $main_cat = $this->listings->getCategory($listing->main_type);
                    $this->view->main_category = $main_cat;
                }
                break;
            case 'step5':
                $template = 'listings-new5';
                $ids = $this->_getParam('page','default');
                if($this->isValidId($ids)){
                    $listing = $this->listings->getListing($ids);
                    if($this->getRequest()->isPost()){
                        if($listing->main_type == 5 || $listing->main_type == 6)
                            $this->_redirect ('/admin/listings/new/'.$listing->id.'/step6');
                        else {
                            $listings = $this->listings->countListings(null, $this->user->getVendorId());
                            if($listings == 1){
                                $this->view->congrats = true;
                            } else {
                                $this->_redirect ('/admin/listings/edit/'.$listing->id);
                            }
                        }
                    }
                    $this->view->listing = $listing;
            
                    $main_cat = $this->listings->getCategory($listing->main_type);
                    $this->view->main_category = $main_cat;
                }
                break;
            case 'step6':
                $template = 'listings-new6';
                $ids = $this->_getParam('page','default');
                if($this->isValidId($ids)){
                    $listing = $this->listings->getListing($ids);
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
                                $this->_redirect ('/admin/listings/');
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
                                $this->_redirect ('/admin/listings/');
                            }
                        }
                    }
                    $this->view->listing = $listing;
            
                    $main_cat = $this->listings->getCategory($listing->main_type);
                    $this->view->main_category = $main_cat;
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
                            $this->_redirect('/admin/listings');
                            break;
                        case 'active':
                            $this->_redirect('/admin/listings/active');
                            break;
                        case 'inactive':
                            $this->_redirect('/admin/listings/inactive');
                            break;
                        default: throw new Exception('Page not found');
                    }
                    break;
            }
        }
        
        switch($this->_getParam('task')){
            case 'default':
                $listings = $this->listings->getListingsOf($this->user->getVendorId());  
                $this->view->listings = $listings;
                $this->view->title = "All Listings";
                break;
            case 'active':
                $listings = $this->listings->getListingsOf($this->user->getVendorId(), 1);
                $this->view->listings = $listings;
                $this->view->title = "Active Listings";
                break;
            case 'inactive':
                $listings = $this->listings->getListingsOf($this->user->getVendorId(), 0);  
                $this->view->listings = $listings;
                $this->view->title = "Inactive Listings";
                break;
            default: throw new Exception('Page not found');
        }
    }
    
    public function listingsEditTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){
            
            $listing = $this->listings->getListing($ids);
            
            if($this->getRequest()->isPost()){
                $this->editListingPostHandler($listing);
                $listing = $this->listings->getListing($ids);
            }
            
            $main_cat   = $this->listings->getCategory($listing->main_type); 
            $categories = $this->listings->getSubCategoriesOf($main_cat->id);
            $tabs       = $this->listings->getTabsOf($listing->id);
            $attrs      = $this->listings->getAttributesOf($listing->id);
            $tags       = $this->listings->getTagsFor($main_cat);
            $countries  = $this->places->getPlaces(2);
            
            $cities2 = array();
            $cities3 = array();
            
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
            
            $this->_redirect('/admin/listings/edit/'.$listing->id);
        }
    }
    
    private function isValidId($ids)
    {
        if($ids == 'default')
            $this->_redirect ('/admin/listings');
        
        $ids = (int) $ids;
        if(!is_int($ids) or $ids <= 0)
            throw new Exception();
        
        return true;
    }
    
    public function listingsPhotosTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids);
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
        $this->_redirect('/admin/listings/photos/'.$listing->id);
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
        
        $this->_redirect('/admin/listings/photos/'.$listing->id);
    }
    
    public function listingsDetailsTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids);
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
                $this->_redirect('/admin/listings/details/'.$listing->id);
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
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
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
                $this->_redirect('/admin/listings/tab/'.$listing->id.'/'.$data['title']);
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
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids);
            if($this->getRequest()->isPost())
                $this->listingFAQsPostHandler($listing);
            
            $faqs = $this->listings->getFAQsOf($listing->id);
            $this->view->faqs    = $faqs;
            $this->view->listing = $listing;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
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
        $this->_redirect('/admin/listings/faqs/'.$listing->id);
        
    }
    
    public function listingsTabTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids);
            if($this->getRequest()->isPost())
                    $this->editListingUpdateTab($listing);
            
            $tab = $this->_getParam('tab');
            $tab = $this->listings->getTab(null, $tab, $listing->id);
            
            $this->view->tab = $tab;
            $this->view->listing = $listing;
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
            
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
        
        $this->_redirect('/admin/listings/details/'.$listing->id);
    }
    
    public function listingsCalendarTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids);
            if($this->getRequest()->isPost())
                $this->listingCalendarPostHandler($listing);
            
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
            
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
        $this->_redirect('/admin/listings/calendar/'.$listing->id);
    }
    
    public function listingsPricingTask()
    {
        $ids = $this->_getParam('page','default');
        if($this->isValidId($ids)){
            $listing = $this->listings->getListing($ids);
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
            
            $main_cat = $this->listings->getCategory($listing->main_type);
            $this->view->main_category = $main_cat;
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
            $this->_redirect('/admin/listings/pricing/'.$listing->id);
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
            $this->_redirect('/admin/listings/pricing/'.$listing->id);
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
        $this->_redirect('/admin/listings/pricing/'.$listing->id);
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
        
        setcookie('alert', 'Your changes have been saved');
        if($data['s'] == 1){
            setcookie('season','active');
        }
        $this->_redirect('/admin/listings/pricing/'.$listing->id);
    }

    public function placesAction() {
        $GLOBALS['menuContext'] = 3;
        $this->view->listingContext = $this->_getParam('task');
        $this->view->baseUrl = '/admin/places/' . $this->_getParam('task') . '/1/default/default';
        switch ($this->_getParam('task')) {
            case 'regions':
            case 'countries':
            case 'cities':
            case 'all':
                $this->placesTask();
                break;
            case 'add':
                $this->placeAddTask();
                break;
            case 'edit':
                $this->placeEditTask();
                break;
            default:
                throw new Exception('Page not found');
        }
    }

    private function placesTask() {
        $page = $this->_getParam('page', 1);
        $this->view->searchText = $this->_getParam('q');
        $this->view->paramSort = (int) $this->_getParam('sort');
        $this->view->paramSequence = $this->_getParam('seq');
        $seq = $this->view->paramSequence == 'desc' ? ' desc' : '';
        $this->view->paramQuery = $this->_getParam('q');
        $filterCountry = $this->_getParam('co');
        $filterCity = $this->_getParam('ct');

        $regionsFields = array('regionName', 'countryTotal', 'cityTotal', 'hotelsTotal', 'activityTotal', 'restaurantTotal', 'entertainmentTotal', 'touristTotal');
        $countryFields = array('countryName', 'cityTotal', 'partnerTotal', 'hotelsTotal', 'activityTotal', 'restaurantTotal', 'entertainmentTotal', 'touristTotal');
        $cityFields = array('cityName', 'countryName', 'hotelsTotal', 'activityTotal', 'restaurantTotal', 'entertainmentTotal', 'touristTotal');


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();

        switch ($this->_getParam('task')) {
            case 'regions':

                $this->view->title = "Regions";
                $template = 'regions';
                $select->from(array('region' => 'places'), array('id', 'regionName' => 'title'))
                        ->joinleft(array('country' => 'places'), 'country.parent_id = region.id', array('countryTotal' => 'COUNT( DISTINCT country.id)'))
                        ->joinleft(array('city' => 'places'), 'city.parent_id = country.id', array('cityTotal' => 'COUNT(DISTINCT city.id)'))
                        ->joinleft('listings', 'city.id=listings.city_id', array('activityTotal' => 'COUNT(IF(listings.main_type=6, 1, NULL))', 'entertainmentTotal' => 'COUNT(IF(listings.main_type=7, 1, NULL))', 'touristTotal' => 'COUNT(IF(listings.main_type=4, 1, NULL))', 'restaurantTotal' => 'COUNT(IF(listings.main_type=2, 1, NULL))', 'hotelsTotal' => 'COUNT(IF(listings.main_type=5, 1, NULL))'))
                        ->where('region.parent_id IS NULL and region.type_id=1')
                        ->group('region.id');
                if ($this->view->searchText) {
					$identifierCompatible = str_replace(' ', '_',$this->view->searchText);
                    $select->where("region.title like '{$this->view->searchText}%' or region.identifier like '{$identifierCompatible}%'");
                }
                $select->order(array_key_exists($this->view->paramSort, $regionsFields) ? $regionsFields[$this->view->paramSort] . "$seq" : $regionsFields[0]);
                break;
            case 'countries':
                $this->view->title = "Countries";
                $template = 'countries';
                $select->from(array('country' => 'places'), array('id', 'countryName' => 'title'))
                        ->join(array('region' => 'places'), 'country.parent_id = region.id', array('region_name' => 'title', 'region_id'=>'id'))
                        ->join(array('city' => 'places'), 'city.parent_id = country.id', array('cityTotal' => 'COUNT(city.id)'))
                        ->joinleft('listings', 'city.id=listings.city_id', array('activityTotal' => 'COUNT(IF(listings.main_type=6, 1, NULL))', 'entertainmentTotal' => 'COUNT(IF(listings.main_type=7, 1, NULL))', 'touristTotal' => 'COUNT(IF(listings.main_type=4, 1, NULL))', 'restaurantTotal' => 'COUNT(IF(listings.main_type=2, 1, NULL))', 'hotelsTotal' => 'COUNT(IF(listings.main_type=5, 1, NULL))'))
                        //->joinleft('vendors', 'country.id=vendors.place_id', array('partnerTotal' => 'COUNT(vendors.id)'))
						->where('country.type_id =2')
                        ->group('country.id');
                if ($this->view->searchText) {
					$identifierCompatible = str_replace(' ', '_',$this->view->searchText);
                    $select->where("country.title like '{$this->view->searchText}%' or country.identifier like '{$identifierCompatible}%'");
                }
                
				if ($filterCountry) {
                    $select->where("region.id = ? ", $filterCountry);
                }
                $select->order(array_key_exists($this->view->paramSort, $countryFields) ? $countryFields[$this->view->paramSort] . "$seq" : $countryFields[0]);

                break;
            case 'cities':
            case 'all':
                $this->view->title = "Cities";
                $template = 'cities';
                $select->from(array('city' => 'places'), array('id', 'cityName' => 'title'))
                        ->join(array('country' => 'places'), 'city.parent_id = country.id', array('countryName' => 'title', 'country_id'=>'id'))
                        ->joinleft('listings', 'city.id=listings.city_id', array('activityTotal' => 'COUNT(IF(listings.main_type=6, 1, NULL))', 'entertainmentTotal' => 'COUNT(IF(listings.main_type=7, 1, NULL))', 'touristTotal' => 'COUNT(IF(listings.main_type=4, 1, NULL))', 'restaurantTotal' => 'COUNT(IF(listings.main_type=2, 1, NULL))', 'hotelsTotal' => 'COUNT(IF(listings.main_type=5, 1, NULL))'))
                        ->where('city.type_id =3')
                        ->group('city.id');
                if ($this->view->searchText) {
					$identifierCompatible = str_replace(' ', '_',$this->view->searchText);
                    $select->where("city.title like '{$this->view->searchText}%' or city.identifier like '{$identifierCompatible}%'");
                }

                if ($filterCountry) {
                    $select->where("country.id = ? ", $filterCountry);
                }
                $select->order(array_key_exists($this->view->paramSort, $cityFields) ? $cityFields[$this->view->paramSort] . "$seq" : $cityFields[0]);

                break;
            default:
                $this->view->title = "Cities";
        }


        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
        $this->view->numCount = $paginator->getTotalItemCount();
        $this->render($template);
    }

    private function placeAddTask() {
        $placeType = $this->_getParam('page');
        $place = $this->places->getPlaceNew();
        $place->lat = '';
        $place->lng = '';

        $this->view->successMessage = '';
        if (@$_SESSION['alert']) {
            $this->view->successMessage = "Your changes have been saved";
            $_SESSION['alert'] = '';
        }

        switch ($placeType) {
            case 'region':
                $place->type_id = 1;
                $this->view->titleMessage = "Create Region";
                $this->regionEditTask($place);
                break;
            case 'country':
                $place->type_id = 2;
                $this->view->titleMessage = "Create Country";
                $this->countryEditTask($place);
                break;
            case 'city':
                $place->type_id = 3;
                $this->view->titleMessage = "Create City";
                $this->cityEditTask($place);
                break;
            default: throw new Exception("Invalid place type");
        }
    }

    private function placeEditTask() {
        $placeId = (int) $this->_getParam('page');
        if (!$placeId) {
            throw new Exception("Listing not found");
        }

        $place = $this->places->getPlaceById($placeId);
        if (!$place) {
            throw new Exception("Listing not found");
        }

        $this->view->successMessage = '';
        if (@$_SESSION['alert']) {
            $this->view->successMessage = "Your changes have been saved";
            $_SESSION['alert'] = '';
        }

        $place->lat = $place->lat ? $place->lat : '';
        $place->lng = $place->lng ? $place->lng : '';


        switch ($place->type_id) {
            case 1: $this->regionEditTask($place);
                break;
            case 2: $this->countryEditTask($place);
                break;
            case 3: $this->cityEditTask($place);
                break;
            default: throw new Exception("Invalid place type");
        }
    }

    private function cityEditTask($place) {
        $this->view->place = $place;
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
        $this->view->landscapes = $this->places->getAllLandscapes();
        $place_lands = $this->places->getLandscapesOf($place->id);
        $p_lands = array();
        foreach ($place_lands as $ls) {
            $p_lands[] = $ls->landscape_id;
        }
        $this->view->p_lands = $p_lands;
        $this->view->errors = array();

        if ($this->getRequest()->isPost()) {
            $errors = $this->validateCityData($_POST);
            $lands = isset($_POST['landscapes']) ? $_POST['landscapes'] : array();
            if (count($errors)) {
                $this->view->errors = $errors;
                $this->view->place->title = $_POST['title'];
                $this->view->place->identifier = $_POST['identifier'];
                $this->view->place->description = $_POST['description'];
                $this->view->place->parent_id = $_POST['country'];
                $this->view->place->lat = $_POST['lat'];
                $this->view->place->lng = $_POST['lng'];
                $this->view->p_lands = $lands;
                $this->render('cityedit');
                return;
            }
            $place->title = $_POST['title'];
            $place->identifier = str_replace(' ', '_', strtolower($_POST['identifier']));
            $place->description = $_POST['description'];
            $place->parent_id = $_POST['country'];
            $place->lat = $_POST['lat'];
            $place->lng = $_POST['lng'];
            $place->save();
            $this->places->saveLandscapes($place->id, $lands);
            $_SESSION['alert'] = 'Your changes have been saved';
            $this->_redirect('admin/places/edit/' . $place->id);
            //put success message
        }
        $this->render('cityedit');
    }

    private function countryEditTask($place) {
        $this->view->place = $place;
        $regions = $this->places->getPlaces(1);
        $this->view->regions = $regions;
        $this->view->landscapes = $this->places->getAllLandscapes();
        $place_lands = $this->places->getLandscapesOf($place->id);
        $p_lands = array();
        foreach ($place_lands as $ls) {
            $p_lands[] = $ls->landscape_id;
        }
        $this->view->p_lands = $p_lands;
        $this->view->errors = array();

        if ($this->getRequest()->isPost()) {
            $errors = $this->validateCountryData($_POST);
            $lands = isset($_POST['landscapes']) ? $_POST['landscapes'] : array();
            if (count($errors)) {
                $this->view->errors = $errors;
                $this->view->place->title = $_POST['title'];
                $this->view->place->identifier = $_POST['identifier'];
                $this->view->place->description = $_POST['description'];
                $this->view->place->parent_id = $_POST['region'];
                $this->view->place->lat = $_POST['lat'];
                $this->view->place->lng = $_POST['lng'];
                $this->view->p_lands = $lands;
                $this->render('countryedit');
                return;
            }
            $place->title = $_POST['title'];
            $place->identifier = str_replace(' ', '_', strtolower($_POST['identifier']));
            $place->description = $_POST['description'];
            $place->parent_id = $_POST['region'];
            $place->lat = $_POST['lat'];
            $place->lng = $_POST['lng'];
            $place->save();
            $this->places->saveLandscapes($place->id, $lands);
            $_SESSION['alert'] = 'Your changes have been saved';
            $this->_redirect('admin/places/edit/' . $place->id);
            //put success message
        }
        $this->render('countryedit');
    }

    private function regionEditTask($place) {
        $this->view->place = $place;
        $this->view->landscapes = $this->places->getAllLandscapes();
        $place_lands = $this->places->getLandscapesOf($place->id);
        $p_lands = array();
        foreach ($place_lands as $ls) {
            $p_lands[] = $ls->landscape_id;
        }
        $this->view->p_lands = $p_lands;
        $this->view->errors = array();

        if ($this->getRequest()->isPost()) {
            $errors = $this->validateRegionData($_POST);
            $lands = isset($_POST['landscapes']) ? $_POST['landscapes'] : array();
            if (count($errors)) {
                $this->view->errors = $errors;
                $this->view->place->title = $_POST['title'];
                $this->view->place->identifier = $_POST['identifier'];
                $this->view->place->description = $_POST['description'];
                $this->view->p_lands = $lands;
                $this->render('regionedit');
                return;
            }
            $place->title = $_POST['title'];
            $place->identifier = str_replace(' ', '_', strtolower($_POST['identifier']));
            $place->description = $_POST['description'];
            $place->save();
            $this->places->saveLandscapes($place->id, $lands);
            $_SESSION['alert'] = 'Your changes have been saved';
            $this->_redirect('admin/places/edit/' . $place->id);
            //put success message
        }
        $this->render('regionedit');
    }

    private function validateCityData($postData) {
        $errors = array();
        if (empty($postData['country']))
            $errors['country'] = 'Select Country';

        if (empty($postData['title']))
            $errors['title'] = 'City Name can not be blank';

        if (empty($postData['description']))
            $errors['description'] = 'City Description can not be blank';

        if (isset($_POST['landscapes'])) {
            if (count($postData['landscapes']) == 0) {
                $errors['landscape'] = 'Select at least one landscape';
            }
        } else {
            $errors['landscape'] = 'Select at least one landscape';
        }

        return $errors;
    }

    private function validateCountryData($postData) {
        $errors = array();
        if (empty($postData['region']))
            $errors['region'] = 'Select Region';

        if (empty($postData['title']))
            $errors['title'] = 'Country Name can not be blank';

        if (empty($postData['description']))
            $errors['description'] = 'Country Description can not be blank';

        if (isset($_POST['landscapes'])) {
            if (count($postData['landscapes']) == 0) {
                $errors['landscape'] = 'Select at least one landscape';
            }
        } else {
            $errors['landscape'] = 'Select at least one landscape';
        }

        return $errors;
    }

    private function validateRegionData($postData) {
        $errors = array();
        if (empty($postData['title']))
            $errors['title'] = 'Region Name can not be blank';

        if (empty($postData['description']))
            $errors['description'] = 'Country Description can not be blank';

        if (isset($_POST['landscapes'])) {
            if (count($postData['landscapes']) == 0) {
                $errors['landscape'] = 'Select at least one landscape';
            }
        } else {
            $errors['landscape'] = 'Select at least one landscape';
        }

        return $errors;
    }

    public function usersAction() {
        $GLOBALS['menuContext'] = 4;
        $this->view->listingContext = $this->_getParam('task');
        $this->view->baseUrl = '/admin/users/' . $this->_getParam('task') . '/1/default/default';
        switch ($this->_getParam('task')) {
            case 'travellers':
            case 'partners':
            case 'all':
                $this->usersTask();
                break;
            case 'add':
                $this->userAddTask();
                break;
            case 'edit':
                $this->userEditTask();
                break;
            case 'view':
                $this->userViewTask();
                break;
            case 'data':
                $this->userDataTask();
                break;
            default:
                throw new Exception('Page not found');
        }
    }

    private function usersTask() {
        $page = $this->_getParam('page', 1);
        $this->view->searchText = $this->_getParam('q');
        $this->view->paramSort = (int) $this->_getParam('sort');
        $this->view->paramSequence = $this->_getParam('seq');
        $seq = $this->view->paramSequence == 'desc' ? ' desc' : '';
        $this->view->paramQuery = $this->_getParam('q');
        $this->view->countries = $this->places->getPlaces(2);

        $travellersFields = array('name', 'email', 'countryName', 'cityName', 'age', 'reservationTotal');
        $partnersFields = array('partnerName', 'partnerEmail', 'countryName', 'listingsCount', 'reservationTotal');


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();

        switch ($this->_getParam('task')) {
            case 'travellers':
            case 'all':
                $this->view->title = "Travellers";
                $template = 'travellers';
                $select->from(array('users'), array('id', 'name', 'email', 'if(birthdate=\'0000-00-00\', NULL, year(now()) - year(birthdate)) as age'))
                        ->joinleft(array('city' => 'places'), 'users.city_id=city.id', array('cityName' => 'title'))
                        ->joinleft(array('country' => 'places'), 'users.country_id=country.id', array('countryName' => 'title'))
                        ->joinleft('reservations', 'users.id=reservations.user_id', array('reservationTotal' => 'COUNT(reservations.user_id)'))
                        ->where('users.role_id = 2')
                        ->group('users.id');
                if ($this->view->searchText) {
                    $select->where("users.name like '{$this->view->searchText}%' or users.lname like '{$this->view->searchText}%' or users.email like '{$this->view->searchText}%'");
                }
                $select->order(array_key_exists($this->view->paramSort, $travellersFields) ? $travellersFields[$this->view->paramSort] . "$seq" : $travellersFields[0]);
                break; 	
            case 'partners':
                $this->view->title = "Partners";
                $template = 'partners';
                $select->from(array('users'), array('id', 'name', 'email', 'active'))
                        ->join('vendors', 'users.id=vendors.user_id', array('vendorId' => 'id', 'partnerName' => 'name', 'partnerEmail' => 'email', 'listingsCount' => 'listings', 'contact_name'))
                        ->joinleft(array('city' => 'places'), 'users.city_id=city.id', array('cityName' => 'title'))
                        ->joinleft(array('country' => 'places'), 'users.country_id=country.id', array('countryName' => 'title'))
                        ->joinleft('reservations', 'vendors.id=reservations.vendor_id', array('reservationTotal' => 'COUNT(reservations.id)'))
                        ->where('users.role_id = 3')
                        ->group('users.id');
                if ($this->view->searchText) {
                    $select->where("vendors.name like '{$this->view->searchText}%' or vendors.email like '{$this->view->searchText}%'");
                }
                $select->order(array_key_exists($this->view->paramSort, $partnersFields) ? $partnersFields[$this->view->paramSort] . "$seq" : $partnersFields[0]);

                break;
            default:
                $this->view->title = "Cities";
        }


        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
        $this->view->numCount = $paginator->getTotalItemCount();
        $this->render($template);
    }

    private function userViewTask() {
        $userType = $this->_getParam('page');
		
        $this->view->successMessage = '';
        if (@$_SESSION['alert']) {
            $this->view->successMessage = "Your changes have been saved";
            $_SESSION['alert'] = '';
        }
		
        switch ($userType) {
            case 'traveller':
                $userId = $this->_getParam('sort');
                $user = $this->users->getFull($userId);
                $this->view->user = $user;
                $this->render('travellerview');
                break;
            case 'partner':
                $vendorId = $this->_getParam('sort');
                $user = $this->vendors->getVendorDetailsById($vendorId);
                $this->view->user = $user;
				$this->partnerViewTask($user);
                break;
				
            default: throw new Exception("Invalid user type");
        }
    }
	
	private function partnerViewTask($user){
		$vendorId = $this->_getParam('sort');
		$page = $this->_getParam('seq')=='default' ? 1: $this->_getParam('seq') ;
		switch($page){
			case '1':
				
				if ($this->getRequest()->isPost()) {
					$name = $_POST['name'];
					$email = $_POST['email'];
					$contact_name = $_POST['contact_name'];
					$date = self::urlDateToMySql($_POST['created']);
					$phone = $_POST['phone'];
					$city = $_POST['city'];
					$country = $_POST['country'];
					$website = $_POST['website'];
					$user_id = $_POST['user_id'];
					$this->vendors->saveInfo($vendorId, $name, $email, $contact_name, $date, $phone, $city, $country, $website, $user->user_id);
					$_SESSION['alert'] = 'Your changes have been saved';
			   		$user = $this->vendors->getVendorDetailsById($vendorId);
                	$this->view->user = $user;
			   }
				
				$countries = $this->places->getPlaces(2);
				$this->view->countries = $countries;
				$this->render('partnerview1');
				break;
			case '2':
                $listings = $this->listings->getVendorListings($vendorId);
                $this->view->listings = $listings;
				$this->render('partnerview2');
				break;
			case '3':
                $reservations = $this->reservations->getHistory($vendorId);
                $this->view->reservations = $reservations;
				$this->render('partnerview3');
				break;
			case '4':
                $offers = $this->vendors->getOffersBy($vendorId);
                $this->view->offers = $offers;
				$this->render('partnerview4');
				break;
			case '5':
                $banks = $this->vendors->getBanksBy($vendorId);
                $this->view->banks = $banks;
				$this->render('partnerview5');
				break;
			case '5a':
				$bankId = $this->_getParam('q');
                $this->vendors->removeBankAccount($bankId);
				$this->_redirect("/admin/users/view/partner/$vendorId/5");
				break;
			case '6':
        		$this->view->banks = WS_BanksService::this()->getBanksBy();
				$bankId = $this->_getParam('q');
				$this->view->bankId = '';	
				$this->view->holderName = '';	
				$this->view->accountNumber = '';	
				$this->view->legalId = '';	
				
				if ($this->getRequest()->isPost()) {
					
					$data['bank_id'] = $_POST['bank'];	
					$data['legalid'] = $_POST['legal_id'];	
					$data['holder'] = $_POST['holder_name'];	
					$data['number'] = $_POST['ac_num'];	
					if(!$bankId){
						$data['vendor_id'] = $vendorId;	
					}
					$id = $this->vendors->saveBankAccount($bankId, $data);
					$_SESSION['alert'] = 'Your changes have been saved';
					$this->_redirect($this->view->url(array('seq'=>5)));
			   }

				if($bankId){
					$bank = $this->vendors->getVendorBankAccountById($bankId);
					$this->view->bankId = $bank->bank_id;	
					$this->view->holderName = $bank->holder;	
					$this->view->accountNumber = $bank->number;	
					$this->view->legalId = $bank->legalid;	
				}
				$this->render('partnerview6');
				break;
		}
	}

    private function userDataTask() {
        $userType = $this->_getParam('page');
        switch ($userType) {
            case 'traveller':
                $this->travellerDataTask();
                break;
            case 'partner':
                $this->partnerDataTask();
                break;
            default: throw new Exception("Invalid user type");
        }
    }


    public function reservationsAction() {
        $GLOBALS['menuContext'] = 6;
        $this->view->listingContext = $this->_getParam('task');
        $this->view->baseUrl = '/admin/reservations/' . $this->_getParam('task') . '/1/default/default';
        switch ($this->_getParam('task')) {
            case 'all':
            case 'hotels':
            case 'restaurants':
            case 'activities':
            case 'entertainments':
            case 'tourists':
                $this->reservationsTask();
                break;
            case 'add':
                $this->reservationAddTask();
                break;
            case 'edit':
                $this->reservationEditTask();
                break;
            case 'view':
                $this->reservationViewTask();
                break;
            case 'data':
                $this->reservationDataTask();
                break;
            default:
                throw new Exception('Page not found');
        }
    }

    private function reservationsTask() {
        $page = $this->_getParam('page', 1);
        $this->view->searchText = $this->_getParam('q');
        $this->view->paramSort = (int) $this->_getParam('sort');
        $this->view->paramSequence = $this->_getParam('seq');
        $seq = $this->view->paramSequence == 'desc' ? ' desc' : '';
        $this->view->paramQuery = $this->_getParam('q');

        $reservationFields = array('code', 'listing_name', 'vendor_name', 'user_name', 'checkin', 'checkout');


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();

        $select->from('reservations')
                ->join('listings', 'reservations.listing_id = listings.id', array('listing_name' => 'title', 'listing_image' => 'image'))
                ->join('listing_types', 'listings.main_type = listing_types.id', array('listing_type' => 'name'))
                ->join('users', 'users.id=reservations.user_id', array('user_name' => 'name'))
                ->join('vendors', 'vendors.id=reservations.vendor_id', array('vendor_name' => 'name'));
        if ($this->view->searchText) {
            $select->where("reservations.code like '{$this->view->searchText}' or listings.title like '{$this->view->searchText}%'");
        }
        $select->order(array_key_exists($this->view->paramSort, $reservationFields) ? $reservationFields[$this->view->paramSort] . "$seq" : $reservationFields[0]);
        switch ($this->_getParam('task')) {
            case 'all':
                $this->view->title = "";
                break;
            case 'hotels':
                $this->view->title = "Hotels";
                $select->where("listings.main_type = 5");
                break;
            case 'activities':
                $this->view->title = "Activities";
                $select->where("listings.main_type = 6");
                break;
            case 'restaurants':
                $this->view->title = "Restaurants";
                $select->where("listings.main_type = 2");
                break;
            case 'entertainments':
                $select->where("listings.main_type = 7");
                $this->view->title = "Entertainments";
                break;
            case 'tourists':
                $this->view->title = "Tourist Sights";
                $select->where("listings.main_type = 4");
                break;
            default:
                $this->view->title = "Cities";
        }


        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
        $this->view->numCount = $paginator->getTotalItemCount();
        $this->render('reservations');
    }

    private function reservationViewTask() {
        $reservationId = $this->_getParam('page');
        $reservation = $this->reservations->details($reservationId);
        $this->view->reservation = $reservation;
        $this->render('reservationview');
    }

    public function paymentsAction() {
        $GLOBALS['menuContext'] = 5;
        $this->view->listingContext = $this->_getParam('task');
        $this->view->baseUrl = '/admin/payments/' . $this->_getParam('task') . '/1/default/default';
        switch ($this->_getParam('task')) {
            case 'all':
            case 'pending':
            case 'history':
                $this->paymentsTask();
                break;
            case 'add':
                $this->paymentsAddTask();
                break;
            case 'edit':
                $this->paymentsEditTask();
                break;
            case 'view':
                $this->paymentsViewTask();
                break;
            case 'paid':
                $this->paidTask();
                break;
            case 'data':
                $this->paymentsDataTask();
                break;
            default:
                throw new Exception('Page not found');
        }
    }
	
	private function paymentsViewTask(){
        $vendorId = $this->_getParam('page');
		$date = $this->_getParam('sort');
		$reservations = $this->reservations->getPendingByDate($vendorId, $date);
		$this->view->reservations = $reservations;
		$this->view->total = count($reservations);
		$this->view->vendorName = $reservations[0]->vendor_name;
		
		$amount = 0;
		foreach($reservations as $reservation){
			$amount+=($reservation->ammount - $reservation->ammount* 0.075);
		}
		$this->view->amount = $amount;
		$this->render('paymentview');
		
	}

    private function paymentsTask() {
        $page = $this->_getParam('page', 1);
        $this->view->searchText = $this->_getParam('q');
        $this->view->paramSort = (int) $this->_getParam('sort');
        $this->view->paramSequence = $this->_getParam('seq');
        $seq = $this->view->paramSequence == 'desc' ? ' desc' : '';
        $this->view->paramQuery = $this->_getParam('q');
        $this->view->renderContext = 'history';

        $reservationFields = array('vendor_name', 'vendor_id', 'reservation_count', 'due_date', 'paid_total');


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();

		
		$select->from('reservations', array( 'paid_total'=>new Zend_Db_Expr('sum(ammount - (ammount* 0.075))'), 'reservation_count'=>new Zend_Db_Expr('count(1)'), 'due_date' => new Zend_Db_Expr('DATE_ADD(reservations.created, INTERVAL (9 - IF(DAYOFWEEK(reservations.created)=1, 8, DAYOFWEEK(reservations.created))) DAY)') ))
                ->join('vendors', 'vendors.id=reservations.vendor_id', array('vendor_name' => 'name', 'vendor_id'=>'id'))
				->group(array('vendors.id', 'due_date'));
		
		if ($this->view->searchText) {
            $select->where("vendors.name like '{$this->view->searchText}' or vendors.id like '{$this->view->searchText}%'");
        }
        $select->order(array_key_exists($this->view->paramSort, $reservationFields) ? $reservationFields[$this->view->paramSort] . "$seq" : $reservationFields[0]);
		switch ($this->_getParam('task')) {
            case 'all':
                $this->view->title = "";
                break;
            case 'pending':
                $this->view->renderContext = 'pending';
                $this->view->title = "Pending";
                $select->where("reservations.status_id=?", 1);
                break;
            case 'history':
                $this->view->title = "History";
                $select->where("reservations.status_id=?", 3);
                break;
            default:
                $this->view->title = "Pending";
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
        $this->view->numCount = $paginator->getTotalItemCount();
        $this->render('payments');
    }

    public function reportsAction() {
        $GLOBALS['menuContext'] = 7;
        $this->view->listingContext = $this->_getParam('task');
        $this->view->baseUrl = '/admin/reports/' . $this->_getParam('task') . '/1/default/default';
        switch ($this->_getParam('task')) {
            case 'all':
            case 'f1':
            case 'f2':
            case 'f3':
            case 'f4':
            case 's1':
            case 's2':
            case 's3':
            case 's4':
            case 's5':
                $this->reportsTask();
                break;
            case 'add':
                $this->reportAddTask();
                break;
            case 'edit':
                $this->reportEditTask();
                break;
            case 'view':
                $this->reportViewTask();
                break;
            case 'data':
                $this->reportDataTask();
                break;
            default:
                throw new Exception('Page not found');
        }
    }

    private function reportsTask() {
        require('html2pdf/lib.php');
        $page = $this->_getParam('page', 1);
        $this->view->searchText = $this->_getParam('q');
        $filter = $this->parseUrlParam($this->view->searchText);
        $this->view->paramSort = (int) $this->_getParam('sort');
        $this->view->paramSequence = $this->_getParam('seq');
        $seq = $this->view->paramSequence == 'desc' ? ' desc' : '';
        $this->view->paramQuery = $this->_getParam('q');

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();

        switch ($this->_getParam('task')) {
            case 'all':
            case 'f1':    //Partner Balance by Date
                $this->view->title = "";
                $template = 'reports1';
                $dataFields = array('date', 'time', 'id', 'user_id', 'partner_id', 'method', 'card_number', 'status', 'ammount');
                $titleFields = array('date' => 'Date', 'time' => 'Time', 'id' => 'Transaction ID', 'user_id' => 'Traveler ID', 'partner_id' => 'Partner ID', 'method' => 'Method', 'card_number' => 'Card Number', 'status' => 'Status', 'ammount' => 'Amount');
                $select->from('transactions')
                        ->join('reservations', 'transactions.id=reservations.transaction_id', array('user_id'));
                if (@$filter['s']) {
                    $mySqlDate = self::urlDateToMySql($filter['s']);
                    if ($mySqlDate) {
                        $select->where('transactions.date>=?', $filter['status']);
                    }
                }
                if (@$filter['e']) {
                    $mySqlDate = self::urlDateToMySql($filter['e']);
                    if ($mySqlDate) {
                        $select->where('transactions.date<=?', $filter['status']);
                    }
                }

                $select->order(array_key_exists($this->view->paramSort, $dataFields) ? $dataFields[$this->view->paramSort] . "$seq" : $dataFields[0]);
                if ($page == 'pdf') {
                    $pdfMaker = new PdfMaker();
                    $pdfMaker->makeByHtml($titleFields, $db->fetchAll($select));
                }
                break;
            case 'f2':   //	Transaction by Date
                $this->view->title = "Transaction by date";
                $this->render('reports2'); return;
                break;
            case 'f3':       // Reservations by Date
                $this->view->title = "Reservation by Date";
                $template = 'reports3';
                $dataFields = array('date', 'code', 'user_id', 'status', 'gross_amount', 'fee', 'net_amount', 'partner_id');
                $titleFields = array('date' => 'Date', 'code' => 'Reservation ID', 'user_id' => 'Traveler ID', 'status' => 'Status', 'status' => 'Status', 'ammount' => 'Gross Amount', 'fee' => 'Fee', 'net' => 'Net Amount', 'vendor_id' => "Partner ID");
                $select->from('transactions', array('*', 'fee' => new Zend_Db_Expr('transactions.ammount * 0.075'), 'net' => new Zend_Db_Expr('transactions.ammount*0.9')))
                        ->join('reservations', 'transactions.id=reservations.transaction_id', array('user_id'));
                if (@$filter['s']) {
                    $mySqlDate = self::urlDateToMySql($filter['s']);
                    if ($mySqlDate) {
                        $select->where('transactions.date>=?', $mySqlDate);
                    }
                }
                if (@$filter['e']) {
                    $mySqlDate = self::urlDateToMySql($filter['e']);
                    if ($mySqlDate) {
                        $select->where('transactions.date<=?', $mySqlDate);
                    }
                }
                if (@$filter['status'] === 1 || @$filter['status'] === 0) {
                    $select->where('transactions.status<=?', $filter['status']);
                }

                $select->order(array_key_exists($this->view->paramSort, $dataFields) ? $dataFields[$this->view->paramSort] . "$seq" : $dataFields[0]);
                if ($page == 'pdf') {
                    $pdfMaker = new PdfMaker();
                    $pdfMaker->makeByHtml($titleFields, $db->fetchAll($select));
                }
				
                if ($page == 'xls') {
					$headers = array(array(
									'Date',
									 'Reservation ID',
									 'Traveler ID', 
									 'Status',
									 'Gross Amount', 
									 'Fee',
									 'Net Amount', 
									 "Partner ID"
									)
								);
					
                                        
                                        $fnRow = create_function('$row','
						return array(
							$row->date,
							$row->code,
							$row->user_id,
							$row->status,
							$row->gross_amount,
							$row->fee,
							$row->net_amount,
							$row->partner_id,
						);');
					
					require_once("TF/Export.php");
					TF_Export::xlsFromData(
						"log-reports.xls",
						$headers,
						$fnRow,
						$db->fetchAll($select)
					);
				}
                if ($page == 'xls') {
					$headers = array(array(
							'Date', 
							'Session ID',
							'User',
							'Log in Time',
							'Log out Time',
							'Total Time'
						));
					$fnRow = create_function('$row','
						return array(
							$row->date,
							$row->session_id,
							$row->name,
							$row->login_time,
							$row->logout_time,
							$row->total
						);');
					
					require_once("TF/Export.php");
					TF_Export::xlsFromData(
						"log-reports.xls",
						$headers,
						$fnRow,
						$db->fetchAll($select)
					);
				}
				
                break;
            case 'f4':       // Payment Receipt Report
                $this->view->title = "Payment Receipt Report";
                $template = 'reports4';
                $dataFields = array('date', 'time', 'id', 'user_id', 'partner_id', 'method', 'card_number', 'status', 'ammount');
                $titleFields = array('date' => 'Date', 'time' => 'Time', 'id' => 'Transaction ID', 'user_id' => 'Traveler ID', 'partner_id' => 'Partner ID', 'method' => 'Method', 'card_number' => 'Card Number', 'status' => 'Status', 'ammount' => 'Amount');
                $select->from('transactions')
                        ->join('reservations', 'transactions.id=reservations.transaction_id', array('user_id'));
                if (@$filter['s']) {
                    $mySqlDate = self::urlDateToMySql($filter['s']);
                    if ($mySqlDate) {
                        $select->where('transactions.date>=?', $mySqlDate);
                    }
                }
                if (@$filter['e']) {
                    $mySqlDate = self::urlDateToMySql($filter['e']);
                    if ($mySqlDate) {
                        $select->where('transactions.date<=?', $mySqlDate);
                    }
                }
                if (@$filter['status'] === 1 || @$filter['status'] === 0) {
                    $select->where('transactions.status<=?', $filter['status']);
                }

                $select->order(array_key_exists($this->view->paramSort, $dataFields) ? $dataFields[$this->view->paramSort] . "$seq" : $dataFields[0]);
                if ($page == 'pdf') {
                    $pdfMaker = new PdfMaker();
                    $pdfMaker->makeByHtml($titleFields, $db->fetchAll($select));
                }
                if ($page == 'xls') {
					$headers = array(array(
 										'Date',
										'Time', 
										'Transaction ID', 
										'Traveler ID', 
										'Partner ID', 
										'Method',  
										'Card Number', 
										'Status', 
										'Amount'
 									));
					$fnRow = create_function('$row','
						return array(
							$row->date,
							$row->time,
							$row->transaction_id,
							$row->user_id,
							$row->partner_id,
							$row->method,
							$row->card_number,
							$row->status,
							$row->ammount
						);');
					
					require_once("TF/Export.php");
					TF_Export::xlsFromData(
						"log-reports.xls",
						$headers,
						$fnRow,
						$db->fetchAll($select)
					);
				}
                break;
            case 's1':       // Partner account activity by date
                $this->view->title = "Partner account activity by date";
                $this->view->title = "Payment Receipt Report";
                $template = 'reportss1';
                $dataFields = array('date', 'session_id', 'name', 'login_time', 'logout_time', 'total');
                $titleFields = array('date' => 'Date', 'session_id' => 'Session ID', 'name' => 'User', 'login_time' => 'Log in Time', 'logout_time' => 'Log out Time', 'total' => 'Total Time');
                $select->from('user_log', array('session_id', 'date' => new Zend_Db_Expr("DATE_FORMAT(login_at,'%b %d, %Y')"), 'login_time' => new Zend_Db_Expr("DATE_FORMAT(login_at,'%h:%i %p')"), 'logout_time' => new Zend_Db_Expr("DATE_FORMAT(last_accessed,'%h:%i %p')"), 'total' => new Zend_Db_Expr("TIMEDIFF(last_accessed, login_at)")))
                        ->join('users', 'users.id=user_log.user_id', array('name'));
                if (@$filter['s']) {
                    $mySqlDate = self::urlDateToMySql($filter['s']);
                    if ($mySqlDate) {
                        $select->where('user_log.login_at>=?', $mySqlDate);
                    }
                }
                if (@$filter['e']) {
                    $mySqlDate = self::urlDateToMySql($filter['e']);
                    if ($mySqlDate) {
                        $select->where('user_log.login_at<=?', $mySqlDate);
                    }
                }
                $select->order(array_key_exists($this->view->paramSort, $dataFields) ? $dataFields[$this->view->paramSort] . "$seq" : $dataFields[0]);
                if ($page == 'pdf') {
                    $pdfMaker = new PdfMaker();
                    $pdfMaker->makeByHtml($titleFields, $db->fetchAll($select));
                }
                if ($page == 'xls') {
					$headers = array(array(
							'Date', 
							'Session ID',
							'User',
							'Log in Time',
							'Log out Time',
							'Total Time'
						));
					$fnRow = create_function('$row','
						return array(
							$row->date,
							$row->session_id,
							$row->name,
							$row->login_time,
							$row->logout_time,
							$row->total
						);');
					
					require_once("TF/Export.php");
					TF_Export::xlsFromData(
						"log-reports.xls",
						$headers,
						$fnRow,
						$db->fetchAll($select)
					);
				}
                break;
            case 's2':       // Partner account change by user
                $this->view->title = "User accont by date";
                $this->render('reportss2'); return;
                //$select->where("transactions.status = 0" );	
                break;
            case 's3':       // Partner pending information by account manager
                $this->view->title = "Partner pending information";
                $template = 'reportss3';
                $dataFields = array('user_name', 'vendor_name', 'title', 'email');
                $titleFields = array('user_name' => 'User', 'vendor_id' => 'Partner', 'title' => 'Listing', 'email' => 'Listing Email', 'pending_info' => 'Pending Info');
                $select->from('listings')
                        ->join('vendors', 'vendors.id=listings.vendor_id', array('vendor_name' => 'name', 'vendor_image' => 'image'))
                        ->join('users', 'users.id=vendors.user_id', array('user_name' => 'name', 'user_image' => 'image'))
                        ->joinleft('listing_overviews', 'listing_overviews.listing_id=listings.id', array('overview_id' => 'id'))
                        ->joinleft('listing_pictures', 'listing_pictures.listing_id=listings.id', array('picture_id' => 'id'))
                ;
                if (@$filter['city']) {
                    $select->where('listings.city_id=?', $filter['city']);
                }
                if (@$filter['listingt']) {
                    if ($filter['listingt'] != 75) {  // not All
                        $select->where('listings.main_type=?', $filter['listingt']);
                    }
                }
                $select->group('listings.id');
                $select->order(array_key_exists($this->view->paramSort, $dataFields) ? $dataFields[$this->view->paramSort] . "$seq" : $dataFields[0]);
                if ($page == 'pdf') {
                    $pdfMaker = new PdfMaker();
                    $pdfMaker->makeByHtml($titleFields, $db->fetchAll($select));
                }
                if ($page == 'xls') {
					$headers = array(array(
							 'User',
							 'Partner',
							 'Listing',
							 'Listing Email',
							 'Pending Info'
							 ));
					$fnRow = create_function('$row','
						return array(
							$row->user_name,
							$row->vender_id,
							$row->title,
							$row->email,
							$row->pending_info,
						);');
					
					require_once("TF/Export.php");
					TF_Export::xlsFromData(
						"log-reports.xls",
						$headers,
						$fnRow,
						$db->fetchAll($select)
					);
				}
                break;
            case 's4':       // User Account by Date
                $this->view->title = "Partner account change by user";
                $this->render('reportss4'); return;
                break;
            case 's5':       // User log time by date
                $this->view->title = "Partner activity by date";
                $this->render('reportss5'); return;
                break;
            default:
                $this->view->title = "Pending";
        }
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPageNumber($page);
        if ($this->_getParam('task') == 's3') {
            foreach ($paginator as $listing) {
                $listing->pending_info = $this->listingFault($listing);
            }
        }
        $this->view->paginator = $paginator;
        $this->view->numCount = $paginator->getTotalItemCount();
        $this->render($template);
    }

    private function listingFault($listing) {
        $errors = array();
        if (empty($listing->vendor_image))
            $errors[] = 'company picture/logo';
        if ($listing->title == "Untitle Listing" or empty($listing->title))
            $errors[] = 'Title';
        if ($listing->description == "")
            $errors[] = 'Description';
        if ($listing->country_id != 0)
            $errors[] = 'Country';
        if ($listing->city_id != 0)
            $errors[] = 'City';
        if (is_null($listing->lat))
            $errors[] = 'Map';
        if (empty($listing->overview_id))
            $errors[] = 'Overview';
        if (empty($listing->picture_id))
            $errors[] = 'Image';

        /*
          $photos = $this->listings->getPictures($listing->id);
          if(count($photos) > 0)
          $validate['photos']['done'] = true;

         */
        /*
          $overview = $this->listings->getOverviewOf($listing->id);
          if(!empty($overview->about))
          $validate['overview']['done'] = true;
         */
        switch ($listing->main_type) {
            case 5:
                break;
            case 6:
                break;
            default: break;
        }

        return implode(', ', $errors);
    }

    private function parseUrlParam($param) {
        $get = array();
        if (!trim($param)) {
            return $get;
        }
        $parts = explode('&', $param);
        foreach ($parts as $part) {
            $queryParts = explode('=', $part);
            $get[$queryParts[0]] = @$queryParts[1];
        }
        return $get;
    }

    public function tripsAction() {
        $GLOBALS['menuContext'] = 2;
        $this->view->listingContext = $this->_getParam('task');
        $this->view->baseUrl = '/admin/trips/' . $this->_getParam('task') . '/1/default/default';
        switch ($this->_getParam('task')) {
            case 'all':
                $this->tripsTask();
                break;
            case 'add':
                $this->tripAddTask();
                break;
            case 'edit':
                $this->tripEditTask();
                break;
            case 'view':
                $this->tripViewTask();
                break;
            case 'data':
                $this->tripDataTask();
                break;
            default:
                throw new Exception('Page not found');
        }
    }


	private function tripAddTask(){
		if($this->getRequest()->isPost()){
			$errors = $this->validateTripData($_POST);
			
			if(count($errors)){
				$validation_error_data = array();
				foreach($errors as $key=>$value){
					$validation_error_data[] = array('field'=>$key, 'message'=>$value);
				}
				self::jsonEcho(json_encode(array('attempt'=>'fail', 'error_code'=>'validation_error', 'description'=>'Field validation error', 'data'=>$validation_error_data)));
			}
		}
	
		//create trip now
	
		try{
			$tblTrip = new Model_Trips();
			$row = $tblTrip->fetchNew();
			$row->title  = $_POST['t_name'];
			$row->country_id = $_POST['trip_country'];
			$row->min=$_POST['trip_min'];
			$row->max=$_POST['trip_max'];
			$row->start_city=$_POST['trip_scity'];
			$row->end_city=$_POST['trip_ecity'];
			$row->price=$_POST['trip_price'];
			$row->created = date("Y-m-d H:i:s");
			$row->updated = date("Y-m-d H:i:s");
			$id = $row->save();
			self::jsonEcho(json_encode(array('attempt'=>'success', 'error_code'=>'0', 'description'=>$id, 'data'=>$id)));
		}catch(Exception $e){
			self::jsonEcho(json_encode(array('attempt'=>'fail', 'error_code'=>'mysql_error', 'description'=>'MySQL Error', 'data'=>$e->getMessage())));
		}
	}
	
	private function validateTripData($postData){
		$errors = array();
		if(empty($postData['t_name']))$errors['t_name'] = 'This field is required';
		if(empty($postData['trip_country']))$errors['trip_country'] = 'This field is required';
		if(empty($postData['trip_scity']))$errors['trip_scity'] = 'This field is required';
		if(empty($postData['trip_ecity']))$errors['trip_scity'] = 'This field is required';
		if(empty($postData['trip_min']))$errors['trip_min'] = 'This field is required';
		if(empty($postData['trip_max']))$errors['trip_min'] = 'This field is required';
		if(empty($postData['trip_price']))$errors['trip_price'] = 'This field is required';
		return $errors;
	}


    private function tripsTask() {
        $page = $this->_getParam('page', 1);
        $this->view->searchText = $this->_getParam('q');
        $this->view->paramSort = (int) $this->_getParam('sort');
        $this->view->paramSequence = $this->_getParam('seq');
        $seq = $this->view->paramSequence == 'desc' ? ' desc' : '';
        $this->view->paramQuery = $this->_getParam('q');



        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();

        switch ($this->_getParam('task')) {
            case 'all':
                $this->view->title = "";
                $template = 'trips';
                $dataFields = array('title', 'days', 'price', 'min', 'category_name', 'country_name');
                $select->from('trips')
                        ->joinleft(array('country' => 'places'), 'trips.country_id=country.id', array('country_name' => 'title'))
                        ->joinleft('tripcategories', 'trips.category_id=tripcategories.id', array('category_name' => 'label'));
                if ($this->view->searchText) {
                    $select->where("trips.title like '{$this->view->searchText}%' or country.title like '{$this->view->searchText}'");
                }
                $select->order(array_key_exists($this->view->paramSort, $dataFields) ? $dataFields[$this->view->paramSort] . "$seq" : $dataFields[0]);
                break;
            default:
                $this->view->title = "Pending";
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
        $this->view->numCount = $paginator->getTotalItemCount();
        $this->render($template);
    }

    private function tripEditTask() {
        $page = $this->_getParam('page', 1);
        $tripId = (int) $this->_getParam('sort');

        if (!$tripId) {
            throw new Exception("Invalid trip");
        }

        try {
            $trip = $this->trips->getTripById($tripId);
        } catch (Exception $e) {
            throw new Exception("Unable to load trip");
        }

        $this->view->successMessage = '';
        if (@$_SESSION['alert']) {
            $this->view->successMessage = "Your changes have been saved";
            $_SESSION['alert'] = '';
        }

        switch ($page) {
            case 1:
                $this->view->title = "";
                $this->tripEditTask1($trip);
                break;
            case 2:
                $this->view->title = "";
                $this->tripEditTask2($trip);
                break;
            case 3:
                $this->view->title = "";
                $template = 'trip3';
                break;
            case 4:
                $this->view->title = "";
                $this->tripEditTask4($trip);
                break;
            case 5:
                $this->view->title = "";
                $this->tripEditTask5($trip);
                break;
            case 6:
                $this->view->title = "";
                $this->tripEditTask6($trip);
                break;
            default:
                $this->view->title = "";
                $template = 'trip1';
        }
    }

    private function tripEditTask1($trip) {
        $this->view->trip = $trip;
        $this->view->highlights = $this->trips->getHighlights($trip->id);
        $this->view->errors = array();
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
		$this->view->cities = json_encode($this->trips->getCities($trip->id));


        if ($this->getRequest()->isPost()) {
            $errors = $this->validateTrip1Data($_POST);
            $lands = isset($_POST['landscapes']) ? $_POST['landscapes'] : array();
            $highlights = array();
            foreach ($_POST['highlight'] as $key => $highlight) {
                $objHighlight = new stdClass;
                $objHighlight->title = @$_POST['highlight'][$key];
                $objHighlight->text = @$_POST['hDescription'][$key];
                $highlights[] = $objHighlight;
            }
			
			$selectedCity = array();
			for($i=1; $i<=5; $i++){
				$selectedCity[] = $_POST["trip_city$i"] ? (object)array('trip_id'=>$trip->id, 'city_id'=>$_POST["trip_city$i"]): (object)array('trip_id'=>$trip->id, 'city_id'=>0);
			}
			$this->view->cities = json_encode($selectedCity);
			
            $this->view->highlights = $highlights;
            if (count($errors)) {
                $this->view->errors = $errors;
                $this->view->trip->title = $_POST['title'];
                $this->view->trip->description = $_POST['description'];
                $this->view->trip->days = $_POST['days'];
                $this->view->trip->country_id = $_POST['trip_country'];

                $this->render('trip1');
                return;
            }
            $trip->title = $_POST['title'];
            $trip->description = $_POST['description'];
            $trip->days = $_POST['days'];
            $trip->country_id = $_POST['trip_country'];
            $trip->save();
			
            if ($uploadedFileName = $this->saveTripPhoto($trip->id)) {
                $trip->image = $uploadedFileName;
                $trip->save();
            }

            $this->trips->saveHighlights($trip->id, $highlights);
			$this->trips->saveCities($trip->id, $trip->country_id, $selectedCity );
            $_SESSION['alert'] = 'Your changes have been saved';
            $this->_redirect('/admin/trips/edit/1/' . $trip->id);
            //put success message
        }

        $this->render('trip1');
    }

    private function saveTripPhoto($tripId) {
        if (!$_FILES['image']['name']) {
            return false;
        }

        $mediaPath = $_SERVER['DOCUMENT_ROOT'] . "/images/trips";
        if (!file_exists($mediaPath)) {
            @mkdir($mediaPath, 0777);
        }

        $tripMediaPath = $mediaPath . '/' . $tripId;
        if (!file_exists($tripMediaPath)) {
            @mkdir($tripMediaPath, 0777);
        }

        $path_parts = pathinfo($_FILES['image']['name']);
        $extension = $path_parts['extension'];
        $targetPath = $tripMediaPath . '/' . $tripId . '.' . $extension;
        $htmlFileName = '/images/trips/' . "/$tripId/" . $tripId . '.' . $extension;

        $tmp_name = $_FILES['image']['tmp_name'];
        @move_uploaded_file($tmp_name, $targetPath);
        return $htmlFileName;
    }

    private function validateTrip1Data($postData) {
        $errors = array();
        if (empty($postData['title']))
            $errors['title'] = 'Trip Title can not be blank';

        if (empty($postData['description']))
            $errors['description'] = 'Trip Description can not be blank';

        if (empty($postData['trip_country']))
            $errors['country'] = 'Select Country';

        if (!(int) $postData['days'])
            $errors['days'] = 'Trip Duration can not be blank';

        return $errors;
    }

    private function tripEditTask2($trip) {
        $this->view->trip = $trip;
        $facts = $this->trips->getfacts($trip->id);
        $paragraphs = array(1 => 'The Culture', 'The Environment', 'Knowledge', 'Things you\'ll love', 'When to take this trip', 'Tips & Recommendations');
        $paras = array();
        foreach ($facts as $fact) {
            $paras[$fact->type][] = array('text' => $fact->text, 'image' => $fact->image);
        }

        foreach ($paragraphs as $key => $value) {
			if (!isset($paras[$key])) {
                $paras[$key]= array();
            }

            /*
			if (!isset($paras[$key])) {
                $paras[$key][0] = array('text' => '', 'image' => '');
            }
			*/
        }

        //echo "<pre>"; print_r($paras); die;
        $this->view->errors = array();
        $this->view->paragraphs = $paragraphs;
        $this->view->paras = $paras;
        if ($this->getRequest()->isPost()) {
            $errors = $this->validateTrip2Data($_POST);

            if (count($errors)) {
                $this->render('trip2');
                return;
            }

            $this->trips->deleteFacts($trip->id);
            foreach ($paragraphs as $key => $value) {
                foreach ($_POST["t$key"] as $innerKey => $text) {
                    if ($_FILES["f$key"]['name'][$innerKey]) {
                        $uploadedFileName = $this->saveTripFactPhoto($trip->id, $_FILES["f$key"], $innerKey);
                    } else {
                        $uploadedFileName = @$_POST["h$key"][$innerKey];
                    }
                    $this->trips->saveFacts($trip->id, $key, $text, $uploadedFileName);
                }
            }

            $_SESSION['alert'] = 'Your changes have been saved';
            $this->_redirect('/admin/trips/edit/2/' . $trip->id);
            //put success message
        }
        $this->render('trip2');
    }

    private function validateTrip2Data($postData) {
        $errors = array();
        return $errors;
    }

    private function saveTripFactPhoto($tripId, $postData, $key) {
        if (!$postData['name'][$key]) {
            return false;
        }

        $mediaPath = $_SERVER['DOCUMENT_ROOT'] . "/images/trips";
        if (!file_exists($mediaPath)) {
            @mkdir($mediaPath, 0777);
        }

        $tripMediaPath = $mediaPath . '/' . $tripId;
        if (!file_exists($tripMediaPath)) {
            @mkdir($tripMediaPath, 0777);
        }

        $path_parts = pathinfo($postData['name'][$key]);
        $extension = $path_parts['extension'];
        $random = self::randomBytes(32);

        $targetPath = $tripMediaPath . '/' . $random . '.' . $extension;
        $htmlFileName = '/images/trips' . "/$tripId/" . $random . '.' . $extension;

        $tmp_name = $postData['tmp_name'][$key];
        @move_uploaded_file($tmp_name, $targetPath);
        return $htmlFileName;
    }

    static public function randomBytes($len) {
        $key = '';
        $chars = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        for ($i = 0; $i < $len; $i++) {
            $key .= $chars[mt_rand(0, count($chars) - 1)];
        }
        return $key;
    }

    private function tripEditTask4($trip) {
        $this->view->trip = $trip;
        $tripIncludeData = $this->trips->getIncludes($trip->id);
        $includes = array();
        $excludes = array();
        foreach ($tripIncludeData as $data) {
            if ($data->included) {
                $includes[] = $data;
            } else {
                $excludes[] = $data;
            }
        }

        if (!count($includes)) {
            $obj = new stdClass;
            $obj->text = '';
            $obj->included = 1;
            $obj->id = 0;
            $includes[] = $obj;
        }

        if (!count($excludes)) {
            $obj = new stdClass;
            $obj->text = '';
            $obj->included = 0;
            $obj->id = 0;
            $excludes[] = $obj;
        }

        $this->view->includes = $includes;
        $this->view->excludes = $excludes;



        if ($this->getRequest()->isPost()) {
            $includes = array();
            foreach ($_POST['inclusion'] as $key => $inclusion) {
                $objInclude = new stdClass;
                $objInclude->text = @$_POST['inclusion'][$key];
                $objInclude->id = @$_POST['id'][$key];
                $includes[] = $objInclude;
            }
            $this->view->includes = $includes;
            $excludes = array();
            foreach ($_POST['exclusion'] as $key => $exclusion) {
                $objEnclude = new stdClass;
                $objEnclude->text = @$_POST['exclusion'][$key];
                $objInclude->id = @$_POST['id'][$key];
                $excludes[] = $objEnclude;
            }
            $this->view->excludes = $excludes;
            /* 	
              if(count($errors)){
              $this->view->errors = $errors;
              $this->view->trip->title = $_POST['title'];
              $this->view->trip->description = $_POST['description'];
              $this->view->trip->days = $_POST['days'];

              $this->render('trip1');
              return;
              } */

            $this->trips->deleteIncludes($trip->id);
            $this->trips->saveIncludes($trip->id, $includes, 1);
            $this->trips->saveIncludes($trip->id, $excludes, 0);
            $_SESSION['alert'] = 'Your changes have been saved';
            $this->_redirect('/admin/trips/edit/4/' . $trip->id);
        }

        $this->render('trip4');
    }
	
    private function tripEditTask5($trip) {
        if (@$_SESSION['alert']) {
            $this->view->successMessage = "Your changes have been saved";
            $_SESSION['alert'] = '';
        }
        $this->view->trip = $trip;
		$day = $this->_request->getParam('q');
		$slide = 0;
		$items = $this->trips->getListingOf3($trip->id); 
        $dayWise = array();
		$maxDay=1;
		foreach($items as $item){
			if($maxDay > $item->day)
				$maxDay = $item->day;
		}
		
		foreach($items as $item){
			$dayWise[$item->day][] = $item;
		}

		$tripDuration = max($maxDay, $trip->days);
		for($i=1; $i<=$tripDuration; $i++){
			if(!isset($dayWise[$i])){
				$dayWise[$i] = array();
			}
		}
		ksort($dayWise, SORT_NUMERIC);
		
		$i=0;
		foreach($dayWise as $key=>$value){
			if($key == $day){
				$slide=$i;
			}
			$i++;
		}
		//echo "<pre>" ; print_r($dayWise); die;
		$this->view->items = $dayWise;
		$this->view->slide = $slide;
		$this->render('trip5');

	}

    private function tripEditTask6($trip) {
        $this->view->trip = $trip;
		$listing = $this->_request->getParam('seq');
		$itineraryDay = $this->_request->getParam('q') ? $this->_request->getParam('q') : 1;
        $this->view->doWhat = $listing ? "Edit" : "Create";
		$countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
		
		$listingType = $this->listings->getMainTypes();
		$this->view->listingTypes = $listingType;

		$this->view->flexi_hour = false;
        $this->view->errors = array();
		$this->view->title = '';
		$this->view->description = '';
		$this->view->day = '';
		$this->view->type = '';
		$this->view->country ='';
		$this->view->city = '';
		$this->view->duration = '';
		$this->view->start_hour = '';
		$this->view->end_hour = '';
		$this->view->timef = '';
		$this->view->lng = '';
		$this->view->lat = '';
		$this->view->image = '';
		$this->view->featured = '';
		$this->view->day = $itineraryDay;
	
        if ($this->getRequest()->isPost()) {
            $errors = $this->validateTrip6Data($_POST);
            if (count($errors)) {
                $this->view->errors = $errors;
                $this->view->title = $_POST['title'];
                $this->view->description = $_POST['description'];
                $this->view->day = $_POST['day'];
                $this->view->type = $_POST['type'];
                $this->view->country = $_POST['country'];
                $this->view->city = $_POST['city'];
                $this->view->duration = $_POST['duration'];
                $this->view->start_hour = $_POST['start_hour'];
                $this->view->end_hour = $_POST['end_hour'];
				$this->view->time_type = $_POST['time_type'];
                $this->view->lng = $_POST['lng'];
                $this->view->lat = $_POST['lat'];
				$this->view->featured = @$_POST['check'];
				$this->view->image = $_POST['image'];
				
                $this->render('trip6');
                return;
            }
			     
            $data = array();
			$data['title'] = $_POST['title'];
			$data['main_type']=$_POST['type'];
			$data['description'] = $_POST['description'];
			$data['day'] = $_POST['day'];
			$data['city_id'] = $_POST['city'];
			$data['country_id'] = $_POST['country'];
			if($_POST['time_type'] == 'fixed'){
				$data['start'] = $this->timeTo24HoursFormat($_POST['start_hour']);
				$data['end'] = $this->timeTo24HoursFormat($_POST['end_hour']);
			}
			else{
				$data['time'] = $_POST['flexi_time'];
				$data['start'] = '00:00:00';
				$data['end'] =  '00:00:00';
			}
			if($_POST['type'] == 5){
				$data['start'] = '00:00:00';
				$data['end'] =  '00:00:00';
				$data['time'] = 4;
			}
			$data['duration'] = $_POST['duration'];
			$data['lng'] = $_POST['lng'];
			$data['lat'] = $_POST['lat'];
			$data['featured']=(int) $_POST['check'];
			if(!$listing){
				$data['itinerary_id'] = $trip->id;
			}
			$listingId = $this->trips->saveListing($data, $listing);
			$imageName = $this->saveTripListingPhoto($trip->id, $listingId);
			if($imageName){
				$imageData = array('image'=>$imageName);
				$listingId = $this->trips->saveListing($imageData, $listingId);
			}
			
            $_SESSION['alert'] = 'Your changes have been saved';
            //$this->_redirect('/admin/trips/edit/5/' . $trip->id);
            $this->_redirect($this->view->url(array('page'=>5, 'q'=>$_POST['day'])));
			
        }

		if($listing){
			$listing = $this->trips->getTripListingById($listing);
			if(!$listing){
				throw new Exception("Error occured. Unable to load trip listing");	
			}
			$this->view->title = $listing->title;
			$this->view->description = $listing->description;
			$this->view->day = $listing->day;
			$this->view->type = $listing->main_type;
			$this->view->country = $listing->country_id;
			$this->view->city = $listing->city_id;
			$this->view->duration = $listing->duration;
			$this->view->start_hour = $listing->start_time;
			$this->view->end_hour = $listing->end_time;
			$this->view->timef = $listing->time;
			$this->view->lng = $listing->lng;
			$this->view->lat = $listing->lat;
			$this->view->image = $listing->image;
			$this->view->featured = $listing->featured;
			if(substr($listing->start, 0, 5)== '00:00' && substr($listing->end, 0, 5)== '00:00' ){
				$this->view->flexi_hour = true;	
				
			}
		}
		$this->render('trip6');
	}
	
	private function saveTripListingPhoto($tripId, $listingId) {
        if (!$_FILES['image']['name']) {
            return false;
        }

        $mediaPath = $_SERVER['DOCUMENT_ROOT'] . "/images/trips";
        if (!file_exists($mediaPath)) {
            @mkdir($mediaPath, 0777);
        }

        $tripMediaPath = $mediaPath . '/' . $tripId;
        if (!file_exists($tripMediaPath)) {
            @mkdir($tripMediaPath, 0777);
        }

        $listingMediaPath = $tripMediaPath . '/listings';
        if (!file_exists($listingMediaPath)) {
            @mkdir($listingMediaPath, 0777);
        }

        $path_parts = pathinfo($_FILES['image']['name']);
        $extension = $path_parts['extension'];
        $targetPath = $listingMediaPath . '/' . $listingId . '.' . $extension;
        $htmlFileName = '/images/trips/' . "/$tripId/listings/" . $listingId . '.' . $extension;

        $tmp_name = $_FILES['image']['tmp_name'];
        @move_uploaded_file($tmp_name, $targetPath);
        return $htmlFileName;
    }

	private function validateTrip6Data($postData) {
        $errors = array();
        if (empty($postData['title']))
            $errors['title'] = 'Listing Title can not be blank';

        if (empty($postData['description']))
            $errors['description'] = 'Listing Description can not be blank';

		if (!(int) $postData['country'])
            $errors['country'] = 'Country can not be blank';

		if (!(int) $postData['city'])
            $errors['city'] = 'City can not be blank';
        
		if (!(int) $postData['day'])
            $errors['day'] = 'Itinerary day must be a positive number';
        
		if (!(int) $postData['duration'])
            $errors['duration'] = 'Itinerary duration must be a positive number';
		
		if($postData['type'] != 5 && $postData['time_type'] == 'fixed'){

			if (empty($postData['start_hour']))
				$errors['start_hour'] = 'Starting hour can not be blank';
			
			if (empty($postData['end_hour']))
				$errors['end_hour'] = 'Ending hour can not be blank';
		}
        return $errors;
    }

	
    public function vendorsAction() {
        switch ($this->_getParam('task')) {
            case 'add':
                $this->vendorAdd();
            default:
                throw new Exception('Invalid page');
        }
    }

    private function vendorAdd() {
        if ($this->getRequest()->isPost()) {
            $errors = $this->validateVendorData($_POST);

            if (count($errors)) {
                $validation_error_data = array();
                foreach ($errors as $key => $value) {
                    $validation_error_data[] = array('field' => $key, 'message' => $value);
                }
                self::jsonEcho(json_encode(array('attempt' => 'fail', 'error_code' => 'validation_error', 'description' => 'Field validation error', 'data' => $validation_error_data)));
            }

            //create vendor here
			$data = array();
			$data['name'] = $_POST['c_name'];
			$data['email'] = $_POST['c_email'];
			$data['phone'] = $_POST['phone'];
			$data['password'] = self::randomBytes(6);
			$data['country_id'] = $_POST['country'];
			$data['website'] = $_POST['website'];
			$data['contact_name'] = $_POST['cnt_name'];
            try {
                $id = $this->accounts->signupVendor($data, false);
                self::jsonEcho(json_encode(array('attempt' => 'success', 'error_code' => '0', 'description' => $id, 'data' => $id)));
            } catch (Exception $e) {
                self::jsonEcho(json_encode(array('attempt' => 'fail', 'error_code' => 'mysql_error', 'description' => 'MySQL Error', 'data' => $e->getMessage())));
            }
        }
    }

    private function validateVendorData($postData) {
        $errors = array();
        if (empty($postData['c_name']))
            $errors['c_name'] = 'This field is required';

        if (empty($postData['c_email'])) {
            $errors['c_email'] = 'This field is required';
        } else {
            $emailValidator = new Zend_Validate_EmailAddress();
            if (!$emailValidator->isValid($postData['c_email'])) {
                $errors['c_email'] = 'Not a valid email';
            }
			
			$this->accounts = new WS_AccountService();
			if(!$this->accounts->validateEmail(trim($postData['c_email']))){
                $errors['c_email'] = 'Email already exists';
			}
			
        }

        if (empty($postData['cnt_name']))
            $errors['cnt_name'] = 'This field is required';

        if (empty($postData['phone']))
            $errors['phone'] = 'This field is required';

        if (empty($postData['website']))
            $errors['website'] = 'This field is required';

        if (empty($postData['country']))
            $errors['country'] = 'This field is required';
        return $errors;
    }
	
	public function timeTo24HoursFormat($time){
		$ampm = strtolower(substr($time, -2, 2));
		if(	$ampm != 'am' && $ampm !='pm'){
			return $time;	
		}
		if(strlen($time)!=8){
			return $time;	
		}
		
		$hour = (int) substr($time, 0, 2);
		$minut = substr($time, 3, 2);
		
		if($ampm == 'am'){
			if($hour == 12){
				$hour=0;
			}
			return str_pad($hour, 2, '0',STR_PAD_LEFT) . ':' . $minut;	
		}
		
		if($hour != 12){
			$hour += 12;
		}
		return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . $minut;	
	}

    static function urlDateToMySql($dateString) {
        $months = array('Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12');
        $monthName = substr($dateString, 0, 3);
        if (!array_key_exists($monthName, $months)) {
            return false;
        }
        return substr($dateString, 8, 4) . '-' . $months[$monthName] . '-' . substr($dateString, 4, 2);
    }

    static function jsonEcho($jsonString) {
        header("content-type:text/json");
        echo $jsonString;
        exit;
    }
	

}
