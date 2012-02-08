<?php

class WS_TripsService {
    
    const SEARCH_LIMIT = 5;
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $trips_db;
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $DB;
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $listings;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $landscapes;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $categories;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $itineraries;
    
    public function __construct() {
        $this->trips_db = new Model_Trips();
        $this->DB       = Zend_Db_Table::getDefaultAdapter();
        $this->listings = new Model_TripListings();
        
        $this->categories = new Zend_Db_Table('tripcategories');
        $this->landscapes = new Zend_Db_Table('landscapes');
        
        $this->itineraries = new Zend_Db_Table('itineraries');
    }
    
    public function getPopularTrips(){
        return $this->_getTrips('trips.views DESC', 'trips.public = 1', 3);
    }
    
    public function getNewestTrips(){
        return $this->_getTrips('trips.created DESC', 'trips.public = 1', 3);
    }
    
    public function getNearestTrips($country){
        return $this->_getTrips(null, 'trips.public = 1 and trips.country_id = '.$country, 3);
    }
    
    private function _getTrips($order = null, $where = null, $limit = null)
    {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('users','trips.user_id = users.id', array(
            'user'      => 'name',
            'username'  => 'username'
        ));
        $select->join('places', 'trips.country_id = places.id', array(
           'country'    => 'title'
        ));
        if(!is_null($where))
            $select->where ($where);
        if(!is_null($order))
            $select->order ($order);
        if(!is_null($limit))
            $select->limit ($limit);
        
        $result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        if(count($result) > 0)
            return $result;
        
        return array();
    }
    
    
    public function getFutureTripsBy($user, $simple = false)
    {
        if(!$simple){
            $select = $this->itineraries->select();
            $select->where('user_id = ?', $user);
            $select->where('status = 1');

            $trips = $this->itineraries->fetchAll($select);
            return $trips;
        } else {
            $select = $this->DB->select();
            $select->from('itineraries', array('id','title'));
            $select->where('user_id = ?', $user);
            $select->where('status = 1');
            
            $trips = $this->DB->fetchAll($select, array(), 5);
            return $trips;
        }
    }
    
    public function getPastTripsBy($user, $simple = false)
    {
        if(!$simple){
            $select = $this->itineraries->select();
            $select->where("user_id = {$user} and (status = 2 or status = 3)");
            $trips = $this->itineraries->fetchAll($select);
            return $trips;
        } else {
            $select = $this->DB->select();
            $select->from('itineraries', array('id','title'));
            $select->where("user_id = {$user} and (status = 2 or status = 3)");            
            $trips = $this->DB->fetchAll($select, array(), 5);
            return $trips;
        }
    }
    
    public function get($trip, $arr = false)
    {
        if(!$arr)
        {
            $select = $this->DB->select();
            $select->from('trips');
            $select->join('places', 'trips.country_id = places.id', array('country'=>'title'));
            $select->where('trips.id = ?', $trip);
            $result = $this->DB->fetchRow($select, array(), 5);
            if(is_null($result))
                throw new Exception();
        } 
        else 
        {
            $select = $this->DB->select();
            $select->from('trips');
            $select->where('trips.id = ?', $trip);
            $result = $this->DB->fetchRow($select);
            if(is_null($result))
                throw new Exception();
        }
        return $result;
    }
    
    public function getItn($trip, $obj = false, $arr = false)
    {
        if($obj){
            $select = $this->itineraries->select();
            $select->where('id = ?', $trip);
            $result = $this->itineraries->fetchRow($select);
            if(is_null($result))
                throw new Exception();
            if(!$arr)
                return $result; 
            return $result->toArray();
        } 
        else {
            $select = $this->DB->select();
            $select->from('itineraries');
            $select->join('places', 'itineraries.country_id = places.id', array('country'=>'title'));
            $select->where('itineraries.id = ?', $trip);
            $result = $this->DB->fetchRow($select, array(), 5);
            if(is_null($result))
                throw new Exception();

            return $result;
        }
    }
    
    public function deleteItn($trip)
    {
        $this->DB->delete("itineraries","id = {$trip}");
        $this->DB->delete('itinerary_listings',"itinerary_id = {$trip}");
    }
    
    
    
