<?php

class CartController extends Zend_Controller_Action {
    
    /**
     *
     * @var WS_User
     */
    protected $user;
    
    /**
     *
     * @var WS_ListingService
     */
    protected $listings;
    
    /**
     *
     * @var Model_Cart
     */
    protected $cart;
    
    /**
     *
     * @var WS_PlacesService
     */
    protected $places;
    
    /**
     *
     * @var Model_Billing
     */
    protected $billing;
    
    /**
     *
     * @var WS_ReservationsService
     */
    protected $reservations;
    
    /**
     *
     * @var Model_Transactions
     */
    protected $transactions;
    
    /**
     *
     * @var WS_TripsService
     */
    protected $trips;
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            $this->_redirect('/en-US/login?b='.$_SERVER['REQUEST_URI']);
            //var_dump($_SESSION); die;
        } else {
            $this->user = new WS_User($auth->getStorage()->read());
            $this->listings = new WS_ListingService();
            $this->cart     = new Model_Cart();
            $this->places   = new WS_PlacesService();
            $this->billing  = new Model_Billing();
            $this->reservations = new WS_ReservationsService();
            $this->transactions = new Model_Transactions();
            $this->trips        = new WS_TripsService();
        }
        
        $this->view->cssVC = Zend_Registry::get('vc');
    }
    
    public function indexAction()
    {
        
    }
    
    public function addAction()
    {
        if($this->getRequest()->isPost()){
            if($this->user->getRole() != 'user')
                    throw new Exception('No access allowed');
            
            $data = $_POST;
            if($data['userids'] != $this->user->getId())
                    throw new Exception('Form corrupted userids');
            if($data['userstoken'] != md5($this->user->getToken()))
                    throw new Exception('Form corrupted userstoken');
            if($data['formids'] != md5($this->user->getToken().'bookit'))
                    throw new Exception('Form corrupted formids');
            if($data['_task'] != md5('bookit'))
                    throw new Exception('Form corrupted _task');
            
            $listing = $this->listings->getListingForCart($data);
            if(is_null($listing))
                    throw new Exception('Listing not found');
            
            $country = $this->places->getPlaceById($listing->country_id);
            $city = $this->places->getPlaceById($listing->city_id);
            if($city->parent_id != $country->id)
                    throw new Exception('Form corrupted city/country');
            
            $vendor = $this->listings->getVendor($listing->vendor_id);
            
            if($listing->main_type == 6){
                $rowR = $this->listings->getActivityQuote(
                            $listing, 
                            $data['adults'], 
                            $data['kids'], 
                            date('Y-m-d', strtotime($data['checkin'])), 
                            $data['option'], 
                            $data['capacity']);
                
                $row = $this->cart->fetchNew();
                
                $row->user_id    = $this->user->getId();
                $row->checkin    = date('Y-m-d', strtotime($data['checkin']));
                $row->listing_id = $listing->id;
                $row->option_id  = $rowR->option_id;
                $row->adults     = $rowR->adults;
                $row->kids       = !is_null($rowR->kids) ? $rowR->kids : 0;
                $row->rate       = $rowR->rate;
                $row->rate       = $rowR->rate;
                $row->additional = $rowR->additional;
                $row->subtotal   = $rowR->subtotal;
                $row->taxes      = $rowR->taxes;
                $row->total      = $rowR->total;
                $row->created    = date('Y-m-d g:i:s');
                $row->token      = md5($this->user->getToken().$row->created);
                
                $row->rate_description       = $rowR->rate_description;
                $row->additional_description = $rowR->additional_description;
                
                $row->save();
                
                $this->_redirect('cart/checkout/'.$row->id);
            }
            elseif($listing->main_type == 5)
            {
                $rowR = $this->listings->getQuote(
                            $listing, 
                            $data['adults'], 
                            $data['kids'], 
                            date('Y-m-d', strtotime($data['checkin'])), 
                            date('Y-m-d', strtotime($data['checkout'])), 
                            null, $data['option']);
                               
                $row = $this->cart->fetchNew();
                
                $row->user_id    = $this->user->getId();
                $row->checkin    = date('Y-m-d', strtotime($data['checkin']));
                $row->checkout   = date('Y-m-d', strtotime($data['checkout']));
                $row->listing_id = $listing->id;
                $row->option_id  = $rowR->option_id;
                $row->adults     = $rowR->adults;
                $row->kids       = $rowR->kids;
                $row->rate       = $rowR->rate;
                $row->rate       = $rowR->rate;
                $row->additional = $rowR->additional;
                $row->rooms      = $rowR->rooms;
                $row->nights     = $rowR->nights;
                $row->subtotal   = $rowR->subtotal;
                $row->taxes      = $rowR->taxes;
                $row->total      = $rowR->total;
                $row->max        = $rowR->max;
                $row->created    = date('Y-m-d g:i:s');
                $row->token      = md5($this->user->getToken().$row->created);
                
                $row->rate_description       = $rowR->rate_description;
                $row->additional_description = $rowR->additional_description;
                
                $row->save();
                
                $this->_redirect('cart/checkout/'.$row->id);
            }
            else
            {
                throw new Exception('Bad Listing type');
            }
        }
        else
            throw new Exception('Bad Request');
    }
    
    public function addtripAction()
    {
        if($this->getRequest()->isPost())
        {
            if($this->user->getRole() != 'user')
                    throw new Exception('No access allowed');
            
            $ids = $_POST['ids'];
            $token = $_POST['token'];
            $trips = new Model_Trips();
            
            $select = $trips->select();
            $select->where('id = ?', $ids);
            $select->where('token = ?', $token);
            $trip = $trips->fetchRow($select);
            if(is_null($trip))
                    $this->_redirect('trips');
            
            $this->view->trip = $trip;
        }
        else
            $this->_redirect('trips');
    }
    
    public function purchasetripAction()
    {
        if($this->getRequest()->isPost()){
                        
            $user = $this->user->getData();
            
            $id    = $_POST['trip'];
            
            $trip = $this->trips->get($id);            
            if($trip->token != $_POST['triptoken'])
                    throw new Exception();
            
            $today = strtotime(date('Y-m-d'));
            $checkin = strtotime($_POST['checkin']);
            
            if($today > $checkin)
                    throw new Exception('No checkin date provided');            
            
            $date   = date('Y-m-d G:i:s', $checkin);
            $adults = (isset($_POST['adults']) and !empty($_POST['adults'])) ? $_POST['adults'] : 0;
            $kids   = (isset($_POST['kids']) and !empty($_POST['kids'])) ? $_POST['kids'] : 0;
            $people = $adults + $kids;
            
            if($adults == 0)
                    throw new Exception('Adult cannot be 0');
            if($people < $trip->min)
                    throw new Exception('You need more people. Minimun '.$trip->min);
            if($people > $trip->max)
                    throw new Exception('Too many people. Maximun '.$trip->max);
            
            $cart = $this->cart->fetchNew();
            $cart->user_id    = $user->id;
            $cart->listing_id = $trip->id;
            $cart->checkin    = $date;
            $cart->checkout   = date('Y-m-d G:i:s', (strtotime($date)) + (86400 * $trip->days));
            $cart->adults     = $adults;
            $cart->kids       = $kids;
            $cart->rate       = $trip->price;
            $cart->rate_description = $trip->title . ' - Trip Purchase';
            $cart->subtotal   = ($cart->rate * $people);
            $cart->taxes      = 0;
            $cart->total      = $cart->subtotal;
            $cart->token      = md5($trip->id . $user->token . time());
            $cart->type       = 2;
            $cart->created    = date('Y-m-d H:i:s');
            
            $cart->save();
            
            $this->_redirect('cart/checkout/'.$cart->id);
        }
        die;
    }
    
    public function purchaseitineraryAction()
    {
        if($this->getRequest()->isPost())
        {
            $auth = Zend_Auth::getInstance();
            if(!$auth->hasIdentity())
                    $this->_redirect ('/login');
            
            $user = $auth->getIdentity();
            
            $id    = $_POST['trip'];
            
            $trip = $this->trips->getItn($id);
            if($trip->token != $_POST['triptoken'])
                    throw new Exception();
            
            $date   = date('Y-m-d G:i:s', strtotime($trip->start));
            $adults = (!empty($_POST['adults'])) ? $_POST['adults'] : 1;
            $kids   = (!empty($_POST['kids'])) ? $_POST['kids'] : 0;
            $items  = array();
            $items2 = array();
            
            $cart = $this->cart->fetchNew();
            $cart->user_id  = $user->id;
            $cart->listing_id = $trip->id;
            $cart->checkin  = $date;
            $cart->adults   = $adults;
            $cart->kids     = $kids;
            $cart->rate     = 0;
            $cart->rate_description = $trip->title . ' - Itinerary Purchase';
            $cart->subtotal = 0;
            $cart->taxes    = 0;
            $cart->total    = 0;
            $cart->token    = md5($user->token . time());
            $cart->type     = 3;
            $cart->created  = date('Y-m-d H:i:s');
            
            $cart->save();
            
            $cartitems = new Zend_Db_Table('cartitems');            
            
            $listings = $this->trips->getItnListingOf($trip->id, false, 'null', true, true);
            
            foreach($listings as $listing){
                if($listing->main_type == 6 || $listing->main_type == 5){
                    $checkin   = ($listing->day > 1) ? (strtotime($date) + (($listing->day - 1) * 86400)) : strtotime($date);
                    $checkin   = date('Y-m-d', $checkin);
                    $item      = $this->listings->getQuote($listing, $adults, $kids, $checkin, null, $trip->days);
                    $item->day = $listing->day;
                    
                    $cartitem = $cartitems->fetchNew();
                    $cartitem->cart_id      = $cart->id;
                    $cartitem->checkin      = $checkin;
                    $cartitem->listing_id   = $listing->id;
                    $cartitem->rate         = $item->rate;
                    $cartitem->additional   = $item->additional;
                    $cartitem->subtotal     = $item->subtotal;
                    $cartitem->taxes        = $item->taxes;
                    $cartitem->total        = $item->total;
                    $cartitem->additional_description = $item->additional_description;
                    $cartitem->rate_description       = $item->rate_description;
                    
                    if($listing->main_type == 5){
                        $cartitem->checkout  = date('Y-m-d', strtotime($item->checkout));
                        $cartitem->option_id = $item->option_id;
                        $cartitem->nights    = $item->nights;
                        $cartitem->rooms     = $item->rooms;
                    }
                    
                    $cartitem->created = date('Y-m-d H:i:s');
                    $cartitem->triplisting = $listing->triplisting_id;
                    $cartitem->save();
                    
                    $items[] = $cartitem;
                }
            }
            
            foreach($listings as $listing){
                if($listing->main_type != 6 && $listing->main_type != 5){                    
                    $checkin = ($listing->day > 1) ? (strtotime($date) + (($listing->day - 1) * 86400)) : strtotime($date);
                    $cartitem = $cartitems->fetchNew();
                    $cartitem->cart_id      = $cart->id;
                    $cartitem->checkin      = $checkin;
                    $cartitem->listing_id   = $listing->id;
                    $cartitem->rate         = 0;
                    $cartitem->additional   = 0;
                    $cartitem->subtotal     = 0;
                    $cartitem->taxes        = 0;
                    $cartitem->total        = 0;
                    $cartitem->created      = date('Y-m-d H:i:s');
                    $cartitem->rate_description = 'Price not charget';
                    $cartitem->save();
                    
                    $items2[] = $cartitem;
                }
            }
            
            $total = 0;
            $subtotal = 0;
            $taxes  = 0;
            foreach($items as $c){
                $total = $total + $c->total;
                $subtotal = $subtotal + $c->subtotal;
                $taxes = $c->taxes;
            }
            
            $cart->total = $total;
            $cart->subtotal = $subtotal;
            $cart->taxes = $taxes;
            $cart->save();
            
            $this->_redirect('cart/checkout/'.$cart->id);
        }
        die;
    }
    
    public function checkoutAction()
    {
        if($this->_getParam('task') == 'default')
                $this->_redirect('/');
        
        if($this->user->getRole() != 'user')
                throw new Exception('No access allowed');
        
        $ids = $this->_getParam('task');
        
        $select = $this->cart->select();
        $select->where('id = ?', $ids);
        $cartitem = $this->cart->fetchRow($select);
        
        if(is_null($cartitem))
                $this->_redirect('/');
        
        if($cartitem->user_id != $this->user->getId())
                $this->_redirect('/');
        
        $keys = Zend_Registry::get('stripe');
        $this->view->pkey = $keys['public_key'];
        
        if($cartitem->type == 1)
        {
            $listing = $this->listings->getListing($cartitem->listing_id);
            $country = $this->places->getPlaceById($listing->country_id);
            $city    = $this->places->getPlaceById($listing->city_id);
            $vendor  = $this->listings->getVendor($listing->vendor_id);
            $countries = $this->places->getPlaces(2);
            $types = $this->listings->getMainCategories(true);
            
            $accounts = $this->user->getAccounts();

            $this->view->cartitem  = $cartitem;
            $this->view->listing   = $listing;
            $this->view->user      = $this->user->getData();
            $this->view->country   = $country;
            $this->view->city      = $city;
            $this->view->vendor    = $vendor;
            $this->view->countries = $this->places->getPlaces(2);
            
            $this->view->types = $types;
            $this->view->accounts = $accounts;
            
            $creditcards = array(
                'Visa'             => '/images2/checkout-card1.png',
                'MasterCard'       => '/images2/checkout-card2.png',
                'American Express' => '/images2/checkout-card3.png',
                'Diners Club'      => '/images2/checkout-card4.png',
                'Discover'         => '/images2/checkout-card5.png',
                'JCB'              => '/images2/checkout-card6.png',
            );
            
            $this->view->creditcards = $creditcards;
            
        } 
        elseif($cartitem->type == 2) 
        { 
            $trip = $this->trips->get($cartitem->listing_id);
            $listings = $this->trips->getListingOf($trip->id, false);
            
            $country = $this->places->getPlaceById($trip->country_id);
            $this->view->country   = $country;
            
            $accounts = $this->user->getAccounts();
            $this->view->accounts  = $accounts;
            
            $this->view->trip      = $trip;
            $this->view->cartitem  = $cartitem;
            $this->view->user      = $this->user->getData();
            $this->view->countries = $this->places->getPlaces(2);
            $this->view->listings  = $listings;
            
            $creditcards = array(
                'Visa'             => '/images2/checkout-card1.png',
                'MasterCard'       => '/images2/checkout-card2.png',
                'American Express' => '/images2/checkout-card3.png',
                'Diners Club'      => '/images2/checkout-card4.png',
                'Discover'         => '/images2/checkout-card5.png',
                'JCB'              => '/images2/checkout-card6.png',
            );
            
            $this->view->creditcards = $creditcards;
            
            $this->render('checkout-trip');
        } 
        elseif($cartitem->type == 3) {
            
            $trip = $this->trips->getItn($cartitem->listing_id);
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from('cartitems');
            $select->join('listings','cartitems.listing_id = listings.id', array(
                'listing_name'=>'title',
                'listing_type'=>'main_type'
            ));
            $select->where('cartitems.cart_id = ?', $cartitem->id);
            $items = $db->fetchAll($select, array(), 5);
            
            $this->view->trip      = $trip;
            $this->view->cartitem  = $cartitem;
            $this->view->user      = $this->user->getData();
            $this->view->countries = $this->places->getPlaces(2);
            $this->view->items     = $items;
            
            $country = $this->places->getPlaceById($trip->country_id);
            $this->view->country   = $country;
            
            $accounts = $this->user->getAccounts();
            $this->view->accounts  = $accounts;
            
            $creditcards = array(
                'Visa'             => '/images2/checkout-card1.png',
                'MasterCard'       => '/images2/checkout-card2.png',
                'American Express' => '/images2/checkout-card3.png',
                'Diners Club'      => '/images2/checkout-card4.png',
                'Discover'         => '/images2/checkout-card5.png',
                'JCB'              => '/images2/checkout-card6.png',
            );
            
            $this->view->creditcards = $creditcards;
            
            $this->render('checkout-itn');
        }
    }
    
    public function chargeAction()
    {
        if($this->getRequest()->isPost())
        {
            if($this->user->getRole() != 'user')
                    throw new Exception('No access allowed');
            
            //var_dump($_POST); die;
            $data = $_POST;
            if($data['userids'] != $this->user->getId())
                    throw new Exception('There is something wrong with the form please try later');
            if($data['userstoken'] != $this->user->getToken())
                    throw new Exception('There is something wrong with the form please try later');
            if($data['formids'] != md5($this->user->getToken().'checkout'))
                    throw new Exception('There is something wrong with the form please try later');
            if($data['_task'] != md5('purchase'))
                    throw new Exception('There is something wrong with the form please try later');
            
            $select = $this->cart->select();
            $select->where('id = ?', $data['cartitemid']);
            $select->where('user_id = ?', $this->user->getId());
            $cartitem = $this->cart->fetchRow($select);
            
            if(is_null($cartitem))
                    throw new Exception();
            if($cartitem->token != $data['cartitemtoken'])
                    throw new Exception();
            
            if($data['account'] == 'new') {
            
                require_once "Stripe/Stripe.php";
                // set your secret key: remember to change this to your live secret key in production
                // see your keys here https://manage.stripe.com/account
                $keys = Zend_Registry::get('stripe');
                Stripe::setApiKey($keys['secret_key']);

                // get the credit card details submitted by the form
                $token = $_POST['stripeToken'];

                // create a Customer
                $customer = Stripe_Customer::create(array(
                  "card" => $token,
                  "description" => $this->user->getEmail())
                );

                //var_dump($customer->active_card); die;

                $strip_accounts = new Zend_Db_Table('stripe_accounts');
                $account = $strip_accounts->fetchNew();
                $account->stripe_id = $customer->id;
                $account->user_id   = $this->user->getId();
                $account->type      = $customer->active_card->type;
                $account->last4     = $customer->active_card->last4;
                $account->created   = date('Y-m-d H:i:s');
                $account->updated   = date('Y-m-d H:i:s');

                $account->save();
            } else {
                $account = $this->user->getAccount($_POST['account']);
            }
            
            if(is_null($account)) 
                    throw new Excpeiton('Account not found');
            
            switch($cartitem->type) {
                case 1:
                    $listing = $this->listings->getListing($cartitem->listing_id);
                    $vendor  = $this->listings->getVendor($listing->vendor_id);
                    $method  = $account->type . ' ************'.$account->last4;
                    $code    = $this->user->getId().$vendor->id.time();
                    
                    $_transaction = array(
                        'code'      => $code,
                        'user_name' => $this->user->getName(),
                        'method'    => $method,
                        'ammount'   => $cartitem->total,
                        'date'      => date('Y-m-d G:i:s'),
                        'listing_id'=> $listing->id,
                        'vendor_id' => $vendor->id,
                        'status'    => 0,
                        'created'   => date('Y-m-d G:i:s'),
                        'updated'   => date('Y-m-d G:i:s'),
                        'paywith'   => $account->id,
                    );

                    $transaction = $this->transactions->fetchNew();
                    $transaction->setFromArray($_transaction);
                    $transaction->save();
                    
                    $reservation = $this->reservations->create($transaction, $cartitem, $listing);

                    $cartitem->delete();
                    
                    $notifier = new WS_Notifier($vendor->user_id);
                    $notifier->newReservation($listing, $reservation);

                    $this->_redirect('cart/invoice/'.$reservation->id);
                break;
                case 2:
                    try {
                        $listing = $this->trips->get($cartitem->listing_id);

                        if($data['account'] != 'new') {
                            require_once "Stripe/Stripe.php";
                            // set your secret key: remember to change this to your live secret key in production
                            // see your keys here https://manage.stripe.com/account
                            $keys = Zend_Registry::get('stripe');
                            Stripe::setApiKey($keys['secret_key']);
                        }

                        $charge = Stripe_Charge::create(array(
                            'amount'    => 200,//$cartitem->total * 100,
                            'currency'  => 'usd',
                            'customer'  => $account->stripe_id,
                            'description' => $cartitem->rate_description
                        ));

                        //echo get_class($charge); die;

                        $method  = $account->type . ' ************'.$account->last4;
                        $code    = 'U'.$this->user->getId().'I'.$cartitem->id.'T'.time();

                        $_transaction = array(
                            'code'      => $code,
                            'charge_id' => $charge->id,
                            'user_name' => $this->user->getName(),
                            'method'    => $method,
                            'ammount'   => $cartitem->total,
                            'date'      => date('Y-m-d G:i:s'),
                            'trip_id'   => $listing->id,
                            'status'    => 1,
                            'created'   => date('Y-m-d G:i:s'),
                            'updated'   => date('Y-m-d G:i:s'),
                            'paywith'   => $account->id
                        );

                        $transaction = $this->transactions->fetchNew();
                        $transaction->setFromArray($_transaction);
                        $transaction->save();

                        $reservation = $this->trips->createPurchase($transaction, $cartitem, $listing);

                        $cartitem->delete();
                        
                        $notifier = new WS_Notifier($this->user->getId());
                        $notifier->tripPurchased($reservation, $listing);
                            
                        

                        $this->_redirect('/cart/tripinvoice/'.$reservation->id);
                    } catch(Exception $e) {
                        $this->_redirect('/en-US/cart/checkout/'.$cartitem->id.'/#/?error='.$e->getMessage());
                    }
                break;
                case 3:
                    $cartitems = new Zend_Db_Table('cartitems');
                    $select = $cartitems->select();
                    $select->where('cart_id = ?', $cartitem->id);
                    $items = $cartitems->fetchAll($select);
                    
                    foreach($items as $item) 
                    {
                        if($item->total > 0) 
                        {
                            $listing = $this->listings->getListing($item->listing_id);
                            $vendor  = $this->listings->getVendor($listing->vendor_id);
                            $method  = $account->type . ' ************'.$account->last4;
                            $code    = $this->user->getId().$vendor->id.time();

                            $_transaction = array(
                                'code'      => $code,
                                'user_name' => $this->user->getName(),
                                'method'    => $method,
                                'ammount'   => $cartitem->total,
                                'date'      => date('Y-m-d H:i:s'),
                                'listing_id'=> $listing->id,
                                'vendor_id' => $vendor->id,
                                'status'    => 0,
                                'created'   => date('Y-m-d H:i:s'),
                                'updated'   => date('Y-m-d H:i:s'),
                                'paywith'   => $account->id,
                            );

                            $transaction = $this->transactions->fetchNew();
                            $transaction->setFromArray($_transaction);
                            $transaction->save();

                            $reservation = $this->reservations->createOfTrip($transaction, $item, $listing, $cartitem);
                            
                            $itnlistings = new Zend_Db_Table('itinerary_listings');
                            $select = $itnlistings->select();
                            $select->where('id = ?', $item->triplisting);
                            $itnlist = $itnlistings->fetchRow($select);
                            $itnlist->reservation_id = $reservation->id;
                            $itnlist->save();                            

                            $notifier = new WS_Notifier($vendor->user_id);
                            $notifier->newReservation($listing, $reservation);
                            
                            $item->delete();
                        }
                        else 
                        {
                            $item->delete();
                        }
                    }
                    
                    $trip = $this->trips->getItn($cartitem->listing_id, true);
                    $trip->status = 2;
                    $trip->save();
                    
                    $cartitem->delete();

                    $this->_redirect('cart/itninvoice/'.$trip->id);
                break;
            }
        }
    }
    
    public function confirmAction()
    {
        if($this->user->getRole() != 'user')
                throw new Exception('No access allowed');
        
        switch($this->_getParam('task')){
            case 'paypal':
                $select = $this->cart->select();
                $select->where('id = ?', $this->_getParam('id'));
                $select->where('user_id = ?', $this->user->getId());
                $cartitem = $this->cart->fetchRow($select);
                
                $service = new WS_Paypal(true);
                
                $finalPaymentAmount = $cartitem->total;
                
                $resArray = $service->ConfirmPayment($finalPaymentAmount);
                $ack = strtoupper($resArray["ACK"]);
                if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
                {
                    $code    = $resArray["PAYMENTINFO_0_TRANSACTIONID"];
                    $listing = $this->listings->getListing($cartitem->listing_id);
                    $vendor  = $this->listings->getVendor($listing->vendor_id);

                    $_transaction = array(
                        'code'      => $code,
                        'user_name' => $this->user->getName(),
                        'method'    => 'PayPal',
                        'ammount'   => $cartitem->total,
                        'date'      => date('Y-m-d G:i:s'),
                        'listing_id'=> $listing->id,
                        'vendor_id' => $vendor->id,
                        'status'    => 0,
                        'created'   => date('Y-m-d G:i:s'),
                        'updated'   => date('Y-m-d G:i:s')
                    );

                    $transaction = $this->transactions->fetchNew();
                    $transaction->setFromArray($_transaction);
                    $transaction->save();

                    $reservation = $this->reservations->create($transaction, $cartitem, $listing);

                    $cartitem->delete();
                    
                    $this->_redirect('cart/invoice/'.$reservation->id);
                } else {
                    $token = "";
                    if (isset($_REQUEST['token'])){
                            $token = $_REQUEST['token'];
                    }
                    if ($token != "")
                    {
                        $resArray = $service->GetShippingDetails($token);
                        $ack = strtoupper($resArray["ACK"]);
                        if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING") 
                            $this->_redirect('cart/confirm/'.$cartitem->id);
                        else  
                            throw new Exception();
                    }
                }
                break;
            case 'creditcard':
                $select = $this->cart->select();
                $select->where('id = ?', $this->_getParam('id'));
                $select->where('user_id = ?', $this->user->getId());
                $cartitem = $this->cart->fetchRow($select);
                
                if($cartitem->type == 1)
                {                                
                    $listing = $this->listings->getListing($cartitem->listing_id);
                    $vendor  = $this->listings->getVendor($listing->vendor_id);
                    $method  = $_GET['method'];
                    $code    = $_GET['code'];

                    $_transaction = array(
                        'code'      => $code,
                        'user_name' => $this->user->getName(),
                        'method'    => $method,
                        'ammount'   => $cartitem->total,
                        'date'      => date('Y-m-d G:i:s'),
                        'listing_id'=> $listing->id,
                        'vendor_id' => $vendor->id,
                        'status'    => 0,
                        'created'   => date('Y-m-d G:i:s'),
                        'updated'   => date('Y-m-d G:i:s')
                    );

                    $transaction = $this->transactions->fetchNew();
                    $transaction->setFromArray($_transaction);
                    $transaction->save();

                    $reservation = $this->reservations->create($transaction, $cartitem, $listing);

                    $cartitem->delete();

                    $this->_redirect('cart/invoice/'.$reservation->id);
                } 
                elseif($cartitem->type == 2)
                {
                    $trip = $this->trips->get($cartitem->listing_id, true);
                    $listings = $this->trips->getListingOf($trip['id'], false);
                    
                    unset($trip['id']);
                    unset($trip['views']);
                    unset($trip['public']);
                    unset($trip['min']);
                    unset($trip['max']);
                    unset($trip['start_city']);
                    unset($trip['end_city']);
                    unset($trip['featured']);
                    
                    $trip['adults']  = $cartitem->adults;
                    $trip['kids']    = $cartitem->kids;
                    $trip['created'] = date('Y-m-d H:i:s');
                    $trip['updated'] = date('Y-m-d H:i:s');
                    $trip['user_id'] = $this->user->getId();
                    $trip['status']  = 2;
                    $trip['price']   = $cartitem->total;
                    
                    $itineraries = new Model_Itineraries();
                    $itinerary   = $itineraries->fetchNew();
                    $itinerary->setFromArray($trip);
                    $itinerary->save();
                    
                    $cartitems = new Zend_Db_Table('cartitems');
                    foreach($listings as $list){
                        $select = $cartitems->select();
                        $select->where('cart_id = ?', $cartitem->id);
                        $select->where('triplisting = ?', $list->triplisting_id);
                        $item = $cartitems->fetchRow($select);
                        if(!is_null($item)){
                            if($list->main_type == 5 || $list->main_type == 6) {
                                $listing = $this->listings->getListing($item->listing_id);
                                $vendor  = $this->listings->getVendor($listing->vendor_id);
                                $method  = $_GET['method'];
                                $code    = $_GET['code'];

                                $_transaction = array(
                                    'code'      => $code,
                                    'user_name' => $this->user->getName(),
                                    'method'    => $method,
                                    'ammount'   => $item->total,
                                    'date'      => date('Y-m-d G:i:s'),
                                    'listing_id'=> $listing->id,
                                    'vendor_id' => $vendor->id,
                                    'status'    => 0,
                                    'created'   => date('Y-m-d G:i:s'),
                                    'updated'   => date('Y-m-d G:i:s')
                                );

                                $transaction = $this->transactions->fetchNew();
                                $transaction->setFromArray($_transaction);
                                $transaction->save();

                                //$reservation = $this->reservations->create($transaction, $item, $listing);
                                $reservation = $this->reservations->createOfTrip($transaction, $item, $listing, $cartitem);
                                
                                $itnlistings = new Zend_Db_Table('itinerary_listings');
                                $itnlist = $itnlistings->fetchNew();
                                $itnlist->reservation_id = $reservation->id;
                                $itnlist->user_id        = $this->user->getId();
                                $itnlist->itinerary_id   = $itinerary->id;
                                $itnlist->listing_id     = $list->id;
                                $itnlist->day            = $list->day;
                                $itnlist->time           = $list->time;
                                $itnlist->start          = $list->start;
                                $itnlist->end            = $list->end;
                                $itnlist->city_id        = $list->city_id;
                                $itnlist->country_id     = $list->country_id;
                                $itnlist->sort           = $list->sort;
                                
                                $itnlist->save();
                            } 
                            else {
                                $itnlistings = new Zend_Db_Table('itinerary_listings');
                                $itnlist = $itnlistings->fetchNew();
                                $itnlist->user_id        = $this->user->getId();
                                $itnlist->itinerary_id   = $itinerary->id;
                                $itnlist->listing_id     = $list->id;
                                $itnlist->day            = $list->day;
                                $itnlist->time           = $list->time;
                                $itnlist->start          = $list->start;
                                $itnlist->end            = $list->end;
                                $itnlist->city_id        = $list->city_id;
                                $itnlist->country_id     = $list->country_id;
                                $itnlist->sort           = $list->sort;
                                
                                $itnlist->save();
                            }
                        } else {
                            $itnlistings = new Zend_Db_Table('itinerary_listings');
                            $itnlist = $itnlistings->fetchNew();
                            $itnlist->user_id        = $this->user->getId();
                            $itnlist->itinerary_id   = $itinerary->id;
                            $itnlist->listing_id     = $list->id;
                            $itnlist->day            = $list->day;
                            $itnlist->time           = $list->time;
                            $itnlist->start          = $list->start;
                            $itnlist->end            = $list->end;
                            $itnlist->city_id        = $list->city_id;
                            $itnlist->country_id     = $list->country_id;
                            $itnlist->sort           = $list->sort;

                            $itnlist->save();
                        }
                    }
                    
                    $this->_redirect('cart/tripinvoice/'.$itinerary->id);
                }
                elseif($cartitem->type == 3)
                {
                    $trip = $this->trips->getItn($cartitem->listing_id, true);
                    $trip->status = 2;
                    $trip->price  = $cartitem->total;
                    $trip->save();
                    
                    $listings = $this->trips->getItnListingOf($trip->id, false, 'null', false, true);
                    
                    $this->trips->deleteNullListings($trip->id);
                    
                    $cartitems = new Zend_Db_Table('cartitems');
                    foreach($listings as $list){
                        $select = $cartitems->select();
                        $select->where('cart_id = ?', $cartitem->id);
                        $select->where('triplisting = ?', $list->triplisting_id);
                        $item = $cartitems->fetchRow($select);
                        if(!is_null($item)){
                            if($list->main_type == 5 || $list->main_type == 6) {
                                $listing = $this->listings->getListing($item->listing_id);
                                $vendor  = $this->listings->getVendor($listing->vendor_id);
                                $method  = $_GET['method'];
                                $code    = $_GET['code'];

                                $_transaction = array(
                                    'code'      => $code,
                                    'user_name' => $this->user->getName(),
                                    'method'    => $method,
                                    'ammount'   => $item->total,
                                    'date'      => date('Y-m-d H:i:s'),
                                    'listing_id'=> $listing->id,
                                    'vendor_id' => $vendor->id,
                                    'status'    => 0,
                                    'created'   => date('Y-m-d H:i:s'),
                                    'updated'   => date('Y-m-d H:i:s')
                                );

                                $transaction = $this->transactions->fetchNew();
                                $transaction->setFromArray($_transaction);
                                $transaction->save();

                                //$reservation = $this->reservations->create($transaction, $item, $listing);
                                $reservation = $this->reservations->createOfTrip($transaction, $item, $listing, $cartitem);
                                
                                $itnlistings = new Zend_Db_Table('itinerary_listings');
                                $select = $itnlistings->select();
                                $select->where('id = ?', $list->triplisting_id);
                                $itnlist = $itnlistings->fetchRow($select);
                                $itnlist->reservation_id = $reservation->id;
                                $itnlist->save();
                            }
                        }
                    }
                    
                    $this->_redirect('cart/itninvoice/'.$trip->id);
                }
                break;
            default:
                $this->_redirect('/cart/checkout/'.$this->_getParam('id'));
        }
    }
    
    public function invoiceAction()
    {
        $id = $this->_getParam('task');
        
        if($this->user->getRole() != 'user'){
            $reservation = $this->reservations->get($id);
            $listing = $this->listings->getListing($reservation->listing_id, $this->user->getVendorId());
            if(is_null($listing))
                    throw new Exception('No access allowed');
            
            $users = new WS_UsersService();
            $user  = $users->get($reservation->user_id);
            
            $select = $this->billing->select();
            $select->where('user_id = ?', $user->id);
            $billing = $this->billing->fetchRow($select);

            $select = $this->transactions->select();
            $select->where('id = ?', $reservation->transaction_id);
            $transaction = $this->transactions->fetchRow($select);

            $this->view->transaction = $transaction;
            $this->view->billing     = $billing;
            $this->view->listing     = $listing;
            $this->view->user        = $user;
            $this->view->reservation = $reservation;
        } else {
            $reservation = $this->reservations->get($id, $this->user->getId());
            $listing = $this->listings->getListing($reservation->listing_id);
            
            $select = $this->billing->select();
            $select->where('user_id = ?', $this->user->getId());
            $billing = $this->billing->fetchRow($select);

            $select = $this->transactions->select();
            $select->where('id = ?', $reservation->transaction_id);
            $transaction = $this->transactions->fetchRow($select);

            $this->view->transaction = $transaction;
            $this->view->billing     = $billing;
            $this->view->listing     = $listing;
            $this->view->user        = $this->user->getData();
            $this->view->reservation = $reservation;
        }
    }
    
    public function tripinvoiceAction()
    {
        $id = $this->_getParam('task');
        
        if($this->user->getRole() != 'user' and $this->user->getRole() != 'admin'){
            throw new Exception('No access allowed');
        } else {
            $reservation = $this->trips->getPurchase($id);
            $listing = $this->trips->get($reservation->trip_id);
            if(is_null($listing))
                    throw new Exception('No access allowed');
            
            $users = new WS_UsersService();
            $user  = $users->get($reservation->user_id);
            
            $select = $this->transactions->select();
            $select->where('id = ?', $reservation->transaction_id);
            $transaction = $this->transactions->fetchRow($select);

            $this->view->transaction = $transaction;
            $this->view->listing     = $listing;
            $this->view->user        = $user;
            $this->view->reservation = $reservation;
        }
    }
    
    public function itninvoiceAction()
    {
        $id = $this->_getParam('task');
        
        $trip = $this->trips->getItn($id, false);
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('itinerary_listings');
        $select->join('listings', 'itinerary_listings.listing_id = listings.id');
        $select->joinLeft('reservations', 'itinerary_listings.reservation_id = reservations.id', array(
            'ammount'
        ));
        $select->where('itinerary_listings.itinerary_id = ?', $id);
        $select->where('itinerary_listings.user_id = ?', $this->user->getId());
        $select->where('itinerary_listings.day is not null');
        $select->where('itinerary_listings.time is not null');
        $listings = $db->fetchAll($select, array(), 5);
        
        $select = $db->select();
        $select->from('itinerary_listings');
        $select->join('reservations', 'itinerary_listings.reservation_id = reservations.id');
        $select->join('listings', 'itinerary_listings.listing_id = listings.id', array(
            'listing_name' => 'title'
        ));
        $select->where('itinerary_listings.itinerary_id = ?', $id);
        $select->where('itinerary_listings.user_id = ?', $this->user->getId());
        $reservations = $db->fetchAll($select, array(), 5);
        
        $select = $db->select();
        $select->from('transactions');
        $select->where('id = ?', $reservations[0]->transaction_id);
        $transaction = $db->fetchRow($select, array(), 5);
        
        $this->view->user         = $this->user->getData();
        $this->view->trip         = $trip;
        $this->view->listings     = $listings;
        $this->view->reservations = $reservations;
        $this->view->transaction  = $transaction;
    }
}
