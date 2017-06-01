<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>
<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div id="page-heading">
	<h1><?php the_title(); ?></h1>	
    <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?>  
</div>
<!-- /post-meta -->

<div class="post clearfix">

    <div class="entry clearfix">
        <?php
		if($data['enable_disable_post_image'] !='disable' && has_post_thumbnail()) {
		$blog_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'post-image');
			?>
        		<div class="post-thumbnail">
        			<img src="<?php echo $blog_thumb[0]; ?>" alt="<?php echo the_title(); ?>" />
                </div>
                <!-- /post-thumbnail -->
        <?php } ?>
        
        
        <?php if($data['enable_disable_single_meta'] !='disable') { ?>
        <div id="post-meta">
            <div class="post-date">
               <?php the_time('j'); ?> <?php the_time('M'); ?>, <?php the_time('Y'); ?>
            </div>
            <!-- /post-date -->
            <div class="post-cat">
               <?php the_category(); ?>
            </div>
            <!-- /post-cat -->
        </div>
    	<!-- /post-meta -->
        <?php } ?>
        
		<?php the_content(); ?>
        
        <div class="clear"></div>
        
        <?php wp_link_pages(' '); ?>
         
        <div class="post-bottom">
        	<?php the_tags('<div class="post-tags"><h4>Tags</h4>','','</div>'); ?>
        </div>
        <!-- /post-bottom -->
        
        
        </div>
        <!-- /entry -->
	
	<?php comments_template(); ?>
   
        
</div>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>
             
<?php get_sidebar(); ?>
<?php get_footer(); ?>