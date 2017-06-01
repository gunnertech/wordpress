<?php
/**
 * The main template file for display portfolio page.
 *
 * @package WordPress
 * @subpackage Narm
*/


get_header(); 

$caption_class = "page_caption";

$cat = get_category(get_query_var('cat'),false);
$sub_cat = '';

$nm_portfolio_cat = get_option('nm_portfolio_cat'); 
$sub_cat = $cat->name;
$page_description = $cat->category_description;

if($cat->cat_ID == $nm_portfolio_cat)
{
	$sub_cat = '';
	$main_cat = $cat->name;
}
else
{
	$portfolio_cat = get_category($nm_portfolio_cat,false);
	$main_cat = $portfolio_cat->name;
}

$nm_portfolio_sort = get_option('nm_portfolio_sort'); 
if(empty($nm_portfolio_sort))
{
	$nm_portfolio_sort = 'ASC';
}
				
$all_photo_arr = get_posts('numberposts=-1&order='.$nm_portfolio_sort.'&orderby=date&category='.$cat->cat_ID);
$nm_portfolio_slider_speed = get_option('nm_portfolio_slider_speed'); 

if(empty($nm_portfolio_slider_speed))
{
	$nm_portfolio_slider_speed = 5;
}

//Get portfolio sub categories
$portfolio_cat_arr = get_categories(array('parent' => $nm_portfolio_cat));
?>

		<input type="hidden" id="slider_speed" name="slider_speed" value="<?php echo $nm_portfolio_slider_speed; ?>"/>				
		<!-- Begin content -->
		<div id="content_wrapper">
			<br class="clear"/>
		
			<div class="<?php echo $caption_class; ?>">
				<div class="caption_inner">
					<h1 class="cufon"><?php echo $main_cat; ?><?php if(!empty($sub_cat)) { echo ' / '.$sub_cat; } ?></h1>
					
					<?php 
						if(!empty($page_description))
						{
					?>
						<p><?php echo $page_description; ?></p>
					<?php
						}
					?>
					
				</div>
			</div>
			
			<div class="inner">
				<br class="clear"/><br/>
			
				<div class="portfolio_wrapper">
				<div class="sub_tab" style="padding-top:0">
					<ul>
						<li>
							<a href="<?php echo get_category_link( $nm_portfolio_cat ); ?>" <?php if($cat->cat_ID == $nm_portfolio_cat) { ?>class="active"<?php } ?>>All</a>
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
				
				<?php
						if(!empty($all_photo_arr))
						{
				?>				
				
				<!-- Begin main content -->
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
							$small_image_url = get_bloginfo( 'stylesheet_directory' ).'/timthumb.php?src='.$image_url.'&h='.$nm_portfolio_height.'&w='.$nm_portfolio_width.'&zc=1';
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
				</div>
			
		</div>
		<!-- End content -->
				

<?php get_footer(); ?>