<?php
// Fill Address
add_action( 'cmb_render_screenshots', 'cjpopups_cmb_render_screenshots', 10, 5 );
function cjpopups_cmb_render_screenshots( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {

    $saved_value = get_post_meta($object_id, $field_args['id'], true);
    $saved_value = ($saved_value == '') ? $field_args['default'] : $saved_value;
    echo '<div id="'.$field_args['id'].'_screenshots" style="width:97%;" class="clearfix">';
    foreach ($field_args['options'] as $key => $value) {
        if($value['name'] == $saved_value){
            echo '<input value="'.$value['name'].'" checked type="radio" id="'.$field_args['id'].'_'.sanitize_title($value['value']).'" name="'.$field_args['id'].'" style="display:none;">';
        }else{
            echo '<input value="'.$value['name'].'" type="radio" id="'.$field_args['id'].'_'.sanitize_title($value['value']).'" name="'.$field_args['id'].'" style="display:none;">';
        }
        echo '<label for="'.$field_args['id'].'_'.sanitize_title($value['value']).'">';
        echo '<img src="'.$field_args['images_path'].$value['value'].'" width="100px" height="100px" />';
        echo '</label>';
    }
    echo '<div>';
    echo $field_type_object->_desc( true );
    echo cjpopups_cmb_render_screenshots_styles($field_args['id']);
}



function cjpopups_cmb_render_screenshots_styles($id){ ?>
    <style type="text/css">

    #<?php echo $id; ?>_screenshots label{
        background: #ffffff;
        border:1px solid #ddd;
        padding:5px;
        line-height:1;
        margin:0 10px 10px 0;
        float:left;
    }
    #<?php echo $id; ?>_screenshots img{
        line-height:1;
    }
    #<?php echo $id; ?>_screenshots input[type="radio"]:checked + label{
        border: 1px solid #444444;
    }
    </style>
<?php }