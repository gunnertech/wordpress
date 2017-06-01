<?php global $wp_query; ?>
<?php if ( $wp_query->max_num_pages > 1 ): ?>
	<nav id="nav-below" class="next-previous">
		<div class="nav-previous"><?php next_posts_link( __( 'Older posts', 'hbgs' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts', 'hbgs' ) ); ?></div>
	</nav>
<?php endif; ?>
