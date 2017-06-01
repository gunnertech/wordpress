<?php

add_action('init','of_options');

if (!function_exists('of_options')) {
function of_options(){
	
//Access the WordPress Categories via an Array
$of_categories = array();  
$of_categories_obj = get_categories('hide_empty=0');
foreach ($of_categories_obj as $of_cat) {
    $of_categories[$of_cat->cat_ID] = $of_cat->cat_name;}
$categories_tmp = array_unshift($of_categories, "Select a category:");    
       
//Access the WordPress Pages via an Array
$of_pages = array();
$of_pages_obj = get_pages('sort_column=post_parent,menu_order');    
foreach ($of_pages_obj as $of_page) {
    $of_pages[$of_page->ID] = $of_page->post_name; }
$of_pages_tmp = array_unshift($of_pages, "Select a page:");       

//Options
$enable_disable = array('enable','disable'); 
$disable_enable = array('disable','enable'); 
$color_schemes = array('light','dark');
$cropped_full = array('cropped','full');
$service_cat_layout = array('full','sidebar');
$fixed_static  = array('fixed','static');
$blank_self = array('blank','self');
$header_styles = array('one','two', 'three', 'four');


$of_options_homepage_blocks = array(
	"enabled" => array (
		"placebo" => "placebo", //REQUIRED!
		"home_slider" => "Slider",
		"home_static_video" => "Static Video",
		"home_tagline" => "Tagline",
		"home_highlights" => "Highlights",
		"home_portfolio" => "Portfolio Items",
		"home_blog" => "Blog Posts",
		"home_static_page" => "Static Page"
	),
	"disabled" => array (
		"placebo" => "placebo", //REQUIRED!
	),
);

//Stylesheets Reader
$alt_stylesheet_path = LAYOUT_PATH;
$alt_stylesheets = array();

if ( is_dir($alt_stylesheet_path) ) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 
        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
            if(stristr($alt_stylesheet_file, ".css") !== false) {
                $alt_stylesheets[] = $alt_stylesheet_file;
            }
        }    
    }
}

//Background Images Reader
$bg_images_path = STYLESHEETPATH. '/images/bg/'; // change this to where you store your bg images
$bg_images_url = get_template_directory_uri().'/images/bg/'; // change this to where you store your bg images
$bg_images = array();

if ( is_dir($bg_images_path) ) {
    if ($bg_images_dir = opendir($bg_images_path) ) { 
        while ( ($bg_images_file = readdir($bg_images_dir)) !== false ) {
            if(stristr($bg_images_file, ".png") !== false || stristr($bg_images_file, ".jpg") !== false) {
                $bg_images[] = $bg_images_url . $bg_images_file;
            }
        }    
    }
}


/*-----------------------------------------------------------------------------------*/
/* The Options Array */
/*-----------------------------------------------------------------------------------*/

// Set the Options Array
global $of_options;
$of_options = array();


$of_options[] = array( "name" => __('General', 'office'),
					"type" => "heading");
					
$of_options[] = array( "name" => __('Enable or Disable Responsive Layout', 'office'),
					"desc" => __('Select to enable/disable the responsive layout', 'office'),
					"id" => "disable_responsive",
					"std" => "enable",
					"type" => "select",
					"options" => $enable_disable);
					
$of_options[] = array( "name" => __('Main Logo Upload', 'office'),
					"desc" => __('Upload your custom logo using the native media uploader, or define the URL directly', 'office'),
					"id" => "custom_logo",
					"std" => "",
					"type" => "media");
					
$of_options[] = array( "name" => __('Custom Login Logo', 'office'),
					"desc" => __('Upload a custom logo for your Wordpress login screen.', 'office'),
					"id" => "custom_login_logo",
					"std" => "",
					"type" => "media");
					
$of_options[] = array( "name" => __('Custom Login Logo Height', 'office'),
					"desc" => __('Enter the height of your custom logo to override the default WordPress image height. Width, must not be changed.', 'office'),
					"id" => "custom_login_logo_height",
					"std" => "",
					"type" => "text");
					
$of_options[] = array( "name" => __('Custom Favicon', 'office'),
					"desc" => __('Upload or past the URL for your custom favicon.', 'office'),
					"id" => "custom_favicon",
					"std" => "",
					"type" => "upload");
										
