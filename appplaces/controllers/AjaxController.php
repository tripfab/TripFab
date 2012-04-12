<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxController
 *
 * @author magentodeveloper
 */
class AjaxController extends Zend_Controller_Action{
    
    const CLIENT_ID     = 'UAZO1NP00B4BHVHJGONXBEKHMARCCDXUCIBRYIKVXJIYDWQU';
    
    const CLIENT_SECRET = 'C5DMAZR3H4PKNSYD4DHGVV0HV2HWBU2R3EW2FHXZPKI0PIRX';
    
    /**
     *
     * @var Zend_Db_Table
     */
    protected $places;    
    
    public function init()
    {
        $this->places = new Zend_Db_Table('places');
    }
    
    public function getplacesAction()
    {
        $select = $this->places->select();
        $select->where('parent_id = ?', $_POST['parent']);
        $select->order('title ASC');
        
        $places = $this->places->fetchAll($select);
        
        $this->view->places = $places;
    }
    
    public function searchAction()
    {
        $client = new Zend_Http_Client('https://graph.facebook.com/search');
        foreach($_POST as $key => $value)
            $client->setParameterGet($key, $value);
        
        $response = $client->request();
        
        $body = $response->getBody();
        
        $this->view->results = json_decode($body);
        $this->view->class = "place";
    }
    
    public function loadjsonAction()
    {
        $client = new Zend_Http_Client($_POST['url']);
        $response = $client->request();
        $body = $response->getBody();
        $this->view->results = json_decode($body);
        $this->view->class = "place";
        $this->render('search');
    }
    
    public function getinfoAction()
    {
        $client = new Zend_Http_Client('https://graph.facebook.com/search');
        foreach($_POST as $key => $value)
            $client->setParameterGet($key, $value);
        
        $response = $client->request();
        
        $body = $response->getBody();
        
        $this->view->results = json_decode($body);
        $this->view->class = "lb";
        $this->render('search');
    }
    
    public function getcatsAction()
    {
        $cat = $this->_getParam('cat',0);
        if($cat == 0)
            throw new Exception();
        
        $types = new Zend_Db_Table('listing_types');
        $select = $types->select();
        $select->where('parent_id = ?', $cat);
        $cats = $types->fetchAll($select);
        
        if(count($cats) == 0)
            throw new Exception();
        
        $this->view->cats = $cats;
    }
    
    public function formAction()
    {
        $id = $this->_getParam('id');
        $city = $this->_getParam('city');
        $type = $this->_getParam('type');
        
        $fqs = new Zend_Db_Table('fq');
        $select = $fqs->select();
        $select->where('fq_id = ?',$id);
        $fq = $fqs->fetchRow($select);
        if(is_null($fq)) {
            $client = new Zend_Http_Client('https://api.foursquare.com/v2/venues/'.$id);
            $client->setParameterGet('client_id', self::CLIENT_ID);
            $client->setParameterGet('client_secret', self::CLIENT_SECRET);
            $client->setParameterGet('v', date('Ymd'));

            $response = $client->request();
            $fq = $fqs->fetchNew();
            $fq->fq_id = $id;
            $fq->fq_data = $response->getBody();
            $fq->created = date('Y-m-d H:i:s');
            $fq->city    = $city;
            $fq->type    = $type;
            $fq->country = 18;

            $fq->save();
        }
        
        if($fq->added != 1 and $fq->trashed != 1){
        
            $fqplace = json_decode($fq->fq_data);

            $this->view->fqplace = $fqplace->response->venue;
            $this->view->id = $fq->id;
            $this->view->city = $this->_getParam('cityname');
        
        } else {
            if($fq->trashed == 1) {
                $this->view->message = 'Place trahed';
                $this->render('trashed');
            }
            else {
                $this->view->message = 'Place already added';
                $this->render('trashed');
            }
        }
    }
    
