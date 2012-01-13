<?php

class WS_JsonService {
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $DB;
    
    /**
     *
     * @var WS_PlacesService 
     */
    protected $places;
 
    public function __construct() {
        $this->DB = Zend_Db_Table::getDefaultAdapter();
        $this->places = new WS_PlacesService();
    }
    
    public function getCountries(){
        $select = $this->DB->select();
        $select->from('places');
        $select->where('type_id = ?', 2);
        $select->order('title ASC');
        
        $_result = $this->DB->fetchAll($select);
        
        return $this->_prepareResult($_result, 'title');
    }
    
    public function getTags(){
        $select = $this->DB->select();
        $select->from('tags');
        $select->order('name ASC');
        
        $_result = $this->DB->fetchAll($select);
        
        return $this->_prepareResult($_result, 'name');
    }
    
    private function _prepareResult($source, $field){
        $result = array();
        foreach($source as $s)
            $result[] = $s[$field];
        
        return json_encode($result);
    }
    
    public function getSerchTerms()
    {   
        $search = new Model_Search();
        $tags = $search->fetchAll();
        $tags = $tags->toArray();
        
        return json_encode($tags);
    }
    
    public function getCountryTerms()
    {
        $places = $this->places->getPlaces("2");
        $places = $places->toArray();
        $countries = array();
        foreach($places as $place)
            $countries[] = array(
                'label' => $place['title'],
                'url'   => '/trips/'.$place['identifier'],
            );
        
        return json_encode($countries);
    }
    
    
}