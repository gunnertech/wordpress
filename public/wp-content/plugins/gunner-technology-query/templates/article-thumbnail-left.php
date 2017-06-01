<?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>
<?php if ($query->have_posts()): ?>
  <div class="articles">
    <?php while ($query->have_posts()): $query->the_post(); $more = ($instance['show_full_content'] ? 1 : 0); $count++; ?>
      <article class="<?php echo has_post_thumbnail(get_the_ID()) ? 'has-featured-image' : '' ?>">
      
        <figure>
          <?php if(has_post_thumbnail(get_the_ID())): $attachment = wp_get_attachment_image( get_post_thumbnail_id(get_the_ID()), 'large'); ?>        
            <a title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>">
              <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(), $instance['image_size'] )) ?>
            </a>
            <figcaption>
              <?php if($attachment && isset($attachment->post_title)): ?>
                <?php echo apply_filters( 'the_title', $attachment->post_title ) ?>
              <?php endif; ?>
            </figcaption>
          <?php endif; ?>
        </figure>
        
        <div>
          <h3><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></h3>
          <?php $instance['show_full_content'] ? the_content() : the_excerpt() ?>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
  
  <?php if(!empty($instance['more_slug']) && !empty($instance['more_url'])): ?>
    <a class="<?php echo empty($instance['use_text_btn']) ? 'btn' : '' ?> more" href="<?php echo $instance['more_url'] ?>"><?php echo $instance['more_slug'] ?></a>
  <?php endif; ?>
<?php endif; ?>
