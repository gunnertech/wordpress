<?php global $thumbnail_list_position; $hbgs_meta = get_post_meta(get_the_ID(),'_hbgs_meta',TRUE); $thumbnail = hbgs_get_post_thumbnail(get_the_ID(),'list'); ?>

<article <?php !!$thumbnail ? post_class("focus has-thumbnail") : post_class("focus") ?>>
  <?php if($thumbnail && !isset($thumbnail_list_position)): ?>
    <figure class="featured-image">
      <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
        <?php echo $thumbnail ?>
      </a>
    </figure>
  <?php endif; ?>
  
  <?php if(!is_page()): ?>
    <hgroup>
      <h2><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
    </hgroup>
    <?php if($thumbnail && isset($thumbnail_list_position) && $thumbnail_list_position == 'below_headline'): ?>
      <figure class="featured-image">
        <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
          <?php echo $thumbnail ?>
        </a>
      </figure>
    <?php endif; ?>
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
    <?php if(has_excerpt()): ?>
      <?php the_excerpt() ?>
    <?php else: ?>
      <?php the_content() ?>
    <?php endif; ?>
  </div>
  
</article>