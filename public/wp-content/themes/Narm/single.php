<?php
/**
 * The main template file for display single post page.
 *
 * @package WordPress
 * @subpackage Narm
*/


get_header(); 

$nm_blog_page = get_option('nm_blog_page');
$page_description = get_post_meta($nm_blog_page, 'page_description', true);
$page_sidebar = get_post_meta($nm_blog_page, 'page_sidebar', true);

if(empty($page_sidebar))
{
	$page_sidebar = 'Blog Sidebar';
}

$caption_class = "page_caption";


//Make blog menu active
if(!empty($nm_blog_page))
{
?>

<script>
$('ul#main_menu li.page-item-<?php echo $nm_blog_page; ?>').addClass('current_page_item');
</script>

<?php
}
?>
		<!-- Begin content -->
		<div id="content_wrapper">
		
			<br class="clear"/>
		
			<div class="<?php echo $caption_class?>">
				<div class="caption_inner">
					<h1 class="cufon"><?php echo get_the_title($nm_blog_page); ?></h1>
					
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
									<!-- Posted by:&nbsp;<?php the_author(); ?>&nbsp;&nbsp;&nbsp; -->
									Tags:&nbsp;
									<?php the_tags(''); ?>&nbsp;&nbsp;&nbsp;
									<!-- Posted date:&nbsp;
									<?php the_time('F j, Y'); ?> <?php edit_post_link('edit post', ', ', ''); ?>
									&nbsp;|&nbsp; -->
									<?php comments_number('No comment', 'Comment', '% Comments'); ?>
								</div>
							</div>
							<br class="clear"/><br/>
							
							<div class="post_img">
								<img src="<?php echo get_image_path($image_thumb); ?>" style="height:350px; width:350px;" alt="" class="frame" />
							
								<div class="post_img_date">
									<?php the_time('F j, Y'); ?>
								</div>
							</div>
							
							<br class="clear"/>
							
							<?php echo the_content(); ?>
							
						</div>
						<!-- End each blog post -->
						
							
						<div id="about_the_author">
							<div class="header">
								<span>About the author</span>
							</div>
							<div class="thumb"><?php echo get_avatar( get_the_author_email(), '50' ); ?></div>
							<div class="description">
								<strong><?php the_author_link(); ?></strong><br/>
								<?php the_author_description(); ?>
							</div>
						</div>
						
						<br class="clear"/><br/><br/>


						<?php comments_template( '' ); ?>
						

<?php endwhile; endif; ?>

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