<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
 
 global $data; //get theme options
?>


<?php
//get post type ==> hp highlights
global $post;
$args = array(
	'post_type' =>'hp_highlights',
	'numberposts' => '-1'
);
$hp_highlight_posts = get_posts($args);
?>
<?php if($hp_highlight_posts) { ?>        


<div id="home-highlights" class="clearfix">
	
    <?php if(!empty($data['home_highlights_title'])) { ?>
        <div class="heading">
            <?php if(!empty($data['home_highlights_title_url'])) { ?>
                <h2><a href="<?php echo $data['home_highlights_title_url']; ?>" title="<?php echo $data['home_highlights_title']; ?>"><span><?php echo $data['home_highlights_title']; ?></span></a></h2>
            <?php } else { ?>
                <h2><span><?php echo $data['home_highlights_title']; ?></span></h2>
            <?php } ?>
        </div>
        <!-- /heading -->
    <?php } ?>
    
	<?php
	//start loop
	$third_count=0;
	$fifth_count=0;
	foreach($hp_highlight_posts as $post) : setup_postdata($post);
	$third_count++;
	$fifth_count++;
	
	//get featured image ==> full size
	$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full-size');
	
	//set padding for heading with image
	$heading_padding = $featured_image[2] + 15;
	$heading_height = $featured_image[1];
	
	//meta
	$hp_highlights_url = get_post_meta($post->ID, 'office_hp_highlights_url', TRUE);
	?>
	
	<div class="hp-highlight <?php if($third_count=='3') { echo 'highlight-third'; } ?> <?php if($fifth_count=='5') { echo 'highlight-fifth'; } ?>">
		<h3 <?php if(!empty($featured_image)) { echo 'class="heading-with-icon" style="background-image: url('.$featured_image[0].');padding-left: '.$heading_padding.'px;height: '.$heading_height.'px;line-height: '.$heading_height.'px;"'; } ?>><?php if(!empty($hp_highlights_url)) { ?><a href="<?php echo $hp_highlights_url; ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a><?php } else { the_title(); } ?></h3>
		<?php the_content(); ?>
	</div>
    <!-- /hp-highlight -->
	<?php if($third_count=='3') { $third_count=1; } if($fifth_count=='5') { $fifth_count=1; } endforeach; ?>

</div>
<!-- END #home-highlights -->      	
<?php } ?>