    public function getListingOf($trip, $full = true)
    {
        $select = $this->DB->select();
        $select->from('trip_listings', array(
            'triplisting_id'=>'id',
            'itinerary_id',
            'listing_id',
            'day',
            'time',
            'city_id',
            'country_id',
            'start',
            'end',
            'sort',
        ));
        $select->join('listings', 'trip_listings.listing_id = listings.id');
        $select->join('vendors', 'listings.vendor_id = vendors.id', array(
            'vendor' => 'name'
        ));
        $select->join('places', 'listings.city_id = places.id',array(
            'city' => 'title',
            'cityurl'=>'identifier'
        ));
        $select->join(array('places2'=>'places'), 'listings.country_id = places2.id',array(
            'country' => 'title',
            'countryurl'=>'identifier'
        ));
        $select->join(array('tabs'=>'listing_tabs'), 'tabs.listing_id = listings.id', array(
            'overview' => 'text'
        ));
        $select->where("trip_listings.itinerary_id = {$trip}");
        $select->where("tabs.title = 'Overview'");
        $select->order('trip_listings.time ASC');
        
        $result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        
        if($full){
            $labels = array('','Morning','Afternoon', 'Night');
            $times = array();
            $days  = array();
            $results = array();
            foreach($result as $day){
              if(!in_array($day->day, $days)){
                $days[] = $day->day;
                $times[$day->day] = array();
                foreach($result as $time){
                  if($time->day == $day->day){
                    if(!in_array($time->time, $times[$day->day])){
                      $times[$day->day][] = $time->time;
                      $results[$day->day][$labels[$time->time]] = array();
                      foreach($result as $listing){
                        if(($listing->day == $day->day) and ($listing->time == $time->time)){
                          $results[$day->day][$labels[$time->time]][] = $listing;
            }}}}}}}

            //print_r($results); die;

            return $results;
        } else {
            return $result;
        }
    }
    
    public function getListingOf2($trip)
    {
        $select = $this->DB->select();
        $select->from('trip_listings');
        $select->where("itinerary_id = ?", $trip);
        $select->order('time ASC');
        $result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        
        return $result;
    }
    
    
    public function getItnListingOf($trip, $full = true, $exclude = false, $id = false, $checkout = false)
    {
        $select = $this->DB->select();
        if(!$checkout)
            $select->from('itinerary_listings');
        else {
            $select->from('itinerary_listings', array(
                'triplisting_id'=>'id',
                'itinerary_id',
                'listing_id',
                'day',
                'time',
                'city_id',
                'country_id',
                'start',
                'end',
                'sort',
            ));
        }
        
        if(!$id){
            $select->join('listings', 'itinerary_listings.listing_id = listings.id', array(
                'listing_id'=>'id',
                'vendor_id','main_type',
                'title','description','identifier',
                'price','image','created','token'
            ));
        } else {
            $select->join('listings', 'itinerary_listings.listing_id = listings.id');
        }
        $select->join('vendors', 'listings.vendor_id = vendors.id', array(
            'vendor' => 'name'
        ));
        $select->join('places', 'listings.city_id = places.id',array(
            'city' => 'title',
            'cityurl'=>'identifier'
        ));
        $select->join(array('places2'=>'places'), 'listings.country_id = places2.id',array(
            'country' => 'title',
            'countryurl'=>'identifier'
        ));
        $select->join(array('tabs'=>'listing_tabs'), 'tabs.listing_id = listings.id', array(
            'overview' => 'text'
        ));
        $select->where("itinerary_listings.itinerary_id = {$trip}");
        $select->where("tabs.title = 'Overview'");
        $select->order('itinerary_listings.time ASC');
        
        if($exclude == 'notnull')
        {
            $select->where('itinerary_listings.day is null');
        } 
        elseif($exclude == 'null')
        {
            $select->where('itinerary_listings.day is not null');
        }
        
        //echo $select->assemble(); die;
        
        $result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        
        if($full){
            $_trip = $this->getItn($trip, true);
            $results = array();
            $labels = array('','Morning','Afternoon', 'Night', 'stay');
            for($i=1;$i<=$_trip->days;$i++){
                $results[$i] = array();
                foreach($labels as $t){
                    if($t!='')
                        $results[$i][$t] = array();
                }
            }
            
            $times = array();
            $days  = array();
            
            foreach($result as $day){
              if(!in_array($day->day, $days)){
                $days[] = $day->day;
                $times[$day->day] = array();
                foreach($result as $time){
                  if($time->day == $day->day){
                    if(!in_array($time->time, $times[$day->day])){
                      $times[$day->day][] = $time->time;
                      //$results[$day->day][$labels[$time->time]] = array();
                      foreach($result as $listing){
                        if(($listing->day == $day->day) and ($listing->time == $time->time)){
                          $results[$day->day][$labels[$time->time]][] = $listing;
            }}}}}}}

            //print_r($results); die;

            return $results;
        } else {
            return $result;
        }
    }
    
    public function deleteNullListings($trip)
    {
        $this->DB->delete('itinerary_listings', "day is null and itinerary_id = {$trip}");
    }
    
    public function copyTo($_trip, $_user)
    {
        $trip = $this->get($_trip);
        if($trip->user_id == $_user)
                throw new Exception();
        if($trip->status != 3)
                throw new Exception();

        $arr  = $trip->toArray();
        unset($arr['id']);
        
        $arr['user_id'] = $_user;
        $arr['status']  = 1;
        
        $id = $this->trips_db->insert($arr);
        
        $listings = $this->listings->fetchAll('itinerary_id = '.$_trip);
        $arr2 = $listings->toArray();
        foreach($arr2 as $list){
            unset($list['id']);
            $list['itinerary_id'] = $id;
            $this->listings->insert($list);
        }
        
        $userstats = new Model_UserStats();
        $stats = $userstats->fetchRow('user_id = '.$_user);
        if(is_null($stats)){
            $stats = $userstats->fetchNew();
            $stats->user_id = $_user;
            $stats->save();
        }
        $stats->itineraries = $stats->itineraries + 1;
        $stats->save();
    }
    
