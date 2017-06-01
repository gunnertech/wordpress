<?php
// Fill Address
add_action( 'cmb_render_address_by_zipcode', 'cjpopups_cmb_render_address_by_zipcode', 10, 5 );
function cjpopups_cmb_render_address_by_zipcode( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {

    $saved_value = get_post_meta($_GET['post'], $field_args['id'], true);

    $saved_value['zipcode'] = (isset($saved_value['zipcode'])) ? $saved_value['zipcode'] : '';
    $saved_value['address_line_1'] = (isset($saved_value['address_line_1'])) ? $saved_value['address_line_1'] : '';
    $saved_value['address_line_2'] = (isset($saved_value['address_line_2'])) ? $saved_value['address_line_2'] : '';
    $saved_value['city'] = (isset($saved_value['city'])) ? $saved_value['city'] : '';
    $saved_value['state'] = (isset($saved_value['state'])) ? $saved_value['state'] : '';
    $saved_value['country'] = (isset($saved_value['country'])) ? $saved_value['country'] : '';
    $saved_value['lat'] = (isset($saved_value['lat'])) ? $saved_value['lat'] : '';
    $saved_value['lng'] = (isset($saved_value['lng'])) ? $saved_value['lng'] : '';

    echo '<div id="'.$field_args['id'].'_address_panel" style="width:97%;">';

    echo '<p class="" style="margin-bottom:10px;">
          <label for="'.$field_args['id'].'_zipcode">'.__('Zip/Postal Code', 'cjpopups').'</label><br>
          <input id="'.$field_args['id'].'_zipcode" name="'.$field_args['id'].'[zipcode]" value="'.$saved_value['zipcode'].'" type="text" style="width:100%;" autocomplete="off" required class="cjpopups-get-address-zipcode" data-list-address="'.$field_args['id'].'_list_address" data-populate-address="'.$field_args['id'].'_address_form" placeholder="'.__('Type Postal Code to search for address', 'cjpopups').'" />
          </p>';

    echo '<div id="'.$field_args['id'].'_list_address" style="padding:10px; border:1px solid #ddd; background:#FFFFFF; margin:15px 0;">'.__('Type postal code to find address.', 'cjpopups').'</div>';
    echo '<div class="clearfix" id="'.$field_args['id'].'_address_form" style="clear:both; margin-top:20px; background:#f7f7f7; border:1px solid #dddddd; padding:10px 20px;">';
    echo '<p style="margin-bottom:10px;"><label for="'.$field_args['id'].'_address_line_1">'.__('Address Line 1', 'cjpopups').'</label><br><input id="'.$field_args['id'].'_address_line_1" name="'.$field_args['id'].'[address_line_1]" value="'.$saved_value['address_line_1'].'" type="text" style="width:100%;" autocomplete="off" required /></p>';
    echo '<p style="margin-bottom:10px;"><label for="'.$field_args['id'].'_address_line_2">'.__('Address Line 2', 'cjpopups').'</label><br><input id="'.$field_args['id'].'_address_line_2" name="'.$field_args['id'].'[address_line_2]" value="'.$saved_value['address_line_2'].'" type="text" style="width:100%;" autocomplete="off" required /></p>';
    echo '<p style="margin-bottom:10px;"><label for="'.$field_args['id'].'_city">'.__('City', 'cjpopups').'</label><br><input id="'.$field_args['id'].'_city" name="'.$field_args['id'].'[city]" value="'.$saved_value['city'].'" type="text" style="width:100%;" autocomplete="off" required /></p>';
    echo '<p style="margin-bottom:10px;"><label for="'.$field_args['id'].'_state">'.__('State', 'cjpopups').'</label><br><input id="'.$field_args['id'].'_state" name="'.$field_args['id'].'[state]" value="'.$saved_value['state'].'" type="text" style="width:100%;" autocomplete="off" required /></p>';
    echo '<p style="margin-bottom:10px;"><label for="'.$field_args['id'].'_country">'.__('Country', 'cjpopups').'</label><br><input id="'.$field_args['id'].'_country" name="'.$field_args['id'].'[country]" value="'.$saved_value['country'].'" type="text" style="width:100%;" autocomplete="off" required /></p>';

    echo '<p class="one_half" style="margin-bottom:10px;"><label for="'.$field_args['id'].'_lat">'.__('Latitude', 'cjpopups').'</label><br><input id="'.$field_args['id'].'_lat" name="'.$field_args['id'].'[lat]" value="'.$saved_value['lat'].'" type="text" style="width:100%;" autocomplete="off" required /></p>';
    echo '<p class="one_half last" style="margin-bottom:10px;"><label for="'.$field_args['id'].'_lng">'.__('Longitude', 'cjpopups').'</label><br><input id="'.$field_args['id'].'_lng" name="'.$field_args['id'].'[lng]" value="'.$saved_value['lng'].'" type="text" style="width:100%;" autocomplete="off" required /></p>';

    echo '</div>';

    echo '<div>';
    echo $field_type_object->_desc( true );

    cjpopups_cmb_render_address_by_zipcode_script($field_args['id']);

}


function cjpopups_cmb_render_address_by_zipcode_script($el){

    ?>

<script type="text/javascript">
    jQuery(document).ready(function($){

        var elraw = '<?php echo '#'.$el; ?>';
        var $el = $('<?php echo '#'.$el.'_zipcode'; ?>');
        var list_address = '#'+$el.attr('data-list-address');
        var address_form = '#'+$el.attr('data-populate-address');

        $el.on('keyup', function(){
            var zipcode = $(this).val();
            var len = $(this).val().length;
            var list_address = '#'+$(this).attr('data-list-address');
            if(len >= 5){
                var data = {
                    'action': 'cjpopups_cmb_render_address_by_zipcode_ajax_callback',
                    'formdata': {
                        zipcode: zipcode
                    }
                };
                $.post(ajaxurl, data, function(data) {
                    if(data != 0){
                        response = jQuery.parseJSON(data);
                        console.log(list_address);
                        $(list_address).html(response.html);
                    }
                });
            }
            return false;
        });

        $(document).on('click', '.fill-address-form', function(){
            $(elraw+'_address_panel '+elraw+'_address_line_1').val('');
            $(elraw+'_address_panel '+elraw+'_address_line_2').val($(this).attr('data-locality'));
            $(elraw+'_address_panel '+elraw+'_city').val($(this).attr('data-city'));
            $(elraw+'_address_panel '+elraw+'_state').val($(this).attr('data-state'));
            $(elraw+'_address_panel '+elraw+'_country').val($(this).attr('data-country'));
            $(elraw+'_address_panel '+elraw+'_lat').val($(this).attr('data-lat'));
            $(elraw+'_address_panel '+elraw+'_lng').val($(this).attr('data-lng'));
            return false;
        });

        //$(address_form).hide();


    });
</script>


<?php
}


add_action( 'wp_ajax_nopriv_cjpopups_cmb_render_address_by_zipcode_ajax_callback', 'cjpopups_cmb_render_address_by_zipcode_ajax_callback' );
add_action( 'wp_ajax_cjpopups_cmb_render_address_by_zipcode_ajax_callback', 'cjpopups_cmb_render_address_by_zipcode_ajax_callback' );
function cjpopups_cmb_render_address_by_zipcode_ajax_callback() {
    global $wpdb;
    $post_data = $_POST['formdata'];
    $result = cjpopups_geo_address_by_zipcode($post_data['zipcode'], true);
    if(is_array($result)){
        $return['data'] = json_encode($result);
        $return['html'] = '';
        foreach ($result as $key => $value) {
            $data_attrs = '';
            $data_attrs .= 'data-lat="'.$value['latlng']->lat.'" ';
            $data_attrs .= 'data-lng="'.$value['latlng']->lng.'" ';
            $data_attrs .= 'data-locality="'.$value['locality'].'" ';
            $data_attrs .= 'data-city="'.$value['city'].'" ';
            $data_attrs .= 'data-state="'.$value['state'].'" ';
            $data_attrs .= 'data-country="'.$value['country'].'" ';
            $return['html'][$key] = '<p><a class="fill-address-form" '.$data_attrs.' href="#">'.$value['address'].'</a></p>';
        }
        echo json_encode($return);
        die();
    }else{
        $return = 0;
        echo $return;
        die();
    }

}

















