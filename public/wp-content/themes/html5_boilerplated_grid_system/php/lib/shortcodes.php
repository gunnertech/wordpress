<?php

add_shortcode('wp_caption', 'hbgs_img_caption_shortcode');
add_shortcode('caption', 'hbgs_img_caption_shortcode');
add_shortcode('column', 'hbgs_column_shortcode');
add_shortcode('header', 'hbgs_make_header_slug');
add_shortcode('small_button', 'hbgs_make_small_button');
add_shortcode('large_button', 'hbgs_make_large_button');
add_shortcode('altfont', 'hbgs_make_alternate_font');
add_shortcode('grid', 'hbgs_grid_shortcode');
add_shortcode('break', 'hbgs_make_line_break');
add_shortcode('widget', 'hbgs_render_widget');
add_shortcode('strip_markup', 'hbgs_strip_markup');
add_shortcode('hbgs_more', 'hbgs_read_more');
add_shortcode('hcard', 'hbgs_hcard');
add_shortcode('donate_box', 'hbgs_donate_box');

/*FIND A WIDGET BY IT'S ID AND RENDER IT INLINE*/
function hbgs_render_widget($attr,$content) {
  extract(shortcode_atts(array(
    "id" => false
  ), $attr));
  
  if($id) {
    return hbgs_the_widget_instance($id,false,$attr);
  } else {
    return hbgs_the_widget_instance_with_no_id($attr);
  }
  
  
}

function hbgs_hcard($attr) {
  extract(shortcode_atts(array(
    "first_name" => false,
    "middle_name" => false,
    "last_name" => false,
    "organization" => false,
    "email" => false,
    "street_address" => false,
    "city" => false,
    "state" => false,
    "zip" => false,
    "country" => false,
    "phone" => false,
    "aim" => false,
    "yim" => false,
    "jabber" => false,
    "url" => false,
    "photo_url" => false,
    "link_org" => false
  ), $attr));
  
  $name = trim("$first_name $middle_name $last_name");
  
  if($name == '') {
    $name = $email;
  }
  
  if($name == '') {
    $name = $url;
  }
  
  return '
  <div id="hcard-'.preg_replace('/\W/','-',$name).'" class="vcard">
    '. ($photo_url ? '<img src="'.$photo_url.'" alt="photo of '.$name.'" class="photo" />' : '') .'
    '. ($url ? '<a class="name url fn n" href="'.$url.'">' : '<span class="name">') .'
      '. ($first_name ? '<span class="given-name">'.$first_name.'</span>' : '') .'
      '. ($middle_name ? '<span class="additional-name">'.$middle_name.'</span>' : '') .'
      '. ($last_name ? '<span class="family-name">'.$last_name.'</span>' : '') .'
    '. ($url ? '</a>' : '</span>') .'
    '. ($organization ? '<div class="org">'.($link_org ? '<a href="'.$url.'">' : '').$organization.($link_org ? '</a>' : '').'</div>' : '') .'
    '. ($email ? '<a class="email" href="mailto:'.$email.'">'.$email.'</a>' : '') .'
    <div class="adr">
      '. ($street_address ? '<div class="street-address">'.$street_address.'</div>' : '') .'
      '. ($city ? '<span class="locality">'.$city.'</span>, ' : '') .'
      '. ($state ? '<span class="region">'.$state.'</span> ' : '') .'
      '. ($zip ? '<span class="postal-code">'.$zip.'</span> ' : '') .'
      '. ($country ? '<span class="country-name">'.$country.'</span> ' : '') .'
    </div>
    '. ($phone ? '<div class="tel">'.$phone.'</div>' : '') .'
    '. ($aim ? '<a class="url" href="aim:goim?screenname='.$aim.'">AIM</a>' : '') .'
    '. ($yim ? '<a class="url" href="ymsgr:sendIM?'.$aim.'">YIM</a>' : '') .'
    '. ($jabber ? '<a class="url" href="xmpp:'.$jabber.'">Jabber</a>' : '') .'
  </div>';
}

function hbgs_read_more($attr,$content) {
  extract(shortcode_atts(array(
    "text" => ''
  ), $attr));
  
  $link = get_permalink();

  return "<a href=\"$link\" class=\"more\">$content</a>";
}

function hbgs_make_header_slug($attr,$content) {
  extract(shortcode_atts(array(), $attr));
  	
	return "<hgroup><h4 class=\"slug\">".do_shortcode($content)."</h4></hgroup>";
}

function hbgs_make_line_break($attr,$content) {
  extract(shortcode_atts(array(), $attr));
  	
	return "<br />";
}

function hbgs_make_small_button($attr,$content) {
  extract(shortcode_atts(array(
    'url' => ''
  ), $attr));
  
  return '<a class="hbgs-small-button hbgs-button" href="'.$url.'">'.$content.'</a>';
}

function hbgs_make_large_button($attr,$content) {
  extract(shortcode_atts(array(
    'url' => ''
  ), $attr));
  
  return '<a class="hbgs-large-button hbgs-button" href="'.$url.'">'.$content.'</a>';
}


function hbgs_make_alternate_font($attr,$content) {
  extract(shortcode_atts(array(), $attr));
  	
	return "<span class=\"altfont\">".do_shortcode($content)."</span>";
}

function hbgs_column_shortcode($attr, $content=null) {
  return hbgs_make_column($attr,$content);
}

function hbgs_grid_shortcode($attr, $content=null) {
  return hbgs_make_column($attr,$content);
}

