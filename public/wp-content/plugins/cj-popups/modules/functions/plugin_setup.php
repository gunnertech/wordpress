<?php
/**
 * Plugin Settings
 *
 * Setup plugin functionality, scripts and other functions.
 *
 * @author      Mohit Aneja - CSSJockey.com
 * @category    Framework
 * @package     CSSJockey/Framework
 * @version     1.0
 * @since       1.0
 */

global $wpdb, $current_user;

$backend_popups = cjpopups_get_option('show_on_backend');

# Plugin Styles & Scripts
####################################################################################################
add_action('wp_enqueue_scripts', 'cjpopups_plugin_scripts');
if($backend_popups == 'enable'){
	add_action('admin_enqueue_scripts', 'cjpopups_plugin_scripts');
}
function cjpopups_plugin_scripts(){
	if(cjpopups_get_option('disable_animate_css') != 'yes'){
		wp_enqueue_style('cjpopups-animate-css', cjpopups_item_path('item_url').'/assets/css/animate.css');
	}
	wp_enqueue_style('cjpopups-css', cjpopups_item_path('item_url').'/assets/css/cjpopups.css');
	wp_enqueue_script( 'cjpopups-js', cjpopups_item_path('item_url').'/assets/js/cjpopups.js', array('jquery'), null, false );
}

add_action('wp_footer', 'cjpopups_custom_scripts');
if($backend_popups == 'enable'){
	add_action('admin_footer', 'cjpopups_custom_scripts');
}
function cjpopups_custom_scripts(){
	$display[] = cjpopups_get_option('custom_css');
	$display[] = cjpopups_get_option('custom_js');
	echo implode("\n", $display);
}

# Plugin Styles & Scripts
####################################################################################################
function cjpopups_show(){

	global $post;

	$args = array(
		'posts_per_page' => 100000,
		'post_type' => 'popups',
		'post_status' => 'publish',
		'orderby' => 'post_date',
		'order' => 'DESC',
	);
	$all_popups = get_posts($args);

	$pops_to_display = null;

	$current_url = cjpopups_current_url(null, 'no');

	if(!empty($all_popups)){
		foreach ($all_popups as $key => $popup) {
			$show_on = get_post_meta($popup->ID, '_cjpopup_show_on', true);
			$urls = get_post_meta($popup->ID, '_cjpopup_urls', true);
			$cats = get_post_meta($popup->ID, '_cjpopup_cats', true);
			$urls_exclude = get_post_meta($popup->ID, '_cjpopup_urls_exclude', true);

			if($show_on == 'all'){
				$pops_to_display[$popup->ID] = $popup->ID;
			}

			// Specified Urls
			if($show_on != 'all' && $show_on != 'cats' && $urls != ''){
				$urls = explode("\n", $urls);
				foreach ($urls as $key => $value) {
					$current_url = esc_url_raw(trim($current_url));
					$current_url = str_replace('www.', '', $current_url);
					$match_url = esc_url_raw(trim($value));
					$match_url = str_replace('www.', '', $match_url);

					if(strcmp(trim($current_url), trim($match_url)) == 0){
						$pops_to_display[$popup->ID] = $popup->ID;
					}
				}
			}

			// Specified Categories
			if($show_on == 'cats'){
				foreach ($cats as $key => $cat) {
					if(isset($cats['is_category']) && $cats['is_category'] == 'yes'){
						if(is_category($cat) && get_query_var('cat') == $cat){
							$pops_to_display[$popup->ID] = $popup->ID;
						}
					}
					if(isset($cats['in_category']) && $cats['in_category'] == 'yes'){
						if(in_category($cat) && is_single()){
							$pops_to_display[$popup->ID] = $popup->ID;
						}
					}
				}
			}

			// Exclude Pop-up
			if($urls_exclude != ''){
				$urls_exclude = explode("\n", $urls_exclude);
				foreach ($urls_exclude as $key => $exurl) {
					$current_url = esc_url_raw(trim($current_url));
					$current_url = str_replace('www.', '', $current_url);
					$ex_match_url = esc_url_raw(trim($exurl));
					$ex_match_url = str_replace('www.', '', $ex_match_url);
					if(strcmp(trim($current_url), trim($ex_match_url)) == 0){
						unset($pops_to_display[$popup->ID]);
					}

				}
			}
		}
	}


	if(!empty($pops_to_display)){
		foreach ($pops_to_display as $key => $popup) {
			echo cjpopups_create_popup($popup);
		}
	}
}
add_action('wp_footer', 'cjpopups_show', 1);
if($backend_popups == 'enable'){
	add_action('admin_footer', 'cjpopups_show', 1);
}

