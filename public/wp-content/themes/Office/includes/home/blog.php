<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
 
 global $data; //get theme options
?>


<?php
//get post type ==> blog
	global $post;
	$args = array(
		'post_type' =>'post',
		'numberposts' => $data['home_blog_count']
	);
	$blog_posts = get_posts($args);
?>
<?php if($blog_posts) { ?>  

<div id="home-blog" class="clearfix">

	<?php if(!empty($data['home_blog_title'])) { ?>
        <div class="heading">
            <?php if(!empty($data['home_blog_title_url'])) { ?>
                <h2><a href="<?php echo $data['home_blog_title_url']; ?>" title="<?php echo $data['home_blog_title']; ?>"><span><?php echo $data['home_blog_title']; ?></span></a></h2>
            <?php } else { ?>
                <h2><span><?php echo $data['home_blog_title']; ?></span></h2>
            <?php } ?>
        </div>
        <!-- /heading -->
    <?php } ?>
	
	
	<?php
	foreach($blog_posts as $post) : setup_postdata($post);
	//get portfolio thumbnail
	$thumbail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'blog-thumb');
	?>
	
	<div class="home-entry">
    	<?php if($thumbail) { ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo $thumbail[0]; ?>" alt="<?php echo the_title(); ?>" /></a>
        <?php } ?>
		<h3><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
		<?php echo excerpt($data['home_blog_excerpt_length']); ?>
	</div>
	
	<?php endforeach; ?>

</div>
<!-- END #home-blog -->      	
<?php } ?>