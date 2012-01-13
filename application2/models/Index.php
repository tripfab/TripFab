<?php

class Model_Index extends Zend_Db_Table_Abstract {
    
    protected $_name = 'index';
    
    
    
    
    public function search($q, $page = 0){
        
        $db = $this->getDefaultAdapter();
        $limit = 30;
        $offset = $page * $limit;
        
        $results = $db->fetchAll("SELECT * FROM `index` WHERE MATCH (title,overview) AGAINST ('".$q."') LIMIT ".$offset.",".$limit."",array(), Zend_Db::FETCH_OBJ);
        
        return $results;
        
    }
    
}