<?php
// Default Sidebar
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'name' => 'Default Sidebar',
		'id' => 'default_sidebar',
		'before_widget' => '<div class="sidebar-widget"><aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside></div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	)
);
// Social Header Sidebar
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'name' => 'Social Header',
		'id' => 'social_header',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	)
);
// Dynamic Sidebar
for ($sidebar_count=1; $sidebar_count <=MAX_SIDEBARS; $sidebar_count++ ) {

	if ( of_get_option('theme_sidebar'.$sidebar_count) <> "" ) {
		if ( function_exists('register_sidebar') )
			register_sidebar(array(
				'name' => of_get_option('theme_sidebar'.$sidebar_count),
				'id' => 'sidebar_' . $sidebar_count . '',
				'before_widget' => '<div class="sidebar-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside></div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			)
		);
	}
}

?>