$of_options[] = array( "name" => __('Enable or Disable Top Bar', 'office'),
					"desc" => __('Select to enable/disable the top bar', 'office'),
					"id" => "disable_top_bar",
					"std" => "enable",
					"type" => "select",
					"options" => $enable_disable);
					
$of_options[] = array( "name" => __('Top Bar Static or Fixed', 'office'),
					"desc" => __('Select if you want a static or fixed top bar. The Fixed option will keep the bar always visible.', 'office'),
					"id" => "top_bar_position",
					"std" => "fixed",
					"type" => "select",
					"options" => $fixed_static);
					
$of_options[] = array( "name" => __('Top Bar Callout Button Text', 'office'),
					"desc" => __('Enter the text for your top bar callout button.', 'office'),
					"id" => "callout_text",
					"std" => "Sample Text",
					"type" => "text"); 
					
$of_options[] = array( "name" => __('Top Bar Callout Button Link', 'office'),
					"desc" => __('Enter the url to link your top bar callout button to', 'office'),
					"id" => "callout_link",
					"std" => "#sample-url",
					"type" => "text"); 

$of_options[] = array( "name" => __('Top Bar Link Target', 'office'),
					"desc" => __('Select your link target', 'office'),
					"id" => "callout_target",
					"std" => "blank",
					"type" => "select",
					"options" => $blank_self);
			
$of_options[] = array( "name" => __('Tracking Code (Header)', 'office'),
					"desc" => __('Paste your Google Analytics (or other) tracking code here. This will be added into the header template of your theme.', 'office'),
					"id" => "tracking_header",
					"std" => "",
					"type" => "textarea");    
					
$of_options[] = array( "name" => __('Tracking Code (Footer)', 'office'),
					"desc" => __('Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.', 'office'),
					"id" => "tracking_footer",
					"std" => "",
					"type" => "textarea");
					
					
$of_options[] = array( "name" => __('Styling', 'office'),
					"type" => "heading");
					
$of_options[] = array( "name" => __('Header Style', 'office'),
					"desc" => __('Select your header style', 'office'),
					"id" => "header_style",
					"std" => "one",
					"type" => "select",
					"options" => $header_styles);
								
$of_options[] = array( "name" => __('Enable or Disable Right Border On Last Menu Icon', 'office'),
					"desc" => __('Select to enable/disable the border on the lasst menu icon. Best if you do not have a lot of menu items on your main navigation', 'office'),
					"id" => "disable_menu_last_border",
					"std" => "enable",
					"type" => "select",
					"options" => $enable_disable);
					
$of_options[] = array( "name" => __('Enable or Disable Diamond Background Pattern', 'office'),
					"desc" => __('Select to enable/disable the light diamond pattern on main content background', 'office'),
					"id" => "disable_background_pattern",
					"std" => "enable",
					"type" => "select",
					"options" => $enable_disable);
					
$url =  ADMIN_DIR . 'images/';
$of_options[] = array( "name" => __('Sidebar Layout', 'office'),
					"desc" => __('Select the sidebar position for your site. Choose left or right.', 'office'),
					"id" => "sidebar_position",
					"std" => "right",
					"type" => "images",
					"options" => array(
						'right' => $url . '2cr.png',
						'left' => $url . '2cl.png')
					);
					
$of_options[] = array( "name" => "Background Images",
					"desc" => "Select a background pattern. You can add your own by dropping the images into your images/bg folder in the theme ~ Epic.",
					"id" => "custom_bg",
					"std" => $bg_images_url."bg0.png",
					"type" => "tiles",
					"options" => $bg_images,
);

$of_options[] = array( "name" => __('Background Color', 'office'),
					"desc" => __('Select your background color. Will come through on some background images, not all.', 'office'),
					"id" => "background_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Highlight Color', 'office'),
					"desc" => __('Select your highlight color.', 'office'),
					"id" => "highlight_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Homepage Tagline Link Color', 'office'),
					"desc" => __('Select your homepage tagline link color.', 'office'),
					"id" => "home_tagline_link_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Nav Background Color', 'office'),
					"desc" => __('Select your navigation background color.', 'office'),
					"id" => "nav_bg_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Nav Hover Background Color', 'office'),
					"desc" => __('Select your navigation hover background color.', 'office'),
					"id" => "nav_hover_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Nav Current Item Background Color', 'office'),
					"desc" => __('Select your navigation current item background color.', 'office'),
					"id" => "nav_current_background_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Nav Current Item Link Color', 'office'),
					"desc" => __('Select your navigation current item text color.', 'office'),
					"id" => "nav_current_link_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Nav Link Color', 'office'),
					"desc" => __('Select your navigation link color.', 'office'),
					"id" => "nav_link_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Nav Link Hover Color', 'office'),
					"desc" => __('Select your navigation link hover color.', 'office'),
					"id" => "nav_link_hover_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Nav Light Border Color', 'office'),
					"desc" => __('Select your navigation light border color.', 'office'),
					"id" => "nav_light_border_color",
					"std" => "",
					"type" => "color");
					
