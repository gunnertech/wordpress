<?php
/*
Template Name: Blog list - Standard
*/
?>
<?php get_header(); ?>
<div class="contents-wrap float-left two-column">
	<?php
	$args = array(
	'paged' => $paged,
	'showposts' => '',
	'posts_per_page' => '',
	'tax_query' => array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => array('post-format-quote','post-format-image','post-format-video','post-format-audio','post-format-gallery','post-format-link','post-format-aside'),
				'operator' => 'NOT IN'
				)
			)
	);
	query_posts( $args );
	?>
	<div class="entry-content-wrapper">
	<h1 class="entry-title"><?php the_title(); ?></h1>
	<?php get_template_part( 'loop', 'blog' ); ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>