<div class="image-grid">
  <?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>
  <?php if ($query->have_posts()): ?>
    <ul class="thumbnails">
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <li>
          <?php if(has_post_thumbnail(get_the_ID())): $attachment = wp_get_attachment_image( get_post_thumbnail_id(get_the_ID()), 'large'); ?>
            <a data-placement="top" data-content="<?php echo esc_js(get_the_excerpt()) ?>" class="thumbnail" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>">
              <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(), $instance['image_size'] )) ?>
            </a>
          <?php endif; ?>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>
  <?php if($instance['more_slug'] && $instance['more_url']): ?>
    <a class="btn more" href="<?php echo $instance['more_url'] ?>"><?php echo $instance['more_slug'] ?></a>
  <?php endif; ?>
</div>