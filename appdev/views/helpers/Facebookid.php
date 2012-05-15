<?php

class Zend_View_Helper_Facebookid {
    
    public function facebookid(){
        $config = Zend_Registry::get('facebook');
        return $config['id'];
    }
    
}
