<article <?php post_class("hyphenate") ?>>

  <hgroup>
    <h1><?php the_title() ?></h1>
    <h3 class="the-category"><?php echo hbgs_get_the_category_name() ?></h3>
  </hgroup>
  
  <?php echo wp_get_attachment_image( get_the_ID(), 'large' ) ?>
  <?php if ( wp_attachment_is_image() ): $metadata = wp_get_attachment_metadata() ?>
    <h4>
      <?php printf( '<a href="%1$s" target="_blank" title="%2$s">Download Full-Sized Image</a>',
        wp_get_attachment_url(),
        esc_attr( __('Full size is '.$metadata['width'].' &times; '.$metadata['height'].' pixels', 'twentyten') )
      ) ?>
    </h4>
  <?php endif; ?>

  <div class="body">
    <?php the_excerpt() ?>
  </div>
  
  <!-- POST CONTENT BODY SIDEBARS -->
  <?php do_action("hbgs_post_content_body_widgets") ?>
  <!/-- POST CONTENT BODY SIDEBARS -->
  
  <div style="clear:both;"></div>
  
  <?php comments_template( '', true ); ?>
  
</article>