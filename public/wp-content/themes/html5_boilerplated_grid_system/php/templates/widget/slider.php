<?php 
$child_tag = $tag == 'section' ? 'article' : 'li'; 
$hbgs_meta = get_post_meta(get_the_ID(),'_hbgs_meta',TRUE); 
$template_parameters = array_merge(array(
  "content_columns" => "",
  "media_columns" => "",
  "wrapper_columns" => "",
  "content_suffix" => "",
  "content_prefix" => "",
  "hyphenate" => "",
  "use_subtitle" => false,
  "media_position" => ""
),$template_parameters);
?>
<<?php echo $child_tag ?> class="<?php echo $template_parameters['wrapper_columns'] ?> featured clearfix" id="post-<?php the_ID(); ?>">
  <?php if($template_parameters['media_position'] == 'left'): ?>
    <figure class="alpha grid_<?php echo $template_parameters['media_columns'] ?>">
      <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
        <?php echo get_the_post_thumbnail(get_the_ID(), $instance['image_size'] ) ?>
      </a>
    </figure>
  <?php endif; ?>
  <div class="description omega grid_<?php echo $template_parameters['content_columns'] ?> suffix_<?php echo $template_parameters['content_suffix'] ?> prefix_<?php echo $template_parameters['content_prefix'] ?>">
    <hgroup>
      <h2><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
        <?php if($template_parameters['use_subtitle'] && isset($hbgs_meta['subtitle'])): ?>
          <?php echo $hbgs_meta['subtitle'] ?>
        <?php else: ?>
          <?php if ( get_the_title() ) the_title(); else the_ID(); ?>
        <?php endif; ?>
      </a></h2>
    </hgroup>
    <div class="body <?php echo $template_parameters['hyphenate'] ?>">
      <?php if(has_excerpt()): ?>
        <?php echo wpautop(do_shortcode(get_the_excerpt())) ?>
      <?php else: ?>
        <?php the_content("Read More &#187;") ?>
      <?php endif; ?>
    </div>
  </div>
  <?php if($template_parameters['media_position'] != 'left'): ?>
    <figure class="alpha grid_<?php echo $template_parameters['media_columns'] ?>">
      <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
        <?php echo get_the_post_thumbnail(get_the_ID(), $instance['image_size'] ) ?>
      </a>
    </figure>
  <?php endif; ?>
  <div class="clear"></div>
</<?php echo $child_tag ?>>