<?php

/**
 * Begin homepage slider shorticodes
 *
 */

function coinslides_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'width' => 920,
		'height' => 360,
		'opacity' => .9,
		'delay'	=> 5,
	), $atts));

	//calculate delay to milliseconds
	$delay = $delay * 1000;

	$return_html = '<div id="content_slider">';
	$return_html.= do_shortcode($content);
	$return_html.= '</div>';
	$return_html.= '<script>';
	$return_html.= "$('#content_slider').coinslider({ width: ".$width.", height: ".$height.", opacity: ".$opacity.", navigation: true, titleSpeed: 800 , delay: ".$delay.", sDelay: 10 });";
	$return_html.= '</script>';
	
	return $return_html;
}
add_shortcode('coinslides', 'coinslides_func');


function coinslide_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'href' => '',
		'src' => '',
		'title' => '',
		'style' => '',
	), $atts));

	
	$return_html = '<a href="'.$href.'">';
	$return_html.= '<img src="'.$src.'" alt="'.$title.'"/>';
	
	if(!empty($style) && !empty($content))
	{
		$return_html.= '<span class="'.$style.'">'.$content.'</span>';
	}
	
	$return_html.= '</a>';
	
	return $return_html;
}
add_shortcode('coinslide', 'coinslide_func');


function fadeslides_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'speed' => .8,
		'delay'	=> 5,
	), $atts));

	//calculate delay to milliseconds
	$speed = $speed * 1000;
	$delay = $delay * 1000;

	$return_html = '<div id="content_slider_slide">';
	$return_html.= do_shortcode($content);
	$return_html.= '</div>';
	$return_html.= '<div id="slider_nav"><div class="slider_nav_btn"></div></div>';
	$return_html.= '<script>';
	$return_html.= "$('#content_slider_slide').pSlider({ nav: '#slider_nav', navWrapper: '.slider_nav_btn', fadeSpeed: ".$speed.", delay: ".$delay." });";
	$return_html.= '</script>';
	
	return $return_html;
}
add_shortcode('fadeslides', 'fadeslides_func');


function fadeslide_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'title' => '',
		'thumb' => '',
	), $atts));

	
	$return_html = '<div>';
	$return_html.= '<span class="title">';
	
	if(empty($thumb))
	{
		$return_html.= $title;
	}
	else
	{
		$return_html.= '<img src="'.$thumb.'" alt="'.$title.'"/>';
	}
	
	$return_html.= '</span>';
	$return_html.= do_shortcode($content);
	$return_html.= '</div>';
	
	return $return_html;
}
add_shortcode('fadeslide', 'fadeslide_func');

/**
 * End homepage slider shorticodes
 *
 */



function galleria_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'transition' => 'fade',
	), $atts));

	$images = preg_split('/[\r\n]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
	/*if(isset($images[0]))
	{
		unset($images[0]);
	}*/
	
	$return_html = '<div class="galleria">';
	
	foreach($images as $image)
	{
		$image = strip_tags($image);
	
		if(!empty($image))
		{
			$return_html.= '<img src="'.$image.'" alt=""/>';
		}
	}
	
	$return_html.= '</div>';
	$return_html.= '<script>';
	$return_html.= "Galleria.loadTheme('".get_bloginfo( 'stylesheet_directory' )."/js/classic/galleria.classic.js');";
	$return_html.= '</script>';
	
	return $return_html;
}
add_shortcode('galleria', 'galleria_func');


// [dropcap foo="foo-value"]
function dropcap_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'style' => 1
	), $atts));
	
	//get first char
	$first_char = substr($content, 0, 1);
	$text_len = strlen($content);
	$rest_text = substr($content, 1, $text_len);

	$return_html = '<span class="dropcap'.$style.'">'.$first_char.'</span>';
	$return_html.= do_shortcode($rest_text);
	
	return $return_html;
}
add_shortcode('dropcap', 'dropcap_func');




// [quote foo="foo-value"]
function quote_func($atts, $content) {
	
	$return_html = '<blockquote>'.do_shortcode($content).'</blockquote>';
	
	return $return_html;
}
add_shortcode('quote', 'quote_func');



// [button foo="foo-value"]
function button_func($atts) {

	//extract short code attr
	extract(shortcode_atts(array(
		'text' => 'something',
		'link' => '',
		'size' => '',
		'align' => '',
	), $atts));
	
	if(!empty($size))
	{
		$size.= '_';
	}
	
	$return_html = '<a class="'.$size.'button '.$align.'" href="'.$link.'">';
	$return_html.= '<span>'.$text.'</span>';
	$return_html.= '</a>';
	
	return $return_html;
}
add_shortcode('button', 'button_func');




// [highlight foo="foo-value"]
function highlight_func($atts) {

	//extract short code attr
	extract(shortcode_atts(array(
		'text' => 'something',
		'style' => '1',
	), $atts));
	
	$return_html = '<span class="highlight'.$style.'">'.$text.'</span>';
	
	return $return_html;
}
add_shortcode('highlight', 'highlight_func');




