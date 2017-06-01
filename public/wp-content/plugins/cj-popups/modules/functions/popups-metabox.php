<?php
function cjpopups_options_metabox( $meta_boxes ) {
	$prefix = '_cjpopup_'; // Prefix for all fields

	global $wp_roles;
	$roles = $wp_roles->role_names;

	// unset($roles['administrator']);

	$user_roles = array();
	$user_roles[] = array('name' => __('Visitor (Not logged in)', 'cjpopups'), 'value' => 'non-user');
	foreach ($roles as $key => $role) {
		$user_roles[] = array('name' => $role, 'value' => $key);
	}

	if(isset($_POST['post'])){
		$post_info = cjpopups_post_info($_POST['post']);
	}


	$popup_types = array(
		array('name' => __('Bar top', 'cjpopups'), 'value' => 'top-bar'),
		array('name' => __('Bar bottom', 'cjpopups'), 'value' => 'bottom-bar'),
		array('name' => __('Box top right', 'cjpopups'), 'value' => 'box-top-right'),
		array('name' => __('Box top left', 'cjpopups'), 'value' => 'box-top-left'),
		array('name' => __('Box bottom left', 'cjpopups'), 'value' => 'box-bottom-left'),
		array('name' => __('Box bottom right', 'cjpopups'), 'value' => 'box-bottom-right'),
		array('name' => __('Panel right', 'cjpopups'), 'value' => 'panel-right'),
		array('name' => __('Panel left', 'cjpopups'), 'value' => 'panel-left'),
		array('name' => __('Modal Box', 'cjpopups'), 'value' => 'modal-box'),
	);

	$yes_no_array = array(
		array('name' => __('Yes', 'cjpopups'), 'value' => 'yes'),
		array('name' => __('No', 'cjpopups'), 'value' => 'no'),
	);

	$animation_types = array(
		array('name' => 'bounce', 'value' =>'bounce'),
		array('name' => 'flash', 'value' =>'flash'),
		array('name' => 'pulse', 'value' =>'pulse'),
		array('name' => 'rubberBand', 'value' =>'rubberBand'),
		array('name' => 'shake', 'value' =>'shake'),
		array('name' => 'swing', 'value' =>'swing'),
		array('name' => 'tada', 'value' =>'tada'),
		array('name' => 'wobble', 'value' =>'wobble'),


		array('name' => 'bounceIn', 'value' =>'bounceIn'),
		array('name' => 'bounceInDown', 'value' =>'bounceInDown'),
		array('name' => 'bounceInLeft', 'value' =>'bounceInLeft'),
		array('name' => 'bounceInRight', 'value' =>'bounceInRight'),
		array('name' => 'bounceInUp', 'value' =>'bounceInUp'),


		array('name' => 'bounceOut', 'value' =>'bounceOut'),
		array('name' => 'bounceOutDown', 'value' =>'bounceOutDown'),
		array('name' => 'bounceOutLeft', 'value' =>'bounceOutLeft'),
		array('name' => 'bounceOutRight', 'value' =>'bounceOutRight'),
		array('name' => 'bounceOutUp', 'value' =>'bounceOutUp'),


		array('name' => 'fadeIn', 'value' =>'fadeIn'),
		array('name' => 'fadeInDown', 'value' =>'fadeInDown'),
		array('name' => 'fadeInDownBig', 'value' =>'fadeInDownBig'),
		array('name' => 'fadeInLeft', 'value' =>'fadeInLeft'),
		array('name' => 'fadeInLeftBig', 'value' =>'fadeInLeftBig'),
		array('name' => 'fadeInRight', 'value' =>'fadeInRight'),
		array('name' => 'fadeInRightBig', 'value' =>'fadeInRightBig'),
		array('name' => 'fadeInUp', 'value' =>'fadeInUp'),
		array('name' => 'fadeInUpBig', 'value' =>'fadeInUpBig'),


		array('name' => 'fadeOut', 'value' =>'fadeOut'),
		array('name' => 'fadeOutDown', 'value' =>'fadeOutDown'),
		array('name' => 'fadeOutDownBig', 'value' =>'fadeOutDownBig'),
		array('name' => 'fadeOutLeft', 'value' =>'fadeOutLeft'),
		array('name' => 'fadeOutLeftBig', 'value' =>'fadeOutLeftBig'),
		array('name' => 'fadeOutRight', 'value' =>'fadeOutRight'),
		array('name' => 'fadeOutRightBig', 'value' =>'fadeOutRightBig'),
		array('name' => 'fadeOutUp', 'value' =>'fadeOutUp'),
		array('name' => 'fadeOutUpBig', 'value' =>'fadeOutUpBig'),


		array('name' => 'flip', 'value' =>'flip'),
		array('name' => 'flipInX', 'value' =>'flipInX'),
		array('name' => 'flipInY', 'value' =>'flipInY'),
		array('name' => 'flipOutX', 'value' =>'flipOutX'),
		array('name' => 'flipOutY', 'value' =>'flipOutY'),


		array('name' => 'lightSpeedIn', 'value' =>'lightSpeedIn'),
		array('name' => 'lightSpeedOut', 'value' =>'lightSpeedOut'),


		array('name' => 'rotateIn', 'value' =>'rotateIn'),
		array('name' => 'rotateInDownLeft', 'value' =>'rotateInDownLeft'),
		array('name' => 'rotateInDownRight', 'value' =>'rotateInDownRight'),
		array('name' => 'rotateInUpLeft', 'value' =>'rotateInUpLeft'),
		array('name' => 'rotateInUpRight', 'value' =>'rotateInUpRight'),


		array('name' => 'rotateOut', 'value' =>'rotateOut'),
		array('name' => 'rotateOutDownLeft', 'value' =>'rotateOutDownLeft'),
		array('name' => 'rotateOutDownRight', 'value' =>'rotateOutDownRight'),
		array('name' => 'rotateOutUpLeft', 'value' =>'rotateOutUpLeft'),
		array('name' => 'rotateOutUpRight', 'value' =>'rotateOutUpRight'),


		array('name' => 'hinge', 'value' =>'hinge'),
		array('name' => 'rollIn', 'value' =>'rollIn'),
		array('name' => 'rollOut', 'value' =>'rollOut'),


		array('name' => 'zoomIn', 'value' =>'zoomIn'),
		array('name' => 'zoomInDown', 'value' =>'zoomInDown'),
		array('name' => 'zoomInLeft', 'value' =>'zoomInLeft'),
		array('name' => 'zoomInRight', 'value' =>'zoomInRight'),
		array('name' => 'zoomInUp', 'value' =>'zoomInUp'),


		array('name' => 'zoomOut', 'value' =>'zoomOut'),
		array('name' => 'zoomOutDown', 'value' =>'zoomOutDown'),
		array('name' => 'zoomOutLeft', 'value' =>'zoomOutLeft'),
		array('name' => 'zoomOutRight', 'value' =>'zoomOutRight'),
		array('name' => 'zoomOutUp', 'value' =>'zoomOutUp'),
	);

	$meta_boxes[] = array(
		'id' => 'popup_metabox',
		'title' => __('Pop-up Options', 'cjpopups'),
		'pages' => array('popups'), // post type
		'context' => 'advanced',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => __('Show on', 'cjpopups'),
				'desc' => '',
				'id' => $prefix . 'show_on',
				'type' => 'select',
				'options' => array(
					array('name' => __('All pages', 'cjpopups'), 'value' => 'all'),
					array('name' => __('Only categories specified below', 'cjpopups'), 'value' => 'cats'),
					array('name' => __('Only full Urls specified below', 'cjpopups'), 'value' => 'urls'),
				)
			),
			array(
				'name' => __('Show on categories', 'cjpopups'),
				'desc' => __('Select categories to show this popup on category archive pages and posts in this category.', 'cjpopups'),
				'id' => $prefix . 'cats',
				'type' => 'categories',
				'std' => array('in_category' => 'yes', 'is_category' => 'yes'),
			),
			array(
				'name' => __('Page Urls (Include)', 'cjpopups'),
				'desc' => __('Specify each page url per line to display this popup on certain website pages.', 'cjpopups'),
				'id' => $prefix . 'urls',
				'type' => 'textarea'
			),
			array(
				'name' => __('Page Urls (Exclude)', 'cjpopups'),
				'desc' => __('Specify each page url per line to restrict display of this popup.', 'cjpopups'),
				'id' => $prefix . 'urls_exclude',
				'type' => 'textarea'
			),
			array(
				'name' => __('Pop-up Type', 'cjpopups'),
				'desc' => __('Choose pop-up type from various available options.', 'cjpopups'),
				'id' => $prefix . 'type',
				'type' => 'select',
				'options' => $popup_types
			),
			array(
				'name' => __('Show this to', 'cjpopups'),
				'desc' => __('Select user roles to whom this pop-up will be displayed.<br>Defaults to: (everyone) and Administrators will always see the popup.', 'cjpopups'),
				'id' => $prefix . 'roles',
				'type' => 'multicheck',
				'options' => $user_roles
			),
			array(
				'name' => __('User Meta', 'cjpopups'),
				'desc' => __('Display this pop-up only to users where user meta key matches specified user meta value.', 'cjpopups'),
				'id' => $prefix . 'usermeta',
				'type' => 'usermeta',
			),
			array(
				'name' => __('Show backdrop', 'cjpopups'),
				'desc' => __('Choose if you want to show backdrop behind the pop-up or not.', 'cjpopups'),
				'id' => $prefix . 'backdrop',
				'type' => 'select',
				'options' => $yes_no_array,
				'std' => 'yes'
			),
			array(
				'name' => __('Delay in seconds', 'cjpopups'),
				'desc' => __('Specify time in <span class="red">seconds</span> to show this popup.<br>Set its value to 0 to show the pop-up as soon as page loads.', 'cjpopups'),
				'id' => $prefix . 'delay',
				'type' => 'text',
				'std' => '0'
			),
			array(
				'name' => __('Auto-hide after', 'cjpopups'),
				'desc' => __('Specify time in <span class="red">seconds</span> to auto hide this popup.<br>Set its value to 0 to disable auto-hide feature.', 'cjpopups'),
				'id' => $prefix . 'auto_hide',
				'type' => 'text',
				'std' => '0'
			),
			array(
				'name' => __('Hide Close Button?', 'cjpopups'),
				'desc' => __('This is useful with auto hide feature, you can force a pop-up for a few seconds with no choice to close the pop-up.', 'cjpopups'),
				'id' => $prefix . 'hideclosebutton',
				'type' => 'checkbox',
					array(
						'yes' => __('Yes', 'cjpopups')
					),
			),
			array(
				'name' => __('Start Animation', 'cjpopups'),
				'desc' => __('Select animation for display popup.', 'cjpopups'),
				'id' => $prefix . 'animation_in',
				'type' => 'select',
				'options' => $animation_types,
				'std' => 'fadeIn'
			),
			array(
				'name' => __('Close Animation', 'cjpopups'),
				'desc' => __('Select animation for hide popup.', 'cjpopups'),
				'id' => $prefix . 'animation_out',
				'type' => 'select',
				'options' => $animation_types,
				'std' => 'fadeOut'
			),
			array(
				'name' => __('Cookie expire in (days)', 'cjpopups'),
				'desc' => __('<p>When a user click the close button, a cookie will be saved to restrict this popup to show again for the same user.</p>Specify the number of days when the cookie to hide this pop-up will expire.', 'cjpopups'),
				'id' => $prefix . 'cookie_expire',
				'type' => 'text',
			),
			array(
				'name' => __('Start date (optional)', 'cjpopups'),
				'desc' => __('Select start date for this pop-up.', 'cjpopups'),
				'id' => $prefix . 'start_date',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name' => __('End date (optional)', 'cjpopups'),
				'desc' => __('Select end date for this pop-up.', 'cjpopups'),
				'id' => $prefix . 'end_date',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name' => __('Bind Open onClick event', 'cjpopups'),
				'desc' => __('Specify CSS Class or ID element to bind open/show event with this popup.<br>e.g. <code>.classname</code> or <code>#idname</code>', 'cjpopups'),
				'id' => $prefix . 'click',
				'type' => 'text',
			),
			array(
				'name' => __('Bind Close onClick event', 'cjpopups'),
				'desc' => __('Specify CSS Class element to bind close event with this popup with onClick.<br>e.g. <code>.classname</code>', 'cjpopups'),
				'id' => $prefix . 'close_click',
				'type' => 'text',
			),
			array(
				'name' => __('Bind with Right Mouse Click (optional)', 'cjpopups'),
				'desc' => __('If enabled, pop-up will show only with right mouse click on the page its set to display.<p class="red">Be aware, that basic right click functionality will not work on the page.</p>', 'cjpopups'),
				'id' => $prefix . 'right_click',
				'type' => 'select',
				'options' => $yes_no_array,
				'std' => 'no'
			),
			array(
				'name' => __('Enable hide with ESC key?', 'cjpopups'),
				'desc' => __('If enabled, user can hide the pop-up by pressing ESC key.', 'cjpopups'),
				'id' => $prefix . 'esc_key',
				'type' => 'select',
				'options' => $yes_no_array,
				'std' => 'no'
			),
			array(
				'name' => __('Enable Anywhere click?', 'cjpopups'),
				'desc' => __('If enabled, pop-up will hide if a user click outside the pop-up container.', 'cjpopups'),
				'id' => $prefix . 'anywhere_click_close',
				'type' => 'select',
				'options' => $yes_no_array,
				'std' => 'no'
			),
			array(
				'name' => __('Close button text', 'cjpopups'),
				'desc' => __('You can change close button text for this pop-up here', 'cjpopups'),
				'id' => $prefix . 'close_text',
				'type' => 'text',
				'std' => 'x',
			),
			array(
				'name' => __('Disable Page Scroll?', 'cjpopups'),
				'desc' => '',
				'id' => $prefix . 'disable_body_scroll',
				'type' => 'checkbox',
				array(
					'yes' => __('Yes', 'cjpopups')
				),
			),
			array(
				'name' => __('Hide on desktops', 'cjpopups'),
				'desc' => '',
				'id' => $prefix . 'desktop_class',
				'type' => 'checkbox',
				array(
					'yes' => __('Yes', 'cjpopups')
				),
			),
			array(
				'name' => __('Hide on tablets', 'cjpopups'),
				'desc' => '',
				'id' => $prefix . 'tablet_class',
				'type' => 'checkbox',
				array(
					'yes' => __('Yes', 'cjpopups')
				),
			),
			array(
				'name' => __('Hide on phones', 'cjpopups'),
				'desc' => '',
				'id' => $prefix . 'phone_class',
				'type' => 'checkbox',
				array(
					'yes' => __('Yes', 'cjpopups')
				),
			),
			array(
			    'name' => __('Pop-up Styles', 'cjpopups'),
			    'desc' => 'Here you can stylize the look of this popup.',
			    'type' => 'title',
			    'id' => $prefix . 'style_title'
			),
			array(
			    'name' => __('Popup Background Color', 'cjpopups'),
			    'desc' => __('You must choose a color, clearing this field will set to default color.', 'cjpopups'),
			    'id'   => $prefix . 'bgcolor',
			    'type' => 'colorpicker',
			    'default'  => '#ffffff',
			    'repeatable' => false,
			),
			array(
			    'name' => __('Popup Text Color', 'cjpopups'),
			    'desc' => __('You must choose a color, clearing this field will set to default color.', 'cjpopups'),
			    'id'   => $prefix . 'textcolor',
			    'type' => 'colorpicker',
			    'default'  => '#333333',
			    'repeatable' => false,
			),
			array(
			    'name' => __('Popup Link Color', 'cjpopups'),
			    'desc' => __('You must choose a color, clearing this field will set to default color.', 'cjpopups'),
			    'id'   => $prefix . 'linkcolor',
			    'type' => 'colorpicker',
			    'default'  => '#4e8ef7',
			    'repeatable' => false,
			),
			array(
			    'name' => __('Backdrop Background Color', 'cjpopups'),
			    'desc' => __('You must choose a color, clearing this field will set to default color.', 'cjpopups'),
			    'id'   => $prefix . 'backdropcolor',
			    'type' => 'colorpicker',
			    'default'  => '#222222',
			    'repeatable' => false,
			),
			array(
			    'name' => __('Backdrop Background Opacity', 'cjpopups'),
			    'desc' => __('Specify backdrop opacity between 0 to 100.<br>Example: 90', 'cjpopups'),
			    'id'   => $prefix . 'backdropopacity',
			    'type' => 'text',
			    'default'  => (isset($post_info[$prefix . 'backdropopacity'])) ? $post_info[$prefix . 'backdropopacity'] : '90',
			    'repeatable' => false,
			),
			array(
			    'name' => __('Close Button Background Color', 'cjpopups'),
			    'desc' => __('You must choose a color, clearing this field will set to default color.', 'cjpopups'),
			    'id'   => $prefix . 'closebgcolor',
			    'type' => 'colorpicker',
			    'default'  => '#222222',
			    'repeatable' => false,
			),
			array(
			    'name' => __('Close Button Text Color', 'cjpopups'),
			    'desc' => __('You must choose a color, clearing this field will set to default color.', 'cjpopups'),
			    'id'   => $prefix . 'closetextcolor',
			    'type' => 'colorpicker',
			    'default'  => '#FFFFFF',
			    'repeatable' => false,
			),
			array(
				'name' => __('Content padding', 'cjpopups'),
				'desc' => __('Specify padding in pixels for the content container.<br>Example: 20', 'cjpopups'),
				'id' => $prefix . 'padding',
				'type' => 'text',
				'std' => '0'
			),
			array(
				'name' => __('Width', 'cjpopups'),
				'desc' => __('Specify width in pixels or percentage for the pop-up, You can leave blank to use default value as per plugin.<br>Example: 100% or 300px', 'cjpopups'),
				'id' => $prefix . 'width',
				'type' => 'text'
			),
			array(
				'name' => __('Height', 'cjpopups'),
				'desc' => __('Specify height in pixels for the pop-up, You can leave blank to use default value as per plugin.<br>Example: 300px', 'cjpopups'),
				'id' => $prefix . 'height',
				'type' => 'text'
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'cjpopups_options_metabox' );


function cjpopups_html_metabox( $meta_boxes ) {
	$prefix = '_cjpopup_'; // Prefix for all fields

	$meta_boxes[] = array(
		'id' => 'popup_html_metabox',
		'title' => __('Raw HTML', 'cjpopups'),
		'pages' => array('popups'), // post type
		'context' => 'advanced',
		'priority' => 'high',
		'show_names' => false, // Show field names on the left
		'fields' => array(
			array(
				'name' => __('Raw HTML Code', 'cjpopups'),
				'desc' => __('You can specify Raw HTML, CSS and Javascript code in this box.', 'cjpopups'),
				'id' => $prefix . 'html',
				'type' => 'textarea_code'
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'cjpopups_html_metabox' );




// Creates User meta field
add_action( 'cmb_render_usermeta', 'cmb_render_usermeta_field', 10, 5 );

function cmb_render_usermeta_field( $field_args, $value, $object_id, $object_type, $field_type_object ) {

	global $wpdb, $current_user;

    $value = wp_parse_args( $value, array(
        'metakey'     => '',
        'metavalue'       => '',
    ));

    $metakey_query = $wpdb->get_results("SELECT DISTINCT meta_key FROM $wpdb->usermeta ORDER BY meta_key ASC");
    $user_meta_key_array = '<option value="none">'.__('Not applicable', 'cjpopups').'</option>';
    foreach ( $metakey_query as $key => $metakey ) {
    	$check_serialized = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE meta_key = '{$metakey->meta_key}'");
    	if(!is_serialized($check_serialized->meta_value)){
        	$user_meta_key_array .= '<option value="'. $metakey->meta_key .'" '. selected( $value['metakey'], $metakey->meta_key, false ) .'>'. $metakey->meta_key .'</option>';
        }
    }

    ?>
    <div class="alignleft" style="margin-right:10px;"><p><label for="<?php echo $field_type_object->_id( '_metakey' ); ?>'"><?php _e('Meta Key', 'cjpopups') ?></label></p>
        <?php echo $field_type_object->select( array(
            'name'    => $field_type_object->_name( '[metakey]' ),
            'id'      => $field_type_object->_id( '_metakey' ),
            'desc'    => '',
            'options' => $user_meta_key_array,
        ) ); ?>
    </div>
    <div class="alignleft" style="margin-right:10px;"><p><label for="<?php echo $field_type_object->_id( '_metavalue' ); ?>'"><?php _e('Meta Value', 'cjpopups') ?></label></p>
        <?php echo $field_type_object->input( array(
            'class' => 'cmb_text_small',
            'name'  => $field_type_object->_name( '[metavalue]' ),
            'id'    => $field_type_object->_id( '_metavalue' ),
            'value' => $value['metavalue'],
            'desc'  => '',
        ) ); ?>
    </div>
    <br style="clear:both;">
    <?php
    echo $field_type_object->_desc( true );

}


// Creates User meta field
add_action( 'cmb_render_categories', 'cmb_render_categories_field', 10, 5 );

function cmb_render_categories_field( $field_args, $value, $object_id, $object_type, $field_type_object ) {

	global $wpdb, $current_user;

	$value = wp_parse_args( $value, array());

	$categories = get_categories(array('hide_empty' => 0));
	$none_selected = (in_array('none', $value)) ? 'selected' : '';
	$categories_array = '<option '.$none_selected.' value="none">'.__('Not Applicable', 'cjpopups').'</option>';
	foreach ($categories as $key => $cat) {
		if(in_array($cat->term_id, $value) && !in_array('none', $value)){
			$selected = 'selected';
		}else{
			$selected = '';
		}

		$categories_array .= '<option value="'. $cat->term_id .'" '.$selected.'>'. $cat->name .'</option>';
	}

	echo $field_type_object->select(array(
		'name'    => $field_type_object->_name('[]'),
		'id'      => $field_type_object->_id(),
		'desc'      => '',
		'class'      => 'cjpopups-metabox-multiselect',
		'multiple'    => true,
		'options' => $categories_array,
	));

	echo '<br>';

	echo '<div style="margin:10px 0;">';
	$is_category_checked = (isset($value['is_category']) && $value['is_category'] == 'yes') ? 'checked' : '';
	echo '<label><input '.$is_category_checked.' name="'.$field_type_object->_name('[is_category]').'" type="checkbox" value="yes" /> '.__('Show on category archive pages', 'cjpopups').'</label>';
	echo '</div>';

	echo '<div style="margin:10px 0;">';
	$in_category_checked = (isset($value['in_category']) && $value['in_category'] == 'yes') ? 'checked' : '';
	echo '<label><input '.$in_category_checked.' name="'.$field_type_object->_name('[in_category]').'" type="checkbox" value="yes" /> '.__('Show on single post pages', 'cjpopups').'</label>';
	echo '</div>';

	echo $field_type_object->_desc( true );
}




