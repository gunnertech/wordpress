<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 * Template Name: FAQs
 */
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
<!-- END page-heading -->

<?php
$content = $post->post_content;
if(!empty($content)) { ?>
	<div id="faqs-description">
    	<?php the_content(); ?>
    </div>
    <!-- /faqs-description -->
<?php }?>

<div class="post full-width clearfix">

	<?php
	$cats = get_terms('faqs_cats');
	if($cats[0]) {
	?>
        <?php 
		$args = array(
			'taxonomy' => 'faqs_cats',
			'orderby' => 'name',
			'show_count' => 0,
			'pad_counts' => 0,
			'hierarchical' => 0,
			'title_li' => ''
		);
		?>
        <ul id="faqs-cats" class="clearfix">
        	<li><a class="active" href="#all" rel="all" title="<?php _e('All FAQs', 'office'); ?>"><?php _e('All', 'office'); ?></a></li>
			<?php
            foreach ($cats as $cat ) : ?>
            <li><a href="#<?php echo $cat->slug; ?>" rel="<?php echo $cat->slug; ?>"><span><?php echo $cat->name; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
        <!-- /faqs-cats -->
    <?php } ?>

    <div id="faqs-wrap clearfix">
    
    	<ul class="faqs-content">
		<?php
        global $post;
        $args = array(
            'post_type' =>'faqs',
            'numberposts' => '-1',
            'order' => 'ASC'
        );
        $faqs = get_posts($args);
        ?>
        
        <?php
		$count=0;
        foreach($faqs as $post) : setup_postdata($post);
		$count++;
		//get terms
		$terms = get_the_terms( get_the_ID(), 'faqs_cats' );
        ?>
        <li data-id="id-<?php echo $count; ?>" data-type="<?php if($terms) { foreach ($terms as $term) { echo $term->slug .' '; } } else { echo 'none'; } ?>" class="faqs-container">       
            
            <div class="faq-item">
                <h2 class="faq-title"><span><?php the_title(); ?></span></h2>
                <div class="faq-content entry">
                    <?php the_content(); ?>
                </div>
                <!-- /faq -->
            </div>
            <!-- /faq-item -->
            
        </li>
        <!-- /faqs-container -->
        <?php endforeach; wp_reset_postdata(); ?>
        
        </ul>
        <!-- /faqs-content -->
    
	</div>
	<!-- /faqs-wrap -->
    
</div>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>	  

<?php get_footer(); ?>