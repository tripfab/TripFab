<?php

class WS_BanksService {

    protected $banks;
	private static $_instance = NULL;

    public function __construct() {
        $this->banks = new Model_Banks();
    }

    public static function this(){
        if (!self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }

    public function getBanksBy($country=null) {
        $select = $this->banks->select();
        if($country){
			$select->where('country_id = ?', $country);
		}
        return $this->banks->fetchAll($select);
    }

}

?>
