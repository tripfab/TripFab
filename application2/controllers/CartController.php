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
            $this->_redirect('login');
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
                $row = $this->cart->fetchNew();
                $row->user_id    = $this->user->getId();
                $row->checkin    = date('Y-m-d', strtotime($data['checkin']));
                $row->option_id  = $data['option'];
                $row->adults     = $data['adults'];
                $row->kids       = $data['kids'];
                $row->listing_id = $listing->id;
                
                $seasson = $this->listings->getSeassonFor($row->checkin, $listing->id);
                if(!is_null($seasson)){
                    $price = $this->listings->getSeassonPrice($seasson->id, $listing->id);
                    if($price->price == '0.00'){
                        $price = $this->listings->getBasicPrice($listing->id); 
                    }
                } else {
                    $price = $this->listings->getBasicPrice($listing->id);
                }
                
                //var_dump($price->toArray()); die;
                
                $total_people = $row->kids + $row->adults;
                if($price->min > $total_people)
                        throw new Exception('you need more people');
                if($price->max < $total_people)
                        throw new Exception('too many people');
                $row->rate = $price->price;
                if($price->type_id == 1){
                    $row->rate_description = 'Basic Price';
                } else {
                    $row->rate_description = $seasson->name . ' seasson price';
                }
                if($price->additional_after < $total_people){
                    $extra = $total_people - $price->additional_after;
                    $row->additional = $price->additional;
                    $row->additional_description = 'Additional after '.$price->additional_after.' person(s)';
                }
                
                if($extra){
                    $row->subtotal = $price->price;
                    $row->subtotal = $row->subtotal + ($row->additional * $extra);
                } else {
                    $row->subtotal = $price->price;
                }
                $row->taxes = ($row->subtotal * 0.1);
                $row->total = $row->subtotal + $row->taxes;
                
                $row->created = date('Y-m-d g:i:s');
                $row->token = md5($this->user->getToken().$row->created);
                
                //var_dump($row->toArray()); die;
                
                $row->save();
                
                $this->_redirect('cart/checkout/'.$row->id);
            }
            elseif($listing->main_type == 5)
            {
                /*
                Array
                (
                    [option] => 8
                    [rooms] => 2
                    [adults] => 4
                    [userids] => 29
                    [userstoken] => 47a0183b6dda816b9e012bb7a80698bc
                    [formids] => 98f4b1724db0da1d6b8029a40e35d9c7
                    [_task] => 6ffab885c2a7c356633c76013c63c275
                    [listingids] => 148
                    [listingstoken] => 6728edb0ec93cd4d9c7a289cd3803738
                    [cityid] => 25
                    [countryid] => 18
                    [checkin] => 2011-7-26
                    [checkout] => 2011-7-28
                )
                 * 
                 */
                $row = $this->cart->fetchNew();
                $row->user_id    = $this->user->getId();
                $row->checkin    = date('Y-m-d', strtotime($data['checkin']));
                $row->checkout   = date('Y-m-d', strtotime($data['checkout']));
                $row->option_id  = $data['option'];
                $row->adults     = $data['adults'];
                $row->kids       = 0;
                $row->listing_id = $listing->id;
                
                $fday = strtotime($row->checkin);
                $lday = strtotime($row->checkout);
                
                $nights = $lday - $fday;
                $nights = $nights / 86400;
                
                $option = $this->listings->getSchedule($data['option']);
                
                //echo '<pre>'; print_r($_POST); echo '</pre>'; die;
                
                $price = $this->listings->getOptionPrice($listing->id, $row->option_id);
                
                $total_people = $row->kids + $row->adults;
                
                $row->rate = $price->price;                
                if($price->type_id == 1){
                    $row->rate_description = 'Basic Price per night';
                } else {
                    $row->rate_description = 'Price for '. $option->name .' Room per night';
                }
                
                if($total_people > $price->max){
                    $aux   = $total_people % $price->max;
                    $rooms = ($total_people - $aux) / $price->max;
                    $rooms = ($aux > 0) ? $rooms + 1 : $rooms;
                } else {
                    $rooms = 1;
                }
                
                $_prices = array();
                $aux = $total_people;
                for($i = 0; $i < $rooms; $i++)
                {
                    $personas   = ($aux > $price->max) ? $price->max : $aux;
                    $basic      = $price->price;
                    $additional = ($personas > $price->additional_after) ? $price->additional * ($personas - $price->additional_after) : 0;
                    $_prices[] = array(
                        'personas' => $personas,
                        'basic'    => $basic,
                        'additional'=> $additional
                    );
                    $aux = $aux - $personas;
                }
                
                $row->additional = ($total_people > $price->additional_after) ? $price->additional : 0;
                $row->additional_description = ($row->additional > 0) ? 'Additional after '.$price->additional_after.' per person, per room, per night' : '' ;
                
                $row->rooms = $rooms;
                $row->nights = $nights;
                
                
                $subtotal = 0;
                foreach($_prices as $p){
                    $subtotal = $subtotal + (($p['basic'] + $p['additional']) * $nights);
                }
                $row->subtotal = $subtotal;
                $row->taxes = ($row->subtotal * 0.1);
                $row->total = $row->subtotal + $row->taxes;
                
                $row->created = date('Y-m-d g:i:s');
                $row->token = md5($this->user->getToken().$row->created);
                
                $row->max = $price->max;
                
                //var_dump($row->toArray()); die;
                
                $row->save();
                
                $this->_redirect('cart/checkout/'.$row->id);
            }
            else
            {
                $this->_redirect('/');
            }
        }
        else
            $this->_redirect('/');
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
        if($this->getRequest()->isPost())
        {
            $auth = Zend_Auth::getInstance();
            if(!$auth->hasIdentity())
                    $this->_redirect ('/login');
            
            $user = $auth->getIdentity();
            
            $id    = $_POST['trip'];
            
            $trip = $this->trips->get($id);            
            if($trip->token != $_POST['triptoken'])
                    throw new Exception();
            
            $date   = date('Y-m-d G:i:s', strtotime($_POST['checkin']));
            $adults = $_POST['adults'];
            $kids   = $_POST['kids'];
            $items  = array();
            $items2 = array();
            
            $cart = $this->cart->fetchNew();
            $cart->user_id  = $user->id;
            $cart->listing_id = $trip->id;
            $cart->checkin  = $date;
            $cart->adults   = $adults;
            $cart->kids     = $kids;
            $cart->rate     = 0;
            $cart->rate_description = $trip->title . ' - Trip Purchase';
            $cart->subtotal = 0;
            $cart->taxes    = 0;
            $cart->total    = 0;
            $cart->token    = md5($user->token . time());
            $cart->type     = 2;
            $cart->created  = date('Y-m-d H:i:s');
            
            $cart->save();
            
            $cartitems = new Zend_Db_Table('cartitems');            
            
            $listings = $this->trips->getListingOf($trip->id, false);
            
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
        
        if($_GET['error_message'])
            $this->view->error = $_GET['error_message'];
        
        $ids = $this->_getParam('task');
        
        $select = $this->cart->select();
        $select->where('id = ?', $ids);
        $cartitem = $this->cart->fetchRow($select);
        
        if(is_null($cartitem))
                $this->_redirect('/');
        
        if($cartitem->user_id != $this->user->getId())
                $this->_redirect('/');
        
        if($cartitem->type == 1){
            $listing = $this->listings->getListing($cartitem->listing_id);
            $country = $this->places->getPlaceById($listing->country_id);
            $city    = $this->places->getPlaceById($listing->city_id);
            $vendor  = $this->listings->getVendor($listing->vendor_id);
            $countries = $this->places->getPlaces(2);

            $billing = $this->billing->fetchRow("user_id = {$this->user->getId()}");

            $this->view->cartitem  = $cartitem;
            $this->view->listing   = $listing;
            $this->view->user      = $this->user->getData();
            $this->view->country   = $country;
            $this->view->city      = $city;
            $this->view->vendor    = $vendor;
            $this->view->countries = $this->places->getPlaces(2);
            $this->view->billing   = $billing;
        } elseif($cartitem->type == 2) {
            
            $trip = $this->trips->get($cartitem->listing_id);
            
            $billing = $this->billing->fetchRow("user_id = {$this->user->getId()}");
            
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
            $this->view->billing   = $billing;
            $this->view->items     = $items;
            $this->render('checkout-trip');
        } elseif($cartitem->type == 3) {
            
            $trip = $this->trips->getItn($cartitem->listing_id);
            
            $billing = $this->billing->fetchRow("user_id = {$this->user->getId()}");
            
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
            $this->view->billing   = $billing;
            $this->view->items     = $items;
            $this->render('checkout-trip');
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
                    $this->chargeReturnError('There is something wrong with the form please try later');
            if($data['userstoken'] != $this->user->getToken())
                    $this->chargeReturnError('There is something wrong with the form please try later');
            if($data['formids'] != md5($this->user->getToken().'checkout'))
                    $this->chargeReturnError('There is something wrong with the form please try later');
            if($data['_task'] != md5('purchase'))
                    $this->chargeReturnError('There is something wrong with the form please try later');
            
            $select = $this->cart->select();
            $select->where('id = ?', $data['cartitemid']);
            $select->where('user_id = ?', $this->user->getId());
            $cartitem = $this->cart->fetchRow($select);
            
            if(is_null($cartitem))
                    throw new Exception();
            if($cartitem->token != $data['cartitemtoken'])
                    throw new Exception();
            
            $_billing = array(
                'user_id' =>$this->user->getId(),
                'street1' =>$data['street1'],
                'street2' =>$data['street2'],
                'country' =>$data['country'],
                'city'    =>$data['city'],
                'state'   =>'',
                'zip'     =>$data['zip'],
                'specs'   =>$data['specs'],
                'location'=>$data['location'],
                'phone'   =>$data['phone'],
                'aboutus' =>$data['aboutus'],
                'updated' =>date('Y-m-d g:i:s')
            );
            
            $billing = $this->billing->fetchRow("user_id = {$this->user->getId()}"); 
            if(is_null($billing)){
                $_billing['created'] = $_billing['updated'];
                $this->billing->insert($_billing);
            } else {
                $this->billing->update($_billing, "id = {$billing->id}");
            }
            
            switch($data['method']){
                case 'paypal':
                    $service = new WS_Paypal(true);
                    $returnURL = "http://cristiantests.com/cart/confirm/paypal/".$cartitem->id;
                    $cancelURL = "http://cristiantests.com/cart/checkout/".$cartitem->id;
                    $service->proceed($cartitem->total, $returnURL, $cancelURL);
                    break;
                case 'creditcard':
                    $service   = new WS_Creditcard(true);
                    $returnURL = "http://cristiantests.com/cart/confirm/creditcard/".$cartitem->id;
                    $cancelURL = "http://cristiantests.com/cart/checkout/".$cartitem->id;
                    $service->proceed($cartitem->total, $returnURL, $cancelURL);
                    break;
                default: throw new Exception(); break;
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
                    
                    $this->_redirect('cart/tripinvoice/'.$trip->id);
                }
                break;
            default:
                $this->_redirect('cart/checkout/'.$this->_getParam('id'));
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
        $listings = $db->fetchAll($select, array(), 5);
        
        $select = $this->billing->select();
        $select->where('user_id = ?', $this->user->getId());
        $billing = $this->billing->fetchRow($select);
        
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
        $this->view->billing      = $billing;
        $this->view->listings     = $listings;
        $this->view->reservations = $reservations;
        $this->view->transaction  = $transaction;
    }
}
