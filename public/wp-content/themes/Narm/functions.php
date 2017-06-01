<?php

/**
*	Themes API call
**/
include (TEMPLATEPATH . "/lib/api.lib.php");

/**
*	Setup Theme post custom fields
**/
include (TEMPLATEPATH . "/lib/theme-post-custom-fields.php");

/**
*	Setup Theme page custom fields
**/
include (TEMPLATEPATH . "/lib/theme-page-custom-fields.php");

/**
*	Setup Sidebar
**/
include (TEMPLATEPATH . "/lib/sidebar.lib.php");


//Get custom function
include (TEMPLATEPATH . "/lib/custom.lib.php");


//Get custom shortcode
include (TEMPLATEPATH . "/lib/shortcode.lib.php");


/**
*	Setup Menu
**/
include (TEMPLATEPATH . "/lib/menu.lib.php");

function get_image_path($src) {
  global $blog_id;
  if(isset($blog_id) && $blog_id > 0) {
    $imageParts = explode('/files/' , $src);
    if(isset($imageParts[1])) {
      $src = "http://dhwlijwe9jil7.cloudfront.net/files/". $imageParts[1];
    }
  }
  return $src;
}


function filter_rss_query($query) {
	if ( $query->is_feed ) {
		$query->set('cat', get_option('nm_blog_cat'));
	}
	return $query;
}
add_filter('pre_get_posts', 'filter_rss_query');


/*
	Begin creating admin optinos
*/

$themename = "Narm";
$shortname = "nm";

$categories = get_categories('hide_empty=0&orderby=name');
$wp_cats = array(
	0		=> "Choose a category"
);
foreach ($categories as $category_list ) {
       $wp_cats[$category_list->cat_ID] = $category_list->cat_name;
}

$pages = get_pages(array('parent' => 0));
$wp_pages = array(
	0		=> "Choose a page"
);
foreach ($pages as $page_list ) {
       $wp_pages[$page_list->ID] = $page_list->post_title;
}

$nm_handle = opendir(TEMPLATEPATH.'/css/skins');
$nm_skin_arr = array();

while (false!==($nm_file = readdir($nm_handle))) {
	if ($nm_file != "." && $nm_file != ".." && $nm_file != ".DS_Store") {
		$nm_file_name = basename($nm_file, '.css');
		$nm_name = str_replace('_', ' ', $nm_file_name);

		$nm_skin_arr[$nm_file_name] = $nm_name;
	}
}
closedir($nm_handle);
asort($nm_skin_arr);


