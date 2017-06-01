<?php
  $thumbnail = $instance['image_size'] == '0' ? null : get_the_post_thumbnail(get_the_ID(),$instance['image_size']);
?>
<article class="<?php echo get_post_type() ?>">
  <?php if($thumbnail): $image = get_post(get_post_thumbnail_id(get_the_ID())); $image_meta = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),$instance['image_size']); ?>
    <figure class="featured-image" style="width: <?php echo $image_meta[1] ?>px;">
      <?php echo $thumbnail ?>
      <figcaption>
        <?php echo $image->post_content ?>
      </figcaption> 
    </figure>
  <?php endif; ?>
  
  <hgroup>
    <h2><a href="<?php the_permalink() ?>"><?php echo do_shortcode(get_the_title()) ?></a></h2>
  </hgroup>
  
  <div class="meta">
    <?php hbgs_post_meta() ?>
    <?php if ( count( get_the_category() ) ) : ?>
      <?php echo hbgs_get_the_category_list( ', ' ) ?> 
      <span class="separator">|</span>  
    <?php endif; ?>
    <?php comments_popup_link( __( 'Leave a comment', 'hbgs' ), __( '1 Comment', 'hbgs' ), __( '% Comments', 'hbgs' ), 'comments-link', '' ) ?>
  </div>
  
  <div class="body">
    <?php if(isset($template_parameters['full_content_with_excerpt']) && $template_parameters['full_content_with_excerpt']): ?>
      <?php the_content() ?>
    <?php elseif(isset($template_parameters['excerpt_length'])): ?>
      <p>
        <?php echo hbgs_shortened_excerpt(get_the_excerpt(),$template_parameters['excerpt_length']) ?>
      </p>
    <?php elseif(has_excerpt() || (isset($template_parameters['force_excerpt']) && $template_parameters['force_excerpt'])): ?>
      <?php the_excerpt() ?>
    <?php else: ?>
      <?php the_content() ?>
    <?php endif; ?>
  
    <?php if((isset($instance['read_more_text']) && $instance['read_more_text'])): ?>
      <a class="read-more-text" href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php echo $template_parameters['read_more_text']; ?></a>
    <?php endif; ?>
  </div>
  
  <div style="clear:both;"></div>
  
</article>