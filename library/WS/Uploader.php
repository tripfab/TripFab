<?php

require_once 'Zend/Auth.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Db/Table.php';
require_once 'Zend/Db/Table/Row.php';
require_once 'Zend/Db/Adapter/Mysqli.php';
require_once 'WS/Uploader/Service.php';


class WS_Uploader {
    
    public function __construct() {}
    
    public function upload(){
        
        if($_POST)
        {
            try
            {
                error_reporting(E_ALL);  
                
                $ids = $_POST['userids'];
                $token = $_POST['formtoken'];
                $form_id = $_POST['usertoken'];

                $dbAdapter = new Zend_Db_Adapter_Mysqli(array(
                    'host'      =>  'localhost',
                    'username'  =>  'costar_admCoreTF',
                    'password'  =>  'OgkX-JLV2L7i',
                    'dbname'    =>  'costar_coreTF'
                ));

                Zend_Db_Table::setDefaultAdapter($dbAdapter);

                $users_db = new Zend_Db_Table('users');

                $select = $users_db->select();
                $select->where('id = ?', $ids);
                $user = $users_db->fetchRow($select);

                if($token != $user->token)
                        throw new Exception();
                if($form_id != md5($user->token.'change_picture'))
                        throw new Exception();
                

                $tempFile   = $_FILES['Filedata']['tmp_name'];
                $targetPath = realpath(APPLICATION_PATH.'/../html/images/users/');
                $targetPath1 = realpath(APPLICATION_PATH.'/../partners/images/users/');
                $targetFile = str_replace('//','/',$targetPath) .'/'. $user->id.substr(md5($user->token), 0, 10) . '.jpg';
                
                mkdir(str_replace('//','/',$targetPath), 0777, true);
                mkdir(str_replace('//','/',$targetPath1), 0777, true);
                mkdir(str_replace('//','/',$targetPath2), 0777, true);
                
                move_uploaded_file($tempFile,$targetFile);
                exec("cp {$targetFile} {$targetPath1}");

                
                $image = $user->id.substr(md5($user->token), 0, 10) . '.jpg';

                if(empty($user->image)){
                    if($user->complete == 90){
                        $user->complete = $user->complete + 10;
                        $user->points   = $user->points   + 20;
                    } else {
                        $user->complete = $user->complete + 10;
                        $user->points   = $user->points   + 10;
                    }   
                }

                $user->image = '/images/users/'.$image;
                $user->save();

                echo $user->image;
            } 
            catch(Exception $e)
            {
                echo 'error';
            }
        }
        
    }
    
    public function uploadPhoto()
    {
        if($_POST)
        {
            try
            {   
                $ids = $_POST['userids'];
                $token = $_POST['formtoken'];
                $form_id = $_POST['usertoken'];
                $aid = $_POST['album'];

                $dbAdapter = new Zend_Db_Adapter_Mysqli(array(
                    'host'      =>  'localhost',
                    'username'  =>  'costar_admCoreTF',
                    'password'  =>  'OgkX-JLV2L7i',
                    'dbname'    =>  'costar_coreTF'
                ));
                Zend_Db_Table::setDefaultAdapter($dbAdapter);

                $users_db = new Zend_Db_Table('users');

                $select = $users_db->select();
                $select->where('id = ?', $ids);
                $user = $users_db->fetchRow($select);

                if($token != $user->token)
                        throw new Exception();
                if($form_id != md5($user->token.'upload_photos'))
                        throw new Exception();
                
                $albums_db = new Zend_Db_Table('user_albums');
                $select = $albums_db->select();
                $select->where('id = ?', $aid);
                $select->where('user_id = ?', $user->id);
                $album = $albums_db->fetchRow($select);
                if(is_null($album))
                        throw new Exception();
                
                $pictures_db = new Zend_Db_Table('user_pictures');
                $picture = $pictures_db->fetchNew();
                $picture->user_id = $user->id;
                $picture->album_id = $album->id;
                $picture->created = date('Y-m-d g:i:s');
                $picture->updated = date('Y-m-d g:i:s');
                $picture->save();
                
                $tempFile   = $_FILES['Filedata']['tmp_name'];
                $publicPath = realpath(APPLICATION_PATH.'/../html');
                $targetPath = '/images/users/albums/'.$album->id;
                $url        = $targetPath .'/'. $user->id.substr(md5($picture->id), 3, 11) .$album->id . '.jpg';
                $targetFile = str_replace('//','/',$publicPath.$url);
                
                mkdir(str_replace('//','/',$publicPath.$targetPath), 0755, true);
                move_uploaded_file($tempFile,$targetFile);
                
                $picture->url = $url;

                if(empty($album->image)){
                    $album->image = $picture->url;
                }
                $album->photos = $album->photos + 1;
                $album->save();
                $picture->save();
                echo $picture->url;
            } 
            catch(Exception $e)
            {
                echo $e;
            }
        }
    }
    
