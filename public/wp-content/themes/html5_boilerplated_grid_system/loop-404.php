<?php global $post; ?>
<div class="clearfix content <?php echo is_singular() ? 'single' : 'multiple' ?>" style="clear:both;" id="post-<?php the_ID(); ?>">
  
  <?php get_template_part( 'php/templates/content/header', (isset($post->post_type) ? $post->post_type : '') ); ?>
  
  <!-- CONTENT LEFT SIDEBARS -->
  <?php do_action("hbgs_content_left_widgets") ?>
  <!/-- CONTENT LEFT SIDEBARS -->
  
  <div <?php hbgs_content_column_classes() ?> >
    <!-- CONTENT ABOVE SIDEBARS -->
    <?php do_action("hbgs_content_above_widgets") ?>
    <!/-- CONTENT ABOVE SIDEBARS -->
    <article class="postformat-standard hentry hyphenate focus">
      <div class="body">
        <p>Sorry, but the page you were trying to view does not exist.</p>
        <p>It looks like this was the result of either:</p>
        <ul>
          <li>a mistyped address</li>
          <li>an out-of-date link</li>
        </ul>
        <script>
        var GOOG_FIXURL_LANG = (navigator.language || '').slice(0,2),
          GOOG_FIXURL_SITE = location.host;
        </script>
        <script src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>
      </div>
    </article>
  </div>
  
  <!-- CONTENT RIGHT SIDEBARS -->
  <?php do_action("hbgs_content_right_widgets") ?>
  <!/-- CONTENT RIGHT SIDEBARS -->
  
  <?php get_template_part( 'php/templates/content/footer', $post->post_type ); ?>
  
</div>