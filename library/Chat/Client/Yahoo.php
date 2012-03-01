<?php

require "Chat/Helpers/CustomYMSG.php";

class Chat_Client_Yahoo 
        extends Chat_Client {
    
    /**
     * Operators Table Name
     */
    const OTABLE = 'live_operators';
    
    /**
     * 
     * @var YMSG
     */
    protected $service;
    
    /**
     *
     * @var string
     */
    protected $yahooUser;
    
    /**
     *
     * @var string
     */
    protected $yahooPassword;
    
    /**
     *
     * @var string
     */
    protected $vendorEmail;
    
    /**
     *
     * @var boolean
     */
    protected $connected = false;
    
    /**
     * Intanciate a new object of the class
     * 
     * @param mixed $config
     * @param string $vendorEmail 
     */
    public function __construct($config, $vendorEmail)
    {
        parent::__construct($config);
        
        $this->vendorEmail = $vendorEmail;
        
        $this->_initOperator();
        $this->_initService();
    }
    
    /**
     *  Fetch operators email and password from
     *  the operators table
     */
    private function _initOperator()
    {
        $table = new Zend_Db_Table(self::OTABLE);
        $select = $table->select();
        $select->where('id = ?', $this->operator);
        $operator = $table->fetchRow($select);
        
        $this->yahooUser     = $operator->email;
        $this->yahooPassword = $operator->password;
    }
    
    /**
     *  Instanciate a new Service/Interface
     *  to interact with yahoo server
     */
    private function _initService()
    {
        $this->service = new YMSG();
        $this->service->Verbosity(0);
        $this->service->YahooUser($this->yahooUser);
        $this->service->YahooPass($this->yahooPassword);
    }
    
    /**
     *  Connects to the yahoo server
     *  if its not connected already
     */
    private function _connect()
    {
        if(!$this->connected){
            $_messages = $this->service->YahooConnect();
            if($this->service->ConnectedStatus() == 0) {
                $this->connected = false;
                exit;
            } else {
                $this->connected = true;
            }
        }
        return $_messages;
    }
    
    public function connect()
    {
        $this->_connect();
    }
    
    /**
     *  Listener to the yahoo server events
     *  Saves new incoming messages to the table
     *  when new message recieved event is trigged
     */
    public function listen() {
        $_messages[] = $this->_connect();
        
        $_messages[] = $this->service->parsepackets();
        
        $_msgs = 0;

        if(isset($_messages[0][6]))
            foreach($_messages[0][6] as $msg) 
                if(!is_array($msg)) {
                    $this->save($msg);
                    $_msgs++;
                }
        if(isset($_messages[0][7]))
            foreach($_messages[0][7] as $msg) 
                if(!is_array($msg)){
                    $this->save($msg);
                    $_msgs++;
                }
        if(isset($_messages[1][0]))
            foreach($_messages[1][0] as $msg) 
                if(!is_array($msg)){
                    $this->save($msg);
                    $_msgs++;
                }
        
        $this->conversation->updated = date('Y-m-d H:i:s');
        $this->conversation->save();
        
        return $_msgs;
    }
    
    public function send($message)
    {
        parent::send($message);
        if(!$this->connected) 
            $this->_connect();
        
        $this->service->YahooPrivateMessage($this->vendorEmail,$message);
    }
}
