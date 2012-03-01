<?php

class WS_Notifier {
    
    public function __construct() {
        
    }
    
    public function sendEmail($to, $subject, $message, $from = null){
        if(is_null($from))
            $from = 'From: TripFab <hello@tripfab.com>';
        
        @mail($to, $subject, $message, $from);
    }
    
    public function newSignup($email, $pass, $name){
        $to = $email;
        $subject = "Welcome to TripFab.com";
        $message = "Hi {$name} \n";
        $message.= "Welcome to tripfab.com \n\n";
        $message.= "You can login to you new account with your email \n";
        $message.= "and the following password: \n\n";
        $message.= "Password: {$pass} \n\n";
        $message.= "Please change your password ASAP for something you can remember";
        
        $this->sendEmail($to, $subject, $message);
    }
    
    public function sendInvites($emails, $from){
        $subject = "{$from->name} has invite you to join TripFab.com";
        $message = "Hi there\n\n";
        $message.= "{$from->name} has invite to enjoy the benefits of planning your own vacations at tripfab.com\n\n";
        $message.= "Join here http://cristiantests.com/signup";
        
        $from = "From: {$from->name} <{$form->email}>";
        foreach($emails as $email){ 
            $this->sendEmail($email, $subject, $message, $from);
        }
    }
    
    public function passwordReset($name, $email, $password){
        $to = $email;
        $subject = "Password Reset";
        $message = "Hi {$name} \n";
        $message.= "We've been asked to reset your account password \n\n";
        $message.= "You can login to you account with your email \n";
        $message.= "and the following new password: \n\n";
        $message.= "Password: {$password} \n\n";
        $message.= "Please, if you haven't asked for this reset contact us ASAP";
        
        $this->sendEmail($to, $subject, $message);
    }
    
}
