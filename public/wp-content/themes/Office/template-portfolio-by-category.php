<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>

<?php
/*
Template Name: Portfolio by Category
*/
?>
<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div id="page-heading">
	<h1><?php the_title(); ?></h1>
    <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?>
</div>
<!-- /page-header -->
 
 
<div id="portfolio-by-category-wrap" class="clearfix">

    <?php
	$content = $post->post_content;
	if(!empty($content)) { ?>
		<div id="portfolio-bycat-description" class="clearfix">
        	<?php the_content(); ?>
        </div>
        <!-- portfolio-description -->
	<?php } ?>

	<?php
		$terms = get_terms('portfolio_cats','orderby=custom_sort');
		foreach($terms as $term) {
	?>
	
    <div class="heading">
		<h2><a href="<?php echo get_term_link($term->slug, 'portfolio_cats'); ?>" class="all-port-cat-items"><span><?php echo $term->name; ?></span></a></h2>
    </div>

	<div class="portfolio-category clearfix">
	
		<?php
		$tax_query = array(
		array(
			'taxonomy' => 'portfolio_cats',
			'terms' => $term->slug,
			'field' => 'slug'
			)
		);
		$term_post_args = array(
			'post_type' => 'portfolio',
			'numberposts' => '4',
			'tax_query' => $tax_query
		);
		$term_posts = get_posts($term_post_args);
		
		//start loop
		foreach ($term_posts as $post) : setup_postdata($post);
		
		//get images
		$featured_image = get_the_post_thumbnail($post->ID, 'grid-thumb');
		?>
		  <?php if(!empty($featured_image)) { ?>
          <div class="portfolio-item">
              <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                  <?php echo $featured_image; ?>
                  <h3><?php the_title(); ?></h3>
              </a>
          </div>
          <!-- /portfolio-item -->
          <?php } ?>
 
	<?php endforeach; ?>
	</div>
	<!-- /portfolio-category -->

<?php } wp_reset_query(); ?>

</div>
<!-- /portfolio-by-category-wrap -->

<?php wp_reset_query(); ?>

<?php endwhile; ?>
<?php endif; ?>
 
<?php get_footer(); ?>