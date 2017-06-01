<?php
/**
 * The template for displaying the footer.
 *
 * @package WordPress
 * @subpackage Narm
 */
?>

		<!-- Begin footer -->
		<div id="footer">
			<div class="shadow"></div>
			
			<ul class="sidebar_widget">
				<?php dynamic_sidebar('Footer Sidebar'); ?>
			</ul>
			
			<br class="clear"/>
			<div id="copyright">
				<?php
					/**
					 * Get footer text
					 */
	
					$nm_footer_text = get_option('nm_footer_text');
	
					if(empty($nm_footer_text))
					{
						$nm_footer_text = 'Copyright © 2010 Peerapong. Remove this once after purchase from the ThemeForest.net';
					}
					
					echo preg_replace('/\\\"/','"',$nm_footer_text);
				?>
			</div>
			
		</div>
		<!-- End footer -->
		
	</div>
	<!-- End template wrapper -->

<?php
		/**
    	*	Setup Google Analyric Code
    	**/
    	include (TEMPLATEPATH . "/google-analytic.php");
?>

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>
