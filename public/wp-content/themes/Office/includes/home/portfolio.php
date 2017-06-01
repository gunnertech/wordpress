<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
 
 global $data; //get theme options
?>


<?php
//get post type ==> portfolio
	global $post;
	$args = array(
		'post_type' =>'portfolio',
		'numberposts' => $data['home_portfolio_count']
	);
	$portfolio_posts = get_posts($args);
?>
<?php if($portfolio_posts) { ?>        


<div id="home-projects" class="home-projects-carousel clearfix">

	<?php if(!empty($data['home_portfolio_title'])) { ?>
        <div class="heading">
            <?php if(!empty($data['home_portfolio_title_url'])) { ?>
                <h2><a href="<?php echo $data['home_portfolio_title_url']; ?>" title="<?php echo $data['home_portfolio_title']; ?>"><span><?php echo $data['home_portfolio_title']; ?></span></a></h2>
            <?php } else { ?>
                <h2><span><?php echo $data['home_portfolio_title']; ?></span></h2>
            <?php } ?>
        </div>
        <!-- /heading -->
    <?php } ?>

	<div id="home-portfolio-carousel-wrp" class="clearfix">
    	<div id="home-portfolio-carousel" class="fredcarousel">
		<?php
        $count=0;
        foreach($portfolio_posts as $post) : setup_postdata($post);
        $count++;
        //get portfolio thumbnail
        $thumbail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'grid-thumb');
        ?>
        
        <?php if ( has_post_thumbnail() ) {  ?>
        <div class="portfolio-item">
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo $thumbail[0]; ?>" height="<?php echo $thumbail[2]; ?>" width="<?php echo $thumbail[1]; ?>" alt="<?php echo the_title(); ?>" /></a>
            <h3><?php the_title(); ?></h3>
        </div>
        <!-- /portfolio-item -->
        <?php } ?>
        
        <?php endforeach; ?>
        </div>
        <!-- /home-portfolio-carousel -->
        <div id="carousel-prev"></div>
        <div id="carousel-next"></div>
        <div id="carousel-pagination" class="pagination"></div>
    </div>
    <!-- /home-portfolio-carousel-wrp -->


</div>
<!-- /home-projects -->      	
<?php } ?>