<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
?><div id="cj-admin-content" class="clearfix"><?php
global $cjpopups_item_vars, $wpdb;
//$cjpopups_addons_path = cjpopups_item_path('item_dir').'/add-ons';
$cjpopups_addons_path = WP_CONTENT_DIR.'/cjpopups-addons/';
if(is_dir($cjpopups_addons_path)){
	$addons_dirs = scandir($cjpopups_addons_path);
	foreach ($addons_dirs as $dir) {
		$addon_dir_path = $cjpopups_addons_path.'/'.$dir;
		if(is_dir($addon_dir_path) && $dir != '.' && $dir != '..'){
			require_once($addon_dir_path.'/addon_setup.php');
		}
	}
	if(isset($cjpopups_item_vars['addons']) && is_array($cjpopups_item_vars['addons'])): ?>
	<table width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th colspan="4">
					<h2 class="main-heading">
						<?php _e('Add-Ons', 'cjpopups'); ?>
						<a target="_blank" class="btn btn-mini btn-success pull-right" href="<?php echo cjpopups_string(admin_url('admin.php?page=cj-products')).'s=cjpopups-addon' ?>">Search Add-ons</a>
					</h2>
				</th>
			</tr>
			<tr>
				<td width="15%" class="bold"><?php _e('Name', 'cjpopups'); ?></td>
				<td width="15%" class="bold"><?php _e('Version', 'cjpopups'); ?></td>
				<td width="50%" class="bold"><?php _e('Directory', 'cjpopups'); ?></td>
				<td width="20%" class="bold"><?php _e('Resources', 'cjpopups'); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($cjpopups_item_vars['addons'] as $key => $value) {
					?>
						<tr>
							<td><a href="<?php echo cjpopups_callback_url($value['slug']) ?>"><?php echo $value['name'] ?></a></td>
							<td><?php echo $value['version'] ?></td>
							<td><?php echo '/wp-content/cjpopups-addons/'.$key ?></td>
							<td> <a target="_blank" href="<?php echo $value['documentation_link'] ?>"><?php _e('Documentation', 'cjpopups') ?></a> &bull; <a target="_blank" href="<?php echo $value['support_link'] ?>"><?php _e('Support', 'cjpopups') ?></a></td>
						</tr>
					<?php
				}
			?>
		</tbody>
	</table>
<?php
	endif;
}?>
</div>