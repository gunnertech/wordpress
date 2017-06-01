<?php
/******************************************
/* Shortcodes
******************************************/
/**** Clean UP SHORTCODES ****///Clean Up WordPress Shortcode Formatting - important for nested shortcodes
//adjusted from http://donalmacarthur.com/articles/cleaning-up-wordpress-shortcode-formatting/
function parse_shortcode_content( $content ) {

   /* Parse nested shortcodes and add formatting. */
    $content = trim( do_shortcode( shortcode_unautop( $content ) ) );

    /* Remove '' from the start of the string. */
    if ( substr( $content, 0, 4 ) == '' )
        $content = substr( $content, 4 );

    /* Remove '' from the end of the string. */
    if ( substr( $content, -3, 3 ) == '' )
        $content = substr( $content, 0, -3 );

    /* Remove any instances of ''. */
    $content = str_replace( array( '<p></p>' ), '', $content );
    $content = str_replace( array( '<p>  </p>' ), '', $content );

    return $content;
}

//move wpautop filter to AFTER shortcode is processed
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 99);
add_filter( 'the_content', 'shortcode_unautop',100 );


/*-----------------------------------------------------------------------------------*/
/*	Google Maps Shortcode
/*-----------------------------------------------------------------------------------*/
function google_maps_shortcode($atts, $content = null) {
   extract(shortcode_atts(array(
      "width" => '640',
      "height" => '480',
      "src" => ''
   ), $atts));
   return '<div class="google-map"><iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&amp;output=embed"></iframe></div>';
}
add_shortcode("googlemap", "google_maps_shortcode");


/*-----------------------------------------------------------------------------------*/
/*	Colored Buttons
/*-----------------------------------------------------------------------------------*/
function button_shortcode( $atts, $content = null )
{
	extract( shortcode_atts( array(
      'color' => 'default',
	  'url' => '',
	  'text' => '',
	  'target' => 'self'
      ), $atts ) );
	  if($url) {
		return '<a href="' . $url . '" class="button ' . $color . '" target="_'.$target.'"><span>' . $text . $content . '</span></a>';
	  } else {
		return '<div class="button ' . $color . '"><span>' . $text . $content . '</span></div>';
	}
}
add_shortcode('button', 'button_shortcode');


/*-----------------------------------------------------------------------------------*/
/*	Lists
/*-----------------------------------------------------------------------------------*/
function list_shortcode( $atts, $content = null )
{
	extract(
	shortcode_atts( array(
      'type' => ''
      ),
	  $atts ) );
		return '<div class="' . $type . '">' . $content . '</div>';
}
add_shortcode('list', 'list_shortcode');


/*-----------------------------------------------------------------------------------*/
/*	Icon Button
/*-----------------------------------------------------------------------------------*/
function icon_button_shortcode( $atts, $content = null ){
	
	extract(shortcode_atts(array(
		'url' => '',
		'type' => '',
		'target' => 'self'
	), $atts ));
	
	if($url) {
		return '<a href="'.$url.'" title="'.$content.'" target="_'.$target.'" class="gh-button icon '.$type.'">'.$content.'</a>';
	}
}

add_shortcode('icon_button','icon_button_shortcode');




/*-----------------------------------------------------------------------------------*/
/*	Clear
/*-----------------------------------------------------------------------------------*/
function clear_shortcode() {
   return '<div class="clear"></div>';
}

add_shortcode( 'clear', 'clear_shortcode' );


/*-----------------------------------------------------------------------------------*/
/*	BR
/*-----------------------------------------------------------------------------------*/
function br_shortcode( ) {
   return '<br />';
}
add_shortcode( 'br', 'br_shortcode' );



/*-----------------------------------------------------------------------------------*/
/*	HR
/*-----------------------------------------------------------------------------------*/
function hr_shortcode( $atts, $content = null ){
	
	extract(shortcode_atts(array(
		'style' => '',
		'margin_top' => '',
		'margin_bottom' => ''
	), $atts ));
	
   return '<div class="clear"></div><hr class="'.$style.'" style="margin-top: '.$margin_top.'px; margin-bottom:'.$margin_bottom.'px;" />';
   
}
add_shortcode( 'hr', 'hr_shortcode' );


/*-----------------------------------------------------------------------------------*/
/*	Testimonial
/*-----------------------------------------------------------------------------------*/
function testimonial_shortcode( $atts, $content = null  ) {
	
	extract( shortcode_atts( array(
		'id' => ''
      ), $atts ) );
	  
	$post_id = get_post($id); 
	$title = $post_id->post_title;
	$content = $post_id->post_content;
	
	$testimonial_content .= '<div class="testimonial-item"><div class="testimonial">';
	$testimonial_content .= $content;
    $testimonial_content .= '</div><div class="testimonial-author">';
	$testimonial_content .= $title .'</div></div>';
		
	return $testimonial_content;
}

add_shortcode( 'testimonial', 'testimonial_shortcode' );


/*-----------------------------------------------------------------------------------*/
/*	Togggle
/*-----------------------------------------------------------------------------------*/

function toggle_shortcode( $atts, $content = null )
{
	extract( shortcode_atts(
	array(
      'title' => 'Click To Open'
      ),
	  $atts ) );
		return '<div class="toggle-wrap"><h3 class="trigger"><a href="#">'. $title .'</a></h3><div class="toggle_container">' . do_shortcode($content) . '</div></div>';
}
add_shortcode('toggle', 'toggle_shortcode');


/*-----------------------------------------------------------------------------------*/
/*	Accordion
/*-----------------------------------------------------------------------------------*/


