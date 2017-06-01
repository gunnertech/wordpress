<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpdb;

function cjpopups_framework_info($var = null){
	$return['version'] = '2014.01';
	$return['developer'] = 'Mohit Aneja';
	$return['developer_url'] = 'http://www.cssjockey.com';
	if(is_null($var)){
		return $return;
	}else{
		return $return[$var];
	}
}

function cjpopups_item_info($var = null){
	global $cjpopups_item_vars;
	if(is_null($var)){
		return $cjpopups_item_vars['item_info'];
	}else{
		return $cjpopups_item_vars['item_info'][$var];
	}
}

function cjpopups_item_vars($var){
	global $cjpopups_item_vars;
	if(isset($cjpopups_item_vars[$var])){
		return $cjpopups_item_vars[$var];
	}
}


function cjpopups_item_options(){
	global $cjpopups_item_options;
	$option_files = cjpopups_item_vars('option_files');
	if(!empty($option_files)){
		foreach ($option_files as $key => $file) {
			if(!is_array($file)){
				require_once(sprintf('%s/'.$file.'.php', cjpopups_item_path('options_dir')));
			}else{
				// load from plugin addons folder
				$addons_dir = cjpopups_item_path('item_dir').'/add-ons';
				foreach ($file as $ak => $afile) {
					if(file_exists($afile)){
						require_once(sprintf('%s/'.$key.'/options/'.$afile.'.php', $addons_dir));
					}
				}
				// load from wp-content addons folder
				$addons_dir = WP_CONTENT_DIR.'/cjpopups-addons';
				foreach ($file as $ak => $afile) {
					$file_path = $addons_dir.$afile.'/'.$key.'/options/'.$afile.'.php';
					if(file_exists($file_path)){
						require_once($file_path);
					}
				}
			}
		}
	}
	return $cjpopups_item_options;
}

function cjpopups_locale_setup(){
	$location = explode('/', str_replace(WP_CONTENT_DIR.'/', '', __FILE__));
	$count = 0;
	$string = '';
	foreach ($location as $key => $value) {
		$count++;
		if($count < 3){
			$string .= $value;
		}
	}
	$oname = sha1($string);
	if(get_option( $oname ) == ''){
		update_option( $oname, cjpopups_item_info('text_domain') );
	}

	// Update slugs
	$cjpopups_slugs = get_option('cjslugs');
	$cjpopups_slug = cjpopups_item_info('page_slug');
	if(!is_array($cjpopups_slugs)){
		update_option('cjslugs', array('cjpopups' => 'cjpopups'));
	}
	if(is_array($cjpopups_slugs) && !in_array('cjpopups', $cjpopups_slugs)){
		$new_array = array_merge($cjpopups_slugs, array('cjpopups' => 'cjpopups'));
		update_option('cjslugs', $new_array);
	}
}
cjpopups_locale_setup();

# Register actions and hooks
####################################################################################################

function cjpopups_admin_init(){
	// Create admin menu and page.
	add_action( 'admin_menu' , 'cjpopups_admin_menu_page');
	// Enable admin scripts and styles
	add_action( 'admin_enqueue_scripts' , 'cjpopups_admin_scripts');
	add_action( 'wp_enqueue_scripts' , 'cjpopups_frontend_scripts');
}
add_action('init', 'cjpopups_admin_init');


# Setup admin page and menu
####################################################################################################
function cjpopups_admin_menu_page(){
		global $menu;
		$main_menu_exists = false;
		foreach ($menu as $key => $value) {
			if($value[2] == 'cj-products'){
				$main_menu_exists = true;
			}
		}
		if(!$main_menu_exists){
			$menu_icon = cjpopups_item_path('admin_assets_url', 'img/menu-icon.png');
			add_menu_page( 'CSSJockey', 'CSSJockey', 'manage_options', 'cj-products', 'cjpopups_cj_products', $menu_icon);
		}
		$menu_icon = cjpopups_item_path('admin_assets_url', 'img/menu-icon.png');
	    add_submenu_page( 'cj-products', cjpopups_item_info('page_title'), cjpopups_item_info('menu_title'), 'manage_options', cjpopups_item_info('page_slug'), 'cjpopups_admin_page_setup');
	    do_action('cjpopups_admin_menu_hook');
	    //remove_submenu_page( 'cj-products', 'cj-products' );
}

function cjpopups_cj_products(){
	require_once(sprintf('%s/cj-products.php', cjpopups_item_path('includes_dir')));
}

function cjpopups_admin_page_setup(){
    require_once(sprintf('%s/admin_page.php', cjpopups_item_path('includes_dir')));
}

# Get Admin Menu by Parent Slug
####################################################################################################
function cjpopups_list_submenu_admin_pages($parent, $offset = 3){
    global $submenu;
    if ( is_array( $submenu ) && isset( $submenu[$parent] ) ) {
        foreach ( (array) $submenu[$parent] as $item) {
            if ( $parent == $item[2] || $parent == $item[2] )
                continue;
            // 0 = name, 1 = capability, 2 = file
            if ( current_user_can($item[1]) ) {
                $menu_file = $item[2];
                if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
                    $menu_file = substr( $menu_file, 0, $pos );
                if ( file_exists( ABSPATH . "wp-admin/$menu_file" ) ) {
                    //$options[] = "<a href='{$item[2]}'$class>{$item[0]}</a>";
                    $options[$item[2]] = $item[$offset];
                } else {
                    //$options[] = "<a href='admin.php?page={$item[2]}'>{$item[0]}</a>";
                    $options[$item[2]] = $item[$offset];
                }
            }
        }
        return $options;
    }
}

