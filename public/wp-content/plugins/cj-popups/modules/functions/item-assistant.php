<?php
function cjpopups_item_assistant(){
	global $cjpopups_assistant_steps;
	require_once(sprintf('%s/functions/assistant/messages.php', cjpopups_item_path('modules_dir')));

	$step_key = null;
	$cjpopups_assistant_step = get_option('cjpopups_assistant_step');
	if($cjpopups_assistant_step == ''){
		$step_key = 0;
		$step_msg = $cjpopups_assistant_steps[0]['text'];
		$step_callback = $cjpopups_assistant_steps[0]['callback'];
		$button_text = $cjpopups_assistant_steps[0]['button_text'];
		$cancel_text = $cjpopups_assistant_steps[0]['cancel_text'];
		$cancel_link = $cjpopups_assistant_steps[0]['cancel_link'];
	}else{
		$step = $cjpopups_assistant_step;
		if($step < count($cjpopups_assistant_steps)){
			$step_key = $step;
			$step_msg = $cjpopups_assistant_steps[$step]['text'];
			$step_callback = $cjpopups_assistant_steps[$step]['callback'];
			$button_text = $cjpopups_assistant_steps[$step]['button_text'];
			$cancel_text = $cjpopups_assistant_steps[$step]['cancel_text'];
			$cancel_link = $cjpopups_assistant_steps[$step]['cancel_link'];
			$step_back = $step_key - 2;
			$step_back_callback = @$cjpopups_assistant_steps[$step_back]['callback'];
			$back_link = cjpopups_assistant_url($step_back, $step_back_callback);
		}
	}



	if(!is_null($step_key)){
		$display[] = '<div id="cjpopups-item-assistant" class="assistant-panel">';
		$display[] = '<span class="header">Setup Assistant ~ '.cjpopups_item_info('item_name').'</span>';
		$display[] = '<span class="logo"><img src="'.cjpopups_item_path('framework_url').'/assets/admin/img/logo.png" width="48" /></span>';
		$display[] = $step_msg;
		$display[] = '<p class="buttons">';
		$display[] = '<a href="'.cjpopups_assistant_url($step_key, $step_callback).'" class="button-primary">'.$button_text.'</a>';
		if($step_key >= 2){
			$display[] = '<a href="'.$back_link.'" class="button-secondary">Go back</a>';
		}

		$display[] = '<a href="'.$cancel_link.'" class="button-secondary">'.$cancel_text.'</a>';
		$display[] = '</p>';
		$display[] = '</div>';
		echo implode('', $display);
	}
}

add_action('cj_assistant_hook', 'cjpopups_item_assistant');

// Add assistant menu item in drop down.
function cjpopups_assistant_menu_item(){
	echo '<li class="assistant-menu-item"><a href="#cjpopups-item-assistant" class="toggle-id">Help & Support</a>';
	echo '<ul>';
	echo '<li class="assistant-menu-item"><a href="'.cjpopups_assistant_url('start-over', cjpopups_callback_url('core_welcome')).'" class="toggle-id">Setup Assistant</a></li>';
	echo '<li><a target="_blank" href="'.cjpopups_item_info('quick_start_guide_url').'">Quick Start Guide</a></li>';
	echo '<li><a target="_blank" href="'.cjpopups_item_info('documentation_url').'">Documentation</a></li>';
	echo '<li><a target="_blank" href="'.cjpopups_item_info('support_forum_url').'">Support Forum</a></li>';
	echo '<li><a target="_blank" href="'.cjpopups_item_info('feature_request_url').'">Reques new features</a></li>';
	echo '<li><a target="_blank" href="'.cjpopups_item_info('report_bugs_url').'">Report Bugs & Issues</a></li>';
	echo '</ul>';
	echo '</li>';
}
add_action('cjpopups_dropdown_hook', 'cjpopups_assistant_menu_item');


function cjpopups_assistant_url($key, $callback){
	return cjpopups_string(cjpopups_current_url()).'cjpopups_complete_step='.$key.'&cjpopups_step_redirect='.urlencode($callback);
}


function cjpopups_assistant_process_steps(){
	if(isset($_REQUEST['cjpopups_complete_step']) && $_REQUEST['cjpopups_complete_step'] != ''){
		if($_REQUEST['cjpopups_complete_step'] == 'start-over'){
			delete_option('cjpopups_assistant_step');
			$location = urldecode($_REQUEST['cjpopups_step_redirect']);
			wp_redirect( $location );
			exit;
		}elseif($_REQUEST['cjpopups_complete_step'] == 'end-tour'){
			update_option('cjpopups_assistant_step', 100000);
			$location = urldecode($_REQUEST['cjpopups_step_redirect']);
			wp_redirect( $location );
			exit;
		}else{
			update_option('cjpopups_assistant_step', $_REQUEST['cjpopups_complete_step'] + 1);
			$location = urldecode($_REQUEST['cjpopups_step_redirect']);
			wp_redirect( $location );
			exit;
		}
	}
}
add_action('init', 'cjpopups_assistant_process_steps');




// Custom Assistant messages


function cjpopups_assistant_messages(){
	global $cjpopups_assistant_messages;
	require_once(sprintf('%s/functions/assistant/messages.php', cjpopups_item_path('modules_dir')));

	$case = basename($_SERVER['SCRIPT_NAME']);
	switch ($case) {
		case 'nav-menus.php':
			//echo @cjpopups_assistant_msg($cjpopups_assistant_messages['nav-menu.php']);
			break;

		default:
			# code...
			break;
	}
}

add_action('admin_footer', 'cjpopups_assistant_messages');