$of_options[] = array( "name" => __('Nav Dark Border Color', 'office'),
					"desc" => __('Select your navigation dark border color.', 'office'),
					"id" => "nav_dark_border_color",
					"std" => "",
					"type" => "color");

$of_options[] = array( "name" => __('Custom CSS', 'office'),
                    "desc" => __('Quickly add some CSS to your theme by adding it to this block.', 'office'),
                    "id" => "custom_css",
                    "std" => "",
                    "type" => "textarea");  
					 
$of_options[] = array( "name" => __('Home', 'office'),
                    "type" => "heading");
					
$of_options[] = array( "name" => __('Enable Blog Style Homepage', 'office'),
					"desc" => __('Select to enable/disable the blog style homepage.', 'office'),
					"id" => "enable_disable_home_blog",
					"std" => "disable",
					"type" => "select",
					"options" => $disable_enable);
					
$of_options[] = array( "name" => __('Homepage Layout Manager', 'office'),
					"desc" => __('Organize how you want the layout to appear on the homepage.', 'office'),
					"id" => "homepage_blocks",
					"std" => $of_options_homepage_blocks,
					"type" => "sorter");
					
$of_options[] = array( "name" => __('Homepage Static Video', 'office'),
					"desc" => __('Enter your embded code or WordPress shortcode for your homepage static video module. Best used for replacing the image slider. <strong>Use 970px for the width when embedding videos</strong>', 'office'),
					"id" => "home_video",
					"std" => "",
					"type" => "textarea");
				
$of_options[] = array( "name" => __('Tagline Title', 'office'),
                    "desc" => __('Enter your custom title for the homepage tagline module. Leave blank to show nothing.', 'office'),
                    "id" => "home_tagline_title",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Tagline URL', 'office'),
                    "desc" => __('Enter a url to link your homepage tagline module title to (optional).', 'office'),
                    "id" => "home_tagline_title_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Tagline Content', 'office'),
					"desc" => __('Control your homepage tagline here. HTML and shortcodes allowed.', 'office'),
					"id" => "home_tagline",
					"std" => 'Office is the <a href="#">PERFECT</a> solution for your business & portfolio website.',
					"type" => "textarea");	
					
$of_options[] = array( "name" => __('Highlights Title', 'office'),
                    "desc" => __('Enter your custom title for the homepage highlights module. Leave blank to show nothing.', 'office'),
                    "id" => "home_highlights_title",
                    "std" => "What We Do",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Highlights Title URL', 'office'),
                    "desc" => __('Enter a url to link your highlights title to (optional).', 'office'),
                    "id" => "home_highlights_title_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Portfolio Items Title', 'office'),
                    "desc" => __('Enter your custom title for the latest portfolio items module. Leave blank to show nothing.', 'office'),
                    "id" => "home_portfolio_title",
                    "std" => "Recent Work",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Portfolio Items Title URL', 'office'),
                    "desc" => __('Enter a url to link your portfolio items title to (optional).', 'office'),
                    "id" => "home_portfolio_title_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('How Many Latest Portfolio Items', 'office'),
					"desc" => __('How many latest portfolio items do you want to show on the homepage.', 'office'),
					"id" => "home_portfolio_count",
					"std" => "8",
					"type" => "text");
					
$of_options[] = array( "name" => __('Blog Posts Title', 'office'),
                    "desc" => __('Enter your custom title for the latest blog items module. Leave blank to show nothing.', 'office'),
                    "id" => "home_blog_title",
                    "std" => "From The Blog",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Blog Items Title URL', 'office'),
                    "desc" => __('Enter a url to link your blog items title to (optional).', 'office'),
                    "id" => "home_blog_title_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('How Many Latest Blog Posts', 'office'),
					"desc" => __('How many latest blog items do you want to show on the homepage.', 'office'),
					"id" => "home_blog_count",
					"std" => "3",
					"type" => "text");
					
