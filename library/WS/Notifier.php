<?php

class WS_Notifier extends Zend_Mail {
    
    /**
     * 
     * @var string
     */
    static $fromName = 'Tripfab';
    
    /**
     * 
     * @var string
     */
    static $fromEmail = 'hello@tripfab.com';
    
    /**
     *
     * @var Zend_View
     */
    static $_defaultView;
    
    /**
     *
     * @var Zend_View
     */
    protected $view;
    
    /**
     *
     * @var WS_UsersService
     */
    protected $users;
    
    /**
     *
     * @var Zend_Db_Table_Row
     */
    protected $user;
    
    /**
     * 
     *  A new partner has just request an invitation to be part of tripfab. We 
     *  need to inform him that we got the request and we are gonna get in touch 
     *  as soon as possible
     * 
     */
    public function newAccountRequest()
    {
        $this->setSubject('Signup Request');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('account-request.phtml');
        
    }
    
    
    public function alertNewSignup($user, $password, $contact, $country, $web)
    {
        $this->setSubject('New Signup Request');
        $this->addTo('ricardo@tripfab.com');
        $this->addBcc('cristian@tripfab.com');
        $this->_view->user     = $user;
        $this->_view->password = $password;
        $this->_view->contact  = $contact;
        $this->_view->country  = $country;
        $this->_view->web      = $web;
        $this->sendHTMLTemplate('new-signup.phtml');
    }
    
    /**
     * 
     *  We just accept the new partner request. So we need to say hello and 
     *  welcome to tripfab
     * 
     */
    public function welcomePartner()
    {
        $this->setSubject('Welcome to Tripfab');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('welcome-partner.phtml');
    }
    
    /**
     * 
     *  The new partner just created his first listing and the listing has been 
     *  reviewed by tripfab but there is information missing or low quality 
     *  pictures. We need to let the partner know about thouse issues
     * 
     */
    public function listingPendingInformation()
    {
        $this->setSubject('Listing Approval');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('listing-pending.phtml');
    }
    
    /**
     * 
     *  The new partner just created his first listing and the listing has been 
     *  approved by tripfab.
     * 
     */
    public function listingApproved()
    {
        $this->setSubject('Listing Approved');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('listing-approved.phtml');
    }
    
    /**
     * 
     *  The partner just got a new reservation from the user who sent the 
     *  message :) So he needs to go to the application and accept the 
     *  reservation
     * 
     */
    public function newReservation($listing, $reservation)
    {
        $this->setSubject('New Reservation');
        $this->addTo($this->user->email);
        $this->_view->listing = $listing;
        $this->_view->reservation = $reservation;
        $this->sendHTMLTemplate('reservation.phtml');
    }
    
    /**
     * 
     *  The user just cancel the reservation. We need to inform the partner of 
     *  the mistake
     * 
     */
    public function reservationCancelled($listing, $client)
    {
        $this->_view->client = $client;
        $this->_view->listing = $listing;
        
        $this->setSubject('Reservation Cancelled');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('reservation-cancelled.phtml');
    }
    
    /**
     * 
     *  Lets say the user just finished the trip a wrote a nice review for the 
     *  hotel. We need to inform the partner about this new review
     * 
     */
    public function newReview($listing, $client)
    {
        $this->setSubject('New Review');
        $this->_view->listing = $listing;
        $this->_view->client = $client;
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('review.phtml');
    }
    
    /**
     * 
     *  The partner want his money, so he has request a new withdraw. We need to 
     *  inform him that we received the request.
     * 
     */
    public function withdrawRequestReceived()
    {
        $this->setSubject('Withdrwal Request Received');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('withdraw-request.phtml');
    }
    
    /**
     * 
     *  We just transfer the money to the partner’s bank account so we need to 
     *  inform the partner that money was transferred successfully.
     * 
     */
    public function withdrawRequestCompleted()
    {
        $this->setSubject('Withdrawal Completed');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('withdraw-completed.phtml');
    }
    
