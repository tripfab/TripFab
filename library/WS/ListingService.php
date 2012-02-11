<?php

class WS_ListingService {
    
    /**
     * 
     */
    const CAT_TODO = 1;
    
    /**
     * 
     */
    const CAT_TOEAT = 2;
    
    /**
     * 
     */
    const CAT_TOSHOP = 3;
    
    /**
     * 
     */
    const CAT_TOSTAY = 5;
    
    /**
     *
     * @var Model_Listings 
     */
    protected $listings;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $listings_types_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $tags_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $tabs_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $faqs_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $listing_attributes;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $listing_listingtypes_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $listing_tags_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $schedules_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $pictures_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $seasons_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $prices_db;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $calendar;
    
    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $vendors;
    
    /**
     *
     * @var Zend_Cache_Core
     */
    protected $cache;
    
    /**
     *
     * @var Boolean
     */
    public $use_cache;
    
    /**
     * 
     */
    public function __construct() 
    {
        $this->listings = new Model_Listings();
        
        $this->listings_types_db = new Model_ListingTypes();
        
        $this->tags_db = new Model_Tags();
        
        $this->tabs_db = new Model_ListingTabs();
        
        $this->faqs_db = new Model_ListingFaqs();
        
        $this->listing_attributes = new Model_ListingAttributes();
        
        $this->listing_listingtypes_db = new Model_ListingListingtypes();
        
        $this->listing_tags_db = new Model_ListingTags();
        
        $this->schedules_db = new Model_ListingSchedules();
        
        $this->pictures_db = new Model_ListingPictures();
        
        $this->seasons_db = new Model_Seasons();
        
        $this->prices_db = new Model_Prices();
        
        $this->calendar = new Model_Calendar();
        
        $this->vendors = new Model_Vendors();
        
        if(Zend_Registry::isRegistered('cache'))
            $this->cache = Zend_Registry::get('cache');
        
        $this->use_cache = false;
    }
    
