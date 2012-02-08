<?php

class WS_PlacesService {
    
    /**
     *
     * @var Model_Places 
     */
    protected $places_db;
    
    /**
     *
     * @var Model_PlaceTabTypes 
     */
    protected $place_tab_types_db;
    
    /**
     *
     * @var Model_PlaceTabs 
     */
    protected $place_tabs_db;
    
    /**
     *
     * @var Model_Landscapes 
     */
    protected $landscapes_db;
    
    /**
     *
     * @var Model_PlacesLandscapes 
     */
    protected $placeslandscapes_db;
    
    /**
     *
     * @var Zend_Db_Table_Rowset 
     */
    protected $landscapes;
    
    /**
     *
     * @var Zend_Cache_Core
     */
    protected $cache;
    
    /**
     * 
     */
    public function __construct() 
    {        
        $this->places_db = new Model_Places();
        
        $this->place_tab_types_db = new Model_PlaceTabTypes();
        
        $this->place_tabs_db = new Model_PlaceTabs();
        
        $this->landscapes_db = new Model_Landscapes();
        
        $this->placeslandscapes_db = new Model_PlacesLandscapes();
        
        $this->landscapes = NULL;
        
        $this->cache = Zend_Registry::get('cache');
    }
    
    /**
     *
     * @param string $idf
     * @param integer $type
     * @param integer $parent
     * 
     * @return Zend_Db_Table_Rowset 
     */
    public function getPlaceByIdf($idf, $type = 2, $parent = null)
    {
        $country_idf = trim(strtolower($idf));
        
        // Get the current country object
        $select  = $this->places_db->select();
        $select->where('identifier = ?',$country_idf);
        $select->where('type_id = ?',$type);
        if(!is_null($parent))
            $select->where('parent_id = ?', $parent);
        
        $place = $this->places_db->fetchRow($select);
        
        return $place;
    }
    
    /**
     *
     * @param integer $id
     * 
     * @return Zend_Db_Table_Row 
     */
    public function getPlaceById($id)
    {
        $select = $this->places_db->select();
        $select->where('id = ?', $id);
        
        $place = $this->places_db->fetchRow($select);
        if(is_null($place))
            return;
        
        return $place;
    }
    
    /**
     *
     * @param string $name
     * @param integer $place
     * 
     * @return Zend_Db_Table_Row 
     */
    public function getTab($name, $place)
    {
        $tab = ucwords(str_replace('_',' ',$name));
        
        $select = $this->place_tab_types_db->select();
        $select->where('name = ?',$tab);
        $active_tab_info = $this->place_tab_types_db->fetchRow($select);
        if(is_null($active_tab_info))
            throw new Exception();
        
        $select = $this->place_tabs_db->select();
        $select->where('place_id = ?',$place);
        $select->where('type_id = ?',$active_tab_info->id);
        $active_tab = $this->place_tabs_db->fetchRow($select);
        if(is_null($active_tab))
            throw new Exception();
        
        return $active_tab;
    }
    
    public function getTabByType($type, $place)
    {
        $select = $this->place_tabs_db->select();
        $select->where('place_id = ?',$place);
        $select->where('type_id = ?',$type);
        $active_tab = $this->place_tabs_db->fetchRow($select);
        
        return $active_tab;
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset 
     */
    public function getAllTabTypes()
    {
        return $this->place_tab_types_db->fetchAll();
    }
    
    /**
     *
     * @param integer $country
     * 
     * @return Zend_Db_Table_Rowset 
     */
    public function getTopPlacesFrom($country)
    {
        // Get Top 5 Places by loves
        $select = $this->places_db->select();
        $select->where('parent_id = '.$country);
        $select->order('loves DESC');
        $select->limit(5);
        $top_places = $this->places_db->fetchAll($select);
        if(is_null($top_places))
            throw new Exception();
        
        return $top_places;
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset 
     */
    public function getAllLandscapes()
    {
        if(is_null($this->landscapes))
            $this->_setLandscapes();
        
        return $this->landscapes;
    }
    
    /**
     *
     * @param integer $place
     * 
     * @return array 
     */
    public function getCityCountByLandscapeFrom($place)
    {
        $city_count = array();
        
        $db = $this->landscapes_db->getDefaultAdapter();
        $count = $db->fetchRow('select count(*) from places where parent_id = '.$place);
        
        $city_count['all'] = $count['count(*)'];
        
        if(is_null($this->landscapes))
            $this->_setLandscapes();
        
        foreach($this->landscapes as $k=>$ls){
            $select = $db->select();
            $select->from('places');
            $select->join('places_landscapes', 'places.id = places_landscapes.place_id');
            $select->join('landscapes', 'places_landscapes.landscape_id = landscapes.id');
            $select->where('places.parent_id = ?',$place);
            $select->where('landscapes.id = ?', $ls->id);
            
            $city_count[$ls->id] = 
                count($db->fetchAll($select->assemble()));
        }
        
        return $city_count;
    }
    
    /**
     *
     * @param integer $place
     * @param string $landscape
     * 
     * @return Zend_Db_Table_Rowset 
     */
    public function getCityByLandscapeFrom($place, $landscape = 'all')
    {
        $landscape = ucwords($landscape);
        if($landscape == 'All'){
            $cities = $this->places_db->fetchAll('parent_id = '.$place, 'loves DESC',12,0);
        } else {
            $landscape = $this->landscapes_db->fetchRow("name = '".$landscape."'");
            
            $db = $this->landscapes_db->getDefaultAdapter();
            $select = $db->select();
            $select->from('places');
            $select->join('places_landscapes', 'places.id = places_landscapes.place_id');
            $select->join('landscapes', 'places_landscapes.landscape_id = landscapes.id');
            $select->where('places.parent_id = ?', $place);
            $select->where('landscapes.id = ?', $landscape->id);
            $select->order('places.loves DESC');
            $select->limit(12, 0);
            
            $cities = $db->fetchAll($select->assemble(), array(), Zend_Db::FETCH_OBJ);
        }
        
        if(is_null($cities))
            $cities = array();
        
        return $cities;
    }
    
    public function getCountriesFrom($region)
    {
        $select = $this->places_db->select();
        $select->where('parent_id = ?',$region);
        $countries = $this->places_db->fetchAll($select);
        if(is_null($countries))
            $countries = array();
        
        return $countries;
        
    }
    
    /**
     * 
     */
    protected function _setLandscapes()
    {
        $this->landscapes = $this->landscapes_db->fetchAll();
    }
    
    public function getRegions()
    {
        return $this->places_db->fetchAll('parent_id is null');
    }
    
    public function getTabs($place)
    {
        $select = $this->place_tabs_db->select();
        $select->where('place_id = ?', $place);
        $tabs = $this->place_tabs_db->fetchAll($select);
        return $tabs;        
    }
    
    public function getPlaces($type, $parent = null)
    {
        $args = func_get_args();
        $cacheId = "PS_getplaces_".md5(print_r($args, true));
        if($this->cache->test($cacheId) !== false) {
            $places = $this->cache->load($cacheId);
        }
        else {
            $select = $this->places_db->select();
            $select->where('type_id = ?', $type);
            $select->order('title ASC');
            if(!is_null($parent))
                $select->where('parent_id = ?', $parent);
            $places = $this->places_db->fetchAll($select);
            
            $this->cache->save($places, $cacheId, array(), 86400);
        }
        return $places;
    }
}