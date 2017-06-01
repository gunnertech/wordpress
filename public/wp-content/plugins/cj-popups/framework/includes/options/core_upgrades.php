<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
$cjpopups_envato_username = get_option('cjpopups_envato_username');
$cjpopups_envato_api_key = get_option('cjpopups_envato_api_key');
$cjpopups_envato_purchase_code = get_option('cjpopups_envato_purchase_code');
$cjpopups_envato_item_id = get_option('cjpopups_envato_item_id');
$cjpopups_last_update = get_option('cjpopups_last_update');

// Download Url
$download_latest_version_url = 'http://marketplace.envato.com/api/edge/'.get_option('cjpopups_envato_username').'/'.get_option('cjpopups_envato_api_key').'/download-purchase:'.get_option('cjpopups_envato_purchase_code').'.json';
$process_download_url = cjpopups_string(cjpopups_callback_url('core_upgrades')).'cjpopups_action=process_download';
// Item Details Url
$item_info_url = 'http://marketplace.envato.com/api/edge/item:'.$cjpopups_envato_item_id.'.json';

if(!isset($_GET['cjpopups_action']) && $cjpopups_envato_username != ''){
	$cjpopups_check_upgrade = wp_remote_get($item_info_url);
	if(is_wp_error($cjpopups_check_upgrade)){
		echo cjpopups_show_message('error', $cjpopups_check_upgrade->get_error_message());
	}else{
		$cjpopups_check_upgrade = json_decode($cjpopups_check_upgrade['body']);
		$remote_last_update_timestamp = strtotime($cjpopups_check_upgrade->item->last_update);
		if($cjpopups_last_update == ''){
			update_option('cjpopups_last_update', time());
		}else{
			$cjpopups_last_update = get_option('cjpopups_last_update');
			if($remote_last_update_timestamp > $cjpopups_last_update){
				echo cjpopups_show_message('info', sprintf(__('<b>New version is available to download.</b> <br><a href="%s" class="bold">Click here</a> to download latest version.', 'cjpopups'), $process_download_url));
			}else{
				echo cjpopups_show_message('success', sprintf(__('<b>You are using the latest version.</b> <br>Last checked on %s', 'cjpopups'), date('M dS, Y h:i:s A', time())));
			}
		}
	}
}


if(isset($_GET['cjpopups_action']) && $_GET['cjpopups_action'] == 'process_download'){
	$download_latest_version = wp_remote_get($download_latest_version_url);
	if(is_wp_error( $download_latest_version )){
		echo cjpopups_show_message('error', $download_latest_version->get_error_message());
	}else{
		$download_response = json_decode($download_latest_version['body']);
		foreach ($download_response as $key => $value) {
			if(isset($value->download_url)){
				wp_safe_redirect( $value->download_url );
				update_option('cjpopups_last_update', time());
			}
		}
	}
}



global $wpdb;
$cjpopups_upgrade_errors = null;
if(isset($_POST['save_envato_info'])){
	if($_POST['cjpopups_envato_username'] == ''){
		$cjpopups_upgrade_errors[] = __('Envato Username is required.', 'cjpopups');
	}elseif($_POST['cjpopups_envato_api_key'] == ''){
		$cjpopups_upgrade_errors[] = __('Envato API Key is required.', 'cjpopups');
	}elseif($_POST['cjpopups_envato_purchase_code'] == ''){
		$cjpopups_upgrade_errors[] = __('Envato Item Purchase Code is required.', 'cjpopups');
	}else{
		$url = 'http://marketplace.envato.com/api/v3/cssjockey/c2u03ax2x2iwd6hbxfxt1ixmn5sqi74w/verify-purchase:'.$_POST['cjpopups_envato_purchase_code'].'.json';
		$response = wp_remote_get($url);
		if(is_wp_error($response)){
			$cjpopups_upgrade_errors[] = $response->get_error_message();
		}else{
			$response = json_decode($response['body']);
			foreach ($response as $key => $value) {
				if(!isset($value->item_id)){
					$cjpopups_upgrade_errors[] = __('Could not verify purchase, please try again.', 'cjpopups');
				}else{
					update_option('cjpopups_envato_item_id', $value->item_id);
				}
			}
		}
	}
	if(!is_null($cjpopups_upgrade_errors)){
		echo cjpopups_show_message('error', implode('<br>', $cjpopups_upgrade_errors));
	}else{
		update_option('cjpopups_envato_username', $_POST['cjpopups_envato_username']);
		update_option('cjpopups_envato_api_key', $_POST['cjpopups_envato_api_key']);
		update_option('cjpopups_envato_purchase_code', $_POST['cjpopups_envato_purchase_code']);
	}
}

$cjpopups_form_options['envato_api_info'] = array(
	array(
		'type' => 'sub-heading',
		'id' => 'envato_info_heading',
		'label' => '',
		'info' => '',
		'suffix' => '',
		'prefix' => '',
		'default' => sprintf(__('%s ~ Verify Purchase', 'cjpopups'), cjpopups_item_info('item_name')),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'text',
		'id' => 'cjpopups_envato_username',
		'label' => __('Envato Username', 'cjpopups'),
		'info' => __('Specify your envato username here', 'cjpopups'),
		'suffix' => '',
		'prefix' => '',
		'default' => get_option('cjpopups_envato_username'),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'text',
		'id' => 'cjpopups_envato_api_key',
		'label' => __('Envato API Key', 'cjpopups'),
		'info' => __('Specify your envato API Key here.<br>You can create or get your API Key from Envato Profile >> Settings >> API Keys.', 'cjpopups'),
		'suffix' => '',
		'prefix' => '',
		'default' => get_option('cjpopups_envato_api_key'),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'text',
		'id' => 'cjpopups_envato_purchase_code',
		'label' => __('Purchase Code', 'cjpopups'),
		'info' => __('Enter your item purchase code here.<br>You can download your purchase from Envato Profile >> Downloads Page', 'cjpopups'),
		'suffix' => '',
		'prefix' => '',
		'default' => get_option('cjpopups_envato_purchase_code'),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'submit',
		'id' => 'save_envato_info',
		'label' => __('Verify Purchase', 'cjpopups'),
		'info' => '',
		'suffix' => '',
		'prefix' => '',
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
);

echo '<form action="" method="post">';
cjpopups_admin_form_raw($cjpopups_form_options['envato_api_info']);
echo '</form>';