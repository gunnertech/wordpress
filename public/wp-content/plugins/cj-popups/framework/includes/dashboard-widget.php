<?php
/**
 * @package CSSJockey WordPress Framework
 * @author Mohit Aneja (cssjockey.com)
 * @version FW-20150208
*/
function cjpopups_add_dashboard_widgets() {
	wp_add_dashboard_widget(
                 'cjpopups_dashboard_widget',         // Widget slug.
                 'cjpopups',         // Title.
                 'cjpopups_dashboard_widget_content' // Display function.
        );
}
// add_action( 'wp_dashboard_setup', 'cjpopups_add_dashboard_widgets' );

function cjpopups_dashboard_widget_content() {
	$display[] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget.</p>';
	$display[] = '<h4>Comments</h4>';
	$display[] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget.</p>';
	echo implode('', $display);
}
