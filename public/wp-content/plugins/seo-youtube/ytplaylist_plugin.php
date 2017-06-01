<?php

/*
Plugin Name: Youtube SEO Playlist
Plugin URI: http://www.cfcms.nl/youtube/
Description: Youtube playlist SEO plugin.
Author: Ceasar Feijen
Version: 1.1
Author URI: http://www.cfconsultancy.nl
*/

require_once('ytplaylist_plugin_class.php');

// write default plugin options to the database on plugin activation
register_activation_hook( __FILE__, YoutubePlaylistPlugin::add_handler('hook_plugin_activate') );
register_deactivation_hook( __FILE__, YoutubePlaylistPlugin::add_handler('hook_plugin_deactivate'));

// add plugin settings menu item to the wordpress admin area
add_action( 'admin_menu', YoutubePlaylistPlugin::add_handler('hook_admin_menu') );

// add setting link
function ytplaylist_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    // check to make sure we are on the correct plugin
    if ($file == $this_plugin) {
        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=youtubeplaylist">Settings</a>';
        // add the link to the list
        array_unshift($links, $settings_link);
    }

    return $links;
}

add_filter('plugin_action_links', 'ytplaylist_links', 10, 2);

// [youtube-list feed=keywords sort=relevance cache=false]
add_shortcode( 'youtube-list', YoutubePlaylistPlugin::add_handler('hook_shortcode') );

// add init hook to load plugin scripts and stylesheets
add_action('init', YoutubePlaylistPlugin::add_handler('hook_plugin_init'));

// add JS and css for the admin
if ($_GET['page'] == "youtubeplaylist") {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        wp_register_script('youtubeplaylist-admin', plugins_url( 'lib/youtubeplaylist-admin.js', __FILE__ ), false, '1', true);
        wp_enqueue_script('youtubeplaylist-admin');
        wp_register_style('youtubeplaylist-admin', plugins_url( 'lib/youtubeplaylist-admin.css', __FILE__ ), false, '1', 'screen');
        wp_enqueue_style('youtubeplaylist-admin');
}

/*
add_action('media_buttons','add_sc_select',11);
function add_sc_select(){
    echo "&nbsp;<select id='sc_select'>
                        <option>Shortcode</option>
                        <option value='[youtube-list feed=\"keywords\" keywords=\"HD nature\"]'>[Keywords]</option>
                        <option value='[youtube-list feed=\"username\" username=\"cfconsultancy\"]'>[Username]</option>
                        <option value='[youtube-list feed=\"playlist\" playlist=\"9E912E4AA41E8618\"]'>[Playlist]</option>
        </select>";
}
add_action('admin_head', 'button_js');
function button_js() {
        echo '
        <script type="text/javascript">
        jQuery(document).ready(function(){
           jQuery("#sc_select").change(function() {jQuery("#content").val(jQuery("#content").val()+jQuery("#sc_select :selected").val());})
        });
        </script>';
}
*/



