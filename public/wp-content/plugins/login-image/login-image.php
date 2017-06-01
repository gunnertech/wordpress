<?php
/*
Plugin Name: Login Image
Plugin URI: http://premium.wpmudev.org/project/login-image
Description: Allows you to change the login image
Author: Andrew Billits, Ulrich Sossou (Incsub)
Version: 1.1
Author URI: http://premium.wpmudev.org/project/
Network: true
Text_domain: login_image
WDP ID: 169
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

/**
 * Plugin main class
 **/
class Login_Image {

	/**
	 * PHP 4 constructor
	 **/
	function Login_Image() {
		__construct();
	}

	/**
	 * PHP 5 constructor
	 **/
	function __construct() {
		global $wp_version;

		if ( ! is_multisite() )
			add_action( 'admin_menu', array( &$this, 'site_admin_page' ) );
		elseif ( version_compare( $wp_version, '3.0.9', '>' ) )
			add_action( 'network_admin_menu', array( &$this, 'network_admin_page' ) );
		else
			add_action( 'admin_menu', array( &$this, 'pre_3_1_network_admin_page' ) );

		add_action( 'admin_init', array( &$this, 'process' ) );

		add_action( 'login_head', array( &$this, 'stylesheet' ) );

		if ( defined( 'WPMU_PLUGIN_DIR' ) && file_exists( WPMU_PLUGIN_DIR . '/login-image.php' ) ) {
			load_muplugin_textdomain( 'login_image', 'login-image-files/languages' );
		} else {
			load_plugin_textdomain( 'login_image', false, dirname( plugin_basename( __FILE__ ) ) . '/login-image-files/languages' );
		}

		if ( ! is_multisite() ) {
			add_filter( 'login_headerurl', array($this, 'home_url') );
			add_filter( 'login_headertitle', array($this, 'login_headertitle') );
		}
	}

	/**
	 * Add site admin page
	 **/
	function login_headertitle() {
		return esc_attr( bloginfo( 'name' ) );
	}

	function home_url () {
		return home_url();
	}

	/**
	 * Add site admin page
	 **/
	function site_admin_page() {
		add_submenu_page( 'options-general.php', __( 'Login Image', 'login_image' ), __( 'Login Image', 'login_image' ), 'manage_options', 'login-image', array( &$this, 'manage_output' ) );
	}

	/**
	 * Add network admin page
	 **/
	function network_admin_page() {
		add_submenu_page( 'settings.php', __( 'Login Image', 'login_image' ), __( 'Login Image', 'login_image' ), 'manage_network_options', 'login-image', array( &$this, 'manage_output' ) );
	}

	/**
	 * Add network admin page the old way
	 **/
	function pre_3_1_network_admin_page() {
		add_submenu_page( 'ms-admin.php', __( 'Login Image', 'login_image' ), __( 'Login Image', 'login_image' ), 'manage_network_options', 'login-image', array( &$this, 'manage_output' ) );
	}

	function stylesheet() {
		global $current_site;
		if ( file_exists( ABSPATH . 'wp-content/login-image/login-form-image.png' ) ) {
		?>
		<style type="text/css">
			h1 a {
				background: url(<?php echo site_url( 'wp-content/login-image/login-form-image.png', '', 'login' ); ?>) no-repeat !important;
				width: 326px;
				height: 67px;
				text-indent: -9999px;
				overflow: hidden;
				padding-bottom: 15px;
				display: block;
			}
		</style>
		<?php
		}
	}

