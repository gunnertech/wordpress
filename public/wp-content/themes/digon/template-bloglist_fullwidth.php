<?php
/*
Template Name: Fullwidth Blog list
*/
?>
<?php get_header(); ?>
<?php 
global $pagelayout_type;
$pagelayout_type="fullwidth";
?>
<h1 class="entry-title"><?php the_title(); ?></h1>
<div class="fullpage-contents-wrap">
	<?php
	query_posts('paged='.$paged.'&posts_per_page=');
	?>
	<div class="entry-content-wrapper">
	<?php get_template_part( 'loop', 'blog' ); ?>
	</div>
</div>
<?php get_footer(); ?>