<?php

class WS_User {
    
    protected $users_db;
    protected $vendors_db;
    protected $roles_db;
    protected $questions_db;
    
    protected $vendor;
    protected $user;
    protected $data;
    
    protected $user_role;
    protected $email_settings;
    protected $user_emailsettings;
    
    protected $user_friends;
    
    public function __construct($data) 
    {
        $this->users_db = new Model_Users();
        $this->vendors_db = new Model_Vendors();
        $this->questions_db = new Model_SecurityQuestions();
        $this->email_settings = new Model_EmailSettings();
        $this->user_emailsettings = new Model_UserEmailsettings();
        $this->user_friends = new Model_UserFriends();
        if($data == 'loggedin'){
            $select = $this->users_db->select();
            $select->where('id = ?', 1);
            $this->user = $this->users_db->fetchRow($select);
            if(is_null($this->user))
                throw new Exception();
            
            $select = $this->vendors_db->select();
            $select->where('user_id = ?', $this->user->id);
            $this->vendor = $this->vendors_db->fetchRow($select);
            if(is_null($this->vendor))
                throw new Exception();                  
        } else {
            $this->users_db = new Model_Users();
            $this->vendors_db = new Model_Vendors();
            
            $this->user = $this->users_db->fetchRow('id = '.$data->id);

            if($this->getRole() == 'provider'){
                $select = $this->vendors_db->select();
                $select->where('user_id = ?', $this->user->id);
                $this->vendor = $this->vendors_db->fetchRow($select);
                if(is_null($this->vendor))
                    throw new Exception();
            } else {
                
            }
        }     
    }
    
    
    private function _setRole()
    {
        if(is_null($this->roles_db))
            $this->roles_db = new Model_Roles();
        
        $select = $this->roles_db->select();
        $select->where('id = ?',$this->user->role_id);
        $this->user_role = $this->roles_db->fetchRow($select);                
    }
    
    public function getRole($id = false)
    {
        if(is_null($this->user_role))
            $this->_setRole();
        if($id)
            return $this->user_role->id;
        return $this->user_role->name;
    }
    
    public function getVendorId()
    {
        return $this->vendor->id;
    }
    
    /**
     *
     * @return Zend_Db_Table_Row
     */
    public function getData($fresh = false)
    {
        if($fresh)
            $this->refreshUserData();
        
        return $this->user;
    }
    
    private function refreshUserData() {
        $select = $this->users_db->select();
        
        $select->where('id = ?', $this->user->id);
        
        $this->user = $this->users_db->fetchRow($select);
    }
    
    private function refreshVendorData()
    {
        $select = $this->vendors_db->select();
        $select->where('id = ?', $this->vendor->id);
        $this->vendor = $this->vendors_db->fetchRow($select);
    }
    
    public function getVendorData($fresh = false)
    {
        if($fresh)
            $this->refreshVendorData();
        
        return $this->vendor;
    }
    
    public function getContacts()
    {
        $contacts_db = new Zend_Db_Table('contacts');
        $select = $contacts_db->select();
        $select->where('vendor_id = ?', $this->vendor->id);
        $contacts = $contacts_db->fetchAll($select);
        return $contacts;
    }
    
    public function updateContacts($data)
    {
        $contacts = $this->getContacts();
        foreach($contacts as $contact){
            if(!isset($data[$contact->id]))
                $contact->delete();
        }
    }
    
    public function addContact($data){
        $contacts = new Zend_Db_Table('contacts');
        $contact = $contacts->fetchNew();
        $contact->setFromArray($data);
        $contact->vendor_id = $this->getVendorId();
        $contact->created   = date('Y-m-d H:i:s');
        $contact->updated   = date('Y-m-d H:i:s');
        $contact->save();
    }
    
    public function updateContact($id, $data){
        $contacts = new Zend_Db_Table('contacts');
        $select = $contacts->select();
        $select->where('id = ?', $id);
        $contact = $contacts->fetchRow($select);
        
        $contact->setFromArray($data);
        $contact->updated = date('Y-m-d H:i:s');
        $contact->save();
    }
    
    public function getId()
    {
        return $this->user->id;
    }
    
    public function getToken()
    {
        return $this->user->token;
    }
    
    public function setVendorImage($image)
    {
        $this->vendor->image = '/images/providers/'.$image;
        $this->vendor->save();
    }
    
    public function setName($name)
    {
        $this->user->name = $name;
        $this->user->save();
    }
    
    public function setEmail($email)
    {
        $this->user->email = $email;
        $this->user->save();
    }
    
    public function setPasword($data)
    {
        if($this->user->password == md5($data['password'])){
            $this->user->password = md5($data['new_password']);
            $this->user->save();
        } else 
            throw new Exception();
    }
    
    public function setImage($image)
    {
        if(empty($this->user->image))
            $this->user->complete = $this->user->complete + 10;
        
        $this->user->image = '/images/users/'.$image;
        $this->user->save();
    }
    
