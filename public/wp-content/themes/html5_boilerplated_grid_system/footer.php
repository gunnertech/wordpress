        <!-- POST CONTENT SIDEBARS -->
        <?php do_action("hbgs_post_content_widgets") ?>
        <!--/ POST CONTENT SIDEBARS -->
      </div>
      <footer class="clearfix grid_24 fullscreen footer">
        <?php do_action("hbgs_footer_widgets") ?>
      </footer>
      <!--[if lt IE 7 ]>
        <script src="<?php bloginfo('template_url') ?>/js/dd_belatedpng.js?v=1"></script>
      <![endif]-->
      <?php wp_footer(); ?>
    </div>
  </body>
</html>