    public function uploadListingPhotos()
    {
            try
            {   
                error_reporting(E_ALL);  
				
                $ids            = $_GET['userids'];
                $token          = $_GET['formtoken'];
                $form_id        = $_GET['usertoken'];
                $listing_id     = $_GET['listings'];
                $listing_token  = $_GET['listtoken'];
                $vendor_id      = $_GET['vendorid'];
                
                $dbAdapter = new Zend_Db_Adapter_Mysqli(array(
                    'host'      =>  'localhost',
                    'username'  =>  'costar_admCoreTF',
                    'password'  =>  'OgkX-JLV2L7i',
                    //'username'  =>  'root',
                    //'password'  =>  'root',
                    'dbname'    =>  'costar_coreTF'
                ));

                Zend_Db_Table::setDefaultAdapter($dbAdapter);
                
                $users_db = new Zend_Db_Table('users');

                $select = $users_db->select();
                $select->where('id = ?', $ids);
                $user = $users_db->fetchRow($select);

                if($token != $user->token)
                        throw new Exception();
                if($form_id != md5($user->token.'upload_listing_pictures'))
                        throw new Exception();
                
                $vendors_db = new Zend_Db_Table('vendors');
                $select = $vendors_db->select();
                $select->where('id = ?', $vendor_id);
                //$select->where('user_id = ?', $user->id);
                $vendor = $vendors_db->fetchRow($select);
                if(is_null($vendor))
                        throw new Exception();
                
                $listings_db = new Zend_Db_Table('listings');
                $select = $listings_db->select();
                $select->where('id = ?', $listing_id);
                $select->where('token = ?', $listing_token);
                $select->where('vendor_id = ?', $vendor->id);
                $listing = $listings_db->fetchRow($select);
                if(is_null($listing))
                        throw new Exception();
                
                $images_db = new Zend_Db_Table('listing_pictures');
                $image = $images_db->fetchNew();
                $image->listing_id  = $listing->id;
                $image->created     = date('Y-m-d H:i:s');
                $image->updated     = $image->created;
                $image->save();
                
                $publicPath  = realpath(APPLICATION_PATH.'/../html');
                $publicPath2 = realpath(APPLICATION_PATH.'/../partners');
                $targetPath  = '/images/listings/'.$listing->id . '/';
                
                $url        = $targetPath . $image->id . substr(md5($image->id), 5, 12) .'.jpg';
                $targetFile = str_replace('//','/',$publicPath . $url);
                
                $allowedExtensions = array('jpg','jpeg','png','gif');
                $sizeLimit = 2 * 1024 * 1024;
                $paths = array(
                    'public'    => $publicPath.$targetPath,
                    'tagetfile' => $targetFile,
                    'public2'   => $publicPath2.$targetPath
                );
                
                $uploader = new WS_Uploader_Service($allowedExtensions, $sizeLimit);
                
                $result = $uploader->handleUpload($paths);
                
                if(isset($result['success'])) {
                    $image->url = $url;
                
                    $defaults = array(
                        '/images2/ico-97.png',
                        '/images2/ico-98.png',
                        '/images2/ico-99.png',
                        '/images2/ico-100.png',
                        '/images2/ico-101.png',
                    );
                    if(empty($listing->image) || $_POST['main'] == 1 || in_array($listing->image,$defaults)){
                        $images_db->update(array('main'=>0), 'listing_id = '.$listing->id);
                        $image->main = 1;
                        $listing->image = $image->url;
                        $listing->save();
                    }
                    $image->save();
                } else {
                    $image->delete();
                }
                
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
            } 
            catch(Exception $e)
            {
                echo htmlspecialchars(json_encode(array('error'=>$e->getMessage())), ENT_NOQUOTES);
            }
    }
}