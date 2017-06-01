<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>
<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>   

<?php
//get meta
$portfolio_style = get_post_meta($post->ID, 'office_portfolio_style', TRUE);
$page_slider = get_post_meta($post->ID, 'office_page_slider', true);
$portfolio_video = get_post_meta($post->ID, 'office_portfolio_video', TRUE);
$portfolio_type = get_post_meta($post->ID, 'office_portfolio_type', TRUE);
$portfolio_cost = get_post_meta($post->ID, 'office_portfolio_cost', TRUE);
$portfolio_client = get_post_meta($post->ID, 'office_portfolio_client', TRUE);
$portfolio_url = get_post_meta($post->ID, 'office_portfolio_url', TRUE);
//get terms
$terms = get_the_term_list( get_the_ID(), 'portfolio_cats' );
?>

<?php
if ($page_slider == 'enable') {
	get_template_part( 'includes/page-slides'); //show slider
}
?>

<?php
/*-----------------------------------------------------------------------------------*/
// Full-Width Portfolio Style 
/*-----------------------------------------------------------------------------------*/
?>

<div id="page-heading">
	<h1><?php the_title(); ?></h1>
    <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?>
</div>
<!-- /page-heading -->

<div class="post full-width clearfix">

<?php if($portfolio_style == 'full') { ?>

<div id="single-portfolio" class="full-portfolio clearfix">

		<div id="single-portfolio-left">
            <div id="single-portfolio-meta" class="clearfix">
				<ul>
                    <li><span><?php _e('Date','office'); ?>:</span><?php the_date('M Y'); ?></li>
                    <?php if($terms) { ?><li><span><?php _e('Labeled','office'); ?>:</span><?php echo get_the_term_list( get_the_ID(), 'portfolio_cats', '',', ',' ') ?></li><?php } ?>    
                    <?php if(!empty($portfolio_cost)) {?><li><span><?php _e('Cost','office'); ?>:</span><?php echo $portfolio_cost; ?></li><?php } ?>
                    <?php if(!empty($portfolio_client)) {?><li><span><?php _e('Client','office'); ?>:</span><?php echo $portfolio_client; ?></li><?php } ?>
                    <?php if(!empty($portfolio_url)) {?><li><span><?php _e('Website','office'); ?>:</span><a href="<?php echo $portfolio_url; ?>" title="<?php _e('Visit Website','office'); ?>"><?php echo $portfolio_url; ?></a></li><?php } ?>
            	</ul>
            </div>
            <!-- /single-portfolio-meta -->
         
            
        </div>
        <!-- /single--portfolio-left -->
        

        <div id="full-portfolio-content" class="clearfix">
			<?php the_content(); ?>
        </div>
        <!-- /full-portfolio-content -->
  
</div>   
<!-- /single-portfolio -->

<?php } else { ?>

<?php
/*-----------------------------------------------------------------------------------*/
// Default Portfolio Style 
/*-----------------------------------------------------------------------------------*/
?>

<div id="single-portfolio" class="clearfix">

		<div id="single-portfolio-left">
			<?php the_content(); ?>
            <div class="clear"></div>
            
           
            <div id="single-portfolio-meta" class="clearfix">
				<ul>
                    <li><span><?php _e('Date','office'); ?>:</span><?php the_date('M Y'); ?></li>
					<?php if($terms) { ?><li><span><?php _e('Labeled','office'); ?>:</span><?php echo get_the_term_list( get_the_ID(), 'portfolio_cats', '', ', ', ' ') ?></li><?php } ?>  
                    <?php if(!empty($portfolio_cost)) {?><li><span><?php _e('Cost','office'); ?>:</span><?php echo $portfolio_cost; ?></li><?php } ?>
                    <?php if(!empty($portfolio_client)) {?><li><span><?php _e('Client','office'); ?>:</span><?php echo $portfolio_client; ?></li><?php } ?>
                    <?php if(!empty($portfolio_url)) {?><li><span><?php _e('Website','office'); ?>:</span><a href="<?php echo $portfolio_url; ?>"><?php echo $portfolio_url; ?></a></li><?php } ?>
            	</ul>
            </div>
            <!-- /single-portfolio-meta -->  
            
        </div>
        <!-- /single-portfolio-left -->
        

        <div id="single-portfolio-right">
        
        <?php if(!empty($portfolio_video)) { echo '<div class="portfolio-video fitvids">'.do_shortcode($portfolio_video).'</div>'; } else { ?>
        
		<?php
        //get attachement count
		$get_attachments = get_children( array( 'post_parent' => $post->ID ) );
		$attachments_count = count( $get_attachments );
		
		if($attachments_count == '0') {
		//show only the 1 single image
		$portfolio_single = wp_get_attachment_image_src(get_post_thumbnail_id(), 'portfolio-single');
		$portfolio_single_full = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full-size');
		?>
        
        <a href="<?php echo $portfolio_single_full[0]; ?>" title="<?php the_title(); ?>" class="prettyphoto-link"><img src="<?php echo $portfolio_single[0]; ?>" height="<?php echo $portfolio_single[2]; ?>" width="<?php echo $portfolio_single[1]; ?>" alt="<?php the_title(); ?>" /></a>
		
        <?php } else {
			//show image slider
			?>
        <div  id="portfolio-slides-wrap">
            <div id="portfolio-slides" class="flexslider clearfix">
                <ul class="slides">
                        <?php
                        
                        //attachement loop
                        $args = array(
                            'orderby' => 'menu_order',
                            'post_type' => 'attachment',
                            'post_parent' => get_the_ID(),
                            'post_mime_type' => 'image',
                            'post_status' => null,
                            'posts_per_page' => -1
                        );
                        $attachments = get_posts($args);
                        
                        //start loop
                        foreach ($attachments as $attachment) :
                        
                        //get images
                        $full_img = wp_get_attachment_image_src( $attachment->ID, 'full-size');
                        $portfolio_single = wp_get_attachment_image_src( $attachment->ID, 'portfolio-single');
                        ?>
                            <li class="slide">
                                <a href="<?php echo $full_img[0]; ?>" title="<?php echo apply_filters('the_title', $attachment->post_title); ?>" <?php if($attachments_count =='1') { echo 'class="prettyphoto-link"'; } else { echo 'rel="prettyPhoto[gallery]"'; } ?>><img src="<?php echo $portfolio_single[0]; ?>" height="<?php echo $portfolio_single[2]; ?>" width="<?php echo $portfolio_single[1]; ?>" alt="<?php echo apply_filters('the_title', $attachment->post_title); ?>" /></a>
                            </li>
                            <!-- /slide -->
                        <?php endforeach;?>
                        </ul>
                        <!-- /slides -->
                    </div>
                    <!-- /portfolio-slides -->
                </div>
                <!-- /portfolio-slides-wrap -->
            <?php } ?>
            <?php } ?>
        </div>
        <!-- /single-portfolio-right -->
  
    </div>   
    <!-- /single-portfolio -->
    
    <?php } ?>
    
    