$options = array (
 
//Begin admin header
array( 
		"name" => $themename." Options",
		"type" => "title"
),
//End admin header
 

//Begin first tab "General"
array( 
		"name" => "General",
		"type" => "section"
)
,

array( "type" => "open"),

array( "name" => "Skins",
	"desc" => "Select the skin for the theme",
	"id" => $shortname."_skin",
	"type" => "select",
	"options" => $nm_skin_arr,
	"std" => "silver"
),
array( "name" => "Content Color",
	"desc" => "Select color for your content",
	"id" => $shortname."_color",
	"type" => "select",
	"options" => array(
		'light' => 'Light',
		'dark' => 'Dark',
	),
	"std" => "Light"
),
array( "name" => "Header Font",
	"desc" => "Select font for header text",
	"id" => $shortname."_font",
	"type" => "select",
	"options" => array(
		'Colaborate_Thin.font' => 'Colaborate',
		'PT_Sans_400.font' => 'PT Sans',
		'GeosansLight_500.font' => 'Geo Sans',
		'Josefin_Sans_Std_300.font' => 'Josefin Sans Std',
		'MgOpen_Modata_400.font' => 'MgOpen Modata',
	),
	"std" => "Colaborate"
),
array( "name" => "Your Logo (Image URL)",
	"desc" => "Enter the URL of image that you want to use as the logo",
	"id" => $shortname."_logo",
	"type" => "text",
	"std" => "",
),
array( "name" => "Google Analytics Domain ID ",
	"desc" => "Get analytics on your site. Simply give us your Google Analytics Domain ID (something like UA-123456-1)",
	"id" => $shortname."_ga_id",
	"type" => "text",
	"std" => ""

),
array( "name" => "Custom Favicon",
	"desc" => "A favicon is a 16x16 pixel icon that represents your site; paste the URL to a .ico image that you want to use as the image",
	"id" => $shortname."_favicon",
	"type" => "text",
	"std" => "",
),
	
array( "type" => "close"),
//End first tab "General"


//Begin second tab "Homepage"
array( "name" => "Homepage",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Show Slider on Homepage",
	"desc" => "Select if you want to show or hide content slider on homepage",
	"id" => $shortname."_homepage_hide_slider",
	"type" => "select",
	"options" => array(
		0 => 'Show',
		1 => 'Hide',
	),
	"std" => 1
),

array( "name" => "Show content boxes on Homepage",
	"desc" => "Select if you want to show or hide content boxes on homepage",
	"id" => $shortname."_homepage_hide_boxes",
	"type" => "select",
	"options" => array(
		0 => 'Show',
		1 => 'Hide',
	),
	"std" => 1
),

array( "name" => "Show Portfolio on Homepage",
	"desc" => "Select if you want to show or hide recent portfolio on homepage",
	"id" => $shortname."_homepage_hide_portfolio",
	"type" => "select",
	"options" => array(
		0 => 'Show',
		1 => 'Hide',
	),
	"std" => 1
),

array( "name" => "Show Blog post on Homepage",
	"desc" => "Select if you want to show or hide recent blog posts on homepage",
	"id" => $shortname."_homepage_hide_blog",
	"type" => "select",
	"options" => array(
		0 => 'Show',
		1 => 'Hide',
	),
	"std" => 1
),

array( "name" => "Homepage slider category",
	"desc" => "Choose a category from which contents in slider are drawn",
	"id" => $shortname."_slider_cat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"
),

array( "name" => "Homepage slider sort by",
	"desc" => "Select sorting type for contents in slider",
	"id" => $shortname."_slider_sort",
	"type" => "select",
	"options" => array(
		'DESC' => 'Newest First',
		'ASC' => 'Oldest First',
	),
	"std" => "ASC"
),

array( "name" => "Homepage slider items",
	"desc" => "How many items you want display in slider?",
	"id" => $shortname."_slider_items",
	"type" => "text",
	"size" => "40px",
	"std" => "5",
),

array( "name" => "Homepage slider height (in pixels)",
	"desc" => "Enter number of height for homepage slider <br/><strong>Maximum height 405 pixels</strong>",
	"id" => $shortname."_slider_height",
	"type" => "text",
	"size" => "40px",
	"std" => "405",
),

array( "name" => "Homepage slider timer (in second)",
	"desc" => "Enter number of seconds for homepage slider timer",
	"id" => $shortname."_slider_timer",
	"type" => "text",
	"size" => "40px",
	"std" => "5",
),

array( "name" => "Homepage site tagline header",
	"desc" => "Enter tagline header to describe what your site is about to homepage",
	"id" => $shortname."_homepage_tagline_header",
	"type" => "text",
	"std" => "Built-in so many custom modification",
),

array( "name" => "Homepage site tagline",
	"desc" => "Enter tagline to describe what your site is about to homepage",
	"id" => $shortname."_homepage_tagline",
	"type" => "text",
	"std" => "",
),

array( "name" => "Homepage content boxes category",
	"desc" => "Choose a category from which contents in boxes are drawn",
	"id" => $shortname."_box_cat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"
),

array( "type" => "close"),
//End second tab "Homepage"


//Begin second tab "Portfolio"
array( "name" => "Portfolio",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Portfolio category",
	"desc" => "Choose a category from which contents in portfolio are drawn",
	"id" => $shortname."_portfolio_cat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"
),
array( "name" => "Portfolio sort by",
	"desc" => "Select sorting type for contents in portfolio",
	"id" => $shortname."_portfolio_sort",
	"type" => "select",
	"options" => array(
		'DESC' => 'Newest First',
		'ASC' => 'Oldest First',
	),
	"std" => "ASC"
),
array( "name" => "Portfolio scroll speed",
	"desc" => "Enter speed number of portfolio scrolling (Larger number for faster speed)",
	"id" => $shortname."_portfolio_slider_speed",
	"type" => "text",
	"size" => "40px",
	"std" => "5",
),
array( "name" => "Auto scroll",
	"desc" => "Select if you want to enable or disable auto scroll feature",
	"id" => $shortname."_portfolio_auto_scroll",
	"type" => "select",
	"options" => array(
		1 => 'Enable',
		0 => 'Disable',
	),
	"std" => 1
),
array( "name" => "Portfolio image & video width (in pixels)",
	"desc" => "Enter number of width for Portfolio image & video",
	"id" => $shortname."_portfolio_width",
	"type" => "text",
	"size" => "40px",
	"std" => "450",
),
array( "name" => "Portfolio image & video height (in pixels)",
	"desc" => "Enter number of height for Portfolio image & video",
	"id" => $shortname."_portfolio_height",
	"type" => "text",
	"size" => "40px",
	"std" => "200",
),
array( "name" => "Portfolio description area height (in pixels)",
	"desc" => "Enter number of height for Portfolio item's description area",
	"id" => $shortname."_portfolio_desc_height",
	"type" => "text",
	"size" => "40px",
	"std" => "300",
),

array( "type" => "close"),
//End second tab "Portfolio"


//Begin second tab "Gallery"
array( "name" => "Gallery",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Choose page for gallery",
	"desc" => "Choose a page from which your gallery to display",
	"id" => $shortname."_gallery_page",
	"type" => "select",
	"options" => $wp_pages,
	"std" => "Choose a page"
),
array( "name" => "Gallery Columns",
	"desc" => "Select the columns style for the galery",
	"id" => $shortname."_gallery_column",
	"type" => "select",
	"options" => array(
		1 => '1 Column',
		2 => '2 Columns',
		3 => '3 Columns',
		4 => '4 Columns',
	),
	"std" => 1
),
array( "name" => "Gallery category",
	"desc" => "Choose a category from which contents in gallery are drawn",
	"id" => $shortname."_gallery_cat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"
),
array( "name" => "Gallery sort by",
	"desc" => "Select sorting type for contents in gallery",
	"id" => $shortname."_gallery_sort",
	"type" => "select",
	"options" => array(
		'DESC' => 'Newest First',
		'ASC' => 'Oldest First',
	),
	"std" => "ASC"
),
array( "name" => "Gallery items per page",
	"desc" => "Enter how many items get displayed in gallery page (default is 8 items)",
	"id" => $shortname."_gallery_items",
	"type" => "text",
	"std" => "8",
	"size" => "40px"	
),

array( "type" => "close"),
//End second tab "Gallery"


//Begin second tab "Special Content"
array( "name" => "Sidebar",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Add a new sidebar",
	"desc" => "Enter sidebar name",
	"id" => $shortname."_sidebar0",
	"type" => "text",
	"std" => "",
),
array( "type" => "close"),
//End second tab "Special Content"

//Begin second tab "Blog"
array( "name" => "Blog",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Choose page for blog",
	"desc" => "Choose a page from which your blog posts to display",
	"id" => $shortname."_blog_page",
	"type" => "select",
	"options" => $wp_pages,
	"std" => "Choose a page"),

array( "name" => "Blog category",
	"desc" => "Choose a category from which content show as Blog posts",
	"id" => $shortname."_blog_cat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"
),
array( "type" => "close"),
//End second tab "Blog"


//Begin fourth tab "Contact"
array( "name" => "Contact",
	"type" => "section"),
array( "type" => "open"),
	
array( "name" => "Choose page for contact form",
	"desc" => "Choose a page from which your contact form to display",
	"id" => $shortname."_contact_page",
	"type" => "select",
	"options" => $wp_pages,
	"std" => "Choose a page"),
array( "name" => "Your email address",
	"desc" => "Enter which email address will be sent from contact form",
	"id" => $shortname."_contact_email",
	"type" => "text",
	"std" => ""

),
//End fourth tab "Contact"

//Begin fifth tab "Footer"
array( "type" => "close"),
array( "name" => "Footer",
	"type" => "section"),
array( "type" => "open"),
	
array( "name" => "Footer text",
	"desc" => "Enter footer text ex. copyright description",
	"id" => $shortname."_footer_text",
	"type" => "textarea",
	"std" => ""

),
//End fifth tab "Footer"

 
array( "type" => "close")
 
);


