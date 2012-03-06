<?php

class WS_UsersService {
    
    /**
     *
     * @var Model_Users
     */
    protected $users_db;
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract 
     */
    protected $db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $user_albums;
    
    /**
     *
     * @var Model_Trips
     */
    protected $itineraries_db;
    
    /**
     *
     * @var Model_UserStats
     */
    protected $user_stats;
    
    /**
     *
     * @var Model_UserCountries
     */
    protected $user_countries;
    
    /**
     *
     * @var WS_PlacesService
     */
    protected $places;
    
    /**
     *
     * @var Model_UserPictures
     */
    protected $user_pictures;
 
    public function __construct() 
    {
        $this->users_db       = new Model_Users();
        $this->user_albums    = new Model_UserAlbums();
        $this->db             = Zend_Db_Table::getDefaultAdapter();
        $this->itineraries_db = new Model_Trips();
        $this->user_stats     = new Model_UserStats();
        $this->user_countries = new Model_UserCountries();
        $this->places         = new WS_PlacesService();
        $this->user_pictures  = new Model_UserPictures();
    }
    
    public function getAccount($id)
    {
        $accounts = new Zend_Db_Table('stripe_accounts');
        $select = $accounts->select();
        $select->where('id = ?', $id);
        $result = $accounts->fetchRow($select);
        
        if(is_null($result))
            throw new Exception();
        
        return $result;
    }
    
    public function getUserByUsername($username)
    {
        $select = $this->users_db->select();
        $select->where('username = ?', $username);
        $user = $this->users_db->fetchRow($select);
        if(!is_null($user))
            return $user;
        
        throw new Exception();
    }
    
    public function getAlbumsOf($user)
    {
        $select = $this->user_albums->select();
        $select->where('user_id = ?', $user);
        
        $albums = $this->user_albums->fetchAll($select);
        
        if(count($albums) > 0)
            return $albums;
        
        return null;        
    }
    
    public function getItineraries($user, $status = 3)
    {
        $select = $this->itineraries_db->select();
        $select->where('user_id = ?', $user);
        $select->where('status = ?', $status);
        
        $itineraries = $this->itineraries_db->fetchAll($select);
        if(count($itineraries) > 0)
            return $itineraries;
        
        return null;
    }
    
    public function getStats($user)
    {
        $select = $this->user_stats->select();
        $select->where('user_id = ?', $user);
        
        $stats = $this->user_stats->fetchRow($select);
        return $stats;
    }
    
    public function getCountries($user)
    {
        $select = $this->user_countries->select();
        $select->where('user_id = ?', $user);
        
        $places = $this->user_countries->fetchAll($select);
        $result = array();
        foreach($places as $_place){
            $place = $this->places->getPlaceById($_place->place_id);
            $aux = 'select count(*) as count from user_cities where user_id = '.$user.' and country_id = '.$place->id;
            $cities = $this->db->fetchRow($aux);
            $cities = $cities['count'];
            $result[$place->id] = array(
                'title'=>$place->title,
                'lat'  =>$place->lat,
                'lng'  =>$place->lng,
                'cities'=>$cities
            );
        }
        
        return $result;
    }
    
    public function createAlbum($data)
    {
        $new = $this->user_albums->fetchNew();
        $new->user_id = $data['userid'];
        $new->name    = $data['name'];
        $new->created = date('Y-m-d g:i:s');
        $new->updated = date('Y-m-d g:i:s');
        
        $new->save();
        
        return $new->id;
    }
    
    public function getAlbum($album, $user)
    {
        $select = $this->user_albums->select();
        $select->where('id = ?', $album);
        $select->where('user_id = ?', $user);
        
        $result = $this->user_albums->fetchRow($select);
        if(is_null($result))
            throw new Exception ('Album not found');
        return $result;
    }
    
    public function getPicturesBy($user = null, $album = null)
    {
        if(is_null($user) and is_null($album))
            return array();
        
        $select = $this->user_pictures->select();
        if(!is_null($user))
            $select->where('user_id = ?', $user);
        if(!is_null($album))
            $select->where('album_id = ?', $album);
        $pics = $this->user_pictures->fetchAll($select);
        
        return $pics;
        
    }
    
    public function get($user)
    {
        $select = $this->users_db->select();
        $select->where('id = ?', $user);
        $result = $this->users_db->fetchRow($select);
        return $result;
    }
    
    public function getFull($user) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();
        $select->from(array('users'), array('*', 'if(birthdate=\'0000-00-00\', null, year(now()) - year(birthdate)) as age'))
               ->joinleft(array('city' => 'places'), 'users.city_id=city.id', array('cityName' => 'title'))
               ->joinleft(array('country' => 'places'), 'users.country_id=country.id', array('countryName' => 'title'))
               ->where('users.id = ?', $user);

        return $db->fetchRow($select);
    }
}