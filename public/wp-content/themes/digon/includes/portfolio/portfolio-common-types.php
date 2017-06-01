<?php
$portfolio_column_count=1;
$columns=4;
$portfolio_perpage=8;
?>
<?php $term = get_queried_object(); ?>

<h1 class="entry-title"><?php echo $term->name; ?></h1>

<div class="fullpage-contents-wrap">
	<div class="page-container">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php
//list terms in a given taxonomy
$taxonomy = 'types';
$tax_terms = get_terms($taxonomy);
?>
		
<div class="portfolio-taxonomies-browse">
<ul>
<?php
foreach ($tax_terms as $tax_term) {
echo '<li>' . '<a href="' . esc_attr(get_term_link($tax_term, $taxonomy)) . '" title="' . sprintf( __( "View portfolios in %s",'mthemelocal' ), $tax_term->name ) . '" ' . '>' . $tax_term->name.'</a></li>';
}
?>
</ul>
</div>	

<?php
if ( post_password_required() ) {
	
	echo '<div id="password-protected">';

	if (DEMO_STATUS) { echo '<p><h2>DEMO Password is 1234</h2></p>'; }
	echo get_the_password_form();
	echo '</div>';
	
	} else {
?>				
<div class="portfoliofilter-columns-wrap clearfix">
	<ul class="portfolio-four">
		<?php
		query_posts( array( 'post_type' => 'mtheme_portfolio', 'orderby' => 'menu_order', 'order' => 'ASC', 'types' => $term->slug , 'posts_per_page' => $portfolio_perpage, 'paged' => $paged) );
		if (have_posts()) : while (have_posts()) : the_post();
		$custom = get_post_custom(get_the_ID());
		$portfolio_cats = get_the_terms( get_the_ID(), 'types' );
		$video_url="";
		$thumbnail="";
		$link_url="";
		if ( isset($custom["video"][0]) ) { $video_url=$custom["video"][0]; }
		if ( isset($custom["thumbnail"][0]) ) { $thumbnail=$custom["thumbnail"][0]; }
		if ( isset($custom["custom_link"][0]) ) { $link_url=$custom["custom_link"][0]; }
		$portfolio_thumb_header=$custom["portfolio_page_header"][0];
if ($portfolio_column_count>$columns) $portfolio_column_count=1;

?>
<li class="portfolio-col-<?php echo $portfolio_column_count++; ?>">
			
			<?php
			if ( $custom["video"][0]<>"" ) {
				$p_class="fadethumbnail-play";
			} elseif ( $custom["custom_link"][0]<>"" ) {
				$p_class="fadethumbnail-link";
			} else {
				$p_class="fadethumbnail-view";
			}
			?>
				<?php
				if ( $custom["video"][0]<>"" ) {
					echo activate_lightbox (
						$lightbox_type="prettyPhoto",
						$ID=$post->ID,
						$link=$video_url,
						$mediatype="video",
						$title=$post->post_title,
						$class="portfolio-image-link portfolio-columns",
						$navigation="prettyPhoto[portfolio]"
						);
					echo '<span class="column-portfolio-icon portfolio-video-icon"></span>';
					} elseif ( $custom["custom_link"][0]<>"" ) {
						echo '<a class="portfolio-image-link portfolio-columns" title="'.get_the_title().'" href="'.$custom["custom_link"][0].'" >';
						echo '<span class="column-portfolio-icon portfolio-link-icon"></span>';

					} else {
						echo activate_lightbox (
							$lightbox_type="prettyPhoto",
							$ID=$post->ID,
							$link=featured_image_link($post->ID),
							$mediatype="image",
							$title=$post->post_title,
							$class="portfolio-image-link portfolio-columns",
							$navigation="prettyPhoto[portfolio]"
							);
					echo '<span class="column-portfolio-icon portfolio-image-icon"></span>';
				}
				?>
				<?php
				// Show Image
				if ($thumbnail<>"") {
					echo '<img src="'.$thumbnail.'" class="preload-image displayed-image" alt="thumbnail" />';
				} else {
					echo display_post_image (
						$post->ID,
						$have_image_url='',
						$link=false,
						$type="portfolio-small",
						$post->post_title,
						$class="preload-image displayed-image"
					);
				}
				?>		
				</a>
				<div class="work-details">
					<h4><a href="<?php if ($link_url<>"") { echo $link_url; } else { the_permalink(); } ?>" rel="bookmark" title="<?php echo get_the_title(); ?>"><?php the_title(); ?></a></h4>
					<p class="entry-content work-description"><?php echo $custom["description"][0];?></p>
				</div>
		</li>
		<?php endwhile; ?>
		<?php endif;?>
 
	</ul>
</div>
			<?php require ( MTHEME_INCLUDES . '/navigation.php' ); ?>
<?php
}
?>
		</div>
	</div>
</div>