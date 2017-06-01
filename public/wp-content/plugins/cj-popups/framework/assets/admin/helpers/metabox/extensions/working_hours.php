<?php
// Longitude Latitude
add_action( 'cmb_render_working_hours', 'cjpopups_cmb_render_working_hours', 10, 5 );
function cjpopups_cmb_render_working_hours( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {

    $saved_value = get_post_meta($_GET['post'], $field_args['id'], true);
    $days_array = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

    echo '<div class="clearfix">';

    echo '<p class="clearfix">
             <span style="display:block; margin-bottom:5px; float:left; width:150px; font-weight:bold;">
             <label style="width:125px; font-weight:bold;">'.__('Day', 'cjpopups').'</label>
             </span>
             <span style="display:block; margin-bottom:5px; float:left; width:150px; font-weight:bold;">'.__('Starttime', 'cjpopups').'</span>
             <span style="display:block; margin-bottom:5px; float:left; width:150px; font-weight:bold;">'.__('Endtime', 'cjpopups').'</span>
          </p>';
    foreach ($days_array as $key => $value) {
        $parsed_key = $value;
        $open = (isset($saved_value[$parsed_key]) && $saved_value[$parsed_key] == 'open') ? 'checked' : '';
        $starttime = (isset($saved_value[$parsed_key.'_starttime'])) ? $saved_value[$parsed_key.'_starttime'] : '';
        $endtime = (isset($saved_value[$parsed_key.'_endtime'])) ? $saved_value[$parsed_key.'_endtime'] : '';
        echo '<p class="clearfix">
                <span style="width:150px; display:block; float:left;">
                  <input class="'.$field_args['id'].'_day_checkbox" '.$open.' id="'.$field_args['id'].'_'.$parsed_key.'" name="'.$field_args['id'].'['.$parsed_key.']" type="checkbox" value="open" />
                  <label style="width: 100px;" for="'.$field_args['id'].'_'.$parsed_key.'">'.$value.'</label>
                </span>
              <span style="width:150px; display:block; float:left;"><input name="'.$field_args['id'].'['.$parsed_key.'_starttime]" type="text" class="cmb_timepicker text_time" placeholder="'.__('Start Time', 'cjpopups').'" value="'.$starttime.'"></span>
              <span style="width:150px; display:block; float:left;"><input name="'.$field_args['id'].'['.$parsed_key.'_endtime]" type="text" class="cmb_timepicker text_time" placeholder="'.__('End Time', 'cjpopups').'" value="'.$endtime.'"></span>
              </p>';
    }
    echo '</div>';
    echo $field_type_object->_desc( true );
    echo cjpopups_cmb_render_working_hours_script($field_args['id'].'_day_checkbox');
}

function cjpopups_cmb_render_working_hours_script($el){
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            var el = '<?php echo $el; ?>';
            var $el = $('.'+el);
            $el.each(function(){
                if($(this).attr('checked') != undefined){
                    $(this).parent().find('input[type="text"]').attr('required', true);
                }else{
                    $(this).parent().find('input[type="text"]').removeAttr('required');
                }
                $(this).change(function(){
                    if($(this).attr('checked') != undefined){
                        $(this).parent().find('input[type="text"]').attr('required', true);
                    }else{
                        $(this).parent().find('input[type="text"]').removeAttr('required');
                    }
                });
            });
        });
    </script>
<?php
}