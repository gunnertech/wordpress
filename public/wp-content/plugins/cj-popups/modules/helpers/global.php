<?php
$helpers_path = dirname(__FILE__).'/';
$dirs = scandir($helpers_path);

foreach ($dirs as $dir) {
	$dir_path = $helpers_path.$dir;
	if(is_dir($dir_path) && $dir != '.' && $dir != '..'){
		$file_path = $dir_path.'/init.php';
		if(file_exists($file_path)){
			require_once($file_path);
		}
	}
}