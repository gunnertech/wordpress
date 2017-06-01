<?php $hbgs_meta = get_post_meta(get_the_ID(),'_hbgs_meta',TRUE); ?>
<li class="item-<?php echo $count ?>">
  <figure>
    <?php the_post_thumbnail( $instance['image_size']); ?>
    <figcaption>
      <h1>
        <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
          <?php the_title() ?>: 
          <?php if($hbgs_meta['subtitle']): ?>
            <?php echo $hbgs_meta['subtitle'] ?>
          <?php else: ?>
            <?php echo get_the_excerpt() ?>
          <?php endif; ?>
        </a>
      </h1>
    </figcaption>
  </figure>
</li>