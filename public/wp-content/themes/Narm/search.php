<?php
/**
 * The main template file for display search page.
 *
 * @package WordPress
 * @subpackage Narm
*/


get_header(); 

//if display normal category blog posts
$nm_blog_page = get_option('nm_blog_page'); 
$page_sidebar = get_post_meta($nm_blog_page, 'page_sidebar', true);

if(empty($page_sidebar))
{
	$page_sidebar = 'Blog Sidebar';
}

$caption_class = "page_caption";

?>

		<!-- Begin content -->
		<div id="content_wrapper">
		
			<br class="clear"/>
		
			<div class="<?php echo $caption_class?>">
				<div class="caption_inner">
					<h1 class="cufon"><?php printf( __( 'Results for &quot;%s&quot;', 'narm' ), '' . get_search_query() . '' ); ?></h1>
					
					<?php
						if(!empty($page_description))
						{
					?>
							<p class="cufon"><?php echo $page_description?></p>
					<?php
						}
					?>
				</div>
			</div>
			
			<div class="inner">
			
				<!-- Begin main content -->
				<div class="inner_wrapper">
				
					<div class="sidebar_content">
<?php

global $more; $more = false; # some wordpress wtf logic
//Get blog post category id
$nm_blog_cat = get_option('nm_blog_cat'); 

$query_string ="cat=$nm_blog_cat&paged=$paged";

query_posts($query_string);

if (have_posts()) : while (have_posts()) : the_post();

	$image_thumb = get_post_meta(get_the_ID(), 'blog_thumb_image_url', true);
?>
						
						
						<!-- Begin each blog post -->
						<div class="post_wrapper">
							<div class="post_header">
								<h2 class="cufon">
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
										<?php the_title(); ?>									
									</a>
								</h2>
								<div class="post_detail">
									Posted by:&nbsp;<?php the_author(); ?>&nbsp;&nbsp;&nbsp;
									Tags:&nbsp;
									<?php the_tags(''); ?>&nbsp;&nbsp;&nbsp;
									Posted date:&nbsp;
									<?php the_time('F j, Y'); ?> <?php edit_post_link('edit post', ', ', ''); ?>
									&nbsp;|&nbsp;
									<?php comments_number('No comment', 'Comment', '% Comments'); ?>
								</div>
							</div>
							<br class="clear"/><br/>
							
							<div class="post_img">
								<img src="<?php echo get_image_path($image_thumb); ?>" style="height:200px; width:590px;" alt="" class="frame" />
							
								<div class="post_img_date">
									<?php the_time('F j, Y'); ?>
								</div>
							</div>
							
							<br class="clear"/>
							
							<?php echo get_the_content_with_formatting(); ?>
							
						</div>
						<!-- End each blog post -->
						



<?php endwhile; endif; ?>

						<div class="pagination"><p><?php posts_nav_link(' '); ?></p></div>
						
					</div>
					
					<div class="sidebar_wrapper">
						<div class="sidebar">
							
							<div class="content">
							
								<ul class="sidebar_widget">
									<?php dynamic_sidebar($page_sidebar); ?>
								</ul>
								
							</div>
						
						</div>
						<br class="clear"/>
					
						<div class="sidebar_bottom"></div>
					</div>
					
				</div>
				<!-- End main content -->
				
				<br class="clear"/>
			</div>
			
			<div class="bottom"></div>
			
		</div>
		<!-- End content -->
				

<?php get_footer(); ?>