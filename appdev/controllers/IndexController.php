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
        if($this->view->lang == 'es-ES')
            $this->render('index-es');
        
        $this->view->countries = $this->places->getPlaces(2);
        
        /*
        $countries = $this->places->getPlaces(2);
        $country   = $this->places->getPlaceById(18);
        $cities    = $this->places->getPlaces(3, $country->id);
        
        $this->view->countries = $countries;
        $this->view->cities    = $cities;
        $this->view->country   = $country;
        */
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
        $cat    = $this->_getParam('cat', 'all');
        $subcat = $this->_getParam('subcat', 'all');
        $sort   = $this->_getParam('sort', 'newest');
        $stars  = $this->_getParam('stars', 'all');
        
        $countries   = $this->places->getPlaces(2);
        
        $country_idf = $this->getRequest()->getParam('country');
        $city_idf    = $this->getRequest()->getParam('city');
        $country     = $this->places->getPlaceByIdf($country_idf);
        $region      = $this->places->getPlaceById($country->parent_id);
        $city        = $this->places->getPlaceByIdf($city_idf, 3, $country->id);
        
        $categories  = $this->listings->getMainCategories(true);
        foreach($categories as $c => $a){
            $s = strtolower(str_replace(' ','-',$a['name']));
            if($s != 'all')
                $subcats[$s] = $this->listings->getSubCategoriesOf($c);
        }
        
        $sortOptions = array(
            'newest' => 'Newest',
            'popular' => 'Most Popular',
            'name' => 'Name',
            'free' => 'Free',
            'lowest' => 'Lowest Price',
            'highest' => 'Highest Price',
            'rating' => 'Rating' 
        );
        
        if($cat != 'all'){
            $category = $this->listings->getCategoryByIdf($cat);
            $subcategories = $this->listings->getSubCategoriesOf($category->id);
        }
        
        $ls_count = $this->listings->countListings($city->id);
        
        $listings = $this->listings->getListings2($city->id, $cat, $subcat, $sort, $stars);
        
        if($this->user)
            $favorites = $this->user->getFavotites();
        
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
        
        $this->view->favorites = $favorites;
        
        $this->view->subcats = $subcats;
        
        if($this->user){
            $trips = $this->trips->getFutureTripsBy($this->user->getId(), true);
            $this->view->trips = $trips; 
        }
        
        
    }
    
    public function searchAction()
    {
        $q = $_GET['q'];
        
        $cat    = $this->_getParam('cat', 'activities');
        $subcat = $this->_getParam('subcat', 'all');
        $sort   = $this->_getParam('sort', 'newest');
        $stars  = $this->_getParam('stars', 'all');
        
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
    
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('listings');
        $select->where("listings.lat BETWEEN {$south} and {$north}");
        $select->where("listings.lng BETWEEN {$west} and {$east}");
        //$select->where('main_type = 4');
        $select->join('vendors','listings.vendor_id = vendors.id',array('vendor_name'=>'name'));
        $select->join('places','listings.city_id = places.id',array('city_idf'=>'title'));
        $select->join(array('places2'=>'places'),'listings.country_id = places2.id',array('country_idf'=>'title'));
        
        $bounds = array($north, $south, $east, $west);
        
        $result = $db->fetchAll($select, array(), 5);
        
        $categories  = $this->listings->getMainCategories(true);
        if($cat != 'all'){
            $category = $this->listings->getCategoryByIdf($cat);
            $subcategories = $this->listings->getSubCategoriesOf($category->id);
        }
        
        $this->view->listings    = $result;
        $this->view->class       = 'landscape-1';
        $this->view->cat         = $cat;
        $this->view->subcat      = $subcat;
        $this->view->sort        = $sort;
        $this->view->stars       = $stars;
        $this->view->categories  = $categories;
        
        
        
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
        
        $tabs        = $this->listings->getTabsOf($listing->id);
        $faqs        = $this->listings->getFAQsOf($listing->id);
        $vendor      = $this->vendors->getVendorById($listing->vendor_id);
        
        $attributes  = $this->listings->getAttributesOf($listing->id);
        
        $pictures    = $this->listings->getPictures($listing->id);
        
        if($this->user)
            $trips = $this->trips->getFutureTripsBy($this->user->getId());
        
        switch($listing->main_type){
            case self::ACTIVITY: 
                $template = 'listing-activity';      
                $options = $this->listings->getSchedulesOf($listing->id);
                $this->view->options = $options;
                break;
            case self::ENTERTAIMENT  : $template = 'listing-entertaiment';  break;
            case self::HOTEL: 
                $template = 'listing-hotel';         
                $options = $this->listings->getSchedulesOf($listing->id);
                $this->view->options = $options;
                break;
            case self::RESTAURANT    : $template = 'listing-restaurant';    break;
            case self::TOURIST_SIGHT : $template = 'listing-touristsight'; break;
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
        
        $questions = $this->questions->getQuestionsOn($listing->id);
        if(count($questions) > 0)
            $this->view->questions = $questions;
        
        $this->view->answer_allow = false;
        
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
        $this->view->tabs            = $tabs;
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