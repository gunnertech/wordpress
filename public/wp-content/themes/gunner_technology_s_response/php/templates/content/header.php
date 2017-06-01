<?php 

global $wp_query;

$default_page_header_image_src = str_replace("null","",get_option("default_page_header_image_src")); 
$hbgs_meta = hbgs_get_meta(true);

?>
<?php if(!is_front_page() || (is_front_page() && is_home())): ?>
  <hgroup class="<?php echo array_key_exists('icon_url',$hbgs_meta) ? 'with_thumb' : isset($default_page_header_image_src) ? 'with_thumb default_page_header' : 'default_page_header' ?>"
    <?php if(is_singular() && isset($hbgs_meta['header_url'])): ?>
      style="<?php if($hbgs_meta['show_header_text'] != 'on'): ?>text-indent:-999em; <?php endif; ?>background-image:url(<?php echo $hbgs_meta['header_url'] ?>); height:<?php echo $hbgs_meta['header_height'] ?>px;"
    <?php elseif($default_page_header_image_src): ?>
      style="background-image:url(<?php echo $default_page_header_image_src ?>); height:<?php echo get_option('default_page_header_image_height',HEADER_IMAGE_HEIGHT) ?>px;"
    <?php endif; ?>
  >
    <?php if(is_author()): ?>
      <h1><?php echo get_the_author() ?></h1>
    <?php elseif(is_attachment()): ?>
      <h2><a href="<?php the_permalink($post->post_parent) ?>"><?php echo do_shortcode(get_the_title($post->post_parent)) ?></a></h2>
    <?php elseif(is_home() && isset($wp_query->queried_object)): ?>
      <?php echo is_front_page() ? '<h2>' : '<h1>'; ?>
        <?php echo do_shortcode($wp_query->queried_object->post_title) ?>
      <?php echo is_front_page() ? '</h2>' : '</h1>'; ?>
    <?php elseif(is_search()): ?>
      <h1>Search Results</h1>
    <?php elseif(is_page()): ?>
      <h1><?php the_title() ?></h1>
    <?php elseif(is_category()): ?>
      <h1><?php single_cat_title() ?></h1>
    <?php else: ?>
      <h2><?php hbgs_category_title_with_link(); ?></h2>
    <?php endif; ?>
  </hgroup>
<?php endif; ?>