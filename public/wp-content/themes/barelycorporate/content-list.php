<?php
/**
 * The default template for displaying content in blogroll.
 */
global $more; $more = 0;
?>
<div class="article-wrap">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php themeblvd_the_title(); ?></h1>
			<?php if( 'show' == themeblvd_get_option( 'post_list_meta', null, 'show' ) ) : ?>
				<?php themeblvd_blog_meta(); ?>
			<?php endif; ?>
		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php themeblvd_the_post_thumbnail( themeblvd_get_att( 'location' ), themeblvd_get_att( 'size' ) ); ?>
			<?php themeblvd_blog_content( themeblvd_get_att( 'content' ) ); ?>
			<div class="clear"></div>
			<?php if( 'show' == themeblvd_get_option( 'post_list_tags', null, 'show' ) ) : ?>
				<?php themeblvd_blog_tags(); ?>
			<?php endif; ?>
		</div><!-- .entry-content -->
	</article><!-- #post-<?php the_ID(); ?> -->
</div><!-- .article-wrap (end) -->
