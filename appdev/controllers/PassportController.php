<?php

class PassportController extends Zend_Controller_Action {
    
    /**
     *
     * @var WS_UsersService
     */
    protected $users;
    
    /**
     *
     * @var WS_User
     */
    protected $user;
    
    /**
     *
     * @var WS_PlacesService
     */
    protected $places;
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
            $this->_redirect('/login');
        
        $user = $auth->getIdentity();
        $this->user  = new WS_User($user);
        
        $this->users = new WS_UsersService();
        
        $this->places = new WS_PlacesService();
    }
    
    public function indexAction()
    {
        $username = $this->_getParam('username');
        if($username == '_default')
            $this->_redirect ('/');
        
        $profile = $this->users->getUserByUsername($username);
        
        $this->view->profile = $profile;
        $this->view->user    = $this->user->getData();
        
        $countries = $this->places->getPlaces(2);
        $this->view->countries = $countries;
        
        $places = $this->users->getCountries($profile->id);
        $this->view->places = $places;
        
        // Just for testing proposes
        $googlemap = new WS_Googlemap();
        $latlng = $googlemap->findLatLng('Costa Rica');
        $this->view->googlemap = $googlemap->getScript();
        $this->view->lat = $latlng[0];
        $this->view->long = $latlng[1];
        
        $albums = $this->users->getAlbumsOf($profile->id);
        if(!is_null($albums))
            $this->view->albums = $albums;
        
        $itineraries = $this->users->getItineraries($profile->id, 3);
        
        if(!is_null($itineraries))
            $this->view->itineraries = $itineraries;
        
        
        $friends = $this->getFriends($profile->id);
        if(count($friends) > 0)
            $this->view->friends = $friends;
        
        $stats = $this->users->getStats($profile->id);
        $this->view->stats = $stats;        
        
    }
    
    public function getFriends($user)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('user_friends');
        $select->where('user_friends.user1_id = ?', $user);
        $select->orWhere('user_friends.user2_id = ?', $user);
        
        $select->join('users','user_friends.user1_id = users.id',array(
            'user1_image'=>'image',
            'user1_name'=>'name',
            'username1'=>'username',
        ));
        $select->join(array('users2' => 'users'),'user_friends.user2_id = users2.id',array(
            'user2_image'=>'image',
            'user2_name'=>'name',
            'username2'=>'username',
        ));
        
        $friends = $db->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        
        return $friends;
    }
}