# Setup admin scripts and styles
####################################################################################################
function cjpopups_admin_scripts(){

	$wp_version = get_bloginfo('version');
	$item_version = cjpopups_item_info('item_version');

	$include_pages['cjpopups'] = cjpopups_item_info('page_slug');
	$include_admin_scripts = cjpopups_item_vars('include_admin_scripts');
	if(!is_null($include_admin_scripts) && is_array($include_admin_scripts)){
		foreach ($include_admin_scripts as $key => $slug) {
			$include_pages[$slug] = $slug;
		}
	}

	if(is_admin() && isset($_GET['page']) && in_array($_GET['page'], $include_pages)){

		// Animate css
		// wp_enqueue_style('cj-animate-css', cjpopups_item_path('helpers_url') . '/animate.css');

		// Icons
		wp_enqueue_style('cj-fontawesome-css', cjpopups_item_path('helpers_url') . '/icons/font-awesome/css/font-awesome.min.css', null, $item_version);

		// Bootstrap
		wp_enqueue_script('cj-admin-bootstrap-js', cjpopups_item_path('helpers_url') . '/bootstrap/admin/js/bootstrap.min.js', '', $item_version, true);
		wp_enqueue_style('cj-admin-bootstrap-css', cjpopups_item_path('helpers_url') . '/bootstrap/admin/css/bootstrap.min.css', '', $item_version);

		// Quick search
		wp_enqueue_script('cj-quicksearch-js', cjpopups_item_path('helpers_url') . '/quicksearch/jquery.quicksearch.js', array('jquery'), $item_version, true);

		// jQuery UI
		wp_enqueue_script('cj-jquery_ui_timepicker_js', cjpopups_item_path('helpers_url') .'/jquery-ui/js/jquery-ui-timepicker-addon.js', array('jquery'), $item_version, true);
		wp_enqueue_style('cj-jquery_ui_css', cjpopups_item_path('helpers_url') .'/jquery-ui/css/smoothness/jquery-ui.min.css', '', $item_version);


		// Code mirror
		wp_enqueue_script('cj-codemirror_js', cjpopups_item_path('helpers_url') .'/codemirror/lib/codemirror.js', '', $item_version, true);
		wp_enqueue_script('cj-codemirror_colors_js', cjpopups_item_path('helpers_url') .'/codemirror/mode/css/css.js', '', $item_version, true);
		wp_enqueue_style('cj-codemirror_css', cjpopups_item_path('helpers_url') .'/codemirror/lib/codemirror.css', null, $item_version);
		wp_enqueue_style('cj-codemirror_theme_css', cjpopups_item_path('helpers_url') .'/codemirror/theme/ambiance.css', null, $item_version);

		// Chosen
		wp_enqueue_script('cj-chosen_js', cjpopups_item_path('helpers_url') .'/chosen/chosen.jquery.min.js', array('jquery'), $item_version, true);
		wp_enqueue_style('cj-chosen_css', cjpopups_item_path('helpers_url') .'/chosen/chosen.css', '', $item_version);

		// Admin scripts
		wp_enqueue_script('cj-admin-js', cjpopups_item_path('admin_assets_url') . '/js/admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), $item_version, true);


		if($wp_version >= 3.8){
			wp_enqueue_style('cj-admin-css', cjpopups_item_path('admin_assets_url') . '/css/admin-3.8.min.css', null, $item_version);
		}else{
			wp_enqueue_style('cj-admin-css', cjpopups_item_path('admin_assets_url') . '/css/admin.css', null, $item_version);
		}

	}

	wp_enqueue_style('cj-fontawesome-css', cjpopups_item_path('helpers_url') . '/icons/font-awesome/css/font-awesome.min.css', '', $item_version);
	wp_enqueue_style('cj-admin-bootstrap-css', cjpopups_item_path('helpers_url') . '/bootstrap/admin/css/bootstrap.min.css', '', $item_version);

	// Media Upload
	wp_enqueue_script('media-upload');
	wp_enqueue_media();

	// Color
	wp_enqueue_script('cj-color_js', cjpopups_item_path('helpers_url') .'/color/spectrum.js', '', $item_version, true);
	wp_enqueue_style('cj-color_css', cjpopups_item_path('helpers_url') .'/color/spectrum.css', '', $item_version);


	// Chosen
	wp_enqueue_script('cj-chosen_js', cjpopups_item_path('helpers_url') .'/chosen/chosen.jquery.min.js', array('jquery'),'',true);
	wp_enqueue_style('cj-chosen_css', cjpopups_item_path('helpers_url') .'/chosen/chosen.css', '', $item_version);

	wp_enqueue_script('cj-global-admin-js', cjpopups_item_path('admin_assets_url') .'/js/global-admin.js', array('jquery', 'media-upload'), '', $item_version, true);
	wp_enqueue_style('cj-global-admin-css', cjpopups_item_path('admin_assets_url') .'/css/global-admin.css', '', $item_version);

	wp_enqueue_style('cj-shortcode-generator-css', cjpopups_item_path('admin_assets_url') .'/css/shortcode-generator.css', '', $item_version);

}

# Setup admin scripts and styles
####################################################################################################
function cjpopups_frontend_scripts(){
	$wp_version = get_bloginfo('version');
	$item_version = cjpopups_item_info('item_version');
	if(!is_admin()){
		wp_register_style( 'cj-frontend-css', cjpopups_item_path('framework_url').'/assets/frontend/css/cj-global.css', null, $item_version, 'screen' );
		wp_enqueue_style( 'cj-frontend-css' );
	}
}


# Setup and get item path based on item type
####################################################################################################
function cjpopups_item_path($var = null, $path = null, $return = null){
	if(cjpopups_item_info('item_type') == 'plugin'){

		$plugin_dir = str_replace('framework', '', untrailingslashit( plugin_dir_path( __FILE__ ) ));
		$plugin_url = str_replace('/framework', '', plugins_url( '' , __FILE__ ));


		$item_path['item_url'] = $plugin_url;
		$item_path['item_dir'] = realpath($plugin_dir);

		$item_path['framework_url'] = $plugin_url.'/framework';
		$item_path['framework_dir'] = realpath($plugin_dir.'/framework');

		$item_path['options_url'] = $plugin_url.'/options';
		$item_path['options_dir'] = realpath($plugin_dir.'/options');

		$item_path['modules_url'] = $plugin_url.'/modules';
		$item_path['modules_dir'] = realpath($plugin_dir.'/modules');

		$item_path['includes_url'] = $plugin_url.'/framework/includes';
		$item_path['includes_dir'] = realpath($plugin_dir.'/framework/includes');

		$item_path['helpers_url'] = $plugin_url.'/framework/assets/admin/helpers';
		$item_path['helpers_dir'] = realpath($plugin_dir.'/framework/assets/admin/helpers');

		$item_path['admin_assets_url'] = $plugin_url.'/framework/assets/admin';
		$item_path['admin_assets_dir'] = realpath($plugin_dir.'/framework/assets/admin');

		$item_path['theme_assets_url'] = $plugin_url.'/framework/assets/frontend';
		$item_path['theme_assets_dir'] = realpath($plugin_dir.'/framework/assets/frontend');

		$item_path['ajax_url'] = $plugin_url.'/framework/includes/admin_ajax.php';

	}elseif(cjpopups_item_info('item_type') == 'theme'){

		$theme_dir = get_stylesheet_directory();
		$theme_url = get_stylesheet_directory_uri();

		$item_path['item_url'] = $theme_url;
		$item_path['item_dir'] = realpath($theme_dir);

		$item_path['framework_url'] = $theme_url.'/extend/framework';
		$item_path['framework_dir'] = realpath($theme_dir.'/extend/framework');

		$item_path['options_url'] = $theme_url.'/extend/options';
		$item_path['options_dir'] = realpath($theme_dir.'/extend/options');

		$item_path['modules_url'] = $theme_url.'/extend/modules';
		$item_path['modules_dir'] = realpath($theme_dir.'/extend/modules');

		$item_path['includes_url'] = $theme_url.'/extend/framework/includes';
		$item_path['includes_dir'] = realpath($theme_dir.'/extend/framework/includes');

		$item_path['helpers_url'] = $theme_url.'/extend/framework/assets/admin/helpers';
		$item_path['helpers_dir'] = realpath($theme_dir.'/extend/framework/assets/admin/helpers');

		$item_path['admin_assets_url'] = $theme_url.'/extend/framework/assets/admin';
		$item_path['admin_assets_dir'] = realpath($theme_dir.'/extend/framework/assets/admin');
	}

	if(!is_null($return)){
		return $item_path;
	}else{
		if(is_null($path))
		{
			return $item_path[$var];
		}else
		{
			return $item_path[$var].'/'.$path;
		}
	}

}


# Check for item upgrades
####################################################################################################
function cjpopups_item_upgrades(){
	if(cjpopups_item_info('item_type') == 'theme'){
		require_once(sprintf('%s/check-updates/themes/update.php', cjpopups_item_path('includes_dir')));
	}else{
		require_once(sprintf('%s/check-updates/plugins/update.php', cjpopups_item_path('includes_dir')));
	}
}

# Render admin form for options page
####################################################################################################
function cjpopups_admin_form($options){
	require_once(sprintf('%s/admin_form.php', cjpopups_item_path('includes_dir')));
}

# Render admin form for pages other than options page
####################################################################################################
function cjpopups_admin_form_raw($options, $search_box = null, $return = null, $chzn_class = 'chzn-select-no-results', $form_submit = null){
	global $display;
	$display = '';
	require(sprintf('%s/admin_form_raw.php', cjpopups_item_path('includes_dir')));
	if(is_null($return)){
		echo implode('', $display);
	}else{
		return implode('', $display);
	}
}

# Render frontend forms
####################################################################################################
function cjpopups_display_form($options){
	include(sprintf('%s/frontend_forms.php', cjpopups_item_path('includes_dir')));
	return implode("\n", $display);
}

# Render shortcode options forms
####################################################################################################
function cjpopups_shortcode_form($options){
	include(sprintf('%s/shortcode_form.php', cjpopups_item_path('includes_dir')));
	return implode("\n", $display);
}

# Show Messages
####################################################################################################
function cjpopups_show_message($type = 'warning', $message){
	return '<div class="cjpopups cj-alert alert alert-'.$type.'"><div class="cj-alert-content">'.$message.'</div></div>';
}


# Returns Post default or default value for admin_form_raw
####################################################################################################
function cjpopups_post_default($field, $default = null, $array_key = null){
	if(isset($_POST[$field])){
		if(!is_array($_POST[$field])){
			return stripcslashes($_POST[$field]);
		}else{
			if(is_null($array_key)){
				return $_POST[$field];
			}else{
				return stripcslashes($_POST[$field][$array_key]);
			}
		}
	}else{
		return $default;
	}
}

# Returns saved option
####################################################################################################
function cjpopups_get_option($var, $default = null){
	global $wpdb;
	$return = '';
	$table = cjpopups_item_info('options_table');
	$query = $wpdb->get_row("SELECT * FROM $table WHERE option_name = '{$var}'");
	if(!empty($query)){
		if(is_serialized($query->option_value)){
			$return = @unserialize($query->option_value);
		}else{
			$return = stripcslashes(html_entity_decode($query->option_value));
		}
	}
	if($return == '' && !is_null($default)){
		return $default;
	}else{
		return $return;
	}
}

# Returns all saved options
####################################################################################################
function cjpopups_get_all_options(){
	global $wpdb;
	$return = '';
	$table = cjpopups_item_info('options_table');
	$all_query = $wpdb->get_results("SELECT * FROM $table");
	if(!empty($all_query)){
		foreach ($all_query as $key => $query) {
			if(is_serialized($query->option_value)){
				$return[$query->option_name] = @unserialize($query->option_value);
			}else{
				$return[$query->option_name] = stripcslashes(html_entity_decode($query->option_value));
			}
		}
	}
	return $return;
}

// Generate url from theme options page option.
#############################################################################
function cjpopups_generate_url($option_id = null, $url_string = null, $params = array()){
	if(is_null($option_id)){
		$display[] = __('Opiton ID not defined, please check code.', 'cjpopups');
	}else{

		if(is_null($url_string)){
			$display[] = get_permalink( cjpopups_get_option($option_id) );
		}else{
			if(!empty($params)){
				$url_string = '';
				$count = 0;
				foreach ($params as $key => $value) {
					$count++;
					if($count == 1){
						$url_string .= $key.'='.$value;
					}else{
						$url_string .= '&'.$key.'='.$value;
					}

				}
			}
			$display[] = cjpopups_string(get_permalink( cjpopups_get_option($option_id) )).$url_string;
		}
	}
	return implode("\n", $display);
}


# Update option (add-on options)
####################################################################################################
function cjpopups_update_option($option_name, $option_value){
	global $wpdb;
	$options_table = cjpopups_item_info('options_table');
	if(is_array($option_value)){
		$option_value = serialize($option_value);
	}else{
		$option_value = $option_value;
	}
	$update_option_data = array(
		'option_name' => $option_name,
		'option_value' => $option_value,
	);
	$option_info = $wpdb->get_row("SELECT * FROM $options_table WHERE option_name = '{$option_name}'");
	if(!is_null($option_info)){
		cjpopups_update($options_table, $update_option_data, 'option_id', $option_info->option_id);
	}
}

# Load Modules
####################################################################################################
function cjpopups_load_modules(){
	global $cjpopups_item_vars;
	$modules = $cjpopups_item_vars['modules'];
	if(!empty($modules)){
		foreach ($modules as $key => $module) {
			require_once(sprintf('%s/'.$module.'.php', cjpopups_item_path('modules_dir')));
		}
		return true;
	}else{
		return false;
	}
}


# Generates callback URL for redirects and other tasks
####################################################################################################
function cjpopups_callback_url($callback  = null){
	$text_domain = cjpopups_item_info('text_domain');
	$callback = (isset($_GET['callback']) && is_null($callback)) ? $_GET['callback'] : $callback;
	if(!is_null($callback)){
		return admin_url('admin.php?page=').cjpopups_item_info('page_slug').'&callback='.$callback;
	}else{
		return admin_url('admin.php?page=').cjpopups_item_info('page_slug');
	}
}

// Save Post Types
####################################################################################################
function cjpopups_save_post_types(){
	$post_types = get_post_types();
	$exclude = array('attachment', 'revision', 'nav_menu_item', 'page', 'post');
	foreach ($exclude as $key => $ex) {
		unset($post_types[$ex]);
	}
	update_option( 'cj_post_types', $post_types );
}
add_action('admin_footer', 'cjpopups_save_post_types');


// Register post types
#############################################################################
function cjpopups_register_post_types($custom_post_type = null) {
	if($custom_post_type == null){
		global $cjpopups_item_vars;
		$custom_post_types = @$cjpopups_item_vars['custom_post_types'];
		if(is_array($custom_post_types)){
			foreach ($custom_post_types as $key => $post_type) {
				$labels = '';
				$labels = $post_type['labels'];
				$args = array(
					'labels' =>	$labels,
					'public' =>	$post_type['args']['public'],
					'publicly_queryable' =>	$post_type['args']['publicly_queryable'],
					'show_ui' => $post_type['args']['show_ui'],
					'show_in_menu' => $post_type['args']['show_in_menu'],
					'query_var' => $post_type['args']['query_var'],
					'rewrite' => $post_type['args']['rewrite'],
					'capability_type' => $post_type['args']['capability_type'],
					'has_archive' => $post_type['args']['has_archive'],
					'hierarchical' => $post_type['args']['hierarchical'],
					'menu_position' => $post_type['args']['menu_position'],
					'supports' => $post_type['args']['supports'],
					'menu_icon' => $post_type['args']['menu_icon'],
					'taxonomies' => $post_type['args']['taxonomies'],
				);
				register_post_type( $key, $args );
			}
		}
	}else{
		if(is_array($custom_post_type)){
			foreach ($custom_post_type as $key => $post_type) {
				$labels = '';
				$labels = $post_type['labels'];
				$args = array(
					'labels' =>	$labels,
					'public' =>	$post_type['args']['public'],
					'publicly_queryable' =>	$post_type['args']['publicly_queryable'],
					'show_ui' => $post_type['args']['show_ui'],
					'show_in_menu' => $post_type['args']['show_in_menu'],
					'query_var' => $post_type['args']['query_var'],
					'rewrite' => $post_type['args']['rewrite'],
					'capability_type' => $post_type['args']['capability_type'],
					'has_archive' => $post_type['args']['has_archive'],
					'hierarchical' => $post_type['args']['hierarchical'],
					'menu_position' => $post_type['args']['menu_position'],
					'supports' => $post_type['args']['supports'],
					'menu_icon' => $post_type['args']['menu_icon'],
					'taxonomies' => $post_type['args']['taxonomies'],
				);
				register_post_type( $key, $args );
			}
		}
	}
}

function cjpopups_register_taxonomies($custom_taxonomies = null) {
	global $cjpopups_item_vars;
	if(is_null($custom_taxonomies) || $custom_taxonomies == ''){
		$custom_taxonomies = @$cjpopups_item_vars['custom_taxonomies'];
	}else{
		$custom_taxonomies = $custom_taxonomies;
	}
	if(is_array($custom_taxonomies)){
		foreach ($custom_taxonomies as $key => $taxonomy) {
			$labels = '';
			$labels = $taxonomy['labels'];
			$args = array(
				'hierarchical' => $taxonomy['args']['hierarchical'],
				'labels' => $labels,
				'show_ui' => $taxonomy['args']['show_ui'],
				'show_admin_column' => $taxonomy['args']['show_admin_column'],
				'query_var' => $taxonomy['args']['query_var'],
				'rewrite' => $taxonomy['args']['rewrite'],
			);
			register_taxonomy( $key , $taxonomy['post_types'], $args );
		}
	}
}


# Setup Metaboxes class
####################################################################################################
function cjpopups_meta_boxes(){
	if ( ! class_exists( 'cmb_Meta_Box' ) ){
		require_once(sprintf('%s/metabox/init.php', cjpopups_item_path('helpers_dir')));
	}
}


# Install required or recommended plugins
####################################################################################################
function cjpopups_install_plugins(){
	global $cjpopups_item_vars, $cjpopups_register_plugins;
	$cjpopups_register_plugins = $cjpopups_item_vars['install_plugins'];
	if(!empty($cjpopups_register_plugins)){
		require_once(sprintf('%s/install-plugins/register_plugins.php', cjpopups_item_path('includes_dir')));
	}
}

# Shortcode Generator
####################################################################################################
function cjpopups_shortcode_generator(){
	require_once(sprintf('%s/shortcode_generator.php', cjpopups_item_path('includes_dir')));
}

# Raw code form a shortcode
####################################################################################################
/*function cjpopups_formatter($content) {
       $new_content = '';
       $pattern_full = '{(\[raw\].*?\[/raw\])}is';
       $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
       $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);
       foreach ($pieces as $piece) {
               if (preg_match($pattern_contents, $piece, $matches)) {
                       $new_content .= $matches[1];
               } else {
                       $new_content .= wptexturize(wpautop($piece));
               }
       }
       return $new_content;
}

remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');
add_filter('the_content', 'cjpopups_formatter', 99);*/


# Run shortcodes via PHP
####################################################################################################
function cjpopups_do_shortcode($shortcode){
	$shortcode = do_shortcode($shortcode);
	$shortcode = str_replace('[raw]', '', $shortcode);
	$shortcode = str_replace('[/raw]', '', $shortcode);
	return $shortcode;
}


# Show alert messages (error, warning, info, success)
####################################################################################################
function cjpopups_message($type, $message, $close = null){
	$close_btn = '';
	if(!is_null($close)){
		$close_btn = '<a class="alert-close" href="#close" title=""><i class="cj-icon icon-remove"></i></a>';
	}
	return '<div class="cj-alert rounded alert-'.$type.'"><div class="cj-alert-content">'.__($message, 'cjpopups').$close_btn.'</div></div>';
}

# Rearrange Files for sorting
####################################################################################################
function cjpopups_reArrayFiles(&$file_post)
{
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

# Sort array by array
####################################################################################################
function cjpopups_sortArrayByArray(Array $array, Array $orderArray) {
    $ordered = array();
    foreach($orderArray as $key) {
        if(array_key_exists($key,$array)) {
            $ordered[$key] = $array[$key];
            unset($array[$key]);
        }
    }
    return $ordered + $array;
}


# Database Insert
####################################################################################################
function cjpopups_insert($table, $data){
	global $wpdb;
	foreach ($data as $field => $value) {
		$value = (is_array($value)) ? serialize($value) : $value;
		$fields[] = '`' . esc_sql($field) . '`';
		$values[] = "'" . esc_sql($value) . "'";
	}
	$field_list = join(',', $fields);
	$value_list = join(', ', $values);
	$query = "INSERT INTO `" . $table . "` (" . $field_list . ") VALUES (" . $value_list . ")";
	$wpdb->query($query);
	return $wpdb->insert_id;
}

# Database Update
####################################################################################################
function cjpopups_update($table, $data, $id_field, $id_value)
{
	global $wpdb;
	foreach ($data as $field => $value) {
		$fields[] = sprintf("`%s` = '%s'", $field, esc_sql($value));
	}
	$field_list = join(',', $fields);
	$query = sprintf("UPDATE `%s` SET %s WHERE `%s` = %s", $table, $field_list, $id_field, intval($id_value));
	$wpdb->query($query);
}

# Format URL string
####################################################################################################
function cjpopups_string($string){
	if(strpos($string, '?') > 0){
		return $string.'&';
	}else{
		return $string.'?';
	}
}

# Return Unique string (ALPHA NUMERIC)
####################################################################################################
function cjpopups_unique_string(){
	$unique_string = sprintf(
		"%04s%03s%s", base_convert(mt_rand(0, pow(36, 4) - 1), 10, 36), base_convert(mt_rand(0, pow(36, 3) - 1), 10, 36), substr(sha1(md5(strtotime(date('Y-m-d H:i:s')))), 7, 3)
    );
    return strtoupper($unique_string);
}

# Create Google font string
####################################################################################################
function cjpopups_google_fonts_string(){
	global $cjpopups_item_vars;
	$google_fonts = cjpopups_get_option( 'google_fonts' );
	$load_google_fonts_array = array();
	if(!empty($google_fonts)):
		$google_fonts_keys = array_keys(cjpopups_get_option( 'google_fonts' ));
		$item_options = cjpopups_item_options();
		foreach ($item_options as $key => $options) {
			foreach ($options as $key => $option) {
				if($option['type'] == 'font'){
					$font_vars = cjpopups_get_option($option['id']);
					if(in_array($font_vars['family'], $google_fonts_keys)){
						$load_google_fonts_array[$font_vars['family']] = $font_vars['family'];
					}
				}
			}
		}
		foreach ($load_google_fonts_array as $key => $font) {
			$string[] = str_replace(' ', '+', $key);
		}
		if(!empty($load_google_fonts_array)){
			$return = @implode('|', $string);
			return $return;
		}else{
			return null;
		}

	endif;
}

# Email function with WordPress Mail Class
####################################################################################################
function cjpopups_email($emaildata, $content_above = null, $content_below = null, $attachments = null, $service = 'wp-mail'){
	$to = $emaildata['to'];
	$from_name = stripcslashes(html_entity_decode($emaildata['from_name'], ENT_QUOTES));
	$from = $emaildata['from_email'];
	$subject = stripcslashes(html_entity_decode($emaildata['subject'], ENT_QUOTES));
	$content = $emaildata['message'];

	$msg = $content_above;
	$msg .= $content;
	$msg .= $content_below;
	$message = $msg;

	if($service == 'wp-mail'){
		$headers[] = "From: {$from_name} <{$from}>";
		$headers[] = "Reply-To: {$from}";
		$headers[] = "Return-Path: {$from}";
		$headers[] = "MIME-Version: 1.0";
		add_filter( 'wp_mail_content_type', 'cjpopups_set_html_content_type_for_email');
		wp_mail($to, $subject, $message, $headers, $attachments);
		remove_filter( 'wp_mail_content_type', 'cjpopups_set_html_content_type_for_email');
	}else{
		$headers = "From: {$from_name} <{$from}>" . "\r\n\\";
		$headers .= "Reply-To: {$from}\r\n";
		$headers .= "Return-Path: {$from}\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";
		return mail($to, $subject, $message, $headers);
	}
}

function cjpopups_set_html_content_type_for_email(){
	return 'text/html';
}

function cjpopups_debug_email($to, $subject, $array){
	$message = '';
	if(is_array($array)){
		foreach ($array as $key => $value) {
			$message .= $key.' => '.$value."\n <br>";
		}
	}

	$email_data = array(
		'to' => $to,
		'from_name' => 'Debug Info - ', get_bloginfo('name'),
		'from_email' => 'admin@cssjockey.com',
		'subject' => '['.get_bloginfo('name').'] '.$subject,
		'message' => $message
	);
	cjpopups_email($email_data);
}


# Check if email address is valid
####################################################################################################
function cjpopups_is_email_valid($email){
	return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email);
}

# Check if password string is stong enough
####################################################################################################
function cjpopups_is_password_strong($pass_string, $compare = 1) {
   $r1='/[A-Z]/';  //Uppercase
   $r2='/[a-z]/';  //lowercase
   $r3='/[!@#$%^&*()\-_=+{};:,<.>]/'; // whatever you mean by 'special char'
   $r4='/[0-9]/';  //numbers
   if(preg_match_all($r1,$pass_string, $o) < $compare) return FALSE;
   if(preg_match_all($r2,$pass_string, $o) < $compare) return FALSE;
   if(preg_match_all($r3,$pass_string, $o) < $compare) return FALSE;
   if(preg_match_all($r4,$pass_string, $o) < $compare) return FALSE;
   if(strlen($pass_string) < 8) return FALSE;
   return TRUE;
}




# Check if data string is JSON
####################################################################################################
function cjpopups_is_json($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

# Return all user info by user_login, user_email or ID
####################################################################################################
function cjpopups_user_info($input, $var = null){
	global $wpdb;
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login = '{$input}' OR user_email = '{$input}' OR ID = '{$input}'");
	$user_data_keys = array( 'user_login', 'user_pass', 'user_nicename', 'user_email', 'user_url', 'display_name' );
	if(!empty($user)){
		foreach ($user as $key => $value) {
			$users_data[$key] = $value;
		}
		$usermeta = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE user_id = '{$user->ID}'");
		foreach ($usermeta as $key => $value) {
			if(!in_array($value->meta_key, $user_data_keys)){
				$users_data[$value->meta_key] = $value->meta_value;
			}
		}
		if(!is_null($var)){
			$return_user_data = (isset($users_data[$var])) ? $users_data[$var] : null;
			return $return_user_data;
		}else{
			$return_user_data = @$users_data;
			foreach ($return_user_data as $ukey => $uvalue) {
				if(is_serialized($uvalue)){
					$return_user_data[$ukey] = unserialize($uvalue);
				}else{
					$return_user_data[$ukey] = $uvalue;
				}
			}
			$return_user_data['user_role'] = cjpopups_user_role($return_user_data['ID']);
			return $return_user_data;
		}

	}else{
		return null;
	}
}

# Parse Usermeta String
####################################################################################################
function cjpopups_parse_usermeta($user_id_or_email, $string = '%%display_name%%'){
	$user_info = cjpopups_user_info($user_id_or_email);
	foreach ($user_info as $key => $user) {
		$search = "%%{$key}%%";
		$string = str_replace($search, $user_info[$key], $string);
	}
	return $string;
}

# Return user role by user id, email or username
####################################################################################################
function cjpopups_user_role($user_info_or_id){
	global $wpdb, $wp_roles;
	if(is_array($user_info_or_id)){
		$uid = cjpopups_user_info($user_info_or_id, 'ID');
	}else{
		$uid = $user_info_or_id;
	}
	$user = get_userdata( $uid );
	if($user && !empty($user->roles)){
		$capabilities = $user->{$wpdb->prefix . 'capabilities'};
		if ( !isset( $wp_roles ) ){
			$wp_roles = new WP_Roles();
		}
		foreach ( $wp_roles->role_names as $role => $name ){
			if ( array_key_exists( $role, $capabilities ) ){
				return $role;
			}
		}
	}else{
		return 'non-user';
	}
}

# Return possible valid username
####################################################################################################
function cjpopups_create_unique_username($user_login_string, $separator = '', $first = 1){
	if(strpos($user_login_string, '@')){
	    $user_login = explode('@', $user_login_string);
	    $user_login = $user_login[0];
    }else{
    	$user_login = str_replace('-', '_', sanitize_title($user_login_string));
    }
    if(!username_exists($user_login)){
        return $user_login;
    }else{
        preg_match('/(.+)'.$separator.'([0-9]+)$/', $user_login, $match);
        $new_user_login = isset($match[2]) ? $match[1].$separator.($match[2] + 1) : $user_login.$separator.$first;
        if(!username_exists( $new_user_login )){
        	return $new_user_login;
        }else{
        	return cjpopups_create_unique_username($new_user_login, $separator = '', $first = 1);
        }
    }
}


# Return possible valid database value
####################################################################################################
function cjpopups_create_unique_record($string, $db_table, $column){
	global $wpdb;
	$check_existing = $wpdb->get_row("SELECT * FROM $db_table WHERE $column = '{$string}'");
	if(is_null($check_existing)){
		return $string;
	}else{
		preg_match('/(.+)([0-9]+)$/', $string, $match);
        $string = isset($match[2]) ? $match[1].($match[2] + 1) : $string.rand(10000,99999);
        $check_existing = $wpdb->get_row("SELECT * FROM $db_table WHERE $column = '{$string}'");
        if(is_null($check_existing)){
        	return $string;
        }else{
        	return cjpopups_create_unique_record($string, $db_table, $column);
        }
	}
}


# Set a cookie as usual, but ALSO add it to $_COOKIE so the current page load has access
####################################################################################################
function cjpopups_set_cookie($name, $value='', $expire = 86400, $path='/', $domain='', $secure=false, $httponly=false){
    $_COOKIE[$name] = $value;
    $expire = time() + $expire;
    return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

# Get cookie value
####################################################################################################
function cjpopups_get_cookie($name){
    return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : null;
}


# Return local date based on the settings in General Settings tab.
####################################################################################################
function cjpopups_local_date($format, $date = null){
	if($date == null){
		$datetime = strtotime(date('Y-m-d H:i:s'));
	}else{
		$datetime = strtotime(date('Y-m-d H:i:s', $date));
	}
	$timezone_string = get_option('timezone_string');
	if($timezone_string != ''){
		date_default_timezone_set($timezone_string);
		$return = date($format, $datetime);
	}else{
		$return = date($format, $datetime);
	}
	return $return;
}

# Return date time based on WordPress general settings
####################################################################################################
function cjpopups_wp_date($timestamp, $time = null){
	$date_format = get_option('date_format');
	$time_format = (!is_null($time)) ? ' '.get_option('time_format') : '';
	$return_format = $date_format.$time_format;
	return date($return_format, $timestamp);
}

# Set local timezone based on WordPress settings.
####################################################################################################
function cjpopups_set_timezone(){
	$timezone_string = get_option('timezone_string');
	$system_timezone_string = date_default_timezone_get();
	$set_timezone_string = ($timezone_string == '') ? $system_timezone_string : $timezone_string;
	date_default_timezone_set($set_timezone_string);
}

function cjpopups_get_timezone(){
	return date_default_timezone_get();
}

# Returns nice time string
####################################################################################################
function cjpopups_time_ago($ptime){
    $etime = time() - $ptime;
    if ($etime < 1){
        return __('Just now', 'cjpopups');
    }
    $a = array(
    	12 * 30 * 24 * 60 * 60  =>  'year',
		30 * 24 * 60 * 60       =>  'month',
		24 * 60 * 60            =>  'day',
		60 * 60                 =>  'hour',
		60                      =>  'minute',
		1                       =>  'second',
    );
    $singular = array(
    	'year' => __('year', 'cjpopups'),
		'month' => __('month', 'cjpopups'),
		'day' => __('day', 'cjpopups'),
		'hour' => __('hour', 'cjpopups'),
		'minute' => __('minute', 'cjpopups'),
		'second' => __('second', 'cjpopups'),
    );
    $plurals = array(
    	'year' => __('years', 'cjpopups'),
		'month' => __('months', 'cjpopups'),
		'day' => __('days', 'cjpopups'),
		'hour' => __('hours', 'cjpopups'),
		'minute' => __('minutes', 'cjpopups'),
		'second' => __('seconds', 'cjpopups'),
    );
    foreach ($a as $secs => $str){
        $d = $etime / $secs;
        if ($d >= 1){
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $plurals[$str] : $singular[$str]) .' '. __('ago', 'cjpopups');
        }
    }
}



# Trim text with specified number of chars
####################################################################################################
function cjpopups_trim_text($str, $cut = 200, $after_trim = ''){
    $str_length = strlen($str);
    if($str_length > $cut){
    	return substr($str, 0, $cut). $after_trim;
    }else{
    	return $str;
    }
}


# Returns current URL of the page
####################################################################################################
function cjpopups_current_url($only_url = null, $port = null){
	$pageURL = (is_ssl()) ? "https://" : "http://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		if(is_null($port)){
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	if(is_null($only_url)){
		return $pageURL;
	}else{
		$url = explode('?', $pageURL);
		return $url[0];
	}
}

# Return parsed domain or Url
####################################################################################################
function cjpopups_parse_domain($url, $return = 'host'){
	// possible values host, path, scheme
	$parse = parse_url($url);
	return $parse[$return];
}

# Remove query string var
####################################################################################################
function cjpopups_remove_querystring_var($url, $key) {
	$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
	$url = substr($url, 0, -1);
	return $url;
}

# Format currency and add symbol.
####################################################################################################
function cjpopups_currency($symbol = '$', $amount = null, $align = 'left'){
	$amount = number_format($amount, 2);
	$return = ($align == 'left') ? $symbol.' '.$amount : $amount.' '.$symbol;
	return $return;
}

# Returns current IP address
####################################################################################################
function cjpopups_current_ip_address(){
	return $_SERVER['REMOTE_ADDR'];
}

# Check if its localhost or webhost
####################################################################################################
function cjpopups_is_local(){
	if($_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
		return true;
	}elseif(strpos(site_url(), 'cssjockey') > 0){
		return true;
	}else{
		return false;
	}
}

# Check if its localhost or webhost
####################################################################################################
function cjpopups_get_exchange_rate($from = 'USD', $to = 'INR'){
	$exchange_rate_data = wp_remote_get('http://rate-exchange.appspot.com/currency?from='.$from.'&to='.$to);
	if(!is_wp_error( $exchange_rate_data )){
		$return = json_decode($exchange_rate_data['body']);
		return $return->rate;
	}else{
		return 62;
	}
}


# File Uplaod
####################################################################################################
function cjpopups_file_upload($field_name, $allowed_width = null, $allowed_height = null, $allowed_file_types = null, $output = 'guid', $allowed_file_size = null){
	global $wpdb;

	$KB = '1024';
	$MB = '1048576';
	$GB = '1073741824';
	$TB = '1099511627776';

	$errors = null;
	$wp_upload_dir = wp_upload_dir();
	$tempFile = @$_FILES[$field_name]['tmp_name'];
	$targetPath = $wp_upload_dir['path'] . '/';
	$targetFile =  @$_FILES[$field_name]['name'];
	$fileParts = @pathinfo($_FILES[$field_name]['name']);
	$ext = '.' . @$fileParts['extension'];
	$file_size = @$_FILES[$field_name]['size'];
	if(!is_null($allowed_file_size) && $file_size > ($allowed_file_size * $KB)){
		$errors[] = sprintf(__('File size must be below %s kilobytes.', 'cjpopups'), $allowed_file_size);
	}

	list($img_width, $img_height) = @getimagesize($tempFile);

	if(!is_null($allowed_width) && $img_width != $allowed_width){
		$errors[] = sprintf(__('Image width must be %s pixels.', 'cjpopups'), $allowed_width);
	}

	if(!is_null($allowed_height) && $img_width != $allowed_height){
		$errors[] = sprintf(__('Image height must be %s pixels.', 'cjpopups'), $allowed_height);
	}

	if(!is_null($allowed_file_types) && !in_array(str_replace('.', '', $ext), explode('|', $allowed_file_types))){
		$errors[] = __('Invalid file type.', 'cjpopups');
	}

	if(is_array($errors)){
		return $errors;
	}else{
		$newFileName = wp_unique_filename( $targetPath, $targetFile );
		$targetFile = str_replace('//', '/', $targetPath) . $newFileName;
		move_uploaded_file($tempFile, $targetFile);
		$filename = $targetFile;
		$wp_filetype = wp_check_filetype(basename($filename), null );
		$attachment = array(
		    'guid' => $wp_upload_dir['baseurl'] . '/' . _wp_relative_upload_path( $filename ),
		    'post_mime_type' => $wp_filetype['type'],
		    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
		    'post_content' => '',
		    'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $filename);
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		global $wpdb;
		$guid = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '{$attach_id}'");
		if($output == 'guid'){
			return $guid->guid;
		}else{
			return $attach_id;
		}

	}
}


# File Uplaods
####################################################################################################
function cjpopups_file_uploads($field_name, $allowed_width = null, $allowed_height = null, $allowed_file_types = null, $output = 'guid', $allowed_file_size = null){
	global $wpdb;

	$errors = null;
	$wp_upload_dir = wp_upload_dir();
	$tempFile = @$field_name;
	$targetPath = $wp_upload_dir['path'] . '/';
	$targetFile = @$filename;
	$fileParts = @pathinfo($filename);
	$ext = explode('.', $tempFile);
	$ext = $ext[1];

	$file_size = @$_FILES[$field_name]['size'];
	if(!is_null($allowed_file_size) && $file_size > ($allowed_file_size * $KB)){
		$errors[] = sprintf(__('File size must be below %s kilobytes.', 'cjpopups'), $allowed_file_size);
	}

	list($img_width, $img_height) = @getimagesize($tempFile);

	if(!is_null($allowed_width) && $img_width != $allowed_width){
		$errors[] = sprintf(__('Image width must be %s pixels.', 'cjpopups'), $allowed_width);
	}

	if(!is_null($allowed_height) && $img_width != $allowed_height){
		$errors[] = sprintf(__('Image height must be %s pixels.', 'cjpopups'), $allowed_height);
	}

	if(!is_null($allowed_file_types) && !in_array(str_replace('.', '', $ext), explode('|', $allowed_file_types))){
		$errors[] = __('Invalid file type.', 'cjpopups');
	}

	if(is_array($errors)){
		return $errors;
	}else{
		//$targetFile = str_replace('//', '/', $targetPath) . 'img_' . sha1(md5(date('M-d-y H:i:s')).rand(5,99999)) . '.'.$ext;
		$newFileName = wp_unique_filename( $targetPath, $targetFile );
		$targetFile = str_replace('//', '/', $targetPath) . $newFileName;
		move_uploaded_file($tempFile, $targetFile);
		$filename = $targetFile;
		$wp_filetype = wp_check_filetype(basename($filename), null );
		$attachment = array(
		    'guid' => $wp_upload_dir['baseurl'] . '/' . _wp_relative_upload_path( $filename ),
		    'post_mime_type' => $wp_filetype['type'],
		    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
		    'post_content' => '',
		    'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $filename);
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		global $wpdb;
		$guid = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '{$attach_id}'");
		if($output == 'guid'){
			return $guid->guid;
		}else{
			return $attach_id;
		}

	}
}



# Template Type
####################################################################################################
function cjpopups_template_type(){
	global $wp_query;
	$return = '';
	if(is_404()){ $return = '404'; }
	if(is_archive()){ $return = 'archive'; }
	if(is_attachment()){ $return = 'attachment'; }
	if(is_author()){ $return = 'author'; }
	if(is_category()){ $return = 'category'; }
	if(is_front_page()){ $return = 'front_page'; }
	if(is_home()){ $return = 'home'; }
	if(is_page()){ $return = 'page'; }
	if(is_search()){ $return = 'search'; }
	if(is_single()){ $return = 'single'; }
	if(is_tag()){ $return = 'tag'; }
	if(is_tax()){ $return = 'tax'; }
	if(is_page_template()){
		$template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );
		$return = str_replace('.php', '', basename($template_name));
	}
	return $return;
}

# Template Type Name
####################################################################################################
function cjpopups_template_type_name(){
	global $wp_query;
	$return = '';
	if(is_404()){ $return = '404'; }
	if(is_archive()){ $return = 'archive'; }
	if(is_attachment()){ $return = 'attachment'; }
	if(is_author()){ $return = 'author'; }
	if(is_category()){ $return = 'category'; }
	if(is_home()){ $return = 'blog'; }
	if(is_page()){ $return = 'page'; }
	if(is_search()){ $return = 'search'; }
	if(is_single()){ $return = 'post'; }
	if(is_tag()){ $return = 'tag'; }
	if(is_tax()){ $return = 'tax'; }
	if(is_front_page()){ $return = 'homepage'; }
	return $return;
}

# Template Title
####################################################################################################
function cjpopups_template_title($post = null){
	global $wp_query, $post;

	$template = str_replace('content/', '', cjpopups_template_type());

	if(is_day()){
		$archive_title = get_the_time('F jS, Y');
	}elseif(is_month()){
		$archive_title = get_the_time('F, Y');
	}elseif(is_year()){
		$archive_title = get_the_time('Y');
	}elseif(is_post_type_archive()){
		$archive_title = post_type_archive_title( '', false );
	}

	if(is_404()){ $return = '<span class="highlight-term">'.__('Oops!! Page not found.', 'cjpopups').'</span>'; }
	if(is_archive()){ $return = sprintf(__('Archive for <span class="highlight-term">"%s"</span>', 'cjpopups'), @$archive_title ); }
	if(is_attachment()){ $return = '<span class="highlight-term">'.$post->post_title.'</span>'; }
	if(is_author()){ $return = '<span class="highlight-term">'.__('Articles posted by: ', 'cjpopups').ucwords(cjpopups_user_info($post->post_author, 'display_name')).'</span>'; }
	if(is_category()){ $return = sprintf(__('Archive for <span class="highlight-term">"%s"</span> category', 'cjpopups'), single_cat_title( '', false )); }
	if(is_front_page()){ $return = 'front_page'; }
	if(is_home()){ $return = '<span class="highlight-term">'.__('Recent Blog Posts', 'cjpopups').'</span>'; }
	if(is_page()){ $return = '<span class="highlight-term">'.$post->post_title.'</span>'; }
	if(is_search()){ $return = sprintf(__('Search results for <span class="highlight-term">"%s"</span>', 'cjpopups'), @$_GET['s']); }
	if(is_single()){ $return = '<span class="highlight-term">'.$post->post_title.'</span>'; }
	if(is_tag()){ $return = sprintf(__('Posts tagged <span class="highlight-term">"%s"</span>', 'cjpopups'), single_tag_title( '', false )); }
	if(is_tax()){ $return = sprintf(__('Archive for <span class="highlight-term">"%s"</span>', 'cjpopups'), single_term_title( '', false )); }

	return @$return;
}


# Post or Page Featured Image
####################################################################################################
function cjpopups_featured_image($size = null, $single = false){
	global $post;
	if(has_post_thumbnail( $post->ID )){
		if(!is_array($size)){
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
		}else{
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
			$image = cjpopups_resize_image($image[0], $size[0], $size[1], true, $single);
		}
		if($single){
			$return = $image[0];
		}else{
			$return = $image;
		}
	}else{
		$return[] = 'http://placehold.it/600x600/eeeeee/cccccc&text=No+Thumbnail';
		$return[] = 150;
		$return[] = 150;
		return $return;
	}
	return $return;
}



# Post or Page Featured Image With POST ID
####################################################################################################
function cjpopups_post_featured_image($post_id, $size = null, $single = false, $default_text = 'No Thumbnail'){
	global $post;
	if(has_post_thumbnail( $post_id )){
		if(!is_array($size)){
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
		}else{
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
			$image = cjpopups_resize_image($image[0], $size[0], $size[1], true, false);
		}

		if($single){
			$return = $image[0];
		}else{
			$return = $image;
		}
	}else{
		if($single){
			$return = 'http://placehold.it/600x600/eeeeee/cccccc&text='.urlencode($default_text);
		}else{
			$return[] = 'http://placehold.it/600x600/eeeeee/cccccc&text='.urlencode($default_text);
			$return[] = 250;
			$return[] = 250;
		}
		$return[] = 'http://placehold.it/600x600/eeeeee/cccccc&text=No+Thumbnail';
		$return[] = 250;
		$return[] = 250;
		return $return;
	}
	return $return;
}

# Resize images for custom thumbnails and other displays
####################################################################################################
function cjpopups_resize_image($src, $width, $height = null, $crop = false, $single = false){
	require_once(sprintf('%s/aq_resizer.php', cjpopups_item_path('helpers_dir')));
	$resized = aq_resize($src, $width, $height, $crop, $single);
	if(!empty($resized)){
		return $resized;
	}else{
		$placeholder = 'http://placehold.it/'.$width.'x'.$height.'&text=No+Thumbnail';;
		if($single){
			$return = $placeholder;
		}else{
			$return[] = $placeholder;
			$return[] = $width;
			$return[] = $height;
		}
		return $return;
	}
}

# Get attachment/file mime type
####################################################################################################
function cjpopups_file_info($url_or_id){
	global $wpdb;
	if(is_numeric($url_or_id)){
		$query = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID='%s';", $url_or_id ));
		return $query;
	}else{
		$query = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid='%s';", $url_or_id ));
		return $query;
	}
}

# Get Gravatar URL
####################################################################################################
function cjpopups_gravatar_url($user_id_or_email, $size = 150){
	$user_email = cjpopups_user_info($user_id_or_email, 'user_email');
	$gravatar = get_avatar( $user_email, $size, null);
    preg_match("/src='(.*?)'/i", $gravatar, $matches);
    $return = (!empty($matches) && is_array($matches)) ? $matches[1] : null;
    return $return;
}

# Get WP Avatar URL
####################################################################################################
function cjpopups_wp_avatar_url($user_id_or_email, $size = 150){
	$user_id = cjpopups_user_info($user_id_or_email, 'ID');
	$get_avatar = get_avatar( $user_id, $size );
    preg_match("/src='(.*?)'/i", $get_avatar, $matches);
    return ( $matches[1] );
}

# Get url of an image
####################################################################################################
function cjpopups_get_image_url($image){
	if(strpos($image, 'img') > 0){
		$xpath = new DOMXPath(@DOMDocument::loadHTML($image));
		$src = $xpath->evaluate("string(//img/@src)");
		return $src;
	}else{
		return $image;
	}
}

# Get Post by meta key from postmeta table
####################################################################################################
function cjpopups_post_by_metakey($meta_key, $meta_value){
	global $wpdb;
	$query = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_key = '{$meta_key}' and meta_value = '{$meta_value}'");
	return (!empty($query)) ? $query->post_id : false;
}

# Return all post meta and data by post id
####################################################################################################
function cjpopups_post_info($post_id, $var = null){
	global $wpdb;
	$post = get_post($post_id);
	if(is_null($post)){
		return null;
	}else{
		$post_data = array();
		foreach ($post as $key => $pdata) {
			$post_data[$key] = $pdata;
		}
		// Get post meta
		$post_meta = $wpdb->get_results("SELECT * FROM 	$wpdb->postmeta WHERE post_id = '{$post_id}'");
		foreach ($post_meta as $key => $value) {
			$post_data[$value->meta_key] = $value->meta_value;
		}

		$post_data['stickty'] = is_sticky($post->ID);

		if((has_post_thumbnail($post->ID))){
			$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID));
			$image_full = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
			$image_retina = '';
			if(file_exists($image_full[0])){
				$filename = explode('.', basename($image_full[0]));
				$filename = basename($filename[0]).'-'.$image[1].'x'.$image[1].'@2x.'.$filename[1];
				$folder_path = str_replace(basename($image_full[0]), '', $image_full[0]);
				$image_retina = $folder_path.$filename;
				$image_retina = (file_exists($image_retina)) ? $image_retina : '';
			}

		}else{
			$image = '';
			$image_full = '';
			$image_retina = '';
		}

		$post_data['featured_image'] = (is_array($image)) ? $image[0] : '';
		$post_data['featured_image_full'] = (is_array($image_full)) ? $image_full[0] : '';
		$post_data['featured_image_retina'] = $image_retina;

		if(is_null($var)){
			return $post_data;
		}else{
			return $post_data[$var];
		}
	}
}

# Return post taxonomies
####################################################################################################
function cjpopups_post_terms($post_id, $taxonomy = null){
	global $wpdb;
	$post = get_post($post_id);
	if(!is_null($taxonomy)){
		$return[$taxonomy] = wp_get_post_terms( $post_id, $taxonomy );
	}else{
		$post_taxonomies = get_object_taxonomies( $post );
		foreach ($post_taxonomies as $key => $taxonomy) {
			$return[$taxonomy] = wp_get_post_terms( $post_id, $taxonomy );
		}
	}

	return $return;
}

# Get post count by term
####################################################################################################
function cjpopups_post_count($term_id){
	global $wpdb;
	$posts = get_posts("category={$term_id}");
	$count = count($posts);
	return $count;
}


function cjpopups_terms_array($taxonomy_slug, $object = false){
	global $wpdb;
	$group_args = array(
		'orderby' => 'name',
	    'order' => 'ASC',
	    'hide_empty' => false,
	);
	$terms = get_terms( $taxonomy_slug , $group_args );
	if(!empty($terms)){
		foreach ($terms as $key => $term) {
			if(is_null($object)){
				$terms_array[$term->term_id] = $term->name;
			}else{
				$terms_array[] = array(
					'name' => $term->name,
					'value' => $term->term_id,
				);
			}
		}
		return $terms_array;
	}else{
		return array();
	}
}


function cjpopups_load_iconset_css(){
	$enable_fonts = @cjpopups_item_vars('load_extras');
	if(isset($enable_fonts['icomoon-icons']) && $enable_fonts['icomoon-icons'] == 1){
		$icons_url = cjpopups_item_path('framework_url').'/assets/helpers/icons/icomoon/all/';
		for ($i=1; $i < 13; $i++) {
			echo '<link rel="stylesheet" id="cjpopups_iconset-'.$i.'"  href="'.$icons_url.'ncicons-'.$i.'/style.css" type="text/css" media="all" />';
		}
	}
}
add_action('wp_head', 'cjpopups_load_iconset_css');



function cjpopups_color_brightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}


