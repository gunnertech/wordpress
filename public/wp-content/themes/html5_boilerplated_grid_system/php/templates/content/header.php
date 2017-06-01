<?php 

global $wp_query;

$default_page_header_image_src = str_replace("null","",get_option("default_page_header_image_src",""));
if(WP_ENV == 'production' && $default_page_header_image_src && strpos($default_page_header_image_src,"http://") === FALSE) {
  $default_page_header_image_src = 'http://dhwlijwe9jil7.cloudfront.net' . $default_page_header_image_src;
}
$hbgs_meta = hbgs_get_meta(true);

if(WP_ENV == 'production' && isset($hbgs_meta['header_url']) && strpos($hbgs_meta['header_url'],"http://") === FALSE) {
  $hbgs_meta['header_url'] = 'http://dhwlijwe9jil7.cloudfront.net'.$hbgs_meta['header_url'];
}

if('http://dhwlijwe9jil7.cloudfront.net' == $default_page_header_image_src) {
  $default_page_header_image_src = null;
}

if('http://dhwlijwe9jil7.cloudfront.net' == $hbgs_meta['header_url']) {
  $hbgs_meta['header_url'] = null;
}




?>
<?php if(!is_front_page() || (is_front_page() && is_home())): ?>
  <hgroup class="<?php echo array_key_exists('icon_url',$hbgs_meta) ? 'grid_24 omega alpha with_thumb clearfix ir' : isset($default_page_header_image_src) && $default_page_header_image_src != 'http://dhwlijwe9jil7.cloudfront.net' ? 'grid_24 omega alpha with_thumb clearfix default_page_header' : 'default_page_header' ?>"
    <?php if(is_singular() && isset($hbgs_meta['header_url'])): ?>
      style="<?php if($hbgs_meta['show_header_text'] != 'on'): ?>text-indent:-999em; <?php endif; ?>background-image:url(<?php echo $hbgs_meta['header_url'] ?>); height:<?php echo $hbgs_meta['header_height'] ?>px;"
    <?php elseif($default_page_header_image_src && $default_page_header_image_src != 'http://dhwlijwe9jil7.cloudfront.net'): ?>
      style="background-image:url(<?php echo $default_page_header_image_src ?>); height:<?php echo get_option('default_page_header_image_height',HEADER_IMAGE_HEIGHT) ?>px;"
    <?php endif; ?>
  >
    <?php if(is_author()): ?>
      <h1><?php echo get_the_author() ?></h1>
    <?php elseif(!is_singular() && isset($wp_query->query_vars["wpsc_product_category"])): ?>
      <h2><?php echo hbgs_get_the_category_name() ?></h2>
    <?php elseif(is_404()): ?>
      <h1>404 - Page Not Found <span style="transform: rotate(90deg); display:inline-block; color: #bbb;">:(</span></h1>
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