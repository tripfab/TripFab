<?php

class WS_AccountService {
 
    protected $users_db;
    protected $vendors_db;
    
    public function __construct() {
        $this->users_db   = new Model_Users();
        $this->vendors_db = new Model_Vendors();
    }
    public function validateEmail($email){
        
        $select = $this->users_db->select();
        $select->where('email = ?', $email);
        $test1 = $this->users_db->fetchRow($select);
        if(!is_null($test1))
            return false;
        
        $select = $this->vendors_db->select();
        $select->where('email = ?', $email);
        $test2 = $this->users_db->fetchRow($select);
        if(!is_null($test2))
            return false;
        
        return true;
    }
    
    public function validateUsername($username){
        $select = $this->users_db->select();
        $select->where('username = ?', $username);
        $test1 = $this->users_db->fetchRow($select);
        if(!is_null($test1))
            return false;        
        return true;
    }
    
    public function signup($data, $fb = false){
        $user = $this->users_db->fetchNew();
        if($fb){
            $user->name     = $data['name'];
            $user->email    = $data['email'];
            $user->gender   = ucfirst($data['gender']);
            $password       = substr(md5($data['email']), 5, 14);
            $user->password = md5($password);
            $user->password_hint = substr(md5(md5($password)), 3, 10).$password.substr(md5(md5($password)), 0, 7);
            $user->role_id  = 2;
            $user->token    = md5($data['email'].time());
            $user->created  = date('Y-m-d H:i:s');
            $user->updated  = date('Y-m-d H:i:s');
            $user->complete = 30;
            $user->save();
            
            $notifier = new WS_Notifier();
            $notifier->newSignup($user->email, $password, $user->name);
            
            $this->login($user->email, $password);
        } else {
            $user->name       = $data['name'];
            $user->lname      = $data['lname'];
            $user->email      = $data['email'];
            $password         = $data['password'];
            $user->password   = md5($password);
            $user->password_hint = substr(md5(md5($password)), 3, 10).$password.substr(md5(md5($password)), 0, 7);
            $user->role_id    = 2;
            $user->token      = md5($data['email'].time());
            $user->created    = date('Y-m-d H:i:s');
            $user->updated    = date('Y-m-d H:i:s');
            $user->country_id = $data['country_id'];
            $user->save();
            
            $notifier = new WS_Notifier();
            $notifier->newSignup($user->email, $password, $user->name);
            
            $this->login($user->email, $password);
        }
        //var_dump($data); die;
    }
    
    public function signupVendor($data)
    {
        $settings = new Zend_Db_Table('email_settings');
        $select = $settings->select();
        $select->orWhere('type = ?',1);
        $select->orWhere('type = ?',2);
        $defaultsVdr = $settings->fetchAll($select);
        
        $user = $this->users_db->fetchNew();
        
        $user->name       = $data['name'];
        $user->email      = trim($data['email']);
        $user->phone      = $data['phone'];
        $password         = $data['password'];
        $user->password   = md5($password);
        $user->password_hint = substr(md5(md5($password)), 3, 10).$password.substr(md5(md5($password)), 0, 7);
        $user->role_id    = 3;
        $user->token      = md5($data['email'].time());
        $user->created    = date('Y-m-d H:i:s');
        $user->updated    = date('Y-m-d H:i:s');
        $user->country_id = $data['country_id'];
        $user->save();
        
        $vendor = $this->vendors_db->fetchNew();
        
        $vendor->name     = $data['name'];
        $vendor->website  = $data['website'];
        $vendor->phone    = $user->phone;
        $vendor->email    = $user->email;
        $vendor->user_id  = $user->id;
        $vendor->token    = md5('vendor'.$data['email'].time());
        $vendor->created  = date('Y-m-d H:i:s');
        $vendor->updated  = date('Y-m-d H:i:s');
        
        $vendor->save();
        
        $user_settings = new Zend_Db_Table('users_emailsettings');
        foreach($defaultsVdr as $n){
            $new = $user_settings->fetchNew();
            $new->user_id = $user->id;
            $new->setting_id = $n->id;
            $new->save();
        }
        
        if(!empty($data['contact'])){
            
            $contact_data = array(
                'fname' => $data['contact'],
                'lname' => '',
                'position' => '',
                'email' => $data['email'],
                'code'  => '',
                'phone' => $data['phone'],
                'ext'   => '',
            );
            $contacts = new Zend_Db_Table('contacts');
            $contact = $contacts->fetchNew();
            $contact->setFromArray($contact_data);
            $contact->vendor_id = $vendor->id;
            $contact->created   = date('Y-m-d H:i:s');
            $contact->updated   = date('Y-m-d H:i:s');
            $contact->save();
        }

        $notifier = new WS_Notifier();
        $notifier->newSignup($user->email, $password, $user->name);

        $this->login($user->email, $password);
    }
    
    public function login($data, $pw, $fb = false){
        if(!$fb){
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity()){
                throw new Exception('User logged in');
            } else {
                $adapter = new Zend_Auth_Adapter_DbTable(null, 'users','email','password');
                $adapter->setIdentity($data);
                $adapter->setCredential(md5($pw));
                $result = $adapter->authenticate();
                if($result->isValid()){
                    $user = $adapter->getResultRowObject();
                    $auth->getStorage()->write($user);
                    return true;
                } else {
                    $errors = $result->getMessages();
                    return $errors;
                }
            }
        } else {
            $select = $this->users_db->select();
            $select->where('email = ?', $data['email']);
            $user = $this->users_db->fetchRow($select);
            if(!is_null($user)){
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity()){
                    throw new Exception('User logged in');
                } else {
                    $auth->getStorage()->write($user);
                }
            } else {
                throw new Exception();                
            }
        }
    }
}