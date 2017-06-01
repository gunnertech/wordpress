<?php
/**
 * The main template file for display error page.
 *
 * @package WordPress
 * @subpackage Narm
*/


get_header(); 

?>

		<!-- Begin content -->
		<div id="content_wrapper">
		
			<br class="clear"/>
		
			<div class="page_caption">
				<div class="caption_inner">
					<h1 class="cufon">404 Not Found</h1>
				</div>
			</div>
			
			<div class="inner">
				
				<!-- Begin main content -->
				<div class="inner_wrapper">
					
					<div class="sidebar_content">
						<h2 class="cufon"><?php _e( 'Oops!', 'narm' ); ?></h2>
						<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'narm' ); ?></p>
					</div>
					
				</div>
				<!-- End main content -->
				
				<br class="clear"/>
			</div>
			
		</div>
		<!-- End content -->
				

<?php get_footer(); ?>