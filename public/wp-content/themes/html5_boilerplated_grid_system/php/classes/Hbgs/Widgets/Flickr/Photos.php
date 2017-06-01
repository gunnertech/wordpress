<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Photos
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Photos.php 2010-11-18 codyswann
 */


require_once(hbgs_theme_path().'php/lib/phpFlickr/phpFlickr.php');

class Hbgs_Widgets_Flickr_Photos extends Hbgs_Widget {
  function __construct() {
  	$widget_ops = array( 'classname' => 'hbgs-widgets-flickr-photos-widget', 'description' => 'A Widget the displays photo albums from flickr' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'hbgs-widgets-flickr-photos-widget' );
  	

  	parent::__construct( 'hbgs-widgets-flickr-photos-widget', 'Hbgs Widgets Flickr Photos', $widget_ops, $control_ops );
  	
  	add_action('wp_ajax_photo_album', array(&$this,"render_album"));
  	add_action('wp_ajax_nopriv_photo_album', array(&$this,"render_album"));
  }
  
  function print_default_styles() { 
    wp_enqueue_style('flickr',get_bloginfo('template_url').'/css/flickr.css');
    wp_enqueue_style('fancybox',get_bloginfo('template_url').'/css/jquery.fancybox-1.3.4.css');
  }
  
  function getSrc ($size, $specs) {
		$specs['id'] = $specs[isset($specs['primary'])? 'primary': 'id'];
		$sizes = array(
			'Square'=>'_s',
			'Thumbnail'=>'_t',
			'Small'=>'_m',
			'Medium'=>'',
			'Large'=>'_b'
		);
		return 'http://farm' . $specs['farm'] . '.static.flickr.com/' . $specs['server'] . '/' . $specs['id'] . '_' . $specs['secret'] . $sizes[$size] . '.jpg';
	}
	
