<?php

class IndexController extends Zend_Controller_Action
{
    const ACTIVITY      = 6;
    const ENTERTAIMENT  = 7;
    const TOURIST_SIGHT = 4;
    const RESTAURANT    = 2;
    const HOTEL         = 5;

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
     * @var WS_VendorService
     */
    protected $vendors;
    
    /**
     *
     * @var WS_Search
     */
    protected $search;
    
    /**
     *
     * @var WS_User
     */
    protected $user;
    
    /**
     *
     * @var WS_ReviewsService
     */
    protected $reviews;
    
    /**
     *
     * @var WS_PublicQuestionsService
     */    
    protected $questions;
    
    /**
     *
     * @var WS_TripsService
     */
    protected $trips;
    
    /**
     *
     * @var WS_JsonService 
     */
    protected $json;
    

    public function init()
    {
        $this->places   = new WS_PlacesService();
        $this->listings = new WS_ListingService();
        $this->vendors  = new WS_VendorService();
        $this->search   = new WS_Search();
        
        $this->reviews  = new WS_ReviewsService();
        $this->questions= new WS_PublicQuestionsService();
        
        $this->trips    = new WS_TripsService();
        
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $this->user = new WS_User($auth->getIdentity());
            $this->view->user = $this->user->getData(true);
        }
        
        $this->json = new WS_JsonService();
        $this->view->tags = $this->json->getSerchTerms();
        