	function process() {
		global $plugin_page;

		if( ! ( 'login-image' == $plugin_page && isset( $_GET[ 'action' ] ) && 'process' == $_GET[ 'action' ] )  )
			return;

		$referer = remove_query_arg( 'error', wp_get_referer() );
		$referer = remove_query_arg( 'updated', $referer );

		if ( isset( $_POST['Reset'] ) ) {

			$this->remove_file( ABSPATH . 'wp-content/login-image/login-form-image.png' );

		} else {

			$this->remove_file( ABSPATH . 'wp-content/login-image/login-form-image.png' );

			if ( ! is_dir( ABSPATH . 'wp-content/login-image/' ) )
				wp_mkdir_p( ABSPATH . 'wp-content/login-image/' );

			$file = ABSPATH . 'wp-content/login-image/' . basename( $_FILES['login_form_image_file']['name'] );

			$this->remove_file( $file );

			if ( ! move_uploaded_file( $_FILES['login_form_image_file']['tmp_name'], $file ) )
				wp_redirect( add_query_arg( 'error', 'true', $referer ) );

			@chmod( $file, 0777 );

			if ( ! function_exists('imagecreatefromstring') )
				return __('The GD image library is not installed.');

			// Set artificially high because GD uses uncompressed images in memory
			@ini_set('memory_limit', '256M');
			$image = imagecreatefromstring( file_get_contents( $file ) );

			if ( ! is_resource( $image ) )
				wp_redirect( add_query_arg( 'error', 'true', $referer ) );

			$size = @getimagesize( $file );
			if ( ! $size )
				wp_redirect( add_query_arg( 'error', 'true', $referer ) );

			list( $orig_w, $orig_h, $orig_type ) = $size;

			$dims = image_resize_dimensions( $orig_w, $orig_h, 310, 70, true );
			if ( ! $dims )
				$dims = array( 0, 0, 0, 0, $orig_w, $orig_h, $orig_w, $orig_h );
			list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

			$newimage = wp_imagecreatetruecolor( $dst_w, $dst_h );

			imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

			// convert from full colors to index colors, like original PNG.
			if ( IMAGETYPE_PNG == $orig_type && function_exists('imageistruecolor') && !imageistruecolor( $image ) )
				imagetruecolortopalette( $newimage, false, imagecolorstotal( $image ) );

			// we don't need the original in memory anymore
			imagedestroy( $image );

			if ( ! imagepng( $newimage, ABSPATH . 'wp-content/login-image/login-form-image.png' ) )
				wp_redirect( add_query_arg( 'error', 'true', $referer ) );

			imagedestroy( $newimage );

			$stat = stat( ABSPATH . 'wp-content/login-image/' );
			$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
			@chmod( ABSPATH . 'wp-content/login-image/login-form-image.png', $perms );

			$this->remove_file( $file );

		}

		wp_redirect( add_query_arg( 'updated', 'true', $referer ) );
	}

	function manage_output() {
		global $wpdb, $current_site;

		if ( ! current_user_can( 'manage_options' ) ) {
			echo '<p>' . __( 'Nice Try...', 'login_image' ) . '</p>';  //If accessed properly, this message doesn't appear.
			return;
		}

		if ( isset( $_GET['error'] ) )
			echo '<div id="message" class="error fade"><p>' . __( 'There was an error uploading the file, please try again.', 'login_image' ) . '</p></div>';
		elseif ( isset( $_GET['updated'] ) )
			echo '<div id="message" class="updated fade"><p>' . __( 'Changes saved.', 'login_image' ) . '</p></div>';
		?>

		<div class="wrap">
			<h2><?php _e( 'Login Image', 'login_image' ) ?></h2>

			<form action="?page=login-image&action=process" method="post" enctype="multipart/form-data">
				<p><?php _e( 'This is the image that is displayed on the login page (wp-login.php).', 'login_image' ); ?></p>
				<?php
				if ( file_exists( ABSPATH . 'wp-content/login-image/login-form-image.png' ) ) {
					echo '<img src="' . site_url( 'wp-content/login-image/login-form-image.png?' ) . md5( time() ) . '" />';
				} else {
					echo '<img src="' . site_url( 'wp-admin/images/logo-login.gif' ) . '" />';
				}
				?>
				</p>

				<h3><?php _e( 'Change Image', 'login_image' ); ?></h3>
				<p>
					<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
					<input name="login_form_image_file" id="login_form_image_file" size="20" type="file">
				</p>

				<p><?php _e( 'Image must be 500KB maximum. It will be cropped to 310px wide and 70px tall. For best results use an image of this size. Allowed Formats: jpeg, gif, and png', 'login_image' ); ?></p>
				<p><?php _e( 'Note that gif animations will not be preserved.', 'login_image' ); ?></p>

				<p class="submit">
					<input name="Submit" value="<?php _e('Upload') ?>" type="submit">
					<input name="Reset" value="<?php _e('Reset') ?>" type="submit">
				</p>
			</form>
		</div>
	<?php
	}

	/**
	 * Delete a file
	 **/
	function remove_file( $file ) {
		@chmod( $file, 0777 );
		if( @unlink( $file ) ) {
			return true;
		} else {
			return false;
		}
	}

}

new Login_Image;


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
