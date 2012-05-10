<?php

class Zend_View_Helper_Formatnumber {

    public function formatnumber($number) {
        
        if(strpos($number,'.') !== false) {
            $aux = explode('.',$number);
            $decimals = (int) $aux[1];
            $price = $aux[0];
        } else {
            $decimals = 0;
            $price = $number;
        }
        
        return ($decimals > 0) ? number_format($number, 2) : number_format($price, 0);
        
    }
    
}
