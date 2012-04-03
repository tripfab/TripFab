<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReservationsService
 *
 * @author magentodeveloper
 */
class WS_ReservationsService {
    
    /**
     *
     * @var Model_Reservations
     */
    protected $reservations;
    
    public function __construct() {
        $this->reservations = new Model_Reservations();
    }
    public function countPending($vendor)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $count = $db->fetchRow('select count(*) as total from reservations where (vendor_id = '.$vendor.' and status_id = 2)');
        return $count['total'];
    }
    
    public function countConfirmed($user)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $count = $db->fetchRow('select count(*) as total from reservations where (user_id = '.$user.' and status_id = 1)');
        return $count['total'];
    }
    
    public function getIncoming($vendor)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reservations');
        $select->join('users','reservations.user_id = users.id',array(
            'username'=>'name',
            'userimage'=>'image'
        ));
        $select->join('listings','reservations.listing_id = listings.id',array('listing_name'=>'title'));
        $select->where('reservations.vendor_id = ?', $vendor);
        $select->where('reservations.status_id = ?',2);
        
        $_reservs = $db->fetchAll($select);
        
        $reservations = array();
        $dates = array();
        $listings = array();
        foreach($_reservs as $r){
            //print $r['checkin'].'<br/>';
            if(!in_array($r['checkin'], $dates)){
                $dates[] = $r['checkin'];
                $listings[$r['checkin']] = array();
                $reservations[$r['checkin']]['day_num'] = date('d', strtotime($r['checkin']));
                $reservations[$r['checkin']]['day_name']= date('M Y', strtotime($r['checkin']));;
                $reservations[$r['checkin']]['listings'] = array();
                foreach($_reservs as $rr){
                    if($r['checkin'] == $rr['checkin']){
                        if(!in_array($rr['listing_id'], $listings[$r['checkin']])){
                            $listings[$r['checkin']][] = $rr['listing_id'];
                            $reservations[$r['checkin']]['listings'][$rr['listing_id']]['name'] = $rr['listing_name'];
                            $reservations[$r['checkin']]['listings'][$rr['listing_id']]['bookings'] = array();
                            foreach($_reservs as $rrr){
                                if($rrr['checkin'] == $r['checkin']){
                                    if($rrr['listing_id'] == $rr['listing_id']){
                                        $reservations[$r['checkin']]['listings'][$rr['listing_id']]['bookings'][] = array(
                                            'user' => $rr['username'],
                                            'code' => $rr['code'],
                                            'image'=> $rr['userimage'],
                                            'id'   => $rrr['id'],
                                        );                                        
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }    
        
        return $reservations;
    }
    
    public function getPending($vendor)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reservations');
        $select->join('vendors','reservations.vendor_id = vendors.id',array(
            'vendor_name'  =>'name'
        ));
        $select->join('listings','reservations.listing_id = listings.id',array(
            'listing_name'=>'title',
            'listing_image'=>'image',
        ));
        $select->join('listing_types','listings.main_type = listing_types.id',array('listing_type'=>'name'));
        $select->join('transactions','reservations.transaction_id = transactions.id',array('method'));
        $select->joinLeft('listing_schedules', 'reservations.schedule_id = listing_schedules.id', array(
            'schedule_name'     => 'name',
            'schedule_starting' => 'starting',
            'schedule_ending'   => 'ending'
        ));
        $select->join('users','reservations.user_id = users.id', array(
            'user_name'         => 'name',
            'user_image'        => 'image',
        ));
        $select->where('reservations.vendor_id = ?', $vendor);
        $select->where('reservations.status_id = ?',1);
        
        $_reservs = $db->fetchAll($select, array(), 5);
        
        //echo '<pre>'; print_r($_reservs); echo '</pre>'; die;
        
        return $_reservs;
    }
    
    public function getHistory($vendor)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reservations');
        $select->join('vendors','reservations.vendor_id = vendors.id',array(
            'vendor_name'  =>'name'
        ));
        $select->join('listings','reservations.listing_id = listings.id',array(
            'listing_name'=>'title',
            'listing_image'=>'image',
        ));
        $select->join('users','reservations.user_id = users.id', array(
            'user_name'         => 'name',
            'user_image'        => 'image',
        ));
        $select->join('listing_types','listings.main_type = listing_types.id',array('listing_type'=>'name'));
        $select->join('transactions','reservations.transaction_id = transactions.id',array('method'));
        $select->where('reservations.vendor_id = ?', $vendor);
        $select->where('reservations.status_id = ?',3);
        
        $_reservs = $db->fetchAll($select, array(), 5);
        
        return $_reservs;
    }
    
    public function getUserConfirmed($user)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reservations');
        $select->join('listings','reservations.listing_id = listings.id',array(
            'listing_name'=>'title',
            'listing_image'=>'image',
            'listing_idf'=>'identifier'
        ));
        $select->join('vendors','reservations.vendor_id = vendors.id',array('vendor_name'=>'name'));
        $select->join('listing_types','listings.main_type = listing_types.id',array('listing_type'=>'name'));
        $select->join('transactions','reservations.transaction_id = transactions.id',array('method'));
        $select->join('places','listings.city_id = places.id',array('listing_city'=>'identifier'));
        $select->join(array('places2'=>'places'),'listings.country_id = places2.id',array('listing_country'=>'identifier'));
        $select->where('reservations.user_id = ?', $user);
        $select->where('reservations.status_id = ?',2);
        
        $reservations = $db->fetchAll($select, array(), 5);
        
        //echo '<pre>'.var_dump($_reservs).'</pre>'; die;
        /*
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reservations');
        $select->join('users','reservations.user_id = users.id',array('username'=>'name'));
        $select->join('listings','reservations.listing_id = listings.id',array('listing_name'=>'title'));
        $select->where('reservations.vendor_id = ?', $vendor);
        $select->where('reservations.status_id = ?',2);
        
        $_reservs = $db->fetchAll($select);
        
        $reservations = array();
        $dates = array();
        $listings = array();
        foreach($_reservs as $r){
            print $r['checkin'].'<br/>';
            if(!in_array($r['checkin'], $dates)){
                $dates[] = $r['checkin'];
                $listings[$r['checkin']] = array();
                $reservations[$r['checkin']]['day_num'] = date('d', strtotime($r['checkin']));
                $reservations[$r['checkin']]['day_name']= date('M Y', strtotime($r['checkin']));;
                $reservations[$r['checkin']]['listings'] = array();
                foreach($_reservs as $rr){
                    if($r['checkin'] == $rr['checkin']){
                        if(!in_array($rr['listing_id'], $listings[$r['checkin']])){
                            $listings[$r['checkin']][] = $rr['listing_id'];
                            $reservations[$r['checkin']]['listings'][$rr['listing_id']]['name'] = $rr['listing_name'];
                            $reservations[$r['checkin']]['listings'][$rr['listing_id']]['bookings'] = array();
                            foreach($_reservs as $rrr){
                                if($rrr['checkin'] == $r['checkin']){
                                    if($rrr['listing_id'] == $rr['listing_id']){
                                        $reservations[$r['checkin']]['listings'][$rr['listing_id']]['bookings'][] = array(
                                            'user' => $rr['username'],
                                            'code' => $rr['code']
                                        );                                        
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }    
        */
        return $reservations;
    }
    
    public function getUserPending($user){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reservations');
        $select->join('vendors','reservations.vendor_id = vendors.id',array(
            'vendor_name'  =>'name'
        ));
        $select->join('listings','reservations.listing_id = listings.id',array(
            'listing_name'=>'title',
            'listing_image'=>'image',
            'listing_idf'=>'identifier'
        ));
        $select->join('listing_types','listings.main_type = listing_types.id',array('listing_type'=>'name'));
        $select->join('transactions','reservations.transaction_id = transactions.id',array('method'));
        $select->join('places','listings.city_id = places.id',array('listing_city'=>'identifier'));
        $select->join(array('places2'=>'places'),'listings.country_id = places2.id',array('listing_country'=>'identifier'));
        $select->joinLeft('listing_schedules', 'reservations.schedule_id = listing_schedules.id', array(
            'schedule_name'     => 'name',
            'schedule_starting' => 'starting',
            'schedule_ending'   => 'ending'
        ));
        $select->where('reservations.user_id = ?', $user);
        $select->where('reservations.status_id = ?',1);
        
        $_reservs = $db->fetchAll($select, array(), 5);
        
        //echo '<pre>'; print_r($_reservs); echo '</pre>'; die;
        
        return $_reservs;
    }
    
    public function getUserHistory($user){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('reservations');
        $select->join('vendors','reservations.vendor_id = vendors.id',array(
            'vendor_name'  =>'name'
        ));
        $select->join('listings','reservations.listing_id = listings.id',array(
            'listing_name'=>'title',
            'listing_image'=>'image',
        ));
        $select->join('listing_types','listings.main_type = listing_types.id',array('listing_type'=>'name'));
        $select->join('transactions','reservations.transaction_id = transactions.id',array('method'));
        $select->where('reservations.user_id = ?', $user);
        $select->where('reservations.status_id = ?',3);
        
        $_reservs = $db->fetchAll($select, array(), 5);
        
        return $_reservs;
    }
    
    public function create($transaction, $cart, $listing)
    {
        $data = $cart->toArray();
        $data['ammount']        = $data['total'];
        $data['schedule_id']    = $data['option_id'];
        $data['vendor_id']      = $listing->vendor_id;
        $data['transaction_id'] = $transaction->id;
        $data['status_id']      = 1;
        $data['code']           = $transaction->code;
        $data['created']        = date('Y-m-d H:i:s');
        
        unset($data['id']);
        unset($data['option_id']);
        unset($data['total']);
        unset($data['token']);
        
        $row = $this->reservations->fetchNew();
        $row->setFromArray($data);
        $row->save();
        
        return $row;
    }
    
    public function createOfTrip($transaction, $item, $listing, $cart)
    {
        $data = $item->toArray();
        $data['ammount']        = $data['total'];
        $data['schedule_id']    = $data['option_id'];
        $data['vendor_id']      = $listing->vendor_id;
        $data['transaction_id'] = $transaction->id;
        $data['status_id']      = 1;
        $data['code']           = $transaction->code;
        $data['created']        = date('Y-m-d H:i:s');
        $data['user_id']        = $cart->user_id;
        
        unset($data['id']);
        unset($data['option_id']);
        unset($data['total']);
        unset($data['token']);
        
        $row = $this->reservations->fetchNew();
        $row->setFromArray($data);
        $row->save();
        
        return $row;
    }
    
    public function get($id, $user = null, $vendor = false)
    {
        $select = $this->reservations->select();
        $select->where('id = ?', $id);
        if(!is_null($user)){
            if($vendor)
                $select->where('vendor_id = ?', $user);
            else
                $select->where('user_id = ?', $user);
        }
        
        $reservation = $this->reservations->fetchRow($select);
        return $reservation;
    }
    
    public function getTransaction($id)
    {
        $trans = new Zend_Db_Table('transactions');
        $select = $trans->select();
        $select->where('id = ?', $id);
        $t = $trans->fetchRow($select);
        
        return $t;
    }
    
    public function details($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();

        $select->from('reservations')
                ->join('listings', 'reservations.listing_id = listings.id', array('listing_name' => 'title', 'listing_image' => 'image'))
                ->join('listing_types', 'listings.main_type = listing_types.id', array('listing_type' => 'name'))
                ->join('users', 'users.id=reservations.user_id', array('user_name' => 'name'))
                ->join('vendors', 'vendors.id=reservations.vendor_id', array('vendor_name' => 'name'))
                ->where('reservations.id=?', $id);
        return $db->fetchRow($select);
    }
    
    public function getUserReservation($user, $listing, $status)
    {
        $select = $this->reservations->select();
        $select->where('user_id = ?', $user);
        $select->where('listing_id = ?', $listing);
        $select->where('status_id = ?', $status);
        
        $reservation = $this->reservations->fetchRow($select);
        
        return $reservation;
    }
	
	public function getPendingByDate($vendor, $date)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();
        $select->from('reservations');
        $select->join('vendors','reservations.vendor_id = vendors.id',array(
            'vendor_name'  =>'name'
        ));
        $select->join('listings','reservations.listing_id = listings.id',array(
            'listing_name'=>'title',
            'listing_image'=>'image',
            'listing_idf'=>'identifier'
        ));
        $select->join('listing_types','listings.main_type = listing_types.id',array('listing_type'=>'name'));
        $select->join('transactions','reservations.transaction_id = transactions.id',array('method'));
        $select->join('users','reservations.user_id = users.id', array(
            'user_name'         => 'name',
            'user_image'        => 'image',
        ));
        $select->where('reservations.vendor_id = ?', $vendor);
        $select->where('reservations.status_id = ?',1);
		$select->where('reservations.created < ?' , $date);
		$select->where('reservations.created >= DATE_SUB(reservations.created, INTERVAL 7 DAY)') ;
        
        $_reservs = $db->fetchAll($select);
        return $_reservs;
    }
	
	public function getByPaidDate($vendor, $date)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();
        $select->from('reservations');
        $select->join('vendors','reservations.vendor_id = vendors.id',array(
            'vendor_name'  =>'name'
        ));
        $select->join('listings','reservations.listing_id = listings.id',array(
            'listing_name'=>'title',
            'listing_image'=>'image',
            'listing_idf'=>'identifier'
        ));
        $select->join('listing_types','listings.main_type = listing_types.id',array('listing_type'=>'name'));
        $select->join('transactions','reservations.transaction_id = transactions.id',array('method'));
        $select->join('users','reservations.user_id = users.id', array(
            'user_name'         => 'name',
            'user_image'        => 'image',
        ));
        $select->where('reservations.vendor_id = ?', $vendor);
        $select->where('reservations.status_id != ?',1);
		$select->where('reservations.paid_date = ?' , $date);
        
        $_reservs = $db->fetchAll($select);
        return $_reservs;
    }
	

	
	public function markPaidByDate($vendor, $date){
		$this->reservations->update(array('status_id'=>3), "vendor_id = $vendor and created < '$date' and created >= DATE_SUB(created, INTERVAL 7 DAY)");
		return true;
	}
    
}