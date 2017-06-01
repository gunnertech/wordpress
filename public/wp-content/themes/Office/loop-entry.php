<?php
global $data;
while (have_posts()) : the_post(); ?>  

<?php
/*-----------------------------------------------------------------------------------*/
// Simple Entries (pages, faqs, testimonials, staff, services)
/*-----------------------------------------------------------------------------------*/
if(get_post_type() == 'page' || get_post_type() == 'faqs' || get_post_type() == 'services' || get_post_type() == 'testimonials') {
?>

    <div class="loop-entry clearfix">
    	<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    	<?php echo excerpt('50'); ?>
    </div><!-- END entry -->

<?php
}
/*-----------------------------------------------------------------------------------*/
// Portfolio Entry 
/*-----------------------------------------------------------------------------------*/
elseif( get_post_type() == 'portfolio' || get_post_type() == 'staff') { ?>

    <div class="loop-entry clearfix">
    
    	<?php
        $portfolio_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'grid-thumb');
		if($portfolio_thumb) { ?>
    	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="search-portfolio-thumb"><img src="<?php echo $portfolio_thumb[0]; ?>" height="<?php echo $portfolio_thumb[2]; ?>" width="<?php echo $portfolio_thumb[1]; ?>" alt="<?php echo the_title(); ?>" /></a>
        <?php } ?>
        
    	<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    	<?php echo excerpt('50'); ?>
        
    </div>
    <!-- /entry -->
<?php
}
/*-----------------------------------------------------------------------------------*/
// Posts Entry 
/*-----------------------------------------------------------------------------------*/
else { ?>
<div class="loop-entry clearfix">

	<?php
	$blog_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'post-image');
    if(has_post_thumbnail() ) {  ?>
        <div class="loop-entry-thumbnail">
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo $blog_thumb[0]; ?>" alt="<?php echo the_title(); ?>" /></a>
        </div>
        <!-- /loop-entry-thumbnail -->
    <?php } ?>
  
	<div class="loop-entry-right">
    	<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
        <?php
        if($data['enable_full_blog'] == 'enable') {
    		the_content();
		}
		else {
			the_excerpt();
		}
		?>
    </div>
    <!-- /loop-entry-right -->
    
	<div class="loop-entry-left">
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
    </div>
    <!-- /loop-entry-left -->
     
</div><!-- /entry -->

<?php } ?>


<?php endwhile; ?>