/*THEME OVERRIDE FOR IMAGE CAPTIONS*/
function hbgs_img_caption_shortcode($attr, $content=null) {
  // Allow plugins/themes to override the default caption template.
	$output = apply_filters('img_caption_shortcode', '', $attr, $content);
	if ( $output != '' )
		return $output;
		
  extract(shortcode_atts(array(
		'id'	=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''
	), $attr));

	if ( 1 > (int) $width || empty($caption) )
		return $content;

	if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

	return '<figure ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (0 + (int) $width) . 'px">'
	. do_shortcode( $content ) . '<figcaption><p>' . $caption . '</p></figcaption></figure>';
}

function hbgs_strip_markup($attr,$content) {
  return do_shortcode(strip_tags($content));
}

function hbgs_make_column($attr,$content) {
  extract(shortcode_atts(array(
		'size'	=> '7',
		'position'	=> '',
		'padleft'	=> '',
		'padright'	=> '',
		'background' => '',
		'add_markup' => true,
		'alt' => false
	), $attr));
	
	$size = str_replace(".","_",$size);
	$padleft = str_replace(".","_",$padleft);
	$padright = str_replace(".","_",$padright);
	$alt_start = '';
	$alt_end = '';
	$style_string = '';
	
	if($background) {
	  $style_string = 'style="background-image:url('.$background.');background-repeat:no-repeat;"';
	}
	
	if($padleft) {
	  $padleft = "prefix_$padleft";
	}
	
	if($padright) {
	  $padright = "suffix_$padright";
	}
	
	$class_string = "grid_$size $padleft $padright";
	
	if($size == '24'){
	  $class_string .= " alpha omega";
	} elseif($position == 'first') {
	  $class_string .= " alpha";
	} elseif($position == 'last') {
	  $class_string .= " omega";
	}
	
	if($alt) {
	  $alt_start = '<div class="alt" '. $style_string .'>';
	  $style_string = '';
  	$alt_end = '</div>';
  	$class_string .= " alt-wrapper";
	}
	
	$add_markup = (($add_markup === 'false' || $add_markup === '0') ? false : $add_markup);
	
	$content = $add_markup ? wpautop(do_shortcode($content)) : do_shortcode($content);
	
	return "<div $style_string class=\"$class_string\">$alt_start $content $alt_end</div>";
}

function hbgs_donate_box($atts,$content=null){
  extract(shortcode_atts(array(
		'code' => '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',
		'title' => 'Click the Button Below To Donate:',
		'align' => 'right',
		'hosted_button_id' => false,
		'type' => 'simple',
		'account_email' => false,
		'foundation_name' => false,
		'button_text' => 'Submit',
		'button_src' => false,
		'session' => false
	), $atts));
	$html = '';
	$donation_action = 'https://www.paypal.com/cgi-bin/webscr';
	$subscription_action = 'https://www.paypal.com/cgi-bin/webscr';
	
	if($type == 'hybrid') {
	  $html = '';
	  $html .= '<aside class="donatearea '.$align.'">';
	  $html .= '<h5>'.$title.'</h5>';
	  $html .= '<form class="donation" action="" method="post">';
	  $html .= '<div>';
	  $html .= '<span class="currency">$</span> <input class="a3" type="text" size="15" name="a3" /> ';
	  $html .= '<select name="t3" class="t3">';
	  $html .= '<option value="O">One Time</option>';
    $html .= '<option value="W">Weekly</option>';
    $html .= '<option value="M">Monthly</option>';
    $html .= '<option value="Y">Annual</option>';
	  $html .= '</select>';
	  $html .= '<input type="hidden" class="amount" name="amount" />';
    $html .= '<input type="hidden" class="subscription_action" name="subscription_action" value="'.$subscription_action.'" />';
    $html .= '<input type="hidden" class="donation_action" name="donation_action" value="'.$donation_action.'" />';
	  $html .= '<input type="hidden" class="cmd" value="_xclick-subscriptions" name="cmd" />';
	  $html .= '<input type="hidden" name="hosted_button_id" value="'.$hosted_button_id.'">';
	  $html .= '<input type="hidden" value="1" name="no_shipping" />';
	  $html .= '<input type="hidden" value="1" name="no_note" />';
	  $html .= '<input type="hidden" value="USD" name="currency_code" />';
	  $html .= '<input type="hidden" value="PP-SubscriptionsBF" name="bn" />';
	  $html .= '<input type="hidden" value="UTF-8" name="charset" />';
	  $html .= '<input type="hidden" value="1" name="p3" />';
	  $html .= '<input type="hidden" value="1" name="src" />';
	  $html .= '<input type="hidden" value="1" name="sra" />';
	  $html .= '<input type="hidden" value="'.str_replace("+"," ",urlencode($foundation_name)).'" name="item_name" />';
	  $html .= '<input type="hidden" value="'.$account_email.'" name="business" />';
	  $html .= '<input type="hidden" value="" class="return" name="return" />';
	  $html .= '<input type="hidden" value="" class="cancel" name="cancel" />';
	  $html .= ($button_src ? '<input class="submit" type="image" src="'.$button_src.'" />' : '<input type="submit" value="'.$button_text.'" />');
	  $html .= '</div>';
	  $html .= '</form>';
	  $html .= '</aside>';
	  
	  return $html;
	} else {
	  if($hosted_button_id) {
  	  return '<aside class="donatearea '.$align.'"><h5>'.$title.'</h5><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><div><input type="hidden" name="cmd" value="_s-xclick"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"><input type="hidden" name="hosted_button_id" value="'.$hosted_button_id.'"></div></form></aside>';
  	}

  	return '<aside class="donatearea '.$align.'"><h5>'.$title.'</h5><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><div><input type="hidden" name="cmd" value="_s-xclick"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"><input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----'.$code.'-----END PKCS7-----"></div></form></aside>';
	}
}

?>