<?php
/*-----------------------------------------------------------------------------------*/
// Related posts if not disabled 
/*-----------------------------------------------------------------------------------*/

    if($data['disable_related_port'] !='disable') {
    //get related portfolio posts
    $cats = wp_get_post_terms($post->ID, 'portfolio_cats');
    if ($cats) {  ?>
    <div id="single-portfolio-related" class="clearfix">
    
        <div class="heading">
            <h2><span><?php _e('Related Items','office'); ?></span></h2>
            <a href="#"><?php _e('View all','office'); ?> &rarr;</a>
        </div>
        <!-- /heading -->
      <?php
		$args = array(
			'post__not_in' => array( $post->ID ),
			'orderby'=> 'post_date',
			'order' => 'rand',
			'post_type' => 'portfolio',
			'posts_per_page' => 4,
			'tax_query' => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'portfolio_cats',
					'terms' => $cats[0]->term_id
				),
			)
		);
		$my_query = new WP_Query($args);
		if( $my_query->have_posts() ) {
		$count=0;
		while ($my_query->have_posts()) : $my_query->the_post();
		$count++;
		//get portfolio thumbnail
		$thumbail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'grid-thumb');
	?>
	<?php if ( has_post_thumbnail() ) {  ?>
	<div class="portfolio-item <?php if($count == '4') { echo 'no-margin'; } ?>">
		<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo $thumbail[0]; ?>" height="<?php echo $thumbail[2]; ?>" width="<?php echo $thumbail[1]; ?>" alt="<?php echo the_title(); ?>" /></a>
		<h3><?php the_title(); ?></h3>
	</div>
	<!-- /portfolio-item -->
	<?php } ?>
	<?php if($count == '4'){ $count=0; } ?>
	<?php endwhile; wp_reset_query(); } ?>
    </div>
    <!-- /single-portfolio-related -->
    <?php } ?>
    <?php } ?>
    
</div>
<!-- /.post -->

<div id="single-portfolio-nav" class="clearfix"> 
        <div class="one-half"><?php next_post_link('%link', '&larr; %title', false); ?></div>
        <div class="one-half column-last"><?php previous_post_link('%link', '%title &rarr;', false); ?></div>
</div>
<!-- /single-portfolio-nav -->
        
        
<?php endwhile; ?>
<?php endif; ?>	
<?php get_footer(); ?>