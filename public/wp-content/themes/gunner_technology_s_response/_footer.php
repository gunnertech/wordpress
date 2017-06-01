<footer class="main">
  <?php if(of_get_option("footer_layout",'contain') == 'full' || of_get_option("footer_layout",'contain') == 'full-content'): ?>
    <div class="container">
  <?php endif; ?>
  
  <?php do_action("hbgs_footer_widgets") ?>
  
  <?php if(of_get_option("footer_layout",'contain') == 'full' || of_get_option("footer_layout",'contain') == 'full-content'): ?>
    </div>
  <?php endif; ?>
    
</footer>