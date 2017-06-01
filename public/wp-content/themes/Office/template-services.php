<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 * Template Name: Services
 */
 global $data; //get theme options
?>

<?php get_header(); ?>

<?php
if (have_posts()) : while (have_posts()) : the_post();
$page_slider = get_post_meta($post->ID, 'office_page_slider', true); //get meta
if ($page_slider == 'enable') {
	get_template_part( 'includes/page-slides'); //show slider
}
?>

    
<div id="page-heading">
	<h1><?php the_title(); ?></h1>	
    <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?> 
</div>
<!-- /page-heading -->
    
<?php
$content = $post->post_content;
if(!empty($content)) { ?>
	<div id="services-description" class="clearfix">
		<?php the_content(); ?>
	</div>
	<!--/services description -->
<?php } ?>
     
     
<div id="services-wrap" class="clearfix">
    
		<?php		
		// get custom post type ==> homepage-tabs
		global $post;
		$args = array(
			'post_type'=>'services',
			'numberposts' => -1,
			'order' => 'ASC'
		);
		$services_posts = get_posts($args);
		?>
        
        <ul id="service-tabs">
			<?php
            //tabs
			$count=0;
            foreach($services_posts as $post) : setup_postdata($post);
			$count++;
            ?>
            <li><a href="#tab-<?php echo $count; ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <!-- /service tabs -->
        
        <div id="service-content" class="entry">
			<?php
            //tab content
			$count=0;
            foreach($services_posts as $post) : setup_postdata($post);
			$count++;
            ?>
            <div id="tab-<?php echo $count; ?>" class="service-tab-content">
                <h2><?php the_title(); ?></h2>
                <?php the_content(); ?>
            </div>
            <!-- /service-tab-content -->
            <?php endforeach; ?>
        </div>
        <!-- /service-content -->
    
</div>
<!-- /services-wrap -->

<?php wp_reset_query(); ?>

<?php endwhile; ?>
<?php endif; ?>	
<?php get_footer(); ?>