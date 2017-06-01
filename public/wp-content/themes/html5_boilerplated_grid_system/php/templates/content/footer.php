<?php global $wp_query; ?>
<?php if ( $wp_query->max_num_pages > 1 ): ?>
	<nav id="nav-below" class="next-previous clearfix">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'hbgs' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'hbgs' ) ); ?></div>
	</nav>
<?php endif; ?>
