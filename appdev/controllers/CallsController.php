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
        
        $this->view->cssVC = Zend_Registry::get('vc');
    }
    
    public function callAction()
    {
        $headers = getallheaders();
        if(array_key_exists('X-Requested-With', $headers) === false and
                array_key_exists('x-requested-with', $headers) === false){
            error_log('Wrong request');
            echo 'Wrong request'; die;
        }
        
        if($this->getRequest()->isPost())
        {
            try {
                $listing_id = $this->_getParam('listing','default');
                if($listing_id == 'default')
                        throw new Exception('Missing listing id');

                $listing = $this->listings->getListing($listing_id);
                if(empty($listing->phone)){
                    $vendor = $this->listings->getVendor($listing->vendor_id);
                    if(empty($vendor->phone))
                        throw new Exception('Listing Phone not found');
                    else $phone = $vendor->phone;
                } else $phone = $listing->phone;
                
                

                $partner  = '+506'.$this->_cleanPhoneNumber($phone);
                $user     = $this->_getParam('number');
                $username = $this->user->getName();

                $this->service->call($user, $this->user->getId(), $username, $partner, 
                        $listing->title, $listing->id, $listing->vendor_id,  $this->callback);
                
                $result = array(
                    'type' => 'ok',
                    'message' => ''
                );
            } catch(Exception $e) {
                $result = array(
                    'type' => 'failed',
                    'message' => $e->getMessage()
                );
            }
            if(is_array($result)) {
                header('Content-type: application/json');
                $result = json_encode($result);
                echo $result;
                die;
            } else {
                echo 'Wrong request'; die;
            }
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
        $number = preg_replace("/^ *\(* *00 *\)* */", "", $number);
        $number = preg_replace("/^\(* *\+? *\(* *506 *\)* */", "", $number);
        $number = preg_replace("/-/", "",$number);
        $number = preg_replace("/ /", "",$number);
        
        return $number;
    }
    
    private function _ensureClean($number){
        $clean = true;
        if(strpos($number, ')') !== false) $clean = false;
        if(strpos($number, '(') !== false) $clean = false;
        if(strpos($number, '+') !== false) $clean = false;
        if(strpos($number, '-') !== false) $clean = false;
        
        return $clean;
    }
}