function cjpopups_create_popup($post_id){

	global $current_user;
	get_currentuserinfo();

	$user_role = cjpopups_user_role($current_user->ID);

	$post = get_post($post_id);
	$popup_id = 'cjpopup-'.$post->ID;
	$popup_cookie_name = 'popup-'.$popup_id;
	$popup_html = get_post_meta($post->ID, '_cjpopup_html', true);

	if($popup_html != ''){
		$popup_content = '<div class="cjpopup-raw-html">'.do_shortcode($popup_html).'</div>';
	}else{
		$popup_content = do_shortcode(wpautop($post->post_content, true));
	}

	$popup_type = get_post_meta($post->ID, '_cjpopup_type', true);
	$popup_delay = get_post_meta($post->ID, '_cjpopup_delay', true);
	$disable_body_scroll = get_post_meta($post->ID, '_cjpopup_disable_body_scroll', true);
	$popup_padding = get_post_meta($post->ID, '_cjpopup_padding', true);
	$popup_width = get_post_meta($post->ID, '_cjpopup_width', true);
	$popup_height = get_post_meta($post->ID, '_cjpopup_height', true);
	$bgcolor = get_post_meta($post->ID, '_cjpopup_bgcolor', true);
	$textcolor = get_post_meta($post->ID, '_cjpopup_textcolor', true);
	$linkcolor = get_post_meta($post->ID, '_cjpopup_linkcolor', true);
	$closebgcolor = get_post_meta($post->ID, '_cjpopup_closebgcolor', true);
	$closetextcolor = get_post_meta($post->ID, '_cjpopup_closetextcolor', true);
	$delay = get_post_meta($post->ID, '_cjpopup_delay', true);
	$auto_hide = get_post_meta($post->ID, '_cjpopup_auto_hide', true);
	$hideclosebutton = get_post_meta($post->ID, '_cjpopup_hideclosebutton', true);
	$animation_in = get_post_meta($post->ID, '_cjpopup_animation_in', true);
	$animation_out = get_post_meta($post->ID, '_cjpopup_animation_out', true);
	$desktop_class = get_post_meta($post->ID, '_cjpopup_desktop_class', true);
	$tablet_class = get_post_meta($post->ID, '_cjpopup_tablet_class', true);
	$phone_class = get_post_meta($post->ID, '_cjpopup_phone_class', true);
	$user_roles = get_post_meta($post->ID, '_cjpopup_roles', true);
	$esc_key = get_post_meta($post->ID, '_cjpopup_esc_key', true);
	$anywhere_click_close = get_post_meta($post->ID, '_cjpopup_anywhere_click_close', true);
	$close_text = get_post_meta($post->ID, '_cjpopup_close_text', true);
	$close_text = ($close_text == '') ? 'x' : $close_text;
	$right_click = get_post_meta($post->ID, '_cjpopup_right_click', true);
	$backdropcolor = get_post_meta($post->ID, '_cjpopup_backdropcolor', true);
	$backdropopacity = get_post_meta($post->ID, '_cjpopup_backdropopacity', true);
	$usermeta = get_post_meta($post->ID, '_cjpopup_usermeta', true);


	$desktop_class = ($desktop_class == 'on') ? 'cjpopup-no-desktop' : '';
	$tablet_class = ($tablet_class == 'on') ? 'cjpopup-no-tablet' : '';
	$phone_class = ($phone_class == 'on') ? 'cjpopup-no-phone' : '';

	$popup_cookie_days = get_post_meta($post->ID, '_cjpopup_cookie_expire', true);

	if($popup_cookie_days > 0){
		$popup_cookie_days = get_post_meta($post->ID, '_cjpopup_cookie_expire', true);
	}else{
		$popup_cookie_days = 0;
	}

	$popup_start_date = get_post_meta($post->ID, '_cjpopup_start_date', true);
	$popup_end_date = get_post_meta($post->ID, '_cjpopup_end_date', true);
	$popup_click_open = get_post_meta($post->ID, '_cjpopup_click', true);
	$popup_click_close = get_post_meta($post->ID, '_cjpopup_close_click', true);


	$backdrop = get_post_meta($post->ID, '_cjpopup_backdrop', true);
	$padding = trim(str_replace('px', '', $popup_padding)).'px';

	$default_width = array(
		'box-top-right' => '400px',
		'box-top-left' => '400px',
		'box-bottom-left' => '400px',
		'box-bottom-right' => '400px',
		'modal-box' => '600px',
	);

	if($popup_type == 'top-bar' || $popup_type == 'bottom-bar'){
		$width = '100%';
	}else{
		if($popup_width != '' && $popup_width != 0){
			$width = $popup_width;
		}else{
			$width = $default_width[$popup_type];
		}
	}
	$click = ($popup_click_open != '') ? $popup_click_open : '';
	if($right_click == 'yes'){
		$click = '';
	}

	$display[] = '<div id="popup-'.$popup_id.'" class="cjpopup '.$tablet_class.' '.$phone_class.' '.$desktop_class.' cjpopup-hidden cjpopup-'.$popup_type.'" data-close-click="'.$popup_click_close.'" data-click="'.$click.'" data-right-click="'.$right_click.'" data-backdrop-id="backdrop-'.$popup_id.'" data-cookie="'.$popup_cookie_days.'" data-popup-type="'.$popup_type.'" data-width="'.$width.'" data-height="'.$popup_height.'" data-padding="'.$padding.'" data-delay="'.$delay.'" data-autohide="'.$auto_hide.'" data-hideclosebutton="'.$hideclosebutton.'" data-bgcolor="'.$bgcolor.'" data-textcolor="'.$textcolor.'" data-linkcolor="'.$linkcolor.'" data-closebgcolor="'.$closebgcolor.'" data-closetextcolor="'.$closetextcolor.'" data-animation-in="'.$animation_in.'" data-animation-out="'.$animation_out.'" data-esc-key="'.$esc_key.'" data-anywhere-click-close="'.$anywhere_click_close.'" data-disable-body-scroll="'.$disable_body_scroll.'">';
	$display[] = '<div class="popup-content">';
	$display[] = $popup_content;
	$display[] = '</div>';
	$display[] = '<a href="#close" class="close-cjpopup"><span>'.$close_text.'</span></a>';
	$display[] = '</div>';

	if($backdrop == 'yes'){
		list($r, $g, $b) = sscanf($backdropcolor, "#%02x%02x%02x");
		$backdropopacity = $backdropopacity / 100;
		$backdrop_style = 'style="background-color: rgba('.$r.','.$g.','.$b.','.$backdropopacity.')"';
		$display[] = '<div id="backdrop-'.$popup_id.'" class="cjbackdrop '.$tablet_class.' '.$phone_class.' '.$desktop_class.' cjpopup-hidden" '.$backdrop_style.'></div>';
	}


	if($popup_start_date > 0 && $popup_end_date > 0){
		$now = time();
		if($now > $popup_start_date && $now < $popup_end_date){
			$time_test = 'pass';
		}else{
			$time_test = 'fail';
		}
	}else{
		$time_test = 'pass';
	}

	if(is_array($user_roles)){
		if(in_array($user_role, $user_roles)){
			$role_test = 'pass';
		}else{
			$role_test = 'fail';
		}
	}else{
		$role_test = 'pass';
	}

	/*if($user_role == 'administrator'){
		$role_test = 'pass';
	}*/


	if(is_array($usermeta) && is_user_logged_in()){
		if($usermeta['metakey'] != 'none' && $usermeta['metakey'] != ''){
			$user_meta_key = $usermeta['metakey'];
			$user_meta_value = $usermeta['metavalue'];
			$usermetavalue_saved = get_user_meta($current_user->ID, $user_meta_key, true);
			if($usermetavalue_saved != $user_meta_value){
				$role_test = 'fail';
			}
		}
	}

	if(!isset($_COOKIE[$popup_cookie_name]) && $time_test == 'pass' && $role_test == 'pass'){
		return implode('', $display);
	}

}