	function render_album($instance=null) {
	  $exclude = array('Product Giveaways');
	  if(!$instance) {
	    $settings = $this->get_settings();
  	  $instance = $settings[3];
  	  
  	  for($i=0; $i<100; $i++) {
  	    if(isset($settings[$i])) {
  	      $instance = $settings[$i];
  	      break;
  	    }
  	  }
	  }
	  
	  if(!isset($instance['key']) || $instance['key'] == '') {
	    $instance['key'] = '86e47f54f1e07d1dc3e6198852645303';
	  }
	  
	  
	  
	  if(!isset($instance['secret']) || $instance['secret'] == '') {
	    $instance['secret'] = '15dd3515b855ed68';
	  }
	  
	  if(!isset($instance['uid']) || $instance['uid'] == '') {
	    $instance['uid'] = '64809370@N07';
	  }
	  
	  
	  $f = new phpFlickr($instance['key'], $instance['secret'], true);

  	//Request
  	$id = $_REQUEST['id'];

  	//Info about album
  	$info = $f->photosets_getInfo($id, $instance['key']);

  	//Get photos
  	$feed = $f->photosets_getPhotos($id, $instance['key'], '', 500, 1, 1, 'photos');
  	$total = $feed['photoset']['total'];
  	$photos = $feed['photoset']['photo'];
  	
  	$thumbs_per_page = isset($instance["lightbox_thumbnails"]) ? $instance["lightbox_thumbnails"] : 6;

  	//Some variables
  	$pages = ceil($total / $thumbs_per_page);

  	//Get other albums
  	$set_resp = $f->photosets_getList($instance['uid'], $instance['key']);
  	$set_resp = $set_resp['photoset'];
  	$nmr_sets = count($set_resp);
  	$sets = array();
  	for ($i = 0; $i < $nmr_sets; $i += 1) {
  		$sets[$set_resp[$i]['title']] =& $set_resp[$i];
  	}
  	//Make sure this album isn't included
  	unset($sets[$info['title']]);

  	//Make sure specified albums aren't included
  	foreach ($exclude as $e) {
  		unset($sets[$e]);
  	}
  	$i = 0;
  	$other_album_max = (int)(isset($instance["other_album_max"]) ? $instance["other_album_max"] : 4);
  	?>
  	<section class="album">
    	<hgroup><h1 id="selected-photo-title"><?php echo $photos[0]['title'] ?></h1></hgroup>
    	<div class="clearfix">
      	<div id="main_image_wrapper" style="<?php echo (intval($instance['featured_image_height']) > 0 ? 'height:' . $instance['featured_image_height'] . 'px; overflow:hidden;' : '') ?>">
      	  <img data-largeimg="<?php echo $this->getSrc('Large', $photos[0])?>" style="max-width:<?php echo $instance['featured_image_width'] ?>px;" id="main_image" src="<?php echo $this->getSrc('Medium', $photos[0])?>" alt="<?php echo $photos[0]['title'] ?>" />
      	</div>
      	<aside class="other_albums_wrapper">
      	  <p><?php echo $instance["album_teaser"] ?></p>
        	<ul id="other_albums">
        		<?php foreach ($sets as $s): $i += 1; $myinfo = $f->photosets_getInfo($s['id'], $instance['key']); ?>
        			<li>
        			  <a class="go_photoset" data-setid="go_photoset_<?php echo $s['id'] ?>" id="go_photoset_<?php echo $s['id'] ?>" href="?id=<?php echo $s['id'] ?>&action=photo_album">
          			  <div class="image-wrapper">
          			    <span><img style="width: <?php echo $instance['secondary_album_cover_width'] ?>px;" src="<?php echo  $this->getSrc('Square', $s) ?>" alt="" /></span>
          			  </div>
          			  <h3><?php echo  $myinfo['title'] ?></h3>
          			  <div class="clear"></div>
          			</a>
        			</li>
        			<?php if ($i >= $other_album_max) { break; } ?>
        		<?php endforeach; ?>
        	</ul>
        </aside>
      </div>
      
      <nav class="thumb-nav clearfix">
        <a class="thumb_page_previous">Previous</a>
      	<ul id="album_thumbs" class="clearfix">
          <?php for ($i = 0; $i < $pages; $i += 1): ?>
        		<li class="page<?php echo  $i==0 ? ' selected' : '' ?>">
        			<ul class="thumbs clearfix">
        		    <?php $myphotos = array_slice($photos, $i * $thumbs_per_page, $thumbs_per_page); foreach ($myphotos as $p): ?>
        				  <li class="thumb">
        				    <span style="height:<?php echo $instance['navigation_thumb_height'] ?>px; overflow:hidden; display:block;">
        				      <a data-largeimg="<?php echo $this->getSrc('Large', $p)?>" title="<?php echo esc_attr(isset($p['title']) ? $p['title'] : '') ?>" href="<?php echo  $this->getSrc('Medium', $p) ?>"><img style="width:<?php echo $instance['navigation_thumb_width'] ?>px;" src="<?php echo  $this->getSrc('Small', $p) ?>" alt="<?php echo  str_replace('"','\\"',strip_tags(isset($p['info']['description']) ? $p['info']['description'] : "")) ?>" /></a>
        				    </span>
        				  </li>
        		    <?php endforeach; ?>
        			</ul>
        		</li>
        	<?php endfor; ?>
      	</ul>
      	<a class="thumb_page_next">Next</a>
      </nav>
    </section>
    <ul class="full-image-options clearfix">
      <li><a class="full-image-download" target="_blank" href="">Full Image</a></li>
      <li><a class="full-image-back" href="">Back to Album</a></li>
    </ul>
	<?php die(); }
	
