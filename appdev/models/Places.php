<?php

class Model_Places extends Zend_Db_Table_Abstract
{

    protected $_name = 'places';

    public function getCountry($idn)
    {
        $select = $this->select();
        $select->where('identifier = ?', $idn);
        $data = $this->fetchRow($select);
        if(!is_null($data)){
            $country = new WS_Country($data->toArray());
            
        } else {
            throw new Exception;
        }
    }
}

