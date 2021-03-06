<?php

class CrontasksController extends Zend_Controller_Action
{

    public function init()
    {

    }
    
    public function createimagesAction()
    {
        $cats = array('all','activities','entertaiment','tourist-sights','restaurants','hotels');
        $images = array(
            'all' => 'All-Big-0',
            'activities' => 'Activities-Big-0',
            'entertaiment' => 'Entertainment-Big-0',
            'tourist-sights' => 'Sights-Small-0',
            'restaurants' => 'Sights-Big-0',
            'hotels' => 'Hotels-Big-0',
        );
        $list_service = new WS_ListingService();

        $subcat   = 'all';
        $sort     = 'newest';
        $stars    = 'all';
        $pricemax = 3000;
        $pricemin = 0;
        
        $pictures = new Model_ListingPictures();
        $used = array();
        
        foreach($cats as $cat)
        {
            echo $cat .'<br><br>';
            $listings = $list_service->getListings2(25, $cat, $subcat, $sort, $stars, $pricemin, $pricemax);
            foreach($listings as $i => $listing){
                if(!in_array($listing->id, $used)){
                    $used[] = $listing->id;
                    
                    $picture = $pictures->fetchNew();
                    $picture->listing_id = $listing->id;
                    $picture->url = '/images/lph/'.$images[$cat].($i + 1).'.jpg';
                    $picture->location = 'Nice picture of '.$listing->title;
                    $picture->created = date('Y-m-d H:i:s');
                    $picture->updated = date('Y-m-d H:i:s');
                    
                    $picture->save();
                    
                    echo $picture->url .'<br>';
                }
            }
            echo $cat.'<br><br>';
        }
        die;
    }
    

    public function indexAction()
    {
       $db = Zend_Db_Table::getDefaultAdapter();
       $index = new Model_Index();

       $limit  = 30;
       $offset = 0;
       $i      = 0;

       $index->delete($where);
       $db->query('ALTER TABLE `index` AUTO_INCREMENT = 1');

       $places = $db->fetchAll('SELECT * FROM places WHERE 1 LIMIT '.$offset.', '.$limit);

       while(count($places) > 0){
           foreach($places as $place){
               switch($place['type_id']){
                   case '1':
                       $title    = $place['title'];
                       $url      = $this->view->baseUrl().'/'.$place['identifier'];
                       $type     = 'Region';
                       $pla      = $db->fetchRow('select count(id) as countries from places where parent_id = '.$place['id']);
                       $loves    = $place['loves'];
                       $destin   = $place['title'];
                       $overview = $db->fetchRow('select * from place_tabs where type_id = 5 and place_id = '.$place['id']);
                       $idf      = $place['identifier'];

                       $data = array(
                           'title'       => $title,
                           'url'         => $url,
                           'type'        => $type,
                           'places'      => $pla['countries'],
                           'loves'       => $loves,
                           'destination' => $destin,
                           'identifier'  => $idf,
                           'overview'    => $overview['text']
                       );

                       $index->insert($data);
                       break;

                   case '3':
                       $doc    = new Zend_Search_Lucene_Document();

                       $country  = $db->fetchRow('SELECT * FROM places WHERE id = '.$place['parent_id']);
                       $region   = $db->fetchRow('SELECT * FROM places WHERE id = '.$country['parent_id']);
                       $title    = $place['title'] . ', ' . $country['title'];
                       $url      = $this->view->baseUrl().'/'.$country['identifier'].'/'.$place['identifier'];  
                       $type     = 'Place';
                       $ttd      = $db->fetchRow('select count(id) as todos from listings where city_id = '.$place['id'].' and (main_type = 4 or main_type = 6 or main_type = 7 or main_type = 8)');
                       $pte      = $db->fetchRow('select count(id) as toeat from listings where city_id = '.$place['id'].' and main_type = 2');
                       $ptsh     = $db->fetchRow('select count(id) as tosho from listings where city_id = '.$place['id'].' and main_type = 3');
                       $ptst     = $db->fetchRow('select count(id) as tosta from listings where city_id = '.$place['id'].' and main_type = 5');
                       $loves    = $place['loves'];
                       $destin   = $region['title'] . ' ' . $country['title'] . ' ' . $place['title'];
                       $overview = $db->fetchRow('select * from place_tabs where type_id = 5 and place_id = '.$place['id']);
                       $idf      = $place['identifier'] .' '.$country['identifier'];

                       $data = array(
                           'title'       => $title,
                           'url'         => $url,
                           'type'        => $type,
                           'todo'        => $ttd['todos'],
                           'toeat'       => $pte['toeat'],
                           'toshop'      => $ptsh['tosho'],
                           'tostay'      => $ptst['tosta'],
                           'loves'       => $loves,
                           'destination' => $destin,
                           'identifier'  => $idf,
                           'overview'    => $overview['text']
                       );

                       $index->insert($data);
                       break;

                   case '2':
                       $doc    = new Zend_Search_Lucene_Document();

                       $region   = $db->fetchRow('SELECT * FROM places WHERE id = '.$place['parent_id']);
                       $title    = $place['title'];
                       $url      = $this->view->baseUrl().'/'.$place['identifier'];
                       $type     = 'Country';
                       $pla      = $db->fetchRow('select count(id) as cities from places where parent_id = '.$place['id']);
                       $listings = $place['listings'];
                       $loves    = $place['loves'];
                       $destin   = $region['title'] . ' ' . $place['title'];
                       $overview = $db->fetchRow('select * from place_tabs where type_id = 5 and place_id = '.$place['id']);
                       $idf      = $place['identifier'];

                       $data = array(
                           'title'       => $title,
                           'url'         => $url,
                           'type'        => $type,
                           'places'      => $pla['cities'],
                           'listings'    => $listings,
                           'loves'       => $loves,
                           'destination' => $destin,
                           'identifier'  => $idf,
                           'overview'    => $overview['text']
                       );

                       $index->insert($data);
                       break;
                   default:  break;
               }
           }

           $i++;
           $offset = $i*$limit;
           $places = $db->fetchAll('SELECT * FROM places WHERE 1 LIMIT '.$offset.', '.$limit);
       }

       exit;
    }