function frame_left_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'src' => '',
		'href' => '',
	), $atts));
	
	$return_html = '<div class="frame_left">';
	
	if(!empty($href))
	{
		$return_html.= '<a href="'.$href.'" class="img_frame">';
	}
	
	$return_html.= '<img src="'.$src.'" alt=""/>';
	
	if(!empty($href))
	{
		$return_html.= '</a>';
	}
	
	if(!empty($content))
	{
		$return_html.= '<span class="caption">'.$content.'</span>';
	}
	
	$return_html.= '</div>';
	
	return $return_html;
}
add_shortcode('frame_left', 'frame_left_func');




function frame_right_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'src' => '',
		'href' => '',
	), $atts));
	
	$return_html = '<div class="frame_right">';
	
	if(!empty($href))
	{
		$return_html.= '<a href="'.$href.'" class="img_frame">';
	}
	
	$return_html.= '<img src="'.$src.'" alt=""/>';
	
	if(!empty($href))
	{
		$return_html.= '</a>';
	}
	
	if(!empty($content))
	{
		$return_html.= '<span class="caption">'.$content.'</span>';
	}
	
	$return_html.= '</div>';
	
	return $return_html;
}
add_shortcode('frame_right', 'frame_right_func');



function frame_center_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'src' => '',
		'href' => '',
	), $atts));
	
	$return_html = '<div class="frame_center">';
	
	if(!empty($href))
	{
		$return_html.= '<a href="'.$href.'" class="img_frame">';
	}
	
	$return_html.= '<img src="'.$src.'" alt=""/>';
	
	if(!empty($href))
	{
		$return_html.= '</a>';
	}
	
	if(!empty($content))
	{
		$return_html.= '<span class="caption">'.$content.'</span>';
	}
	
	$return_html.= '</div>';
	
	return $return_html;
}
add_shortcode('frame_center', 'frame_center_func');




function arrow_list_func($atts, $content) {
	
	$return_html = '<ul class="arrow_list">'.html_entity_decode(strip_tags($content,'<li><a>')).'</ul>';
	
	return $return_html;
}
add_shortcode('arrow_list', 'arrow_list_func');




function check_list_func($atts, $content) {
	
	$return_html = '<ul class="check_list">'.html_entity_decode(strip_tags($content,'<li><a>')).'</ul>';
	
	return $return_html;
}
add_shortcode('check_list', 'check_list_func');




function star_list_func($atts, $content) {
	
	$return_html = '<ul class="star_list">'.html_entity_decode(strip_tags($content,'<li><a>')).'</ul>';
	
	return $return_html;
}
add_shortcode('star_list', 'star_list_func');



function one_half_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'class' => '',
	), $atts));
	
	$return_html = '<div class="one_half '.$class.'">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_half', 'one_half_func');




function one_half_last_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'class' => '',
	), $atts));
	
	$return_html = '<div class="one_half last '.$class.'">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_half_last', 'one_half_last_func');



function one_third_func($atts, $content) {
	
	$return_html = '<div class="one_third">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_third', 'one_third_func');




function one_third_last_func($atts, $content) {
	
	$return_html = '<div class="one_third last">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_third_last', 'one_third_last_func');



function two_third_func($atts, $content) {
	
	$return_html = '<div class="two_third">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('two_third', 'two_third_func');




function two_third_last_func($atts, $content) {
	
	$return_html = '<div class="two_third last">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('two_third_last', 'two_third_last_func');




function one_fourth_func($atts, $content) {
	
	$return_html = '<div class="one_fourth">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_fourth', 'one_fourth_func');




function one_fourth_last_func($atts, $content) {
	
	$return_html = '<div class="one_fourth last">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_fourth_last', 'one_fourth_last_func');



function one_fifth_func($atts, $content) {
	
	$return_html = '<div class="one_fifth">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_fifth', 'one_fifth_func');




function one_fifth_last_func($atts, $content) {
	
	$return_html = '<div class="one_fifth last">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_fifth_last', 'one_fifth_last_func');



function one_sixth_func($atts, $content) {
	
	$return_html = '<div class="one_sixth">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_sixth', 'one_sixth_func');




function one_sixth_last_func($atts, $content) {
	
	$return_html = '<div class="one_sixth last">'.do_shortcode($content).'</div>';
	
	return $return_html;
}
add_shortcode('one_sixth_last', 'one_sixth_last_func');



function narm_gallery_func($atts, $content) {
  global $blog_id;
	//extract short code attr
	/*extract(shortcode_atts(array(
		'src' => '',
	), $atts));*/
	$return_html = '<div class="narm_gallery">'.html_entity_decode(strip_tags($content,'<img><a>')).'<div style="clear:both;"></div></div>';
	
	$return_html = str_replace('/wp-content/themes/Narm/timthumb.php?src=/files','/wp-content/themes/Narm/timthumb.php?src=/blogs.dir/' . $blog_id . '/files',$return_html);
	
	return $return_html;
}
add_shortcode('narm_gallery', 'narm_gallery_func');



function accordion_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'title' => '',
		'close' => 0,
	), $atts));
	
	$close_class = '';
	
	if(!empty($close))
	{
		$close_class = 'accordion_close';
	}
	
	$return_html = '<div class="accordion '.$close_class.'"><h3><a href="#">'.$title.'</a></h3>';
	$return_html.= '<div><p>';
	$return_html.= $content;
	$return_html.= '</p></div></div><br class="clear"/>';
	
	return $return_html;
}
add_shortcode('accordion', 'accordion_func');



