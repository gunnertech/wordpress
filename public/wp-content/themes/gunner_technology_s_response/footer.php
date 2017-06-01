        <!-- POST CONTENT SIDEBARS -->
        <?php do_action("hbgs_post_content_widgets") ?>
        <!--/ POST CONTENT SIDEBARS -->
      </div>
      <footer>
        <?php do_action("hbgs_footer_widgets") ?>
      </footer>
      <script>
        Modernizr.load({test: Modernizr.hbgs_loaded, complete:function(){ MBP.scaleFix(); }});
      </script>
      
        <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
             chromium.org/developers/how-tos/chrome-frame-getting-started -->
        <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
        <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
        <![endif]-->
        
      <?php wp_footer(); ?>
    </div>
  </body>
</html>