<?php

if (!isset($_GET['page']) || $_GET['page'] == hbgs_current_admin_page(__FILE__)) {
  add_action('admin_init', str_replace("-","_",hbgs_current_admin_page(__FILE__).'_init'));
}

add_action('admin_menu', 'hbgs_sidebars_menu');

function hbgs_sidebars_menu() {
  if (current_user_can( "delete_published_posts" )) {
    add_submenu_page('hbgs-settings','Sidebar Settings', 'Sidebars', 'administrator', 'hbgs-sidebars', 'hbgs_sidebars_page');
  }
}

function hbgs_sidebars_init() {
  wp_enqueue_script( "sidebars", get_template_directory_uri().'/admin/js/sidebars.js', array('admin') );
  
	register_setting( 'hbgs-sidebars-group', 'hbgs_sidebars', 'hbgs_save_sidebars' );	  
}

function hbgs_save_sidebars($sidebars) {
  $sidebars = is_object($sidebars) ? $sidebars : json_decode($sidebars);
  
  if(!$sidebars) {
    wp_die( "We're sorry. There was a problem with your change. Please go back and try again.", "Error", array(
     "back_link" => true
    ));
  }
  // $myFile = dirname(__FILE__)."/../backup/sidebars.txt";
  // $fh = fopen($myFile, 'w');
  // 
  // if($fh) {
  //   fwrite($fh, $sidebars);
  //   fclose($fh);
  // }
  
  return $sidebars;
}


