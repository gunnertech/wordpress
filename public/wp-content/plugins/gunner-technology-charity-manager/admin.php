<?php
add_action('admin_menu', 'gtcm_admin');

function gtcm_admin() {
  include_once(GTCM_FILE_PATH."/achievements.php");
  include_once(GTCM_FILE_PATH."/campaigns.php");
  include_once(GTCM_FILE_PATH."/donations.php");
  
  $base_page = 'gtcm-campaigns';
					
	add_menu_page(__('Charity'), __('Charity'), 2, $base_page, array(), GTCM_URL."/images/charity.png");
	
	$page_hooks[] = add_submenu_page($base_page,__('Campaigns'), __('Campaigns'), 7, 'gtcm-campaigns', 'gtcm_campaigns');
  $page_hooks[] = add_submenu_page($base_page, __('Donations'), __('Donations'), 7, 'gtcm-donations', 'gtcm_donations');
	$page_hooks[] = add_submenu_page($base_page,__('Achievements'), __('Achievements'), 7, 'gtcm-achievements', 'gtcm_achievements');
  
  wp_enqueue_style('gtcm-admin', GTCM_URL.'/stylesheets/admin.css');
  wp_enqueue_style('gtcm-datePicker', GTCM_URL.'/stylesheets/datePicker.css');
  wp_enqueue_script('gtcm-admin', GTCM_URL.'/javascripts/admin.js');
  wp_enqueue_script('gtcm-date', GTCM_URL.'/javascripts/date.js');
  wp_enqueue_script('gtcm-datePicker', GTCM_URL.'/javascripts/datePicker.js');
  wp_enqueue_script('gtcm-donations', GTCM_URL.'/javascripts/donations.js');
} ?>