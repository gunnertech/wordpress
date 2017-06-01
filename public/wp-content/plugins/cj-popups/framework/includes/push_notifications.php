<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
#########################################################################################
# Push Notifications
#########################################################################################

$cjpopups_notification_name = sha1('cjpopups_notification_'.site_url());
$cjpopups_notification_value = get_option($cjpopups_notification_name);
$cjpopups_notification_timestamp_name = sha1('cjpopups_notification_'.site_url().'timestamp');
$cjpopups_notification_timestamp_value = get_option($cjpopups_notification_timestamp_name);

// delete_option($cjpopups_notification_name);
// delete_option($cjpopups_notification_timestamp_name);
// die();

add_action( 'wp_ajax_cjpopups_get_notifications', 'cjpopups_get_notifications' );
function cjpopups_get_notifications() {

	$cjpopups_notification_name = sha1('cjpopups_notification_'.site_url());
	$cjpopups_notification_value = get_option($cjpopups_notification_name);

	$cjpopups_notification_timestamp_name = sha1('cjpopups_notification_'.site_url().'timestamp');
	$cjpopups_notification_timestamp_value = get_option($cjpopups_notification_timestamp_name);

	$now = time();
	$check = $cjpopups_notification_timestamp_value['timestamp'];

	$cjpopups_notification_slug = cjpopups_item_info('page_slug');
	$url = 'http://api.cssjockey.com/?cj_action=get-notifications&version='.cjpopups_item_info('item_version').'&slug='.$cjpopups_notification_slug;
	$cjpopups_notifications = wp_remote_get( $url );
	if($cjpopups_notifications['body'] != 'none'){
		$notification_response = json_decode($cjpopups_notifications['body']);
		if($cjpopups_notification_timestamp_value['ID'] != $notification_response->ID){
			update_option($cjpopups_notification_timestamp_name, array('ID' => $notification_response->ID, 'timestamp' => strtotime('+1 day'), 'closed' => 0));
			update_option($cjpopups_notification_name, $notification_response);
		}
	}

	die();
}

add_action( 'wp_ajax_cjpopups_close_notification', 'cjpopups_close_notification' );
function cjpopups_close_notification() {
	$cjpopups_notification_name = sha1('cjpopups_notification_'.site_url());
	$cjpopups_notification_value = get_option($cjpopups_notification_name);
	$cjpopups_notification_timestamp_name = sha1('cjpopups_notification_'.site_url().'timestamp');
	$cjpopups_notification_timestamp_value = get_option($cjpopups_notification_timestamp_name);
	update_option($cjpopups_notification_timestamp_name, array('ID' => $_POST['id'], 'timestamp' => strtotime('+1 day'), 'closed' => 1));
	die();
}

add_action('admin_notices' , 'cjpopups_show_notification');
function cjpopups_show_notification(){
	$cjpopups_notification_name = sha1('cjpopups_notification_'.site_url());
	$cjpopups_notification_value = get_option($cjpopups_notification_name);
	$cjpopups_notification_timestamp_name = sha1('cjpopups_notification_'.site_url().'timestamp');
	$cjpopups_notification_timestamp_value = get_option($cjpopups_notification_timestamp_name);
	if($cjpopups_notification_value && $cjpopups_notification_timestamp_value['closed'] != 1){
		$display[] = '<div id="notification-'.$cjpopups_notification_value->ID.'" class="updated push-notification-message">';
		$display[] = '<div class="notification-icon">';
		$display[] = '<img src="http://cssjockey.com/files/leaf-64.png" />';
		$display[] = '</div>';
		$display[] = '<div class="notification-content">';
		$display[] = '<h3 style="margin:0 0 10px 0;">'.cjpopups_item_info('item_name').'</h3>';
		$display[] = '<p style="font-size:14px; margin:0 0 0 0;"><b>'.$cjpopups_notification_value->title.'</b><i style="color: #999;"> ~ '.$cjpopups_notification_value->dated.'</i></p>';
		$display[] = '<div style="padding-right:50px;">'.$cjpopups_notification_value->content.'</div>';
		$display[] = '</div>';
		$display[] = '<a href="#notification-'.$cjpopups_notification_value->ID.'" data-id="'.$cjpopups_notification_value->ID.'" class="notification-close">x</a>';
		$display[] = '</div>';
		echo implode('', $display);
	}
}



function cjpopups_notifications_js(){
	$cjpopups_notification_name = sha1('cjpopups_notification_'.site_url());
	$cjpopups_notification_value = get_option($cjpopups_notification_name);
	$cjpopups_notification_timestamp_name = sha1('cjpopups_notification_'.site_url().'timestamp');
	$cjpopups_notification_timestamp_value = get_option($cjpopups_notification_timestamp_name);

	if(!isset($cjpopups_notification_timestamp_value['timestamp'])){
		update_option($cjpopups_notification_timestamp_name, array('ID' => 0, 'timestamp' => time('+1 minute'), 'closed' => 0));
	}

	$cjpopups_notification_value = get_option($cjpopups_notification_name);
	$cjpopups_notification_timestamp_value = get_option($cjpopups_notification_timestamp_name);

	$now = time();
	$check = $cjpopups_notification_timestamp_value['timestamp'];

	if($check < $now){
		wp_enqueue_script('cj-push-notifications-js', cjpopups_item_path('admin_assets_url') .'/js/push-notifications.js', array('jquery'),'',true);
	}
}
add_action( 'admin_enqueue_scripts' , 'cjpopups_notifications_js', 10);







