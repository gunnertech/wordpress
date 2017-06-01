<?php
/**
 * The main template file.
 *
 * @package WordPress
 * @subpackage Narm
 */

$nm_homepage_hide_slider = get_option('nm_homepage_hide_slider');
$nm_slider_height = get_option('nm_slider_height');
$nm_slider_cat = get_option('nm_slider_cat');
$nm_slider_items = get_option('nm_slider_items');

if(empty($nm_slider_items))
{
	$nm_slider_items = 5;
}

if(empty($nm_slider_height))
{
	$nm_slider_height = 405;
}

$nm_slider_height_offset = $nm_slider_height - 405;
$nm_slider_timer = get_option('nm_slider_timer'); 

if(empty($nm_slider_timer))
{
	$nm_slider_timer = 5;
}

get_header(); ?>

		<input type="hidden" id="slider_timer" name="slider_timer" value="<?php echo $nm_slider_timer; ?>"/>

		<?php
			if(!$nm_homepage_hide_slider)
			{
				$nm_slider_sort = get_option('nm_slider_sort'); 
				if(empty($nm_slider_sort))
				{
					$nm_slider_sort = 'ASC';
				}
			
				$slider_arr = get_posts('numberposts='.$nm_slider_items.'&order='.$nm_slider_sort.'&orderby=date&category='.$nm_slider_cat);
				
				if(!empty($slider_arr))
				{
		?>
		
				<div id="slider">
					<div class="wrapper">
						<ul>
							<?php
								foreach($slider_arr as $key => $gallery_item)
								{
													
									$gallery_type = get_post_meta($gallery_item->ID, 'gallery_type', true);
									$image_url = get_post_meta($gallery_item->ID, 'gallery_image_url', true);
									$youtube_id = get_post_meta($gallery_item->ID, 'gallery_youtube_id', true);
									$vimeo_id = get_post_meta($gallery_item->ID, 'gallery_vimeo_id', true);
									$content_align = strtolower(get_post_meta($gallery_item->ID, 'content_align', true));
									
									if(empty($content_align))
									{
										$content_align = 'left';
									}
									
									switch($gallery_type)
									{
										case 'Image':
							?>
							<li>
								<img src="<?php echo get_image_path($image_url); ?>" style="height:<?php echo 447+$nm_slider_height_offset; ?>px; width: 960px;" alt=""/>
								<div class="caption-<?php echo $content_align; ?>">
									<h3 class="cufon"><?php echo $gallery_item->post_title; ?></h3>
									<p><?php echo $gallery_item->post_excerpt; ?></p>
								</div>
							</li>
							<?php
										break;
										//End image gallery
														
										case 'Vimeo Video':
							?>
							<li>
								<object width="939" height="<?php echo 400+$nm_slider_height_offset; ?>"><param name="allowfullscreen" value="true" /><param name="wmode" value="opaque"><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $vimeo_id; ?>&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $vimeo_id; ?>&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="939" height="<?php echo 400+$nm_slider_height_offset; ?>" wmode="transparent"></embed></object>
							</li>
							<?php
										break;
										//End Vimeo video
										
										case 'Youtube Video':
							?>
							
							<li>
								<object type="application/x-shockwave-flash" data="http://www.youtube.com/v/<?php echo $youtube_id?>&hd=1" style="width:939px;height:<?php echo 400+$nm_slider_height_offset; ?>px">
								<param name="wmode" value="opaque">
			        		    <param name="movie" value="http://www.youtube.com/v/<?php echo $youtube_id?>&hd=1" />
			    			</object>
							</li>
							
							<?php
										break;
										//End Youtube video
							
									}
								}
							?>
						</ul>
					</div>
				</div>
		
		<?php 
				}
			}
		?>
		
		<!-- Begin content -->
		<div id="content_wrapper">
			
			<div class="inner">
				
				<!-- Begin tagline content -->
				<div id="home_tagline">
					<h2>
						<?php
						    //get tagline header
						    $nm_tagline_header = get_option('nm_homepage_tagline_header');
						    
						    if(empty($nm_tagline_header))
						    {
						    	$nm_tagline_header = 'Built-in so many custom modification';
						    }
						    
						    echo $nm_tagline_header;
						?>
					</h2>
						<?php
						    //get tagline
						    $nm_tagline = get_option('nm_homepage_tagline');

							if(!empty($nm_tagline))
							{
						?>
								<span class="desc"><?php echo $nm_tagline; ?></span>
						<?php
							}
						?>
						<br class="clear"/>
				</div>
				<!-- End tagline content -->
				
				
				<!-- Begin main content -->
				<div class="home_wrapper">&nbsp;
				
					<?php
						/**
						*	Get homepage box category
						**/
						$nm_homepage_hide_boxes = get_option('nm_homepage_hide_boxes'); 
						$nm_box_cat = get_option('nm_box_cat');
						
						$box_posts = get_posts('numberposts=-1&category='.$nm_box_cat.'&orderby=date&order=ASC');
						$all_boxes = count($box_posts);
						
						if($all_boxes > 0 && !$nm_homepage_hide_boxes)
						{
					?>
				
					<div class="home_box_wrapper">
					
						<?php
								
						//This loop to display each slide
						foreach($box_posts as $key => $box_post)
						{
							 
							$box_icon = get_post_meta($box_post->ID, 'home_box_icon_url', true);
							
							$last_class = '';
							if(($key+1)%3 == 0)
							{
								$last_class = 'last';	
							}
						
						?>
						
						<div class="one_third <?php echo $last_class?>">
							<div class="home_thumb">
							
								<?php
								if(!empty($box_icon))
								{
									?> <img src="<?php echo $box_icon; ?>" alt=""/>

									<?php
								}
								?>
								
							</div>
							<div class="home_box">
								<h4 class="cufon"><?php echo $box_post->post_title; ?></h4>
								<p><?php echo do_shortcode($box_post->post_excerpt); ?></p>
							</div>
						</div>
						
						<?php
						} //end foreach
						?>
						
					</div>
					<br class="clear"/>
					
					<?php
					} //end if has contents
					?>
				
				</div>
				<!-- End main content -->
				
				<br class="clear"/>
				
				<?php
					$nm_homepage_hide_portfolio = get_option('nm_homepage_hide_portfolio'); 
					$nm_portfolio_cat = get_option('nm_portfolio_cat'); 
					$all_photo_arr = get_posts('numberposts=5&order=DESC&orderby=date&category='.$nm_portfolio_cat);
					$nm_portfolio_slider_speed = get_option('nm_portfolio_slider_speed'); 
					
					if(empty($nm_portfolio_slider_speed))
					{
						$nm_portfolio_slider_speed = 5;
					}
					
					//Get portfolio sub categories
					$portfolio_cat_arr = get_categories(array('parent' => $nm_portfolio_cat));
				?>
				
				<input type="hidden" id="slider_speed" name="slider_speed" value="<?php echo $nm_portfolio_slider_speed; ?>"/>
				
				<?php
						if(!empty($all_photo_arr) && !$nm_homepage_hide_portfolio)
						{
				?>				
				
				<!-- Begin main content -->
				<div class="portfolio_wrapper">
				
				<div class="header_text">
					<h2 class="cufon">Recent Portfolio</h2>
					
					<br class="clear"/>
					<hr/>
					
					<div class="sub_tab" style="padding-top:0px">
					<ul>
						<li>
							<a href="<?php echo get_category_link( $nm_portfolio_cat ); ?>" class="active">Recent</a>
						</li>
						<?php
							if(!empty($portfolio_cat_arr))
							{
								foreach($portfolio_cat_arr as $portfolio_cat)
								{
						?>
									<li>
										<a href="<?php echo get_category_link( $portfolio_cat->cat_ID ); ?>" <?php if($cat->cat_ID == $portfolio_cat->cat_ID) { ?>class="active"<?php } ?>><?php echo $portfolio_cat->name; ?></a>
									</li>
						<?php
								}
							}
						?>
					</ul>
				</div>
				</div>
				
				<br class="clear"/>
				
				<div id="inner_slide" class="inner_slide">
				
				<div class="inner_wrapper">
						
				<?php
					//Get portfolio width and height
					$nm_portfolio_width = get_option('nm_portfolio_width');
					if(empty($nm_portfolio_width))
					{
						$nm_portfolio_width = 450;
					}
					$nm_portfolio_height = get_option('nm_portfolio_height');
					if(empty($nm_portfolio_height))
					{
						$nm_portfolio_height = 200;
					}
					
					$nm_portfolio_desc_height = get_option('nm_portfolio_desc_height');
					if(empty($nm_portfolio_desc_height))
					{
						$nm_portfolio_desc_height = 300;
					}
					
					//cal width offset
					$portfolio_offset = $nm_portfolio_width - 450;
				?>
				
				<input type="hidden" id="portfolio_width" name="portfolio_width" value="<?php echo $nm_portfolio_width; ?>"/>				
				
				<?php
					foreach($all_photo_arr as $key => $photo)
					{
						$item_type = get_post_meta($photo->ID, 'gallery_type', true); 

   		 				if(empty($item_type))
   		 				{
   		 					$item_type = 'Image';
   		 				}
					
						$image_url = get_post_meta($photo->ID, 'gallery_image_url', true);
						$small_image_url = get_post_meta($photo->ID, 'gallery_preview_image_url', true);
						
						//if not have preview image then create from timthumb
						if(empty($small_image_url))
						{
							$small_image_url = get_image_path($image_url);
						}
						
						$youtube_id = get_post_meta($photo->ID, 'gallery_youtube_id', true);
						$vimeo_id = get_post_meta($photo->ID, 'gallery_vimeo_id', true);
				?>
		
				<div class="card" style="width:<?php echo intval(450+$portfolio_offset); ?>px;height:<?php echo intval($nm_portfolio_height+$nm_portfolio_desc_height); ?>px">
					<?php 
    					switch($item_type)
    					{
    						case 'Image':
    				?>		
							<a href="<?php echo $image_url?>" class="portfolio_image">
								<img src="<?php echo $small_image_url?>" alt=""/>
							</a>
					<?php
    					break;
    					//End image type
    					
    						case 'Youtube Video':
    				?>			
    						
    						<object type="application/x-shockwave-flash" data="http://www.youtube.com/v/<?php echo $youtube_id?>" style="width:<?php echo $nm_portfolio_width; ?>px;height:<?php echo $nm_portfolio_height; ?>px">
								<param name="wmode" value="opaque">
			        		    <param name="movie" value="http://www.youtube.com/v/<?php echo $youtube_id?>" />
			    			</object>
    						
    				<?php		
    						break;
    						//End youtube video type
    						
    						case 'Vimeo Video':
    				?>			
    						
    						<object width="<?php echo $nm_portfolio_width; ?>" height="<?php echo $nm_portfolio_height; ?>" data="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $vimeo_id; ?>&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" type="application/x-shockwave-flash">
			  				    	<param name="allowfullscreen" value="true" />
			  				    	<param name="allowscriptaccess" value="always" />
									<param name="wmode" value="opaque">
			  				    	<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $vimeo_id; ?>&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" />
							</object>
    						
    				<?php		
    						break;
    						//End vimeo video type
    					}		
    				?>			
					
					<?php
						if(!empty($photo->post_title) OR !empty($photo->post_content))
						{
					?>
					
					<span class="title" style="width:<?php echo intval(420+$portfolio_offset); ?>px">
					
						<?php
							if(!empty($photo->post_title))
							{
						?>
								<h3 class="portfolio_cufon"><?php echo $photo->post_title; ?></h3><br/>
						<?php
							}
						?>
						
						<?php echo do_shortcode($photo->post_content); ?>
					</span>
					
					<?php
						}
					?>
					
				</div>
				
				<?php
					}	
				?>
			
			</div>
			
			<div id="move_prev"></div>
			<div id="move_next"></div>
			
			</div>
			<!-- End content -->
			
			<br class="clear"/>
			
			<div id="content_slider_wrapper"><div id="content_slider"></div></div>
			
			<?php
				}
			?>
			</div>
			</div>
			
			
			<?php
				//get recent blog post
				$nm_homepage_hide_blog = get_option('nm_homepage_hide_blog'); 
				$nm_blog_cat = get_option('nm_blog_cat'); 
				$nm_blog_page = get_option('nm_blog_page'); 
				$blog_posts_arr = get_posts('numberposts=4&order=DESC&orderby=date&category='.$nm_blog_cat);
				
				if(!empty($blog_posts_arr) && !$nm_homepage_hide_blog)
				{
			?>
			
				<br class="clear"/><br/>
				
				<div class="header_text">
					<h2 class="cufon">Recent <?php echo get_the_title($nm_blog_page); ?></h2>
				</div>
				
				<br class="clear"/>
				<hr/>
				
				<div class="content_align">
					<div class="two_third">
						
						<?php
							$image_thumb = get_post_meta($blog_posts_arr[0]->ID, 'blog_thumb_image_url', true);
						?>
					
						<div class="one_third">
							<img src="<?php echo get_image_path($image_thumb); ?>" style="height:150px; width:150px;" alt="" class="frame" />
						</div>
						<div class="two_third last">
							<h3 class="cufon"><a href="<?php echo get_permalink($blog_posts_arr[0]->ID); ?>"><?php echo $blog_posts_arr[0]->post_title; ?></a></h3>
								<div class="recent_post_detail">
									<?php the_time('F j, Y'); ?> <?php edit_post_link('edit post', ', ', ''); ?>
									&nbsp;|&nbsp;
									<?php comments_number('No comment', 'Comment', '% Comments'); ?>
								</div>
							<br/>
							<?php echo _substr(strip_tags(strip_shortcodes($blog_posts_arr[0]->post_content)), 600); ?>
						</div>
					</div>
					
					<div class="one_third last">
						<?php 
							foreach($blog_posts_arr as $key => $blog_post)
							{
								if($key > 0)
								{
								
								$image_thumb = get_post_meta($blog_post->ID, 'blog_thumb_image_url', true);
						?>
						
								<img src="<?php echo get_image_path($image_thumb); ?>" style="height:75px; width:75px;" alt="" class="frame_left" />
								<strong><a href="<?php echo get_permalink($blog_post->ID); ?>"><?php echo $blog_post->post_title; ?></a></strong>
								<br/>
								<?php echo _substr(strip_tags(strip_shortcodes($blog_post->post_content)), 50); ?>
								
								<br class="clear"/><br/>
						
						<?php
								}
							}
						?>
					</div>
				</div>
				
				<br class="clear"/><br/>
				
			<?php
				}
			?>
			
		</div>
		<!-- End content -->

<?php get_footer(); ?>