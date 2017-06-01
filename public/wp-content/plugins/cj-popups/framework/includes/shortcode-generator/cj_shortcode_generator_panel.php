<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
	global $shortcode_tags;
	$select_shortcodes = null;
	$shortcode_options = null;
	$cj_products_text_domains = @array_keys(cjpopups_list_submenu_admin_pages('cj-products'));
	if(!empty($shortcode_tags)){
		foreach ($shortcode_tags as $key => $value) {
			$tag = explode('_', $key);
			if(@in_array($tag[0], $cj_products_text_domains)){
				$select_shortcodes[$tag[0]][] = $value;
				$shortcode_options[$value] = @unserialize(cjpopups_do_shortcode( '['.$value.' return="options"]' ));
			}
		}
	}

	$cj_products_array[] = '<option value="0">'.__('Select Product', 'cjpopups').'</option>';
	if(is_array($shortcode_options)){
		foreach ($shortcode_options as $pkey => $product) {
			$product_slug = explode('_', $pkey);
			$func_name = $product_slug[0].'_item_info';
			$item_name = $func_name('item_name');
			//echo '<pre>'; print_r($item_name); echo '</pre>';
			$cj_products_array[$product_slug[0]] = '<option value="'.$product_slug[0].'">'.$item_name.'</option>';
		}
	}
?>

<div id="cj-shortcode-generator-panel" style="display: none;">
	<div id="cj-admin-content">
		<div class="panel panel-default" style="border-bottom: none; box-shadow: none;">
			<div class="panel-heading no-border-radius">
				<h2 class="panel-title">
					<a href="#" class="pull-right close close-cj-shortcode-generator-panel"><i class="fa fa-times"></i></a>
					<?php _e('CSSJockey Shortcode Generator', 'cjpopups') ?>
				</h2>
			</div>
			<div class="panel-body" style="border-bottom: 1px solid #ddd;">
				<div class="clearfix">
					<select id="cj-sg-select-product" style="width: 100%;">
						<?php echo implode("\n", $cj_products_array) ?>
					</select>
					<select id="cj-sg-select-shortcode" data-no-shortcode="<?php _e('Select a shortcode to configure settings and insert.', 'cjpopups'); ?>" style="width: 100%; display:none; margin-top:10px;"></select>
				</div>
			</div>

			<div class="panel-body">
				<div class="cj-sg-loading-icon" style="display:none;"><i class="fa fa-spinner fa-spin"></i></div>
				<div id="cj-sg-dynamic-content" class="clearfix" style="display:none;">
					<div class="alert alert-warning">
						<?php _e('Select a shortcode to configure settings and insert.', 'cjpopups'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="cj-shortcode-generator-backdrop" style="display: none;"></div>