$of_options[] = array( "name" => __('Blog Items Excerpt Length', 'office'),
                    "desc" => __('Enter your excerpt length for the latest blog items.', 'office'),
                    "id" => "home_blog_excerpt_length",
                    "std" => "20",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Static Page Title', 'office'),
					"desc" => __('Enter the text for your static page module. (optional)', 'office'),
					"id" => "home_static_page_title",
					"std" => "Sample Title",
					"type" => "text");
					
$of_options[] = array( "name" => __('Static Page Title URL', 'office'),
					"desc" => __('Enter the url for your static page module. (optional)', 'office'),
					"id" => "home_static_page_title_url",
					"std" => "",
					"type" => "text");
					
$of_options[] = array( "name" => __('Static Page', 'office'),
					"desc" => __('Select a page for your homepage static page module. <strong>Important:</strong> Pages with custom loops like the portfolio, services, staff..etc, pages will not work.This only shows the content from the post editor.', 'office'),
					"id" => "home_static_page",
					"std" => "Select a page:",
					"type" => "select",
					"options" => $of_pages);

$of_options[] = array( "name" => __('Portfolio', 'office'),
					"type" => "heading");
					
$of_options[] = array( "name" => __('Portfolio Items Per Portfolio Page', 'office'),
                    "desc" => __('How many portfolio items do you wish to show on the portfolio page before activating the pagination. (filtered portfolio will always show all)', 'office'),
                    "id" => "portfolio_pagination",
                    "std" => "12",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Portfolio Items Per Portfolio Category', 'office'),
                    "desc" => __('How many portfolio items do you wish to show on the portfolio categories before activating the pagination.', 'office'),
                    "id" => "portfolio_cat_pagination",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Disable or Enable Related Items', 'office'),
					"desc" => __('Select to enable or disable the related portfolio items on single portfolio posts.', 'office'),
					"id" => "disable_related_port",
					"std" => "disable",
					"type" => "select",
					"options" => $disable_enable);
					
$of_options[] = array( "name" => __('Blog', 'office'),
					"type" => "heading");
					
$of_options[] = array( "name" => __('Full Content Entries', 'office'),
					"desc" => __('Select to enable the full content for entries. This means the entries for the blog posts will show the entire post content with a large featured image instead of the small image on the left and title, meta and excerpt on the right.', 'office'),
					"id" => "enable_full_blog",
					"std" => "disable",
					"type" => "select",
					"options" => $disable_enable);	
					
$of_options[] = array( "name" => __('Excerpt Length', 'office'),
                    "desc" => __('Add your own custom blog excerpt length. Used for blog page, archives and search results.', 'office'),
                    "id" => "blog_excerpt",
                    "std" => "60",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Enable or Disable Single Blog Post Featured Images.', 'office'),
					"desc" => __('Select to enable or disable the featured image on blog posts.', 'office'),
					"id" => "enable_disable_post_image",
					"std" => "enable",
					"type" => "select",
					"options" => $enable_disable);
					
$of_options[] = array( "name" => __('Enable or Disable Single Blog Post Meta', 'office'),
					"desc" => __('Select to enable or disable the meta box with the date and categories from the single blog posts.', 'office'),
					"id" => "enable_disable_single_meta",
					"std" => "enable",
					"type" => "select",
					"options" => $enable_disable);

$of_options[] = array( "name" => __('Social', 'office'),
					"type" => "heading");
					
$of_options[] = array( "name" => __('Twitter', 'office'),
                    "desc" => __('Add your twitter url.', 'office'),
                    "id" => "twitter",
                    "std" => "http://twitter.com/#!/WPExplorer",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Dribbble', 'office'),
                    "desc" => __('Add your dribbble url.', 'office'),
                    "id" => "dribbble",
                    "std" => "http://dribbble.com/aj-clarke",
                    "type" => "text");

$of_options[] = array( "name" => __('Forrst', 'office'),
                    "desc" => __('Add your forrst url.', 'office'),
                    "id" => "forrst",
                    "std" => "http://forrst.com/",
                    "type" => "text");

