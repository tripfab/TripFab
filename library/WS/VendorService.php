<?php

class WS_VendorService {

    protected $vendors;

    public function __construct() {
        $this->vendors = new Model_Vendors();
    }

    public function getVendorById($ids) {
        $select = $this->vendors->select();
        $select->where('id = ?', $ids);
        $vendor = $this->vendors->fetchRow($select);
        if (is_null($vendor))
            throw new Exception();

        return $vendor;
    }

    public function getVendorDetailsById($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();
        $select->from('vendors')
                ->join(array('users'), 'vendors.user_id=users.id', array('userName' => 'name','active'=>'active', 'user_id'=>'id'))
                ->joinleft(array('city' => 'places'), 'users.city_id=city.id', array('city_id'=>'id','cityName' => 'title'))
                ->joinleft(array('country' => 'places'), 'users.country_id=country.id', array('country_id'=>'id','countryName' => 'title'))
                ->where('vendors.id = ?', $id);

        $vendor = $db->fetchRow($select);
        if (is_null($vendor))
            throw new Exception();

        return $vendor;
    }

    public function getOffersBy($vendor) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select();
        $select->from('offers')
                ->join(array('listings'), 'offers.listing_id=listings.id', array('listing_name' => 'title', 'listing_id' => 'id'))
                ->join('listing_types', 'listings.main_type = listing_types.id', array("type_name" => "name"))
                ->where('listings.vendor_id = ?', $vendor);

        $offers = $db->fetchAll($select);
        return $offers;
    }
	public function saveInfo($id,$name, $email, $contact_name, $date, $phone, $city, $country, $website, $user_id){
		$vendor = new Model_Vendors();
		$vendor->update(array("id"=>$id,"name"=>$name,"email"=>$email,"contact_name"=>$contact_name,"created"=>$date,"phone"=>$phone,"website"=>$website), $vendor->getAdapter()->quoteInto('id = ?', $id));
		$user = new Model_Users();
		$user->update(array("city_id"=>$city,"country_id"=>$country), $user->getAdapter()->quoteInto('id = ?', $user_id));
	
	}

}

?>
