<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>
<?php get_header(); ?>

<?php if(have_posts()) : ?>

<div id="page-heading">
	<?php $term = $wp_query->queried_object; ?>
	<h1><?php _e('FAQs', 'office'); ?>: <?php echo $term->name; ?></h1>
    <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?> 
</div>
<!-- /page-heading -->

<?php
$category_description = category_description();
if(!empty($category_description )) {
	echo apply_filters('category_archive_meta','<div id="faqs-description">' . $category_description . '</div>');
}
?>

<div class="post full-width clearfix">
    
    <div id="faqs-wrap" class="clearfix">
        
        <?php
		global $query_string;
		query_posts( $query_string . '&order=ASC' );
        //start loop
        while (have_posts()) : the_post();   
        ?>
        
         <div class="faq-item">
            <h2 class="faq-title"><span><?php the_title(); ?></span></h2>
            <div class="faq-content entry">
                <?php the_content(); ?>
            </div>
            <!-- /faq -->
        </div>
        <!-- /faq-item -->
        
        <?php endwhile; ?>
    
    </div>
    <!-- /faqs-wrap -->

</div>
<!-- .post -->

<?php endif; ?>
<?php get_footer(); ?>