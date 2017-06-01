<?php global $post; ?>
<div class="content <?php echo is_singular() ? 'single' : 'multiple' ?>" id="post-<?php the_ID(); ?>">
  
  <?php get_template_part( 'php/templates/content/header', (isset($post->post_type) ? $post->post_type : '') ); ?>
  
  <!-- CONTENT LEFT SIDEBARS -->
  <?php do_action("hbgs_content_left_widgets") ?>
  <!/-- CONTENT LEFT SIDEBARS -->
  
  <div class="article-list">
    <!-- CONTENT ABOVE SIDEBARS -->
    <?php do_action("hbgs_content_above_widgets") ?>
    <!/-- CONTENT ABOVE SIDEBARS -->
    <?php while ( have_posts() ) : the_post(); $hbgs_meta = hbgs_get_meta(true); ?>
      <?php if(array_key_exists('post_template',$hbgs_meta) && !!$hbgs_meta['post_template'] && $hbgs_meta['post_template'] != 'default.php'): ?>
        <?php get_template_part( 'php/templates/content/custom/'.str_replace(".php",$template,"") ) ?>
      <?php else: ?>
        <?php get_template_part( 'php/templates/content/'.(is_singular() ? 'show' : 'index'), (isset($post->post_type) ? $post->post_type : '') ) ?>
      <?php endif; ?>
    <?php endwhile; ?>
  </div>
  
  <!-- CONTENT RIGHT SIDEBARS -->
  <?php do_action("hbgs_content_right_widgets") ?>
  <!/-- CONTENT RIGHT SIDEBARS -->
  
  <?php get_template_part( 'php/templates/content/footer', $post->post_type ); ?>
  
</div>