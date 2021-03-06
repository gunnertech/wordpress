<?php
/*
Template Name: Blog list
*/
?>
<?php get_header(); ?>
<?php
global $pagelayout_type;
$pagelayout_type="two-column";
?>
<h1 class="entry-title"><?php the_title(); ?></h1>
<div class="contents-wrap float-left two-column">
	<?php
	query_posts('paged='.$paged.'&posts_per_page=');
	?>
	<div class="entry-content-wrapper">
	<?php get_template_part( 'loop', 'blog' ); ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>