<?php
/*
Plugin Name: Gunner Technology Packages
Plugin URI: http://gunnertech.com/2012/02/pinboard-a-pinterest-wordpress-plugin/
Description: A plugin that allows authors to add their Packageboard via widgets
Version: 0.0.1
Author: gunnertech, codyswann
Author URI: http://gunnnertech.com
License: GPL2
*/

class GtPackages {
  private static $VERSION;
  private static $URL;
  private static $PATH;
  private static $PREFIX;
  private static $instance;
  
  public static function setup() {
    self::$VERSION = '0.0.1';
    self::$PREFIX = "gt_packages";
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
    
    add_action('widgets_init',function(){
      register_widget('GtArticleList_Widget');
      register_widget('GtFeaturedArticle_Widget');
      register_widget('GtInfographic_Widget');
      register_widget('GtPodcast_Widget');
    });
    
    //remove_filter( 'the_content', 'wpautop' );
    
    add_action("parse_query",function() {
      global $wp_query;
      
      if($wp_query->query_vars['post_type'] == 'gt_package') {
        remove_filter( 'the_content', 'wpautop' );
      }
    });
    
    
    
    if(is_admin()) {
      add_action('restrict_manage_posts', function() {
        global $typenow;
        if ($typenow == 'gt_package') {

          $filters = array('gt_packagesboard');

          foreach ($filters as $tax_slug) {
            $tax_obj = get_taxonomy($tax_slug);
            $tax_name = $tax_obj->labels->name;
            $terms = get_terms($tax_slug);

            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
            echo "<option value=''>Show All $tax_name</option>";
            foreach ($terms as $term) {
              echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
            }
            
            echo "</select>";
          }
        }
      });

      add_filter('parse_query', function ($query) {
          global $pagenow;
          $qv = &$query->query_vars;
          if ($pagenow=='edit.php' && isset($qv['post_type']) && $qv['post_type'] == 'gt_packages' && isset($qv['gt_packagesboard']) && is_numeric($qv['gt_packagesboard']) ) {
            $term = get_term_by('id',$qv['gt_packagesboard'],'gt_packagesboard');
            $qv['term'] = $term->slug;
          }
      });
      

      add_action( "manage_pages_custom_column", function($column) {
        global $post;

        switch ($column) {
          case "description":
            the_excerpt();
            break;
          case "gt_packagesboard":
            echo get_the_term_list($post->ID, 'gt_packagesboard', '', ', ','');
            break;
        }
      });

      add_filter("manage_edit-gt_packages_columns", function($columns) {
        return array(
          "cb" => "<input type=\"checkbox\" />",
          "title" => "Package",
          "description" => "Description",
          "gt_packagesboard" => "Packageboards",
        );
      });
    }
    
    
    add_filter( 'the_dcontent', function($content) {
      global $post, $wp_query;
      
      if(!is_single() || $post->post_type != 'gt_package') { return $content; }
      
      $meta = GtPackages::meta_data();
      ?>
      <?php if(isset($meta['featured_article_url'])): $post = GtPackages::get_post_by_full_path(str_replace(get_site_url(),'',$meta['featured_article_url'])); ob_start(); ?>
        <?php if(isset($post)): setup_postdata($post); ?>
          <section class="module featured-article">
            <article>
              <?php if(has_post_thumbnail(get_the_ID())): ?>
                <figure>
                  <a href="<?php the_permalink() ?>"><?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'), array("",""), get_the_post_thumbnail(get_the_ID(),'large' )) ?></a>
                </figure>
              <?php endif; ?>
              <h2><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
              <div class="body"><?php the_excerpt() ?></div>
            </article>
          </section>
        <?php endif; wp_reset_postdata(); ?>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['article_urls']) && is_array($meta['article_urls'])): ob_start(); ?>
        <section class="module articles">
          <ul>
            <?php foreach($meta['article_urls'] as $article_url): $post = GtPackages::get_post_by_full_path(str_replace(get_site_url(),'',$article_url)); ?>
              <?php if(isset($post)): setup_postdata($post); ?>
                <li class="<?php has_post_thumbnail(get_the_ID()) ? 'has-featured-image' : '' ?>">
                  <?php if(has_post_thumbnail(get_the_ID())): ?>
                    <figure>
                      <a href="<?php the_permalink() ?>"><?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'), array("",""), get_the_post_thumbnail(get_the_ID(),'large' )) ?></a>
                    </figure>
                  <?php endif; ?>
                  <h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
                  <div class="body"><?php the_excerpt() ?></div>
                </li>
              <?php endif; ?>
            <?php endforeach; wp_reset_postdata(); ?>
          </ul>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['twitter'])): ob_start(); ?>
        <section class="module twitter">
          <?php echo do_shortcode($meta['twitter']) ?>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['youtube'])): ob_start(); ?>
        <section class="module youtube">
          <?php echo do_shortcode($meta['youtube']) ?>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['pinboard'])): ob_start(); ?>
        <section class="module pinboard">
          <?php echo do_shortcode($meta['pinboard']) ?>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['flickr'])): ob_start(); ?>
        <section class="module flickr">
          <?php echo do_shortcode($meta['flickr']) ?>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['poll'])): ob_start(); ?>
        <section class="module poll">
          <?php echo do_shortcode($meta['poll']) ?>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['curation_feed'])): ob_start(); ?>
        <section class="module curation_feed">
          <?php echo do_shortcode($meta['curation_feed']) ?>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['ad_code'])): ob_start(); ?>
        <section class="module ad_code">
          <?php echo $meta['ad_code'] ?>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php if(isset($meta['info_graphic_url'])): ob_start(); ?>
        <section class="module info_graphic_url">
          <figure>
            <img src="<?php echo $meta['info_graphic_url'] ?>" />
          </figure>
        </section>
      <?php $content .= ob_get_contents(); ob_end_clean(); endif; ?>
      
      <?php 
       
      
      return $content;
    });
            
    add_action('admin_init', function() {
      global $wp_query;
            
      wp_enqueue_script( GtPackages::getConst('PREFIX'), GtPackages::getConst('URL').'/js/script.js', array());
      wp_enqueue_script("thickbox");
      wp_enqueue_style("thickbox");
      
      
      add_action('save_post', function($post_id) {
        if (!array_key_exists('gt_packages_meta_noncename',$_POST)) return $post_id;
        if (!wp_verify_nonce($_POST['gt_packages_meta_noncename'],__FILE__)) return $post_id;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

        $current_data = get_post_meta($post_id, '_gt_packages_meta', TRUE);  

        $new_data = $_POST['_gt_packages_meta'];
        
        if(isset($new_data['article_urls'])) {
          $new_data['article_urls'] = explode("\n",$new_data['article_urls']);
        }

        GtPackages::meta_clean($new_data);
        
        if ($current_data) {
          if (is_null($new_data)) { 
            delete_post_meta($post_id,'_gt_packages_meta'); 
          } else {
            update_post_meta($post_id,'_gt_packages_meta',$new_data);
          }
        } elseif (!is_null($new_data)) {
          add_post_meta($post_id,'_gt_packages_meta',$new_data,TRUE);
        }
        
        $refreshed_data = get_post_meta($post_id, '_gt_packages_meta', TRUE);

        return $post_id;
      });

      if (current_user_can('delete_published_posts')) {
        $gt_packages_meta_boxes_has_been_nonced = false;
        $custom_types = array("gt_package");
        $metas = array(
          array(
            "slug" => "gt_packages_meta",
            "title" => "Package Components",
            "placement" => "normal",
            "priority" => "core",
            "callback" => function() { 
              $gt_packages_meta = array_merge(
                array(
                  "location" => false
                ),
                GtPackages::meta_data()
              );
              ?><div class="ajaxtag notypeahead">
                <h4 style="margin-bottom:0;">Featured Article <small>(How To: <a href="">Create a Featured Article</a> | <a href="#">Add the Article's URL</a>)</small></h4>
                <div>
                  <input style="width:100%;" type="text" placeholder="Featured Article URL" class="newtag form-input-tip" name="_gt_packages_meta[featured_article_url]" value="<?php echo $gt_packages_meta['featured_article_url'] ?>" />
                </div>
                
                <h4 style="margin-bottom:0;">Articles <small>(How To: <a href="">Create an Article</a> | <a href="#">Add the Articles' URLs</a>)</small></h4>
                <div>
                  <textarea style="width:100%;height:100px;" placeholder="Article URLs (one per line)" class="newtag form-input-tip" name="_gt_packages_meta[article_urls]"><?php echo isset($gt_packages_meta['article_urls']) ? implode("\n",$gt_packages_meta['article_urls']) : ''  ?></textarea>
                </div>
                
                <h4 style="margin-bottom:0;">YouTube Playlist <small>(How To: <a href="">Create a Playlist</a> | <a href="#">Find a playlist's ID</a>)</small></h4>
                <div>
                  <input style="width:100%;" type="text" placeholder="YouTube Playlist ID" class="newtag form-input-tip" name="_gt_packages_meta[youtube]" value="<?php echo $gt_packages_meta['youtube'] ?>" />
                </div>
                
                <h4 style="margin-bottom:0;">Podcast URL <small>(How To: <a href="">Create a Podcast</a> | <a href="#">Find the Podcast's URL</a>)</small></h4>
                <div>
                  <input style="width:100%;" type="text" placeholder="Podcast's iTunes URL" class="newtag form-input-tip" name="_gt_packages_meta[podcast_url]" value="<?php echo $gt_packages_meta['podcast_url'] ?>" />
                </div>
                
                <h4 style="margin-bottom:0;">Twitter <small>(How To: <a href="">Create a List</a> | <a href="#">Add list to this package</a>)</small></h4>
                <div>
                  <input style="width:100%;" type="text" placeholder="Twitter Shortcode" class="newtag form-input-tip" name="_gt_packages_meta[twitter]" value="<?php echo $gt_packages_meta['twitter'] ?>" />
                </div>
                                
                <h4 style="margin-bottom:0;">Flickr Photostream <small>(How To: <a href="">Create a Photostream</a> | <a href="#">Find the ID</a>)</small></h4>
                <div>
                  <input style="width:100%;" type="text" placeholder="Flickr Shortcode" class="newtag form-input-tip" name="_gt_packages_meta[flickr]" value="<?php echo $gt_packages_meta['flickr'] ?>" />
                </div>
                
                <h4 style="margin-bottom:0;">Pinboard <small>(How To: <a href="">Setup</a> | <a href="#">Create a Pin</a>)</small></h4>
                <div>
                  <input style="width:100%;" type="text" placeholder="Pinboard" class="newtag form-input-tip" name="_gt_packages_meta[pinboard]" value="<?php echo $gt_packages_meta['pinboard'] ?>" />
                </div>
              
                
                <h4 style="margin-bottom:0;">Poll <small>(How To: <a href="">Create a Poll</a> | <a href="#">Find the ID</a>)</small></h4>
                <div>
                  <input style="width:100%;" type="text" placeholder="Poll Id" class="newtag form-input-tip" name="_gt_packages_meta[poll]" value="<?php echo $gt_packages_meta['poll'] ?>" />
                </div>
                
                <h4 style="margin-bottom:0;">Curated Link Feed <small>(How To: <a href="">Create a Creation Feed</a> | <a href="#">Find the Feed's URL</a>)</small></h4>
                <div>
                  <input style="width:100%;" type="text" placeholder="Curation Feed URL" class="newtag form-input-tip" name="_gt_packages_meta[curation_feed]" value="<?php echo $gt_packages_meta['curation_feed'] ?>" />
                </div>
                
                <h4 style="margin-bottom:0;">Ad Code</h4>
                <div>
                  <textarea style="width:100%;height:100px;" placeholder="HTML/Scripts for an advertisement" class="newtag form-input-tip" name="_gt_packages_meta[ad_code]"><?php echo $gt_packages_meta['ad_code']  ?></textarea>
                </div>
                
                <h4 style="margin-bottom:0;">Infographic <small>(How To: <a href="">Create an Infographic</a> | <a href="#">Upload the Infographic</a>)</small></h4>
                <div>
                  <input style="width:80%;" type="text" placeholder="Enter a URL or Click 'Pick Image from Computer'" class="newtag upload_image_value form-input-tip" name="_gt_packages_meta[info_graphic_url]" value="<?php echo $gt_packages_meta['info_graphic_url'] ?>" />
                  <input class="upload_image_button" type="button" value="Pick Image from Computer" />
                </div>
              </div> 
            <?php }
          ),
        );
      
        foreach ($metas as $meta) {
          foreach ($custom_types as $type) {
            $callback = function() use ($meta, &$gt_packages_meta_boxes_has_been_nonced){
              if(!$gt_packages_meta_boxes_has_been_nonced) {
                echo '<input type="hidden" name="gt_packages_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
              }
              $gt_packages_meta_boxes_has_been_nonced = true;
              $meta['callback']();
            };
            add_meta_box('gt_packages_'.$meta['slug'], $meta['title'], $callback, $type, $meta['placement'], $meta['priority']);
          }
        }
      }
    });
    
    add_action('init', function() {
      // register_taxonomy('gt_packagesboard', 'gt_packages', array(
      //   'labels' => array(
      //     'name' => __( 'Packageboards' ),
      //     'singular_name' => __( 'Packageboard' ),
      //     'search_items' => __( 'Search Packageboards' ),
      //     'popular_items' => __( 'Popular Packageboards' ),
      //     'all_items' => __( 'All Packageboards' ),
      //     'popular_items' => __( 'Popular Packageboards' ),
      //     'add_new_item' => __( 'Add New Packageboard' ),
      //     'edit_item' => __( 'Edit Packageboard' ),
      //     'new_item' => __( 'New Packageboard' ),
      //     'update_item' => __( 'Update Packageboard' ),
      //     'new_item_name' => __( 'New Packageboard Name' ),
      //     'add_or_remove_items' => __( 'Add or remove Packageboards' ),
      //     'choose_from_most_used' => __( 'Choose from the most used Packageboards' ),
      //     'menu_name' => __( 'Packageboards' )
      //   ),
      //   'public' => true,
      //   'show_in_nav_menus' => true,
      //   'show_ui' => true,
      //   'show_tagcloud' => false,
      //   'hierarchical' => true,
      // ));
      
      register_post_type( 'gt_package',
        array(
          'labels' => array(
            'name' => __( 'Packages' ),
            'singular_name' => __( 'Package' ),
            'add_new_item' => __( 'Add New Package' ),
            'edit_item' => __( 'Edit Package' ),
            'new_item' => __( 'New Package' ),
            'view_item' => __( 'View Package' ),
            'search_items' => __( 'Search Packages' ),
            'not_found' => __( 'No packages found' ),
            'not_found_in_trash' => __( 'No packages found in trash' ),
          ),
          'description' => __( 'Packages are packages of content' ),
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
          'rewrite' => array('slug' => 'packages'),
          'taxonomies' => array('category','post_tag')
        )
      );
    });
    
    add_action('init',function() {    
     
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
  
  public static function get_post_by_full_path($path) {
    $pieces = explode("/",$path);
    $path = $pieces[sizeof($pieces)-1];
    
    if(trim($path) == '') {
      $path = $pieces[sizeof($pieces)-2];
    }
    
    return get_page_by_path($path,'OBJECT','post');
  }
  
  public static function meta_data($reload=false) {
    global $wp_query, $post, $gt_packages_meta_data;

    $meta = $gt_packages_meta_data;
    if($reload) {
      if(is_search() || is_category()) {
        $meta = get_post_meta(get_option('page_for_posts'),'_gt_packages_meta',TRUE);
      } elseif(isset($wp_query->queried_object)) {
        $meta = get_post_meta($wp_query->queried_object->ID,'_gt_packages_meta',TRUE);
      } elseif(isset($post)) {
        $meta = get_post_meta($post->ID,'_gt_packages_meta',TRUE);
      }
    } else {
      if(isset($gt_packages_meta_data)) {
        $meta = $gt_packages_meta_data;
      } else {
        if(is_search() || is_category()) {
          $meta = get_post_meta(get_option('page_for_posts'),'_gt_packages_meta',TRUE);
        } elseif(isset($wp_query->queried_object) && isset($wp_query->queried_object->ID)) {
          $meta =  get_post_meta($wp_query->queried_object->ID,'_gt_packages_meta',TRUE);
        } elseif(isset($post)) {
          $meta = get_post_meta($post->ID,'_gt_packages_meta',TRUE);
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
}

class GtArticleList_Widget extends WP_Widget {
  function __construct() {
    $this->default_instance = array( 
      'title' => 'Article List', 
      'article_urls' => array(), 
      "description" => 'Put an optional description in here'
    );
    
    parent::__construct( 
      'gunner_technology_article_list', 
      'Article List Widget', 
      array( 'description' => 'Allows editors to create a list of content simply by pasting urls', 'classname' => 'gunner-technology-article-list' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
     extract( $args );
     $instance = wp_parse_args( (array) $instance, $this->default_instance);

     $title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
     $description = do_shortcode($instance['description']);
     
     $article_urls = $instance['article_urls'];
     
     
     if(isset($article_urls) && !is_array($article_urls)) {
       $article_urls = explode(',',$article_urls);
     }


     ?>
     <?php echo $before_widget ?>
     <?php if ( $title ) {
       echo $before_title . $title . $after_title;
     } ?>
     <?php if($description): ?>
       <?php echo wpautop($description) ?>
     <?php endif; ?>
     
     <ul>
       <?php foreach($article_urls as $article_url): $post = GtPackages::get_post_by_full_path(str_replace(get_site_url(),'',$article_url)); ?>
         <?php if(isset($post)): setup_postdata($post); ?>
           <li class="<?php has_post_thumbnail($post->ID) ? 'has-featured-image' : '' ?>">
             <?php if(has_post_thumbnail($post->ID)): ?>
               <figure>
                 <a href="<?php the_permalink($post->ID) ?>"><?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'), array("",""), get_the_post_thumbnail($post->ID,'large' )) ?></a>
               </figure>
             <?php endif; ?>
             <h4><a href="<?php echo get_permalink($post->ID) ?>"><?php echo get_the_title($post->ID) ?></a></h4>
             <div class="body"><?php echo apply_filters('the_excerpt',$post->post_excerpt) ?></div>
           </li>
         <?php endif; ?>
       <?php endforeach; wp_reset_postdata(); ?>
     </ul>
     
     <?php echo $after_widget ?>
   <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['description'] = $new_instance['description'];
    
    $instance['article_urls'] = explode("\n",$new_instance['article_urls']);

    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, $this->default_instance);
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
      <label for="<?php echo $this->get_field_id( 'article_urls' ); ?>">Article URLs:</label><br />
      <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('article_urls'); ?>" name="<?php echo $this->get_field_name('article_urls'); ?>"><?php echo isset($instance['article_urls']) ? implode("\n",$instance['article_urls']) : '' ?></textarea>
    </p>
    
  <?php 
  }

}



class GtFeaturedArticle_Widget extends WP_Widget {
  function __construct() {
    $this->default_instance = array( 
      'title' => 'Featured Article', 
      'article_url' => "", 
      "description" => 'Put an optional description in here'
    );
    
    parent::__construct( 
      'gunner_technology_featured_article', 
      'Featured Article Widget', 
      array( 'description' => 'Allows editors to feature an article simply by pasting a url', 'classname' => 'gunner-technology-featured-article' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
     extract( $args );
     $instance = wp_parse_args( (array) $instance, $this->default_instance);

     $title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
     $description = do_shortcode($instance['description']);
     
     $article_url = $instance['article_url'];
     


     ?>
     <?php echo $before_widget ?>
     <?php if ( $title ) {
       echo $before_title . $title . $after_title;
     } ?>
     <?php if($description): ?>
       <?php echo $description ?>
     <?php endif; ?>
     
     <?php if(isset($article_url)): $post = GtPackages::get_post_by_full_path(str_replace(get_site_url(),'',$article_url));?>
       <?php if(isset($post)): setup_postdata($post); ?>
         <section class="module featured-article">
           <article>
             <?php if(has_post_thumbnail($post->ID)): ?>
               <figure>
                 <a href="<?php echo get_permalink($post->ID) ?>"><?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'), array("",""), get_the_post_thumbnail($post->ID,'large' )) ?></a>
               </figure>
             <?php endif; ?>
             <h2><a href="<?php echo get_permalink($post->ID) ?>"><?php echo get_the_title($post->ID) ?></a></h2>
             <div class="body"><?php echo apply_filters('the_excerpt',$post->post_excerpt) ?></div>
           </article>
         </section>
       <?php endif; wp_reset_postdata(); ?>
     <?php endif; ?>
     
     <?php echo $after_widget ?>
   <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['description'] = $new_instance['description'];
    
    $instance['article_url'] = strip_tags($new_instance['article_url']);

    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, $this->default_instance);
  ?>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'article_url' ); ?>">Article URL:</label><br />
      <input type="text" id="<?php echo $this->get_field_id('article_url'); ?>" name="<?php echo $this->get_field_name('article_url'); ?>" value="<?php echo $instance['article_url'] ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description (optional):</label><br />
      <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $instance['description']; ?></textarea>
    </p>
    
  <?php 
  }

}

class GtInfographic_Widget extends WP_Widget {
  function __construct() {
    $this->default_instance = array( 
      'title' => 'Infographic Widget', 
      'infographic_url' => "", 
      "description" => 'Put an optional description in here'
    );
    
    parent::__construct( 
      'gunner_technology_infographic', 
      'Infographic Widget', 
      array( 'description' => 'Upload an infographic and display it in a wiget', 'classname' => 'gunner-technology-infographic' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
     extract( $args );
     $instance = wp_parse_args( (array) $instance, $this->default_instance);

     $title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
     $description = do_shortcode($instance['description']);

     ?>
     <?php echo $before_widget ?>
     <?php if ( $title ) {
       echo $before_title . $title . $after_title;
     } ?>
     
     <figure>
       <a href="<?php echo $instance['infographic_url'] ?>">
        <img src="<?php echo $instance['infographic_url'] ?>" title="<?php echo strip_tags($title) ?>" alt="<?php echo strip_tags($title) ?>" />
       </a>
       <?php if(isset($description)): ?>
        <figcaption><?php echo $description; ?></figcaption>
       <?php endif; ?>
     </figure>
     
     <?php echo $after_widget ?>
   <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['description'] = $new_instance['description'];
    
    $instance['infographic_url'] = strip_tags($new_instance['infographic_url']);

    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, $this->default_instance);
  ?>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'infographic_url' ); ?>">Infographic URL:</label><br />
      <input style="width:80%;" type="text" placeholder="Enter a URL or Click 'Pick Image from Computer'" class="newtag upload_image_value form-input-tip" id="<?php echo $this->get_field_id('infographic_url'); ?>" name="<?php echo $this->get_field_name('infographic_url'); ?>" value="<?php echo $instance['infographic_url'] ?>" />
      <input class="upload_image_button" type="button" value="Pick Image from Computer" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description (optional):</label><br />
      <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $instance['description']; ?></textarea>
    </p>
    
  <?php 
  }

}



class GtPodcast_Widget extends WP_Widget {
  function __construct() {
    $this->default_instance = array( 
      'title' => 'Podcast Widget', 
      'audio_url' => "", 
      'itunes_url' => "", 
      'cover_url' => "", 
      "description" => 'Put an optional description in here'
    );
    
    parent::__construct( 
      'gunner_technology_podcast', 
      'Podcast Widget', 
      array( 'description' => 'Easily add a Podcast episode via a widget', 'classname' => 'gunner-technology-podcast' ),
      array( 'width' => 300, 'height' => 350)
    );
  }


  function widget( $args, $instance ) {
     extract( $args );
     $instance = wp_parse_args( (array) $instance, $this->default_instance);

     $title = do_shortcode(apply_filters('widget_title', $instance['title'] ));
     $description = do_shortcode($instance['description']);

     ?>
     <?php echo $before_widget ?>
     <?php if ( $title ) {
       echo $before_title . $title . $after_title;
     } ?>
     
     <?php if(isset($instance['cover_url'])): ?>
       <figure>
         <img src="<?php echo $instance['cover_url'] ?>" title="<?php echo strip_tags($title) ?>" alt="<?php echo strip_tags($title) ?>" />
       </figure>
      <?php endif; ?>
      
      <?php if(isset($instance['audio_url'])): ?>
        <audio src="<?php echo $instance['audio_url'] ?>" controls preload="auto" autobuffer></audio>
      <?php endif; ?>
      
      <?php if(isset($instance['itunes_url'])): ?>
        <a class="call-to-action" href="<?php echo $instance['itunes_url'] ?>">Get it on iTunes</a>
      <?php endif; ?>
     
     <?php echo $after_widget ?>
   <?php
  }
  
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['description'] = $new_instance['description'];
    
    $instance['cover_url'] = strip_tags($new_instance['cover_url']);
    $instance['itunes_url'] = strip_tags($new_instance['itunes_url']);
    $instance['audio_url'] = strip_tags($new_instance['audio_urll']);

    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, $this->default_instance);
  ?>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'audio_urll' ); ?>">Audio URL:</label><br />
      <input style="width:80%;" type="text" placeholder="Enter a URL or Click 'Pick File from Computer'" class="newtag upload_image_value form-input-tip" id="<?php echo $this->get_field_id('audio_urll'); ?>" name="<?php echo $this->get_field_name('audio_urll'); ?>" value="<?php echo $instance['audio_urll'] ?>" />
      <input class="upload_image_button" type="button" value="Pick Image from Computer" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'cover_url' ); ?>">Cover Graphic URL:</label><br />
      <input style="width:80%;" type="text" placeholder="Enter a URL or Click 'Pick Image from Computer'" class="newtag upload_image_value form-input-tip" id="<?php echo $this->get_field_id('cover_url'); ?>" name="<?php echo $this->get_field_name('cover_url'); ?>" value="<?php echo $instance['cover_url'] ?>" />
      <input class="upload_image_button" type="button" value="Pick Image from Computer" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'itunes_url' ); ?>">iTunes URL:</label><br />
      <input id="<?php echo $this->get_field_id( 'itunes_url' ); ?>" name="<?php echo $this->get_field_name( 'itunes_url' ); ?>" value="<?php echo $instance['itunes_url']; ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description (optional):</label><br />
      <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $instance['description']; ?></textarea>
    </p>
    
  <?php 
  }

}


register_activation_hook( __FILE__, array('GtPackages', 'activate') );
register_activation_hook( __FILE__, array('GtPackages', 'deactivate') );
register_activation_hook( __FILE__, array('GtPackages', 'uninstall') );

add_action('plugins_loaded', array('GtPackages', 'setup') );