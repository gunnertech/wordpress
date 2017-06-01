<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
 
 global $data; //get theme options
?>


<?php
	//get custom post type === > Slides
	global $post;
	$args = array(
		'post_type' =>'slides',
		'numberposts' => -1,
		'order' => 'ASC'
	);
	$slides = get_posts($args);
?>
<?php if($slides) { ?>
<div id="slider-wrap">
	<div id="full-slides" class="flexslider clearfix">
		<ul class="slides">
            <?php
            foreach($slides as $post) : setup_postdata($post);
			
			//image
            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'slider');
			
			//meta
            $slidelink = get_post_meta($post->ID, 'office_slides_url', TRUE);
			$slide_description = get_post_meta($post->ID, 'office_slides_description', TRUE);
			$enable_caption = get_post_meta($post->ID, 'office_enable_caption', TRUE);
			$slides_video = get_post_meta($post->ID, 'office_slides_video', TRUE);
            ?>
            	<?php if (has_post_thumbnail()) { ?>
            	<li class="slide">
                <?php if(!empty($slidelink)) { ?>
                    <a href="<?php echo $slidelink ?>" title="<?php the_title(); ?>"><img src="<?php echo $featured_image[0]; ?>" /></a>
                <?php } else { ?> 
                <img src="<?php echo $featured_image[0]; ?>" alt="<?php the_title(); ?>" width="<?php echo $featured_image[1]; ?>" height="<?php echo $featured_image[2]; ?>" />
                <?php } ?>
                <?php if(!empty($enable_caption) && $enable_caption == 'enable') { ?>
				<div class="caption">
					<h3><?php the_title(); ?></h3>
                    <?php if(!empty($slide_description)) { echo '<p> '. $slide_description .'</p>'; } ?>
				</div>
                <!-- /caption -->
                <?php } ?>
			</li><!--/slide -->
            <?php } ?>
            <?php endforeach; ?>
		</ul><!-- /slides -->
    </div><!--/full-slides -->
</div>
<!-- /slider-wrap -->
<?php } ?>
