<?php
//for in the loop, display all "content", regardless of post_type,
//that have the same custom taxonomy (e.g. genre) terms as the current post
$portfolio_post_ID=$post->ID;
//$backup = $post;  // backup the current object
$found_none = '';
$portfolio_column_count=1;
$columns=4;
$taxonomy = 'types';//  e.g. post_tag, category, custom taxonomy
$param_type = 'types'; //  e.g. tag__in, category__in, but genre__in will NOT work
$post_types = get_post_types( array('public' => true), 'names' );
$tax_args=array('orderby' => 'none');

$tags = wp_get_post_terms( $post->ID , $taxonomy, $tax_args);

$first_tag="";
$second_tag="";
$third_tag="";

if (isset($tags[0]->term_id)) $first_tag 	= $tags[0]->term_id;
if (isset($tags[1]->term_id)) $second_tag = $tags[1]->term_id;
if (isset($tags[2]->term_id)) $third_tag 	= $tags[2]->term_id;

?>

<?php
if ($tags) {
  foreach ($tags as $tag) {
	//echo $tag->slug;


	$args = array(
		'post_type' => get_post_type($post->ID),
		'post__not_in' => array($portfolio_post_ID),
		'posts_per_page' => 4,
		'tax_query' => array(
			'relation' => 'OR',
			array(
				'taxonomy' => $taxonomy,
				'terms' => $second_tag,
				'field' => 'id',
				'operator' => 'IN',
			),
			array(
				'taxonomy' => $taxonomy,
				'terms' => $first_tag,
				'field' => 'id',
				'operator' => 'IN',
			),
			array(
				'taxonomy' => $taxonomy,
				'terms' => $third_tag,
				'field' => 'id',
				'operator' => 'IN',
			)
		)
	);
    $my_query = null;
    $my_query = new WP_Query($args);

    if( $my_query->have_posts() ) {
		?>
		<h3 class="related_posts_title"><?php _e('Related Projects','mthemelocal'); ?></h3>

		<div class="portfolio-related-wrap clearfix">
			<ul class="portfolio-four">
		
		<?php
		while ($my_query->have_posts()) : $my_query->the_post();

		if ($portfolio_column_count>$columns) $portfolio_column_count=1;
		$custom = get_post_custom($post->ID);
		?>
		<li class="portfolio-col-<?php echo $portfolio_column_count++; ?>">
				<a class="portfolio-image-link portfolio-columns" href="<?php the_permalink(); ?>">
			<?php
			echo '<span class="column-portfolio-icon portfolio-image-icon"></span>';
				echo display_post_image (
					$post->ID,
					$have_image_url=false,
					$link=false,
					$type="portfolio-small",
					$post->post_title,
					$class="portfolio-related-image" 
				);
				
			?>
			</a>
			<div class="work-details">
				<h4><a href="<?php if ($link_url<>"") { echo $link_url; } else { the_permalink(); } ?>" rel="bookmark" title="<?php echo get_the_title(); ?>"><?php the_title(); ?></a></h4>
				<p class="entry-content work-description"><?php echo $custom["description"][0];?></p>
			</div>
			</li>

			<?php $found_none = '';
		endwhile;
    }
?>
	</ul>
</div>
<?php	
	break;

  }
}
wp_reset_query();
?>