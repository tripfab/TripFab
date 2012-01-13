<?php

/*
function by Wes Edling .. http://joedesigns.com
feel free to use this in any project, i just ask for a credit in the source code.
a link back to my site would be nice too.
*/

function resize($imagePath,$opts=null){

	# start configuration
	
	$cacheFolder = './cache/'; # path to your cache folder, must be writeable by web server
	$remoteFolder = $cacheFolder.'remote/'; # path to the folder you wish to download remote images into
	$quality = 90; # image quality to use for ImageMagick (0 - 100)
	
	$cache_http_minutes = 20; 	# cache downloaded http images 20 minutes

	$path_to_convert = 'convert'; # this could be something like /usr/bin/convert or /opt/local/share/bin/convert
	
	## you shouldn't need to configure anything else beyond this point

	$purl = parse_url($imagePath);
	$finfo = pathinfo($imagePath);
	$ext = $finfo['extension'];
	
	//var_dump($purl); die;

	# check for remote image..
	if(isset($purl['scheme']) && $purl['scheme'] == 'http'):
		# grab the image, and cache it so we have something to work with..
		list($filename) = explode('?',$finfo['basename']);
		$local_filepath = $remoteFolder.$filename;
		$download_image = true;
		if(file_exists($local_filepath)):
			if(filemtime($local_filepath) < strtotime('+'.$cache_http_minutes.' minutes')):
				$download_image = false;
			endif;
		endif;
		if($download_image == true):
			$ch = curl_init($imagePath);
			$fp = fopen($local_filepath, "w");
			
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
		endif;
		$imagePath = $local_filepath;
	endif;

	if(file_exists($imagePath) == false):
		$imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
		if(file_exists($imagePath) == false):
			return 'image not found';
		endif;
	endif;

	if(isset($opts['w'])): $w = $opts['w']; endif;
	if(isset($opts['h'])): $h = $opts['h']; endif;

	$filename = md5_file($imagePath);

	if(!empty($w) and !empty($h)):
		$newPath = $cacheFolder.$filename.'_w'.$w.'_h'.$h.(isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "").(isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "").'.'.$ext;
	elseif(!empty($w)):
		$newPath = $cacheFolder.$filename.'_w'.$w.'.'.$ext;	
	elseif(!empty($h)):
		$newPath = $cacheFolder.$filename.'_h'.$h.'.'.$ext;
	else:
		return false;
	endif;

	$create = true;

	if(file_exists($newPath) == true):
		$create = false;
		$origFileTime = date("YmdHis",filemtime($imagePath));
		$newFileTime = date("YmdHis",filemtime($newPath));
		if($newFileTime < $origFileTime):
			$create = true;
		endif;
	endif;

	if($create == true):
		if(!empty($w) and !empty($h)):

			list($width,$height) = getimagesize($imagePath);
			$resize = $w;
		
			if($width > $height):
				$resize = $w;
				if(isset($opts['crop']) && $opts['crop'] == true):
					$resize = "x".$h;				
				endif;
			else:
				$resize = "x".$h;
				if(isset($opts['crop']) && $opts['crop'] == true):
					$resize = $w;
				endif;
			endif;

			if(isset($opts['scale']) && $opts['scale'] == true):
				$cmd = $path_to_convert." \"".$imagePath."\" -resize ".$resize." -quality ".$quality." ".$newPath;
			else:
				$cmd = $path_to_convert." \"".$imagePath."\" -resize ".$resize." -size ".$w."x".$h." xc:".(isset($opts['canvas-color'])?$opts['canvas-color']:"transparent")." +swap -gravity center -composite -quality ".$quality." ".$newPath;
			endif;
						
		else:
			$cmd = $path_to_convert." \"".$imagePath."\" -thumbnail ".(!empty($h) ? 'x':'').$w."".(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "")." -quality ".$quality." ".$newPath;
		endif;
		
		$c = exec($cmd);
		
	endif;

	# return cache file path
	return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);
	
}
?>