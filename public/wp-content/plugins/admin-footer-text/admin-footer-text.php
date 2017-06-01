<?php
/*
Plugin Name: Admin Footer Text
Plugin URI: http://premium.wpmudev.org/project/admin-footer-text
Description: Display text in admin dashboard footer
Author: S H Mohanjith (Incsub), Andrew Billits (Incsub)
Version: 1.0.8
Author URI: http://premium.wpmudev.org
WDP ID: 53
*/

/*
Copyright 2007-2009 Incsub (http://incsub.com)

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

$admin_footer_text_default = '';

/**
 * Escaping for textarea values.
 *
 * @since 3.1
 *
 * Added for compatibility with WordPress 3.0.*
 *
 * @param string $text
 * @return string
 */
if( !function_exists( 'esc_textarea' ) ) {
	function esc_textarea( $text ) {
		$safe_text = htmlspecialchars( $text, ENT_QUOTES );
		return apply_filters( 'esc_textarea', $safe_text, $text );
	}
}

class Admin_Footer_Text {
	function Admin_Footer_Text() {
		__construct();
	}

	function __construct() {
		add_action( 'wpmu_options', array( &$this, 'site_admin_options' ) );
		add_action( 'update_wpmu_options', array( &$this, 'site_admin_options_process' ) );
		add_action( 'admin_init', array( &$this, 'add_settings_field' ) );
		add_filter( 'admin_footer_text', array( &$this, 'output' ), 1, 1 );

		if ( defined( 'WPMU_PLUGIN_DIR' ) && file_exists( WPMU_PLUGIN_DIR . '/admin-footer-text.php' ) ) {
			load_muplugin_textdomain( 'admin_footer_text', 'admin-footer-text/languages' );
		} else {
			load_plugin_textdomain( 'admin_footer_text', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}
	}

	function site_admin_options_process() {
		update_site_option( 'admin_footer_text' , stripslashes( $_POST['admin_footer_text'] ) );
	}

	function output( $footer_text ) {
		global $admin_footer_text_default;

		if ( $this->is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
			$admin_footer_text = get_site_option( 'admin_footer_text' );
		else
			$admin_footer_text = get_option( 'admin_footer_text' );

		if ( empty( $admin_footer_text ) ) {
			$footer_text = $admin_footer_text_default;
		} else {
			$footer_text = $admin_footer_text;
		}
		return $footer_text;
	}

	function site_admin_options() {
		if( ! $this->is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
			return;

		global $admin_footer_text_default;
		$admin_footer_text = get_site_option('admin_footer_text');
		if ( empty( $admin_footer_text ) )
			$admin_footer_text = $admin_footer_text_default;
		?>
			<h3><?php _e( 'Admin Panel Footer Settings', 'admin_footer_text' ) ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Footer Text', 'admin_footer_text' ) ?></th>
					<td>
						<textarea name="admin_footer_text" type="text" rows="5" wrap="soft" id="admin_footer_text" style="width: 95%"/><?php echo esc_textarea( $admin_footer_text ); ?></textarea>
						<br />
						<?php _e( 'HTML Allowed.', 'admin_footer_text' ) ?>
					</td>
				</tr>
			</table>
		<?php
	}

	/**
	 * Add setting field for singlesite
	 **/
	function add_settings_field() {
		if( $this->is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
			return;

		add_settings_section( 'admin_footer_text_setting_section', __( 'Admin Footer Text', 'admin_footer_text' ), '__return_false', 'general' );

		add_settings_field( 'admin_footer_text', __( 'Footer Text', 'admin_footer_text' ), array( &$this, 'site_option' ), 'general', 'admin_footer_text_setting_section' );

		register_setting( 'general', 'admin_footer_text' );
	}

	/**
	 * Setting field for singlesite
	 **/
	function site_option() {
		global $admin_footer_text_default;

		$admin_footer_text = get_option( 'admin_footer_text' );
		if ( empty( $admin_footer_text ) )
			$admin_footer_text = $admin_footer_text_default;

		echo '<textarea name="admin_footer_text" type="text" rows="5" wrap="soft" id="admin_footer_text" style="width: 95%" />' . esc_textarea( $admin_footer_text ) . '</textarea>
		<p class="description"> ' . __( 'HTML Allowed.', 'admin_footer_text' ) . '</p>';
	}

	/**
	 * Verify if plugin is network activated
	 **/
	function is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() )
			return false;

		$plugins = get_site_option( 'active_sitewide_plugins');
		if ( isset($plugins[$plugin]) )
			return true;

		return false;
	}

}

new Admin_Footer_Text;


if ( !function_exists( 'wdp_un_check' ) ) {
	add_action( 'admin_notices', 'wdp_un_check', 5 );
	add_action( 'network_admin_notices', 'wdp_un_check', 5 );

	function wdp_un_check() {
		if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
			echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
	}
}