$of_options[] = array( "name" => __('Flickr', 'office'),
                    "desc" => __('Add your flickr url.', 'office'),
                    "id" => "flickr",
                    "std" => "http://www.flickr.com/photos/ahappyfish/",
                    "type" => "text");

$of_options[] = array( "name" => __('Facebook', 'office'),
                    "desc" => __('Add your facebook url.', 'office'),
                    "id" => "facebook",
                    "std" => "http://www.facebook.com/WPExplorerThemes",
                    "type" => "text");
					
$of_options[] = array( "name" => __('LinkedIn', 'office'),
                    "desc" => __('Add your LinkedIn url.', 'office'),
                    "id" => "linkedin",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Google Plus', 'office'),
                    "desc" => __('Add your Google Plus url.', 'office'),
                    "id" => "googleplus",
                    "std" => "#",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Google', 'office'),
                    "desc" => __('Add your Google url.', 'office'),
                    "id" => "google",
                    "std" => "#",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Youtube', 'office'),
                    "desc" => __('Add your youtube url.', 'office'),
                    "id" => "youtube",
                    "std" => "",
                    "type" => "text");

$of_options[] = array( "name" => __('Vimeo', 'office'),
                    "desc" => __('Add your vimeo url.', 'office'),
                    "id" => "vimeo",
                    "std" => "http://vimeo.com/user7001558",
                    "type" => "text");

$of_options[] = array( "name" => __('RSS', 'office'),
                    "desc" => __('Add your rss url.', 'office'),
                    "id" => "rss",
                    "std" => "",
                    "type" => "text");	
					
$of_options[] = array( "name" => __('Support', 'office'),
                    "desc" => __('Add your support url.', 'office'),
                    "id" => "support",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Mail', 'office'),
                    "desc" => __('Add your mail/contact url.', 'office'),
                    "id" => "mail",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Breadcrumbs', 'office'),
					"type" => "heading");	
					
$of_options[] = array( "name" => __('Enable or Disable Breadcrumbs', 'office'),
					"desc" => __('Select to enable/disable breadcrumbs navigation', 'office'),
					"id" => "disable_breadcrumbs",
					"std" => "enable",
					"type" => "select",
					"options" => $enable_disable);	
					
$of_options[] = array( "name" => __('Portfolio Page URL', 'office'),
                    "desc" => __('Enter the URL to your portfolio page. Used for breadcrumbs.', 'office'),
                    "id" => "portfolio_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Staff Page URL', 'office'),
                    "desc" => __('Enter the URL to your staff page. Used for breadcrumbs.', 'office'),
                    "id" => "staff_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Service Page URL', 'office'),
                    "desc" => __('Enter the URL to your service page. Used for breadcrumbs.', 'office'),
                    "id" => "services_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Testimonials Page URL', 'office'),
                    "desc" => __('Enter the URL to your testimonials page. Used for breadcrumbs.', 'office'),
                    "id" => "testimonials_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('FAQs Page URL', 'office'),
                    "desc" => __('Enter the URL to your FAQs page. Used for breadcrumbs.', 'office'),
                    "id" => "faqs_url",
                    "std" => "",
                    "type" => "text");
					
$of_options[] = array( "name" => __('Blog Page URL', 'office'),
                    "desc" => __('Enter the URL to your blog page. Used for breadcrumbs.', 'office'),
                    "id" => "blog_url",
                    "std" => "",
                    "type" => "text");

$of_options[] = array( "name" => __('Footer', 'office'),
					"type" => "heading");
					
$of_options[] = array( "name" => __('Enable or Disable The Widgetized Footer.', 'office'),
					"desc" => __('Select to enable or disable the widgetized footer.', 'office'),
					"id" => "disable_widgetized_footer",
					"std" => "enable",
					"type" => "select",
					"options" => $enable_disable);
					
$of_options[] = array( "name" => __('Custom Copyright', 'office'),
                    "desc" => __('Add your own custom text/html for copyright region.', 'office'),
                    "id" => "custom_copyright",
                    "std" => "",
                    "type" => "textarea");
					
$of_options[] = array( "name" => "Backup",
"type" => "heading");

$of_options[] = array( "name" => "Backup and Restore Options",
                    "desc" => __('You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back.','office'),
                    "id" => "aq_backup",
                    "std" => "",
                    "type" => "backup",
					"options" => "",
					);
	}
}
?>
