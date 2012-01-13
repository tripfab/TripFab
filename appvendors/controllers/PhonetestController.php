<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhonetestController
 *
 * @author magentodeveloper
 */
class PhonetestController extends Zend_Controller_Action {
    
    public function init()
    {
        
    }
    
    public function indexAction()
    {
        $capability = new Services_Twilio_Capability('ACf96aa655ba664244859b35fef7300bb3', 
                '646c518e51482ab80244d9284eca1667');
        
        $capability->allowClientOutgoing($capability->accountSid);
        
        $this->view->token = $capability->generateToken();
    }
    
    public function requestAction()
    {
        // make an associative array of callers we know, indexed by phone number
        $people = array(
            "+14158675309"=>"Curious George",
            "+14158675310"=>"Boots",
            "+14158675311"=>"Virgil",
            "client:sandbox"=>"Marcel"
        );
        // if the caller is known, then greet them by name
        // otherwise, consider them just another monkey
        if(!$name = $people[$_REQUEST['From']])
                $name = "Monkey";
        
        $this->view->name = $name;
    }
    
    public function keyhandlerAction()
    {
        // if the caller pressed anything but 1 send them back
        if($_REQUEST['Digits'] != '1' and $_REQUEST['Digits'] != '2') {
            $this->_redirect('phonetest/request');
        }
    }
    
    public function recordhandlerAction()
    {
        
    }
    
    public function fallbackAction()
    {
        
    }
    
    public function statusAction()
    {
        
    }
    
    public function smsAction()
    {
        
    }
    
    public function smsfallbackAction()
    {
        
    }
    
    public function smsstatusAction()
    {
        
    }
}