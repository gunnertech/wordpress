<?php
add_action('init', 'cjpopups_automatic_upgrades');
function cjpopups_automatic_upgrades(){
	$cjpopups_eon = sha1('cjpopups_verify_epc'.site_url());
	$cjpopups_eov = get_option($cjpopups_eon);
	// Reset license
	if(isset($_GET['cjpopups_action']) && $_GET['cjpopups_action'] == 'reset-license'){
		$url = 'http://api.cssjockey.com/?cj_action=reset_license&purchase_code='.$cjpopups_eov.'&u='.site_url();
		$response = wp_remote_get($url);
		if(!is_wp_error($response)){
			if($response['body'] == 'removed'){
				delete_option($cjpopups_eon);
				wp_safe_redirect(cjpopups_callback_url('core_welcome'));
				exit;
			}else{
				$location = cjpopups_callback_url('core_welcome').'&cjmsg=pc-no-match';
				wp_safe_redirect($location);
				exit;
			}
		}
	}

	if($cjpopups_eov != ''){
		$item_type = cjpopups_item_info('item_type');
		switch ($item_type) {
			case 'theme':
				require_once('themes/update.php');
				break;
			case 'plugin':
				require_once('plugins/update.php');
				break;
		}
	}
}

add_action('admin_notices', 'cjpopups_verify_epcp');
function cjpopups_verify_epcp(){
	global $wpdb, $current_user;
	$cjpopups_eon = sha1('cjpopups_verify_epc'.site_url());
	$cjpopups_eov = get_option($cjpopups_eon);
	$item_info = cjpopups_item_info();

	$cssjockey_api_url = 'http://api.cssjockey.com/';

	$errors = null; $success = null; $error_msg = null; $success_msg = null;
	// Handle local install
	if(isset($_POST['cjpopups_verify_purchase_code'])){
		if($_POST['cjpopups_purchase_code'] == ''){
			$errors[] = __('Please enter purchase code for this product.', 'cjpopups');
		}
		if(is_null($errors)){
			$pdata = array(
				'cj_action' => 'verify_envato_code',
				'purchase_code' => $_POST['cjpopups_purchase_code'],
				'item_id' => $item_info['item_id'],
				'textdomain' => $item_info['page_slug'],
				'site_url' => site_url(),
			);
			$response = wp_remote_post($cssjockey_api_url , array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => $pdata,
				'cookies' => array()
			    )
			);
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				$errors[] = $error_message;
			} else {
				$result = json_decode($response['body']);
				if(isset($result->success)){
					update_option($cjpopups_eon, $_POST['cjpopups_purchase_code']);
					$location = cjpopups_callback_url('core_welcome');
					wp_safe_redirect( $location );
					exit;
				}
				if(isset($result->error)){
					$errors['envato'] = $result->error;
				}
			}
		}
		if(!is_null($errors)){
			$error_msg = '<p class="red">'.implode('<br>', $errors).'</p>';
		}
	}

	if(!cjpopups_is_local() && cjpopups_item_info('item_id') != 'NA'){
		$display[] = '<div id="verify-purchase-code" class="updated push-notification-message">';
		$display[] = '<div class="notification-icon">';
		$display[] = '<img src="http://api.cssjockey.com/files/leaf-64.png" />';
		$display[] = '</div>';
		$display[] = '<div class="notification-content">';
		$display[] = '<h3 style="margin:0 0 10px 0; line-height:1;">'.$item_info['item_name'].'</h3>';
		$display[] = $success_msg;
		$display[] = '<p>'.sprintf(__('Verify purchase code to enable automatic upgrades and use this %s on this installation.', 'cjpopups'), $item_info['item_type']).'</p>';
		$display[] = '<form action="" method="post">';
		$display[] = $error_msg;
		$display[] = '<p><input name="cjpopups_purchase_code" type="text" value="'.cjpopups_post_default('cjpopups_purchase_code', '').'" class="verify-input" /></p>';
		$display[] = '<p>';
		$display[] = '<button name="cjpopups_verify_purchase_code" class="button-primary" style="margin-right:10px;" type="submit">'.__('Verify & Activate License', 'cjpopups').'</button>';
		$display[] = sprintf(__('<a target="_blank" href="%s">Where can I find my Purchase Code?</a>', 'cjpopups'), 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-can-I-find-my-Purchase-Code-');
		$display[] = '</p>';
		$display[] = '</form>';
		$display[] = '</div>';
		$display[] = '</div>';
	}else{
		$display[] = '';
	}

	if($cjpopups_eov == ''){
		echo implode(null, $display);
	}
}
