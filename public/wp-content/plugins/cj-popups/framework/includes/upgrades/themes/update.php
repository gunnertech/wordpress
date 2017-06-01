<?php
require 'theme-update-checker.php';
$item_info = cjpopups_item_info();
$cjpopups_eon = sha1('cjpopups_verify_epc'.site_url());
$cjpopups_eov = get_option($cjpopups_eon);
$cjpopups_upgrade_url = 'http://api.cssjockey.com/?cj_action=upgrades&item_id='.$item_info['item_id'].'&item_type='.$item_info['item_type'].'&purchase_code='.$cjpopups_eov.'&slug='.basename(cjpopups_item_path('item_dir')).'&site_url='.site_url();
$theme_slug = basename(get_template_directory_uri());
$theme_update_checker = new ThemeUpdateChecker(
	$theme_slug,
	$cjpopups_upgrade_url //URL of the metadata file.
);