<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin
 *
 * @author magentodeveloper
 */
class WS_Admin {
    
    private $validListingSorts = array(
'country_id','city_id','status','main_type','title'
    );
    
    public function getListings($limiters, $params)
    {    
        $listings = new Zend_Db_Table('listings');
        $select = $listings->select();
        
        foreach($limiters as $column => $value)
            if($value != 'all') $select->where("{$column} = ?", $value);
        
        if(in_array($params['sort'],$this->validListingSorts) && 
                in_array($params['order'],array('ASC','DESC')))
            $select->order("{$params['sort']} {$params['order']}");
        else 
            $select->order("title DESC");
        
        $limit = 50;
        $offset = ($params['page'] - 1) * $limit;
        $select->limit($limit, $offset);
        
        if(!is_null($params['find']))
            $select->where("title like '%{$params['find']}%'");
        
        $result = $listings->fetchAll($select);
        return (count($result) > 0) ? $result : array();
    }
    
    public function countListings(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $listings = $db->fetchRow('Select count(*) as total from listings');
        return $listings['total'];
    }
}