function hbgs_sidebars_page() { 
  $sidebars = hbgs_sidebar_as_json_object();
  $sidebarString = json_encode($sidebars);
  $areas = hbgs_sidebar_areas();
  $sidebars_from_file = "";//file_get_contents(dirname(__FILE__)."/../backup/sidebars.txt");
  
  ?>
  <div class="wrap">
    <h2>HTML5 Boilerplated Grid System Sidebars</h2>
    <form method="post" action="options.php" class="hbgs-options" id="hbgs-sidebars-form">
      <?php settings_fields( 'hbgs-sidebars-group' ); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Add a Sidebar<br /><br /><br />
            Learn More About:<br /><br />
            <a href="http://www.youtube.com/watch?v=64rnK1Z6XN8" target="_blank">Creating a Sidebar</a><br /><br />
            <a href="http://www.youtube.com/watch?v=B8IShCT9TOg" target="_blank">Adding Widgets Sidebars</a><br /><br />
            <a href="TODO:" target="_blank">How the CSS Grid Works</a>
          </th>
          <td>
            Name: <input style="vertical-align:top;" type="text" id="hbgs-sidebar-name" />
            Placement: 
            <select style="vertical-align:top;" id="hbgs-sidebar-placement">
              <?php foreach($areas as $area): $area_name = ucwords(str_replace("_"," ",$area)); ?>
                <option value="<?php echo $area ?>"><?php echo $area_name ?></option>
              <?php endforeach; ?>
            </select>
            Categories:
            <select multiple="multiple" size="3" style="height:4em;" id="hbgs-sidebar-categories">
              <?php foreach(get_terms('sidebar_category', 'orderby=count&hide_empty=0') as $category): ?>
                <option <?php selected($category->name == 'Default') ?> value="<?php echo $category->term_id ?>"><?php echo $category->name ?></option>
              <?php endforeach; ?>
            </select>
            <!--input type="checkbox" id="hbgs-sidebar-default" /-->
            First?:
            <input type="checkbox" id="hbgs-sidebar-alpha" style="vertical-align:top;" />
            Last?:
            <input type="checkbox" id="hbgs-sidebar-omega" style="vertical-align:top;" />
            <br />
            <br />
            Number of Columns: 
            <select id="hbgs-sidebar-columns">
              <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
            Position:
            <select id="hbgs-sidebar-position">
              <?php for($i=1; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
            Pull:
            <select id="hbgs-sidebar-pull">
              <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
            Push:
            <select id="hbgs-sidebar-push">
              <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
            Prefix:
            <select id="hbgs-sidebar-prefix">
              <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
            Suffix:
            <select id="hbgs-sidebar-suffix">
              <?php for($i=0; $i<=24; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
            <input type="hidden" id="hbgs-sidebars" name="hbgs_sidebars" value='<?php echo $sidebarString ?>' />
            <input type="hidden" id="sidebars-from-file" name="sidebars_from_file" value="<?php echo esc_js($sidebars_from_file) ?>" />
            <br />
            <br />
            Styles:
            <br />
            <textarea id="hbgs-sidebar-styles" rows="10" cols="80"></textarea>
          </td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Add Sidebar') ?>" />
      </p>
      <?php if($sidebars): ?>
      <?php foreach($sidebars as $key => $value): if(!is_array($value)) { $value = json_decode($value);} if(!$value) { continue; } ?>
        <h3><?php echo ucwords(str_replace("_"," ",$key)) ?></h3>
        <table class="form-table">
          <input type="hidden" class="hbgs-placement-name" value="<?php echo $key ?>" />
          <?php usort($value, "hbgs_sidebar_cmp"); foreach($value as $sidebar): if(is_array($sidebar)){ $sidebar = json_decode(json_encode($sidebar)); } ?>
            <tr valign="top" class="<?php echo $key ?>-row">
              <th scope="row"><?php echo $sidebar->name ?></th>
              <td>
                <input type="hidden" class="hbgs-sidebar-name" value="<?php echo $sidebar->name ?>" />
                <!--Default? 
                <input type="checkbox" <?php if($sidebar->default == 'on'){ echo ' checked="checked"'; } ?> class="hbgs-sidebar-default" />
                -->
                Categories:
                <select multiple="multiple" size="3" style="height:4em;" class="hbgs-sidebar-categories">
                  <?php foreach(get_terms('sidebar_category', 'orderby=count&hide_empty=0') as $category): ?>
                    <option <?php selected(is_array($sidebar->categories) && in_array($category->term_id,$sidebar->categories)) ?> value="<?php echo $category->term_id ?>"><?php echo $category->name ?></option>
                  <?php endforeach; ?>
                </select>
                First?:
                <input type="checkbox" <?php checked($sidebar->alpha == 'on',true) ?> class="hbgs-sidebar-alpha" style="vertical-align:top;" />
                Last?:
                <input type="checkbox" <?php checked($sidebar->omega == 'on',true) ?> class="hbgs-sidebar-omega" style="vertical-align:top;" />
                <br />
                <br />
                Number of Columns:
                <select class="hbgs-sidebar-columns">
                  <?php for($i=1; $i<=24; $i++): ?>
                    <option 
                      <?php if($i == intval($sidebar->columns)): ?>
                        <?php echo 'selected="selected"' ?>
                      <?php endif; ?>
                    value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php endfor; ?>
                </select>
                
                Position: 
                <select class="hbgs-sidebar-position">
                  <?php for($i=1; $i<=24; $i++): ?>
                    <option 
                      <?php if($i == intval($sidebar->position)): ?>
                        <?php echo 'selected="selected"' ?>
                      <?php endif; ?>
                    value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php endfor; ?>
                </select>
                Pull:
                <select class="hbgs-sidebar-pull">
                  <?php for($i=0; $i<=24; $i++): ?>
                    <option 
                      <?php if($i == intval($sidebar->pull)): ?>
                        <?php echo 'selected="selected"' ?>
                      <?php endif; ?>
                    value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php endfor; ?>
                </select>
                Push:
                <select class="hbgs-sidebar-push">
                  <?php for($i=0; $i<=24; $i++): ?>
                    <option 
                      <?php if($i == intval($sidebar->push)): ?>
                        <?php echo 'selected="selected"' ?>
                      <?php endif; ?>
                    value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php endfor; ?>
                </select>
                Prefix:
                <select class="hbgs-sidebar-prefix">
                  <?php for($i=0; $i<=24; $i++): ?>
                    <option 
                      <?php if($i == intval($sidebar->prefix)): ?>
                        <?php echo 'selected="selected"' ?>
                      <?php endif; ?>
                    value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php endfor; ?>
                </select>
                Suffix:
                <select class="hbgs-sidebar-suffix">
                  <?php for($i=0; $i<=24; $i++): ?>
                    <option 
                      <?php if($i == intval($sidebar->suffix)): ?>
                        <?php echo 'selected="selected"' ?>
                      <?php endif; ?>
                    value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php endfor; ?>
                </select>
                <br />
                <br />
                Styles:
                <br />
                <textarea class="hbgs-sidebar-styles" rows="10" cols="80"><?php echo format_to_edit(str_replace('\n',"\n",$sidebar->styles)) ?></textarea>
                <br />
                <a href="" class="hbgs-sidebar-update" style="font-weight:bold;">Update</a> | 
                <a href="" class="hbgs-sidebar-delete" style="font-weight:bold;">Delete</a>
                <br />
                <br />
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endforeach; ?>
      <?php endif; ?>
    </form>
  </div>
<?php }
