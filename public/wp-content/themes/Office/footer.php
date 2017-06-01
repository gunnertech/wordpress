<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
global $data
?>

<div class="clear"></div>
</div><!-- /container -->

    <div id="footer">
    
    	<?php if($data['disable_widgetized_footer'] !='disable') { ?>
        <div id="footer-widget-wrap" class="clearfix">
    
            <div id="footer-left">
            <?php dynamic_sidebar('footer-left'); ?>
            </div>
            
            <div id="footer-middle">
             <?php dynamic_sidebar('footer-middle'); ?>
            </div>
            
            <div id="footer-right">
             <?php dynamic_sidebar('footer-right'); ?>
            </div>
        
        </div>
        <!-- /footer-widget-wrap -->
        <?php } ?>
        
        <a href="#toplink" id="toplink"><?php _e('back up', 'office'); ?> &uarr;</a>
    
    </div>
    <!-- /footer -->
    
</div>
<!-- /wrap -->

    <div id="footer-bottom" class="clearfix">
    
        <div id="copyright">
            <?php if(!empty($data['custom_copyright'])) { echo $data['custom_copyright']; } else { ?>
            &copy; <?php _e('Copyright', 'office'); ?> <?php echo date('Y'); ?> <a href="<?php echo home_url(); ?>/" title="<?php bloginfo('name'); ?>" rel="home"><?php bloginfo('name'); ?></a>
            <?php } ?>
        </div>
        <!-- /copyright -->
        
        <div id="footer-menu">
            <?php wp_nav_menu( array(
                'theme_location' => 'footer_menu',
                'sort_column' => 'menu_order',
                'fallback_cb' => ''
            )); ?>
        </div>
        <!-- /footer-menu -->
    
    </div>
    <!-- /footer-bottom -->
    
<?php 
//show tracking code - footer 
echo stripslashes($data['tracking_footer']); 
?>

<!-- WP Footer -->
<?php wp_footer(); ?>
</body>
</html>