function nm_add_admin() {
 
global $themename, $shortname, $options;
 
if ( isset($_GET['page']) && $_GET['page'] == basename(__FILE__) ) {
 
	if ( isset($_REQUEST['action']) && 'save' == $_REQUEST['action'] ) {
 
		foreach ($options as $value) 
		{
			update_option( $value['id'], $_REQUEST[ $value['id'] ] );
		}
 
foreach ($options as $value) {
	if( isset( $_REQUEST[ $value['id'] ] ) ) { 
		if($value['id'] != $shortname."_sidebar0")
		{
			update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
		}
		elseif(isset($_REQUEST[ $value['id'] ]) && !empty($_REQUEST[ $value['id'] ]))
		{
			//get last sidebar serialize array
			$current_sidebar = get_option($shortname."_sidebar");
			$current_sidebar[ $_REQUEST[ $value['id'] ] ] = $_REQUEST[ $value['id'] ];

			update_option( $shortname."_sidebar", $current_sidebar );
		}
	} 
	else 
	{ 
		delete_option( $value['id'] ); 
	} 
}

 
	header("Location: admin.php?page=functions.php&saved=true");
 
} 
else if( isset($_REQUEST['action']) && 'reset' == $_REQUEST['action'] ) {
 
	foreach ($options as $value) {
		delete_option( $value['id'] ); }
 
	header("Location: admin.php?page=functions.php&reset=true");
 
}
}
 
add_menu_page($themename, $themename, 'administrator', basename(__FILE__), 'nm_admin');
}

