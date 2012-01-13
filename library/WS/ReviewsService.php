<?php

class WS_ReviewsService {

    public function __construct() {
        
    }
    
    public function getReviewsBy($user)
    {
        $user = 1;
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reviews');
        $select->join('listings', 'reviews.listing_id = listings.id', array('listing_name' => 'title'));
        $select->join('listing_types', 'listings.main_type = listing_types.id', array('listing_type' => 'name'));
        $select->join('places', 'listings.city_id = places.id', array('city' => 'title'));
        $select->join(array('places2' => 'places'), 'listings.country_id = places2.id', array('country' => 'title'));
        $select->where('reviews.user_id = ?', $user);
        
        $reviews = $db->fetchAll($select);
        return $reviews;
        
        
    }
    
    public function getReviewsFor($listing)
    {
        //$listing = 16;
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reviews');
        $select->join('users', 'reviews.user_id = users.id', array(
            'username'  => 'username',
            'user_name' => 'name',
            'user_image'=> 'image',
        ));
        $select->where('reviews.listing_id = ?', $listing);
        $select->orWhere('reviews.listing_id = ?', 16);
        
        $reviews = $db->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        if(count($reviews) > 0)
            return $reviews;
        
        return array();
    }
    
}
