<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>

<?php
	//find images in the content with "wp-image-{n}" in the class name
	preg_match_all('/<img[^>]?class=["|\'][^"]*wp-image-([0-9]*)[^"]*["|\'][^>]*>/i', get_the_content(), $result);  
	//echo '<pre>' . htmlspecialchars( print_r($result, true) ) .'</pre>';
	$exclude_imgs = $result[1];

	//attachement loop
	$args = array(
		'orderby' => 'menu_order',
		'post_type' => 'attachment',
		'post_parent' => get_the_ID(),
		'post_mime_type' => 'image',
		'post_status' => null,
		'posts_per_page' => -1,
		'exclude' => $exclude_imgs
	);
	$attachments = get_posts($args);   
?>
<div id="slider-wrap">
	<div id="full-slides" class="flexslider clearfix">
		<ul class="slides">
			<?php
			// start loop
	        foreach ($attachments as $attachment) :
			//get img
			$slide_img = wp_get_attachment_image_src( $attachment->ID, 'slider');
			//set variables
			$slides_description = $attachment->post_content;
			?>		
			<li class="slide">
				<img src="<?php echo $slide_img[0]; ?>" alt="<?php echo apply_filters('the_title', $attachment->post_title); ?>" />
                <?php if(!empty($slides_description)) { ?>
                <div class="caption">
					<h3><?php echo apply_filters('the_title', $attachment->post_title); ?></h3>
                    <p><?php echo $slides_description; ?></p>
				</div>
                <!-- /caption -->
                <?php } ?>
	        </li>
            <!--/slide --> 
			<?php endforeach; ?>
		</ul><!-- /slides -->
    </div><!--/full-slides -->
</div>
<!-- /slider-wrap -->