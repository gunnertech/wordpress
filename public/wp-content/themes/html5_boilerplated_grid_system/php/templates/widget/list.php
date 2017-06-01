<?php


$template_parameters = array_merge(
  array(
  'include_thumbnail' => 'bottom'
  ),
  $template_parameters
);
?>
<li id="post-<?php the_ID(); ?>">
   <?php if($template_parameters['include_thumbnail'] == 'top' && has_post_thumbnail() && $instance['image_size'] != "0"): ?>
    <a class="featured-image" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>"> 
      <?php echo get_the_post_thumbnail(get_the_ID(), $instance['image_size'] ) ?>
    </a>
  <?php endif; ?>
  <hgroup>
    <h4><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></h4>
  </hgroup>
  <?php if(isset($template_parameters['include_time'])): ?>
    <time><?php the_time(get_option('date_format')); ?> <span class="time"><?php the_time() ?></span></time>
  <?php endif; ?>
  <?php if(($template_parameters['include_thumbnail'] == 'bottom' || $template_parameters['include_thumbnail'] == '1') && has_post_thumbnail() && $instance['image_size'] != "0"): ?>
    <a class="featured-image" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>"> 
      <?php echo get_the_post_thumbnail(get_the_ID(), $instance['image_size'] ) ?>
    </a>
  <?php endif; ?>
  <?php if(get_post_meta(get_the_ID(), 'LinkFormatSource', true)): ?>
    <cite class="source"><?php echo get_post_meta(get_the_ID(), 'LinkFormatSource', true) ?></cite>
  <?php endif; ?>
  <div class="excerpt">
    <?php if($instance['show_full_content'] == 1): ?>
      <?php the_content() ?>
    <?php elseif(isset($template_parameters['excerpt_length'])): 
      remove_filter('excerpt_more', 'hbgs_excerpt_more');
      add_filter('excerpt_more', function(){
        return "";
      });
    ?>
      <p>
        <?php echo hbgs_shortened_excerpt(get_the_excerpt(),$template_parameters['excerpt_length']) ?>
      </p>
    <?php else: ?>
      <?php the_excerpt() ?>
    <?php endif; ?>
    <?php if(isset($template_parameters['read_more_text'])): ?>
      <a class="read-more-text" href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php echo $template_parameters['read_more_text']; ?></a>
    <?php endif; ?>
  </div>
  <?php if(isset($template_parameters['more_text'])): ?>
    <a class="read-more" href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php echo $template_parameters['more_text']; ?></a>
  <?php endif; ?>
</li>