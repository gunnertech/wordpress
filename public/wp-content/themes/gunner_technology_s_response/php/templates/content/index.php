<?php 
  $use_excerpt = get_option('rss_use_excerpt');
?>

<article <?php post_class("focus") ?>>
  <?php do_action("gt_featured_image") ?>
  
  <?php if(!is_page()): ?>
    <hgroup>
      <h2><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
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
    <?php if($use_excerpt): ?>
      <?php the_excerpt() ?>
    <?php else: ?>
      <?php the_content() ?>
    <?php endif; ?>
  </div>
  
</article>