	function print_scripts($id,$instance,$scripts=null) {
    $instance['scripts'] .= '
    $(".full-image-back").live("click",function() {
      $("#fancybox-content").css({
        backgroundImage:"none"
      });
      $("#fancybox-wrap").removeClass("show-full");
      return false;
    });

    $(".backgroundsize #main_image").live("click",function() {
      $(".full-image-download").attr("href",$(this).data("largeimg"));
      $("#fancybox-content").css({
        backgroundImage:"url("+$(this).data("largeimg")+")"
      });
      $("#fancybox-wrap").addClass("show-full");
    });

    $(".page_nav a").click(function(){
      $(this).parents(".page_nav").find("a").removeClass("selected");
      $(this).addClass("selected");
      $(".pages .page").removeClass("selected");
      $(".pages #page_"+$(this).text()).addClass("selected");

      return false;
    });

    $(".thumb-nav .thumb a").live("click",function(){
      $("#main_image").attr("src",$(this).attr("href"));
      $("#main_image").data("largeimg",$(this).data("largeimg"));
      $("#selected-photo-title").html($(this).attr("title"))
      return false;
    });

    $(["previous","next"]).each(function(i,item){
      $(".thumb_page_"+item).live("click",function(){
        var $pages = $(this).parents(".thumb-nav").find(".page");
        var func = (item == "previous" ? "prev" : item);
        var $selected = $pages.filter(".selected");
        var $target = $selected[func](".page");

        func = (item == "previous" ? "last" : "first");
        $selected.removeClass("selected");
        if($target.size()) {
          $target.addClass("selected");
        } else {
          $pages[func]().addClass("selected");
        }
        return false;
      });
    });

    $(".go_photoset").live("click",function(){
      $.fancybox({content: "<div class=\"loading\">Loading...</div>"});
      $.ajax({
        url:"/wp-admin/admin-ajax.php",
        type:"GET",
        data:"action=photo_album&id="+$(this).data("setid").replace("go_photoset_",""),
        success:function(results) {
          $.fancybox({
            centerOnScroll: false,
            content: results,
            transitionIn	:	"elastic",
        		transitionOut	:	"elastic",
        		speedIn		:	200, 
        		speedOut		:	200, 
        		overlayShow	:	true,
        		overlayOpacity: .75,
        		overlayColor: "#000",
        		onComplete: function () {
        			$("object,embed").css("display", "none");
        		},
        		onCleanup: function() {
        		  $("#fancybox-wrap").removeClass("show-full");
        		  $("#fancybox-content").css({
                backgroundImage:"none"
              });
        		},
        		onClosed: function () {
        			$("object,embed").css("display", "block");
        		}
          });
        }
      });
      return false;
    });
    ';
    parent::print_scripts($id,$instance);
  }
  
  function print_default_scripts() { 
    wp_enqueue_script( 'easing', get_bloginfo('template_url').'/js/libs/jquery.easing-1.3.pack.js', array('jquery'));
    wp_enqueue_script( 'fancybox', get_bloginfo('template_url').'/js/libs/jquery.fancybox-1.3.4.pack.js', array('jquery'));
  }
	
	function cache_expire() {
	  return 60*30;
	}
  