    public function setPhone($phone){
        if(empty($this->user->phone))
            $this->user->complete = $this->user->complete + 10;
        if(empty($phone))
            $this->user->complete = $this->user->complete - 10;
        $this->user->phone = $phone;
        $this->user->save();
    }
    public function setGender($gender){
        if(empty($this->user->gender))
            $this->user->complete = $this->user->complete + 10;
        $this->user->gender = $gender;
        $this->user->save();
    }
    public function setBirthdate($date){
        if(is_null($this->user->birthdate))
            $this->user->complete = $this->user->complete + 10;
        if(empty($date)){
            $this->user->complete = $this->user->complete - 10;
            $date = null;
        }
        $this->user->birthdate = $date;
        $this->user->save();
    }
    public function setLocation(){
        if(empty($this->user->location))
            $this->user->complete = $this->user->complete + 10;
        
        $places  = new Model_Places();
        $country = $places->fetchRow('id = '.$_POST['country_id']);
        $city    = $places->fetchRow('id = '.$_POST['city_id']);
        if($city->parent_id == $country->id){
            $this->user->country_id = $country->id;
            $this->user->city_id    = $city->id;
            $this->user->location   = $city->title .', '.$country->title;
            $this->user->save();
        } else 
            throw new Exception();        
    }
    
    public function setWork($work){
        if(empty($this->user->work))
           $this->user->complete = $this->user->complete + 10;
        if(empty($work))
            $this->user->complete = $this->user->complete - 10;
        $this->user->work = $work;
        $this->user->save();
    }
    public function setLanguages($languages){
        if(empty($this->user->languages))
            $this->user->complete = $this->user->complete + 10;
        if(empty($languages))
            $this->user->complete = $this->user->complete - 10;
        $this->user->languages = $languages;
        $this->user->save();
    }
    public function setQuestion($data)
    {
        if(empty($this->user->question))
            $this->user->complete = $this->user->complete + 10;
        if(empty($data['question'])){
            $this->user->complete = $this->user->complete - 10;
            $this->user->question = '';
            $this->user->answer   = '';
        } else {
            $this->user->question = $data['question'];
            $this->user->answer   = $data['answer'];
        }
        $this->user->save();
    }
    
    public function getCountryId()
    {
        return $this->user->country_id;
    }
    
    public function getDefaultQuestions()
    {
        return $this->questions_db->fetchAll();
    }
    
    public function getDefaultNotifications()
    {
        $ntfs = $this->email_settings->fetchAll();
        $used = array();
        $result = array();
        foreach($ntfs->toArray() as $ntf){
            if(!in_array($ntf['type'], $used)){
                $used[] = $ntf['type'];
                $result[$ntf['type']] = array();
                foreach($ntfs as $ntf2){
                    if($ntf['type'] == $ntf2->type){
                        $result[$ntf['type']][] = $ntf2;
                    }
                }
            }
        }
        //echo '<pre>'; var_dump($ntfs); echo '</pre>'; die;
        return $result;
    }
    
    public function getNotificationsSettings()
    {
        $select = $this->user_emailsettings->select();
        $select->where('user_id = ?', $this->user->id);
        $settings = $this->user_emailsettings->fetchAll($select);
        $result = array();
        foreach($settings as $st)
            $result[] = $st->setting_id;
        
        return $result;
    }
    
    public function setNotifications($data)
    {
        $this->user_emailsettings->delete('user_id = '.$this->user->id);
        if(isset($data['ntf'])){
            foreach($data['ntf'] as $ntf){
                $row = $this->user_emailsettings->fetchNew();
                $row->user_id = $this->user->id;
                $row->setting_id = $ntf;
                $row->save();
            }
        } 
    }
    
    public function getFriends()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('user_friends');
        $select->where('user_friends.user1_id = ?', $this->user->id);
        $select->orWhere('user_friends.user2_id = ?', $this->user->id);
        
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
    
    public function getName(){
        return $this->user->name;
    }
    
    public function getEmail(){
        return $this->user->email;
    }
    
    public function getFavotites(){
        $_favorites = new Zend_Db_Table('favorites');
        $select = $_favorites->select();
        $select->where('user_id = ?', $this->user->id);
        $rows = $_favorites->fetchAll($select);
        $favorites = array();
        foreach($rows as $row)
            $favorites[] = $row->listing_id;
        
        return $favorites;
    }
    
    public function getVendorToken()
    {
        return $this->vendor->token;
    }
    
    public function getAccounts()
    {
        $accounts = new Zend_Db_Table('stripe_accounts');
        $select = $accounts->select();
        $select->where('user_id = ?', $this->user->id);
        $result = $accounts->fetchAll($select);
        
        return $result;
    }
    
    public function getAccount($id)
    {
        $accounts = new Zend_Db_Table('stripe_accounts');
        $select = $accounts->select();
        $select->where('user_id = ?', $this->user->id);
        $select->where('id = ?', $id);
        $result = $accounts->fetchRow($select);
        
        if(is_null($result))
            throw new Exception();
        
        return $result;
    }
    
    public function getBankAccounts()
    {
        $accounts = new Zend_Db_Table('bankaccounts');
        $select = $accounts->select();
        $select->where('vendor_id = ?', $this->user->id);
        $result = $accounts->fetchAll($select);
        
        return $result;
    }
    
    public function getBankAccount($id)
    {
        $accounts = new Zend_Db_Table('bankaccounts');
        $select = $accounts->select();
        $select->where('vendor_id = ?', $this->user->id);
        $select->where('id = ?', $id);
        $result = $accounts->fetchRow($select);
        
        if(is_null($result))
            throw new Exception();
        
        return $result;
    }
}
