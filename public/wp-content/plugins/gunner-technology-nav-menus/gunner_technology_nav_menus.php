<?php
/*
Plugin Name: Gunner Technology Dynamic Menus
Plugin URI: http://gunnertech.com/2012/02/dynamic-menus-wordpress-plugin
Description: A plugin that allows authors to creat menu locations and add menus via widgets.
Version: 0.1.0
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


define('GT_NAV_MENUS_VERSION', '0.1.0');
define('GT_NAV_MENUS_URL', plugin_dir_url( __FILE__ ));

class GtNavMenus {
  private static $instance;
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  
  public static function activate() {
    global $wpdb;
  
    update_option("gt_nav_menus_db_version", GT_NAV_MENUS_VERSION);
  }
  
  public static function getConst($const) {
    return self::$$const;
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    
    $installed_ver = get_option( "gt_nav_menus_db_version" );
    
    if( $installed_ver != GT_NAV_MENUS_VERSION ) {
      self::activate();
    }
  }
  
  private function __construct(){
    add_action('widgets_init',function() {
      return register_widget('GtNavMenus_Widget');
    });
    
    add_action('after_setup_theme', function() {
      $dynamic_nav_menus = GtNavMenus::as_json(get_option('dynamic_nav_menus'));

      if(is_array($dynamic_nav_menus)) {
        foreach($dynamic_nav_menus as $nav) {
          if(!empty($nav[0])) {
            register_nav_menu($nav[0],$nav[1]);
          }
        }
      }
    });
    
    add_action('init',function() {
      wp_enqueue_script( 'nav-menus-script', GT_NAV_MENUS_URL.'js/script.js', array('jquery'));
      wp_enqueue_style( GtNavMenus::getConst('PREFIX'), GtNavMenus::getConst('URL').'css/style.css' );
    });
  }
  
  public static function setup() {
    self::$VERSION = '0.0.1';
    self::$PREFIX = "gt_nav_menus";
    self::$URL = plugin_dir_url( __FILE__ );
    self::$PATH = plugin_dir_path( __FILE__ );
    
    self::update_db_check();
    $gunner_technology_nav_menus = self::singleton();
  }
  
  public static function singleton() {
    if (!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    
    return self::$instance;
  }
  
  public static function as_json($value) {
    if(is_string($value)) {
      return self::as_json(json_decode($value));
    }

    return $value;
  }
    
}

class GtNavMenus_Widget extends WP_Widget {
  
  private $options = array( 
    "title" => "",
    "nav_location" => "",
    "bootstrap-position" => "navbar-fixed-top",
    "branding" => '',
    'brand' => '',
    'walker' => 'default.php',
    'search' => ''
  );
    
  function __construct() {
    $widget_ops = array( 'classname' => 'gunner-technology-nav-menus', 'description' => 'A widget that displays Nav Menus photos.' );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'gunner-technology-nav-menus' );
    
    parent::__construct( 
      'gunner_technology_nav_menus', 
      'Nav Menus Widget', 
      array( 'description' => 'Create Dynamic Menus', 'classname' => 'gunner-technology-nav-menus' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
    extract( $args );
    
    $instance = wp_parse_args( 
      (array) $instance, 
      array( 'title' => '', 'nav_location' => 'primary', 'brand' => "", 'walker' => 'default.php', 'search' => "", 'branding' => get_bloginfo("name"), "bootstrap-position" => "navbar-fixed-top")
    );
    
    if($instance['walker'] == 'Bootstrap_Walker_Nav_Menu.php') {
      include_once GtNavMenus::getConst('PATH').'/walkers/'.$instance['walker'];
    }
    ?>
    <?php echo $before_widget ?>
      <?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>
      <?php if($instance['walker'] == 'Bootstrap_Walker_Nav_Menu.php'): ?>
        <div class="navbar <?php echo $instance['bootstrap-position'] ?>">
          <div class="navbar-inner">
            <div class="container">
              <?php if($instance['brand'] == 'left'): ?>
                <a class="brand pull-left" href="<?php bloginfo("url") ?>"><?php echo $instance['branding'] ?></a>
              <?php endif; ?>

              <?php if($instance['search'] == 'left'): ?>
                <form class="navbar-search pull-left">
                  <input type="text" class="search-query" placeholder="Search" />
                </form>
              <?php endif; ?>
              
              <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </a>
              
              <?php
                wp_nav_menu(array(
                  'theme_location' => $instance['nav_location'],
                  'depth'     => 2,
                  'container'   => false,
                  'menu_class'   => 'nav',
                  'walker'   => new Bootstrap_Walker_Nav_Menu()
                ));
              ?>
              
                <?php if($instance['search'] == 'right'): ?>
                  <form class="navbar-search pull-right">
                    <input type="text" class="search-query" placeholder="Search" />
                  </form>
                <?php endif; ?>

                <?php if($instance['brand'] == 'right'): ?>
                  <a class="brand pull-right" href="<?php bloginfo("url") ?>"><?php echo $instance['branding'] ?></a>
                <?php endif; ?>
            </div>
          </div>
        </div>
      <?php elseif($instance['walker'] == 'default.php'): ?>
        <?php if($instance['brand'] == 'left'): ?>
          <a class="brand pull-left" href="<?php bloginfo("url") ?>"><?php echo $instance['branding'] ?></a>
        <?php endif; ?>
      
        <?php if($instance['search'] == 'left'): ?>
          <form class="navbar-search pull-left">
            <input type="text" class="search-query" placeholder="Search" />
          </form>
        <?php endif; ?>
      
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
      
        <div class="nav-collapse">
          <?php echo do_shortcode(
            wp_nav_menu( 
              array( 
                'echo' => false, 
                'after_link' => '', 
                'container' => false, 
                'menu_class' => 'nav', 
                'theme_location' => $instance['nav_location']
              ) 
            )
          ); ?>
        
          <?php if($instance['search'] == 'right'): ?>
            <form class="navbar-search pull-right">
              <input type="text" class="search-query" placeholder="Search" />
            </form>
          <?php endif; ?>
        
          <?php if($instance['brand'] == 'right'): ?>
            <a class="brand pull-right" href="<?php bloginfo("url") ?>"><?php echo $instance['branding'] ?></a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <?php echo do_shortcode(
          wp_nav_menu( 
            array( 
              'echo' => false, 
              'after_link' => '', 
              'container' => false, 
              'menu_class' => (preg_replace('/\.php/','',$instance['walker']).'-nav'), 
              'theme_location' => $instance['nav_location']
            ) 
          )
        ); ?>
      <?php endif; ?>
    <?php echo $after_widget ?>
    <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['nav_location'] = $new_instance['nav_location'];
    $instance['search'] = $new_instance['search'];
    $instance['brand'] = $new_instance['brand'];
    $instance['branding'] = $new_instance['branding'];
    $instance['walker'] = $new_instance['walker'];
    $instance['bootstrap-position'] = $new_instance['bootstrap-position'];
    
    if(!empty($new_instance['new_nav_location']) && !empty($new_instance['new_nav_description'])) {
      $dynamic_nav_menus_string = get_option('dynamic_nav_menus');
      $dynamic_nav_menus = isset($dynamic_nav_menus_string) ? json_decode($dynamic_nav_menus_string) : array();
      $dynamic_nav_menus[] = array(
        strtolower(preg_replace('/\W/',"-",$new_instance['new_nav_location'])),
        $new_instance['new_nav_description']
      );
      
      register_nav_menu(strtolower(preg_replace('/\W/',"-",$new_instance['new_nav_location'])), $new_instance['new_nav_description'] );
      update_option('dynamic_nav_menus' , json_encode($dynamic_nav_menus) );
    }
    
    return $instance;
    
  }

  function form( $instance ) {
    $instance = wp_parse_args( 
      (array) $instance, 
      array( 'title' => '', 'walker' => 'default.php', 'nav_location' => 'primary', 'brand' => "", 'search' => "", 'branding' => get_bloginfo("name")) 
    );
    $navs = get_registered_nav_menus();
    ?>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'branding' ); ?>">Branding:</label><br />
      <input id="<?php echo $this->get_field_id( 'branding' ); ?>" name="<?php echo $this->get_field_name( 'branding' ); ?>" value="<?php echo $instance['branding']; ?>" />
    </p>
  
    <p>
      <label for="<?php echo $this->get_field_id( 'nav_location' ); ?>">Nav Location:</label>
      <select id="<?php echo $this->get_field_id( 'nav_location' ); ?>" name="<?php echo $this->get_field_name( 'nav_location' ); ?>">
      <?php foreach($navs as $key => $nav): ?>
        <option <?php selected($instance['nav_location'],$key) ?> value="<?php echo $key ?>"><?php echo $key ?></option>
      <?php endforeach; ?>
      </select>
    </p>
    
    <?php if($instance['walker'] == 'default.php' || $instance['walker'] == 'Bootstrap_Walker_Nav_Menu.php'): ?>
      <p>
        <label for="<?php echo $this->get_field_id( 'brand' ); ?>">Branding Position</label>
        <select id="<?php echo $this->get_field_id( 'brand' ); ?>" name="<?php echo $this->get_field_name( 'brand' ); ?>">
          <option <?php selected($instance['brand'],"") ?> value="">None</option>
          <option <?php selected($instance['brand'],"left") ?> value="left">Left</option>
          <option <?php selected($instance['brand'],"right") ?> value="right">Right</option>
        </select>
      </p>
    
      <p>
        <label for="<?php echo $this->get_field_id( 'search' ); ?>">Search Position</label>
        <select id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>">
          <option <?php selected($instance['search'],"") ?> value="">None</option>
          <option <?php selected($instance['search'],"left") ?> value="left">Left</option>
          <option <?php selected($instance['search'],"right") ?> value="right">Right</option>
        </select>
      </p>
    <?php endif; ?>
    
    <?php if($instance['walker'] == 'Bootstrap_Walker_Nav_Menu.php'): ?>
      <p>
        <label for="<?php echo $this->get_field_id( 'bootstrap-position' ); ?>">Nav Type</label>
        <select id="<?php echo $this->get_field_id( 'bootstrap-position' ); ?>" name="<?php echo $this->get_field_name( 'bootstrap-position' ); ?>">
          <option <?php selected($instance['bootstrap-position'],"") ?> value="">Default</option>
          <option <?php selected($instance['bootstrap-position'],"navbar-fixed-top") ?> value="navbar-fixed-top">Fixed Top</option>
          <option <?php selected($instance['bootstrap-position'],"navbar-fixed-bottom") ?> value="navbar-fixed-bottom">Fixed Bottom</option>
        </select>
      </p>
    <?php endif; ?>
    
    <p>
      <label for="<?php echo $this->get_field_id('walker'); ?>"><?php _e('Walker:'); ?></label><br />
      <select class="widefat" id="<?php echo $this->get_field_id('walker'); ?>" name="<?php echo $this->get_field_name('walker'); ?>">
        <?php if ($handle = opendir(GtNavMenus::getConst('PATH').'/walkers')) { while (false !== ($entry = readdir($handle))) { if ($entry != "." && $entry != "..") { ?>
          <option value="<?php echo $entry ?>" <?php selected($instance['walker'], $entry) ?>><?php echo str_replace('.php', '', $entry) ?></option>
        <?php }}} ?>
      </select>
    </p>
    
  
    <p>Need to add a Nav Location? Fill in the fields below and click "Save"</p>

    <p>
      <label for="<?php echo $this->get_field_id( 'new_nav_location' ); ?>">New Nav Location:</label>
      <input id="<?php echo $this->get_field_id( 'new_nav_location' ); ?>" name="<?php echo $this->get_field_name( 'new_nav_location' ); ?>" value="" />
    </p>
  
    <p>
      <label for="<?php echo $this->get_field_id( 'new_nav_description' ); ?>">New Nav Description:</label>
      <input id="<?php echo $this->get_field_id( 'new_nav_description' ); ?>" name="<?php echo $this->get_field_name( 'new_nav_description' ); ?>" value="" />
    </p>
  
    <?php
  }

} // class Foo_Widget

register_activation_hook( __FILE__, array('GtNavMenus', 'activate') );
register_activation_hook( __FILE__, array('GtNavMenus', 'deactivate') );
register_activation_hook( __FILE__, array('GtNavMenus', 'uninstall') );

add_action('plugins_loaded', array('GtNavMenus', 'setup') );