  function render( $args, $instance ) {
    
    extract( $args );
				
		$title = apply_filters('widget_title', $instance['title'] );
		$excerpt = $instance['excerpt'];
				
		$f = new phpFlickr($instance['key'], $instance['secret'], true);
		$set_resp = $f->photosets_getList($instance['uid'], $instance['key']);
  	$set_resp = $set_resp['photoset'];
  	$nmr_sets = count($set_resp);
  	$sets = array();
  	$sets_per_page = $instance['sets_per_page'];
  	
  	for ($i = 0; $i < $nmr_sets; $i += 1) {
  		$sets[$set_resp[$i]['title']] =& $set_resp[$i];
  	}
  	
  	$exclude = array('Product Giveaways');
  	foreach ($exclude as $e) {
  		unset($sets[$e]);
  	}
  	
  	$pages = ceil(count($sets) / $sets_per_page);
		?>
		<?php echo $before_widget ?>
		  <?php if ( $title ) echo $before_title . do_shortcode($title) . $after_title; ?>
		  <?php if ( $excerpt ) echo '<p class="excerpt">' . do_shortcode($excerpt) . '</p>'; ?>
		  <?php if(isset($_REQUEST['id']) && $_REQUEST['action'] == 'photo_album'): ?>
		    <?php $this->render_album($instance) ?>
		  <?php else: ?>
  		  <ul class="pages">
    		  <?php for ($i = 0; $i < $pages; $i += 1): $mysets = array_slice($sets, $i * $sets_per_page, $sets_per_page) ?>
      		  <li id="page_<?php echo ($i + 1) ?>" <?php echo ($i==0 ? ' class="page selected"' : ' class="page"') ?>>
              <ul class="thumbs">
        			  <?php foreach ($mysets as $s): ?>
        				  <li class="thumb" style="width:<?php echo $instance['max_photo_album_cover_width'] ?>px;">
        				    <a href="?id=<?php echo $s['id'] ?>&action=photo_album" data-setid="go_photoset_<?php echo  $s['id'] ?>" id="go_photoset_<?php echo  $s['id'] ?>" class="go_photoset">
        				      <span style="height:<?php echo $instance['max_photo_album_cover_height'] ?>px; overflow:hidden; display:block;">
        				        <img style="width:<?php echo $instance['max_photo_album_cover_width'] ?>px;" src="<?php echo  $this->getSrc('Medium', $s) ?>" alt="<?php echo  $s['title'] ?>" />
        				      </span>
        				    </a>
        				    <span class="qty"><?php echo  $s['photos'] ?></span>
        				    <p><a data-setid="go_photoset_<?php echo $s['id'] ?>" href="?id=<?php echo $s['id'] ?>&action=photo_album" class="go_photoset"><?php echo  $s['title'] ?></a></p>
        				  </li>
        			  <?php endforeach; ?>
              </ul>
      		  </li>
    	    <?php endfor; ?>
    	  </ul>
        <?php if ($pages > 1): ?>
          <div class="page_footer">
            <span>Page:</span>
            <ul class="page_nav">
      			  <?php for ($i = 1; $i <= $pages; $i += 1): ?>
      				  <li><a href="#" <?php echo ($i === 1? ' class="selected"' : '') ?>><?php echo  $i ?></a><?php echo ($i != $pages ? '<span class="seperator">|</span>' : '') ?></li>
    				  <?php endfor; ?>
            </ul>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    <?php echo $after_widget ?>

    <?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['album_teaser'] = $new_instance['album_teaser'];
		$instance['excerpt'] = $new_instance['excerpt'];
		$instance['key'] = strip_tags($new_instance['key']);
		$instance['secret'] = strip_tags($new_instance['secret']);
		$instance['user'] = strip_tags($new_instance['user']);
		$instance['uid'] = strip_tags($new_instance['uid']);
		$instance['sets_per_page'] = intval($new_instance['sets_per_page']);
		$instance['max_photo_album_cover_width'] = intval($new_instance['max_photo_album_cover_width']);
		$instance['max_photo_album_cover_height'] = intval($new_instance['max_photo_album_cover_height']);
		$instance['featured_image_width'] = intval($new_instance['featured_image_width']);
		$instance['featured_image_height'] = intval($new_instance['featured_image_height']);
		$instance['secondary_album_cover_width'] = intval($new_instance['secondary_album_cover_width']);
		$instance['navigation_thumb_width'] = intval($new_instance['navigation_thumb_width']);
		$instance['navigation_thumb_height'] = intval($new_instance['navigation_thumb_height']);
		$instance['other_album_max'] = intval($new_instance['other_album_max']);
		$instance['lightbox_thumbnails'] = intval($new_instance['lightbox_thumbnails']);
		
		
		
		return $instance;
	}
	
