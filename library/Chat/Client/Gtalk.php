<?php

require "Chat/Helpers/XMPPHP/XMPP.php";

class Chat_Client_Gtalk 
        extends Chat_Client {
    
    /**
     * Operators Table Name
     */
    const OTABLE = 'live_operators';
    
    /**
     * 
     * @var XMPPHP_XMPP
     */
    protected $service;
    
    /**
     *
     * @var string
     */
    protected $gmailUser;
    
    /**
     *
     * @var string
     */
    protected $gmailPassword;
    
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
        
        $this->gmailUser     = $operator->email;
        $this->gmailPassword = $operator->password;
    }
    
    /**
     *  Instanciate a new Service/Interface
     *  to interact with yahoo server
     */
    private function _initService()
    {
        $this->service = new XMPPHP_XMPP('talk.google.com', 
                                         5222, 
                                         $this->gmailUser, 
                                         $this->gmailPassword, 
                                         'xmpphp', 'gmail.com', 
                                         $printlog = False, 
                                         $loglevel = LOGGING_INFO);
    }
    
    /**
     *  Connects to the yahoo server
     *  if its not connected already
     */
    private function _connect()
    {
        if(!$this->connected){
            $this->service->connect();
            $this->service->processUntil('session_start');
            $this->service->presence($status = "Live Support Team 1");
            if(!$this->service->disconnected) {
                $this->connected = true;
            } else {
                $this->connected = false;
            }
        }
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
        
        
        if(!$this->connected)
            $this->_connect();
        
        $_msgs = 0;
        
        $last = '';
        $last2 = '';
        
        $proceed = true;
        $read = true;
        
        while (!$this->service->disconnected) {
            $this->service->presence($status = "Live Support Team 1");
            $payloads = $this->service->processUntil(array('message', 'presence', 'end_stream', 'session_start'));
            foreach ($payloads as $event) {
                $pl = $event[1];
                switch ($event[0]) {
                    case 'message':
                        //if ($pl['subject'])
                            //print "Subject: {$pl['subject']}\n";
                        
                        
                        if(!is_null($pl['body'])) {

                            if ($pl['body'] == 'quit')
                                $this->service->disconnect();
                            elseif ($pl['body'] == 'break')
                                $this->service->send("</end>");
                            elseif($proceed && ($last != $pl['body'])) {
                                $proceed = false;
                                $last = $pl['body'];
                                //$this->save($pl['body']);
                                var_dump($pl);
                                return;
                                $this->conversation->updated = date('Y-m-d H:i:s');
                                $this->conversation->save();
                            }
                        }
                        break;
                }
            }
            
            $proceed = true;
            
            if($read) {
                $messages = new Zend_Db_Table(self::MTABLE);
                $select = $messages->select();
                $select->where('conversation = ?', $this->conversation->id);
                $select->where('readed = ?', 0);
                $select->where('sender = ?', $this->user);

                $news = $messages->fetchAll($select);

                if(count($news) > 0) {
                    $read = false;
                    foreach($news as $new) {
                        if($new != $last2){
                            $last2 = false;
                            $this->service->message($this->vendorEmail, $new->message);
                            $new->readed = 1;
                            $new->save();
                        }
                    }
                }
                
                $read = true;
            }
        }
    }
    
    protected function sendMsgs()
    {
        
    }
}
