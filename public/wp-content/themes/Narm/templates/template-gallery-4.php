<?php
/**
 * The main template file for display gallery page.
 *
 * @package WordPress
 * @subpackage Narm
*/


get_header(); 

$page_description = get_post_meta($current_page_id, 'page_description', true);
$caption_class = "page_caption";

?>
				
		<!-- Begin content -->
		<div id="content_wrapper">
		
			<br class="clear"/>
		
			<div class="<?php echo $caption_class?>">
				<div class="caption_inner">
					<h1 class="cufon"><?php the_title(); ?></h1>
					<p><?php echo $page_description?></p>
				</div>
			</div>
			
			<div class="inner">
			
				<!-- Begin main content -->
				<div class="inner_wrapper">
						
						<!-- Begin gallery content -->
						
						<?php
							//Define items per page
 							$item_per_page = get_option('nm_gallery_items');
 							if(empty($item_per_page))
 							{
 								$item_per_page = 8;
 							}
						
							//prepare data for pagintion
							if(!isset($_GET['page']) OR empty($_GET['page']) OR $_GET['page'] == 1)
						    {
						    	$current_page = 1;
						    }
						    else
						    { 
						    	$current_page = $_GET['page'];
						    	$offset = (($current_page-1) * $item_per_page);
						    	$offset_query = '&offset='.$offset;
						    }
						
						    /**
						    *	Get all photos
						    **/
						    $nm_gallery_cat = get_option('nm_gallery_cat');
						    $nm_gallery_sort = get_option('nm_gallery_sort'); 
							if(empty($nm_gallery_sort))
							{
								$nm_gallery_sort = 'ASC';
							}
							
						    $all_items_arr = get_posts('numberposts=-1&order='.$nm_gallery_sort.'&orderby=date&category='.$nm_gallery_cat);
						    $total = count($all_items_arr);
						
							$gallery_items = array();
							$query_str = 'numberposts='.$item_per_page.'&order='.$nm_gallery_sort.'&orderby=date&category='.$nm_gallery_cat.$offset_query;
						    $gallery_items = get_posts($query_str);
		
							if(isset($gallery_items) && !empty($gallery_items))
							{
								
						?>
						
											<?php

												foreach($gallery_items as $key => $gallery_item)
												{
													
													$gallery_type = get_post_meta($gallery_item->ID, 'gallery_type', true);
													$image_url = get_post_meta($gallery_item->ID, 'gallery_image_url', true);
													$youtube_id = get_post_meta($gallery_item->ID, 'gallery_youtube_id', true);
													$vimeo_id = get_post_meta($gallery_item->ID, 'gallery_vimeo_id', true);
													
													$last_class = '';
													$line_break = '';
													if(($key+1) % 4 == 0)
													{	
														$last_class = ' last';
														$line_break = '<br class="clear"/><br/><br/><br/>';
													}
													
													switch($gallery_type)
													{
														case 'Image':
											?>
															<div class="one_fourth<?php echo $last_class?>">
																<div class="gallery_image">
																	<a href="<?php echo $image_url?>" class="portfolio_image">
																		<img style="height:131px; width:180px;" src="<?php echo get_image_path($image_url); ?>" alt="" class="frame"/>
																	</a>
																	
																	<span class="gallery4_hover">
																		<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/icon_zoom.png" alt=""/>
																	</span>
																</div>
																
																<div class="gallery_desc">
																	<h4 class="cufon"><?php echo $gallery_item->post_title?></h4><br/>
																	<?php echo do_shortcode($gallery_item->post_excerpt)?>
																</div>
															</div>

											<?php
														break;
														//End image gallery
														
														case 'Youtube Video':
											?>
												    
													    	<div class="one_fourth<?php echo $last_class?>">
															    <div class="gallery_image">
																	<a href="#youtube_video<?php echo $key?>" class="portfolio_youtube">
																		<img src="<?php echo get_image_path($image_url); ?>&h=131&w=180&zc=1<?php echo $image_url?>" alt="" class="frame"/>
																	</a>
																	
																	<span class="gallery4_hover">
																		<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/icon_play.png" alt=""/>
																	</span>
																</div>	
																
																<div class="gallery_desc">
																	<h4 class="cufon"><?php echo $gallery_item->post_title?></h4><br/>
																	<?php echo do_shortcode($gallery_item->post_excerpt)?>
																	
																	<!-- Begin youtube video content -->
																	<div style="display:none;">
																	    <div id="youtube_video<?php echo $key?>" style="width:640px;height:385px">
																	        
																	        <object type="application/x-shockwave-flash" data="http://www.youtube.com/v/<?php echo $youtube_id?>" style="width:640px;height:385px">
											        				    		<param name="movie" value="http://www.youtube.com/v/<?php echo $youtube_id?>" />
											    					    	</object>
																	        
																	    </div>	
																	</div>
																	<!-- End youtube video content -->
																</div>
															</div>
												    
											<?php
														break;
														//End youtube video gallery
														
														case 'Vimeo Video':
											?>
												    
													    	<div class="one_fourth<?php echo $last_class?>">
													    		<div class="gallery_image">
																	<a href="#vimeo_video<?php echo $key?>" class="portfolio_vimeo">
																		<img src="<?php echo get_image_path($image_url); ?>&h=131&w=180&zc=1<?php echo $image_url?>" alt="" class="frame"/>
																	</a>
																	
																	<span class="gallery4_hover">
																		<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/icon_play.png" alt=""/>
																	</span>
																</div>	
																
																<div class="gallery_desc">
																	<h4 class="cufon"><?php echo $gallery_item->post_title?></h4><br/>
																	<?php echo do_shortcode($gallery_item->post_excerpt)?>
																	
																	<!-- Begin vimeo video content -->
																	<div style="display:none;">
																	    <div id="vimeo_video<?php echo $key?>" style="width:601px;height:338px">
																	    
																	        <object width="601" height="338" data="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $vimeo_id?>&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" type="application/x-shockwave-flash">
											  						    		<param name="allowfullscreen" value="true" />
											  						    		<param name="allowscriptaccess" value="always" />
											  						    		<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $vimeo_id?>&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" />
																	    	</object>
																	        
																	    </div>	
																	</div>
																	<!-- End vimeo video content -->
																</div>
															</div>
										    
										    <?php
										    			break;
										    			//End vimeo video gallery
										    			
													}
												
													echo $line_break;
												}
												//End foreach loop
												
										    ?>
								
							<?php
								$base_link = get_permalink($post->ID);
								
								echo gen_pagination($total, $current_page, $base_link, TRUE, $item_per_page);
								
							}
							//End if have gallery items
							?>
						
						    
						</div>
						<!-- End main content -->
					
					<br class="clear"/><br/>
				</div>
			
		</div>
		<!-- End content -->
				

<?php get_footer(); ?>