    public function form2Action()
    {
        $fqs = new Zend_Db_Table('fq');
        $select = $fqs->select();
        $select->where('id = ?', $this->_getParam('id'));
        $fq = $fqs->fetchRow($select);
        
        if($this->_getParam('task') == 'Next >>') {
            
            if(is_null($fq->fb_data)) {
            
                $fq->fb_page = $this->_getParam('url');

                $url = parse_url($this->_getParam('url'));
                $url = @split('/', $url['path']);
                $id = ($url[1] == 'pages') ? $url[3] : $url[1];
				
				//echo $id; die;

                $client = new Zend_Http_Client('https://graph.facebook.com/'.$id);
                $client->setParameterGet('access_token', $this->_getParam('token'));

                $response = $client->request();

                $body = json_decode($response->getBody());
                
                if(isset($body->error)) {
                    echo 'Error: '. $body->error->message; die;
                }

                $client = new Zend_Http_Client('https://graph.facebook.com/'.$id.'/albums');
                $client->setParameterGet('access_token', $this->_getParam('token'));

                $response = $client->request();

                $albums = json_decode($response->getBody());
                
                if(isset($albums->error)) {
                    echo 'Error: '. $albums->error->message; die;
                }

                $photos = array();

                foreach($albums->data as $alb) {
                    $client = new Zend_Http_Client('https://graph.facebook.com/'.$alb->id.'/photos');
                    $client->setParameterGet('access_token', $this->_getParam('token'));

                    $response = $client->request();

                    $pics = json_decode($response->getBody());
                    
                    if(!isset($pics->error)) {

                        foreach($pics->data as $pic) {
                            $_pic = new stdClass();
                            $_pic->thumb = $pic->picture;
                            $_pic->url = $pic->source;

                            $photos[] = $_pic;
                        }

                    }
                }

                $body->photos = $photos;

                $fq->fb_data = json_encode($body);
                $fq->save();
                
            } 
                
            $this->view->fqplace = $fq;
        }
        else
        {
            $fq->trashed = 1;
            $fq->save();
            $this->view->message = 'The place was successfully trashed';
            $this->render('trashed');
        }
    }
    
    public function saveAction()
    {
        if($this->_request->isPost())
        {
            $data = $_POST;
            $listings = new Zend_Db_Table('listings');
            
            $listing  = $listings->fetchNew();
            
            $listing->country_id  = $data['country_id'];
            $listing->city_id     = $data['city_id'];
            $listing->vendor_id   = 0;
            $listing->main_type   = $data['main_type'];
            $listing->title       = $data['title'];
            $listing->description = $data['description'];
            $listing->identifier  = str_replace(' ','_',strtolower($data['title']));
            $listing->address     = $data['address'];
            $listing->lat         = $data['lat'];
            $listing->lng         = $data['lng'];
            $listing->status      = 1;
            $listing->phone       = $data['phone'];
            $listing->email       = $data['email'];
            $listing->website     = $data['website'];
            $listing->created     = date('Y-m-d H:i:s');
            $listing->updated     = date('Y-m-d H:i:s');
            $listing->save();
            $listing->token = md5($listing->id.time().rand(0,100000));
            $listing->save();
            
            $pictures = new Zend_Db_Table('listing_pictures');
            if(count($data['pics'])>0) {
                $main = (isset($data['pics']['main'])) ? $data['pics']['main'] : false;
                unset($data['pics']['main']);
                if($main === false) {
                    $kwys = array_keys($data['pics']);
                    $main = $kwys[0];
                }
                foreach($data['pics'] as $i => $pic) {
                    $picture = $pictures->fetchNew();
                    $picture->listing_id = $listing->id;
                    $picture->url = $pic['include'];
                    if($main == $i) {
                        $picture->main = 1;
                        $listing->image = $picture->url;
                        $listing->save();
                    }
                    $picture->created = date('Y-m-d H:i:s');
                    $picture->updated = date('Y-m-d H:i:s');
                    $picture->save();
                }
            }
            
            $schedules = new Zend_Db_Table('listing_schedules');
            foreach($data['sch'] as $sch){
                if(!empty($sch['starting'])) {
                    $schedule = $schedules->fetchNew();
                    $schedule->listing_id = $listing->id;
                    $schedule->name = $sch['name'];
                    $schedule->starting = $sch['starting'];
                    $schedule->ending = $sch['ending'];
                    $schedule->save();
                }
            }
            
            $cards = implode(', ', $data['cards']);
            if(is_null($cards)) $cards = '';
            
            $places = new Zend_Db_Table('listing_place');
            $place  = $places->fetchNew();
            $place->setFromArray($data['place']);
            
            $place->cards = $cards;
            $place->created = date('Y-m-d H:i:s');
            $place->updated = date('Y-m-d H:i:s');
            $place->listing_id = $listing->id;
            $place->fqid = $data['fqid'];
            
            $place->save();
            
            $fqs = new Zend_Db_Table('fq');
            $select = $fqs->select();
            $select->where('id = ?', $data['fqid']);
            $fq = $fqs->fetchRow($select);
            
            $fq->added = 1;
            $fq->save();
        }        
    }
}
