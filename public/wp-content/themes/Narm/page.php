<?php
/**
 * The main template file for display page.
 *
 * @package WordPress
 * @subpackage Narm
*/


/**
*	Get Current page object
**/
$page = get_page($post->ID);


/**
*	Get current page id
**/
$current_page_id = '';

if(isset($page->ID))
{
    $current_page_id = $page->ID;
}


/**
*	Check if contact page
**/
$nm_contact_page = get_option('nm_contact_page');
/**
*	if contact page
**/
if($current_page_id == $nm_contact_page)
{
    include (TEMPLATEPATH . "/templates/template-contact.php");
    exit;
}


/**
*	Check if gallery page
**/
$nm_gallery_page = get_option('nm_gallery_page');
/**
*	Check if portfolio page
**/
$nm_portfolio_page = get_option('nm_portfolio_page');
/**
*	Check if blog page
**/
$nm_blog_page = get_option('nm_blog_page');


/**
*	if gallery page
**/
if($current_page_id == $nm_gallery_page)
{
	$nm_gallery_column = get_option('nm_gallery_column'); 
	
	if(empty($nm_gallery_column))
	{
		$nm_gallery_column = '1';
	}
	
	include (TEMPLATEPATH . "/templates/template-gallery-".$nm_gallery_column.".php");
    exit;
}
/**
*	if portfolio page
**/
else if($current_page_id == $nm_portfolio_page)
{
    include (TEMPLATEPATH . "/templates/template-portfolio.php");
    exit;
}
/**
*	if blog page
**/
else if($current_page_id == $nm_blog_page)
{
    include (TEMPLATEPATH . "/templates/template-blog.php");
    exit;
}


/**
*	if other page
**/
else
{

$page_style = get_post_meta($current_page_id, 'page_style', true);
$page_description = get_post_meta($current_page_id, 'page_description', true);
$page_sidebar = get_post_meta($current_page_id, 'page_sidebar', true);

$caption_class = "page_caption";

if(empty($page_style))
{
	$page_style = 'Fullwidth';
}

$add_sidebar = FALSE;
if($page_style == 'Right Sidebar')
{
	$add_sidebar = TRUE;
	$page_class = 'sidebar_content';
}
else
{
	$page_class = 'inner_wrapper';
}

get_header(); 
?>

		<!-- Begin content -->
		<div id="content_wrapper">
		
			<br class="clear"/>
		
			<div class="<?php echo $caption_class; ?>">
				<div class="caption_inner">
					<h1 class="cufon"><?php the_title(); ?></h1>
					
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
					
					<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>		
						
						<?php if($add_sidebar) { ?>
							<div class="sidebar_content">
						<?php } ?>
						
								<?php do_shortcode(the_content()); ?>
								
						<?php if($add_sidebar) { ?>
							</div>
						<?php } ?>

					<?php endwhile; ?>
					
					<?php
						if($add_sidebar)
						{
					?>
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
					<?php
						}
					?>
				
				</div>
				<!-- End main content -->
				
				<br class="clear"/><br/>
			</div>
			
		</div>
		<!-- End content -->


<?php get_footer(); ?>

<?php
}
//end if other page
?>