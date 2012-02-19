<?php

class WS_MessagesService {
    
    protected $messages;
    protected $conversations;
    
    public function __construct() {
        $this->messages = new Model_Messages();
        $this->conversations = new Model_Conversations();
    }
    
    public function countNew($user)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $count = $db->fetchRow("select count(*) as total from conversations where ((starter = {$user} and snew = 1) or (wwith = {$user} and wnew = 1))");
        return $count['total'];
    }
    
    public function getMessagesOf($user, $task = null)
    {
        $select = $this->messages->select();
        $select->where('to_id = ?',$user);
        switch($task) {
            case 'unread':
                $select->where('trashed = ?',0);
                $select->where('viewed is null');
                $select->where('box_id = ?',1);
                break;
            case 'starred':
                $select->where('trashed = ?',0);
                $select->where('featured = ?',1);
                $select->where('box_id = ?',1);
                break;
            case 'sent':
                $select = $this->messages->select();
                $select->where('from_id = ?', $user);
                $select->where('trashed = ?',0);
                $select->where('box_id = ?',2);
                break;
            case 'trash':
                $select->where('trashed = ?',1);
                break;
            default:
                $select->where('trashed = ?',0);
                $select->where('box_id = ?',1);
                break;
        }
        
        $select->order('created ASC');
        $msgs = $this->messages->fetchAll($select);
        
        return $msgs;
    }
    
    public function getConversationOf($user)
    {
        $select = $this->conversations->select();
        $select->where("(starter = {$user} and sdelete = 0) or (wwith = {$user} and wdelete = 0)");
        $select->order('updated DESC');
        
        $conversations = $this->conversations->fetchAll($select);
        
        return $conversations;
        
    }
    
    public function getConversationById($id, $user)
    {
        //print $user; die;
        $select = $this->conversations->select();
        $select->where("id = {$id} and (starter = {$user} or wwith = {$user})");
        
        //print $select->assemble(); die;
        
        $conversation = $this->conversations->fetchRow($select);
        return $conversation;
    }
    
    public function getConversationMessages($id)
    {
        $select = $this->messages->select();
        $select->where('conversation_id = ?', $id);
        $select->order('created DESC');
        
        return $this->messages->fetchAll($select);
    }
    
}

?>