    public function testAction()
    {       
       $index = Zend_Search_Lucene::open(APPLICATION_PATH.'/../tmp/index');
       $query = Zend_Search_Lucene_Search_QueryParser::parse('San');
       $hits = $index->find($query);

       var_dump($hits);

       exit;
    }

    public function updatepricesAction()
    {
        $listings = new Model_Listings();
        $listing_prices = new Model_Prices();

        $sanjose = $listings->fetchAll('country_id = 18');
        foreach($sanjose as $listing){
            if($listing->main_type == 5 or $listing->main_type == 6){
                $price = $listing_prices->fetchRow('listing_id = '.$listing->id.' and type_id = 1');
                if(is_null($price))
                    $price = $listing_prices->fetchRow('listing_id = '.$listing->id);
                if(is_null($price)){
                    $listing->price = '0';
                    $listing->save();
                    echo $listing->title . ' - '.$listing->price .'<br>';
                } else {
                    $listing->price = $price->price;
                    $listing->save();
                }
            } else {
                $listing->price = '0';
                $listing->save();
                //echo $listing->price .' not ecommerce <br>';
            }
        }
        die;
    }

    public function updatedescriptionsAction()
    {
        $listings = new Model_Listings();
        $listing_tabs = new Model_ListingTabs();

        $sanjose = $listings->fetchAll('country_id = 18');
        foreach($sanjose as $listing){
            $tab = $listing_tabs->fetchRow('listing_id = '.$listing->id.' and label = "Overview"');
            if(is_null($tab))
                $tab = $listing_tabs->fetchRow('listing_id = '.$listing->id);
            if(!is_null($tab)){
                $listing->description = substr(strip_tags($tab->text), 0, 197);
                $listing->save();
            }
            echo $listing->description .'... <br>';
        }
        die;
    }

    public function updateindexAction()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $db->delete('search');
        $db->query('ALTER TABLE `search` AUTO_INCREMENT = 1');
        
        $select = $db->select();
        $select->from('places', array(
            'citittl'=>'title',
            'citiidf'=>'identifier'
        ));
        $select->join(array('places2'=>'places'), 'places.parent_id = places2.id', array(
            'countryttl'=>'title',
            'countryidf'=>'identifier',
        ));
        $select->where('places.parent_id = ?',18); 
        
