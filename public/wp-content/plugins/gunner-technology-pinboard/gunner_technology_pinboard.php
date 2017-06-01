<?php
/*
Plugin Name: Gunner Technology Pinboard
Plugin URI: http://gunnertech.com/2012/02/pinboard-a-pinterest-wordpress-plugin/
Description: A plugin that allows authors to add their Pinboard via widgets
Version: 0.1.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/


class GtPinboard {
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  private static $instance;
  
  public static function setup() {
    self::$VERSION = '0.1.1';
    self::$PREFIX = "gt_pinboard";
    self::$URL = plugin_dir_url( __FILE__ );
    self::$PATH = plugin_dir_path( __FILE__ );
    
    
    self::update_db_check();
    $me = self::singleton();
  }
  
  public static function getConst($const) {
    return self::$$const;
  }
  
  public static function activate() {
    global $wpdb;
    
    update_option(self::$PREFIX."_db_version", self::$VERSION);
  }
  
  public static function deactivate() { }
  
  public static function uninstall() { }
  
  public static function update_db_check() {
    $installed_ver = get_option( self::$PREFIX."_db_version" );
    
    if( $installed_ver != self::$VERSION ) {
      self::activate();
    }
  }
  
  private function __construct() {
    $_this = $this;
    
    add_action( 'wp_ajax_nopriv_gt_pin-submit', array(&$this,'handle_ajax') );
    add_action( 'wp_ajax_gt_pin-submit', array(&$this,'handle_ajax') );
    
    if(is_admin()) {
      add_action('restrict_manage_posts', function() {
        // only display these taxonomy filters on desired custom post_type listings
        global $typenow;
        if ($typenow == 'gt_pin') {

            // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
          $filters = array('gt_pinboard');

          foreach ($filters as $tax_slug) {
            // retrieve the taxonomy object
            $tax_obj = get_taxonomy($tax_slug);
            $tax_name = $tax_obj->labels->name;
            // retrieve array of term objects per taxonomy
            $terms = get_terms($tax_slug);

            // output html for taxonomy dropdown filter
            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
            echo "<option value=''>Show All $tax_name</option>";
            foreach ($terms as $term) {
              // output each select option line, check against the last $_GET to show the current option selected
              echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
            }
            
            echo "</select>";
          }
        }
      });

      add_filter('parse_query', function ($query) {
          global $pagenow;
          $qv = &$query->query_vars;
          if ($pagenow=='edit.php' && isset($qv['post_type']) && $qv['post_type'] == 'gt_pin' && isset($qv['gt_pinboard']) && is_numeric($qv['gt_pinboard']) ) {
            $term = get_term_by('id',$qv['gt_pinboard'],'gt_pinboard');
            $qv['term'] = $term->slug;
          }
      });      

      add_action( "manage_pages_custom_column", function($column) {
        global $post;

        switch ($column) {
          case "description":
            the_excerpt();
            break;
          case "gt_pinboard":
            echo get_the_term_list($post->ID, 'gt_pinboard', '', ', ','');
            break;
        }
      });

      add_filter("manage_edit-gt_pin_columns", function($columns) {
        return array(
          "cb" => "<input type=\"checkbox\" />",
          "title" => "Pin",
          "description" => "Description",
          "gt_pinboard" => "Pinboards",
        );
      });
    }
    
    
    //add_filter( 'the_content', array(&$this,'replace_content'));
    
    // add_filter( "single_template", function($single_template) {
    //   global $post;
    //   
    //   if ($post->post_type == 'gt_pin-nope') {
    //     $single_template = dirname( __FILE__ ) . '/single-gt_pin.php';
    //   }
    //   
    //   return $single_template;
    // });
    
    add_shortcode('pinboard', function($atts, $content=null, $code="") use ($_this) { 
      global $post;
      
      $default_query_params = array(
        "gt_pinboards" => false,
        "post_status" => "publish",
        'posts_per_page' => 8,
        'orderby' => 'modified',
        'nopaging' => 0, 
        'ignore_sticky_posts' => 0,
        'order' => 'DESC', 
        'meta_key' => '_thumbnail_id',
        'post_type' => 'gt_pin',
        'paged' => 1,
        'load_more' => 1
      );
      
      $shortcode_atts = shortcode_atts(array_merge($default_query_params,array("column_width" => 0)), $atts);
      
      extract($shortcode_atts);
      
      if($gt_pinboards && strpos($gt_pinboards,",") !== FALSE) {
        $gt_pinboards = explode(",",$gt_pinboards); 
      }
      
      if($gt_pinboards) {
        $shortcode_atts['tax_query'] = array(
          array(
            'taxonomy' => 'gt_pinboard',
            'field' => 'slug',
            'terms' => $gt_pinboards
          )
        );
      }
            
      $pin_query = new WP_Query($shortcode_atts);
      $html = "";
      
      
      if ($pin_query->have_posts()) {
        $html .= '<div class="gt-pinboard" data-columnWidth="'.$column_width.'"';
        
        foreach($shortcode_atts as $key => $value) {
          $html .= ' data-'.$key.'="'.$value.'"';
        }
        
        $html .= '>';
        while ($pin_query->have_posts()) { $pin_query->the_post();
          $html .= '<figure class="gt-pin">
            <a data-id="'.get_the_ID().'" title="' . (esc_attr(get_the_title() ? get_the_title() : get_the_ID())) . '" href="' . get_permalink(get_the_ID()) . '">'.
              preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(),'large' ))
            .'</a>
            <figcaption><strong>'.get_the_title().' </strong>'.wpautop($post->post_excerpt).'</figcaption>
          </figure>';
        }
        $html .= '</div>';
        $html .= $load_more ? '<div class="infinite-scroll-indicator"><p></p></div>' : '';
        wp_reset_postdata();
      }
      
      return $html;
    });
    
    add_action('widgets_init',function() {
      return register_widget('GtPinboard_Widget');
    });
    
    add_action('admin_init', function() {
      global $wp_query;
            
      wp_enqueue_script( "content_meta_boxes", get_template_directory_uri().'/admin/js/content_meta_boxes.js', array('suggest') );
      
      
      add_action('save_post', function($post_id) {
        if (!array_key_exists('gt_pin_meta_noncename',$_POST)) return $post_id;
        if (!wp_verify_nonce($_POST['gt_pin_meta_noncename'],__FILE__)) return $post_id;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

        $current_data = get_post_meta($post_id, '_gt_pin_meta', TRUE);  

        $new_data = $_POST['_gt_pin_meta'];

        GtPinboard::meta_clean($new_data);
        
        if ($current_data) {
          if (is_null($new_data)) { 
            delete_post_meta($post_id,'_gt_pin_meta'); 
          } else {
            update_post_meta($post_id,'_gt_pin_meta',$new_data);
          }
        } elseif (!is_null($new_data)) {
          add_post_meta($post_id,'_gt_pin_meta',$new_data,TRUE);
        }

        return $post_id;
      });

      if (current_user_can('delete_published_posts')) {
        $gt_pin_meta_boxes_has_been_nonced = false;
        $custom_types = array("gt_pin");//get_post_types();
        $metas = array(
          array(
            "slug" => "gt_pinboard_pin_meta",
            "title" => "Pin Info",
            "placement" => "side",
            "priority" => "low",
            "callback" => function() { 
              $gt_pin_meta = array_merge(
                array(
                  "location" => false
                ),
                GtPinboard::meta_data()
              );
              ?><div class="ajaxtag notypeahead">
                <p><input style="width:100%;" type="text" placeholder="Location of the Pin" class="newtag form-input-tip" name="_gt_pin_meta[location]" value="<?php echo $gt_pin_meta['location'] ?>" /></p>
                <p><input style="width:100%;" type="text" placeholder="Date Pin was taken" class="newtag form-input-tip" name="_gt_pin_meta[date_taken]" value="<?php echo $gt_pin_meta['date_taken'] ?>" /></p>
                
                <p>Your site may use subtitles to add more information to content in list or detail view. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about subtitles.</a></p>
              </div> 
            <?php }
          ),
        );
      
        foreach ($metas as $meta) {
          foreach ($custom_types as $type) {
            $callback = function() use ($meta, &$gt_pin_meta_boxes_has_been_nonced){
              if(!$gt_pin_meta_boxes_has_been_nonced) {
                echo '<input type="hidden" name="gt_pin_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
              }
              $gt_pin_meta_boxes_has_been_nonced = true;
              $meta['callback']();
            };
            add_meta_box('gt_pin_'.$meta['slug'], $meta['title'], $callback, $type, $meta['placement'], $meta['priority']);
          }
        }
      }
    });
    
    add_action('init', function() {
      register_taxonomy('gt_pinboard', 'gt_pin', array(
        'labels' => array(
          'name' => __( 'Pinboards' ),
          'singular_name' => __( 'Pinboard' ),
          'search_items' => __( 'Search Pinboards' ),
          'popular_items' => __( 'Popular Pinboards' ),
          'all_items' => __( 'All Pinboards' ),
          'popular_items' => __( 'Popular Pinboards' ),
          'add_new_item' => __( 'Add New Pinboard' ),
          'edit_item' => __( 'Edit Pinboard' ),
          'new_item' => __( 'New Pinboard' ),
          'update_item' => __( 'Update Pinboard' ),
          'new_item_name' => __( 'New Pinboard Name' ),
          'add_or_remove_items' => __( 'Add or remove Pinboards' ),
          'choose_from_most_used' => __( 'Choose from the most used Pinboards' ),
          'menu_name' => __( 'Pinboards' )
        ),
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => false,
        'hierarchical' => true,
      ));
      
      register_post_type( 'gt_pin',
        array(
          'labels' => array(
            'name' => __( 'Pins' ),
            'singular_name' => __( 'Pins' ),
            'add_new_item' => __( 'Add New Pin' ),
            'edit_item' => __( 'Edit Pin' ),
            'new_item' => __( 'New Pin' ),
            'view_item' => __( 'View Pin' ),
            'search_items' => __( 'Search Pins' ),
            'not_found' => __( 'No pins found' ),
            'not_found_in_trash' => __( 'No pins found in trash' ),
          ),
          'description' => __( 'Pins are items to place in your Pinboards' ),
          'public' => true,
          'has_archive' => true,
          'show_ui' => true,
          'menu_position' => 5,
          'menu_icon' => null,
          'show_in_menu' => true,
          'hierarchical' => true,
          'show_in_nav_menus' => true,
          'supports' => array(
            'title',
            'author',
            'editor',
            'thumbnail',
            'revisions',
            'excerpt',
            'comments'
          ),
          'rewrite' => array('slug' => 'pins'),
          'taxonomies' => array('gt_pinboard','post_tag')
        )
      );
    });
    
    add_action('init',function() {
      wp_enqueue_script( 'masonry', GtPinboard::getConst('URL').'js/lib/jquery.masonry.min.js', array('jquery'));
      wp_enqueue_script( 'bootstrap-modal', GtPinboard::getConst('URL').'js/lib/bootstrap-modal.js', array('jquery'));      
      wp_enqueue_script( GtPinboard::getConst('PREFIX'), GtPinboard::getConst('URL').'js/script.js', array('masonry','bootstrap-modal'));
      
      wp_localize_script( GtPinboard::getConst('PREFIX'), 'GtPinboard', array(
        'ajaxurl'          => admin_url( 'admin-ajax.php' ),
        'postCommentNonce' => wp_create_nonce( GtPinboard::getConst('PREFIX').'-post-comment-nonce' ),
      ));
      
      wp_enqueue_style( GtPinboard::getConst('PREFIX'), GtPinboard::getConst('URL').'css/style.css' );
    });
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
  
  public static function meta_data($reload=false) {
    global $wp_query, $post, $gt_pin_meta_data;

    $meta = $gt_pin_meta_data;
    if($reload) {
      if(is_search() || is_category()) {
        $meta = get_post_meta(get_option('page_for_posts'),'_gt_pin_meta',TRUE);
      } elseif(isset($wp_query->queried_object)) {
        $meta = get_post_meta($wp_query->queried_object->ID,'_gt_pin_meta',TRUE);
      } elseif(isset($post)) {
        $meta = get_post_meta($post->ID,'_gt_pin_meta',TRUE);
      }
    } else {
      if(isset($gt_pin_meta_data)) {
        $meta = $gt_pin_meta_data;
      } else {
        if(is_search() || is_category()) {
          $meta = get_post_meta(get_option('page_for_posts'),'_gt_pin_meta',TRUE);
        } elseif(isset($wp_query->queried_object) && isset($wp_query->queried_object->ID)) {
          $meta =  get_post_meta($wp_query->queried_object->ID,'_gt_pin_meta',TRUE);
        } elseif(isset($post)) {
          $meta = get_post_meta($post->ID,'_gt_pin_meta',TRUE);
        }
      }
    }

    if(!is_array($meta)) {
      $meta = array();
    }

    return $meta;
  }
  
  public static function meta_clean(&$arr) {
    if (is_array($arr)) {
      foreach ($arr as $i => $v) {
        if (is_array($arr[$i])) {
          self::meta_clean($arr[$i]);

          if (!count($arr[$i])) {
            unset($arr[$i]);
          }
        } else {
          if (trim($arr[$i]) == '') {
            unset($arr[$i]);
          } else {
            //$arr[$i] = hbgs_sanitize_data($arr[$i]);
            $arr[$i] = $arr[$i];
          }
        }
      }

      if (!count($arr)) {
        $arr = NULL;
      }
    }
  }
  
  private function get_ID_by_slug($page_slug) {
    if(is_numeric($page_slug)) {
      return $page_slug;
    }
    
    $page = get_page_by_path($page_slug,'OBJECT','gt_pin');
    if ($page) {
      return $page->ID;
    } else {
      return null;
    }
  }
  
  public function replace_content($content) {
    global $post, $wp_query;
    
    
    
    if(!is_single() || $post->post_type != 'gt_pin') { return $content; }
    
    
    $terms = get_the_terms( $post->ID, 'gt_pinboard' );
    $term = false;
    $cat_query = false;

    if(is_array($terms)) {
      foreach($terms as $t) {
        $term = $t;
        break;
      }
    }

    if($term) {
      $cat_query = new WP_Query( array(
        'tax_query' => array(
          array(
            'taxonomy' => 'gt_pinboard',
            'field' => 'id',
            'terms' => $term->term_id
          )
        ),
        'post_type' => 'gt_pin'
      ));
    }
    
    ?>
      <figure>
        <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(),'large' )) ?>
        <figcaption class="well">
          <?php remove_filter('the_content',array(&$this,'replace_content')); the_excerpt(); add_filter('the_content',array(&$this,'replace_content')); ?>
        </figcaption>
      </figure>
      <?php if($cat_query && $cat_query->have_posts()) : ob_start(); ?>
        <div class="gt-more">
          <h3>More from <?php echo $term->name ?></h3>
          <ul class="gt-in-content thumbnails gt-category-pins">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
              <li class="gt-category-pin<?php echo (get_the_ID() == $id ? ' active' : '') ?>">
                <a data-id="<?php the_ID() ?>" class="tooltip-link thumbnail" title="<?php echo (esc_attr(get_the_title() ? get_the_title() : get_the_ID())) ?>" href="<?php echo get_permalink(get_the_ID()) ?>">
                  <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(),'large' )) ?>
                </a>
              </li>
            <?php endwhile; wp_reset_postdata(); ?>
          </ul>
        </div>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; 
    
    return $content;
  }
  
  public function show_pin($id) {
    $query = new WP_Query( array( 'p' => $id, 'post_type' => 'gt_pin' ) );
    
    $terms = get_the_terms( $id, 'gt_pinboard' );
    $term = false;
    $cat_query = false;
    
    if(is_array($terms)) {
      foreach($terms as $t) {
        $term = $t;
        break;
      }
    }
    
    if($term) {
      $cat_query = new WP_Query( array(
        'tax_query' => array(
          array(
            'taxonomy' => 'gt_pinboard',
            'field' => 'id',
            'terms' => $term->term_id
          )
        ),
        'post_type' => 'gt_pin'
      ));
    }
    ?>
    
    <div id="gt-pin-modal" class="gt-full-pin modal fade">
      <div class="modal-contents">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
          <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h1><?php the_title(); ?></h1>
          </div>
          <div class="modal-body">
            <figure>
              <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(),'large' )) ?>
              <figcaption class="well">
                <strong><?php get_the_title() ?></strong>
                <?php the_excerpt() ?>
                <a href="<?php echo get_permalink(get_the_ID()) ?>" class="more">Expanded View</a>
              </figcaption>
            </figure>
            <article>
              <?php the_content() ?>
            </article>
          </div>
        <?php endwhile; wp_reset_postdata(); ?>
        <div class="modal-footer">
          <?php if($term && $cat_query->have_posts()) : ?>
            <h3>More from <?php echo $term->name ?></h3>
            <ul class="thumbnails gt-category-pins">
              <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <li class="gt-category-pin<?php echo (get_the_ID() == $id ? ' active' : '') ?>">
                  <a class="tooltip-link thumbnail" data-caption="<?php echo esc_attr(get_the_excerpt()) ?>" data-body="<?php echo esc_attr(get_the_content()) ?>" data-title="<?php echo esc_attr(get_the_title()) ?>" data-id="<?php get_the_ID() ?>" title="<?php echo (esc_attr(get_the_title() ? get_the_title() : get_the_ID())) ?>" href="<?php echo get_permalink(get_the_ID()) ?>">
                    <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(),'large' )) ?>
                  </a>
                </li>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          <?php endif; ?>
        </div>        
      </div>
    </div>
    
    <?php
  }
  
  public function list_pins() {
    $query_params = array_merge($_GET,array('post_type' => 'gt_pin'));
    
    if(isset($_GET['gt_pinboards']) && $_GET['gt_pinboards'] != '') {
      $query_params = array_merge($query_params,array(
        'tax_query' => array(
          array(
            'taxonomy' => 'gt_pinboard',
            'field' => 'slug',
            'terms' => explode(",",$_GET['gt_pinboards'])
          )
        )
      ));
    }
    
    $query = new WP_Query( $query_params );
    if ($query->have_posts()) {
      while ($query->have_posts()) { $query->the_post();
        $html .= '<figure class="gt-pin">
          <a data-id="'.get_the_ID().'" title="' . (esc_attr(get_the_title() ? get_the_title() : get_the_ID())) . '" href="' . get_permalink(get_the_ID()) . '">'.
            preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(),'large' ))
          .'</a>
          <figcaption><strong>'.get_the_title().' </strong>'.get_the_excerpt().'</figcaption>
        </figure>';
      }
      wp_reset_postdata();
    }
    echo $html;
  }
  
  public function handle_ajax() {
    $nonce = isset($_POST['postCommentNonce']) ? $_POST['postCommentNonce'] : $_GET['postCommentNonce'] ;
    // if ( ! wp_verify_nonce( $nonce, GtPinboard::getConst('PREFIX').'-post-comment-nonce' ) ) {
    //   die ( 'Busted!');
    // }
    // $response = json_encode( array( 
    //   'success' => true,
    //   'id' => $_GET['id']
    // ) );
    // header( "Content-Type: application/json" );
    // echo $response;
    
    if(isset($_GET['id'])) {
      $this->show_pin($this->get_ID_by_slug($_GET['id']));
    } else {
      $this->list_pins();
    }
    
    exit;
  }
    
}

class GtPinboard_Widget extends WP_Widget {
  
  private $options = array( 
    "title" => "",
    "nav_location" => ""
  );
    
  function __construct() {
    $this->default_instance = array(
      'title' => "Pinboard",
      'description' => "Add an optional description",
      'posts_per_page' => 8,
      'load_more' => 1,
      'gt_pinboards' => false
    );
    
    parent::__construct( 
      'gunner_technology_pinboard', 
      'Pinboard Widget', 
      array( 'description' => 'Create Pinboards', 'classname' => 'gunner-technology-pinboard' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
    extract( $args );
    $instance = wp_parse_args( 
      (array) $instance, 
      $this->default_instance
    );
    ?>
    <?php echo $before_widget ?>
      <?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>
      <?php if ( $instance['description'] ) { echo wpautop($instance['description']); } ?>
      <?php echo do_shortcode('[pinboard gt_pinboards='.intval($instance['gt_pinboards']).' posts_per_page='.$instance['posts_per_page'].' load_more='.$instance['load_more'].']'); ?>
    <?php echo $after_widget ?>
    <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['description'] = $new_instance['description'];
    $instance['posts_per_page'] = intval($new_instance['posts_per_page']);
    $instance['load_more'] = intval($new_instance['load_more']);
    $instance['gt_pinboards'] = $new_instance['gt_pinboards'];
            
    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( 
      (array) $instance, 
      $this->default_instance
    );
    ?>
    
      <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
        <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description (optional):</label><br />
        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $instance['description']; ?></textarea>
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id( 'gt_pinboards' ); ?>">Pinboard Categories (Comma Separated):</label><br />
        <input id="<?php echo $this->get_field_id( 'gt_pinboards' ); ?>" name="<?php echo $this->get_field_name( 'gt_pinboards' ); ?>" value="<?php echo $instance['gt_pinboards']; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>">Pins to Show:</label><br />
        <input id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" value="<?php echo $instance['posts_per_page']; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id( 'load_more' ); ?>">Paginate?</label><br />
        <input <?php checked($instance['load_more'],1) ?> id="<?php echo $this->get_field_id( 'load_more' ); ?>" name="<?php echo $this->get_field_name( 'load_more' ); ?>" value="<?php echo $instance['load_more']; ?>" type="checkbox" />
      </p>

  
    <?php
  }

} // class Foo_Widget

register_activation_hook( __FILE__, array('GtPinboard', 'activate') );
register_activation_hook( __FILE__, array('GtPinboard', 'deactivate') );
register_activation_hook( __FILE__, array('GtPinboard', 'uninstall') );

add_action('plugins_loaded', array('GtPinboard', 'setup') );