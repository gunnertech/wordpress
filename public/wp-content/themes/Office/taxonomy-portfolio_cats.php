<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>

<?php get_header(); ?>
<?php if (have_posts()) : ?>

<div id="page-heading">
	<h1>
	<?php
		$term =	$wp_query->queried_object;
		echo $term->name;
	?>
	</h1>
    <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?>   
</div>
<!-- /page-heading -->

<?php if(category_description()) { ?>
<div id="portfolio-description">
	 <?php echo category_description( ); ?>
</div>
<!-- /portfolio-description -->
<?php } ?>
    
<div class="post full-width clearfix">
    
    <div id="portfolio-wrap" class="clearfix">
    
        <?php
        while (have_posts()) : the_post();
        //get portfolio thumbnail
        $thumbail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'grid-thumb');
        ?>
        
        <?php if ( has_post_thumbnail() ) {  ?>
        <div class="portfolio-item <?php if($count == '4') { echo 'no-margin'; } ?>">
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
            	<img src="<?php echo $thumbail[0]; ?>" height="<?php echo $thumbail[2]; ?>" width="<?php echo $thumbail[1]; ?>" alt="<?php echo the_title(); ?>" />
            	<h3><?php the_title(); ?></h3>
            </a>
        </div>
        <!-- /portfolio-item -->
        <?php } ?>
        
        <?php endwhile; ?>
          
    </div>
    <!-- /portfolio-wrap -->
    
	<?php pagination(); wp_reset_query(); ?>

</div>
<!-- /post --->


<?php endif; ?>
<?php get_footer(); ?>