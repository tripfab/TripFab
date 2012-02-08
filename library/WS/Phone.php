<?php

class WS_Phone {
    
    /**
     *
     * @var Services_Twilio
     */
    protected $service;
    
    /**
     *
     * @var array
     */
    protected $config;
    
    /**
     *
     * @var Zend_Db_Table
     */
    protected $records;
    
    public function __construct() 
    {
        $this->_init();
        $this->service = new Services_Twilio($this->account_sid, $this->auth_token);
        $this->records = new Zend_Db_Table($this->table);
    }
    
    public function call($user, $user_id, $name, $partner, $listing, $listing_id, $vendor, $callback)
    {
        $params = array(
            'number'     => $user,
            'username'   => $name,
            'listing'    => $listing,
            'listing_id' => $listing_id,
            'vendor'     => $vendor,
            'user'       => $user_id
        );
        $query = http_build_query($params);
        $callback = $callback . '?' . $query;
        $this->service->account->calls->create($this->number, $partner, $callback);
    }
    
    public function getSID()
    {
        return $this->service->sid;
    }
    
    public function saveConversation($mp3, $user, $partner, $listing)
    {
        $record = $this->records->fetchNew();
        $record->url        = $mp3;
        $record->user_id    = $user;
        $record->vendor_id  = $partner;
        $record->listing_id = $listing;
        $record->created    = date("Y-m-d H:i:s");
        
        $record->save();
    }
    
    protected function _init()
    {
        $this->config = Zend_Registry::get('twilio');
    }
    
    public function __get($name) 
    {
        return $this->config[$name];
    }
}