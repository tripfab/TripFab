<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChatController
 *
 * @author magentodeveloper
 */
class ChatController extends Zend_Controller_Action {
 
    /**
     *
     * @var Chat_Client
     */
    protected $chat;
    
    /**
     *
     * @var WS_UsersService
     */
    protected $users;
    
    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $session;
    
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                $this->_redirect('/login');
        $this->users   = new WS_UsersService();
        $this->user    = new WS_User($auth->getIdentity());
        $this->session = new Zend_Session_Namespace('Chat');
        
        $this->view->_cssVC = Zend_Registry::get('vc');
    }
    
    public function indexAction()
    {
        $user   = $this->user->getData();
        $vendor = $this->users->get($this->_getParam('provider','default'));
        //unset($this->session->chat); die;
        if(!isset($this->session->chat)){
            if(is_null($vendor) or $vendor->role_id != '3')
                throw new Exception('Page not found vendor null or not vedor');
            
            $vendorEmail = $this->getLivechatEmail($vendor->id);
            if(is_null($vendorEmail)) {
                $this->view->online = false;
            } else {
                switch($vendorEmail->service){
                    case 'yahoo':
                        $operators = $this->getOperators('yahoo');
                        $operator  = null;
                        foreach($operators as $opt) {
                            if(!$this->existsConversation($vendor->id, $opt->id) and is_null($operator)) {
                                $operator = $opt;
                            }
                        }
                        if(is_null($operator)) {
                            $this->view->online = false;
                        } else {
                            $this->chat = new Chat_Client_Yahoo(array(
                                'user' => $user->id,
                                'provider' => $vendor->id,
                                'operator' => $operator->id
                            ), $vendorEmail->email);
                            
                            $chat = new stdClass();
                            $chat->user     = $user->id;
                            $chat->partner  = $vendor->id;
                            $chat->service  = 'yahoo';
                            $chat->email    = $vendorEmail->email;
                            $chat->conv     = $this->chat->getConversationId();

                            $this->session->chat = $chat;
                            
                            $this->view->online = true;
                        }
                        break;
                    case 'gmail':
                        $operators = $this->getOperators('gmail');
                        $operator  = null;
                        foreach($operators as $opt) {
                            if(!$this->existsConversation($vendor->id, $opt->id) and is_null($operator)) {
                                $operator = $opt;
                            }
                        }
                        if(is_null($operator)) {
                            $this->view->online = false;
                        } else {
                            $this->chat = new Chat_Client_Yahoo(array(
                                'user' => $user->id,
                                'provider' => $vendor->id,
                                'operator' => $operator->id
                            ), $vendorEmail->email);
                            
                            $chat = new stdClass();
                            $chat->user     = $user->id;
                            $chat->partner  = $vendor->id;
                            $chat->service  = 'gmail';
                            $chat->email    = $vendorEmail->email;
                            $chat->conv     = $this->chat->getConversationId();

                            $this->session->chat = $chat;
                            
                            $this->view->online = true;
                        }
                        break;
                    default:
                        $this->view->online = false;
                }
            }
        } else {
            $chat   = $this->session->chat;
            
            if($chat->partner != $vendor->id or $chat->user != $user->id){
                unset($this->session->chat);
                echo 'Your session has expired. Please reload the page'; die;
            }
            
            switch($chat->service){
                case 'yahoo':
                    $this->chat = new Chat_Client_Yahoo(
                            $chat->conv, $chat->email);

                    $this->view->online = true;
                break;
            
                case 'gmail':
                    $this->chat = new Chat_Client_Gtalk(
                            $chat->conv, $chat->email);

                    $this->view->online = true;
                break;

                default:
                    unset($this->session->chat);
                    echo 'Your session has expired. Please reload the page'; die;
                break;
            }
        }
        $this->view->vendor = $vendor;
        $this->view->user   = $user;
    }
    
    public function listenAction()
    {
        $headers = getallheaders();
        if((array_key_exists('X-Requested-With', $headers) === false) and 
                (array_key_exists('x-requested-with', $headers) === false)){
            error_log('Wrong request');
            echo 'Wrong request'; die;
        }
        
        if(!isset($this->session->chat)){
            echo 'Your session has expired. Please reload the page'; die;
        }
        
        $chat   = $this->session->chat;
        $vendor = $this->_getParam('provider','default');
        $user   = $this->user->getData();
        if($chat->partner != $vendor or $chat->user != $user->id){
            unset($this->session->chat);
            echo 'Your session has expired. Please reload the page'; die;
        }
        
        
        switch($chat->service){
            case 'yahoo':
                $this->chat = new Chat_Client_Yahoo(
                        $chat->conv, $chat->email);
                
                echo $this->chat->listen(); die;
            break;
        
            case 'gmail':
                $this->chat = new Chat_Client_Gtalk(
                        $chat->conv, $chat->email);
                
                echo $this->chat->listen(); die;
            break;
        
            default:
                unset($this->session->chat);
                echo 'Your session has expired. Please reload the page'; die;
            break;
        }
    }
    
    public function sendAction()
    {
        $headers = getallheaders();
        if((array_key_exists('X-Requested-With', $headers) === false) and 
                (array_key_exists('x-requested-with', $headers) === false)){
            error_log('Wrong request');
            echo 'Wrong request'; die;
        }
        
        if(!isset($this->session->chat)){
            echo 'Your session has expired. Please reload the page'; die;
        }
        
        $chat   = $this->session->chat;
        $vendor = $this->_getParam('provider','default');
        $user   = $this->user->getData();
        if($chat->partner != $vendor or $chat->user != $user->id){
            unset($this->session->chat);
            echo 'Your session has expired. Please reload the page'; die;
        }
        
        switch($chat->service){
            case 'yahoo':
                $this->chat = new Chat_Client_Yahoo(
                        $chat->conv, $chat->email);
                
                echo $this->chat->send($this->_getParam('message')); die;
            break;
        
            case 'gmail':
                $this->chat = new Chat_Client_Gtalk(
                        $chat->conv, $chat->email);
                
                echo $this->chat->send($this->_getParam('message')); die;
            break;
        
            default:
                unset($this->session->chat);
                echo 'Your session has expired. Please reload the page'; die;
            break;
        }
    }
    
    public function readAction()
    {
        $headers = getallheaders();
        if((array_key_exists('X-Requested-With', $headers) === false) and 
                (array_key_exists('x-requested-with', $headers) === false)){
            error_log('Wrong request');
            echo 'Wrong request'; die;
        }
        
        if(!isset($this->session->chat)){
            echo 'Your session has expired. Please reload the page'; die;
        }
        
        $chat   = $this->session->chat;
        $vendor = $this->_getParam('provider','default');
        $user   = $this->user->getData();
        if($chat->partner != $vendor or $chat->user != $user->id){
            unset($this->session->chat);
            echo 'Your session has expired. Please reload the page'; die;
        }
        
        switch($chat->service){
            case 'yahoo':
                $this->chat = new Chat_Client_Yahoo(
                        $chat->conv, $chat->email);
                
                $messages = $this->chat->read();
                $response = array();
                foreach($messages as $msg) {
                    $response[] = $msg->message;
                }
                header('Content-type: application/json');
                echo json_encode($response); die;
            break;
            
            case 'gmail':
                $this->chat = new Chat_Client_Gtalk(
                        $chat->conv, $chat->email);
                
                $messages = $this->chat->read();
                $response = array();
                foreach($messages as $msg) {
                    $response[] = $msg->message;
                }
                header('Content-type: application/json');
                echo json_encode($response); die;
            break;
        
            default:
                unset($this->session->chat);
                echo 'Your session has expired. Please reload the page'; die;
            break;
        }
    }
    
    private function getLivechatEmail($vendor)
    {
        $table = new Zend_Db_Table('livechat_accounts');
        $select = $table->select();
        $select->where('user_id = ?', $vendor);
        $account = $table->fetchRow($select);
        return $account;
    }
    
    private function getOperators($service)
    {
        $table = new Zend_Db_Table('live_operators');
        $select = $table->select();
        $select->where('service = ?', $service);
        $opts = $table->fetchAll($select);
        
        return $opts;
    }
    
    private function existsConversation($vendor, $opt)
    {
        $updated = date('Y-m-d H:i:s', (time() - 1200));
        $table = new Zend_Db_Table('livechats');
        $select = $table->select();
        $select->where('vendor_id = ?', $vendor);
        $select->where('operator_id = ?', $opt);
        $select->where('updated > ?', $updated);
        
        $convs = $table->fetchRow($select);
        
        return !is_null($convs);
    }
}