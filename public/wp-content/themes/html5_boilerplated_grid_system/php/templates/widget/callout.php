<?php 
$hbgs_meta = get_post_meta(get_the_ID(),'_hbgs_meta',TRUE); 
$url = $hbgs_meta['call_to_action_url'] ? $hbgs_meta['call_to_action_url'] : $hbgs_meta['permalink'] ? $hbgs_meta['permalink'] : get_permalink();
?>
<li class="clearfix callout  item-<?php echo $count ?> <?php if($count == 1){ echo 'alpha'; } ?> <?php if($count == $instance['posts_per_page']){ echo 'omega'; } ?> grid_<?php echo $template_parameters['content_columns'] ?> suffix_<?php echo $template_parameters['content_suffix'] ?> prefix_<?php echo $template_parameters['content_prefix'] ?>" id="post-<?php the_ID(); ?>">
  <div class="description">
    <?php if(!isset($template_parameters['figure_placement']) || $template_parameters['figure_placement'] == 'top'): ?>
      <figure class="<?php echo ($template_parameters['figure_alignment'] ? $template_parameters['figure_alignment'] : "center"); ?>">
        <a href="<?php echo $url ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
          <?php if($hbgs_meta['icon_url']): ?>
            <img alt="<?php echo esc_attr(get_the_title()) ?>" src="<?php echo $hbgs_meta['icon_url'] ?>" />
          <?php else: ?>
            <?php the_post_thumbnail($instance['image_size']) ?>
          <?php endif; ?>
        </a>
      </figure>
    <?php endif; ?>
    <hgroup>
      <h2><a href="<?php echo $url ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></h2>
    </hgroup>
    <?php if($template_parameters['figure_placement'] == 'middle'): ?>
      <a href="<?php echo $url ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
        <?php if($hbgs_meta['icon_url']): ?>
          <img alt="<?php echo esc_attr(get_the_title()) ?>" src="<?php echo $hbgs_meta['icon_url'] ?>" />
        <?php else: ?>
          <?php the_post_thumbnail($instance['image_size']) ?>
        <?php endif; ?>
      </a>
    <?php endif; ?>
    <div class="body hyphenate">
      <?php if($hbgs_meta['call_to_action']): ?>
        <a class="call-to-action" href="<?php echo $hbgs_meta['call_to_action_url'] ?>">
          <?php echo $hbgs_meta['call_to_action'] ?>
        </a>
      <?php endif; ?>
      <?php the_excerpt() ?>
    </div>
    <?php if($template_parameters['figure_placement'] == 'bottom'): ?>
      <a href="<?php echo $url ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
        <?php if($hbgs_meta['icon_url']): ?>
          <img alt="<?php echo esc_attr(get_the_title()) ?>" src="<?php echo $hbgs_meta['icon_url'] ?>" />
        <?php else: ?>
          <?php the_post_thumbnail($instance['image_size']) ?>
        <?php endif; ?>
      </a>
    <?php endif; ?>
  </div>
  <a href="<?php the_permalink() ?>" class="button">Read More</a>
  <div class="more hyphenate">
    <?php the_content() ?>
    <div class="button-wrapper" style="padding-bottom:60px;">
      <a href="#contact" class="submit_button close">Contact Us</a>
    </div>
  </div>
</li>