<?php

class CallsController extends Zend_Controller_Action {
    
    /**
     *
     * @var WS_Phome
     */
    protected $service;
    
    /**
     *
     * @var string
     */
    protected $callback;
    
    /**
     *
     * @var WS_User
     */
    protected $user;
    
    /**
     *
     * @var WS_ListingService
     */
    protected $listings;
    
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $this->user = new WS_User($auth->getIdentity());
            if($this->user->getRole() != 'user') {
                //echo 'You don\'t have enough permissions to access this page'; die;
            }
        } else {
            //echo 'You don\'t have enough permissions to access this page'; die;
        }
        $this->service  = new WS_Phone();
        $this->listings = new WS_ListingService();
        $this->callback = "http://developer.tripfab.com/phone/callback";
    }
    
    public function callAction()
    {
        $headers = getallheaders();
        if(array_key_exists('X-Requested-With', $headers) === false){
            error_log('Wrong request');
            echo 'Wrong request'; die;
        }
        
        if($this->getRequest()->isPost())
        {
            $listing_id = $this->_getParam('listing','default');
            if($listing_id == 'default')
                    throw new Exception('Missing listing id');
            
            $listing = $this->listings->getListing($listing_id);
            if(empty($listing->phone))
                    throw new Exception('Listing Phone not found');
            
            $partner  = '+506'.$this->_cleanPhoneNumber($listing->phone);
            $user     = $this->_getParam('number');
            $username = $this->user->getName();
            
            $this->service->call($user, $this->user->getId(), $username, $partner, 
                    $listing->title, $listing->id, $listing->vendor_id,  $this->callback);
            
            echo "Connecting..........".$this->service->getSID(); 
            die;
        } 
        else echo 'Wrong request'; die;
    }
    
    public function callbackAction()
    {
        $this->view->listing  = $this->_getParam('listing');
        $this->view->number   = $this->_getParam('number');
        $this->view->username = $this->_getParam('username');
        
        $params = array(
            'user_id' => $this->_getParam('user'),
            'vendor_id' => $this->_getParam('vendor'),
            'listing_id' => $this->_getParam('listing_id')
        );
        
        $action = 'http://developer.tripfab.com/phone/record?';
        $query  = http_build_query($params,null,'&amp;');
        
        $this->view->action = $action . $query;
        
        header("content-type: text/xml");
    }
    
    public function recordAction()
    {
        $url     = $this->_getParam('RecordingUrl');
        $user    = $this->_getParam('user_id', 0);
        $partner = $this->_getParam('vendor_id', 0);
        $listing = $this->_getParam('listing_id', 0);
        $this->service->saveConversation($url, $user, $partner, $listing);
        
        //var_dump($this->_getAllParams());
        
        header("content-type: text/xml");
        echo "<Response/>"; die;
    }
    
    private function _cleanPhoneNumber($number) {
        $clean = false;
        while(!$clean) {
            $number = str_replace('506', '', $number);
            $number = str_replace('(', '', $number);
            $number = str_replace(')', '', $number);
            $number = str_replace('+', '', $number);
            $number = str_replace('-', '', $number);
            $number = str_replace(' ', '', $number);
            
            $clean = $this->_ensureClean($number);
        }
        return $number;
    }
    
    private function _ensureClean($number){
        $clean = true;
        if(strpos($number, '506') !== false) $clean = false;
        if(strpos($number, ')') !== false) $clean = false;
        if(strpos($number, '(') !== false) $clean = false;
        if(strpos($number, '+') !== false) $clean = false;
        if(strpos($number, '-') !== false) $clean = false;
        
        return $clean;
    }
}