<?php


class ProviderController extends Zend_Controller_Action
{
    protected $user;
    protected $messages;
    protected $reservations;
    protected $feeds;
    protected $listings;
    protected $places;
 
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            throw new Exception();
        } else {
            $this->user = new WS_User($auth->getStorage()->read());
            if($this->user->getRole() != 'provider'){
                throw new Exception();
            } else {
                $this->messages     = new WS_MessagesService();
                $this->reservations = new WS_ReservationsService();
                $this->feeds        = new WS_FeedsService();
                $this->listings     = new WS_ListingService();
                $this->places       = new WS_PlacesService();
            }
        }   
    }
    
    public function indexAction()
    {
        $new_messages = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countPending($this->user->getVendorId());
        
        $alerts = $this->getAlerts($new_messages, $new_reservations);
        
        $feeds = $this->feeds->getFeedsFor($this->user->getVendorId());
        
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->alerts           = $alerts;
        $this->view->feeds            = $feeds;
    }
    
    public function listingsAction()
    {
        $new_messages = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countPending($this->user->getVendorId());
        $template = 'listings';
        
        switch($this->_getParam('task','default')){
            case 'all':
                $listings = $this->listings->getListingsOf($this->user->getVendorId());
                
                $this->view->listings = $listings;
                $this->view->active_tab = 'All';
                break;
            case 'active';
                $listings = $this->listings->getListingsOf($this->user->getVendorId(), 1);
                $this->view->listings = $listings;
                $this->view->active_tab = 'Active';
                break;
            case 'inactive':
                $listings = $this->listings->getListingsOf($this->user->getVendorId(), 0);
                $this->view->listings = $listings;
                $this->view->active_tab = 'Inactive';
                break;
            case 'edit':
                $this->editListing();
                $template = 'edit-listing';
                break;
            case 'create':
                $this->createListing();
                $template = 'create-listing';
                break;
            case 'photos':
                $this->listingPhotos();
                $template = 'photos-listing';
                break;
            case 'pricing':
                $this->listingPricing();
                $template = 'pricing-listing';
                break;
            case 'faqs':
                $this->listingFAQs();
                $template = 'faqs-listing';
                break;
            case 'calendar':
                $this->listingCalendar();
                $template = 'calendar-listing';
                break;
            default:
                $listings = $this->listings->getListingsOf($this->user->getVendorId());
                $this->view->listings = $listings;
                $this->view->active_tab = 'All';
                break;            
        }
        
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->statuses         = array('Inactive','Active');
        
        $this->render($template);
    }
    
    private function getAlerts($msgs, $reserv)
    {
        $alerts = array();
        if($msgs > 0){
            $alerts[] = array(
                'class'   => 'messages',
                'url'     => 'inbox',
                'message' => 'You have '.$msgs.' new messages'
            );
        } 
        if($reserv > 0){
            $alerts[] = array(
                'class'   => 'reservations',
                'url'     => 'reservations',
                'message' => 'You have '.$reserv.' reservation pending approval'
            );
        }
        return $alerts;
    }
    
    private function editlisting()
    {   
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            
            $listing = $this->listings->getListing($ids, $this->user->getVendorId());
            
            if($this->getRequest()->isPost())
                $this->editListingPostHandler($listing);
            
            $main_cat   = $this->listings->getCategory($listing->main_type);            
            $categories = $this->listings->getSubCategoriesOf($main_cat->id);
            $tabs       = $this->listings->getTabsOf($listing->id);
            $attrs      = $this->listings->getAttributesOf($listing->id);
            $tags       = $this->listings->getTagsFor($main_cat);
            $countries  = $this->places->getPlaces(2);
            $cities     = $this->places->getPlaces(3, $listing->country_id);
            $schedules  = $this->listings->getSchedulesOf($listing->id);
            
            $_listing_types = $this->listings->getListingTypesOf($listing->id);
            $l_types = array();
            foreach($_listing_types as $lt)
                $l_types[] = $lt->listingtype_id;
            
            $_tags = $this->listings->getTagsOf($listing->id);
            $l_tags = array();
            foreach($_tags as $t)
                $l_tags[] = $t->tag_id;
            
            $this->view->listing        = $listing;
            $this->view->main_category  = $main_cat;
            $this->view->categories     = $categories;
            $this->view->listing_types  = $l_types;
            $this->view->tabs           = $tabs;
            $this->view->attributes     = $attrs;
            $this->view->tags           = $tags;
            $this->view->listing_tags   = $l_tags;
            $this->view->countries      = $countries;
            $this->view->cities         = $cities;
            $this->view->schedules      = $schedules;
        }
    }
    
    private function createlisting()
    {
        
    }
    
    private function isValidId($ids)
    {
        if($ids == 'default')
            $this->_redirect ('provider/listings');
        
        $ids = (int) $ids;
        if(!is_int($ids) or $ids <= 0)
            throw new Exception();
        
        return true;
    }
    
    private function editListingPostHandler($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;   
        switch($_POST['_task']){
            case md5('edit_tab'):
                $this->editListingUpdateTab($listing);
                break;
            case md5('add_tab'):
                $this->editListingAddTab($listing);
                break;
            case md5('edit_attr'):
                $this->editListingUpdateAttr($listing);
                break;
            case md5('add_attr'):
                $this->editListingAddAttr($listing);
                break;
            case md5('edit_sch'):
                $this->editListingUpdateSch($listing);
                break;
            case md5('add_sch'):
                $this->editListingAddSch($listing);
                break;
            case md5('edit_listing'):
                $this->saveListing($listing);
                break;
            case md5('delete_tab'):
                $this->editListingDeleteTab($listing);
                break;
            case md5('delete_attr'):
                $this->editListingDeleteAttr($listing);
                break;
            case md5('delete_sch'):
                $this->editListingDeleteSch($listing);
                break;
            default:
                throw new Exception();
        }
    }
    
    private function editListingUpdateTab($listing){
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_tab_'.$data['tab_id']))
            throw new Exception();
        $tab = $this->listings->getTab($data['tab_id']);
        if(is_null($tab))
            throw new Exception();
        if($data['tab_token'] != $tab->token)
            throw new Exception();    
        if(empty ($data['title']))
            throw new Exception();
        if(empty ($data['text']))
            throw new Exception();
        
        $tab->label   = $data['title'];
        $tab->title   = $data['title'];
        $tab->text    = $data['text'];
        $tab->updated = date('Y-m-d H:i:s');
        $tab->save();
    }
    
    private function editListingDeleteTab($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_tab_delete_'.$data['tab_id']))
            throw new Exception();
        $tab = $this->listings->getTab($data['tab_id']);
        if(is_null($tab))
            throw new Exception();
        if($data['tab_token'] != $tab->token)
            throw new Exception();
        
        $tab->delete();
    }
    
    private function editListingAddTab($listing){
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_tab_new'))
            throw new Exception();
        if(empty ($data['title']))
            throw new Exception();
        if(empty ($data['text']))
            throw new Exception();
        
        $this->listings->addTabTo($listing->id, $data);
    }
    
    private function editListingAddAttr($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;   
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_attr_new'))
            throw new Exception();
        if(empty ($data['name']))
            throw new Exception();
        if(empty ($data['value']))
            throw new Exception();
        
        $this->listings->addAttrTo($listing->id, $data);        
    }
    
    private function editListingUpdateAttr($listing){
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;   
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_attr_'.$data['attr_id']))
            throw new Exception();
        $attr = $this->listings->getAttr($data['attr_id']);
        if(is_null($attr))
            throw new Exception();
        if(empty ($data['name']))
            throw new Exception();
        if(empty ($data['value']))
            throw new Exception();
        
        $attr->name   = $data['name'];
        $attr->value  = $data['value'];
        $attr->save();
    }
    
    private function editListingDeleteAttr($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_attr_delete_'.$data['attr_id']))
            throw new Exception();
        $attr = $this->listings->getAttr($data['attr_id']);
        if(is_null($attr))
            throw new Exception();
        
        $attr->delete();
    }
    
    private function editListingUpdateSch($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_sch_'.$data['sch_id']))
            throw new Exception();
        $sch = $this->listings->getSchedule($data['sch_id']);
        if(is_null($sch))
            throw new Exception();
        
        if(empty ($data['start_hour']))
            throw new Exception();
        if(empty ($data['end_hour']))
            throw new Exception();
        if(empty ($data['name']))
            throw new Exception();
        
        $sch->name = $data['name'];
        
        $starting = $data['start_hour'] .':'.$data['start_min'] .':00 '.$data['start_time'];
        $ending   = $data['end_hour']   .':'.$data['end_min']   .':00 '.$data['end_time'];
        
        $starting = date('H:i:s',strtotime($starting));
        $ending   = date('H:i:s',strtotime($ending));        
        
        $sch->starting   = $starting;
        $sch->ending     = $ending;
        
        $sch->save();
    }
    
    private function editListingDeleteSch($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_sch_delete_'.$data['sch_id']))
            throw new Exception();
        $sch = $this->listings->getSchedule($data['sch_id']);
        if(is_null($sch))
            throw new Exception();
        
        $sch->delete();
    }
    
    private function editListingAddSch($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'form_sch_new'))
            throw new Exception();
        if($listing->main_type != 5){
            if(empty ($data['start_hour']))
                throw new Exception();
            if(empty ($data['end_hour']))
                throw new Exception();
        }
        if(empty ($data['name']))
            throw new Exception();
        
        $this->listings->addScheduleTo($listing, $data);
    }
    
    private function saveListing($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception();
        if($data['listing_token'] != $listing->token)
            throw new Exception();
        if($data['form_id'] != md5($listing->token.'edit-listing'))
            throw new Exception();

        $listing->title = $data['title'];
        $listing->description = $data['description'];
        
        if($data['country_id'] != $listing->country_id){
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->query('Update places set listings = listings - 1 where id = '.$listing->country_id);
            $db->query('Update places set listings = listings + 1 where id = '.$data['country_id']);
            $listing->country_id = $data['country_id'];
            
        }
        if($data['city_id'] != $listing->city_id){
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->query('Update places set listings = listings - 1 where id = '.$listing->city_id);
            $db->query('Update places set listings = listings + 1 where id = '.$data['city_id']);
            $listing->city_id    = $data['city_id'];
        }
        
        $this->listings->saveTypes($listing->id, $data['types']);
        $this->listings->saveTags($listing->id, $data['tags']);
        
        $listing->save();
    }
    
    private function listingPhotos()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            
            $listing  = $this->listings->getListing($ids, $this->user->getVendorId());
            $main_cat = $this->listings->getCategory($listing->main_type);
            if($this->getRequest()->isPost())
                $this->listingPhotosPostHandler($listing);
            
            $pictures = $this->listings->getPictures($listing->id);
            $this->view->listing        = $listing;
            $this->view->main_category  = $main_cat;
            $this->view->pictures       = $pictures;
            
            
        }
    }
    
    private function listingPhotosPostHandler($listing)
    {
        switch($_POST['_task']){
            case md5('edit_picture'):
                $this->listingPhotosUpdatePic($listing);
                break;
            case md5('delete_picture'):
                $this->listingPhotosDeletePic($listing);
                break;
            default:
                throw new Exception();
        }
    }
    
    private function listingPhotosUpdatePic($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;        
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_picture_'.$data['img_id']))
            throw new Exception('Form id violated');
        $img = $this->listings->getPhoto($listing->id, $data['img_id']);
        if(is_null($img))
            throw new Exception('Img not found');
        
        $img->location = $data['location'];
        if(isset($data['main'])){
            $this->listings->removeMainPhoto($listing->id);
            $img->main = 1;
        }
        $img->save();
        $this->view->activephoto = $img->id;
    }
    
    private function listingPhotosDeletePic($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_picture_delete_'.$data['img_id']))
            throw new Exception('Form id violated');
        $img = $this->listings->getPhoto($listing->id, $data['img_id']);
        if(is_null($img))
            throw new Exception('Img not found');

        unlink(realpath(APPLICATION_PATH.'/../../'.$img->url));
        $img->delete();
    }
    
    private function listingPricing()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing   = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost())
                $this->listingPricingPostHandler($listing);
            
            $main_cat  = $this->listings->getCategory($listing->main_type);
            $schedules = $this->listings->getSchedulesOf($listing->id);
            $seasons   = $this->listings->getSeasonsOf($listing->id);
            $termtypes = array(
                'Flexible: Full refund 1 day prior to arrival, except fees',
                'Moderate: Full refund 5 days prior to arrival, except fees',
                'Strict: 50% refund up until 1 week prior to arrival, except fees'
            );
            
            if($listing->main_type != 5){
                $basic_price = $this->listings->getBasePrice($listing->id);
                $season      = $this->listings->getSeasonPrices($listing->id);
            } else {
                $basic_price = $this->listings->getBasePrice($listing->id);
                $season      = $this->listings->getSeasonPrices($listing);
                $sch         = $this->listings->getSchPrices($listing);
            }
            $this->view->listing        = $listing;
            $this->view->main_category  = $main_cat;
            $this->view->schedules      = $schedules;
            $this->view->seasons        = $seasons;
            $this->view->termtypes      = $termtypes;
            $this->view->basic_price    = $basic_price;
            $this->view->weekend        = $weekend;
            $this->view->season         = $season;
            $this->view->sch            = $sch;
        }
    }
    
    private function listingPricingPostHandler($listing)
    {
        switch($_POST['_task']){
            case md5('update_pricing'):
                $this->listingPricingUpdate($listing);
                break;
            default:
                throw new Exception();
        }
    }
    
    private function listingPricingUpdate($listing)
    {
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_pricing'))
            throw new Exception('Form id violated');
        
        if(empty($data['currency']))
            throw new Exception('Currency cannot be empty');
        if(empty($data['price']))
            throw new Exception('Price cannot be empty');
        $price = (float) $data['price'];
        if(!is_float($price))
            throw new Exception('Price need to be a valid price');
        if(empty($data['min']))
            throw new Exception('Minimun People cannot be empty');
        if(empty($data['max']))
            throw new Exception('Maximun People cannot be empty');
        if(empty($data['termtype']))
            throw new Exception('Cancelation Policy type cannot be empty');
        if(empty($data['policy']))
            throw new Exception('Policy Description cannot be empty');
        
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        
        $this->listings->updatePricesOf($listing, $data);        
    }
    
    private function listingFAQs()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing   = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost())
                $this->listingFAQsPostHandler($listing);
            
            $main_cat  = $this->listings->getCategory($listing->main_type);
            $faqs      = $this->listings->getFAQsOf($listing->id);
            
            $this->view->listing        = $listing;
            $this->view->main_category  = $main_cat;
            $this->view->faqs           = $faqs;
        }
    }
    
    private function listingFAQsPostHandler($listing)
    {
        switch($_POST['_task']){
            case md5('update_faq'):
                $this->listingFAQsUpdate($listing);
                break;
            case md5('add_faq'):
                $this->listingFAQsAdd($listing);
                break;
            default:
                throw new Exception();
        }
    }
    
    private function listingFAQsUpdate($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_faq'))
            throw new Exception('Form id violated');
        
        $this->listings->updateFAQsOf($listing->id, $data);
    }
    
    private function listingFAQsAdd($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'new_faq'))
            throw new Exception('Form id violated');
        
        if(empty($data['question']))
            throw new Exception('Question cannot be empty');
        if(empty($data['answer']))
            throw new Exception('Answer cannot be empty');
        
        $this->listings->addFAQsTo($listing->id, $data);
    }
    
    private function listingCalendar()
    {
        $ids = $this->_getParam('id','default');
        if($this->isValidId($ids)){
            $listing   = $this->listings->getListing($ids, $this->user->getVendorId());
            if($this->getRequest()->isPost())
                $this->listingCalendarPostHandler($listing);
            
            $this->createCalendar($listing);
            
            $main_cat  = $this->listings->getCategory($listing->main_type);
            
            $this->view->listing        = $listing;
            $this->view->main_category  = $main_cat;
        }
    }
    
    private function createCalendar($listing)
    {
        $less     = array(0,1,2,3,4,5,6,0);
        $p_months = array(0,12,1,2,3,4,5,6,7,8,9,10,11);
        $n_months = array(0,2,3,4,5,6,7,8,9,10,11,12,1);
        
        $year  = $this->_getParam('year', date('Y'));
        $month = $this->_getParam('month', date('n'));
        
        $start_at = (strtotime($year.'-'.$month.'-01')) - (86400 * $less[(date('N', (strtotime($year.'-'.$month.'-01'))))]);
        $today    = date('Y-m-d');
        
        $days = array();
        for($i=1;$i<43;$i++){
            
            $aux   = $this->listings->getDayFromCalendar($listing->id,  date('Y-m-d', $start_at));
            
            if(is_null($aux))
                $aux1 = '';
            else 
                $aux1 = $aux->state;
            
            $class = (date('n', $start_at) != $month) ? 'disabled' : $aux1;
            $class = ($start_at < strtotime($today)) ? 'disabled' : $class;
            $days[] = array(
                'class' => $class, 
                'num'   => date('j', $start_at),
                'date'  => date('Y-m-d', $start_at)
            );
            $start_at = $start_at + 86400;
        }
        
        $this->view->prevmonth    = $p_months[$month];
        $this->view->nextmonth    = $n_months[$month];
        $this->view->prevyear     = ($month == 1) ? $year - 1 : $year;
        $this->view->nextyear     = ($month == 12) ? $year + 1 : $year;
        $this->view->currentMonth = date('F Y', strtotime($year.'-'.$month.'-01'));
        $this->view->days         = $days;
    }
    
    private function listingCalendarPostHandler($listing)
    {
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
        $data = $_POST;
        if($data['ids'] != $listing->id)
            throw new Exception('Listing not found');
        if($data['listing_token'] != $listing->token)
            throw new Exception('Listing Token Violated');
        if($data['form_id'] != md5($listing->token .'form_calendar_'.$data['day']))
            throw new Exception('Form id violated');
        
        $this->listings->saveDayInCalendar($listing->id, $data);
    }
    
    public function inboxAction()
    {        
        if($this->getRequest()->isPost())
            $this->inboxPostHandler();
        
        switch ($this->_getParam('task')){
            case 'unread':
                $messages = $this->messages->getMessagesOf($this->user->getId(),'unread');
                $this->view->active_tab = 'unread';
                break;
            case 'starred':
                $messages = $this->messages->getMessagesOf($this->user->getId(),'starred');
                $this->view->active_tab = 'starred';
                break;
            case 'sent':
                $messages = $this->messages->getMessagesOf($this->user->getId(),'sent');
                $this->view->active_tab = 'sent';
                break;
            case 'trash':
                $messages = $this->messages->getMessagesOf($this->user->getId(),'trash');
                $this->view->active_tab = 'trash';
                break;
            default:
                $messages = $this->messages->getMessagesOf($this->user->getId());
                $this->view->active_tab = 'all';
                break;
        }
        $new_messages = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countPending($this->user->getVendorId());
        
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->messages         = $messages;
    }
    
    public function reservationsAction()
    {
        if($this->getRequest()->isPost())
            $this->inboxPostHandler();
        
        switch($this->_getParam('task')){
            case 'pending':
                $_reservs   = $this->reservations->getPending($this->user->getVendorId());
                $active_tab = 'Waiting Approval';
                $template   = 'pending-reservations';
                break;
            case 'history':
                $_reservs   = $this->reservations->getHistory($this->user->getVendorId());
                $template   = 'history-reservations';
                break;
            default;
                $_reservs   = $this->reservations->getIncoming($this->user->getVendorId());
                $active_tab = 'Incoming';
                $template   = 'reservations';
                break;
        }
        
        $new_messages = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countPending($this->user->getVendorId());
        
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        $this->view->active_tab       = $active_tab;
        $this->view->reservations     = $_reservs;
        
        $this->render($template);
    }
    
    public function accountAction()
    {
        //print md5('admin'); die;
        $new_messages     = $this->messages->countNew($this->user->getId());
        $new_reservations = $this->reservations->countPending($this->user->getVendorId());
        
        $template = 'account';
        switch($this->_getParam('task')){
            case 'default':
                $this->accountSettingsTask();
                break;
            case 'notifications':
                $this->accountNotificationsTask();
                $template = 'account-notifications';
                break;
            case 'payments':
                $template = 'account-payments';
                break;
            case 'transactions':
                $template = 'account-transactions';
                break;
            default:
                throw new Exception();                
        }
        
        $this->view->user             = $this->user->getData();
        $this->view->vendor           = $this->user->getVendorData();
        $this->view->new_messages     = $new_messages;
        $this->view->new_reservations = $new_reservations;
        
        $this->render($template);
    }
    
    private function accountSettingsTask(){
        $questions        = $this->user->getDefaultQuestions();
        if($this->getRequest()->isPost())
            $this->accountSettingsPostHandler();
        
        $this->view->sq     = $questions;
    }
    
    private function accountSettingsPostHandler()
    {
        switch($_POST['_task']){
            case md5('update_name'):
                $this->validatePostData('provider_form_name');
                if(!empty($_POST['name'])){
                    $vendor = $this->user->getVendorData();
                    $vendor->name = $_POST['name'];
                    $vendor->save();
                } else 
                    throw new Exception();
                break;
            case md5('update_email'):
                $this->validatePostData('provider_form_email');
                if(!empty($_POST['email'])){
                    $vendor = $this->user->getVendorData();
                    $vendor->email = $_POST['email'];
                    $vendor->save();
                    
                    $user = $this->user->getData();
                    $user->email = $_POST['email'];
                    $user->save();
                } else 
                    throw new Exception();
                break;
            case md5('update_login'):
                $this->validatePostData('provider_form_login');
                if(!empty($_POST['password']) &&
                   !empty($_POST['newpassword']) &&
                   $_POST['newpassword'] == $_POST['newpassword2']){
                    $user = $this->user->getData();
                    if($user->password == md5($_POST['password']))
                        $user->password = md5($_POST['newpassword']);
                    else 
                        throw new Exception();
                    $user->save();
                } else
                    throw new Exception();
                break;
            case md5('update_photo'):
                $this->validatePostData('provider_form_photo');
                //echo '<pre>'; var_dump($_FILES); echo '</pre>'; die;
                if (!empty($_FILES)) {
                    $tempFile   = $_FILES['picture']['tmp_name'];
                    $targetPath = realpath(APPLICATION_PATH.'/../public/images/providers/');
                    $targetFile = str_replace('//','/',$targetPath) .'/'. $this->user->getVendorId().substr(md5($this->user->getToken()), 0, 10) . '.jpg';
                    //echo $targetFile; die;
                    mkdir(str_replace('//','/',$targetPath), 0755, true);
                    move_uploaded_file($tempFile,$targetFile);
                    $this->user->setVendorImage($this->user->getVendorId().substr(md5($this->user->getToken()), 0, 10) . '.jpg');
                } 
                break;
            case md5('update_phone'):
                $this->validatePostData('provider_form_phone');
                if(!empty($_POST['phone'])){
                    $vendor = $this->user->getVendorData();
                    $vendor->phone = $_POST['phone'];
                    $vendor->save();
                } else 
                    throw new Exception();                
                break;
            case md5('update_fax'):
                $this->validatePostData('provider_form_fax');
                if(!empty($_POST['fax'])){
                    $vendor = $this->user->getVendorData();
                    $vendor->fax = $_POST['fax'];
                    $vendor->save();
                } else 
                    throw new Exception(); 
                break;
            case md5('update_location'):
                $this->validatePostData('provider_form_location');
                if(!empty($_POST['address'])){
                    $vendor = $this->user->getVendorData();
                    $vendor->address = $_POST['address'];
                    $vendor->save();
                } else 
                    throw new Exception();
                break;
            case md5('update_website'):
                $this->validatePostData('provider_form_website');
                if(!empty($_POST['website'])){
                    $vendor = $this->user->getVendorData();
                    $vendor->website = $_POST['website'];
                    $vendor->save();
                } else 
                    throw new Exception();
                break;
            case md5('update_question'):
                $this->validatePostData('provider_form_question');
                if(!empty($_POST['question']) && !empty($_POST['answer'])){
                    $user = $this->user->getData();
                    $user->question = $_POST['question'];
                    $user->answer   = $_POST['answer'];
                    $user->save();
                } else 
                    throw new Exception();
                break;
            default:
                break;
        }
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
    }
    
    private function validatePostData($fid)
    {
        $data = $_POST;
        if($data['ids'] != $this->user->getId())
            throw new Exception();
        if($data['vids'] != $this->user->getVendorId())
            throw new Exception();
        if($data['user_token'] != $this->user->getToken())
            throw new Exception();
        if($data['form_id'] != md5($this->user->getToken().$fid))
            throw new Exception();
    }
    
    private function accountNotificationsTask()
    {
        $notifications = $this->user->getDefaultNotifications();
        if($this->getRequest()->isPost())
            $this->accountNotificationsPostHandler();        
            
        $usersettings  = $this->user->getNotificationsSettings();    
        $this->view->notifications = $notifications;
        $this->view->usersettings  = $usersettings;
    }
    
    private function accountNotificationsPostHandler()
    {
        $this->validatePostData('user_form_notifications');
        $this->user->setNotifications($_POST);
        //echo '<pre>'; var_dump($_POST); echo '</pre>'; die;
    }
}