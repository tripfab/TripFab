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
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $purchases;

    public function __construct() {
        $this->trips_db = new Model_Trips();
        $this->DB = Zend_Db_Table::getDefaultAdapter();
        $this->listings = new Model_TripListings();

        $this->categories = new Zend_Db_Table('trip_categories');
        $this->landscapes = new Zend_Db_Table('landscapes');

        $this->itineraries = new Zend_Db_Table('itineraries');

        $this->purchases = new Zend_Db_Table('trip_purchases');
    }

    public function getPopularTrips() {
        return $this->_getTrips('trips.views DESC', 'trips.public = 1', 3);
    }

    public function getNewestTrips() {
        return $this->_getTrips('trips.created DESC', 'trips.public = 1', 3);
    }

    public function getNearestTrips($country) {
        return $this->_getTrips(null, 'trips.public = 1 and trips.country_id = ' . $country, 3);
    }

    private function _getTrips($order = null, $where = null, $limit = null) {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('users', 'trips.user_id = users.id', array(
            'user' => 'name',
            'username' => 'username'
        ));
        $select->join('places', 'trips.country_id = places.id', array(
            'country' => 'title'
        ));
        if (!is_null($where))
            $select->where($where);
        if (!is_null($order))
            $select->order($order);
        if (!is_null($limit))
            $select->limit($limit);

        $result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        if (count($result) > 0)
            return $result;

        return array();
    }

    public function getFutureTripsBy($user, $simple = false) {
        if (!$simple) {
            $select = $this->itineraries->select();
            $select->where('user_id = ?', $user);
            $select->where('status = 1');

            $trips = $this->itineraries->fetchAll($select);
            return $trips;
        } else {
            $select = $this->DB->select();
            $select->from('itineraries', array('id', 'title'));
            $select->where('user_id = ?', $user);
            $select->where('status = 1');

            $trips = $this->DB->fetchAll($select, array(), 5);
            return $trips;
        }
    }

    public function getPastTripsBy($user, $simple = false) {
        if (!$simple) {
            $select = $this->itineraries->select();
            $select->where("user_id = {$user} and (status = 2 or status = 3)");
            $trips = $this->itineraries->fetchAll($select);
            return $trips;
        } else {
            $select = $this->DB->select();
            $select->from('itineraries', array('id', 'title'));
            $select->where("user_id = {$user} and (status = 2 or status = 3)");
            $trips = $this->DB->fetchAll($select, array(), 5);
            return $trips;
        }
    }

    public function get($trip, $arr = false, $user = null) {
        if (!$arr) {
            $select = $this->DB->select();
            $select->from('trips');
            $select->join('places', 'trips.country_id = places.id', array('country' => 'title'));
            $select->where('trips.id = ?', $trip);
            $result = $this->DB->fetchRow($select, array(), 5);
            if (is_null($result))
                throw new Exception();
        }
        else {
            $select = $this->DB->select();
            $select->from('trips');
            $select->where('trips.id = ?', $trip);
            $result = $this->DB->fetchRow($select);
            if (is_null($result))
                throw new Exception();
        }
        return $result;
    }

    public function getItn($trip, $obj = false, $arr = false) {
        if ($obj) {
            $select = $this->itineraries->select();
            $select->where('id = ?', $trip);
            $result = $this->itineraries->fetchRow($select);
            if (is_null($result))
                throw new Exception();
            if (!$arr)
                return $result;
            return $result->toArray();
        }
        else {
            $select = $this->DB->select();
            $select->from('itineraries');
            $select->join('places', 'itineraries.country_id = places.id', array('country' => 'title'));
            $select->where('itineraries.id = ?', $trip);
            $result = $this->DB->fetchRow($select, array(), 5);
            if (is_null($result))
                throw new Exception();

            return $result;
        }
    }

    public function deleteItn($trip) {
        $this->DB->delete("itineraries", "id = {$trip}");
        $this->DB->delete('itinerary_listings', "itinerary_id = {$trip}");
    }

    public function getListingOf($trip, $full = true) {
        $select = $this->DB->select();
        $select->from('trip_listings', array(
            'triplisting_id' => 'id',
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
        $select->join('places', 'listings.city_id = places.id', array(
            'city' => 'title',
            'cityurl' => 'identifier'
        ));
        $select->join(array('places2' => 'places'), 'listings.country_id = places2.id', array(
            'country' => 'title',
            'countryurl' => 'identifier'
        ));
        $select->join(array('tabs' => 'listing_tabs'), 'tabs.listing_id = listings.id', array(
            'overview' => 'text'
        ));
        $select->where("trip_listings.itinerary_id = {$trip}");
        $select->where("tabs.title = 'Overview'");
        $select->order('trip_listings.time ASC');

        $result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);

        if ($full) {
            $labels = array('', 'Morning', 'Afternoon', 'Night');
            $times = array();
            $days = array();
            $results = array();
            foreach ($result as $day) {
                if (!in_array($day->day, $days)) {
                    $days[] = $day->day;
                    $times[$day->day] = array();
                    foreach ($result as $time) {
                        if ($time->day == $day->day) {
                            if (!in_array($time->time, $times[$day->day])) {
                                $times[$day->day][] = $time->time;
                                $results[$day->day][$labels[$time->time]] = array();
                                foreach ($result as $listing) {
                                    if (($listing->day == $day->day) and ($listing->time == $time->time)) {
                                        $results[$day->day][$labels[$time->time]][] = $listing;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //print_r($results); die;

            return $results;
        } else {
            return $result;
        }
    }

    public function getListingOf2($trip) {
        $select = $this->DB->select();
        $select->from('trip_listings');
        $select->where("itinerary_id = ?", $trip);
        $select->order('time ASC');
        $result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);

        return $result;
    }
	
	public function getListingOf3($trip) {
        $select = $this->DB->select();
        $select->from('trip_listings')->
        joinleft('places', 'trip_listings.city_id = places.id', array('city_id'=>'id', 'city' => 'title', 'cityurl' => 'identifier' )) ->
        joinleft(array('places2' => 'places'), 'trip_listings.country_id = places2.id', array('country_id'=>'id', 'country' => 'title','countryurl' => 'identifier' ))->
        joinleft('listing_types', 'trip_listings.main_type = listing_types.id', array('type_name'=>'name')) ->
		where("trip_listings.itinerary_id = ?", $trip)->
		order('trip_listings.day ASC')->
		order('trip_listings.sort ASC');
		$result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        return $result;
	}
	

    public function getItnListingOf($trip, $full = true, $exclude = false, $id = false, $checkout = false) {
        $select = $this->DB->select();
        if (!$checkout)
            $select->from('itinerary_listings');
        else {
            $select->from('itinerary_listings', array(
                'triplisting_id' => 'id',
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

        if (!$id) {
            $select->join('listings', 'itinerary_listings.listing_id = listings.id', array(
                'listing_id' => 'id',
                'vendor_id', 'main_type',
                'title', 'description', 'identifier',
                'price', 'image', 'created', 'token'
            ));
        } else {
            $select->join('listings', 'itinerary_listings.listing_id = listings.id');
        }
        $select->join('vendors', 'listings.vendor_id = vendors.id', array(
            'vendor' => 'name'
        ));
        $select->join('places', 'listings.city_id = places.id', array(
            'city' => 'title',
            'cityurl' => 'identifier'
        ));
        $select->join(array('places2' => 'places'), 'listings.country_id = places2.id', array(
            'country' => 'title',
            'countryurl' => 'identifier'
        ));
        $select->where("itinerary_listings.itinerary_id = {$trip}");
        $select->order('itinerary_listings.day ASC');

        if ($exclude == 'notnull') {
            $select->where('itinerary_listings.day is null');
        } elseif ($exclude == 'null') {
            $select->where('itinerary_listings.day is not null');
        }

        //echo $select->assemble(); die;

        $result = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);

        if ($full) {
            $_trip = $this->getItn($trip, true);
            $results = array();
            $labels = array('', 'Morning', 'Afternoon', 'Night', 'stay');
            for ($i = 1; $i <= $_trip->days; $i++) {
                $results[$i] = array();
                foreach ($labels as $t) {
                    if ($t != '')
                        $results[$i][$t] = array();
                }
            }

            $times = array();
            $days = array();

            foreach ($result as $day) {
                if (!in_array($day->day, $days)) {
                    $days[] = $day->day;
                    $times[$day->day] = array();
                    foreach ($result as $time) {
                        if ($time->day == $day->day) {
                            if (!in_array($time->time, $times[$day->day])) {
                                $times[$day->day][] = $time->time;
                                //$results[$day->day][$labels[$time->time]] = array();
                                foreach ($result as $listing) {
                                    if (($listing->day == $day->day) and ($listing->time == $time->time)) {
                                        $results[$day->day][$labels[$time->time]][] = $listing;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //print_r($results); die;

            return $results;
        } else {
            return $result;
        }
    }
    
    public function replaceListing($trip, $old, $new)
    {
        $listings = new Zend_Db_Table('itinerary_listings');
        $select = $listings->select();
        $select->where('listing_id = ?', $old);
        $select->where('itinerary_id = ?', $trip);
        $listing = $listings->fetchAll($select);
        foreach($listing as $l){
            $l->listing_id = $new;
            $l->save();
        }
    }

    public function deleteNullListings($trip) {
        $this->DB->delete('itinerary_listings', "day is null and itinerary_id = {$trip}");
    }

    public function copyTo($_trip, $_user) {
        $trip = $this->get($_trip);
        if ($trip->user_id == $_user)
            throw new Exception();
        if ($trip->status != 3)
            throw new Exception();

        $arr = $trip->toArray();
        unset($arr['id']);

        $arr['user_id'] = $_user;
        $arr['status'] = 1;

        $id = $this->trips_db->insert($arr);

        $listings = $this->listings->fetchAll('itinerary_id = ' . $_trip);
        $arr2 = $listings->toArray();
        foreach ($arr2 as $list) {
            unset($list['id']);
            $list['itinerary_id'] = $id;
            $this->listings->insert($list);
        }

        $userstats = new Model_UserStats();
        $stats = $userstats->fetchRow('user_id = ' . $_user);
        if (is_null($stats)) {
            $stats = $userstats->fetchNew();
            $stats->user_id = $_user;
            $stats->save();
        }
        $stats->itineraries = $stats->itineraries + 1;
        $stats->save();
    }

    public function delete($user, $trip) {
        $trip = $this->get($trip);
        if ($trip->user_id != $user)
            throw new Exception();

        $this->listings->delete('itinerary_id = ' . $trip->id);
        $trip->delete();
    }

    public function create($data) {
        $id = $this->trips_db->insert($data);
        return $id;
    }

    public function getFavoritesOf($user) {
        $select = $this->DB->select();
        $select->from('favorites');
        $select->join('listings', 'favorites.listing_id = listings.id');
        $select->where('favorites.user_id = ?', $user);

        $favorites = $this->DB->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        if (!count($favorites) > 0)
            return array();

        $ids = array(2 => 4, 4 => 3, 5 => 5, 6 => 1, 7 => 2);
        $used = array();
        $result = array();
        foreach ($favorites as $listing) {
            if (!in_array($listing->main_type, $used)) {
                $used[] = $listing->main_type;
                $id = $ids[$listing->main_type];
                $result[$id] = array();
                foreach ($favorites as $list) {
                    if ($list->main_type == $listing->main_type) {
                        $result[$id][] = $list;
                    }
                }
            }
        }

        return $result;
    }

    public function getCategories() {
        $cats = $this->categories->fetchAll();
        return $cats;
    }

    public function getLandscapes() {
        $lands = $this->landscapes->fetchAll();
        return $lands;
    }

    public function getAll() {
        $trips = $this->trips_db->fetchAll();
        return $trips;
    }

    public function getFeatured() {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('places', 'trips.country_id = places.id', array('country' => 'title'));
        $select->where('trips.featured = ?', 1);
        $select->limit(10);

        $trips = $this->DB->fetchAll($select, array(), 5);
        //var_dump($trips); die;
        return $trips;
    }

    public function getLatest() {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('places', 'trips.country_id = places.id', array('country' => 'title'));
        $select->order('created DESC');
        $select->limit(10);

        $trips = $this->DB->fetchAll($select, array(), 5);
        //var_dump($trips); die;
        return $trips;
    }

    public function getNearest($country) {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('places', 'trips.country_id = places.id', array('country' => 'title'));
        $select->where('trips.country_id = ?', $country);
        $select->limit(10);

        $trips = $this->DB->fetchAll($select, array(), 5);
        //var_dump($trips); die;
        return $trips;
    }

    public function getTripsOf($country, $price=array(), $days = array(), $people = 0, $category = "all", $page = 1) {
        $select = $this->DB->select();
        $select->from('trips');
        $select->join('places', 'trips.country_id = places.id', array('country' => 'title'));
        $select->join(array('places2' => 'places'), 'trips.start_city = places2.id', array('start_city_name' => 'title'));
        $select->join(array('places3' => 'places'), 'trips.end_city = places3.id', array('end_city_name' => 'title'));
        $select->join('trip_categories', 'trips.category_id = trip_categories.id', array('category' => 'name'));
        $select->where('trips.country_id = ?', $country);
        if ($category != 'all')
            $select->where('trips.category_id = ?', $category);
        if (isset($price['max']) && isset($price['min'])) {
            $select->where('trips.price >= ?', $price['min']);
            $select->where('trips.price <= ?', $price['max']);
        }
        if (isset($days['min']) && isset($days['max'])) {
            $select->where('trips.days >= ?', $days['min']);
            $select->where('trips.days <= ?', $days['max']);
        }
        if ($people > 0) {
            $select->where('trips.min <= ?', $people);
            $select->where('trips.max >= ?', $people);
        }

        $select->order('trips.created DESC');

        //echo $select->assemble(); die;
        //$select->limit(5);
        $trips = $this->DB->fetchAll($select, array(), 5);
        //var_dump($trips); die;
        return $trips;
    }

    public function countListings($trip) {
        $result = array();
        $listings = new WS_ListingService();
        $categories = $listings->getMainCategories();
        foreach ($categories as $cat) {
            $select = $this->DB->select();
            $select->from('itinerary_listings');
            $select->join('listings', 'itinerary_listings.listing_id = listings.id', array('main_type'));
            $select->where('itinerary_listings.itinerary_id = ?', $trip);
            $select = $select;
            $select->where('listings.main_type = ?', $cat->id);
            $lists = $this->DB->fetchAll($select);
            $result[$cat->id] = count($lists);
        }

        //echo $select->assemble(); die;

        return $result;
    }

    public function getTripsBy($user) {
        $select = $this->DB->select();
        $this->DB->setFetchMode(Zend_Db::FETCH_OBJ);
        $select->from('itineraries', array('*'));
        $select->where('user_id = ?', $user);
        $select->limit(15);
        $trips = $this->DB->fetchAll($select);
        return $trips;
    }

    public function getHighlights($trip) {
        $select = $this->DB->select();
        $this->DB->setFetchMode(Zend_Db::FETCH_OBJ);
        $select->from('trip_highlights', array('*'));
        $select->where('trip_id = ?', $trip);
        $trips = $this->DB->fetchAll($select);
        return $trips;
    }

    public function getTripById($id) {
        $select = $this->trips_db->select();
        $select->where('id = ?', $id);

        $trip = $this->trips_db->fetchRow($select);
        if (is_null($trip))
            return;

        return $trip;
    }

    public function saveHighlights($trip, $highlights) {
        $table = new Zend_Db_Table('trip_highlights');
        $table->delete('trip_id = ' . $trip);
        foreach ($highlights as $highlight) {
            $row = $table->fetchNew();
            $row->trip_id = $trip;
            $row->title = $highlight->title;
            $row->text = $highlight->text;
            $row->created = date("Y-m-d H:i:s");
            $row->updated = date("Y-m-d H:i:s");
            $row->save();
        }
    }

    public function getFacts($trip) {
        $select = $this->DB->select();
        $this->DB->setFetchMode(Zend_Db::FETCH_OBJ);
        $select->from('trip_facts', array('*'));
        $select->where('trip_id = ?', $trip);
        $facts = $this->DB->fetchAll($select);
        return $facts;
    }

    public function getFacts1($trip) {
        $select = $this->DB->select();
        $this->DB->setFetchMode(Zend_Db::FETCH_OBJ);
        $select->from('trip_facttypes', array('*'))->order('sequence asc');
        $factsTypes = $this->DB->fetchAll($select);
		
		
        $select1 = $this->DB->select();
        $this->DB->setFetchMode(Zend_Db::FETCH_OBJ);
        $select1->from('trip_facts', array('*'));
        $select1->where('trip_id = ?', $trip);
		$select1->order('id');
        $facts = $this->DB->fetchAll($select1);
		
		$factsDetails = array();
		foreach($factsTypes as $factType){
			$factsByType = array();
			foreach($facts as $fact){
				if($fact->type == $factType->id){
					$factsByType[] = $fact;
				}
			}
			$factDetails[] = array(
				'title'=>$factType->title, 
				'multi'=>$factType->multi, 
				'id'=>$factType->id, 
				'sequence'=>$factType->sequence, 
				'facts'=>$factsByType
			);
		}
		
		
        return $factDetails;
    }

    public function deleteFacts($trip) {
        $table = new Zend_Db_Table('trip_facts');
        $table->delete('trip_id = ' . $trip);
    }

    public function getIncludes($trip) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $db->query("SELECT * FROM trip_includes WHERE trip_id='$trip'");
        return $result->fetchAll();

        /* $select = $this->DB->select();
          $this->DB->setFetchMode(Zend_Db::FETCH_OBJ);
          $select->from('trip_includes', array('*'));
          $select->where('trip_id = ?', $trip);
          $includes = $this->DB->fetchAll($select);
          return $includes; */
    }

    public function deleteIncludes($trip) {
        $table = new Zend_Db_Table('trip_includes');
        $table->delete('trip_id = ' . $trip);
    }

    public function saveIncludes($trip, $includes, $type) {
        $table = new Zend_Db_Table('trip_includes');
        foreach ($includes as $include) {
            $row = $table->fetchNew();
            $row->trip_id = $trip;
            $row->included = $type;
            $row->text = $include->text;
            $row->created = date("Y-m-d H:i:s");
            $row->updated = date("Y-m-d H:i:s");
            $row->save();
        }
    }

    public function saveFacts($trip, $type, $text, $imageName) {
        $table = new Zend_Db_Table('trip_facts');
        $row = $table->fetchNew();
        $row->trip_id = $trip;
        $row->type = $type;
        $row->text = $text;
        $row->created = date("Y-m-d H:i:s");
        $row->updated = date("Y-m-d H:i:s");
        $row->save();
    }

    public function createPurchase($transaction, $cart, $trip) {
        $data = $cart->toArray();
        $data['transaction_id'] = $transaction->id;
        $data['status_id'] = 1;
        $data['code'] = $transaction->code;
        $data['created'] = date('Y-m-d g:i:s');
        $data['trip_id'] = $trip->id;

        unset($data['id']);
        unset($data['option_id']);
        unset($data['token']);
        unset($data['created']);
        unset($data['checkout']);
        unset($data['nights']);
        unset($data['rooms']);
        unset($data['listing_id']);
        unset($data['rate_description']);
        unset($data['additional']);
        unset($data['additional_description']);
        unset($data['max']);
        unset($data['token']);
        unset($data['type']);

        $row = $this->purchases->fetchNew();
        $row->setFromArray($data);
        $row->save();

        return $row;
    }

    public function getPurchase($id) {
        $select = $this->purchases->select();
        $select->where('id = ?', $id);
        $purchase = $this->purchases->fetchRow($select);

        return $purchase;
    }
    
    public function getCities($trip) {
        $select = $this->DB->select();
        $this->DB->setFetchMode(Zend_Db::FETCH_OBJ);
        $select->from('trip_cities', array('*'));
        $select->where('trip_id = ?', $trip);
        $select->order('id');
        $cities = $this->DB->fetchAll($select);
        return $cities;
    }

    public function saveCities($trip, $country, $cities) {
        $table = new Zend_Db_Table('trip_cities');
        $table->delete('trip_id = ' . $trip);
        foreach ($cities as $city) {
            if (!$city->city_id) {
                continue;
            }
            $row = $table->fetchNew();
            $row->trip_id = $trip;
            $row->country_id = $country;
            $row->city_id = $city->city_id;
            $row->save();
        }
    }
	
	public function deleteListing($listing) {
        $table = new Zend_Db_Table('trip_listings');
        $table->delete('id = ' . $listing);
		return true;
    }
	
	public function getTripListingById($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();
        
        $select->from('trip_listings',array('*', 'start_time'=>new Zend_Db_Expr("TIME_FORMAT(start, '%h:%i %p' )"), 'end_time'=>new Zend_Db_Expr("TIME_FORMAT(end, '%h:%i %p' )")));
        $select->where('id = ?', $id);
		$trip = $db->fetchRow($select);
		if (is_null($trip))
            return;

        return $trip;
    }
	
	public function saveListing($data, $id=null){
		
		if($id){
			$this->listings->update($data, "id=$id");
			return $id;
		}
		else{
			return $this->listings->insert($data);
		}
	}


}