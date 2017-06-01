<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $cjpopups_item_vars, $wpdb;

/*** Do not change anything in this file unless you know what you are doing ***/

# Item info
####################################################################################################
$cjpopups_item_vars['item_info'] = array(
	'item_type' => 'plugin', // plugin or theme
	'item_id' => 'P6963X66A0', // Unique ID of the item
	'item_name' => 'CSSJockey Unlimited Pop-ups',
	'item_version' => cjpopups_version,
	'text_domain' => 'cjpopups',
	'options_table' => $wpdb->prefix.'cjpopups_options',
	'addon_tables' => null,
	'page_title' => 'Pop-Ups',
	'menu_title' => 'Pop-Ups',
	'page_slug' => 'cjpopups',

	'license_url' => 'http://cssjockey.com/terms-of-use',
	'api_url' => 'http://api.cssjockey.com',
	'recover_password_url' => 'http://cssjockey.com/dashboard/forgot-password/',
	'item_url' => 'http://www.cssjockey.com/shop/wordpress-plugin/wordpress-pop-ups/',
	'premium_membership_url' => 'http://www.cssjockey.com/shop/wordpress-plugin/wordpress-pop-ups/',

	'quick_start_guide_url' => 'http://docs.cssjockey.com/cjpopups/quick-start-guide/',
	'documentation_url' => 'http://docs.cssjockey.com/cjpopups',
	'support_forum_url' => 'http://support.cssjockey.com',
	'feature_request_url' => 'http://support.cssjockey.com',
	'report_bugs_url' => 'http://support.cssjockey.com',
);


# Dropdown items
####################################################################################################
$cjpopups_item_vars['dropdown'] = array(
	'cjpopups_configuration' => __('Plugin Configuration', 'cjpopups'),
	'cjpopups_setup' => __('Manage Pop-Ups', 'cjpopups'),
);

$cjpopups_item_vars["localize_variables"] = array();


# Option Files
####################################################################################################
$cjpopups_item_vars['option_files'] = array(
	'plugin_addon_options',
	'cjpopups_configuration',
);

# Load Modules
####################################################################################################
$cjpopups_item_vars['modules'] = array(
	'functions/global',
	'shortcodes/global',
	'widgets/global',
	'helpers/global',

	'functions/item-assistant',
	'functions/popups-metabox',
);


# Load Extras
####################################################################################################
$cjpopups_item_vars['load_extras'] = array();


# Sidebar Vars
####################################################################################################
$cjpopups_item_vars['sidebar_vars'] = array(
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget' => '</div>',
	'before_title' => '<h3 class="title">',
	'after_title' => '</h3>',
);




# Theme Nav Menus
####################################################################################################
$cjpopups_item_vars['nav_menus'] = array();
/*$cjpopups_item_vars['nav_menus'] = array(
	'cjpopups_visitors_menu' => 'Visitors Only Menu (Membership Modules) <a href="http://docs.cssjockey.com/cjpopups/configuring-nav-menus/" target="_blank">Documentation</a>',
	'cjpopups_users_menu' => 'Users Only Menu (Membership Modules) <a href="http://docs.cssjockey.com/cjpopups/configuring-nav-menus/" target="_blank">Documentation</a>',
);*/


# Database Tables
####################################################################################################
$options_table = $cjpopups_item_vars['item_info']['options_table'];
$cjpopups_item_vars['db_tables']['sql'] = "
	CREATE TABLE IF NOT EXISTS `{$options_table}` (
        `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `option_name` varchar(64) NOT NULL DEFAULT '',
        `option_value` longtext NOT NULL,
        PRIMARY KEY (`option_id`),
        UNIQUE KEY `option_name` (`option_name`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
";

# Recommended or Required Plugins
####################################################################################################
$cjpopups_item_vars['install_plugins'] = array();
/*
$cjpopups_item_vars['install_plugins'] = array(

	// This is an example of how to include a plugin pre-packaged with a theme
	array(
		'name'     				=> 'TGM Example Plugin', // The plugin name
		'slug'     				=> 'tgm-example-plugin', // The plugin slug (typically the folder name)
		'source'   				=> get_stylesheet_directory() . '/lib/plugins/tgm-example-plugin.zip', // The plugin source
		'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
		'version' 				=> '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
		'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
		'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
		'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
	),


	// This is an example of how to include a plugin from the WordPress Plugin Repository
	array(
		'name' 		=> 'WordPress SEO by Yoast',
		'slug' 		=> 'http://wordpress.org/plugins/wordpress-seo/',
		'required' 	=> false,
	),
);
*/

# Custom Post Types
####################################################################################################
//$cjpopups_item_vars['custom_post_types'] = array();
$cjpopups_item_vars['custom_post_types']['popups'] = array(
	'labels' => array(
		'name' => __('Pop-ups', 'cjpopups'),
		'singular_name' => __('Pop-up', 'cjpopups'),
		'add_new' => __('Add New', 'cjpopups'),
		'add_new_item' => __('Add New Pop-up', 'cjpopups'),
		'edit_item' => __('Edit Pop-up', 'cjpopups'),
		'new_item' => __('New Pop-up', 'cjpopups'),
		'view_item' => __('View Pop-up', 'cjpopups'),
		'search_items' => __('Search Pop-ups', 'cjpopups'),
		'not_found' => __('No pop-ups found', 'cjpopups'),
		'not_found_in_trash' => __('No pop-ups found in Trash', 'cjpopups'),
		'parent_item_colon' => ''
	),
	'args' => array(
		'exclude_from_search' => true,
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'has_archive' => false,
		'query_var' => false,
		'rewrite' => array( 'slug' => 'popups', 'with_front' => false, 'hierarchical' => true ),
		'capability_type' => 'post',
		'taxonomies' => array(),
		'hierarchical' => false,
		'menu_icon' => false,
		'menu_position' => 5,
		'supports' => array('title', 'editor')
	)
);


# Custom Taxonomies
####################################################################################################
$cjpopups_item_vars['custom_taxonomies'] = array();
/*$cjpopups_item_vars['custom_taxonomies']['shop'] = array(
	'name' => __('Shop Categories', 'cjpopups'),
    'singular_name' => __('Shop Category', 'cjpopups'),
    'search_items' => __('Search Shop Category', 'cjpopups'),
    'all_items' => __('All Shop Categories', 'cjpopups'),
    'parent_item' => __('Parent Shop Category', 'cjpopups'),
    'parent_item_colon' => __('Parent Shop Category:', 'cjpopups'),
    'edit_item' => __('Edit Shop Category', 'cjpopups'),
    'update_item' => __('Update Shop Category', 'cjpopups'),
    'add_new_item' => __('Add New Shop Category', 'cjpopups'),
    'new_item_name' => __('New Shop Category', 'cjpopups'),
    'post_types' => array('Listings'),
);*/