<?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>
<?php if ($query->have_posts()): ?>
  <ul class="simple-list">
    <?php while ($query->have_posts()): $query->the_post(); $more = ($instance['show_full_content'] ? 1 : 0); $count++; ?>
      <li>
        <a class="<?php echo GtQuery::categories_string(get_the_ID()); ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
      </li>
    <?php endwhile; ?>
  </ul>
<?php endif; ?>
<?php if($instance['more_slug'] && $instance['more_url']): ?>
  <a class="<?php echo apply_filters( 'simple_list_more_classes', "btn more" ) ?>" href="<?php echo $instance['more_url'] ?>"><?php echo $instance['more_slug'] ?></a>
<?php endif; ?>