function cjpopups_item_assistant_holder(){
	echo '<div class="cj-assistant">';
	echo do_action('cj_assistant_hook');
	echo '</div>';
}
add_action('admin_footer', 'cjpopups_item_assistant_holder');


function cjpopups_convert_html($html){
	$return = '<span class="cj-code">';
	$return .= htmlentities($html);
	$return .= '</span>';
	return $return;
}

add_filter( 'no_texturize_shortcodes', 'cjpopups_shortcode_no_wptexturize' );
function cjpopups_shortcode_no_wptexturize($shortcodes){
    $shortcodes[] = 'rev_slider';
    return $shortcodes;
}


function cjpopups_add_settings_link( $links ) {
    $settings_link = '<a href="'.cjpopups_callback_url('core_welcome').'">'.__('Settings', 'cjpopups').'</a>';
    $settings_link .= ' | <a href="'.cjpopups_callback_url('core_uninstall').'">'.__('Uninstall', 'cjpopups').'</a>';
  	array_push( $links, $settings_link );
  	return $links;
}

function cjpopups_get_multi_array_values($array, $return = array()) {
    for($x = 0; $x <= count($array); $x++) {
        if(isset($array[$x]) && is_array($array[$x])) {
            $return = cjpopups_get_multi_array_values($array[$x], $return);
        }
        else {
            if(isset($array[$x])) {
                $return[] = $array[$x];
            }
        }
    }
    return array_unique($return);
}

