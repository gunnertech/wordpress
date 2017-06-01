<?php $post_count = 0; ?>
<div class="carousel-custom">
  <?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>
  <?php if ($query->have_posts()): ?>
    <?php while ($query->have_posts()): $query->the_post(); ?>
      <?php if($post_count == 0): $post_count++; ?>
        <div class="main-item">
          <h3><?php the_title() ?></h3>
          <?php if(has_post_thumbnail(get_the_ID())): $attachment = wp_get_attachment_image( get_post_thumbnail_id(get_the_ID()), 'large'); ?>
            <a title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>">
              <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(), $instance['image_size'] )) ?>
            </a>
          <?php endif; ?>
          <div class="content-holder"><?php $instance['show_full_content'] ? the_content() : the_excerpt() ?></div>
        </div>
      <?php endif; ?>
    <?php endwhile; ?>
  <?php endif; ?>
  
  <?php if (isset($instance['subheader']) && $instance['subheader']): ?>
    <h4><?php echo $instance['subheader']; ?></h4>
  <?php endif ?>
  
  <?php if ($query->have_posts()): ?>
    <ul class="items">
      <?php while ($query->have_posts()): $query->the_post(); ?>
        <li>
          <?php if(has_post_thumbnail(get_the_ID())): $attachment = wp_get_attachment_image( get_post_thumbnail_id(get_the_ID()), 'large'); ?>
            <a data-content="<?php echo esc_attr(get_the_excerpt()) ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>">
              <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(), $instance['image_size'] )) ?>
            </a>
          <?php endif; ?>
          <h5><?php the_title() ?></h5>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>
  
  <?php if($instance['more_slug'] && $instance['more_url']): ?>
    <a class="btn more" href="<?php echo $instance['more_url'] ?>"><?php echo $instance['more_slug'] ?></a>
  <?php endif; ?>
</div>