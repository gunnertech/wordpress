<?php 
  if(has_filter('the_content', 'shrsb_position_menu')) {
    remove_filter('the_content', 'shrsb_position_menu');
    add_filter('the_content', 'shrsb_position_menu',11);
  }
  add_filter('the_content','hbgs_image_album_the_content');
?>

<article <?php if(is_singular()){ post_class("hyphenate"); } ?>>
  <?php if(!is_singular() && $thumbnail = get_the_post_thumbnail(get_the_ID())): ?>
    <figure class="featured-image">
      <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
        <?php echo $thumbnail ?>
      </a>
    </figure>
  <?php endif; ?>
  
  <hgroup>
    <?php if(is_singular()): ?>
      <h1><?php echo do_shortcode(get_the_title()) ?></h1>
    <?php else: ?>
      <h2><a href="<?php the_permalink() ?>"><?php echo do_shortcode(get_the_title()) ?></a></h2>
    <?php endif; ?>
  </hgroup>
  
  <div class="meta">
    <?php hbgs_post_meta() ?>
    <?php if ( count( get_the_category() ) ) : ?>
		  <?php echo hbgs_get_the_category_list( ', ' ) ?> 
		  <span class="separator">&nbsp;|&nbsp;</span>  
		<?php endif; ?>
		<?php comments_popup_link( __( 'Leave a comment', 'hbgs' ), __( '1 Comment', 'hbgs' ), __( '% Comments', 'hbgs' ) ) ?>
  </div>

  <?php if(!is_singular() && has_excerpt()): ?>
    <?php the_excerpt() ?>
  <?php else: ?>
    <?php the_content("Read More &#187;"); ?>
  <?php endif; ?>
  
  <div style="clear:both;"></div>
  
  <?php if(is_single()): ?>
    <?php if ( get_the_author_meta( 'description' ) ) :  ?>
      <div id="entry-author-info">
    	  <div id="author-description">
    		  <h3><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author() ); ?></h3>
    		  <div id="author-avatar">
      		  <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
      	  </div><!-- #author-avatar -->
    		  <p><?php the_author_meta( 'description' ); ?></p>
    		  <p id="author-link">
    			  <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
    				  <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author() ); ?>
    			  </a>
    		  </p><!-- #author-link	-->
    	  </div><!-- #author-description -->
      </div><!-- #entry-author-info -->
    <?php endif; ?>
  
  	<nav class="next-previous clearfix">
      <div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
  		<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
  	</nav>
  <?php endif; ?>
  <?php if(!is_page()): ?>
    <?php comments_template( '', true ); ?>
  <?php endif; ?>
</article>