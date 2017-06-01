<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>


<?php
global $data;
$terms = get_the_term_list( get_the_ID(), 'portfolio_cats' ); //get terms
?>

<div id="single-portfolio" class="full-portfolio clearfix">

		<div id="single-portfolio-left">
            <div id="single-portfolio-meta" class="clearfix">
				<ul>
                    <li><span><?php _e('Date','office'); ?>:</span><?php the_date('M Y'); ?></li>
                    <?php if($terms) { ?><li><span><?php _e('Labeled','office'); ?>:</span><?php echo get_the_term_list( get_the_ID(), 'portfolio_cats', ' ', ' , ', ' ') ?></li><?php } ?>    
                    <?php if(!empty($portfolio_cost)) {?><li><span><?php _e('Cost','office'); ?>:</span><?php echo $portfolio_cost; ?></li><?php } ?>
                    <?php if(!empty($portfolio_client)) {?><li><span><?php _e('Client','office'); ?>:</span><?php echo $portfolio_client; ?></li><?php } ?>
                    <?php if(!empty($portfolio_url)) {?><li><span><?php _e('Website','office'); ?>:</span><a href="<?php echo $portfolio_url; ?>"><?php echo $portfolio_url; ?></a></li><?php } ?>
            	</ul>
            </div>
            <!-- /single-portfolio-meta -->
         
            
        </div>
        <!-- /single--portfolio-left -->
        

        <div id="full-portfolio-content" class="clearfix">
			<?php the_content(); ?>
        </div>
        <!-- /full-portfolio-content -->
  
</div>   
<!-- /single-portfolio -->