<?php

class TripsController extends Zend_Controller_Action {
 
    /**
     *
     * @var WS_JsonService
     */
    protected $json;
    
    /**
     *
     * @var WS_TripsService
     */
    protected $trips;
    
    /**
     *
     * @var WS_User
     */
    protected $user;
    
    /**
     *
     * @var WS_UsersService
     */
    protected $users;
    
    /**
     *
     * @var WS_PlacesService
     */
    protected $places;
    
    /**
     *
     * @var WS_ListingService
     */
    protected $listings;
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $this->user = new WS_User($auth->getIdentity());
            $this->view->user = $this->user->getData(true);
        }
        
        $this->json = new WS_JsonService();
        
        $this->view->json_categories = $this->json->getTags();
        $this->view->json_countries  = $this->json->getCountries();
        
        $this->trips = new WS_TripsService();
        $this->listings = new WS_ListingService();
        $this->users = new WS_UsersService();
        $this->places = new WS_PlacesService();
        $this->view->tags = $this->json->getSerchTerms();
        
        $this->view->cssVC = Zend_Registry::get('vc');
    }
    
    public function indexAction()
    {
        $this->view->countries = $this->json->getCountryTerms();
        
        $this->view->categories = $this->trips->getCategories();
        $this->view->landscapes = $this->trips->getLandscapes();
        $this->view->regions    = $this->places->getPlaces(1);
        
        $this->view->recommended = $this->trips->getFeatured();
        $this->view->latest      = $this->trips->getLatest();
        $this->view->nearest     = $this->trips->getNearest(18);
    }
    
    public function searchAction()
    {
        /**
        $_trips = new Model_Trips();        
        $trips = $_trips->fetchAll();
        foreach($trips as $trip)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from('trip_listings');
            $select->join('listings','trip_listings.listing_id = listings.id', array('image'));
            $select->where('itinerary_id = ?', $trip->id);
            
            $image = $db->fetchRow($select,array(), Zend_Db::FETCH_OBJ);
            
            $trip->image = $image->image;
            $trip->save();
        }
        
        die;
         * 
         */
        $this->view->countries = $this->json->getCountryTerms();
        $this->view->categories = $this->trips->getCategories();
        $this->view->landscapes = $this->trips->getLandscapes();
        $this->view->regions    = $this->places->getPlaces(1);
        
        $idf = $this->_getParam('country','costa_rica');
        $country = $this->places->getPlaceByIdf($idf);
        
        $trips = $this->trips->getTripsOf($country->id);
        
        $this->view->country = $country;
        $this->view->trips   = $trips;
    }
    
    public function countryAction()
    {
        $this->view->countries  = $this->json->getCountryTerms();
        $this->view->categories = $this->trips->getCategories();
        $this->view->landscapes = $this->trips->getLandscapes();
        $this->view->regions    = $this->places->getPlaces(1);
        
        $idf = $this->_getParam('country');
        $country = $this->places->getPlaceByIdf($idf);
        
        $trips_count = $this->trips->countTrips($country->id);
        $this->view->trips_count = $trips_count;
        
        $this->view->country = $country;
    }
    
    public function futureAction()
    {
        $trips = $this->trips->getFutureTripsBy($this->user->getId());
        $this->view->trips = $trips;
    }
    
    public function pastAction()
    {
        $trips = $this->trips->getPastTripsBy($this->user->getId());
        $this->view->trips = $trips;
    }
    
    public function viewAction()
    {        
        $trip            = $this->_getTrip();
        $trip_highlights = $this->trips->getHighlights($trip->id);
        $trip_facts      = $this->trips->getFacts($trip->id);
        $trip_includes   = $this->trips->getIncludes($trip->id); 
        $cities          = $this->trips->getCities($trip->id);
        $info            = $this->trips->getInfo($trip->id);
        $pictures        = $this->trips->getPictures($trip->id);
        
        $this->view->trip       = $trip;
        $this->view->highlights = $trip_highlights;
        $this->view->facts      = $trip_facts;
        $this->view->includes   = $trip_includes;
        $this->view->cities     = $cities;        
        $this->view->info       = $info;
        $this->view->pictures   = $pictures;

        $listings = $this->trips->getListingOf3($trip->id, false);
        $this->view->listings = $listings;
    }
    
    public function itineraryAction()
    {        
        $trip = $this->_getTrip();
        $this->view->trip = $trip;

        $listings = $this->trips->getListingOf3($trip->id, false);
        $this->view->listings = $listings;
        
        $labels  = array('Stay','Morning','Afternoon', 'Night','Stay');
        $times   = array();
        $days    = array();
        $results = array();
        $stay    = array();
        foreach($listings as $day){
          if(!in_array($day->day, $days)){
            $days[] = $day->day;
            $times[$day->day] = array();
            foreach($listings as $time){
              if($time->day == $day->day){
                if(!in_array($time->time, $times[$day->day])){
                  if(is_null($time->time)) {
                    $start = explode(':', $time->start); $start = $start[0];
                    if($start >= 05 and $start <= 11) $time->time = 1;
                    if($start >= 12 and $start <= 18) $time->time = 2;
                    if($start >= 18 and $start <= 04) $time->time = 3;
                  }
                  $times[$day->day][] = $time->time;
                  $results[$day->day][$labels[$time->time]] = array();
                  foreach($listings as $listing){
                    if(is_null($time->time)) {
                      $start = explode(':', $listing->start); $start = $start[0];
                      if($start >= 05 and $start <= 11) $listing->time = 1;
                      if($start >= 12 and $start <= 18) $listing->time = 2;
                      if($start >= 18 and $start <= 04) $listing->time = 3;
                    }
                    if(($listing->day == $day->day) and ($listing->time == $time->time)){
                      $results[$day->day][$labels[$time->time]][] = $listing;
        }}}}}}}
        
        $this->view->stay = $stay;
        
        $this->view->listingsbyday = $results;
        
        //var_dump($results); die;
    }
    
    public function newAction()
    {
        if($this->getRequest()->isPost())
        {
            if($_POST['userids'] != $this->user->getId())
                    throw new Exception('Corrupted From: userids');
            if($_POST['usertoken'] != $this->user->getToken())
                    throw new Exception('Corrupted From: user token');
            if($_POST['_task'] != md5('create_trip'))
                    throw new Exception('Corrupted From: task');
            if($_POST['formid'] != md5($this->user->getToken().'create_trip'))
                    throw new Exception('Corrupted From: formid');
            
            $fday = strtotime($_POST['start']);
            $lday = strtotime($_POST['end']);
            
            $diff = $lday - $fday;
            $days = $diff / 86400;
            $days++;
            
            $data = array(
                'title'=> $_POST['title'],
                'description'=> $_POST['description'],
                'user_id'=>$this->user->getId(),
                'country_id'=>0,
                'start'=>date('Y-m-d', strtotime($_POST['start'])),
                'end'=>date('Y-m-d', strtotime($_POST['end'])),
                'created'=>date('Y-m-d g:i:s'),
                'updated'=>date('Y-m-d g:i:s'),
                'token'=>md5($_POST['title'].time()),
                'days'=>$days,
            );
            
            $id = $this->trips->create($data);
            
            $this->_redirect('trips/edit/'.$id);
        }
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
    
    public function addAction()
    {
        $id = $this->_getId();
        $this->trips->copyTo($id, $this->user->getId());        
        $this->_redirect('trips/future');
    }
    
    private function _getId($var = 'task')
    {
        $id = $this->_getParam('task');
        if(!$this->_isValidId($id))
                $this->_redirect('trips');
        
        return $id;
    }
    
    private function _getTrip($var = 'task', $user = false)
    {
        $id = $this->_getId($var);
        $trip = $this->trips->get($id);
        if($user){
            if($trip->user_id != $this->user->getId()){
                throw new Exception();
        }}
        return $trip;
    }
    
    public function deleteAction()
    {
        $id = $this->_getId();
        $this->trips->delete($this->user->getId(),$id);
        $this->_redirect('trips/future');
    }
    
    public function editAction()
    {
        $trip = $this->_getTrip('task', true);
        $this->view->trip = $trip;
        
        $favorites = $this->trips->getFavoritesOf($this->user->getId());
        //var_dump($favorites); die;
        $this->view->favorites = $favorites;
        
        $_listings = $this->trips->getListingOf($trip->id, false);
        
        $listings = array();
        $class = array(
            2=>array('class'=>'cat-restaurant','color'=>'#f5911c'),
            4=>array('class'=>'cat-sight','color'=>'#7bc9c5'),
            5=>array('class'=>'cat-hotel','color'=>'#f37a89'),
            6=>array('class'=>'cat-activity','color'=>'#8dc63f'),
            7=>array('class'=>'cat-entertainment','color'=>'#a865a8')
        );
        foreach($_listings as $listing)
        {
            $listings[] = array(
                'title' => $listing->title,
                'className'=> $class[$listing->main_type]['class'],
                'color'=>$class[$listing->main_type]['color'],
                'start'=>date('r',strtotime($listing->start)),
                'listing'=>$listing->id,
                'city'=>$listing->city_id,
                'country'=>$listing->country_id,
                'itemid'=>$listing->id,
                'allDay' =>0,

            );
        }
        
        $listings = json_encode($listings);
        $this->view->listings = $listings;
    }
    
    public function checkpriceAction()
    {
        if($this->getRequest()->isPost()){            
            $ids = $_POST['ids'];
            $id  = $this->_getParam('id');
            if($ids != $id)
                    throw new Exception();
            
            $trip = $this->trips->get($id);            
            if($trip->token != $_POST['token'])
                    throw new Exception();
            
            $listings = $this->trips->getListingOf($trip->id, false);
            
            $date = date('Y-m-d H:i:s', strtotime($_POST['date']));
            $adults = $_POST['adults'];
            $kids   = $_POST['kids'];
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
            
            $country = $this->places->getPlaceById($trip->country_id);
            $this->country = $country;
        }
        else {
            $id  = $this->_getParam('id');
            $trip = $this->trips->get($id);
            
            $this->view->trip = $trip;
        }
        //var_dump($_POST); die;
    }
}