function cjpopups_load_debug_file(){
	if(isset($_GET['cjpopups_debug']) && $_GET['cjpopups_debug'] == 1){
		require_once(sprintf('%s/debug.php', cjpopups_item_path('item_dir')));
		die();
	}
}
add_action('init', 'cjpopups_load_debug_file');

function cjpopups_countries_array($country = null){
	$countries = array( "Andorra" => "Andorra", "United Arab Emirates" => "United Arab Emirates", "Afghanistan" => "Afghanistan", "Antigua And Barbuda" => "Antigua And Barbuda", "Anguilla" => "Anguilla", "Albania" => "Albania", "Armenia" => "Armenia", "Netherlands Antilles" => "Netherlands Antilles", "Angola" => "Angola", "Antarctica" => "Antarctica", "Argentina" => "Argentina", "American Samoa" => "American Samoa", "Austria" => "Austria", "Australia" => "Australia", "Aruba" => "Aruba", "Azerbaijan" => "Azerbaijan", "Bosnia And Herzegowina" => "Bosnia And Herzegowina", "Barbados" => "Barbados", "Bangladesh" => "Bangladesh", "Belgium" => "Belgium", "Burkina Faso" => "Burkina Faso", "Bulgaria" => "Bulgaria", "Bahrain" => "Bahrain", "Burundi" => "Burundi", "Benin" => "Benin", "Bermuda" => "Bermuda", "Brunei Darussalam" => "Brunei Darussalam", "Bolivia" => "Bolivia", "Brazil" => "Brazil", "Bahamas" => "Bahamas", "Bhutan" => "Bhutan", "Bouvet Island" => "Bouvet Island", "Botswana" => "Botswana", "Belarus" => "Belarus", "Belize" => "Belize", "Canada" => "Canada", "Cocos (Keeling) Islands" => "Cocos (Keeling) Islands", "Congo, The Drc" => "Congo, The Drc", "Central African Republic" => "Central African Republic", "Congo" => "Congo", "Switzerland" => "Switzerland", "Cote D'ivoire" => "Cote D'ivoire", "Cook Islands" => "Cook Islands", "Chile" => "Chile", "Cameroon" => "Cameroon", "China" => "China", "Colombia" => "Colombia", "Costa Rica" => "Costa Rica", "Cuba" => "Cuba", "Cape Verde" => "Cape Verde", "Christmas Island" => "Christmas Island", "Cyprus" => "Cyprus", "Czech Republic" => "Czech Republic", "Germany" => "Germany", "Djibouti" => "Djibouti", "Denmark" => "Denmark", "Dominica" => "Dominica", "Dominican Republic" => "Dominican Republic", "Algeria" => "Algeria", "Ecuador" => "Ecuador", "Estonia" => "Estonia", "Egypt" => "Egypt", "Western Sahara" => "Western Sahara", "Eritrea" => "Eritrea", "Spain" => "Spain", "Ethiopia" => "Ethiopia", "Finland" => "Finland", "Fiji" => "Fiji", "Falkland Islands (Malvinas)" => "Falkland Islands (Malvinas)", "Micronesia, Federated States Of" => "Micronesia, Federated States Of", "Faroe Islands" => "Faroe Islands", "France" => "France", "France, Metropolitan" => "France, Metropolitan", "Gabon" => "Gabon", "United Kingdom" => "United Kingdom", "Grenada" => "Grenada", "Georgia" => "Georgia", "French Guiana" => "French Guiana", "Ghana" => "Ghana", "Gibraltar" => "Gibraltar", "Greenland" => "Greenland", "Gambia" => "Gambia", "Guinea" => "Guinea", "Guadeloupe" => "Guadeloupe", "Equatorial Guinea" => "Equatorial Guinea", "Greece" => "Greece", "South Georgia And South S.s." => "South Georgia And South S.s.", "Guatemala" => "Guatemala", "Guam" => "Guam", "Guinea-bissau" => "Guinea-bissau", "Guyana" => "Guyana", "Hong Kong" => "Hong Kong", "Heard And Mc Donald Islands" => "Heard And Mc Donald Islands", "Honduras" => "Honduras", "Croatia (Local Name: Hrvatska)" => "Croatia (Local Name: Hrvatska)", "Haiti" => "Haiti", "Hungary" => "Hungary", "Indonesia" => "Indonesia", "Ireland" => "Ireland", "Israel" => "Israel", "India" => "India", "British Indian Ocean Territory" => "British Indian Ocean Territory", "Iraq" => "Iraq", "Iran (Islamic Republic Of)" => "Iran (Islamic Republic Of)", "Iceland" => "Iceland", "Italy" => "Italy", "Jamaica" => "Jamaica", "Jordan" => "Jordan", "Japan" => "Japan", "Kenya" => "Kenya", "Kyrgyzstan" => "Kyrgyzstan", "Cambodia" => "Cambodia", "Kiribati" => "Kiribati", "Comoros" => "Comoros", "Saint Kitts And Nevis" => "Saint Kitts And Nevis", "Korea, D.p.r.o." => "Korea, D.p.r.o.", "Korea, Republic Of" => "Korea, Republic Of", "Kuwait" => "Kuwait", "Cayman Islands" => "Cayman Islands", "Kazakhstan" => "Kazakhstan", "Laos" => "Laos", "Lebanon" => "Lebanon", "Saint Lucia" => "Saint Lucia", "Liechtenstein" => "Liechtenstein", "Sri Lanka" => "Sri Lanka", "Liberia" => "Liberia", "Lesotho" => "Lesotho", "Lithuania" => "Lithuania", "Luxembourg" => "Luxembourg", "Latvia" => "Latvia", "Libyan Arab Jamahiriya" => "Libyan Arab Jamahiriya", "Morocco" => "Morocco", "Monaco" => "Monaco", "Moldova, Republic Of" => "Moldova, Republic Of", "Montenegro" => "Montenegro", "Madagascar" => "Madagascar", "Marshall Islands" => "Marshall Islands", "Macedonia" => "Macedonia", "Mali" => "Mali", "Myanmar (Burma)" => "Myanmar (Burma)", "Mongolia" => "Mongolia", "Macau" => "Macau", "Northern Mariana Islands" => "Northern Mariana Islands", "Martinique" => "Martinique", "Mauritania" => "Mauritania", "Montserrat" => "Montserrat", "Malta" => "Malta", "Mauritius" => "Mauritius", "Maldives" => "Maldives", "Malawi" => "Malawi", "Mexico" => "Mexico", "Malaysia" => "Malaysia", "Mozambique" => "Mozambique", "Namibia" => "Namibia", "New Caledonia" => "New Caledonia", "Niger" => "Niger", "Norfolk Island" => "Norfolk Island", "Nigeria" => "Nigeria", "Nicaragua" => "Nicaragua", "Netherlands" => "Netherlands", "Norway" => "Norway", "Nepal" => "Nepal", "Nauru" => "Nauru", "Niue" => "Niue", "New Zealand" => "New Zealand", "Oman" => "Oman", "Panama" => "Panama", "Peru" => "Peru", "French Polynesia" => "French Polynesia", "Papua New Guinea" => "Papua New Guinea", "Philippines" => "Philippines", "Pakistan" => "Pakistan", "Poland" => "Poland", "St. Pierre And Miquelon" => "St. Pierre And Miquelon", "Pitcairn" => "Pitcairn", "Puerto Rico" => "Puerto Rico", "Portugal" => "Portugal", "Palau" => "Palau", "Paraguay" => "Paraguay", "Qatar" => "Qatar", "Reunion" => "Reunion", "Romania" => "Romania", "Serbia" => "Serbia", "Russian Federation" => "Russian Federation", "Rwanda" => "Rwanda", "Saudi Arabia" => "Saudi Arabia", "Solomon Islands" => "Solomon Islands", "Seychelles" => "Seychelles", "Sudan" => "Sudan", "Sweden" => "Sweden", "Singapore" => "Singapore", "St. Helena" => "St. Helena", "Slovenia" => "Slovenia", "Svalbard And Jan Mayen Islands" => "Svalbard And Jan Mayen Islands", "Slovakia (Slovak Republic)" => "Slovakia (Slovak Republic)", "Sierra Leone" => "Sierra Leone", "San Marino" => "San Marino", "Senegal" => "Senegal", "Somalia" => "Somalia", "Suriname" => "Suriname", "South Sudan" => "South Sudan", "Sao Tome And Principe" => "Sao Tome And Principe", "El Salvador" => "El Salvador", "Syrian Arab Republic" => "Syrian Arab Republic", "Swaziland" => "Swaziland", "Turks And Caicos Islands" => "Turks And Caicos Islands", "Chad" => "Chad", "French Southern Territories" => "French Southern Territories", "Togo" => "Togo", "Thailand" => "Thailand", "Tajikistan" => "Tajikistan", "Tokelau" => "Tokelau", "Turkmenistan" => "Turkmenistan", "Tunisia" => "Tunisia", "Tonga" => "Tonga", "East Timor" => "East Timor", "Turkey" => "Turkey", "Trinidad And Tobago" => "Trinidad And Tobago", "Tuvalu" => "Tuvalu", "Taiwan, Province Of China" => "Taiwan, Province Of China", "Tanzania, United Republic Of" => "Tanzania, United Republic Of", "Ukraine" => "Ukraine", "Uganda" => "Uganda", "U.s. Minor Islands" => "U.s. Minor Islands", "United States" => "United States", "Uruguay" => "Uruguay", "Uzbekistan" => "Uzbekistan", "Holy See (Vatican City State)" => "Holy See (Vatican City State)", "Saint Vincent And The Grenadines" => "Saint Vincent And The Grenadines", "Venezuela" => "Venezuela", "Virgin Islands (British)" => "Virgin Islands (British)", "Virgin Islands (U.S.)" => "Virgin Islands (U.S.)", "Viet Nam" => "Viet Nam", "Vanuatu" => "Vanuatu", "Wallis And Futuna Islands" => "Wallis And Futuna Islands", "Samoa" => "Samoa", "Yemen" => "Yemen", "Mayotte" => "Mayotte", "South Africa" => "South Africa", "Zambia" => "Zambia", "Zimbabwe" => "Zimbabwe");
	if(is_null($country)){
		return $countries;
	}else{
		$country = (isset($countries[$country])) ? $countries[$country] : null;
		return $country;
	}
}

