<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FeedsService
 *
 * @author magentodeveloper
 */
class WS_FeedsService {
    
    protected $feeds;
    
    public function __construct() {
        $this->feeds = new Model_Feeds();
    }
    
    public function getFeedsFor($vendor){
        $feeds = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('feed');
        $select->join('users', 'feed.from_id = users.id',array('user_image'=>'image','user_name' => 'name'));
        $select->join('feed_types', 'feed.type_id = feed_types.id',array('message','name'));
        $select->joinLeft('listings', 'feed.listing_id = listings.id',array('listing_name' => 'title'));
        $select->where('feed.to_id = ?',$vendor);
        $select->limit(15,0);
        $select->order('feed.created DESC');
        $_feeds = $db->fetchAll($select);
        if(count($_feeds) == 0)
            return $feeds;
        
        //var_dump($_feeds); exit;
        $dates = array();
        foreach($_feeds as $f){
            $d = date('F jS', strtotime($f['created']));
            if(!in_array($d, $dates)){
                $dates[] = $d;
                $items = array();
                foreach($_feeds as $ff){
                    $dd = date('F jS', strtotime($ff['created']));
                    if($d == $dd)
                        $items[] = array(
                            'user_image' => $ff['user_image'],
                            'message'    => 
                                $ff['user_name'].' '.$ff['message'].' '.$ff['name'].' '.$ff['listing_name'],
                        );
                }
                $feeds[] = array(
                    'date' => $d,
                    'items'=> $items                    
                );
            }
        }
        
        //var_dump($feeds); exit;
        
        
        
        return $feeds;
    }
    
}

?>
