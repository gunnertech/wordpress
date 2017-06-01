<?php
global $cjpopups_item_vars;
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
// Addons
$addons_dir = cjpopups_item_path('item_dir').'/add-ons';
$cjpopups_addon_options = false;
if(is_dir($addons_dir)){
	$dirs = scandir($addons_dir);
	foreach ($dirs as $dir) {
		$dir_path = $addons_dir.'/'.$dir;
		if(is_dir($dir_path) && $dir != '.' && $dir != '..'){
			$file_path = $dir_path.'/options/'.$_GET['callback'].'.php';
			if(file_exists($file_path)){
				$cjpopups_addon_options = $file_path;
				$cjpopups_addon_tabs = $dir_path.'/addon-tabs.php';;
			}
		}
	}
}

// wp-content Addons
$addons_dir = WP_CONTENT_DIR.'/cjpopups-addons';
$cjpopups_addon_options = false;
if(is_dir($addons_dir)){
	$dirs = scandir($addons_dir);
	foreach ($dirs as $dir) {
		$dir_path = $addons_dir.'/'.$dir;
		if(is_dir($dir_path) && $dir != '.' && $dir != '..'){
			$file_path = $dir_path.'/options/'.$_GET['callback'].'.php';
			if(file_exists($file_path)){
				$cjpopups_addon_options = $file_path;
				$cjpopups_addon_tabs = $dir_path.'/addon-tabs.php';;
			}
		}
	}
}

if(!isset($cjpopups_item_vars['localize_variables'])){
	echo '<div class="margin-25-top margin-15-right">';
	echo cjpopups_show_message('error', __('Dropdown key language strings not found in item_setup.php. Specify an array $cjpopups_item_vars["localize_variables"]', 'cjpopups'));
	echo '</div>';
	die();
}
?><div class="wrap">

	<h2><?php echo sprintf(__('%s Settings', 'cjpopups'), ucwords(cjpopups_item_info('item_name'))) ?></h2>

	<nav class="cjpopups-dropdown clearfix">
		<ul>
			<li class="home"><a href="<?php echo cjpopups_callback_url('core_welcome'); ?>" title=""><i class="fa fa-home"></i></a>
				<ul>
					<!-- <li><a href="<?php echo cjpopups_callback_url('core_maintenance_mode'); ?>"><?php _e('Maintenance Mode', 'cjpopups') ?></a></li> -->
					<!-- <li><a href="<?php echo cjpopups_callback_url('core_upgrades'); ?>"><?php _e('Check for Upgrades', 'cjpopups') ?></a></li> -->
					<li><a href="<?php echo cjpopups_callback_url('core_import_export'); ?>"><?php _e('Backup &amp; Restore', 'cjpopups') ?></a></li>
					<li><a href="<?php echo cjpopups_callback_url('core_uninstall'); ?>"><?php _e('Uninstall', 'cjpopups') ?></a></li>
				</ul>
			</li>
			<?php
				$dropdown = cjpopups_item_vars('dropdown');
				foreach ($dropdown as $key => $menu) {
					$li_id = $key;
					if(is_array($menu)){
						$mname = ucwords(str_replace('_', ' ', $key));
						echo '<li id="'.$li_id.'" class="has-sub-menu"><a href="#" onclick="return false;" title="">'.__($mname, 'cjpopups').' <i class="margin-5-left fa fa-caret-down"></i></a>';
						echo '<ul>';
						foreach ($menu as $skey => $sub_menu) {
							echo '<li id="'.$li_id.'"><a href="'.cjpopups_callback_url($skey).'" title="">'.__($sub_menu, 'cjpopups').'</a></li>';
						}
						echo '</ul>';
						echo '</li>';
					}else{
						echo '<li id="'.$li_id.'"><a href="'.cjpopups_callback_url($key).'" title="">'.__($menu, 'cjpopups').'</a></li>';
					}
				}
				do_action('cjpopups_dropdown_hook');
			?>
		</ul>
	</nav>

	<noscript class="no-script">
		<?php _e('Javascript must be enabled.', 'cjpopups') ?>
	</noscript>

	<?php do_action('cjpopups_message_hook'); ?>

	<div id="cj-admin-content" class="clearfix">
		<?php
			if(isset($_GET['page']) && isset($_GET['callback']) && $_GET['callback'] != ''){

				$callback = $_GET['callback'];

				$check_item_options_folder = file_exists(sprintf('%s/'.$callback.'.php', cjpopups_item_path('options_dir')));
				$check_core_options_folder = file_exists(sprintf('%s/includes/options/'.$callback.'.php', cjpopups_item_path('framework_dir')));

				if(!$check_item_options_folder && !$check_core_options_folder && !$cjpopups_addon_options){
					echo cjpopups_message('error', sprintf(__('<b>%s.php</b> not found in options or addons directory.', 'cjpopups'), $callback));
				}else{
					if($check_item_options_folder){
						require_once(sprintf('%s/'.$callback.'.php', cjpopups_item_path('options_dir')));
						global $cjpopups_item_options;
						if(!empty($cjpopups_item_options) && !empty($cjpopups_item_options[$callback])){
							cjpopups_admin_form($cjpopups_item_options[$callback]);
						}
					}elseif($check_core_options_folder){
						require_once(sprintf('%s/includes/options/'.$callback.'.php', cjpopups_item_path('framework_dir')));
						global $cjpopups_item_options;
						if(!empty($cjpopups_item_options) && !empty($cjpopups_item_options[$callback])){
							cjpopups_admin_form($cjpopups_item_options[$callback]);
						}
					}elseif($cjpopups_addon_options){
						require_once($cjpopups_addon_tabs);
						if(!empty($cjpopups_addon_tabs)){
							echo '<ul class="cj-addon-tabs">';
							foreach ($cjpopups_addon_tabs as $key => $menu) {
								$li_id = $key;
								if(is_array($menu)){
									echo '<li id="'.$li_id.'" class="has-sub-menu"><a href="#" onclick="return false;" title="">'.ucwords(str_replace('_', ' ', $key)).' <i class="cj-icon icon-white cj-icon-caret-down"></i></a>';
									echo '<ul>';
									foreach ($menu as $skey => $sub_menu) {
										echo '<li id="'.$li_id.'"><a href="'.cjpopups_callback_url($skey).'" title="">'.$sub_menu.'</a></li>';
									}
									echo '</ul>';
									echo '</li>';
								}else{
									echo '<li id="'.$li_id.'"><a href="'.cjpopups_callback_url($key).'" title="">'.$menu.'</a></li>';
								}
							}
							echo '</ul>';
						}
						require_once($cjpopups_addon_options);
						global $cjpopups_item_options;
						if(!empty($cjpopups_item_options) && !empty($cjpopups_item_options[$callback])){
							cjpopups_admin_form($cjpopups_item_options[$callback]);
						}
					}
				}

			}else{
				$location = cjpopups_callback_url('core_welcome');
				wp_safe_redirect( $location, $status = 302 );
				exit;
			}
		?>
	</div>

</div><!-- wrap -->