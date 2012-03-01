<?php

class Chat_Client {
    
    /**
     *  Livechat conversations table name
     */
    const CTABLE = 'livechats';
    
    /**
     *  Livechat conversation messages table name
     */
    const MTABLE = 'live_messages';
    
    /**
     *
     * @var int
     */
    protected $user;
    
    /**
     *
     * @var int
     */
    protected $provider;
    
    /**
     *
     * @var int
     */
    protected $operator;
    
    /**
     *
     * @var Zend_Db_Table_Row
     */
    protected $conversation;
    
    /**
     *
     * @var string
     */
    protected $id;
    
    /**
     *  Send a message to the table
     *  A message comming from the user
     */
    public function send($message)
    {
        $messages = new Zend_Db_Table(self::MTABLE);
        $new = $messages->fetchNew();
        $new->sender  = $this->user;
        $new->message = $message;
        $new->conversation = $this->conversation->id;
        $new->created = date('Y-m-d H:i:s');
        $new->save();
        
        $this->conversation->updated = date('Y-m-d H:i:s');
        $this->conversation->save();
    }
    
    /**
     *  Save a message to the table
     *  A message coming from the partner
     */
    public function save($message)
    {
        $messages = new Zend_Db_Table(self::MTABLE);
        $new = $messages->fetchNew();
        $new->sender  = $this->provider;
        $new->message = $message;
        $new->conversation = $this->conversation->id;
        $new->created = date('Y-m-d H:i:s');
        $new->save();
    }
    
    /**
     *  Read new message on the table
     *  Messages sent by the provider
     */
    public function read()
    {
        $messages = new Zend_Db_Table(self::MTABLE);
        $select = $messages->select();
        $select->where('conversation = ?', $this->conversation->id);
        $select->where('readed = ?', 0);
        $select->where('sender = ?', $this->provider);

        $news = $messages->fetchAll($select);
        if(count($news) > 0) {
            foreach($news as $new) {
                $new->readed = 1;
                $new->save();
            }
            return $news;
        } 
        return array();
    }
    
    public function getConversationId()
    {
        return $this->conversation->id;
    }
    
    /**
     *  Open an existing conversation
     */
    private function _openConversation()
    {
        $table  = new Zend_Db_Table(self::CTABLE);
        $select = $table->select();
        $select->where('id = ?', $this->id);
        
        $this->conversation = $table->fetchRow($select);
        $this->user      = $this->conversation->user_id;
        $this->provider  = $this->conversation->vendor_id;
        $this->operator  = $this->conversation->operator_id;
        $this->id        = $this->conversation->token;
    }
    
    /**
     *  Creates a new Conversation
     */
    private function _initConversation()
    {
        $table  = new Zend_Db_Table(self::CTABLE);
        $this->conversation = $table->fetchNew();
        $this->conversation->user_id     = $this->user;
        $this->conversation->vendor_id   = $this->provider;
        $this->conversation->operator_id = $this->operator;
        $this->conversation->token       = md5($this->user.$this->provider.$this->operator);
        $this->conversation->created     = date('Y-m-d H:i:s');
        $this->conversation->updated     = date('Y-m-d H:i:s');
        
        $this->conversation->save();
    }
    
    /**
     *  Instanciate a new object of the class
     * 
     *  @param mixed $config
     */
    public function __construct($config) {
        if(is_array($config)){
            $this->user     = $config['user'];
            $this->provider = $config['provider'];
            $this->operator = $config['operator'];
            
            $this->_initConversation();
        } else {
            $this->id = $config;
            $this->_openConversation();
        }
    }
}