    public function getDefaultListing($vendor)
    {
        $args = func_get_args();
        $cacheId = "LS_getDefaultListing_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $select = $this->listings->select();
            $select->where('vendor_id = ?', $vendor);
            $select->where('status != ?', 3);
            $select->order('created ASC');
            $row = $this->listings->fetchRow($select);
        
            if($this->use_cache)
                $this->cache->save($row, $cacheId);
        } else {
            $row = $this->cache->load($cacheId);
        }
        return (!is_null($row)) ? $row : null;
    }
    
    public function getDefaultLandscapes()
    {
        $cacheId = "LS_getDefaultLandscapes";
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $table  = new Zend_Db_Table('landscapes');
            $result = $table->fetchAll();
            
            if($this->use_cache)
                $this->cache->save($result, $cacheId);
            
        } else {
            $result = $this->cache->load($cacheId);
        }
        
        return $result;
    }
    
    public function getLandscapesOf($listing)
    {
        $args = func_get_args();
        $cacheId = "LS_getLandscapesOf_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $table = new Zend_Db_Table('listing_landscapes');
            $select = $table->select();
            $select->where('listing_id = ?', $listing);
            $lands = $table->fetchAll($select);
            
            if($this->use_cache)
                $this->cache->save($lands, $cacheId);
            
        } else {
            $lands = $this->cache->load($cacheId);
        }
        
        return $lands;
    }
    
    public function getHelpSettings($user)
    {
        $helpmessages = new Zend_Db_Table('helpmessages');
        $select = $helpmessages->select();
        $select->where('user_id = ?', $user);
        
        $help = $helpmessages->fetchRow($select);
        if(is_null($help)){
            $help = $helpmessages->fetchNew();
            $help->user_id = $user;
            $help->save();
        }
        
        return $help;
    }
    
    /**
     *
     * @param integer $place
     * 
     * @return Zend_Db_Table_Rowset 
     */
    public function getTopFreeFrom($place)
    {
        $args = func_get_args();
        $cacheId = "LS_getTopFreeFrom_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $select = $this->listings->select();
            $select->where('country_id = ?',$place);
            $select->order('loves DESC');
            $select->limit(5);
            $free_listings = $this->listings->fetchAll($select);
            if(is_null($free_listings))
                $free_listings = array();
            
            if($this->use_cache)
                $this->cache->save($free_listings, $cacheId);
            
        } else {
            $free_listings = $this->cache->load($cacheId);
        }
        
        return $free_listings;
    }
    
    public function getDetails($listing)
    {
        $args = func_get_args();
        $cacheId = "LS_getDetails_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $details_db = new Zend_Db_Table('listing_details');
            $select = $details_db->select();
            $select->where('listing_id = ?', $listing);
            $details = $details_db->fetchAll($select);
            
            if($this->use_cache)
                $this->cache->save($details, $cacheId);
            
        } else {
            $details = $this->cache->load($cacheId);
        }
        
        return $details;
    }
    
    /**
     *
     * @param integer $place
     * 
     * @return Zend_Db_Table_Rowset 
     */
    public function getTopThingsToDoIn($place)
    {
        $args = func_get_args();
        $cacheId = "LS_getTopThingsToDoIn_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $top_thingToDo = $this->_getTop5($place, self::CAT_TODO);
            
            if($this->use_cache)
                $this->cache->save($top_thingToDo, $cacheId);
            
        } else {
            $top_thingToDo = $this->cache->load($cacheId);
        }
            
        return $top_thingToDo;
        
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset 
     */
    public function getMainCategories($assoc = false)
    {
        $args = func_get_args();
        $cacheId = "LS_getMainCategories_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            if(!$assoc){
                $select = $this->listings_types_db->select();
                $select->where('parent_id is null');
                $select->order('weight asc');
                $result = $this->listings_types_db->fetchAll($select);
            } else {
                $db = Zend_Db_Table::getDefaultAdapter();
                $select = $db->select();
                $select->from('listing_types');
                $select->where('parent_id is null');
                $select->order('weight asc');
                $result = $db->fetchAssoc($select, array(), Zend_Db::FETCH_OBJ);;
            }
            
            if($this->use_cache)
                $this->cache->save($result, $cacheId);
            
        } else {
            $result = $this->cache->load($cacheId);
        }
        
        return $result;
    }
    
    /**
     *
     * @param string $idf
     * 
     * @return Zend_Db_Table_Row 
     */
    public function getCategoryByIdf($idf)
    {
        $args = func_get_args();
        $cacheId = "LS_getCategoryByIdf_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $active_cat_idf = ucwords(str_replace('-',' ',$idf));
            $active_category = $this->listings_types_db->fetchRow('name = "'.$active_cat_idf.'"');
            
            if($this->use_cache)
                $this->cache->save($active_category, $cacheId);
            
        } else {
            $active_category = $this->cache->load($cacheId);
        }
        
        if(is_null($active_category))
            throw new Exception;
        
        return $active_category;
    }
    
    public function getCategory($id)
    {
        $args = func_get_args();
        $cacheId = "LS_getCategory_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $category = $this->listings_types_db->fetchRow('id = '.$id);
            
            if($this->use_cache)
                $this->cache->save($category, $cacheId);
            
        } else {
            $category = $this->cache->load($cacheId);
        }
        if(is_null($category))
            throw new Exception();
        
        return $category;
    }
    
    /**
     *
     * @param integer $cat
     * 
     * @return Zend_Db_Table_Rowset 
     */
    public function getSubCategoriesOf($cat)
    {
        $args = func_get_args();
        $cacheId = "LS_getSubCategoriesOf_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $select = $this->listings_types_db->select();
            $select->where('parent_id = ?',$cat);
            $select->order('name ASC');
            $subcategories = $this->listings_types_db->fetchAll($select);
            
            if($this->use_cache)
                $this->cache->save($subcategories, $cacheId);
            
        } else {
            $subcategories = $this->cache->load($cacheId);
        }
        
        if(is_null($subcategories))
            $subcategories = array();
        
        return $subcategories;
    }
    
    /**
     *
     * @param integer $place
     * @param integer $cat
     * 
     * @return array 
     */
    protected function _getTop5($place, $cat)
    {
        $args = func_get_args();
        $cacheId = "LS__getTop5_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $lisring_types = $this->listings_types_db->fetchAll('parent_id = '.$cat);

            $select = $this->listings->select();
            foreach($lisring_types as $lt)
                $select->orWhere('main_type = ?', $lt->id);

            $select->where('city_id = ?', $place);
            $select->order('loves DESC');
            $select->limit(5,0);

            $top5 = $this->listings->fetchAll($select);
            if(is_null($top5))
                $top5 = array();
            
            if($this->use_cache)
                $this->cache->save($top5, $cacheId);
            
        } else {
            $top5 = $this->cache->load($cacheId);
        }
        return $top5;
    }
    
    public function getTagsFor($cat)
    {
        $args = func_get_args();
        $cacheId = "LS_getTagsFor_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $select = $this->tags_db->select();
        
            if($cat->id == self::CAT_TODO){
                $subcats = $this->getSubCategoriesOf($cat->id);
                $select->where('listing_type = ?', $subcats[0]->id);} 
            elseif((is_null($cat->parent_id)) or ($cat->parent_id == self::CAT_TODO))
                $select->where('listing_type = ?', $cat->id);
            else 
                $select->where('listing_type = ?', $cat->parent_id);
            $tags = $this->tags_db->fetchAll($select);
            if(is_null($tags))
                $tags = array();
            
            if($this->use_cache)
                $this->cache->save($tags, $cacheId);
            
        } else {
            $tags = $this->cache->load($cacheId);
        }
        
        return $tags;
    }
    
    public function getTagsOf($listing)
    {
        $args = func_get_args();
        $cacheId = "LS_getTagsOf_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $select = $this->listing_tags_db->select();
            $select->where('listing_id = ?', $listing);
            $tags = $this->listing_tags_db->fetchAll($select);
            
            if($this->use_cache)
                $this->cache->save($tags, $cacheId);
            
        } else {
            $tags = $this->cache->load($cacheId);
        }
        
        return $tags;
    }
    
    public function getListings($cat, $place)
    {
        $args = func_get_args();
        $cacheId = "LS_getListings_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {
        
            $select = $this->listings->select();
            if($cat->id == self::CAT_TODO){
                $subcats = $this->getSubCategoriesOf($cat->id);
                $select->where('main_type = ?', $subcats[0]->id);
                $select->where('city_id = ?', $place);
                $select->order('listings.loves DESC');
                $select->limit(12, 0);
                //var_dump($select->assemble()); die;
                $lists = $this->listings->fetchAll($select);
            } 
            elseif((is_null($cat->parent_id)) or ($cat->parent_id == self::CAT_TODO)){
                $select->where('main_type = ?',$cat->id);
                $select->where('city_id = ?', $place);
                $select->order('listings.loves DESC');
                $select->limit(12, 0);
                $lists = $this->listings->fetchAll($select);
            } 
            else {
                $db = $this->listings->getDefaultAdapter();
                $select = $db->select();
                $select->from('listings');
                $select->join('listing_listingtypes', 'listings.id = listing_listingtypes.listing_id');
                $select->join('listing_types', 'listing_listingtypes.listingtype_id = listing_types.id');
                $select->where('listings.city_id = ?', $place);
                $select->where('listing_types.id = ?', $cat->id);
                $select->order('listings.loves DESC');
                $select->limit(12, 0);

                $lists = $db->fetchAll($select->assemble(), array(), Zend_Db::FETCH_OBJ);
            }
            if(is_null($lists))
                $lists = array();
            
            if($this->use_cache)
                $this->cache->save($lists, $cacheId);
            
        } else {
            $lists = $this->cache->load($cacheId);
        }
        return $lists;
    }
    
    public function getListings2($place, $cat, $subcat, $sort, $stars, $pricemin = 0, $pricemax = 3000)
    {
        $args = func_get_args();
        $cacheId = "LS_getListings2_".md5(print_r($args, true));
        
        if(!$this->use_cache || !$this->cache->test($cacheId)) {
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from('listings');
            //$select->where('listings.status = ?', 1);
            $select->where('listings.city_id = ?', $place);
            if($cat != 'all'){
                $_cat = $this->getCategoryByIdf($cat);
                $select->where('listings.main_type = ?', $_cat->id);}
            if($subcat != 'all'){
                $select->join('listing_listingtypes', 'listing_listingtypes.listing_id = listings.id' ,array('subcat' => 'listingtype_id'));
                if(count($subcat) == 1) {
                    $select->where('listing_listingtypes.listingtype_id = ?', reset($subcat));
                } else {
                    $i = 0;
                    foreach($subcat as $sc) {
                        if($i === 0) $select->where('(listing_listingtypes.listingtype_id = ?', $sc);
                        elseif($i === (count($subcat) - 1)) $select->orWhere('listing_listingtypes.listingtype_id = ?)', $sc);
                        else $select->orWhere('listing_listingtypes.listingtype_id = ?', $sc);
                        $i++;}
                }
            }
            //echo $select->assemble(); die;
            if($sort != 'newest'){
                switch($sort){
                    case 'popular': $select->order('listings.views DESC'); break;
                    case 'name'   : $select->order('listings.title ASC'); break;
                    case 'free'   : $select->where('listings.price = ?',0); break;
                    case 'rating' : $select->order('listings.loves DESC');break;
                    case 'lowest' : $select->where('listings.price <> ?',0);
                        $select->order('listings.price ASC'); break;
                    case 'highest': $select->where('listings.price <> ?',0);
                        $select->order('listings.price DESC'); break;
                    default: break;
                }}
            if($stars != 'all'){
                $_stars = (int) str_replace('-stars','',$stars);
                if(is_int($_stars) && $_stars > 0 && $_stars < 6){
                    $select->where('listings.rate = ?', $_stars);
                }}

           $select->join('vendors','listings.vendor_id = vendors.id',array('vendor_name'=>'name'));
           //print $select->assemble(); //die;

           //$count = 9;
           //$select->limit($count);

           $select->where('listings.price >= ?', $pricemin);
           $select->where('listings.price <= ?', $pricemax);

           $listings = $db->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
           
           if($this->use_cache)
               $this->cache->save($listings, $cacheId, array(), 86400);
       } else {
           $listings = $this->cache->load($cacheId);
       }
       return $listings;
    }
    
    public function getListingByIdf($idf, $city, $country)
    {
        $args = func_get_args();
        $cacheId = "LS_getListingByIdf_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {

            $idf = trim(strtolower($idf));
            $select = $this->listings->select();
            $select->where('identifier = ?', $idf);
            $select->where('city_id = ?', $city);
            $select->where('country_id = ?', $country);
            $listing = $this->listings->fetchRow($select);
            
            if($this->use_cache)
                $this->cache->save($listing, $cacheId);
            
        } else {
            $listing = $this->cache->load($cacheId);
        }
        if(is_null($listing))
            throw new Exception();
        
        return $listing;
    }
    
    public function getTabsOf($ids)
    {
        $args = func_get_args();
        $cacheId = "LS_getTabsOf_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {

            $select = $this->tabs_db->select();
            $select->where('listing_id = ?', $ids);
            $tabs = $this->tabs_db->fetchAll($select);
            
            if($this->use_cache)
                $this->cache->save($tabs, $cacheId);
            
        } else {
            $tabs = $this->cache->load($cacheId);
        }
        
        if(is_null($tabs))
            throw new Exception();
        
        return $tabs;
    }
    
    public function getFAQsOf($ids)
    {
        $args = func_get_args();
        $cacheId = "LS_getFAQsOf_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {

            $select = $this->faqs_db->select();
            $select->where('listing_id = ?',$ids);
            $faqs = $this->faqs_db->fetchAll($select);
            if(is_null($faqs))
                $faqs = array();
            
            if($this->use_cache)
                $this->cache->save($faqs, $cacheId);
            
        } else {
            $faqs = $this->cache->load($cacheId);
        }
        
        return $faqs;
    }
    
    public function getAttributesOf($ids)
    {
        $args = func_get_args();
        $cacheId = "LS_getAttributesOf_".md5(print_r($args,true));
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {

            $select = $this->listing_attributes->select();
            $select->where('listing_id = ?',$ids);
            $attrs = $this->listing_attributes->fetchAll($select);
            
            if($this->use_cache)
                $this->cache->save($attrs, $cacheId);
            
        } else {
            $attrs = $this->cache->load($cacheId);
        }
            
        return $attrs;
    }
    
    public function getMainTypes()
    {
        $args = func_get_args();
        $cacheId = "LS_getMainTypes";
        
        if(!$this->use_cache || ($this->cache->test($cacheId) === false)) {

            $types = $this->listings_types_db->fetchAll('parent_id is null');
            
            if($this->use_cache)
                $this->cache->save($types, $cacheId);
            
        } else {
            $types = $this->cache->load($cacheId);
        }
        
        return $types;
    }
    
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    ############################################################################
    
    public function getOverviewOf($listing)
    {
        $overviews = new Zend_Db_Table('listing_overviews');
        $select = $overviews->select();
        $select->where('listing_id = ?', $listing);
        $overview = $overviews->fetchRow($select);
        if(is_null($overview)){
            $overview = $overviews->fetchNew();
            $overview->listing_id = $listing;
            $overview->created = date('Y-m-d H:i:s');
            $overview->updated = date('Y-m-d H:i:s');
            $overview->save();
        }
        
        return $overview;
    }
    
    public function getOverviewOf2($listing)
    {
        $overviews = new Zend_Db_Table('listing_overviews');
        $select = $overviews->select();
        $select->where('listing_id = ?', $listing);
        $overview = $overviews->fetchRow($select);
        if(is_null($overview)){
            $overview = $overviews->fetchNew();
            $overview->listing_id = $listing;
            $overview->created = date('Y-m-d H:i:s');
            $overview->updated = date('Y-m-d H:i:s');
            $overview->save();
        }
        
        return $overview;
    }
    
    public function getLocationOf($listing)
    {
        $locations = new Zend_Db_Table('listing_getthere');
        $select = $locations->select();
        $select->where('listing_id = ?', $listing);
        $location = $locations->fetchRow($select);
        if(is_null($location)){
            $location = $locations->fetchNew();
            $location->listing_id = $listing;
            $location->created = date('Y-m-d H:i:s');
            $location->updated = date('Y-m-d H:i:s');
            $location->save();
        }
        
        return $location;
    }
    
    public function getListingsOf($vendor, $status = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        
        $select->from('listings',array(
            'identifier','status','title','created','id','image','description',
            'country_id','city_id','main_type'));
        $select->join('listing_types', 'listings.main_type = listing_types.id', array('listing_type'  => 'name'));
        $select->where('vendor_id = ?', $vendor);
        if(!is_null($status))
            $select->where('status = ?',$status);
        else
            $select->where('status <> ?', 3);
        
        $select->order('created DESC');
        
        $listings = $db->fetchAll($select, array(), 5);
        //var_dump($listings); die;
        return $listings;
    }
    
    public function getListingsOf2($vendor, $status = 1)
    {
        $listings_db = new Zend_Db_Table('listings');
        $select = $listings_db->select();
        $select->where('(vendor_id = ?) and (status = 1) and ((main_type = 5) or (main_type = 6))', $vendor);
        
        $listings = $listings_db->fetchAll($select);
        //var_dump($listings); die;
        return $listings;
    }
    
    public function getListing($id, $vendor = null)
    {
        $select = $this->listings->select();
        $select->where('id = ?',$id);
        if(!is_null($vendor))
            $select->where('vendor_id = ?', $vendor);
        $listing = $this->listings->fetchRow($select);
        if(is_null($listing))
            throw new Exception();
        
        return $listing;
    }
    
    public function getListingTypesOf($id)
    {
        $select = $this->listing_listingtypes_db->select();
        $select->where('listing_id = ?', $id);
        $types = $this->listing_listingtypes_db->fetchAll($select);
        
        //var_dump($types); die;
        return $types;
    }
    
    public function getTab($id, $label = null, $listing = null)
    {        
        if(!is_null($id)){
            $select = $this->tabs_db->select();
            $select->where('id = ?', $id);
            $tab = $this->tabs_db->fetchRow($select);
            
            return $tab;
        }
        if(!is_null($label)){
            if(!is_null($listing)){
                $select = $this->tabs_db->select();
                $select->where('listing_id = ?', $listing);
                $select->where('label = ?', $label);
                $tab = $this->tabs_db->fetchRow($select);
                
                return $tab;
            }
            throw new Exception();
        }
        throw new Exception();
    }
    
    public function getAttr($id)
    {
        $select = $this->listing_attributes->select();
        $select->where('id = ?',$id);
        $attr = $this->listing_attributes->fetchRow($select);
        
        return $attr;
    }
    
    public function addTabTo($listing, $data)
    {
        $tab = $this->tabs_db->fetchNew();
        $tab->listing_id = $listing;
        $tab->label = $data['title'];
        $tab->title = $data['title'];
        $tab->text  = '';
        $tab->created = date('Y-m-d H-i-s');
        $tab->updated = date('Y-m-d H-i-s');
        $tab->token   = md5($tab->title . $tab->created);
        
        $tab->save();
    }
    public function addAttrTo($listing, $data)
    {
        $attr = $this->listing_attributes->fetchNew();
        $attr->listing_id = $listing;
        $attr->name = $data['name'];
        $attr->value = $data['value'];
        
        $attr->save();
    }
    
    public function addScheduleTo($listing, $data)
    {
        if($listing->main_type == 5) {
            $sch = $this->schedules_db->fetchNew();
            $sch->listing_id = $listing->id;
            $sch->name       = $data['name'];
            
            $sch->save();
            
            $rooms = new Zend_Db_Table('rooms');
            $room  = $rooms->fetchNew();
            $room->setFromArray($data['room']);
            $room->schedule_id = $sch->id;
            $room->created = date('Y-m-d H:i:s');
            $room->updated = date('Y-m-d H:i:s');
            
            $room->save();
            
            $beds = new Zend_Db_Table('beds');
            foreach($data['beds'] as $_bed){
                $bed = $beds->fetchNew();
                $bed->setFromArray($_bed);
                $bed->room_id = $room->id;
                $bed->save();
            }
            
            $amenities = new Zend_Db_Table('room_amenities');
            foreach($data['amenities'] as $_amenity){
                $amenity = $amenities->fetchNew();
                $amenity->room_id = $room->id;
                $amenity->amenity_id = $_amenity;
                $amenity->save();
            }
        } else {
            $sch = $this->schedules_db->fetchNew();
            $sch->listing_id = $listing->id;
            $sch->name       = $data['name'];

            $starting = $data['start_hour'].':00 '.$data['start_time'];
            $ending   = $data['end_hour'].':00 '.$data['end_time'];

            $starting = date('H:i:s',strtotime($starting));
            $ending   = date('H:i:s',strtotime($ending));        

            $sch->starting   = $starting;
            $sch->ending     = $ending;
            if(isset($data['duration']))
                $sch->duration   = $data['duration'];
            else
                $sch->duration   = 1;
            $sch->duration_label = 'days';
            
            $sch->save();
        }
        
    }
    
    public function updateRoom($listing, $data)
    {
        $rooms = new Zend_Db_Table('rooms');
        $select = $rooms->select();
        $select->where('id = ?', $data['roomid']);
        $room  = $rooms->fetchRow($select);
        $room->setFromArray($data['room']);
        $room->updated = date('Y-m-d H:i:s');
        $room->save();
        
        $sch = $this->getSchedule($room->schedule_id);
        $sch->name = $data['name']; 
        $sch->save();

        $beds = new Zend_Db_Table('beds');
        $select = $beds->select();
        $select->where('room_id = ?', $room->id);
        $_beds = $beds->fetchAll($select);
        foreach($_beds as $bed){
            if(isset($data['bed'][$bed->id])){
                $bed->setFromArray($data['bed'][$bed->id]);
                $bed->save();
            } else {
                $bed->delete();
            }
        }
        foreach($data['beds'] as $_bed){
            $bed = $beds->fetchNew();
            $bed->setFromArray($_bed);
            $bed->room_id = $room->id;
            $bed->save();
        }

        $amenities = new Zend_Db_Table('room_amenities');
        $amenities->delete("room_id = {$room->id}");
        foreach($data['amenities'] as $_amenity){
            $amenity = $amenities->fetchNew();
            $amenity->room_id = $room->id;
            $amenity->amenity_id = $_amenity;
            $amenity->save();
        }
    }
    
    public function updateMaxOnPricesOf($sch, $people)
    {
        //echo $sch; die;
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->update('prices', array('max'=>$people),"schedule_id = {$sch}");
    }
    
    public function addScheduleTo2($listing, $data)
    {
        $sch = $this->schedules_db->fetchNew();
        $sch->listing_id = $listing->id;
        $sch->name       = $data['name'];
        if($listing->main_type == 6){
            $sch->duration = $data['duration'];
            $sch->duration_label = $data['duration_lb'];
        }
        
        if($listing->main_type != 5){

            $starting = $data['start']['hour'].':00 '.$data['start']['time'];
            $ending   = $data['end']['hour'].':00 '.$data['end']['time'];

            $starting = date('H:i:s',strtotime($starting));
            $ending   = date('H:i:s',strtotime($ending));        

            $sch->starting   = $starting;
            $sch->ending     = $ending;
        }
        
        $sch->save();
    }
    
    public function getSchedulesOf($listing)
    {
        $select = $this->schedules_db->select();
        $select->where('listing_id = ?', $listing);
        $schs = $this->schedules_db->fetchAll($select);
        
        return $schs;
    }
    
    public function getSeasonsOf($listing)
    {
        $select = $this->seasons_db->select();
        $select->where('listing_id = ?', $listing);
        $schs = $this->seasons_db->fetchAll($select);
        
        return $schs;
    }
    
    public function getSchedule($id)
    {
        $select = $this->schedules_db->select();
        $select->where('id = ?',$id);
        $sch = $this->schedules_db->fetchRow($select);
        
        return $sch;
    }
    
    public function saveTypes($listing, $types)
    {
        $this->listing_listingtypes_db->delete('listing_id = '.$listing);
        foreach($types as $type){
            $row = $this->listing_listingtypes_db->fetchNew();
            $row->listingtype_id = $type;
            $row->listing_id     = $listing;
            $row->save();
        }
    }
    
    public function saveTags($listing, $tags)
    {
        $this->listing_tags_db->delete('listing_id = '.$listing);
        foreach($tags as $tag){
            $row = $this->listing_tags_db->fetchNew();
            $row->tag_id     = $tag;
            $row->listing_id = $listing;
            $row->save();
        }
    }
    
    public function saveLandscapes($listing, $lands)
    {
        $table = new Zend_Db_Table('listing_landscapes');
        $table->delete('listing_id = '.$listing);
        foreach($lands as $land){
            $row = $table->fetchNew();
            $row->landscape_id = $land;
            $row->listing_id   = $listing;
            $row->save();
        }
    }
    
    public function getPictures($listing)
    {
        $select = $this->pictures_db->select();
        $select->where('listing_id = ?', $listing);
        $select->limit(10,0);
        
        $pictures = $this->pictures_db->fetchAll($select);
        
        return $pictures;
    }
    
    public function getPhoto($listing, $photo)
    {
        $select = $this->pictures_db->select();
        $select->where('listing_id = ?', $listing);
        $select->where('id = ?', $photo);
        
        $img = $this->pictures_db->fetchRow($select);
        
        return $img;
    }
    
    public function removeMainPhoto($listing){
        $select = $this->pictures_db->select();
        $select->where('listing_id = ?', $listing);
        $select->where('main = ?', 1);
        
        $img = $this->pictures_db->fetchRow($select);
        if(!is_null($img)){
            $img->main = 0;
            $img->save();
        }
    }
    
    public function updatePricesOf($listing, $data){   
        //var_dump($data); die;
        if($listing->main_type == 5) {
            $__price = 100000;
            $__key;
            foreach($data['sch'][0] as $key => $value){
                if($value['price'] < $__price){
                    $__price = $value['price'];
                    $__key   = $key;
                }
            }
            if($__price != 100000){
                $ddata = $data['sch'][0][$__key];
            } else {
                $ddata = reset($data['sch'][0]);
            }
            //var_dump($ddata); die;
            $this->_updatePrice($listing->id, 1, $ddata, $ddata);
            $listing->price = $ddata['price'];
            foreach($data['sch'] as $season => $_info){
                foreach($_info as $sch => $info){
                    $ddata = $data['sch'][0][$sch];
                    //var_dump($info); 
                    $this->_updatePrice ($listing->id, 4, $ddata, $info, null, $season, $sch);
                }
            }
            //die;
        } 
        else {
            if(!is_null($data['sch'][0])){
                $__price = 100000;
                $__key;
                foreach($data['sch'][0] as $key => $value){
                    if($value['price'] < $__price){
                        $__price = $value['price'];
                        $__key   = $key;
                    }
                }
                if($__price != 100000){
                    $ddata = $data['sch'][0][$__key];
                } else {
                    $ddata = reset($data['sch'][0]);
                }
                $this->_updatePrice($listing->id, 1, $data, $ddata);
                $listing->price = $ddata['price'];
            } else {
                $this->_updatePrice($listing->id, 1, $data, $data);
                $listing->price = $data['price'];
            }
            //var_dump($ddata); die;
            //var_dump($ddata); die;
            foreach($data['season'] as $season => $info)
                $this->_updatePrice($listing->id, 3, $data, $info, null, $season);
            foreach($data['sch'] as $season => $_info){
                foreach($_info as $sch => $info){
                    $this->_updatePrice ($listing->id, 4, $data, $info, null, $season, $sch);
                }
            }
        }
        $listing->save();
        //die;
    }
    
    private function _updatePrice(
            $listing, $type, $data, $info, $day = null, $season = null, $schedule = null){
        
        $select = $this->prices_db->select();
        $select->where('listing_id = ?', $listing);
        if($type == 4 and $season != 0){
            $type = 3;
            //var_dump($data); die;
        }
        $select->where('type_id = ?', $type);
        
        switch ($type){
            case 2:
                $select->where('day = ?', $day);
                break;
            case 3:
                $select->where('season_id = ?', $season);
                $select->where('schedule_id = ?', $schedule);
                break;
            case 4:
                $select->where('schedule_id = ?', $schedule);
                $select->where('season_id = ?', $season);
                break;
            default: break;
        }
        
        $row = $this->prices_db->fetchRow($select);
        if(is_null($row)){
            $row = $this->prices_db->fetchNew();
            $row->listing_id        = $listing;
            $row->created           = date('Y-m-d H:i:s');
            $row->type_id           = $type;
        } 
        
        $row->currency          = 'USD';
        $row->price             = $info['price'];
        $row->kids              = (isset($info['kids'])) ? $info['kids'] : $info['price'];
        $row->additional        = $info['additional'];
        if(!is_null($schedule) and !is_null($season)){
            $row->additional_after  = $info['additional_after'];
            //var_dump($info);
        }
        else {
            if(isset($data['additional_after']))
                $row->additional_after  = $data['additional_after'];
            else
                $row->additional_after  = $info['additional_after'];
        }
        $row->day               = $day;
        $row->season_id         = $season;
        $row->schedule_id       = $schedule;
        
        $row->updated           = date('Y-m-d H:i:s');
        
        $row->save();
        
        //var_dump($row->toArray());
    }
    
    public function getBasePrice($listing)
    {
        $select = $this->prices_db->select();
        $select->where('listing_id = ?', $listing->id);
        $select->where('type_id = ?', 1);
        $price = $this->prices_db->fetchRow($select);
        
        return $price;
    }
    
    public function getWeekendPrices($listing)
    {
        $select = $this->prices_db->select();
        $select->where('listing_id = ?', $listing);
        $select->where('type_id = ?', 2);
        $prices = $this->prices_db->fetchAll($select);
        
        $result = array();
        foreach($prices as $p){
            $result[$p->day]['price']            = $p->price;
            $result[$p->day]['additional']       = $p->additional;
            $result[$p->day]['additional_after'] = $p->additional_after;
        }
        
        return $result;
    }
    public function getSeasonPrices($listing)
    {
        $select = $this->prices_db->select();
        $select->where('listing_id = ?', $listing->id);
        $select->where('type_id = ?', 3);
        $prices = $this->prices_db->fetchAll($select);
        
        //echo '<pre>'; var_dump($prices); echo '</pre>'; die;
        
        $result = array();
        if($listing->main_type != 5){
            foreach($prices as $p){
                $result[$p->season_id][$p->schedule_id]['price']            = $p->price;
                $result[$p->season_id][$p->schedule_id]['kids']             = $p->kids;
                $result[$p->season_id][$p->schedule_id]['additional']       = $p->additional;
                $result[$p->season_id][$p->schedule_id]['additional_after'] = $p->additional_after;
            }
        } else {
            foreach($prices as $p){
                $result[$p->season_id][$p->schedule_id]['price']            = $p->price;
                $result[$p->season_id][$p->schedule_id]['kids']             = $p->kids;
                $result[$p->season_id][$p->schedule_id]['additional']       = $p->additional;
                $result[$p->season_id][$p->schedule_id]['additional_after'] = $p->additional_after;
            }
        }
        
        //var_dump($result); die;
        return $result;
    }
    public function getSchPrices($listing)
    {
        $select = $this->prices_db->select();
        $select->where('listing_id = ?', $listing->id);
        $select->where('type_id = ?', 4);
        $prices = $this->prices_db->fetchAll($select);
        
        $result = array();
        if($listing->main_type != 5){
            foreach($prices as $p){
                $result[$p->season_id][$p->schedule_id]['price']            = $p->price;
                $result[$p->season_id][$p->schedule_id]['kids']             = $p->kids;
                $result[$p->season_id][$p->schedule_id]['additional']       = $p->additional;
                $result[$p->season_id][$p->schedule_id]['additional_after'] = $p->additional_after;
            }
        } else {
            foreach($prices as $p){
                $result[$p->season_id][$p->schedule_id]['price']            = $p->price;
                $result[$p->season_id][$p->schedule_id]['kids']             = $p->kids;
                $result[$p->season_id][$p->schedule_id]['additional']       = $p->additional;
                $result[$p->season_id][$p->schedule_id]['additional_after'] = $p->additional_after;
            }
        }
        
        //var_dump($result); die;
        return $result;
    }
    
    public function updateFAQsOf($listing, $data)
    {
        foreach($data['faq'] as $id => $faq){
            $select = $this->faqs_db->select();
            $select->where('listing_id = ?', $listing);
            $select->where('id = ?', $id);
            $row = $this->faqs_db->fetchRow($select);
            if($row->answer != $faq['answer'] || $row->question != $faq['question']){
                if(empty($faq['answer']) || empty($faq['question']))
                    $row->delete();
                else{
                    $row->question  = $faq['question'];
                    $row->answer    = $faq['answer'];
                    $row->save();
                }
            }
        }
    }
    
    public function addFAQsTo($listing, $data)
    {
        $row = $this->faqs_db->fetchNew();
        $row->listing_id = $listing;
        $row->question   = $data['question'];
        $row->answer     = $data['answer'];
        
        $row->save();
    }
    
    public function saveDayInCalendar($listing, $data)
    {
        
        if($listing->main_type == 6){
            
            foreach($data['dates'] as $day) {        
                foreach($data['sch'] as $sch => $data2){
                    $row = $this->getDayFromCalendar($listing->id, $day, $sch);
                    if(is_null($row)){
                        if(!empty($data2['state'])){
                            $row = $this->calendar->fetchNew();
                            $row->listing_id = $listing->id;
                            $row->day = $day;
                            $row->schedule_id = $sch;
                        }
                    }
                    if(!is_null($row)){
                        if(empty($data2['state']))
                            $row->delete();
                        else {
                            $row->state = $data2['state'];
                            $row->save();
                        }
                    }
                }
                $row = $this->getDayFromCalendar($listing->id, $day);
                if(is_null($row)){
                    $row = $this->calendar->fetchNew();
                    $row->listing_id = $listing->id;
                    $row->day = $day;
                }
                if(!isset($data['state']))
                    $row->delete();
                else {
                    $row->state = $data['state'];
                    $row->save();
                }
            }
        } else {
            foreach($data['dates'] as $day) {
                $row = $this->getDayFromCalendar($listing->id, $day);
                if(is_null($row)){
                    $row = $this->calendar->fetchNew();
                    $row->listing_id = $listing->id;
                    $row->day = $day;
                }
                if(empty($data['state']))
                    $row->delete();
                else {
                    $row->state = $data['state'];
                    $row->save();
                }
            }
        }
    }
    
    public function getDayFromCalendar($listing, $day, $sch = 'NULL')
    {
        $select = $this->calendar->select();
        $select->where('listing_id = ?', $listing);
        $select->where('day = ?', $day);
        if($sch == 'NULL')
            $select->where('schedule_id is null ');
        else
            $select->where('schedule_id = ?', $sch);
        $row = $this->calendar->fetchRow($select);            
        
        return $row;
    }
    
    public function getDisabledDays($listing)
    {
        $select = $this->calendar->select();
        $select->where('listing_id = ?', $listing);
        $days = $this->calendar->fetchAll($select);
        if(count($days) > 0)
            return $days;
        return array();
    }
    
    public function getListingTitle($listing)
    {
        $select = $this->listings->select();
        $select->where('id = ?', $listing);
        $row = $this->listings->fetchRow($select);
        
        return $row->title;
    }
    
    public function countListings($city, $vendor = null)
    {
        if(!is_null($city)){
            $db = Zend_Db_Table::getDefaultAdapter();
            $cats = $this->getMainCategories();
            $counter = array();
            foreach($cats as $cat){
                if($cat->name == 'All'){
                    $sql = "Select count(*) as c from listings where city_id = {$city}";
                    $count = $db->fetchRow($sql);
                    $counter[$cat->id] = $count['c'];
                } else {
                    $sql = "Select count(*) as c from listings where city_id = {$city} and main_type = {$cat->id}";
                    $count = $db->fetchRow($sql);
                    $counter[$cat->id] = $count['c'];
                }
            }
            return $counter;
        } elseif(!is_null($vendor)){
            $db = Zend_Db_Table::getDefaultAdapter();
            
            $sql     = "Select count(*) as c from listings where vendor_id = {$vendor}";
            $count   = $db->fetchRow($sql);
            $counter = $count['c'];
            
            return $counter;
        } else{
            throw new Exception();
        }
    }
    
    public function getListingForCart($data)
    {
        $select = $this->listings->select();
        $select->where('id = ?', $data['listingids']);
        $select->where('city_id = ?', $data['cityid']);
        $select->where('country_id = ?', $data['countryid']);
        
        $listing = $this->listings->fetchRow($select);
        
        return $listing;
    }
    
    public function getPricesOf($listing)
    {
        $select = $this->prices_db->select();
        $select->where('listing_id = ?', $listing);
        $prices = $this->prices_db->fetchAll($select);
        $prices = $prices->toArray();
        $result = array();
        $used   = array();
        foreach($prices as $price){
            if(!in_array($price['type_id'], $used)){
                $used[] = $price['type_id'];
                foreach($prices as $price2){
                    if($price['type_id'] == $price2['type_id']){
                        $result[$price['type_id']][] = $price2;
        } } } }
        
        return $result;
    }
    
    public function getSeassonFor($date, $listing){
        $date = date('Y-m-d', strtotime($date));
        $select = $this->seasons_db->select();
        $select->where('`starting` <= ?', $date);
        $select->where('`ending` >= ?', $date);
        $select->where('listing_id = ?', $listing);
        
        $season = $this->seasons_db->fetchRow($select);
        return $season;
    }
    
    public function getSeassonPrice($season, $listing, $room = null)
    {
        $select = $this->prices_db->select();
        $select->where('season_id = ?', $season);
        $select->where('listing_id = ?', $listing);
        if(!is_null($room))
            $select->where('schedule_id = ?', $room);
        $price = $this->prices_db->fetchRow($select);
        
        return $price;
    }
    
    public function getBasicPrice($listing, $room = null)
    {
        $select = $this->prices_db->select();
        $select->where('listing_id = ?', $listing);
        if(!is_null($room)){
            $select->where('schedule_id = ?', $room);
            $select->where('type_id = ?', 4);
        } else {
            $select->where('type_id = ?', 1);
        }
        $price = $this->prices_db->fetchRow($select);
        
        return $price;
    }
    
    public function getOptionPrice($listing, $option)
    {
        $select = $this->prices_db->select();
        $select->where('listing_id = ?', $listing);
        $select->where('schedule_id = ?', $option);
        $select->where('type_id = 4');
        //echo $select->assemble(); die;
        
        $price = $this->prices_db->fetchRow($select);
        if(is_null($price)){
            $select = $this->prices_db->select();
            $select->where('listing_id = ?', $listing);
            $select->where('type_id = 1');
            
            $price = $this->prices_db->fetchRow($select);
            if(is_null($price))
                throw new Exception('Prices not found');
        }
        
        return $price;
    }
    
    public function getVendor($vendor)
    {
        $select = $this->vendors->select();
        $select->where('id = ?', $vendor);
        $user = $this->vendors->fetchRow($select);
        
        return $user;
    }
    
    public function getActivityQuote(
            $listing, $adults, $kids, $checkin, $option = null, $capacity = null)
    {
        
        $option = ($option != 'flex') ? $this->getSchedule($option) : null;
        $capacity = ($capacity != 'single') ? $this->getActivityType($capacity) : null;
        $seasson = $this->getSeassonFor($checkin, $listing->id);
        
        $price = null;
        if(!is_null($seasson)) 
            $price = $this->getSeassonPrice($seasson->id, $listing->id, $capacity->id);
        if(is_null($price) || $price->price == '0.00') {
            $price = $this->getBasicPrice($listing->id, $capacity->id);
            if(is_null($price) || $price == '0.00') 
                $price = $this->getBasicPrice($listing->id);}
        if(is_null($price) || $price->price == '0.00') 
            throw new Exception('We couldn\'t find the price', 1);
        
        //var_dump($price); die;
        
        
        $min = (!is_null($capacity)) ? $capacity->min : $listing->min; 
        $max = (!is_null($capacity)) ? $capacity->max : $listing->max;
        $kids_alowed = (!is_null($capacity)) ? $capacity->kids : $listing->kids;
        
        if($kids > 0 && !is_null($kids_alowed))
            throw new Exception('Kids are not allowed', 1);
        
        $total_people = $kids + $adults;
        if($min > $total_people)
            throw new Exception("You need min {$min} person(people)", 1);
        if($max < $total_people)
            throw new Exception("Too many people max {$max} person(people)", 1);
        
        $cart = new stdClass();
        $cart->rate = $price->price;
        
        if($price->type_id == 3){
            $cart->rate_description = (is_null($capacity))
                    ? "Price for {$seasson->name} Season"
                    : "Price for {$capacity->name} Type on {$seasson->name} Season";
        } elseif($price->type_id == 4){
            $cart->rate_description = (is_null($seasson)) 
                    ? "Price for {$capacity->name} Type" 
                    : "Price for {$capacity->name} Type on {$seasson->name} Season";
        } else {
            $cart->rate_description = 'Basic Price';
        }
        
        if($price->additional_after < $total_people){
            $extra = $total_people - $price->additional_after;
            $cart->additional = $price->additional;
            $cart->additional_description = 'Additional after '.$price->additional_after.' person(s)';
        }

        if($extra){
            $cart->subtotal = $price->price;
            $cart->subtotal = $cart->subtotal + ($cart->additional * $extra);
        } else {
            $cart->subtotal = $price->price;
        }
        
        $cart->taxes = ($cart->subtotal * 0.1);
        $cart->total = $cart->subtotal + $cart->taxes;
        
        $cart->checkin = date('D M d Y',strtotime($checkin));
        $cart->kids    = $kids;
        $cart->adults  = $adults;
        
        $cart->option_id = (!is_null($capacity)) ? $capacity->id : 0;

        return $cart;
    }
    
    public function getHotelQuote($listing, $adults, $kids, $checkin, $checkout = null, $days = null, $option = null)
    {
        $row = new stdClass();
        
        if(is_null($checkout) && is_null($days))
                throw new Exception('Not checkout date provided', 1);
        
        if(is_null($checkout)){
            $checkout = strtotime($checkin);
            $checkout = $checkout + (86400 * ($days - 1));
        } else {
            $checkout = strtotime($checkout);
        }
        $checkout = date('Y-m-d G:i:s', $checkout);
        
        if(is_null($days)){
            $fday = strtotime($checkin);
            $lday = strtotime($checkout);

            $nights = $lday - $fday;
            $nights = $nights / 86400;
        } else {
            $nights = $days - 1;
        }
        
        if(is_null($option)){
            $options = $this->getHotelRooms($listing->id);
            $option  = $options[0];
        } else {
            $option = $this->getRoom($option);
        }
        
        $row->max = $option->people;
        
        $sch = $this->getSchedule($option->schedule_id);
        
        $seasson = $this->getSeassonFor($checkin, $listing->id);
        if(!is_null($seasson))
            $price = $this->getSeassonPrice($seasson->id, $listing->id, $option->schedule_id);
        else
            $price = $this->getOptionPrice($listing->id, $option->schedule_id);
        
        //echo '<pre>'; var_dump($option); echo '</pre>'; die;
        
        $total_people = $kids + $adults;

        $row->rate = $price->price;
        if($price->type_id == 3)
            $row->rate_description = 'Price for '. $sch->name .' on '.$seasson->name.' Seasson per night';
        elseif($price->type_id == 4) 
            $row->rate_description = 'Price for '. $sch->name .' per night';
        else 
            $row->rate_description = 'Basic Price per night';
        
        //echo $row->rate_description; die;

        if($total_people > $option->people){
            $aux   = $total_people % $option->people;
            $rooms = ($total_people - $aux) / $option->people;
            $rooms = ($aux > 0) ? $rooms + 1 : $rooms;
        } else {
            $rooms = 1;
        }

        $_prices = array();
        $aux = $total_people;
        for($i = 0; $i < $rooms; $i++)
        {
            $personas   = ($aux > $option->people) ? $option->people : $aux;
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

        $subtotal = 0;
        foreach($_prices as $p){
            $subtotal = $subtotal + (($p['basic'] + $p['additional']) * $nights);
        }
        $row->subtotal = $subtotal;
        $row->taxes = ($row->subtotal * 0.1);
        $row->total = $row->subtotal + $row->taxes;
        
        $row->checkin = date('D M d Y',strtotime($checkin));
        $row->checkout = date('D M d Y',strtotime($checkout));
        $row->kids  = $kids;
        $row->adults = $adults;
        $row->nights = $nights;
        $row->rooms = $rooms;
        $row->option_id = $option->id;

        //var_dump($row->toArray()); die;

        return $row;
    }
    
    public function getQuote(
                $listing, $adults, $kids, $checkin, $checkout = null, $days = null, $option = null, $capacity = null)
    {        
        try {
            $cart = ($listing->main_type == 6)
                        ? $this->getActivityQuote($listing, $adults, $kids, $checkin, $option, $capacity)
                        : $this->getHotelQuote($listing, $adults, $kids, $checkin, $checkout, $days, $option);
            $cart->available = true;
        } catch(Exception $e) {
            if($e->getCode() == 1){
                $cart->available = false;
                $cart->error     = $e->getMessage();

                $cart->checkin = $checkin;
                $cart->kids  = $kids;
                $cart->adults = $adults;
            } else {
                throw $e;
            }
        }
        
        $cart->listing_title = $listing->title;
        $cart->listing_image = $listing->image;
        
        return $cart;
    }
    
    public function createNew()
    {
        $listing = $this->listings->fetchNew();
        return $listing;
    }
    
    public function getDefaultAmenities($assoc = false)
    {
        $db = new Zend_Db_Table('amenities');
        $result = $db->fetchAll();
        if(!$assoc)
            return $result;
        
        $result2 = array();
        foreach($result as $amm)
            $result2[$amm->id] = $amm;
        
        return $result2;
    }
    
    public function getDefaultGenAmenities($assoc = false)
    {
        $db = new Zend_Db_Table('gen_amenities');
        $result = $db->fetchAll();
        if(!$assoc)
            return $result;
        
        $result2 = array();
        foreach($result as $amm)
            $result2[$amm->id] = $amm;
        
        return $result2;
    }
    
    public function getAmenities($room)
    {
        $db = new Zend_Db_Table('room_amenities');
        $select = $db->select();
        $select->where('room_id = ?', $room);
        $result = $db->fetchAll($select);
        $ammm = array();
        foreach($result as $r)
            $ammm[] = $r->amenity_id;
        
        return $ammm;
    }
    
    public function getGenAmenities($listing)
    {
        $db = new Zend_Db_Table('listing_amenities');
        $select = $db->select();
        $select->where('listing_id = ?', $listing);
        $result = $db->fetchAll($select);
        $amm = array();
        foreach($result as $r)
            $amm[] = $r->amenitie_id;
        
        return $amm;
    }
    
    public function getRoom($sch)
    {
        $db = new Zend_Db_Table('rooms');
        $select = $db->select();
        $select->where('schedule_id = ?', $sch);
        $room = $db->fetchRow($select);
        if(is_null($room)){
            $room = $db->fetchNew();
            $room->schedule_id = $sch;
            $room->save();
        }
        
        return $room;
    }
    
    public function getHotelRooms($listing)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from('listing_schedules', array('name'));
        $select->join('rooms', 'listing_schedules.id = rooms.schedule_id');
        $select->where('listing_schedules.listing_id = ?', $listing);
        
        $rooms = $db->fetchAll($select, array(), 5);
        return $rooms;
    }
    
    public function getBeds($room)
    {
        $db = new Zend_Db_Table('beds');
        $select = $db->select();
        $select->where('room_id = ?', $room);
        $beds = $db->fetchAll($select);
        
        return $beds;
    }
    
    public function getSpecials($listing)
    {
        $specialities = new Zend_Db_Table('specialities');
        $select = $specialities->select();
        $select->where('listing_id = ?', $listing);
        $specials = $specialities->fetchAll($select);
        
        return $specials;
    }
    
    public function addSpecialTo($listing, $data)
    {
        $specialities = new Zend_Db_Table('specialities');
        $special = $specialities->fetchNew();
        $special->listing_id = $listing;
        $special->title = $data['title'];
        $special->description = $data['description'];
        $special->save();
        
        //var_dump($special->toArray()); die;
    }
    
    public function updateSpecialsOf($listing, $data)
    {
        $specials = $this->getSpecials($listing);
        foreach($specials as $special){
            if(isset($data['special'][$special->id])){
                $special->title = $data['special'][$special->id]['title'];
                $special->description = $data['special'][$special->id]['description'];
                $special->save();
            } else {
                $special->delete();
            }
        }
    }
    
    public function getActivityTypes($listing)
    {
        $actTypes = new Zend_Db_Table('activity_types');
        $select = $actTypes->select();
        $select->where('listing_id = ?', $listing);
        $types = $actTypes->fetchAll($select);
        
        return $types;
    }
    
    public function deleteActivityType($type){
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->delete('activity_types', "id = {$type}");
        $db->delete('types_tips', "type_id = {$type}");
    }
    
    public function getActivityType($type)
    {
        $actTypes = new Zend_Db_Table('activity_types');
        $select = $actTypes->select();
        $select->where('id = ?', $type);
        $types = $actTypes->fetchRow($select);
        
        return $types;
    }
    
    public function getActivityTypeTips($type)
    {
        $typeTips = new Zend_Db_Table('types_tips');
        $select = $typeTips->select();
        $select->where('type_id = ?', $type);
        $tips = $typeTips->fetchAll($select);
        
        return $tips;
    }
    
    public function createSchedules($listing){
        if($listing->main_type == 2){
            $days = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
            foreach($days as $day){
                $sch = array(
                    'start' => array(
                        'time'=>'AM',
                        'hour'=>'1:00'),
                    'end'   => array(
                        'time'=>'AM',
                        'hour'=>'1:00'),
                    'name'  => $day
                );
                $this->addScheduleTo2($listing, $sch);
            }
            
            return $this->getSchedulesOf($listing->id);
        }
    }
}