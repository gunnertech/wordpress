<?php $hbgs_meta = hbgs_get_meta(true); ?>

<article <?php post_class("hyphenate focus") ?>>
  <?php if($thumbnail = hbgs_get_post_thumbnail(get_the_ID(),'detailed')): $image = get_post(get_post_thumbnail_id(get_the_ID())); $image_meta = hbgs_get_post_thumbnail_meta(get_the_ID(),'detailed'); ?>
    <figure class="featured-image" style="width: <?php echo $image_meta[1] ?>px;">
      <?php echo $thumbnail ?>
      <figcaption>
        <?php echo $image->post_content ?>
      </figcaption> 
    </figure>
  <?php endif; ?>
  
  <?php if(!is_page()): ?>
    <hgroup>
      <h1><?php the_title() ?></h1>
      <h3 class="the-category">
        <?php echo hbgs_get_the_category_name() ?>
      </h3>
    </hgroup>
    <div class="meta">
      <?php hbgs_post_meta() ?>
      <?php if ( count( get_the_category() ) ) : ?>
  		  <?php echo hbgs_get_the_category_list( ', ' ) ?> 
  		  <span class="separator">|</span>  
  		<?php endif; ?>
  		<?php comments_popup_link( __( 'Leave a comment', 'hbgs' ), __( '1 Comment', 'hbgs' ), __( '% Comments', 'hbgs' ), 'comments-link', '' ) ?>
    </div>
  <?php endif; ?>

  <div class="body">
    <?php the_content("Read More &#187;"); ?>
  </div>
  <!-- POST CONTENT BODY SIDEBARS -->
  <?php do_action("hbgs_post_content_body_widgets") ?>
  <!/-- POST CONTENT BODY SIDEBARS -->
  
  
  <div style="clear:both;"></div>

  <?php if(!is_page()): ?>
    <?php if ( get_the_author_meta( 'description' ) ) :  ?>
      <div id="entry-author-info">
    	  <div id="author-description">
    		  <h3><?php printf( esc_attr__( 'About %s', 'hbgs' ), get_the_author() ); ?></h3>
    		  <div id="author-avatar">
      		  <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
      	  </div><!-- #author-avatar -->
    		  <div class="body">
            <?php echo wpautop(get_the_author_meta( 'description' )); ?>
          </div>
    		  <p id="author-link">
    			  <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
    				  <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'hbgs' ), get_the_author() ); ?>
    			  </a>
    		  </p><!-- #author-link	-->
    	  </div><!-- #author-description -->
      </div><!-- #entry-author-info -->
    <?php endif; ?>
    
    <nav class="next-previous clearfix">
      <div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'hbgs' ) . '</span> %title' ); ?></div>
  		<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'hbgs' ) . '</span>' ); ?></div>
  	</nav>
  <?php endif; ?>

  <?php if(!is_page() && (!isset($hbgs_meta['allow_comments_on_pages']) || !!$hbgs_meta['allow_comments_on_pages'])): ?>
    <?php comments_template( '', true ); ?>
  <?php endif; ?>
</article>