    public function delete($user,$trip)
    {
        $trip = $this->get($trip);
        if($trip->user_id != $user)
                throw new Exception();
        
        $this->listings->delete('itinerary_id = '.$trip->id);
        $trip->delete();
    }
    
    public function create($data)
    {
        $id = $this->trips_db->insert($data);
        return $id;
    }
    
    public function getFavoritesOf($user)
    {
        $select = $this->DB->select();
        $select->from('favorites');
        $select->join('listings','favorites.listing_id = listings.id');
        $select->where('favorites.user_id = ?', $user);
        
        $favorites = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        if(!count($favorites) > 0)
            return array();
        
        $ids = array(2=>4,4=>3,5=>5,6=>1,7=>2); $used = array(); $result = array();
        foreach($favorites as $listing){
            if(!in_array($listing->main_type, $used)){
                $used[] = $listing->main_type;
                $id     = $ids[$listing->main_type];
                $result[$id] = array();
                foreach($favorites as $list){
                    if($list->main_type == $listing->main_type){
                        $result[$id][] = $list;
        }}}}
        
        return $result;        
    }
    
    public function getCategories()
    {
        $cats = $this->categories->fetchAll();
        return $cats;
    }
    
    public function getLandscapes()
    {
        $lands = $this->landscapes->fetchAll();
        return $lands;
    }
    
    public function getAll()
    {
        $trips = $this->trips_db->fetchAll();
        return $trips;
    }
    
    public function getFeatured()
    {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('places', 'trips.country_id = places.id', array('country'=>'title'));
        $select->where('trips.featured = ?',1);
        $select->limit(10);
        
        $trips = $this->DB->fetchAll($select, array(), 5);
        //var_dump($trips); die;
        return $trips;
    }
    
    public function getLatest()
    {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('places', 'trips.country_id = places.id', array('country'=>'title'));
        $select->order('created DESC');
        $select->limit(10);
        
        $trips = $this->DB->fetchAll($select, array(), 5);
        //var_dump($trips); die;
        return $trips;
    }
    
    public function getNearest($country)
    {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('places', 'trips.country_id = places.id', array('country'=>'title'));
        $select->where('trips.country_id = ?',$country);
        $select->limit(10);
        
        $trips = $this->DB->fetchAll($select, array(), 5);
        //var_dump($trips); die;
        return $trips;
    }
    
    public function getTripsOf($country, $price=array(), $days = array(), $people = 0, $page = 1)
    {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('places', 'trips.country_id = places.id', array('country'=>'title'));
        $select->join(array('places2'=>'places'), 'trips.start_city = places2.id',array('start_city_name'=>'title'));
        $select->join(array('places3'=>'places'), 'trips.end_city = places3.id',array('end_city_name'=>'title'));
        $select->where('trips.country_id = ?',$country);
        if(isset($price['max']) && isset($price['min'])){
            $select->where('trips.price >= ?', $price['min']);
            $select->where('trips.price <= ?', $price['max']); }
        if(isset($days['min']) && isset($days['max'])){
            $select->where('trips.days >= ?', $days['min']);
            $select->where('trips.days <= ?', $days['max']); }
        if($people > 0){
            $select->where('trips.min <= ?', $people);
            $select->where('trips.max >= ?', $people); }
        
        $select->order('trips.created DESC');
        $select->limit(5);
        $trips = $this->DB->fetchAll($select, array(), 5);
        //var_dump($trips); die;
        return $trips;
    }
    
    public function countListings($trip){        
        $result = array();
        $listings = new WS_ListingService();
        $categories = $listings->getMainCategories();
        foreach($categories as $cat){
            $select = $this->DB->select();
            $select->from('itinerary_listings');
            $select->join('listings','itinerary_listings.listing_id = listings.id',array('main_type'));
            $select->where('itinerary_listings.itinerary_id = ?', $trip);
            $select = $select;
            $select->where('listings.main_type = ?', $cat->id);
            $lists = $this->DB->fetchAll($select);
            $result[$cat->id] = count($lists);
        }
        
        //echo $select->assemble(); die;
        
        return $result;        
    }
    
    
    public function getHighlights($trip)
    {
        $trip = 23;
        $table = new Zend_Db_Table('trip_highlights');
        $select = $table->select();
        $select->where('trip_id = ?', $trip);
        $select->limit(3);
        $result = $table->fetchAll($select);
        
        return $result;
    }
    
    public function getFacts($trip)
    {
        $trip = 23;
        $table = new Zend_Db_Table('trip_facts');
        $select = $table->select();
        $select->where('trip_id = ?', $trip);
        $result = $table->fetchAll($select);
        
        return $result;
    }
    
    public function getIncludes($trip)
    {
        $trip = 23;
        $table = new Zend_Db_Table('trip_includes');
        $select = $table->select();
        $select->where('trip_id = ?', $trip);
        $result = $table->fetchAll($select);
        
        return $result;
    }
}