<div class="wrap gunner-sidebars">
  <h2>Manage Sidebars <small><a href="/wp-admin/themes.php?page=options-framework">Sidebar Options</a></small></h2>
  
  <form method="post" action="options.php" class="hbgs-options" id="hbgs-sidebars-form">
    <?php settings_fields( 'gunner-technology-sidebars-group' ); ?>
    <h3 class="sub-option-menu">Add a Sidebar</h3>
    <table class="form-table">
      <tr valign="top">
        <td>
          <p>
            <span style="display:inline-block; width: 125px; text-align: right;">Name: </span><input style="vertical-align:top;" type="text" id="hbgs-sidebar-name" placeholder="Unique name to identify this sidebar" />
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Placement: </span>
            <select style="vertical-align:top;" id="hbgs-sidebar-placement">
              <?php foreach($this->areas as $area): $area_name = ucwords(str_replace("_"," ",$area)); ?>
                <option value="<?php echo $area ?>"><?php echo $area_name ?></option>
              <?php endforeach; ?>
            </select>
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Order: </span>
            <select id="hbgs-sidebar-position">
              <?php for($i=1; $i<=12; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
          </p>
          <!--p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Columns: </span>
            <select id="hbgs-sidebar-columns">
              <?php for($i=0; $i<=12; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Title: </span><input style="vertical-align:top;" type="text" id="hbgs-sidebar-title" placeholder="Title to appear to users. Leave blank for no title." />
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Offset: </span>
            <select id="hbgs-sidebar-offset">
              <?php for($i=0; $i<=12; $i++): ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php endfor; ?>
            </select>
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Row Type: </span>
            <select id="hbgs-sidebar-row-type">
              <?php foreach(array('None','Fluid','Static') as $row_type): ?>
                <option value="<?php echo $row_type ?>"><?php echo $row_type ?></option>
              <?php endforeach; ?>
            </select>
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Tag Name: </span>
            <select id="hbgs-sidebar-tag-name">
              <?php foreach(array('aside','div','article','ul','ol') as $tag_name): ?>
                <option <?php selected($tag_name,$sidebar->tag_name) ?>  value="<?php echo $tag_name ?>"><?php echo $tag_name ?></option>
              <?php endforeach; ?>
            </select>
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Header Background URL: </span>
            <input style="vertical-align:top;" type="text" id="hbgs-sidebar-header-background-url" />
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Header Background Position: </span>
            <select id="hbgs-sidebar-header-background-position">
              <?php foreach(array('top left','top center','top left','middle left','middle center','middle left','bottom left','bottom center','bottom left') as $position): ?>
                <option <?php selected($position,$sidebar->header_background_position) ?>  value="<?php echo $position ?>"><?php echo $position ?></option>
              <?php endforeach; ?>
            </select>
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Header Background Repeat: </span>
            <select id="hbgs-sidebar-header-background-repeat">
              <?php foreach(array('no-repeat','repeat-y','repeat-x') as $repeat): ?>
                <option <?php selected($repeat,$sidebar->header_background_repeat) ?>  value="<?php echo $repeat ?>"><?php echo $repeat ?></option>
              <?php endforeach; ?>
            </select>
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Header Background Height: </span>
            <input style="vertical-align:top;" type="text" id="hbgs-sidebar-header-background-height" />
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;">Header Background Width: </span>
            <input style="vertical-align:top;" type="text" id="hbgs-sidebar-header-background-width" />
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;"> Background URL: </span>
            <input style="vertical-align:top;" type="text" id="hbgs-sidebar-background-url" />
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;"> Background Position: </span>
            <select id="hbgs-sidebar-background-position">
              <?php foreach(array('top left','top center','top left','middle left','middle center','middle left','bottom left','bottom center','bottom left') as $position): ?>
                <option <?php selected($position,$sidebar->background_position) ?>  value="<?php echo $position ?>"><?php echo $position ?></option>
              <?php endforeach; ?>
            </select>
          </p>
          <p>
            <span style="display:inline-block; width: 125px;  text-align: right;"> Background Repeat: </span>
            <select id="hbgs-sidebar-background-repeat">
              <?php foreach(array('no-repeat','repeat-y','repeat-x') as $repeat): ?>
                <option <?php selected($repeat,$sidebar->background_repeat) ?>  value="<?php echo $repeat ?>"><?php echo $repeat ?></option>
              <?php endforeach; ?>
            </select>
          </p-->
          
          <input type="hidden" id="hbgs-sidebars" name="hbgs_sidebars" value='<?php echo str_replace("'","",$sidebarString) ?>' />
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Add Sidebar') ?>" />
    </p>
    <?php if(isset($sidebars)): ?>
      <?php foreach($sidebars as $key => $value): if(!is_array($value)) { $value = json_decode($value);} if(!$value) { continue; } ?>
        <h3><?php echo ucwords(str_replace("_"," ",$key)) ?></h3>
        <table class="form-table">
          <input type="hidden" class="hbgs-placement-name" value="<?php echo $key ?>" />
          <?php usort($value, "hbgs_sidebar_cmp"); foreach($value as $sidebar): if(is_array($sidebar)){ $sidebar = json_decode(json_encode($sidebar)); } ?>
            <tr valign="top" class="<?php echo $key ?>-row">
              <th scope="row">
                <h4><?php echo $sidebar->name ?></h4>
                Shortcode:<br />
                <input type="text" value="[sidebar slug='<?php echo $key.'_'.strtolower(preg_replace('/\W/',"_",$sidebar->name)) ?>']" />
              </th>
              <td>
                <input type="hidden" class="hbgs-sidebar-name" value="<?php echo $sidebar->name ?>" />
                <p>
                  <span>Order: </span>
                  <select class="hbgs-sidebar-position">
                    <?php for($i=1; $i<=12; $i++): ?>
                      <option <?php selected($i,$sidebar->position) ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php endfor; ?>
                  </select>
                </p>
                <!--p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Title: </span><input style="vertical-align:top;" type="text" class="hbgs-sidebar-title" value="<?php echo $sidebar->title ?>" />
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Columns: </span>
                  <select class="hbgs-sidebar-columns">
                    <?php for($i=0; $i<=12; $i++): ?>
                      <option <?php selected($i,$sidebar->columns) ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php endfor; ?>
                  </select>
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Offset: </span>
                  <select class="hbgs-sidebar-offset">
                    <?php for($i=0; $i<=12; $i++): ?>
                      <option <?php selected($i,$sidebar->offset) ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php endfor; ?>
                  </select>
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Row Type: </span>
                  <select class="hbgs-sidebar-row-type">
                    <?php foreach(array('None','Fluid','Static') as $row_type): ?>
                      <option <?php selected($row_type,$sidebar->row_type) ?>  value="<?php echo $row_type ?>"><?php echo $row_type ?></option>
                    <?php endforeach; ?>
                  </select>
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Tag Name: </span>
                  <select class="hbgs-sidebar-tag-name">
                    <?php foreach(array('aside','div','article','ul','ol') as $tag_name): ?>
                      <option <?php selected($tag_name,$sidebar->tag_name) ?>  value="<?php echo $tag_name ?>"><?php echo $tag_name ?></option>
                    <?php endforeach; ?>
                  </select>
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Header Background URL: </span>
                  <input style="vertical-align:top;" type="text" value="<?php echo $sidebar->header_background_url ?>" class="hbgs-sidebar-header-background-url" />
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Header Background Height: </span>
                  <input style="vertical-align:top;" type="text" value="<?php echo $sidebar->header_background_height ?>" class="hbgs-sidebar-header-background-height" />
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Header Background Width: </span>
                  <input style="vertical-align:top;" type="text" value="<?php echo $sidebar->header_background_width ?>" class="hbgs-sidebar-header-background-width" />
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Header Background Position: </span>
                  <select class="hbgs-sidebar-header-background-position">
                    <?php foreach(array('top left','top center','top left','middle left','middle center','middle left','bottom left','bottom center','bottom left') as $position): ?>
                      <option <?php selected($position,$sidebar->header_background_position) ?>  value="<?php echo $position ?>"><?php echo $position ?></option>
                    <?php endforeach; ?>
                  </select>
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Header Background Repeat: </span>
                  <select class="hbgs-sidebar-header-background-repeat">
                    <?php foreach(array('no-repeat','repeat-y','repeat-x') as $repeat): ?>
                      <option <?php selected($repeat,$sidebar->header_background_repeat) ?>  value="<?php echo $repeat ?>"><?php echo $repeat ?></option>
                    <?php endforeach; ?>
                  </select>
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Background URL: </span>
                  <input style="vertical-align:top;" type="text" value="<?php echo $sidebar->background_url ?>" class="hbgs-sidebar-background-url" />
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Background Position: </span>
                  <select class="hbgs-sidebar-background-position">
                    <?php foreach(array('top left','top center','top left','middle left','middle center','middle left','bottom left','bottom center','bottom left') as $position): ?>
                      <option <?php selected($position,$sidebar->background_position) ?>  value="<?php echo $position ?>"><?php echo $position ?></option>
                    <?php endforeach; ?>
                  </select>
                </p>
                <p>
                  <span style="display:inline-block; width: 125px;  text-align: right;">Background Repeat: </span>
                  <select class="hbgs-sidebar-background-repeat">
                    <?php foreach(array('no-repeat','repeat-y','repeat-x') as $repeat): ?>
                      <option <?php selected($repeat,$sidebar->background_repeat) ?>  value="<?php echo $repeat ?>"><?php echo $repeat ?></option>
                    <?php endforeach; ?>
                  </select>
                </p-->
                
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