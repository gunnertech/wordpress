<?php
/*
Plugin Name: Remove WP Dashboard Widgets
Plugin URI: http://premium.wpmudev.org/project/remove-wordpress-dashboard-widgets
Description: Removes the wordpress dashboard widgets
Author: Andrew Billits, Ulrich Sossou
Version: 1.0.3
Author URI: http://premium.wpmudev.org/
WDP ID: 172
*/

/*
Copyright 2007-2011 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_action( 'wp_dashboard_setup', 'remove_wp_dashboard_widgets' );

function remove_wp_dashboard_widgets() {
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
}


/**
 * Show notification if WPMUDEV Update Notifications plugin is not installed
 **/
if ( !function_exists( 'wdp_un_check' ) ) {
	add_action( 'admin_notices', 'wdp_un_check', 5 );
	add_action( 'network_admin_notices', 'wdp_un_check', 5 );

	function wdp_un_check() {
		if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
			echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</p></div>';
	}
}