    /**
     * 
     *  A user wants to use or application, so he signed up. We sent him an 
     *  email wih a link to verify the email 
     * 
     */
    public function emailVerification()
    {
        $this->setSubject('Please Verufy your Email');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('email.phtml');
    }
    
    /**
     * 
     *  The user just verify his email. So this is welcome email from us
     * 
     */
    public function welcomeUser()
    {
        $this->setSubject('Welcome to Tripfab');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('welcome.phtml');
    }
    
    /**
     * 
     *  The user has just purchased a Preplanned Trip. This email is for the 
     *  details of the trip
     * 
     */
    public function tripPurchased($reservation, $trip)
    {
        $this->_view->reservation = $reservation;
        $this->_view->trip        = $trip;
        $this->setSubject('Preplaned Itinerary Purchase');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('trip.phtml');
    }
    
    /**
     * 
     *  The partner just accept the user reservation. This email inform the user 
     *  about the good news
     * 
     */
    public function reservationApproved()
    {
        $this->setSubject('Reservation Approved');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('reservation-approved.phtml');
    }
    
    /**
     * 
     *  The user just finished his trip, we know that so we sent him and email 
     *  asking how the trip went and a link so he can write a review for the 
     *  listing he stay/use during the trip
     * 
     */
    public function writeReview()
    {
        $this->setSubject('Hey! How was the trip?');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('write-review.phtml');
    }
    
    /**
     * 
     *  The partner/user just got a new meesage from an user/partner 
     *  You’ve got a new message! Please click here to view it.
     * 
     *  @param string $from 
     */
    public function newMessage($from)
    {
        $from = $this->users->get($from);
        $this->_view->from = $from;
        
        $this->setSubject('New message');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('new-message.phtml');
    }
    
    /**
     * 
     *  The partner/user has request a password reset. We need to send him an 
     *  email with a link he can follow to enter the new password
     * 
     */
    public function passwordReset()
    {
        $this->setSubject('Password Reset');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('password.phtml');
    }
    
    /**
     * 
     *  We need to inform the user/partner about the new password reset that 
     *  just happpen
     * 
     */
    public function passwordResetSuccess()
    {
        $this->setSubject('Your new password');
        $this->addTo($this->user->email);
        $this->sendHTMLTemplate('password-reset.phtml');
    }
    
    /**
     * 
     * Retunrs a Zend View instance
     * 
     *  @return Zend_View
     */
    protected static function getDefaultView()
    {
        if(self::$_defaultView === null) {
            self::$_defaultView = new Zend_View();
            self::$_defaultView->setScriptPath(APPLICATION_PATH.'/views/scripts/mails');
        }
        return self::$_defaultView;
    }
    
    /**
     * 
     *  Send an HTML email based on the template param
     * 
     *  @param string $template
     *  @param string $encoding 
     */
    public function sendHTMLTemplate($template, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $html = $this->_view->render($template);
        $this->setBodyHtml($html);
        $this->setEncodingOfHeaders($encoding);
        $this->send();
    }
    
    /**
     *
     *  Send a plain email
     * 
     *  @param string $to
     *  @param string $subject
     *  @param string $message
     *  @param string $from 
     */
    protected function sendEmail($to, $subject, $message, $from = null){
        if(is_null($from))
            $from = 'From: TripFab <hello@tripfab.com>';
        
        @mail($to, $subject, $message, $from);
    }
    
    /**
     * 
     *  Return a new instance based on the user id
     * 
     *  @param int $user 
     */
    public function __construct($user = null)
    {
        parent::__construct();
        $this->setFrom(self::$fromEmail, self::$fromName);
        $this->_view = self::getDefaultView();
        $this->users = new WS_UsersService();
        
        if(!is_null($user)) {
            $this->user  = $this->users->get($user);
            $this->_view->user = $this->user;
        }
    }
}
