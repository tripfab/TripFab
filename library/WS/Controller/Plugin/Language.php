<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Language
 *
 * @author magentodeveloper
 */
class WS_Controller_Plugin_Language
        extends Zend_Controller_Plugin_Abstract {
    
    protected $extensions = array('png','jpg','jpeg','gif','css','js','swf','php','html');
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) 
    {
        if(isset($_SERVER['REDIRECT_URL'])) {
            if(strpos($_SERVER['REDIRECT_URL'], "ajax") === false &&
                    strpos($_SERVER['REDIRECT_URL'], "admin") === false &&
                    strpos($_SERVER['REDIRECT_URL'], "phone") === false &&
                    strpos($_SERVER['REDIRECT_URL'], "chat") === false){
                
                $finfo = pathinfo($_SERVER['REDIRECT_URL']);
                if(isset($finfo['extension'])) {
                    if(!in_array($finfo['extension'], $this->extensions)) {
                        $this->setLanguage($request); }
                    else {
                        if(!file_exists($_SERVER['REDIRECT_URL'])) {
                            header('Location: http://tripfab.com'.$_SERVER['REDIRECT_URL']); exit; } }
                } else {
                    $this->setLanguage($request);}
            }
        } else {
            $this->setLanguage($request); }
    }
    
    private function setLanguage($request)
    {
        $lang = $request->getParam('lang');
        $languages = array('en'=>'en-US','es'=>'es-ES');

        if(in_array($lang, $languages)){
            $locale     = new Zend_Locale($lang);
            $translate  = new Zend_Translate('csv',APPLICATION_PATH.'/languages/'.$lang.'.csv',$lang);
            Zend_Registry::set('Zend_Locale',$lang);
            Zend_Registry::set('Zend_Translate', $translate);
        } else {
            header('Location: /en-US'.$_SERVER['REDIRECT_URL']); exit;
        }
    }
    
}

?>
