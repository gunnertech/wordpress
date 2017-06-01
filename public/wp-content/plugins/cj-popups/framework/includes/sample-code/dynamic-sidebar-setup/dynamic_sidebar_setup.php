<?php
# Sync Dynamic Sidebars (if present)
####################################################################################################
$saved_sidebars = cjpopups_get_option('dynamic_sidebars');
if(!empty($saved_sidebars)){
	foreach ($saved_sidebars as $key => $sidebar)
	{
		$sidebar_vars = cjpopups_item_vars('sidebar_vars');
		$args = array(
			'name'          => $sidebar['name'],
			'id'            => $key,
			'description'   => $sidebar['description'],
			'class'         => $sidebar['class'],
			'before_widget' => $sidebar_vars['before_widget'],
			'after_widget'  => $sidebar_vars['after_widget'],
			'before_title'  => $sidebar_vars['before_title'],
			'after_title'   => $sidebar_vars['after_title'],
		);
		register_sidebar($args);
	}
}


# Dynamic Sidebar Meta Box
####################################################################################################
add_filter( 'cmb_meta_boxes', 'cjpopups_custom_sidebar_metabox' );
function cjpopups_custom_sidebar_metabox( array $meta_boxes ) {

	global $wp_registered_sidebars;

	$default_post_types = array('post', 'page');
	$custom_post_types = cjpopups_item_vars('custom_post_types');

	if(!empty($custom_post_types)){
		$custom_post_types = @array_keys($custom_post_types);
		if(is_array($custom_post_types)){
			$show_metabox_on = array_merge($custom_post_types, $default_post_types);
		}else{
			$show_metabox_on = $default_post_types;
		}
	}else{
		$show_metabox_on = $default_post_types;
	}


	if(!empty($wp_registered_sidebars)){
		foreach ($wp_registered_sidebars as $key => $ssidebar) {
			$sidebars[] = array(
							'name' => $ssidebar['name'],
							'value' => $ssidebar['name'],
						);
		}
	}


	// Start with an underscore to hide fields from custom fields list
	$prefix = '_';

	$meta_boxes[] = array(
		'id'         => 'cjpopups_custom_sidebar_metabox',
		'title'      => __('Choose Sidebar', 'cjpopups'),
		'pages'      => $show_metabox_on,
		'context'    => 'side',
		'priority'   => 'default',
		'show_names' => false, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Sidebar',
				'desc' => '',
				'id'   => $prefix . 'custom_sidebar',
				'type' => 'select',
				'options' => $sidebars,
				'std' => 'Blog Sidebar',
			),
		),
	);

	return $meta_boxes;
}


# Add sidebar column on post and pages screen
####################################################################################################
function cjpopups_add_sidebar_column($columns) {
    return array_merge( $columns,
              array('custom_sidebar' => __('Active Sidebar', 'cjpopups')) );
}
add_filter('manage_posts_columns' , 'cjpopups_add_sidebar_column');
add_filter('manage_pages_columns' , 'cjpopups_add_sidebar_column');

function cjpopups_custom_sidebar_columns( $column, $post_id ) {
  switch ( $column ) {
    case "custom_sidebar":
      echo get_post_meta( $post_id, '_custom_sidebar', true);
      break;
  }
}

add_action( "manage_posts_custom_column", "cjpopups_custom_sidebar_columns", 10, 2 );
add_action( "manage_pages_custom_column", "cjpopups_custom_sidebar_columns", 10, 2 );



# Category and Taxonomies Sidebar selection.
####################################################################################################


# CUSTOM SIDEBAR FOR CATEGORIES
add_action('category_edit_form_fields','cjpopups_category_edit_form_fields');
add_action('category_edit_form', 'cjpopups_category_edit_form');
add_action('category_add_form_fields','cjpopups_category_edit_form_fields');
add_action('category_add_form','cjpopups_category_edit_form');
add_action('edited_category', 'cjpopups_save_category_meta');
add_action('create_category', 'cjpopups_save_category_meta');

# CUSTOM SIDEBAR FOR TAGS
add_action('post_tag_edit_form_fields','cjpopups_category_edit_form_fields');
add_action('post_tag_edit_form', 'cjpopups_category_edit_form');
add_action('post_tag_add_form_fields','cjpopups_category_edit_form_fields');
add_action('post_tag_add_form','cjpopups_category_edit_form');
add_action('edited_post_tag', 'cjpopups_save_category_meta');
add_action('create_post_tag', 'cjpopups_save_category_meta');


$custom_taxonomies = cjpopups_item_vars('custom_taxonomies');
if(!empty($custom_taxonomies)){

	if(is_array($custom_taxonomies)){
		foreach ($custom_taxonomies as $key => $taxonomy) {
			# CUSTOM SIDEBAR FOR CUSTOM TAXONOMIES
			add_action($key.'_edit_form_fields','cjpopups_category_edit_form_fields');
			add_action($key.'_edit_form', 'cjpopups_category_edit_form');
			add_action($key.'_add_form_fields','cjpopups_category_edit_form_fields');
			add_action($key.'_add_form','cjpopups_category_edit_form');
			add_action('edited_'.$key, 'cjpopups_save_category_meta');
			add_action('create_'.$key, 'cjpopups_save_category_meta');
		}
	}
}


function cjpopups_save_category_meta($term_id){
	if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "cjpopups_term_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['term_meta'][$key])){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( "cjpopups_term_$t_id", $term_meta );
    }
}

function cjpopups_category_edit_form() {
	echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
		jQuery("#edittag").attr( "enctype", "multipart/form-data" ).attr( "encoding", "multipart/form-data" );
		        });
		</script>
	';
}

function cjpopups_category_edit_form_fields () {
	global $wp_registered_sidebars;
	$sidebar_options = '';
	if(!empty($wp_registered_sidebars)){

		if(isset($_GET['tag_ID'])){
			$saved_term_meta = get_option( 'cjpopups_term_'.$_GET['tag_ID'] );
		}

		foreach ($wp_registered_sidebars as $key => $sidebar) {
			if(isset($_GET['tag_ID'])){
				if($sidebar['name'] == $saved_term_meta['custom_sidebar']){
					$sidebar_options .= '<option selected value="'.$sidebar['name'].'">'.$sidebar['name'].'</option>';
				}else{
					$sidebar_options .= '<option value="'.$sidebar['name'].'">'.$sidebar['name'].'</option>';
				}
			}else{
				if($sidebar['class'] == 'required'){
					$sidebar_options .= '<option selected value="'.$sidebar['name'].'">'.$sidebar['name'].'</option>';
				}else{
					$sidebar_options .= '<option value="'.$sidebar['name'].'">'.$sidebar['name'].'</option>';
				}
			}
		}
	}

	echo '
		<tr class="form-field">
	        <th valign="top" scope="row">
	            <label for="catpic">'.__('Choose Sidebar', 'cjpopups').'</label>
	        </th>
	        <td>
	            <select name="term_meta[custom_sidebar]">
	            	'.$sidebar_options.'
	            </select>
	        </td>
	    </tr>
	';
}