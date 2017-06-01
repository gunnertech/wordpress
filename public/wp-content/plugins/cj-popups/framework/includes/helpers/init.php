<?php
$cjpopups_helpers_path = dirname(__FILE__).'/';
$cjpopups_exclude_helpers = array(
	'.', '..', 'init.php'
);
$cjpopups_helpers = scandir($cjpopups_helpers_path);
foreach ($cjpopups_helpers as $key => $file) {
	if(!in_array($file, $cjpopups_exclude_helpers)){
		if(file_exists($cjpopups_helpers_path.$file)){
			require_once($file);
		}
	}
}