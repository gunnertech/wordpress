<?php
require_once ('class-plugin-update.php');
$item_info = cjpopups_item_info();
$cjpopups_eon = sha1('cjpopups_verify_epc'.site_url());
$cjpopups_eov = get_option($cjpopups_eon);
$cjpopups_upgrade_url = 'http://api.cssjockey.com/?cj_action=upgrades&item_id='.$item_info['item_id'].'&item_type='.$item_info['item_type'].'&purchase_code='.$cjpopups_eov.'&slug='.basename(cjpopups_item_path('item_dir')).'&site_url='.site_url();
$cjpopups_plugin_upgrades = new PluginUpdateChecker(
	$cjpopups_upgrade_url,
	cjpopups_item_path('item_dir').'/index.php',
	basename(cjpopups_item_path('item_dir'))
);