        $this->view->lang = $this->_getParam('lang');
    }

    public function indexAction()
    {
        $countries = $this->places->getPlaces(2);
        $country   = $this->places->getPlaceById(18);
        $cities    = $this->places->getPlaces(3, $country->id);
        
        $this->view->countries = $countries;
        $this->view->cities    = $cities;
        $this->view->country   = $country;
    }
    
    public function aboutAction()
    {
        if($this->view->lang == 'es-ES')
            $this->render('about-es');
    }
    
    public function faqsAction()
    {
        $faqs = new Zend_Db_Table('faqs');
        $select = $faqs->select();
        $select->where('lang = ?', $this->view->lang);
        
        $this->view->faqs = $faqs->fetchAll($select);
    }
    
    public function cityAction()
    {   
        $countries   = $this->places->getPlaces(2);
        
        $country_idf = $this->getRequest()->getParam('country');
        $city_idf    = $this->getRequest()->getParam('city');
        $country     = $this->places->getPlaceByIdf($country_idf);
        $region      = $this->places->getPlaceById($country->parent_id);
        
        $city = null;
        if($city_idf != 'default') 
            $city    = $this->places->getPlaceByIdf($city_idf, 3, $country->id);
        
        $categories  = $this->listings->getMainCategories(true);
        foreach($categories as $c => $a){
            $s = strtolower(str_replace(' ','-',$a['name']));
            if($s != 'all')
                $subcats[$s] = $this->listings->getSubCategoriesOf($c);
        }
        
        if(!is_null($city)) 
            $ls_count = $this->listings->countListings($city->id);
        else
            $ls_count = $this->listings->countListings(null, null, $country->id);
        
        $this->view->countries = $countries;
        
        $this->view->region  = $region;
        $this->view->country = $country;
        $this->view->city    = $city;
        $this->view->categories = $categories;
        $this->view->subcats    = $subcats;
        
        $this->view->ls_count = $ls_count;
        
        //var_dump($ls_count); die;
    }
    
    public function searchAction()
    {
        
    }
    
    public function collectionsAction()
    {
        
    }
    
    public function resultsAction()
    {
        
    }
    
    public function listingAction()
    {
        $template    = 'listing';
        
        $country_idf = $this->getRequest()->getParam('country');
        $city_idf    = $this->getRequest()->getParam('city');
        $listing_idf = $this->getRequest()->getParam('listing');
        $country     = $this->places->getPlaceByIdf($country_idf);
        $region      = $this->places->getPlaceById($country->parent_id);
        $city        = $this->places->getPlaceByIdf($city_idf, 3, $country->id);
        $listing     = $this->listings->getListingByIdf($listing_idf, $city->id, $country->id);
        
        $pictures    = $this->listings->getPictures($listing->id);
        
        $trips = null;
        if($this->user)
            $trips = $this->trips->getFutureTripsBy($this->user->getId());
        
        switch($listing->main_type){
            case self::ACTIVITY: 
                $keys = Zend_Registry::get('stripe');
                $template = 'listing-activity';      
                $options = $this->listings->getSchedulesOf($listing->id);
                $this->view->options = $options;
                $overview = $this->listings->getOverviewOf2($listing->id);
                $departure_city = null;
                if($listing->departure)
                    $departure_city = $this->places->getPlaceById($listing->departure);
                $return_city = null;
                if($listing->departure != $listing->return){
                    if($listing->return)
                        $return_city = $this->places->getPlaceById($listing->return);
                }
                
                if(is_null($listing->min) or is_null($listing->max)){
                    $capacities = $this->listings->getActivityTypes($listing->id);
                    $this->view->capacities = $capacities;
                    
                    $prices = $this->listings->getSchPrices($listing);
                    if(!is_null($prices) and isset($prices[0]))
                        $prices = $prices[0];
                    
                    $this->view->prices = $prices;
                }

                $details = $this->listings->getDetails($listing->id);

                $getthere = $this->listings->getLocationOf($listing->id);
                
                $faqs        = $this->listings->getFAQsOf($listing->id);
                $vendor      = $this->vendors->getVendorById($listing->vendor_id);

                $attributes  = $this->listings->getAttributesOf($listing->id);

                $this->view->overview       = $overview;
                $this->view->departure_city = $departure_city;
                $this->view->return_city    = $return_city;
                $this->view->details        = $details;
                $this->view->getthere       = $getthere;
                $this->view->key            = $keys['public_key'];
            break;
            case self::ENTERTAIMENT  : 
                $template = 'listing-entertaiment';

                $place = $this->listings->getPlaceInfo($listing->id);
                $this->view->place = $place;
                
                $options = $this->listings->getSchedulesOf($listing->id);
                $this->view->schedules = $options;

                //var_dump($details->toArray()); die;
                $creditcards = array(
                    'visa'             => '/images2/checkout-card1.png',
                    'mastercard'       => '/images2/checkout-card2.png',
                    'amex'             => '/images2/checkout-card3.png',
                    'discover'         => '/images2/checkout-card5.png',
                );
                
                $this->view->cards = $creditcards;
            break;
            case self::HOTEL: 
                $keys = Zend_Registry::get('stripe');
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
                
                
                $prices = $this->listings->getSchPrices($listing);
                if(!is_null($prices))
                    $prices = $prices[0];

                $this->view->prices = $prices;

                $faqs        = $this->listings->getFAQsOf($listing->id);
                $vendor      = $this->vendors->getVendorById($listing->vendor_id);

                $attributes  = $this->listings->getAttributesOf($listing->id);
                //var_dump($rooms2); die;

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
                $this->view->key            = $keys['public_key'];
            break;
            case self::RESTAURANT    : 
                $template = 'listing-restaurant';
                
                $place = $this->listings->getPlaceInfo($listing->id);
                $this->view->place = $place;
                
                $options = $this->listings->getSchedulesOf($listing->id);
                $this->view->schedules = $options;

                //var_dump($details->toArray()); die;
                $creditcards = array(
                    'visa'             => '/images2/checkout-card1.png',
                    'mastercard'       => '/images2/checkout-card2.png',
                    'amex'             => '/images2/checkout-card3.png',
                    'discover'         => '/images2/checkout-card5.png',
                );
                
                $this->view->cards = $creditcards;
            break;
            case self::TOURIST_SIGHT : 
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
            $this->view->form_action = 'cart/add';
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
            $this->view->form_action = 'login';
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
        
        //var_dump($listing); die;
        @$this->view->region          = $region;
        @$this->view->country         = $country;
        @$this->view->city            = $city;
        @$this->view->listing         = $listing;
        @$this->view->faqs            = $faqs;
        @$this->view->vendor          = $vendor;
        @$this->view->attributes      = $attributes;
        @$this->view->pictures        = $pictures;
        @$this->view->trips           = $trips;
        
        $places = $this->places->getPlaces(2);
        $this->view->countries = $places;
        
        if(!is_null($this->user))
                $this->view->user = $this->user->getData();
        
        $this->render($template);     
    }
    
    public function termsAction()
    {
        
    }
    
    public function mytripsAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
                $this->_redirect('/user/trips');
    }
    
    public function resetAction()
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
        $user = $users->fetchRow($select);
        
        if(is_null($user)) 
                throw new Exception('Email not found');
        
        $tokens = new Zend_Db_Table('resetpass_tokens');
        $select = $tokens->select();
        $select->where('user_id = ?', $user->id);
        $select->where('token = ?', $_token);
        $token = $tokens->fetchRow($select);
        
        if(is_null($token)) 
                throw new Exception('Token not found');
        
        $created = strtotime($token->created);
        $diff    = time() - $created;
        
        $caducated = false;
        if($diff > (60 * 60)) {
            $caducated = true;            
            $token->delete();
        }
        
        if($this->getRequest()->isPost()){
            if(!$caducated){
                if(($_POST['npassword'] == $_POST['rpassword']) and !empty($_POST['npassword'])) {
                    $user->password = md5($_POST['npassword']);
                    $user->save();
                    
                    $token->delete();
                    
                    $notifier = new WS_Notifier($user->id);
                    $notifier->passwordResetSuccess();
                    
                    setcookie('alert','Your password has been change. Please Login');
                    $this->_redirect('/login');
                } else {
                    $this->view->error = "Password verification incorrect";
                }
            }
        }
        $this->view->caducated = $caducated;
    }
}