/*main*/
function accordion_shortcode( $atts, $content = null  ) {
   return '<div class="accordion">' . do_shortcode($content) . '</div>';
}

add_shortcode( 'accordion', 'accordion_shortcode' );


/*section*/
function accordion_section_shortcode( $atts, $content = null  ) {
	
	extract( shortcode_atts( array(
      'title' => 'Title',
	), $atts ) );
	  
   return '<h3><a href="#">'. $title .'</a></h3><div>' . do_shortcode($content) . '</div>';
}

add_shortcode( 'accordion_section', 'accordion_section_shortcode' );


/*-----------------------------------------------------------------------------------*/
/*	Tabs
/*-----------------------------------------------------------------------------------*/

function tabgroup_shortcode( $atts, $content = null ){
	
	extract(shortcode_atts(
	array(
      	'set' => '1',
	 	'titles' => '',
		'margin' => '0'
      ),
	  $atts ) );
	  
	  $tabgroup_titles = esc_attr($titles);
	  $tabgroub_title = explode(",", $tabgroup_titles);
	  
	  	$tabgroup_content = '';
		
		$tabgroup_content .='<div class="tabs tab-shortcode" style="margin: '.$margin.'px 0;"><ul class="clearfix ">';

		$count = 1;
		for( $i = 0; $i <= count($tabgroub_title)-1; $i++) {
			$tabgroup_content .= '<li><a href="#tabs-'. $set .'-'.$count++.'">'.trim($tabgroub_title[$i]).'</a></li>';
		}
		
		$tabgroup_content .='</ul>'. do_shortcode($content);
			
		$tabgroup_content .='</div>';
		
		return $tabgroup_content;
}
add_shortcode('tabgroup', 'tabgroup_shortcode');


function single_tab( $atts, $content = null ){
	extract(shortcode_atts(
	array(
      'set' => '1',
	  'position' =>''
      ),
	  $atts ) );
	  
	    $tab_content = '';
		$tab_content .='<div id="tabs-'. $set .'-'. $position .'" class="tab_content">'. do_shortcode($content) .'</div>';
		
		return $tab_content;
}
add_shortcode( 'tab', 'single_tab' );



/*-----------------------------------------------------------------------------------*/
/*	Alerts
/*-----------------------------------------------------------------------------------*/
function alert_shortcode( $atts, $content = null )
{
	extract( shortcode_atts( array(
		'color' => '',
		'align' =>'center',
		'title' => ''
      ), $atts ) );
	  
	  $alert_content = '';
	  $alert_content .= '<div class="alert-' . $color . ' align'.$align.'">';
	  	if($title) {
			$alert_content .='<h2 class="alert-title">'.$title.'</h2>';
		}
	  $alert_content .= ' '.do_shortcode($content) .'</div>';

      return $alert_content;

}
add_shortcode('alert', 'alert_shortcode');


/*-----------------------------------------------------------------------------------*/
/*	Pricing Tables
/*-----------------------------------------------------------------------------------*/

/*main*/
function pricing_table_shortcode( $atts, $content = null  ) {
   return '<ul class="pricing-table clearfix">' . do_shortcode($content) . '</ul><div class="clear"></div>';
}

add_shortcode( 'pricing_table', 'pricing_table_shortcode' );


/*section*/
function pricing_shortcode( $atts, $content = null  ) {
	
	extract( shortcode_atts( array(
		'column' => '3',
		'featured' => '',
		'title' => 'Title',
		'price' => '',
		'per' => '',
		'button_url' => '',
		'button_text' => 'Buy'
	), $atts ) );
	
	
	//set variables
	if($column == '3') {
		$column_size = 'third';
	}
	if($column =='4') {
		$column_size = 'fourth';
	}
	if($column =='5') {
		$column_size = 'fifth';
	}
	
	if($featured =='yes') {
		$featured_pricing = 'featured-pricing';
		$pricing_button_color = 'orange';
	}
	if($featured =='' || $featured =='no') {
		$featured_pricing = NULL;
		$pricing_button_color = 'gray';
	}
	
	//start content  
	$pricing_content ='';
	$pricing_content .= '<li class="pricing pricing-'. $column_size .' '. $featured .' '. $featured_pricing .'">';
	$pricing_content .= '<div class="pricing-header">';
	$pricing_content .= '<h4>'. $title. '</h4>';
	$pricing_content .= '<div class="price">'. $price .'</div>';
	$pricing_content .= '</div>';
	$pricing_content .= '<div class="pricing-content">';
	$pricing_content .= ''. $content. '';
	$pricing_content .= '</div>';
	if($button_text) {
		$pricing_content .= '<div class="pricing-button"><a href="'. $button_url .'" class="button '. $pricing_button_color .'"><span>'. $button_text .'</span></a></div>';
	}
	$pricing_content .= '</li>';
	  
   return $pricing_content;
}

add_shortcode( 'pricing', 'pricing_shortcode' );



/*-----------------------------------------------------------------------------------*/
/*	Columns
/*-----------------------------------------------------------------------------------*/
function column_shortcode( $atts, $content = null )
{
	extract( shortcode_atts( array(
	  'offset' =>'',
      'size' => '',
	  'position' =>''
      ), $atts ) );


	  if($offset !='') { $column_offset = $offset; } else { $column_offset ='one'; }
		
      return '<div class="'.$column_offset.'-' . $size . ' column-'.$position.'">' . do_shortcode($content) . '</div>';

}
add_shortcode('column', 'column_shortcode');


/*-----------------------------------------------------------------------------------*/
/*	Shortcode filters - alow shortcodes in widgets
/*-----------------------------------------------------------------------------------*/
add_filter('the_content', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');
?>