function cjpopups_fontawesome_icons_array($icon = null){
	$icons = array('' => __('None', 'cjpopups'), 'fa-adjust' => 'adjust', 'fa-adn' => 'adn', 'fa-align-center' => 'align-center', 'fa-align-justify' => 'align-justify', 'fa-align-left' => 'align-left', 'fa-align-right' => 'align-right', 'fa-ambulance' => 'ambulance', 'fa-anchor' => 'anchor', 'fa-android' => 'android', 'fa-angellist' => 'angellist', 'fa-angle-double-down' => 'angle-double-down', 'fa-angle-double-left' => 'angle-double-left', 'fa-angle-double-right' => 'angle-double-right', 'fa-angle-double-up' => 'angle-double-up', 'fa-angle-down' => 'angle-down', 'fa-angle-left' => 'angle-left', 'fa-angle-right' => 'angle-right', 'fa-angle-up' => 'angle-up', 'fa-apple' => 'apple', 'fa-archive' => 'archive', 'fa-area-chart' => 'area-chart', 'fa-arrow-circle-down' => 'arrow-circle-down', 'fa-arrow-circle-left' => 'arrow-circle-left', 'fa-arrow-circle-o-down' => 'arrow-circle-o-down', 'fa-arrow-circle-o-left' => 'arrow-circle-o-left', 'fa-arrow-circle-o-right' => 'arrow-circle-o-right', 'fa-arrow-circle-o-up' => 'arrow-circle-o-up', 'fa-arrow-circle-right' => 'arrow-circle-right', 'fa-arrow-circle-up' => 'arrow-circle-up', 'fa-arrow-down' => 'arrow-down', 'fa-arrow-left' => 'arrow-left', 'fa-arrow-right' => 'arrow-right', 'fa-arrow-up' => 'arrow-up', 'fa-arrows' => 'arrows', 'fa-arrows-alt' => 'arrows-alt', 'fa-arrows-h' => 'arrows-h', 'fa-arrows-v' => 'arrows-v', 'fa-asterisk' => 'asterisk', 'fa-at' => 'at', 'fa-automobile' => 'automobile (alias)', 'fa-backward' => 'backward', 'fa-ban' => 'ban', 'fa-bank' => 'bank (alias)', 'fa-bar-chart' => 'bar-chart', 'fa-bar-chart-o' => 'bar-chart-o (alias)', 'fa-barcode' => 'barcode', 'fa-bars' => 'bars', 'fa-beer' => 'beer', 'fa-behance' => 'behance', 'fa-behance-square' => 'behance-square', 'fa-bell' => 'bell', 'fa-bell-o' => 'bell-o', 'fa-bell-slash' => 'bell-slash', 'fa-bell-slash-o' => 'bell-slash-o', 'fa-bicycle' => 'bicycle', 'fa-binoculars' => 'binoculars', 'fa-birthday-cake' => 'birthday-cake', 'fa-bitbucket' => 'bitbucket', 'fa-bitbucket-square' => 'bitbucket-square', 'fa-bitcoin' => 'bitcoin (alias)', 'fa-bold' => 'bold', 'fa-bolt' => 'bolt', 'fa-bomb' => 'bomb', 'fa-book' => 'book', 'fa-bookmark' => 'bookmark', 'fa-bookmark-o' => 'bookmark-o', 'fa-briefcase' => 'briefcase', 'fa-btc' => 'btc', 'fa-bug' => 'bug', 'fa-building' => 'building', 'fa-building-o' => 'building-o', 'fa-bullhorn' => 'bullhorn', 'fa-bullseye' => 'bullseye', 'fa-bus' => 'bus', 'fa-cab' => 'cab (alias)', 'fa-calculator' => 'calculator', 'fa-calendar' => 'calendar', 'fa-calendar-o' => 'calendar-o', 'fa-camera' => 'camera', 'fa-camera-retro' => 'camera-retro', 'fa-car' => 'car', 'fa-caret-down' => 'caret-down', 'fa-caret-left' => 'caret-left', 'fa-caret-right' => 'caret-right', 'fa-caret-square-o-down' => 'caret-square-o-down', 'fa-caret-square-o-left' => 'caret-square-o-left', 'fa-caret-square-o-right' => 'caret-square-o-right', 'fa-caret-square-o-up' => 'caret-square-o-up', 'fa-caret-up' => 'caret-up', 'fa-cc' => 'cc', 'fa-cc-amex' => 'cc-amex', 'fa-cc-discover' => 'cc-discover', 'fa-cc-mastercard' => 'cc-mastercard', 'fa-cc-paypal' => 'cc-paypal', 'fa-cc-stripe' => 'cc-stripe', 'fa-cc-visa' => 'cc-visa', 'fa-certificate' => 'certificate', 'fa-chain' => 'chain (alias)', 'fa-chain-broken' => 'chain-broken', 'fa-check' => 'check', 'fa-check-circle' => 'check-circle', 'fa-check-circle-o' => 'check-circle-o', 'fa-check-square' => 'check-square', 'fa-check-square-o' => 'check-square-o', 'fa-chevron-circle-down' => 'chevron-circle-down', 'fa-chevron-circle-left' => 'chevron-circle-left', 'fa-chevron-circle-right' => 'chevron-circle-right', 'fa-chevron-circle-up' => 'chevron-circle-up', 'fa-chevron-down' => 'chevron-down', 'fa-chevron-left' => 'chevron-left', 'fa-chevron-right' => 'chevron-right', 'fa-chevron-up' => 'chevron-up', 'fa-child' => 'child', 'fa-circle' => 'circle', 'fa-circle-o' => 'circle-o', 'fa-circle-o-notch' => 'circle-o-notch', 'fa-circle-thin' => 'circle-thin', 'fa-clipboard' => 'clipboard', 'fa-clock-o' => 'clock-o', 'fa-close' => 'close (alias)', 'fa-cloud' => 'cloud', 'fa-cloud-download' => 'cloud-download', 'fa-cloud-upload' => 'cloud-upload', 'fa-cny' => 'cny (alias)', 'fa-code' => 'code', 'fa-code-fork' => 'code-fork', 'fa-codepen' => 'codepen', 'fa-coffee' => 'coffee', 'fa-cog' => 'cog', 'fa-cogs' => 'cogs', 'fa-columns' => 'columns', 'fa-comment' => 'comment', 'fa-comment-o' => 'comment-o', 'fa-comments' => 'comments', 'fa-comments-o' => 'comments-o', 'fa-compass' => 'compass', 'fa-compress' => 'compress', 'fa-copy' => 'copy (alias)', 'fa-copyright' => 'copyright', 'fa-credit-card' => 'credit-card', 'fa-crop' => 'crop', 'fa-crosshairs' => 'crosshairs', 'fa-css3' => 'css3', 'fa-cube' => 'cube', 'fa-cubes' => 'cubes', 'fa-cut' => 'cut (alias)', 'fa-cutlery' => 'cutlery', 'fa-dashboard' => 'dashboard (alias)', 'fa-database' => 'database', 'fa-dedent' => 'dedent (alias)', 'fa-delicious' => 'delicious', 'fa-desktop' => 'desktop', 'fa-deviantart' => 'deviantart', 'fa-digg' => 'digg', 'fa-dollar' => 'dollar (alias)', 'fa-dot-circle-o' => 'dot-circle-o', 'fa-download' => 'download', 'fa-dribbble' => 'dribbble', 'fa-dropbox' => 'dropbox', 'fa-drupal' => 'drupal', 'fa-edit' => 'edit (alias)', 'fa-eject' => 'eject', 'fa-ellipsis-h' => 'ellipsis-h', 'fa-ellipsis-v' => 'ellipsis-v', 'fa-empire' => 'empire', 'fa-envelope' => 'envelope', 'fa-envelope-o' => 'envelope-o', 'fa-envelope-square' => 'envelope-square', 'fa-eraser' => 'eraser', 'fa-eur' => 'eur', 'fa-euro' => 'euro (alias)', 'fa-exchange' => 'exchange', 'fa-exclamation' => 'exclamation', 'fa-exclamation-circle' => 'exclamation-circle', 'fa-exclamation-triangle' => 'exclamation-triangle', 'fa-expand' => 'expand', 'fa-external-link' => 'external-link', 'fa-external-link-square' => 'external-link-square', 'fa-eye' => 'eye', 'fa-eye-slash' => 'eye-slash', 'fa-eyedropper' => 'eyedropper', 'fa-facebook' => 'facebook', 'fa-facebook-square' => 'facebook-square', 'fa-fast-backward' => 'fast-backward', 'fa-fast-forward' => 'fast-forward', 'fa-fax' => 'fax', 'fa-female' => 'female', 'fa-fighter-jet' => 'fighter-jet', 'fa-file' => 'file', 'fa-file-archive-o' => 'file-archive-o', 'fa-file-audio-o' => 'file-audio-o', 'fa-file-code-o' => 'file-code-o', 'fa-file-excel-o' => 'file-excel-o', 'fa-file-image-o' => 'file-image-o', 'fa-file-movie-o' => 'file-movie-o (alias)', 'fa-file-o' => 'file-o', 'fa-file-pdf-o' => 'file-pdf-o', 'fa-file-photo-o' => 'file-photo-o (alias)', 'fa-file-picture-o' => 'file-picture-o (alias)', 'fa-file-powerpoint-o' => 'file-powerpoint-o', 'fa-file-sound-o' => 'file-sound-o (alias)', 'fa-file-text' => 'file-text', 'fa-file-text-o' => 'file-text-o', 'fa-file-video-o' => 'file-video-o', 'fa-file-word-o' => 'file-word-o', 'fa-file-zip-o' => 'file-zip-o (alias)', 'fa-files-o' => 'files-o', 'fa-film' => 'film', 'fa-filter' => 'filter', 'fa-fire' => 'fire', 'fa-fire-extinguisher' => 'fire-extinguisher', 'fa-flag' => 'flag', 'fa-flag-checkered' => 'flag-checkered', 'fa-flag-o' => 'flag-o', 'fa-flash' => 'flash (alias)', 'fa-flask' => 'flask', 'fa-flickr' => 'flickr', 'fa-floppy-o' => 'floppy-o', 'fa-folder' => 'folder', 'fa-folder-o' => 'folder-o', 'fa-folder-open' => 'folder-open', 'fa-folder-open-o' => 'folder-open-o', 'fa-font' => 'font', 'fa-forward' => 'forward', 'fa-foursquare' => 'foursquare', 'fa-frown-o' => 'frown-o', 'fa-futbol-o' => 'futbol-o', 'fa-gamepad' => 'gamepad', 'fa-gavel' => 'gavel', 'fa-gbp' => 'gbp', 'fa-ge' => 'ge (alias)', 'fa-gear' => 'gear (alias)', 'fa-gears' => 'gears (alias)', 'fa-gift' => 'gift', 'fa-git' => 'git', 'fa-git-square' => 'git-square', 'fa-github' => 'github', 'fa-github-alt' => 'github-alt', 'fa-github-square' => 'github-square', 'fa-gittip' => 'gittip', 'fa-glass' => 'glass', 'fa-globe' => 'globe', 'fa-google' => 'google', 'fa-google-plus' => 'google-plus', 'fa-google-plus-square' => 'google-plus-square', 'fa-google-wallet' => 'google-wallet', 'fa-graduation-cap' => 'graduation-cap', 'fa-group' => 'group (alias)', 'fa-h-square' => 'h-square', 'fa-hacker-news' => 'hacker-news', 'fa-hand-o-down' => 'hand-o-down', 'fa-hand-o-left' => 'hand-o-left', 'fa-hand-o-right' => 'hand-o-right', 'fa-hand-o-up' => 'hand-o-up', 'fa-hdd-o' => 'hdd-o', 'fa-header' => 'header', 'fa-headphones' => 'headphones', 'fa-heart' => 'heart', 'fa-heart-o' => 'heart-o', 'fa-history' => 'history', 'fa-home' => 'home', 'fa-hospital-o' => 'hospital-o', 'fa-html5' => 'html5', 'fa-ils' => 'ils', 'fa-image' => 'image (alias)', 'fa-inbox' => 'inbox', 'fa-indent' => 'indent', 'fa-info' => 'info', 'fa-info-circle' => 'info-circle', 'fa-inr' => 'inr', 'fa-instagram' => 'instagram', 'fa-institution' => 'institution (alias)', 'fa-ioxhost' => 'ioxhost', 'fa-italic' => 'italic', 'fa-joomla' => 'joomla', 'fa-jpy' => 'jpy', 'fa-jsfiddle' => 'jsfiddle', 'fa-key' => 'key', 'fa-keyboard-o' => 'keyboard-o', 'fa-krw' => 'krw', 'fa-language' => 'language', 'fa-laptop' => 'laptop', 'fa-lastfm' => 'lastfm', 'fa-lastfm-square' => 'lastfm-square', 'fa-leaf' => 'leaf', 'fa-legal' => 'legal (alias)', 'fa-lemon-o' => 'lemon-o', 'fa-level-down' => 'level-down', 'fa-level-up' => 'level-up', 'fa-life-bouy' => 'life-bouy (alias)', 'fa-life-buoy' => 'life-buoy (alias)', 'fa-life-ring' => 'life-ring', 'fa-life-saver' => 'life-saver (alias)', 'fa-lightbulb-o' => 'lightbulb-o', 'fa-line-chart' => 'line-chart', 'fa-link' => 'link', 'fa-linkedin' => 'linkedin', 'fa-linkedin-square' => 'linkedin-square', 'fa-linux' => 'linux', 'fa-list' => 'list', 'fa-list-alt' => 'list-alt', 'fa-list-ol' => 'list-ol', 'fa-list-ul' => 'list-ul', 'fa-location-arrow' => 'location-arrow', 'fa-lock' => 'lock', 'fa-long-arrow-down' => 'long-arrow-down', 'fa-long-arrow-left' => 'long-arrow-left', 'fa-long-arrow-right' => 'long-arrow-right', 'fa-long-arrow-up' => 'long-arrow-up', 'fa-magic' => 'magic', 'fa-magnet' => 'magnet', 'fa-mail-forward' => 'mail-forward (alias)', 'fa-mail-reply' => 'mail-reply (alias)', 'fa-mail-reply-all' => 'mail-reply-all (alias)', 'fa-male' => 'male', 'fa-map-marker' => 'map-marker', 'fa-maxcdn' => 'maxcdn', 'fa-meanpath' => 'meanpath', 'fa-medkit' => 'medkit', 'fa-meh-o' => 'meh-o', 'fa-microphone' => 'microphone', 'fa-microphone-slash' => 'microphone-slash', 'fa-minus' => 'minus', 'fa-minus-circle' => 'minus-circle', 'fa-minus-square' => 'minus-square', 'fa-minus-square-o' => 'minus-square-o', 'fa-mobile' => 'mobile', 'fa-mobile-phone' => 'mobile-phone (alias)', 'fa-money' => 'money', 'fa-moon-o' => 'moon-o', 'fa-mortar-board' => 'mortar-board (alias)', 'fa-music' => 'music', 'fa-navicon' => 'navicon (alias)', 'fa-newspaper-o' => 'newspaper-o', 'fa-openid' => 'openid', 'fa-outdent' => 'outdent', 'fa-pagelines' => 'pagelines', 'fa-paint-brush' => 'paint-brush', 'fa-paper-plane' => 'paper-plane', 'fa-paper-plane-o' => 'paper-plane-o', 'fa-paperclip' => 'paperclip', 'fa-paragraph' => 'paragraph', 'fa-paste' => 'paste (alias)', 'fa-pause' => 'pause', 'fa-paw' => 'paw', 'fa-paypal' => 'paypal', 'fa-pencil' => 'pencil', 'fa-pencil-square' => 'pencil-square', 'fa-pencil-square-o' => 'pencil-square-o', 'fa-phone' => 'phone', 'fa-phone-square' => 'phone-square', 'fa-photo' => 'photo (alias)', 'fa-picture-o' => 'picture-o', 'fa-pie-chart' => 'pie-chart', 'fa-pied-piper' => 'pied-piper', 'fa-pied-piper-alt' => 'pied-piper-alt', 'fa-pinterest' => 'pinterest', 'fa-pinterest-square' => 'pinterest-square', 'fa-plane' => 'plane', 'fa-play' => 'play', 'fa-play-circle' => 'play-circle', 'fa-play-circle-o' => 'play-circle-o', 'fa-plug' => 'plug', 'fa-plus' => 'plus', 'fa-plus-circle' => 'plus-circle', 'fa-plus-square' => 'plus-square', 'fa-plus-square-o' => 'plus-square-o', 'fa-power-off' => 'power-off', 'fa-print' => 'print', 'fa-puzzle-piece' => 'puzzle-piece', 'fa-qq' => 'qq', 'fa-qrcode' => 'qrcode', 'fa-question' => 'question', 'fa-question-circle' => 'question-circle', 'fa-quote-left' => 'quote-left', 'fa-quote-right' => 'quote-right', 'fa-ra' => 'ra (alias)', 'fa-random' => 'random', 'fa-rebel' => 'rebel', 'fa-recycle' => 'recycle', 'fa-reddit' => 'reddit', 'fa-reddit-square' => 'reddit-square', 'fa-refresh' => 'refresh', 'fa-remove' => 'remove (alias)', 'fa-renren' => 'renren', 'fa-reorder' => 'reorder (alias)', 'fa-repeat' => 'repeat', 'fa-reply' => 'reply', 'fa-reply-all' => 'reply-all', 'fa-retweet' => 'retweet', 'fa-rmb' => 'rmb (alias)', 'fa-road' => 'road', 'fa-rocket' => 'rocket', 'fa-rotate-left' => 'rotate-left (alias)', 'fa-rotate-right' => 'rotate-right (alias)', 'fa-rouble' => 'rouble (alias)', 'fa-rss' => 'rss', 'fa-rss-square' => 'rss-square', 'fa-rub' => 'rub', 'fa-ruble' => 'ruble (alias)', 'fa-rupee' => 'rupee (alias)', 'fa-save' => 'save (alias)', 'fa-scissors' => 'scissors', 'fa-search' => 'search', 'fa-search-minus' => 'search-minus', 'fa-search-plus' => 'search-plus', 'fa-send' => 'send (alias)', 'fa-send-o' => 'send-o (alias)', 'fa-share' => 'share', 'fa-share-alt' => 'share-alt', 'fa-share-alt-square' => 'share-alt-square', 'fa-share-square' => 'share-square', 'fa-share-square-o' => 'share-square-o', 'fa-shekel' => 'shekel (alias)', 'fa-sheqel' => 'sheqel (alias)', 'fa-shield' => 'shield', 'fa-shopping-cart' => 'shopping-cart', 'fa-sign-in' => 'sign-in', 'fa-sign-out' => 'sign-out', 'fa-signal' => 'signal', 'fa-sitemap' => 'sitemap', 'fa-skype' => 'skype', 'fa-slack' => 'slack', 'fa-sliders' => 'sliders', 'fa-slideshare' => 'slideshare', 'fa-smile-o' => 'smile-o', 'fa-soccer-ball-o' => 'soccer-ball-o (alias)', 'fa-sort' => 'sort', 'fa-sort-alpha-asc' => 'sort-alpha-asc', 'fa-sort-alpha-desc' => 'sort-alpha-desc', 'fa-sort-amount-asc' => 'sort-amount-asc', 'fa-sort-amount-desc' => 'sort-amount-desc', 'fa-sort-asc' => 'sort-asc', 'fa-sort-desc' => 'sort-desc', 'fa-sort-down' => 'sort-down (alias)', 'fa-sort-numeric-asc' => 'sort-numeric-asc', 'fa-sort-numeric-desc' => 'sort-numeric-desc', 'fa-sort-up' => 'sort-up (alias)', 'fa-soundcloud' => 'soundcloud', 'fa-space-shuttle' => 'space-shuttle', 'fa-spinner' => 'spinner', 'fa-spoon' => 'spoon', 'fa-spotify' => 'spotify', 'fa-square' => 'square', 'fa-square-o' => 'square-o', 'fa-stack-exchange' => 'stack-exchange', 'fa-stack-overflow' => 'stack-overflow', 'fa-star' => 'star', 'fa-star-half' => 'star-half', 'fa-star-half-empty' => 'star-half-empty (alias)', 'fa-star-half-full' => 'star-half-full (alias)', 'fa-star-half-o' => 'star-half-o', 'fa-star-o' => 'star-o', 'fa-steam' => 'steam', 'fa-steam-square' => 'steam-square', 'fa-step-backward' => 'step-backward', 'fa-step-forward' => 'step-forward', 'fa-stethoscope' => 'stethoscope', 'fa-stop' => 'stop', 'fa-strikethrough' => 'strikethrough', 'fa-stumbleupon' => 'stumbleupon', 'fa-stumbleupon-circle' => 'stumbleupon-circle', 'fa-subscript' => 'subscript', 'fa-suitcase' => 'suitcase', 'fa-sun-o' => 'sun-o', 'fa-superscript' => 'superscript', 'fa-support' => 'support (alias)', 'fa-table' => 'table', 'fa-tablet' => 'tablet', 'fa-tachometer' => 'tachometer', 'fa-tag' => 'tag', 'fa-tags' => 'tags', 'fa-tasks' => 'tasks', 'fa-taxi' => 'taxi', 'fa-tencent-weibo' => 'tencent-weibo', 'fa-terminal' => 'terminal', 'fa-text-height' => 'text-height', 'fa-text-width' => 'text-width', 'fa-th' => 'th', 'fa-th-large' => 'th-large', 'fa-th-list' => 'th-list', 'fa-thumb-tack' => 'thumb-tack', 'fa-thumbs-down' => 'thumbs-down', 'fa-thumbs-o-down' => 'thumbs-o-down', 'fa-thumbs-o-up' => 'thumbs-o-up', 'fa-thumbs-up' => 'thumbs-up', 'fa-ticket' => 'ticket', 'fa-times' => 'times', 'fa-times-circle' => 'times-circle', 'fa-times-circle-o' => 'times-circle-o', 'fa-tint' => 'tint', 'fa-toggle-down' => 'toggle-down (alias)', 'fa-toggle-left' => 'toggle-left (alias)', 'fa-toggle-off' => 'toggle-off', 'fa-toggle-on' => 'toggle-on', 'fa-toggle-right' => 'toggle-right (alias)', 'fa-toggle-up' => 'toggle-up (alias)', 'fa-trash' => 'trash', 'fa-trash-o' => 'trash-o', 'fa-tree' => 'tree', 'fa-trello' => 'trello', 'fa-trophy' => 'trophy', 'fa-truck' => 'truck', 'fa-try' => 'try', 'fa-tty' => 'tty', 'fa-tumblr' => 'tumblr', 'fa-tumblr-square' => 'tumblr-square', 'fa-turkish-lira' => 'turkish-lira (alias)', 'fa-twitch' => 'twitch', 'fa-twitter' => 'twitter', 'fa-twitter-square' => 'twitter-square', 'fa-umbrella' => 'umbrella', 'fa-underline' => 'underline', 'fa-undo' => 'undo', 'fa-university' => 'university', 'fa-unlink' => 'unlink (alias)', 'fa-unlock' => 'unlock', 'fa-unlock-alt' => 'unlock-alt', 'fa-unsorted' => 'unsorted (alias)', 'fa-upload' => 'upload', 'fa-usd' => 'usd', 'fa-user' => 'user', 'fa-user-md' => 'user-md', 'fa-users' => 'users', 'fa-video-camera' => 'video-camera', 'fa-vimeo-square' => 'vimeo-square', 'fa-vine' => 'vine', 'fa-vk' => 'vk', 'fa-volume-down' => 'volume-down', 'fa-volume-off' => 'volume-off', 'fa-volume-up' => 'volume-up', 'fa-warning' => 'warning (alias)', 'fa-wechat' => 'wechat (alias)', 'fa-weibo' => 'weibo', 'fa-weixin' => 'weixin', 'fa-wheelchair' => 'wheelchair', 'fa-wifi' => 'wifi', 'fa-windows' => 'windows', 'fa-won' => 'won (alias)', 'fa-wordpress' => 'wordpress', 'fa-wrench' => 'wrench', 'fa-xing' => 'xing', 'fa-xing-square' => 'xing-square', 'fa-yahoo' => 'yahoo', 'fa-yelp' => 'yelp', 'fa-yen' => 'yen (alias)', 'fa-youtube' => 'youtube', 'fa-youtube-play' => 'youtube-play', 'fa-youtube-square' => 'youtube-square');
	if(is_null($icon)){
		return $icons;
	}else{
		$icon = (isset($icons[$icon])) ? $icons[$icon] : null;
		return $icon;
	}
}


