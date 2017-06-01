<?php
/**
 * The template used for displaying single post content in single.php
 */
?>
<div class="article-wrap single-post">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php themeblvd_the_title(); ?></h1>
			<?php if( themeblvd_get_att( 'show_meta' ) ) : ?>
				<div class="meta-wrapper">
					<?php themeblvd_blog_meta(); ?>
				</div><!-- .meta-wrapper (end) -->
			<?php endif; ?>
		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php themeblvd_the_post_thumbnail( 'single', themeblvd_get_att( 'size' ) ); ?>
			<?php the_content(); ?>
			<div class="clear"></div>
			<?php themeblvd_blog_tags(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . themeblvd_get_local('pages').': ', 'after' => '</div>' ) ); ?>
			<?php edit_post_link( themeblvd_get_local( 'edit_post' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-<?php the_ID(); ?> -->
</div><!-- .article-wrap (end) -->