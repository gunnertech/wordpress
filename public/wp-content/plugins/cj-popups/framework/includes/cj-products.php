<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
	//$url = 'http://cssjockey.com/api/products.php';
	$url = 'http://api.cssjockey.com/?cj_action=get_products_array';
	$response = wp_remote_post( $url, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => array(),
		'cookies' => array()
	    )
	);
	if ( is_wp_error( $response ) ) {
	   	$error_message = $response->get_error_message();
	   	$errors = $error_message;
	} else {
		$errors = null;
	   	$products = json_decode($response['body']);
	}
?>
<div class="wrap">
	<h2 style="padding:10px; line-height:1; margin:20px 0px 20px 0px; padding:0px;"><?php _e('Other CSSJockey Products', 'cjpopups'); ?></h2>
	<?php
	if(!is_null($errors)){
		echo $errors;
	}else{
		echo '<div class="clearfix" style="padding:0 15px 0 0px;">';
		$count = 0;
		foreach ($products as $key => $value) {
			if(isset($_GET['s']) && $_GET['s'] != ''){
				if($value->search_term == $_GET['s']){
					$count++;
					echo '<div style="margin-bottom:25px; line-height:1.6; clear:both;">';
					echo '<div class="clearfix" style="padding:10px; background: #ffffff; border:1px solid #ddd;">';
					echo '<div style="padding:0 20px 5px 0; width:230px; float:left;"><a href="'.$value->url.'" target="_blank"><img src="'.$value->image.'" width="100%"></a></div>';
					echo '<div style="padding:0 0 5px 0;"><b style="font-size:12pt;">'.$value->title.' </b><br><b> '.$value->category.'</b></div>';
					echo '<div style="padding:0 0 10px 0;">'.$value->description.'</div>';
					echo '<div><a href="'.$value->url.'" target="_blank" class="button-primary">'.__('View Details', 'cjpopups').'</a></div>';
					echo '</div>';
					echo '</div>';
				}
			}else{
				$count++;
				echo '<div style="margin-bottom:25px; line-height:1.6; clear:both;">';
				echo '<div class="clearfix" style="padding:10px; background: #ffffff; border:1px solid #ddd;">';
				echo '<div style="padding:0 20px 5px 0; width:230px; float:left;"><a href="'.$value->url.'" target="_blank"><img src="'.$value->image.'" width="100%"></a></div>';
				echo '<div style="padding:0 0 5px 0;"><b style="font-size:12pt;">'.$value->title.' </b><br><b> '.$value->category.'</b></div>';
				echo '<div style="padding:0 0 10px 0;">'.$value->description.'</div>';
				echo '<div><a href="'.$value->url.'" target="_blank" class="button-primary">'.__('View Details', 'cjpopups').'</a></div>';
				echo '</div>';
				echo '</div>';
			}
		}
		if($count == 0){
			echo sprintf(__('No products or add-ons found matched your query. <a href="%s">View All</a>', 'cjpopups'), admin_url('admin.php?page=cj-products'));
		}
		echo '</div>';
	}
	?>
</div>