        $cities = $db->fetchAll($select, array(), 5);
        foreach($cities as $city){
            $db->insert('search', array(
                'label' => $city->citittl . ', ' . $city->countryttl,
                'url'   => '/'.$city->countryidf . '/' . $city->citiidf,
            ));
            
            echo $city->countryttl . ', ' . $city->citittl .'<br>';
        }        
        die;
    }

    public function createidfAction()
    {
        $places = new Model_Places();
        $select = $places->select();
        $select->where('type_id = ?', 3);
        $select->where('identifier = ""');
        $select->limit(200);
        
        $cities = $places->fetchAll($select);
        
        foreach($cities as $city){
            $idf = $this->_clean($city->title);
            $city->identifier = $idf;
            $city->save();
            echo $idf.'<br>';
        }
        
        die;
    }
    
    private function _clean($str){
        $normalizeChars = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
            'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
            'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
            'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
        );
        
        $str     =     str_replace('&', '-and-', $str);
        $str     =     trim(preg_replace('/[^\w\d_ -]/si', '', $str));//remove all illegal chars
        $str     =     str_replace(' ', '_', $str);
        $str     =     str_replace('--', '-', $str);

        return strtolower(strtr($str, $normalizeChars));
    }
    
    public function assignoptionsAction()
    {
        $listings = new Model_Listings();
        $listings_schedules = new Model_ListingSchedules();
        
        $select = $listings->select();
        $select->where('country_id = 18');
        $select->where('main_type = 6');
        $lists = $listings->fetchAll($select);
        
        $data = array(
            'Morning'   => array('07:00:00','12:00:00'),
            'Afternoon' => array('01:00:00','06:00:00'),
            'Night'     => array('07:00:00','10:00:00'),
        );
        
        foreach($lists as $list){
            $select = $listings_schedules->select();
            $select->where('listing_id = ?', $list->id);
            $schs = $listings_schedules->fetchAll($select);
            if(count($schs) == 0){
                foreach($data as $name => $time){
                    $row = $listings_schedules->fetchNew();
                    $row->listing_id = $list->id;
                    $row->name = $name;
                    $row->starting = $time[0];
                    $row->ending   = $time[1];
                    $row->save();
                    echo $row->id . ' ' . $list->id . ' ' . $row->name . '<br/>';
                }
            }
        }
        die;
    }
    
    public function updateprices2Action()
    {
        $listings = new Model_Listings();
        $prices   = new Model_Prices();
        
        $select = $listings->select();
        $select->where('country_id = 18');
        $select->where('main_type = 6');
        $lists = $listings->fetchAll($select);
        
        foreach($lists as $list){
            $select = $prices->select();
            $select->where('listing_id = ?', $list->id);
            $price = $prices->fetchAll($select);
            if(count($price) > 0){
                $basic_price = '';
                foreach($price as $p){
                    echo $p->price . '   ';
                    if($p->price == '0.00' && $p->type_id == 1){
                        $p->price = '50';
                        $basic_price = $p->price;
                    }
                    if($p->type_id == 1)
                            $basic_price = $p->price;
                    
                    if($p->price == '0.00' && $p->type_id != 1){
                        if(!empty($basic_price))
                            $p->price = $basic_price;
                        else
                            $p->price = '50';
                    }
                    $p->min = 1;
                    $p->max = 50;
                    $p->additional = $p->price;
                    $p->additional_after = 1;
                    $p->save();
                    
                    echo $p->price . '    ' . $list->id . '<br>';
                }
            } else {
                $price = $prices->fetchNew();
                $price->listing_id  = $list->id;
                $price->currency    = 'USD';
                $price->price       = 50;
                $price->additional  = 50;
                $price->additional_after = 1;
                $price->min = 1;
                $price->max = 50;
                $price->type_id = 1;
                $price->created = date('Y-m-d H:i:s');
                $price->updated = date('Y-m-d H:i:s');
                $price->save();
                
                echo $price->price . '    ' . $list->id . '<br>';
            }
        }
        die;
    }
    
    public function createusersAction()
    {
        $vendors = new Model_Vendors();
        $select = $vendors->select();
        $select->where('user_id is null');
        $select->where('email <> ?',"");
        $select->where('email <> ?',"N/A");
        $vendorss = $vendors->fetchAll($select);
        
        $users = new Model_Users();
        foreach($vendorss as $vendor){
            $user = $users->fetchNew();
            $user->name         = $vendor->name;
            $user->email        = $vendor->email;
            $user->phone        = $vendor->phone;
            $user->country_id   = 18;
            $user->city_id      = $vendor->place_id;
            $user->image        = $vendor->image;
            $user->role_id      = 3;
            $user->password     = '0192023a7bbd73250516f069df18b500';
            $user->token        = $vendor->token;
            $user->created      = $vendor->created;
            $user->updated      = $vendor->updated;
            $user->active       = 1;
            
            try
            {
                $user->save();
                $vendor->user_id = $user->id;
                $vendor->save();
                echo $user->name .'<br>';    
            } 
            catch (Exception $e)
            {
                echo $e->getMessage() .'<br>';
            }
        }
        die;
    }
}