function tabs_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'tab1' => '',
		'tab2' => '',
		'tab3' => '',
		'tab4' => '',
		'tab5' => '',
		'tab6' => '',
		'tab7' => '',
		'tab8' => '',
		'tab9' => '',
		'tab10' => '',
	), $atts));
	
	$tab_arr = array(
		$tab1,
		$tab2,
		$tab3,
		$tab4,
		$tab5,
		$tab6,
		$tab7,
		$tab8,
		$tab9,
		$tab10,
	);
	
	$return_html = '<div class="tabs"><ul>';
	
	foreach($tab_arr as $key=>$tab)
	{
		//display title1
		if(!empty($tab))
		{
			$return_html.= '<li><a href="#tabs-'.($key+1).'">'.$tab.'</a></li>';
		}
	}
	
	$return_html.= '</ul>';
	$return_html.= do_shortcode($content);
	$return_html.= '</div>';
	
	return $return_html;
}
add_shortcode('tabs', 'tabs_func');


function tab_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'id' => '',
	), $atts));
	
	$return_html.= '<div id="tabs-'.$id.'" class="tab_wrapper"><br class="clear"/>'.do_shortcode($content).'<br class="clear"/></div>';
	
	return $return_html;
}
add_shortcode('tab', 'tab_func');



function recent_posts_func($atts) {

	$return_html = narm_posts('recent', FALSE);
	
	return $return_html;
}
add_shortcode('recent_posts', 'recent_posts_func');



function popular_posts_func($atts) {

	$return_html = narm_posts('poopular', FALSE);
	
	return $return_html;
}
add_shortcode('popular_posts', 'popular_posts_func');


function customers_func($atts, $content) {

	$return_html = '<ul class="customer_list">';
	
	$items = preg_split('/[\r\n]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
	
	foreach($items as $item)
	{
		$item = strip_tags($item);
	
		if(!empty($item))
		{
			$return_html.= '<li><img src="'.$item.'" alt=""/></li>';
		}
	}
	
	$return_html.= '</ul>';
	
	return $return_html;
}
add_shortcode('customers', 'customers_func');


function services_func($atts, $content) {
	
	//extract short code attr
	extract(shortcode_atts(array(
		'thumb' => '',
	), $atts));
	
	$return_html = '<div class="one_third">';
	
	if(!empty($thumb))
	{
		$return_html.= '<div class="home_thumb">';
		$return_html.= '<img src="'.$thumb.'" alt=""/>';
		$return_html.= '</div>';
	}
	
	$return_html.= '<div class="home_box">';
	$return_html.= do_shortcode($content);
	$return_html.= '</div>';
	
	$return_html.= '</div>';
	
	return $return_html;
}
add_shortcode('services', 'services_func');




function services_last_func($atts, $content) {
	
	//extract short code attr
	extract(shortcode_atts(array(
		'thumb' => '',
	), $atts));
	
	$return_html = '<div class="one_third last">';
	
	if(!empty($thumb))
	{
		$return_html.= '<div class="home_thumb">';
		$return_html.= '<img src="'.$thumb.'" alt=""/>';
		$return_html.= '</div>';
	}
	
	$return_html.= '<div class="home_box">';
	$return_html.= do_shortcode($content);
	$return_html.= '</div>';
	
	$return_html.= '</div>';
	
	return $return_html;
}
add_shortcode('services_last', 'services_last_func');


function pricing_func($atts, $content) {
	
	//extract short code attr
	extract(shortcode_atts(array(
		'size' => '',
		'title' => '',
		'column' => 3,
	), $atts));
	
	$width_class = 'three';
	switch($column)
	{
		case 3:
			$width_class = 'three';
		break;
		case 4:
			$width_class = 'four';
		break;
		case 5:
			$width_class = 'five';
		break;
	}
	
	$return_html = '<div class="pricing_box '.$size.' '.$width_class.'">';
	
	if(!empty($title))
	{
		$return_html.= '<div class="header">';
		$return_html.= '<span>'.$title.'</span>';
		$return_html.= '</div><br/>';
	}
	
	$return_html.= do_shortcode($content);
	$return_html.= '</div>';
	
	return $return_html;
}
add_shortcode('pricing', 'pricing_func');


function alert_func($atts, $content) {

	//extract short code attr
	extract(shortcode_atts(array(
		'type' => 'info',
	), $atts));
	
	$return_html = '<div class="alert_'.$type.'" style="margin-top:0">';
	$return_html.= '<p><img src="'.get_bloginfo( 'stylesheet_directory' ).'/images/icon_'.$type.'.png" alt="'.$type.'" class="mid_align"/>';
	$return_html.= $content.'</p></div>';
	
	return $return_html;
}
add_shortcode('alert', 'alert_func');


?>