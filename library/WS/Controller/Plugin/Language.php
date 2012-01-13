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
        if(strpos($_SERVER['REDIRECT_URL'], "ajax") === false &&
                strpos($_SERVER['REDIRECT_URL'], "admin") === false){
            $finfo = pathinfo($_SERVER['REDIRECT_URL']);
            if(!in_array($finfo['extension'], $this->extensions)){
                //echo "<pre>"; print_r($finfo); echo '</pre>'; die;
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
            } else {
                if(!file_exists($_SERVER['REDIRECT_URL'])){
                    header('Location: http://tripfab.com'.$_SERVER['REDIRECT_URL']); exit;
                }
            }
        }
    }
    
}

?>
