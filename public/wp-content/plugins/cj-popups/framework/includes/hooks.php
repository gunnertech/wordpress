<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
$hooks_file = sprintf('%s/hooks.php', cjpopups_item_path('item_dir'));
if(file_exists($hooks_file)){
	require_once($hooks_file);
}