<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
 
 global $data; //fetch options stored in $data
?>


<?php
if($data['home_static_page'] !='Select a page:') {
	$home_static_page = $data['home_static_page'];
	$page = get_page_by_path($home_static_page);
	$page_id = $page->ID;
?>

	<?php if(!empty($data['home_static_page_title'])) { ?>
	<div class="heading">
    	<?php if(!empty($data['home_static_page_title_url'])) { ?>
			<h2><a href="<?php echo $data['home_static_page_title_url']; ?>" title="<?php echo $data['home_static_page_title']; ?>"><span><?php echo $data['home_static_page_title']; ?></span></a></h2>
        <?php } else { ?>
        	<h2><span><?php echo $data['home_static_page_title']; ?></span></h2>
        <?php } ?>
	</div>
	<!-- /heading -->
    <?php } ?>
    
<div id="home-static-page">
	<?php
    $content_post = get_post($page_id);
    $content = $content_post->post_content;
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]>', $content);
    echo $content;
    ?>  
</div>
<!-- /home-static-page -->
<?php } wp_reset_postdata(); ?>
