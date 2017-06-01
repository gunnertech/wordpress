<?php
/*
 Single Portfolio Page
*/
?>
<?php
wp_enqueue_script( 'flexislider', MTHEME_JS . '/flexislider/jquery.flexslider-min.js', array('jquery') , '',true );
wp_enqueue_style( 'flexislider_css', MTHEME_ROOT . '/css/flexislider/flexslider-page.css', false, 'screen' );
function flexislideshow_init() {
	?>
<!-- Flexi Slider init -->
<script type="text/javascript">
	jQuery(window).load(function() {
		jQuery('.flexslider').flexslider({
			animation: "slide",
			slideshow: true,
			pauseOnAction: true,
			pauseOnHover: false,
			controlsContainer: "flexslider-container-page"
		});
	});
</script>
	<?php
}
add_action('wp_footer', 'flexislideshow_init',20);
?>
<?php get_header(); ?>
<?php
/**
*  Portfolio Loop
 */
?>
<h1 class="entry-title"><?php the_title(); ?></h1>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

					
						
					
						<?php
						$width=FULLPAGE_WIDTH;
						$single_height='';
						
						$custom = get_post_custom($post->ID);
						
						$portfolio_page_header=$custom["portfolio_page_header"][0];
						$height=$custom["portfolio_slide_height"][0];
						$portfolio_videoembed=$custom["portfolio_videoembed"][0];
						$custom_link=$custom["custom_link"][0];
						if (isset($custom["portfolio_slide_style"][0])) $portfolio_style=$custom["portfolio_slide_style"][0];
						
						if (isset($custom["portfolio_client"][0])) $portfolio_client=$custom["portfolio_client"][0];
						if (isset($custom["portfolio_projectlink"][0])) $portfolio_projectlink=$custom["portfolio_projectlink"][0];
						
						switch ($portfolio_page_header) {
						
							case "Slideshow" :

								$flexi_slideshow = do_shortcode('[flexislideshow imagesize="fullwidth"]');
								echo $flexi_slideshow;
								
							break;
							case "Image" :
								// Show Image									
								echo display_post_image (
									$post->ID,
									$have_image_url=false,
									$link=false,
									$type="fullwidth",
									$post->post_title,
									$class="portfolio-single-image" 
								);

							break;
							case "Video Embed" :
							echo '<div class="fitVids">';
							
								echo $portfolio_videoembed;
								
							echo '</div>';
							break;
							
						}
								
								
						?>
						
		<div class="fullpage-contents-wrap">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<ul class="portfolio-metainfo">
							<?php if ( isset($portfolio_client) ) { ?>
							<li class="ajax-client"><?php echo $portfolio_client; ?></li>
							<?php } ?>
							<?php if ( $portfolio_projectlink<>"" ) { ?>
							<li class="ajax-link"><a target="_blank" href="<?php echo $portfolio_projectlink; ?>">Project Link</a></li>
							<?php } ?>
							<li class="ajax-type">
								<?php echo get_the_term_list( $post->ID, 'types', '', ' , ', '' ); ?>
							</li>
						</ul>
						
						<div class="entry-content clearfix">
						<?php the_content(); ?>
					
						</div>
						
						
					</div>
							
<?php endwhile; // end of the loop. ?>

<?php require_once (MTHEME_INCLUDES . 'related-portfolio.php'); ?>

<?php comments_template(); ?>

</div>
<?php get_footer(); ?>