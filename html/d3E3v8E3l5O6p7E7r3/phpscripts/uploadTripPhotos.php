<?php

ini_set('desplay_errors', 1);

// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application2'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'), 
        get_include_path(),
)));

require_once "WS/Uploader.php";

$uploader = new WS_Uploader();
$uploader->uploadTripPhotos();