	function form( $instance ) {
	  global $wp_post_statuses;
	  
		$defaults = array( 
		  'title' => '',
		  'album_teaser' => 'Check Out Some Other Great Photos:',
		  'sets_per_page' => 5,
		  'max_photo_album_cover_width' => 243,
		  'max_photo_album_cover_height' => 160,
		  'featured_image_width' => 600,
		  'featured_image_height' => 0,
		  'secondary_album_cover_width' => 68,
		  'navigation_thumb_width' => 136,
		  'navigation_thumb_height' => 87,
		  'other_album_max' => 4,
		  'excerpt' => '',
		  'key' => '',
		  'secret' => '',
		  'user' => '',
		  'uid' => '',
		  'lightbox_thumbnails' => 6
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
		  <label for="<?php echo $this->get_field_id('album_teaser'); ?>"><?php _e('Album Teaser:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('album_teaser'); ?>" name="<?php echo $this->get_field_name('album_teaser'); ?>" type="text" value="<?php echo esc_attr($instance['album_teaser']); ?>" />
  	</p>
		<p>
		  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
  	</p>
		<p>
		  <label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Excerpt:'); ?></label>
		  <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>"><?php echo $instance['excerpt']; ?></textarea>
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('max_photo_album_cover_width'); ?>"><?php _e('Max Photo Album Cover Width:'); ?></label>
		  <input size="3" id="<?php echo $this->get_field_id('max_photo_album_cover_width'); ?>" name="<?php echo $this->get_field_name('max_photo_album_cover_width'); ?>" type="text" value="<?php echo $instance['max_photo_album_cover_width']; ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('secondary_album_cover_width'); ?>"><?php _e('Secondary Album Cover Width:'); ?></label>
		  <input size="3" id="<?php echo $this->get_field_id('secondary_album_cover_width'); ?>" name="<?php echo $this->get_field_name('secondary_album_cover_width'); ?>" type="text" value="<?php echo $instance['secondary_album_cover_width']; ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('navigation_thumb_width'); ?>"><?php _e('Navigation Thumbnail Width:'); ?></label>
		  <input size="3" id="<?php echo $this->get_field_id('navigation_thumb_width'); ?>" name="<?php echo $this->get_field_name('navigation_thumb_width'); ?>" type="text" value="<?php echo $instance['navigation_thumb_width']; ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('navigation_thumb_height'); ?>"><?php _e('Navigation Thumbnail Height:'); ?></label>
		  <input size="3" id="<?php echo $this->get_field_id('navigation_thumb_height'); ?>" name="<?php echo $this->get_field_name('navigation_thumb_height'); ?>" type="text" value="<?php echo $instance['navigation_thumb_height']; ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('featured_image_width'); ?>"><?php _e('Featured Image Width:'); ?></label>
		  <input size="3" id="<?php echo $this->get_field_id('featured_image_width'); ?>" name="<?php echo $this->get_field_name('featured_image_width'); ?>" type="text" value="<?php echo $instance['featured_image_width']; ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('featured_image_height'); ?>"><?php _e('Featured Image Height (optional):'); ?></label>
		  <input size="3" id="<?php echo $this->get_field_id('featured_image_height'); ?>" name="<?php echo $this->get_field_name('featured_image_height'); ?>" type="text" value="<?php echo $instance['featured_image_height']; ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('max_photo_album_cover_height'); ?>"><?php _e('Max Photo Album Cover Height:'); ?></label>
		  <input size="3" id="<?php echo $this->get_field_id('max_photo_album_cover_height'); ?>" name="<?php echo $this->get_field_name('max_photo_album_cover_height'); ?>" type="text" value="<?php echo $instance['max_photo_album_cover_height']; ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('key'); ?>"><?php _e('Flickr Key:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('key'); ?>" name="<?php echo $this->get_field_name('key'); ?>" type="text" value="<?php echo esc_attr($instance['key']); ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('secret'); ?>"><?php _e('Flickr Secret:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('secret'); ?>" name="<?php echo $this->get_field_name('secret'); ?>" type="text" value="<?php echo esc_attr($instance['secret']); ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('user'); ?>"><?php _e('Flickr User:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>" type="text" value="<?php echo esc_attr($instance['user']); ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('uid'); ?>"><?php _e('Flickr UID:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('uid'); ?>" name="<?php echo $this->get_field_name('uid'); ?>" type="text" value="<?php echo esc_attr($instance['uid']); ?>" />
  	</p>
	  <p>
		  <label for="<?php echo $this->get_field_id('sets_per_page') ?>"><?php _e('Sets Per Page:') ?></label>
		  <select id="<?php echo $this->get_field_id('sets_per_page') ?>" name="<?php echo $this->get_field_name('sets_per_page') ?>">
		    <?php for($i=1; $i<=20; $i++): ?>
		      <option value="<?php echo $i ?>" <?php selected($instance['sets_per_page'],$i) ?>><?php echo $i ?></option>
		    <?php endfor; ?>
		  </select>
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('lightbox_thumbnails') ?>"><?php _e('Thumbs in Lightbox:') ?></label>
		  <select id="<?php echo $this->get_field_id('lightbox_thumbnails') ?>" name="<?php echo $this->get_field_name('lightbox_thumbnails') ?>">
		    <?php for($i=1; $i<=10; $i++): ?>
		      <option value="<?php echo $i ?>" <?php selected($instance['lightbox_thumbnails'],$i) ?>><?php echo $i ?></option>
		    <?php endfor; ?>
		  </select>
  	</p>
  	<p>
		  <label for="<?php echo $this->get_field_id('other_album_max') ?>"><?php _e('Max Albums in Lightbox:') ?></label>
		  <select id="<?php echo $this->get_field_id('other_album_max') ?>" name="<?php echo $this->get_field_name('other_album_max') ?>">
		    <?php for($i=1; $i<=20; $i++): ?>
		      <option value="<?php echo $i ?>" <?php selected($instance['other_album_max'],$i) ?>><?php echo $i ?></option>
		    <?php endfor; ?>
		  </select>
  	</p>
		<?php
  }
}