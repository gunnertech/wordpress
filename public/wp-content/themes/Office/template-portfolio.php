<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 * Template Name: Portfolio
 */
global $data
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
			// get portfolio pagination option
			if(!empty($data['portfolio_pagination'])) {
			$portfolio_posts_per_page =  $data['portfolio_pagination'];
			} else {
			$portfolio_posts_per_page = -1;
			}
            //get post type ==> portfolio
            query_posts(array(
                'post_type'=>'portfolio',
                'posts_per_page' => 8,
				'posts_per_page' => $portfolio_posts_per_page,
                'paged'=>$paged
            ));
            ?>
        
            <?php
			$count=0;
            while (have_posts()) : the_post();
			$count++;
            //get portfolio thumbnail
            $thumbail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'grid-thumb');
            ?>
            
            <?php if ( has_post_thumbnail() ) {  ?>
            <li class="portfolio-item">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                	<img src="<?php echo $thumbail[0]; ?>" height="<?php echo $thumbail[2]; ?>" width="<?php echo $thumbail[1]; ?>" alt="<?php echo the_title(); ?>" />
                	<h3><?php the_title(); ?></h3>
				</a>
            </li>
            <!-- /portfolio-item -->
            <?php } ?>
            
            <?php endwhile; ?>
		</ul>
    </div>
    <!-- /portfolio-wrap -->
	<?php pagination(); wp_reset_query(); ?>

</div>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>