function cjpopups_google_fonts_array($font = null){
	$fonts = array("ABeeZee" => "ABeeZee", "Abel" => "Abel", "Abril+Fatface" => "Abril Fatface", "Aclonica" => "Aclonica", "Acme" => "Acme", "Actor" => "Actor", "Adamina" => "Adamina", "Advent+Pro" => "Advent Pro", "Aguafina+Script" => "Aguafina Script", "Akronim" => "Akronim", "Aladin" => "Aladin", "Aldrich" => "Aldrich", "Alef" => "Alef", "Alegreya" => "Alegreya", "Alegreya+SC" => "Alegreya SC", "Alegreya+Sans" => "Alegreya Sans", "Alegreya+Sans+SC" => "Alegreya Sans SC", "Alex+Brush" => "Alex Brush", "Alfa+Slab+One" => "Alfa Slab One", "Alice" => "Alice", "Alike" => "Alike", "Alike+Angular" => "Alike Angular", "Allan" => "Allan", "Allerta" => "Allerta", "Allerta+Stencil" => "Allerta Stencil", "Allura" => "Allura", "Almendra" => "Almendra", "Almendra+Display" => "Almendra Display", "Almendra+SC" => "Almendra SC", "Amarante" => "Amarante", "Amaranth" => "Amaranth", "Amatic+SC" => "Amatic SC", "Amethysta" => "Amethysta", "Anaheim" => "Anaheim", "Andada" => "Andada", "Andika" => "Andika", "Angkor" => "Angkor", "Annie+Use+Your+Telescope" => "Annie Use Your Telescope", "Anonymous+Pro" => "Anonymous Pro", "Antic" => "Antic", "Antic+Didone" => "Antic Didone", "Antic+Slab" => "Antic Slab", "Anton" => "Anton", "Arapey" => "Arapey", "Arbutus" => "Arbutus", "Arbutus+Slab" => "Arbutus Slab", "Architects+Daughter" => "Architects Daughter", "Archivo+Black" => "Archivo Black", "Archivo+Narrow" => "Archivo Narrow", "Arimo" => "Arimo", "Arizonia" => "Arizonia", "Armata" => "Armata", "Artifika" => "Artifika", "Arvo" => "Arvo", "Asap" => "Asap", "Asset" => "Asset", "Astloch" => "Astloch", "Asul" => "Asul", "Atomic+Age" => "Atomic Age", "Aubrey" => "Aubrey", "Audiowide" => "Audiowide", "Autour+One" => "Autour One", "Average" => "Average", "Average+Sans" => "Average Sans", "Averia+Gruesa+Libre" => "Averia Gruesa Libre", "Averia+Libre" => "Averia Libre", "Averia+Sans+Libre" => "Averia Sans Libre", "Averia+Serif+Libre" => "Averia Serif Libre", "Bad+Script" => "Bad Script", "Balthazar" => "Balthazar", "Bangers" => "Bangers", "Basic" => "Basic", "Battambang" => "Battambang", "Baumans" => "Baumans", "Bayon" => "Bayon", "Belgrano" => "Belgrano", "Belleza" => "Belleza", "BenchNine" => "BenchNine", "Bentham" => "Bentham", "Berkshire+Swash" => "Berkshire Swash", "Bevan" => "Bevan", "Bigelow+Rules" => "Bigelow Rules", "Bigshot+One" => "Bigshot One", "Bilbo" => "Bilbo", "Bilbo+Swash+Caps" => "Bilbo Swash Caps", "Bitter" => "Bitter", "Black+Ops+One" => "Black Ops One", "Bokor" => "Bokor", "Bonbon" => "Bonbon", "Boogaloo" => "Boogaloo", "Bowlby+One" => "Bowlby One", "Bowlby+One+SC" => "Bowlby One SC", "Brawler" => "Brawler", "Bree+Serif" => "Bree Serif", "Bubblegum+Sans" => "Bubblegum Sans", "Bubbler+One" => "Bubbler One", "Buda" => "Buda", "Buenard" => "Buenard", "Butcherman" => "Butcherman", "Butterfly+Kids" => "Butterfly Kids", "Cabin" => "Cabin", "Cabin+Condensed" => "Cabin Condensed", "Cabin+Sketch" => "Cabin Sketch", "Caesar+Dressing" => "Caesar Dressing", "Cagliostro" => "Cagliostro", "Calligraffitti" => "Calligraffitti", "Cambay" => "Cambay", "Cambo" => "Cambo", "Candal" => "Candal", "Cantarell" => "Cantarell", "Cantata+One" => "Cantata One", "Cantora+One" => "Cantora One", "Capriola" => "Capriola", "Cardo" => "Cardo", "Carme" => "Carme", "Carrois+Gothic" => "Carrois Gothic", "Carrois+Gothic+SC" => "Carrois Gothic SC", "Carter+One" => "Carter One", "Caudex" => "Caudex", "Cedarville+Cursive" => "Cedarville Cursive", "Ceviche+One" => "Ceviche One", "Changa+One" => "Changa One", "Chango" => "Chango", "Chau+Philomene+One" => "Chau Philomene One", "Chela+One" => "Chela One", "Chelsea+Market" => "Chelsea Market", "Chenla" => "Chenla", "Cherry+Cream+Soda" => "Cherry Cream Soda", "Cherry+Swash" => "Cherry Swash", "Chewy" => "Chewy", "Chicle" => "Chicle", "Chivo" => "Chivo", "Cinzel" => "Cinzel", "Cinzel+Decorative" => "Cinzel Decorative", "Clicker+Script" => "Clicker Script", "Coda" => "Coda", "Coda+Caption" => "Coda Caption", "Codystar" => "Codystar", "Combo" => "Combo", "Comfortaa" => "Comfortaa", "Coming+Soon" => "Coming Soon", "Concert+One" => "Concert One", "Condiment" => "Condiment", "Content" => "Content", "Contrail+One" => "Contrail One", "Convergence" => "Convergence", "Cookie" => "Cookie", "Copse" => "Copse", "Corben" => "Corben", "Courgette" => "Courgette", "Cousine" => "Cousine", "Coustard" => "Coustard", "Covered+By+Your+Grace" => "Covered By Your Grace", "Crafty+Girls" => "Crafty Girls", "Creepster" => "Creepster", "Crete+Round" => "Crete Round", "Crimson+Text" => "Crimson Text", "Croissant+One" => "Croissant One", "Crushed" => "Crushed", "Cuprum" => "Cuprum", "Cutive" => "Cutive", "Cutive+Mono" => "Cutive Mono", "Damion" => "Damion", "Dancing+Script" => "Dancing Script", "Dangrek" => "Dangrek", "Dawning+of+a+New+Day" => "Dawning of a New Day", "Days+One" => "Days One", "Dekko" => "Dekko", "Delius" => "Delius", "Delius+Swash+Caps" => "Delius Swash Caps", "Delius+Unicase" => "Delius Unicase", "Della+Respira" => "Della Respira", "Denk+One" => "Denk One", "Devonshire" => "Devonshire", "Dhurjati" => "Dhurjati", "Didact+Gothic" => "Didact Gothic", "Diplomata" => "Diplomata", "Diplomata+SC" => "Diplomata SC", "Domine" => "Domine", "Donegal+One" => "Donegal One", "Doppio+One" => "Doppio One", "Dorsa" => "Dorsa", "Dosis" => "Dosis", "Dr+Sugiyama" => "Dr Sugiyama", "Droid+Sans" => "Droid Sans", "Droid+Sans+Mono" => "Droid Sans Mono", "Droid+Serif" => "Droid Serif", "Duru+Sans" => "Duru Sans", "Dynalight" => "Dynalight", "EB+Garamond" => "EB Garamond", "Eagle+Lake" => "Eagle Lake", "Eater" => "Eater", "Economica" => "Economica", "Ek+Mukta" => "Ek Mukta", "Electrolize" => "Electrolize", "Elsie" => "Elsie", "Elsie+Swash+Caps" => "Elsie Swash Caps", "Emblema+One" => "Emblema One", "Emilys+Candy" => "Emilys Candy", "Engagement" => "Engagement", "Englebert" => "Englebert", "Enriqueta" => "Enriqueta", "Erica+One" => "Erica One", "Esteban" => "Esteban", "Euphoria+Script" => "Euphoria Script", "Ewert" => "Ewert", "Exo" => "Exo", "Exo+2" => "Exo 2", "Expletus+Sans" => "Expletus Sans", "Fanwood+Text" => "Fanwood Text", "Fascinate" => "Fascinate", "Fascinate+Inline" => "Fascinate Inline", "Faster+One" => "Faster One", "Fasthand" => "Fasthand", "Fauna+One" => "Fauna One", "Federant" => "Federant", "Federo" => "Federo", "Felipa" => "Felipa", "Fenix" => "Fenix", "Finger+Paint" => "Finger Paint", "Fira+Mono" => "Fira Mono", "Fira+Sans" => "Fira Sans", "Fjalla+One" => "Fjalla One", "Fjord+One" => "Fjord One", "Flamenco" => "Flamenco", "Flavors" => "Flavors", "Fondamento" => "Fondamento", "Fontdiner+Swanky" => "Fontdiner Swanky", "Forum" => "Forum", "Francois+One" => "Francois One", "Freckle+Face" => "Freckle Face", "Fredericka+the+Great" => "Fredericka the Great", "Fredoka+One" => "Fredoka One", "Freehand" => "Freehand", "Fresca" => "Fresca", "Frijole" => "Frijole", "Fruktur" => "Fruktur", "Fugaz+One" => "Fugaz One", "GFS+Didot" => "GFS Didot", "GFS+Neohellenic" => "GFS Neohellenic", "Gabriela" => "Gabriela", "Gafata" => "Gafata", "Galdeano" => "Galdeano", "Galindo" => "Galindo", "Gentium+Basic" => "Gentium Basic", "Gentium+Book+Basic" => "Gentium Book Basic", "Geo" => "Geo", "Geostar" => "Geostar", "Geostar+Fill" => "Geostar Fill", "Germania+One" => "Germania One", "Gidugu" => "Gidugu", "Gilda+Display" => "Gilda Display", "Give+You+Glory" => "Give You Glory", "Glass+Antiqua" => "Glass Antiqua", "Glegoo" => "Glegoo", "Gloria+Hallelujah" => "Gloria Hallelujah", "Goblin+One" => "Goblin One", "Gochi+Hand" => "Gochi Hand", "Gorditas" => "Gorditas", "Goudy+Bookletter+1911" => "Goudy Bookletter 1911", "Graduate" => "Graduate", "Grand+Hotel" => "Grand Hotel", "Gravitas+One" => "Gravitas One", "Great+Vibes" => "Great Vibes", "Griffy" => "Griffy", "Gruppo" => "Gruppo", "Gudea" => "Gudea", "Gurajada" => "Gurajada", "Habibi" => "Habibi", "Halant" => "Halant", "Hammersmith+One" => "Hammersmith One", "Hanalei" => "Hanalei", "Hanalei+Fill" => "Hanalei Fill", "Handlee" => "Handlee", "Hanuman" => "Hanuman", "Happy+Monkey" => "Happy Monkey", "Headland+One" => "Headland One", "Henny+Penny" => "Henny Penny", "Herr+Von+Muellerhoff" => "Herr Von Muellerhoff", "Hind" => "Hind", "Holtwood+One+SC" => "Holtwood One SC", "Homemade+Apple" => "Homemade Apple", "Homenaje" => "Homenaje", "IM+Fell+DW+Pica" => "IM Fell DW Pica", "IM+Fell+DW+Pica+SC" => "IM Fell DW Pica SC", "IM+Fell+Double+Pica" => "IM Fell Double Pica", "IM+Fell+Double+Pica+SC" => "IM Fell Double Pica SC", "IM+Fell+English" => "IM Fell English", "IM+Fell+English+SC" => "IM Fell English SC", "IM+Fell+French+Canon" => "IM Fell French Canon", "IM+Fell+French+Canon+SC" => "IM Fell French Canon SC", "IM+Fell+Great+Primer" => "IM Fell Great Primer", "IM+Fell+Great+Primer+SC" => "IM Fell Great Primer SC", "Iceberg" => "Iceberg", "Iceland" => "Iceland", "Imprima" => "Imprima", "Inconsolata" => "Inconsolata", "Inder" => "Inder", "Indie+Flower" => "Indie Flower", "Inika" => "Inika", "Irish+Grover" => "Irish Grover", "Istok+Web" => "Istok Web", "Italiana" => "Italiana", "Italianno" => "Italianno", "Jacques+Francois" => "Jacques Francois", "Jacques+Francois+Shadow" => "Jacques Francois Shadow", "Jim+Nightshade" => "Jim Nightshade", "Jockey+One" => "Jockey One", "Jolly+Lodger" => "Jolly Lodger", "Josefin+Sans" => "Josefin Sans", "Josefin+Slab" => "Josefin Slab", "Joti+One" => "Joti One", "Judson" => "Judson", "Julee" => "Julee", "Julius+Sans+One" => "Julius Sans One", "Junge" => "Junge", "Jura" => "Jura", "Just+Another+Hand" => "Just Another Hand", "Just+Me+Again+Down+Here" => "Just Me Again Down Here", "Kalam" => "Kalam", "Kameron" => "Kameron", "Kantumruy" => "Kantumruy", "Karla" => "Karla", "Karma" => "Karma", "Kaushan+Script" => "Kaushan Script", "Kavoon" => "Kavoon", "Kdam+Thmor" => "Kdam Thmor", "Keania+One" => "Keania One", "Kelly+Slab" => "Kelly Slab", "Kenia" => "Kenia", "Khand" => "Khand", "Khmer" => "Khmer", "Khula" => "Khula", "Kite+One" => "Kite One", "Knewave" => "Knewave", "Kotta+One" => "Kotta One", "Koulen" => "Koulen", "Kranky" => "Kranky", "Kreon" => "Kreon", "Kristi" => "Kristi", "Krona+One" => "Krona One", "La+Belle+Aurore" => "La Belle Aurore", "Laila" => "Laila", "Lakki+Reddy" => "Lakki Reddy", "Lancelot" => "Lancelot", "Lato" => "Lato", "League+Script" => "League Script", "Leckerli+One" => "Leckerli One", "Ledger" => "Ledger", "Lekton" => "Lekton", "Lemon" => "Lemon", "Libre+Baskerville" => "Libre Baskerville", "Life+Savers" => "Life Savers", "Lilita+One" => "Lilita One", "Lily+Script+One" => "Lily Script One", "Limelight" => "Limelight", "Linden+Hill" => "Linden Hill", "Lobster" => "Lobster", "Lobster+Two" => "Lobster Two", "Londrina+Outline" => "Londrina Outline", "Londrina+Shadow" => "Londrina Shadow", "Londrina+Sketch" => "Londrina Sketch", "Londrina+Solid" => "Londrina Solid", "Lora" => "Lora", "Love+Ya+Like+A+Sister" => "Love Ya Like A Sister", "Loved+by+the+King" => "Loved by the King", "Lovers+Quarrel" => "Lovers Quarrel", "Luckiest+Guy" => "Luckiest Guy", "Lusitana" => "Lusitana", "Lustria" => "Lustria", "Macondo" => "Macondo", "Macondo+Swash+Caps" => "Macondo Swash Caps", "Magra" => "Magra", "Maiden+Orange" => "Maiden Orange", "Mako" => "Mako", "Mallanna" => "Mallanna", "Mandali" => "Mandali", "Marcellus" => "Marcellus", "Marcellus+SC" => "Marcellus SC", "Marck+Script" => "Marck Script", "Margarine" => "Margarine", "Marko+One" => "Marko One", "Marmelad" => "Marmelad", "Marvel" => "Marvel", "Mate" => "Mate", "Mate+SC" => "Mate SC", "Maven+Pro" => "Maven Pro", "McLaren" => "McLaren", "Meddon" => "Meddon", "MedievalSharp" => "MedievalSharp", "Medula+One" => "Medula One", "Megrim" => "Megrim", "Meie+Script" => "Meie Script", "Merienda" => "Merienda", "Merienda+One" => "Merienda One", "Merriweather" => "Merriweather", "Merriweather+Sans" => "Merriweather Sans", "Metal" => "Metal", "Metal+Mania" => "Metal Mania", "Metamorphous" => "Metamorphous", "Metrophobic" => "Metrophobic", "Michroma" => "Michroma", "Milonga" => "Milonga", "Miltonian" => "Miltonian", "Miltonian+Tattoo" => "Miltonian Tattoo", "Miniver" => "Miniver", "Miss+Fajardose" => "Miss Fajardose", "Modern+Antiqua" => "Modern Antiqua", "Molengo" => "Molengo", "Molle" => "Molle", "Monda" => "Monda", "Monofett" => "Monofett", "Monoton" => "Monoton", "Monsieur+La+Doulaise" => "Monsieur La Doulaise", "Montaga" => "Montaga", "Montez" => "Montez", "Montserrat" => "Montserrat", "Montserrat+Alternates" => "Montserrat Alternates", "Montserrat+Subrayada" => "Montserrat Subrayada", "Moul" => "Moul", "Moulpali" => "Moulpali", "Mountains+of+Christmas" => "Mountains of Christmas", "Mouse+Memoirs" => "Mouse Memoirs", "Mr+Bedfort" => "Mr Bedfort", "Mr+Dafoe" => "Mr Dafoe", "Mr+De+Haviland" => "Mr De Haviland", "Mrs+Saint+Delafield" => "Mrs Saint Delafield", "Mrs+Sheppards" => "Mrs Sheppards", "Muli" => "Muli", "Mystery+Quest" => "Mystery Quest", "NTR" => "NTR", "Neucha" => "Neucha", "Neuton" => "Neuton", "New+Rocker" => "New Rocker", "News+Cycle" => "News Cycle", "Niconne" => "Niconne", "Nixie+One" => "Nixie One", "Nobile" => "Nobile", "Nokora" => "Nokora", "Norican" => "Norican", "Nosifer" => "Nosifer", "Nothing+You+Could+Do" => "Nothing You Could Do", "Noticia+Text" => "Noticia Text", "Noto+Sans" => "Noto Sans", "Noto+Serif" => "Noto Serif", "Nova+Cut" => "Nova Cut", "Nova+Flat" => "Nova Flat", "Nova+Mono" => "Nova Mono", "Nova+Oval" => "Nova Oval", "Nova+Round" => "Nova Round", "Nova+Script" => "Nova Script", "Nova+Slim" => "Nova Slim", "Nova+Square" => "Nova Square", "Numans" => "Numans", "Nunito" => "Nunito", "Odor+Mean+Chey" => "Odor Mean Chey", "Offside" => "Offside", "Old+Standard+TT" => "Old Standard TT", "Oldenburg" => "Oldenburg", "Oleo+Script" => "Oleo Script", "Oleo+Script+Swash+Caps" => "Oleo Script Swash Caps", "Open+Sans" => "Open Sans", "Open+Sans+Condensed" => "Open Sans Condensed", "Oranienbaum" => "Oranienbaum", "Orbitron" => "Orbitron", "Oregano" => "Oregano", "Orienta" => "Orienta", "Original+Surfer" => "Original Surfer", "Oswald" => "Oswald", "Over+the+Rainbow" => "Over the Rainbow", "Overlock" => "Overlock", "Overlock+SC" => "Overlock SC", "Ovo" => "Ovo", "Oxygen" => "Oxygen", "Oxygen+Mono" => "Oxygen Mono", "PT+Mono" => "PT Mono", "PT+Sans" => "PT Sans", "PT+Sans+Caption" => "PT Sans Caption", "PT+Sans+Narrow" => "PT Sans Narrow", "PT+Serif" => "PT Serif", "PT+Serif+Caption" => "PT Serif Caption", "Pacifico" => "Pacifico", "Paprika" => "Paprika", "Parisienne" => "Parisienne", "Passero+One" => "Passero One", "Passion+One" => "Passion One", "Pathway+Gothic+One" => "Pathway Gothic One", "Patrick+Hand" => "Patrick Hand", "Patrick+Hand+SC" => "Patrick Hand SC", "Patua+One" => "Patua One", "Paytone+One" => "Paytone One", "Peddana" => "Peddana", "Peralta" => "Peralta", "Permanent+Marker" => "Permanent Marker", "Petit+Formal+Script" => "Petit Formal Script", "Petrona" => "Petrona", "Philosopher" => "Philosopher", "Piedra" => "Piedra", "Pinyon+Script" => "Pinyon Script", "Pirata+One" => "Pirata One", "Plaster" => "Plaster", "Play" => "Play", "Playball" => "Playball", "Playfair+Display" => "Playfair Display", "Playfair+Display+SC" => "Playfair Display SC", "Podkova" => "Podkova", "Poiret+One" => "Poiret One", "Poller+One" => "Poller One", "Poly" => "Poly", "Pompiere" => "Pompiere", "Pontano+Sans" => "Pontano Sans", "Port+Lligat+Sans" => "Port Lligat Sans", "Port+Lligat+Slab" => "Port Lligat Slab", "Prata" => "Prata", "Preahvihear" => "Preahvihear", "Press+Start+2P" => "Press Start 2P", "Princess+Sofia" => "Princess Sofia", "Prociono" => "Prociono", "Prosto+One" => "Prosto One", "Puritan" => "Puritan", "Purple+Purse" => "Purple Purse", "Quando" => "Quando", "Quantico" => "Quantico", "Quattrocento" => "Quattrocento", "Quattrocento+Sans" => "Quattrocento Sans", "Questrial" => "Questrial", "Quicksand" => "Quicksand", "Quintessential" => "Quintessential", "Qwigley" => "Qwigley", "Racing+Sans+One" => "Racing Sans One", "Radley" => "Radley", "Rajdhani" => "Rajdhani", "Raleway" => "Raleway", "Raleway+Dots" => "Raleway Dots", "Ramabhadra" => "Ramabhadra", "Ramaraja" => "Ramaraja", "Rambla" => "Rambla", "Rammetto+One" => "Rammetto One", "Ranchers" => "Ranchers", "Rancho" => "Rancho", "Ranga" => "Ranga", "Rationale" => "Rationale", "Ravi+Prakash" => "Ravi Prakash", "Redressed" => "Redressed", "Reenie+Beanie" => "Reenie Beanie", "Revalia" => "Revalia", "Ribeye" => "Ribeye", "Ribeye+Marrow" => "Ribeye Marrow", "Righteous" => "Righteous", "Risque" => "Risque", "Roboto" => "Roboto", "Roboto+Condensed" => "Roboto Condensed", "Roboto+Slab" => "Roboto Slab", "Rochester" => "Rochester", "Rock+Salt" => "Rock Salt", "Rokkitt" => "Rokkitt", "Romanesco" => "Romanesco", "Ropa+Sans" => "Ropa Sans", "Rosario" => "Rosario", "Rosarivo" => "Rosarivo", "Rouge+Script" => "Rouge Script", "Rozha+One" => "Rozha One", "Rubik+Mono+One" => "Rubik Mono One", "Rubik+One" => "Rubik One", "Ruda" => "Ruda", "Rufina" => "Rufina", "Ruge+Boogie" => "Ruge Boogie", "Ruluko" => "Ruluko", "Rum+Raisin" => "Rum Raisin", "Ruslan+Display" => "Ruslan Display", "Russo+One" => "Russo One", "Ruthie" => "Ruthie", "Rye" => "Rye", "Sacramento" => "Sacramento", "Sail" => "Sail", "Salsa" => "Salsa", "Sanchez" => "Sanchez", "Sancreek" => "Sancreek", "Sansita+One" => "Sansita One", "Sarina" => "Sarina", "Sarpanch" => "Sarpanch", "Satisfy" => "Satisfy", "Scada" => "Scada", "Schoolbell" => "Schoolbell", "Seaweed+Script" => "Seaweed Script", "Sevillana" => "Sevillana", "Seymour+One" => "Seymour One", "Shadows+Into+Light" => "Shadows Into Light", "Shadows+Into+Light+Two" => "Shadows Into Light Two", "Shanti" => "Shanti", "Share" => "Share", "Share+Tech" => "Share Tech", "Share+Tech+Mono" => "Share Tech Mono", "Shojumaru" => "Shojumaru", "Short+Stack" => "Short Stack", "Siemreap" => "Siemreap", "Sigmar+One" => "Sigmar One", "Signika" => "Signika", "Signika+Negative" => "Signika Negative", "Simonetta" => "Simonetta", "Sintony" => "Sintony", "Sirin+Stencil" => "Sirin Stencil", "Six+Caps" => "Six Caps", "Skranji" => "Skranji", "Slabo+13px" => "Slabo 13px", "Slabo+27px" => "Slabo 27px", "Slackey" => "Slackey", "Smokum" => "Smokum", "Smythe" => "Smythe", "Sniglet" => "Sniglet", "Snippet" => "Snippet", "Snowburst+One" => "Snowburst One", "Sofadi+One" => "Sofadi One", "Sofia" => "Sofia", "Sonsie+One" => "Sonsie One", "Sorts+Mill+Goudy" => "Sorts Mill Goudy", "Source+Code+Pro" => "Source Code Pro", "Source+Sans+Pro" => "Source Sans Pro", "Source+Serif+Pro" => "Source Serif Pro", "Special+Elite" => "Special Elite", "Spicy+Rice" => "Spicy Rice", "Spinnaker" => "Spinnaker", "Spirax" => "Spirax", "Squada+One" => "Squada One", "Sree+Krushnadevaraya" => "Sree Krushnadevaraya", "Stalemate" => "Stalemate", "Stalinist+One" => "Stalinist One", "Stardos+Stencil" => "Stardos Stencil", "Stint+Ultra+Condensed" => "Stint Ultra Condensed", "Stint+Ultra+Expanded" => "Stint Ultra Expanded", "Stoke" => "Stoke", "Strait" => "Strait", "Sue+Ellen+Francisco" => "Sue Ellen Francisco", "Sunshiney" => "Sunshiney", "Supermercado+One" => "Supermercado One", "Suranna" => "Suranna", "Suravaram" => "Suravaram", "Suwannaphum" => "Suwannaphum", "Swanky+and+Moo+Moo" => "Swanky and Moo Moo", "Syncopate" => "Syncopate", "Tangerine" => "Tangerine", "Taprom" => "Taprom", "Tauri" => "Tauri", "Teko" => "Teko", "Telex" => "Telex", "Tenali+Ramakrishna" => "Tenali Ramakrishna", "Tenor+Sans" => "Tenor Sans", "Text+Me+One" => "Text Me One", "The+Girl+Next+Door" => "The Girl Next Door", "Tienne" => "Tienne", "Timmana" => "Timmana", "Tinos" => "Tinos", "Titan+One" => "Titan One", "Titillium+Web" => "Titillium Web", "Trade+Winds" => "Trade Winds", "Trocchi" => "Trocchi", "Trochut" => "Trochut", "Trykker" => "Trykker", "Tulpen+One" => "Tulpen One", "Ubuntu" => "Ubuntu", "Ubuntu+Condensed" => "Ubuntu Condensed", "Ubuntu+Mono" => "Ubuntu Mono", "Ultra" => "Ultra", "Uncial+Antiqua" => "Uncial Antiqua", "Underdog" => "Underdog", "Unica+One" => "Unica One", "UnifrakturCook" => "UnifrakturCook", "UnifrakturMaguntia" => "UnifrakturMaguntia", "Unkempt" => "Unkempt", "Unlock" => "Unlock", "Unna" => "Unna", "VT323" => "VT323", "Vampiro+One" => "Vampiro One", "Varela" => "Varela", "Varela+Round" => "Varela Round", "Vast+Shadow" => "Vast Shadow", "Vesper+Libre" => "Vesper Libre", "Vibur" => "Vibur", "Vidaloka" => "Vidaloka", "Viga" => "Viga", "Voces" => "Voces", "Volkhov" => "Volkhov", "Vollkorn" => "Vollkorn", "Voltaire" => "Voltaire", "Waiting+for+the+Sunrise" => "Waiting for the Sunrise", "Wallpoet" => "Wallpoet", "Walter+Turncoat" => "Walter Turncoat", "Warnes" => "Warnes", "Wellfleet" => "Wellfleet", "Wendy+One" => "Wendy One", "Wire+One" => "Wire One", "Yanone+Kaffeesatz" => "Yanone Kaffeesatz", "Yellowtail" => "Yellowtail", "Yeseva+One" => "Yeseva One", "Yesteryear" => "Yesteryear", "Zeyada" => "Zeyada");
	if(is_null($font)){
		return $fonts;
	}else{
		$font = (isset($fonts[$font])) ? $fonts[$font] : null;
		return $font;
	}

	/*
	$url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyAxzo22R-bRR2QIMP7KUBUU89xMJkx-jIE';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($ch);
	curl_close($ch);
	$fonts = json_decode($result);
	foreach ($fonts as $key => $font) {
		if(is_array($font)){
			foreach ($font as $key => $fval) {
				$gfonts[] = str_replace(' ', '+', $fval->family);
			}
		}
	}
	*/

}

