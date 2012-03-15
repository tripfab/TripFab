<?php

class WS_ReviewsService {
    
    /**
     *
     * @var Zend_Db_Table
     */
    protected $reviews;
    
    public function __construct() 
    {
        $this->reviews = new Zend_Db_Table('reviews');
    }
    
    public function getReviewsBy($user)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
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
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reviews');
        $select->join('users', 'reviews.user_id = users.id', array(
            'username'  => 'username',
            'user_name' => 'name',
            'user_image'=> 'image',
        ));
        $select->where('reviews.listing_id = ?', $listing);
        
        $reviews = $db->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        if(count($reviews) > 0)
            return $reviews;
        
        return array();
    }
    
    public function getReview($user, $listing)
    {
        $select = $this->reviews->select();
        $select->where('listing_id = ?', $listing);
        $select->where('user_id = ?', $user);
        $review = $this->reviews->fetchRow($select);
        return $review;
    }
    
    public function save($listing, $user, $text)
    {
        $review = $this->reviews->fetchNew();
        $review->listing_id = $listing;
        $review->user_id    = $user;
        $review->title      = substr($text, 0, 10).'...';
        $review->text       = $text;
        $review->listing_id = $listing;
        $review->created    = date('Y-m-d H:i:s');
        $review->updated    = date('Y-m-d H:i:s');
        
        $review->save();
        
        return $review;
    }
    
    public function markReviewsAsRead($listing)
    {
        $this->reviews->update(array('new'=>0), "listing_id = {$listing}");
    }
}