function nm_add_init() {

$file_dir=get_bloginfo('template_directory');
wp_enqueue_style("functions", $file_dir."/functions/functions.css", false, "1.0", "all");
wp_enqueue_script("rm_script", $file_dir."/functions/rm_script.js", false, "1.0");

}
function nm_admin() {
 
global $themename, $shortname, $options;
$i=0;
 
if ( isset($_REQUEST['saved']) &&  $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
if ( isset($_REQUEST['reset']) &&  $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
 
?>
	<div class="wrap rm_wrap">
	<h2><?php echo $themename; ?> Settings</h2>

	<div class="rm_opts">
	<form method="post"><?php foreach ($options as $value) {
switch ( $value['type'] ) {
 
case "open":
?> <?php break;
 
case "close":
?>
	
	</div>
	</div>
	<br />


	<?php break;
 
case "title":
?>
	<br />


<?php break;
 
case 'text':
	
	//if sidebar input then not show default value
	if($value['id'] != $shortname."_sidebar0")
	{
		$default_val = get_settings( $value['id'] );
	}
	else
	{
		$default_val = '';	
	}
?>

	<div class="rm_input rm_text"><label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	<input name="<?php echo $value['id']; ?>"
		id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>"
		value="<?php if ($default_val != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>"
		<?php if(!empty($value['size'])) { echo 'style="width:'.$value['size'].'"'; } ?> />
		<small><?php echo $value['desc']; ?></small>
	<div class="clearfix"></div>
	
	<?php
	if($value['id'] == $shortname."_sidebar0")
	{
		$current_sidebar = get_option($shortname."_sidebar");
		
		if(!empty($current_sidebar))
		{
	?>
		<ul id="current_sidebar" class="rm_list">

	<?php
		foreach($current_sidebar as $sidebar)
		{
	?> 
			
			<li id="<?=$sidebar?>"><?=$sidebar?> ( <a href="/wp-admin/admin.php?page=functions.php" class="sidebar_del" rel="<?=$sidebar?>">Delete</a> )</li>
	
	<?php
		}
	?>
	
		</ul>
	
	<?php
		}
	}
	?>

	</div>
	<?php
break;

case 'password':
?>

	<div class="rm_input rm_text"><label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	<input name="<?php echo $value['id']; ?>"
		id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>"
		value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>"
		<?php if(!empty($value['size'])) { echo 'style="width:'.$value['size'].'"'; } ?> />
	<small><?php echo $value['desc']; ?></small>
	<div class="clearfix"></div>

	</div>
	<?php
break;
 
case 'textarea':
?>

	<div class="rm_input rm_textarea"><label
		for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	<textarea name="<?php echo $value['id']; ?>"
		type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
	<small><?php echo $value['desc']; ?></small>
	<div class="clearfix"></div>

	</div>

	<?php
break;
 
case 'select':
?>

	<div class="rm_input rm_select"><label
		for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

	<select name="<?php echo $value['id']; ?>"
		id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $key => $option) { ?>
		<option
		<?php if (get_settings( $value['id'] ) == $key) { echo 'selected="selected"'; } ?>
			value="<?php echo $key; ?>"><?php echo $option; ?></option>
		<?php } ?>
	</select> <small><?php echo $value['desc']; ?></small>
	<div class="clearfix"></div>
	</div>
	<?php
break;
 
case "checkbox":
?>

	<div class="rm_input rm_checkbox"><label
		for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

	<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
	<input type="checkbox" name="<?php echo $value['id']; ?>"
		id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />


	<small><?php echo $value['desc']; ?></small>
	<div class="clearfix"></div>
	</div>
	<?php break; 
case "section":

$i++;

?>

	<div class="rm_section">
	<div class="rm_title">
	<h3><img
		src="<?php bloginfo('template_directory')?>/functions/images/trans.png"
		class="inactive" alt="""><?php echo $value['name']; ?></h3>
	<span class="submit"><input name="save<?php echo $i; ?>" type="submit"
		value="Save changes" /> </span>
	<div class="clearfix"></div>
	</div>
	<div class="rm_options"><?php break;
 
}
}
?> <input type="hidden" name="action" value="save" />
	</form>
	<form method="post"><!-- p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p --></form>
	</div>


	<?php
}

add_action('admin_init', 'nm_add_init');
add_action('admin_menu', 'nm_add_admin');

/*
	End creating admin options
*/

//Make widget support shortcode
add_filter('widget_text', 'do_shortcode');
?>