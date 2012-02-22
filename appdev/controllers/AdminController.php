<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminController
 *
 * @author magentodeveloper
 */
class AdminController extends Zend_Controller_Action {
    
    /**
     *
     * @var WS_User
     */
    protected $user;
    
    /**
     *
     * @var WS_Admin
     */
    protected $admin;
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
            $this->_redirect('/en-US/login?b='.$_SERVER['REQUEST_URI']);
        else {
            $this->user = new WS_User($auth->getStorage()->read());
            if($this->user->getRole() != 'admin'){
                throw new Exception();
            } else {
                $this->admin = new WS_Admin();
                $this->view->user = $this->user->getData();
            }
        }
    }
    
    
    /**
     * Manage all listings
     */
    public function listingsAction()
    {
        $template = 'listings';
        switch($this->_getParam('task')){
            case 'default':
                $this->listingsDefaultTask();
                break;
            case 'edit':
                $this->listingsEditTask();
                $template = 'listing-edit';
                break;
            default: throw new Exception('Page not Found', 404, $previous);
        }
        $this->render($template);
    }
    
    private function listingsDefaultTask()
    {
        $params = array(
            'page'  => isset($_GET['page']) ? $_GET['page'] : 1,
            'sort'  => isset($_GET['sort']) ? $_GET['sort'] : 'title',
            'order' => isset($_GET['order']) ? $_GET['order'] : 'ASC',
            'find'  => isset($_GET['find']) ? $_GET['find'] : NULL,
        );
        
        $limiters = array(
            'country_id' => isset($_GET['country'])  ? $_GET['country']  : 'all',
            'city_id'    => isset($_GET['city'])     ? $_GET['city']     : 'all',
            'status'     => isset($_GET['status'])   ? $_GET['status']   : 'all',
            'main_type'  => isset($_GET['type'])     ? $_GET['type']     : 'all',
        );
        
        $listings = $this->admin->getListings($limiters, $params);
        $total    = $this->admin->countListings();
        
        $this->view->listings = $listings;
        $this->view->params   = $params;
        $this->view->limiters = $limiters;
        $this->view->total    = $total;
        
        //echo '<pre>'; var_dump($listings->toArray()); echo '</pre>'; die;
    }
    
    private function listingsEditTask()
    {
        
    }
    
    
    
    
    
    /**
     * Manage all trips and itineraries
     */
    public function tripsAction()
    {
        $template = 'trips';
        switch($this->_getParam('task')){
            case 'default':
                $this->tripsDefaultTask();
                break;
            case 'edit':
                $this->tripsEditTask();
                $template = 'trip-edit';
                break;
            case 'facts':
                $this->tripsFactsTask();
                $template = 'trip-facts';
                break;
            case 'details':
                $this->tripsDetailsTask();
                $template = 'trip-details';
                break;
            case 'itinerary':
                $this->tripsItineraryTask();
                $template = 'trip-itinerary';
                break;
            case 'listing':
                $this->tripsListingTask();
                $template = 'trip-listing';
                break;
            default: throw new Exception('Page not Found', 404, $previous);
        }
        $this->render($template);
    }
    
    private function tripsDefaultTask() 
    {
        
    }
    
    private function tripsEditTask() 
    {
        
    }
    
    private function tripsFactsTask() 
    {
        
    }
    
    private function tripsDetailsTask() 
    {
        
    }
    
    private function tripsItineraryTask() 
    {
        
    }
    
    private function tripsListingTask() 
    {
        
    }
    
    
    
    
    /**
     * Manage all countries and cities
     */
    public function placesAction()
    {
        $template = 'places';
        switch($this->_getParam('task')){
            case 'default':
                $this->placesDefaultTask();
                break;
            case 'countries':
                $this->placesCountriesTask();
                $template = 'places-countries';
                break;
            case 'cities':
                $this->placesCitiesTask();
                $template = 'places-cities';
                break;
            case 'newcity':
                $this->placesNewcityTask();
                $template = 'places-newcity';
                break;
            default: throw new Exception('Page not Found', 404, $previous);
        }
        $this->render($template);
    }
    
    private function placesDefaultTask()
    {
        
    }
    
    private function placesCountriesTask()
    {
        
    }
    
    private function placesCitiesTask()
    {
        
    }
    
    private function placesNewcityTask()
    {
        
    }
    
    
    
    
    
    /**
     * Manage all users
     */
    public function usersAction()
    {
        $template = 'users';
        switch($this->_getParam('task')){
            case 'default':
                $this->usersDefaultTask();
                break;
            default:
                $this->usersViewTask();
                $template = 'user-view';
                break;
        }
        $this->render($template);
    }
    
    private function usersDefaultTask()
    {
        
    }
    
    private function usersViewTask()
    {
        
    }
    
    public function partnersAction()
    {
        $template = 'partners';
        switch($this->_getParam('task')){
            case 'default':
                $this->partnersDefaultTask();
                break;
            default:
                $this->partnersViewTask();
                $template = 'partner-view';
                break;
        }
        $this->render($template);
    }
    
    private function partnersDefaultTask()
    {
        
    }
    
    private function partnersViewTask()
    {
        
    }
    
    
    
    
    /**
     * Manage all the payments transactions
     */
    public function paymentsAction()
    {
        $template = 'payments';
        switch($this->_getParam('task')){
            case 'users':
                $template = $this->paymentsDefaultTask();
                break;
            case 'partners':
                $template = $this->paymentsPartnersTask();
                break;
            case 'default':
                $template = $this->paymentsDefaultTask();
                break;
            default: throw new Exception('Page not Found', 404, $previous);
        }
        $this->render($template);
    }
    
    private function paymentsDefaultTask()
    {
        $template = 'payments';
        switch($this->_getParam('id')){
            case 'default':
                $this->paymentUsersDefault();
                break;
            case 'pending':
                $this->paymentUsersPending();
                $template = 'payments-pending';
                break;
            default: throw new Exception('Page not Found', 404, $previous);
        }
        return $template;
    }
    
    private function paymentUsersDefault()
    {
        
    }
    
    private function paymentUsersPending()
    {
        
    }
    
    private function paymentsPartnersTask()
    {
        $template = 'payments-partners';
        switch($this->_getParam('id')){
            case 'default':
                $this->paymentPartnersDefault();
                break;
            case 'history':
                $template = 'payments-history';
                $this->paymentPartnersHistory();
                break;
            default: throw new Exception('Page not Found', 404, $previous);
        }
        return $template;
    }
    
    private function paymentPartnersDefault()
    {
        
    }
    
    private function paymentPartnersHistory()
    {
        
    }
    
    
    
    
    /**
     * Manage all reservations
     */
    public function reservationsAction()
    {
        
    }
    
    
    
    
    
    /**
     * Create reports
     */
    public function reportsAction()
    {
        if($this->getRequest()->isPost())
        {
            
        }
        else
            throw new Exception('Page not found', 404);
    }
}