# Get longitude and latitude with Zip Code
###############################################################################################################
function cjpopups_geo_address_by_zipcode($zip, $only_zip = false){
	$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($zip)."&sensor=false";
	$json = @file_get_contents($url);
	$data = json_decode($json, true);
	$status = $data['status'];
	if($status=="OK"){
		if(count($data['results']) > 0){
			foreach ($data['results'] as $key => $result) {
				$location = $data['results'][$key]['geometry']['location'];
				$address = cjpopups_geo_address_by_latlng($location['lat'], $location['lng']);
				if(is_array($address)){
					foreach ($address as $key => $adval) {
						if($only_zip){
							if($adval['zipcode'] != ''){
								$return[] = $adval;
							}
						}else{
							$return[] = $adval;
						}
					}
				}
			}
			$return = is_array($return) ? array_values($return) : '';
	    }else{
	    	$return = __('No locations found.', 'cjpopups');
	    }
	}else{
	    $return = __('No locations found.', 'cjpopups');
	}
	return $return;
}

#
###############################################################################################################
function cjpopups_geo_address_by_latlng($lat,$lng){
    $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
    $json = @file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    if($status=="OK"){
        if(count($data->results) > 0){
        	foreach ($data->results as $key => $value) {
        		$return[$key]['address'] = $data->results[$key]->formatted_address;
        		$return[$key]['latlng'] = $data->results[$key]->geometry->location;

        		$return[$key]['street_number'] = '';
        		$return[$key]['establishment'] = '';
        		$return[$key]['route'] = '';
        		$return[$key]['locality'] = '';
        		$return[$key]['sublocality'] = '';
        		$return[$key]['city'] = '';
        		$return[$key]['state'] = '';
        		$return[$key]['country'] = '';
        		$return[$key]['zipcode'] = '';

        		foreach ($data->results[$key]->address_components as $akey => $avalue) {
        			if(in_array('locality', $avalue->types)){
        				$return[$key]['locality'] = $avalue->long_name;
        			}
        			if(in_array('sublocality', $avalue->types)){
        				$return[$key]['sublocality'] = $avalue->long_name;
        			}
        			if(in_array('administrative_area_level_2', $avalue->types)){
        				$return[$key]['city'] = $avalue->long_name;
        			}
        			if(in_array('administrative_area_level_1', $avalue->types)){
        				$return[$key]['state'] = $avalue->long_name;
        			}
        			if(in_array('country', $avalue->types)){
        				$return[$key]['country'] = $avalue->long_name;
        			}
        			if(in_array('postal_code', $avalue->types)){
        				$return[$key]['zipcode'] = $avalue->long_name;
        			}
        		}
        	}
        }
    }else{
        $return = __('No locations found.', 'cjpopups');
    }
    return $return;
}

if(cjpopups_item_info('item_type') == 'plugin'){
	$plugin = str_replace('framework/framework.php', 'index.php', plugin_basename( __FILE__ ));
	add_filter( "plugin_action_links_$plugin", 'cjpopups_add_settings_link' );
}

if(cjpopups_item_info('item_id') != 'NA'){
	require_once(sprintf('%s/upgrades/init.php', cjpopups_item_path('includes_dir')));
}
require_once(sprintf('%s/init.php', cjpopups_item_path('includes_dir')));
