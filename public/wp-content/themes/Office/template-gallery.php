<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 * Template Name: Photo Gallery
 */
?>
<?php get_header(); ?>

<?php
if (have_posts()) : while (have_posts()) : the_post();
$page_slider = get_post_meta($post->ID, 'office_page_slider', true); //get meta
if ($page_slider == 'enable') {
	get_template_part( 'includes/page-slides'); //show slider
}
?>

    <div id="page-heading">
        <h1><?php the_title(); ?></h1>		
        <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?>
    </div>
    <!-- /page-heading -->

	<div class="gallery-wrap clearfix">
    
		<?php
        //get attachement count
        $get_attachments = get_children( array( 'post_parent' => $post->ID ) );
		
		//get ID of featured image
        $featured_image_id = get_post_thumbnail_id( $post->ID );
		
        //attachement loop
        $args = array(
            'orderby' => 'menu_order',
            'post_type' => 'attachment',
            'post_parent' => get_the_ID(),
            'post_mime_type' => 'image',
            'post_status' => null,
            'posts_per_page' => -1,
			'exclude' => $featured_image_id
        );
        $attachments = get_posts($args);
        ?>
        
        <?php if ($attachments) { ?>

			<?php
            foreach ($attachments as $attachment) :
            //get images
			$cropped_thumb = wp_get_attachment_image_src($attachment->ID, 'gallery-thumb');
			$full_image = wp_get_attachment_image_src($attachment->ID, 'full-size');
            ?>
            
            <div class="gallery-photo">
                <a href="<?php echo $full_image[0]; ?>" class="single-gallery-thumb" rel="prettyPhoto[gallery]" title="<?php echo apply_filters('the_title', $attachment->post_title); ?>"><img src="<?php echo $cropped_thumb[0]; ?>" alt="<?php echo apply_filters('the_title', $attachment->post_title); ?>" class="attachment-post-thumbnail" height="" /></a>
            </div>
            <!-- /gallery-photo -->
    
    	<?php endforeach; ?>
    
    <?php } ?>
    
    </div>
    <!-- /gallery-wrap -->

<?php the_content(); ?>

<?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>