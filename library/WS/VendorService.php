<?php

class WS_VendorService {
    
    protected $vendors;
    
    public function __construct() 
    {
        $this->vendors = new Model_Vendors();
    }
    
    public function getVendorById($ids)
    {
        $select = $this->vendors->select();
        $select->where('id = ?', $ids);
        $vendor = $this->vendors->fetchRow($select);
        if(is_null($vendor))
            throw new Exception();
        
        return $vendor;
    }
    
}

?>
