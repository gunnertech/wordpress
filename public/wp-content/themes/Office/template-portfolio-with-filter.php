<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 * Template Name: Portfolio With Filter
 */
 global $data //get theme options
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
    
        <?php 
		//get portfolio categories
		$cats = get_terms('portfolio_cats');
		//show filter if categories exist
		if($cats[0] !='') { ?>
        
        <!-- Portfolio Filter -->
        <ul id="portfolio-cats" class="filter clearfix">
            <li><a href="#all" rel="all" class="active"><span><?php _e('All', 'office'); ?></span></a></li>
            <?php
            foreach ($cats as $cat ) : ?>
            <li><a href="#<?php echo $cat->slug; ?>" rel="<?php echo $cat->slug; ?>"><span><?php echo $cat->name; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
        <!-- /portfolio-cats -->
        
	<?php } ?>
</div>
<!-- /page-heading -->

<?php
$content = $post->post_content;
if(!empty($content)) { ?>
	<div id="portfolio-description" class="clearfix">
		<?php the_content(); ?>
	</div>
	<!-- portfolio-description -->
<?php } ?>

    
<div class="post full-width clearfix">

    <div id="portfolio-wrap" class="clearfix">
    	<ul class="portfolio-content">
			<?php
            //get post type ==> portfolio
            query_posts(array(
                'post_type'=>'portfolio',
                'posts_per_page' => -1,
                'paged'=>$paged
            ));
            ?>
        
            <?php
			$count=0;
            while (have_posts()) : the_post();
			$count++;
            //get portfolio thumbnail
            $thumbail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'grid-thumb');
            //get terms
            $terms = get_the_terms( get_the_ID(), 'portfolio_cats' );
			$terms_list = get_the_term_list( get_the_ID(), 'portfolio_cats' );
            ?>
            
            <?php if ( has_post_thumbnail() ) {  ?>
            <li data-id="id-<?php echo $count; ?>" data-type="<?php if($terms) { foreach ($terms as $term) { echo $term->slug .' '; } } else { echo 'none'; } ?>" class="portfolio-item">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                	<img src="<?php echo $thumbail[0]; ?>" alt="<?php echo the_title(); ?>" />
                	<h3><?php the_title(); ?></h3>
                </a>
            </li>
            <!-- /portfolio-item -->
            <?php } ?>
            
            <?php endwhile; wp_reset_query(); ?>
		</ul>
    </div>
    <!-- /portfolio-wrap -->

</div>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>