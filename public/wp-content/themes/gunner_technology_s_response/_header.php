<header class="main">
  <?php if(of_get_option("header_layout",'contain') == 'full-content'): ?><div class="container"><?php endif; ?>
    <hgroup>
      <?php if(is_home() || is_front_page()): ?>
        <h1><a href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h1>
        <h4><a href="<?php echo home_url(); ?>/"><?php bloginfo('description'); ?></a></h4>
      <?php else: ?>
        <h4><a href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h4>
        <h6><a href="<?php echo home_url(); ?>/"><?php bloginfo('description'); ?></a></h6>
      <?php endif; ?>
    </hgroup>
    <?php do_action("hbgs_header_widgets") ?>
    <?php get_template_part( '_more_header' ) ?>
  <?php if(of_get_option("header_layout",'contain') == 'full-content'): ?></div><?php endif; ?>
</header>