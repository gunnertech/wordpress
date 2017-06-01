<?php

// ***********************************************************
// Author: Pascal Gill
//         http://blog.dreamdevil.com/
// ***********************************************************
// LICENSE:
//
//            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
//                    Version 2, December 2004
//
// Copyright (C) 2004 Sam Hocevar
//  14 rue de Plaisance, 75014 Paris, France
// Everyone is permitted to copy and distribute verbatim or modified
// copies of this license document, and changing it is allowed as long
// as the name is changed.
//
//            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
//   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
//
//  0. You just DO WHAT THE FUCK YOU WANT TO.
//
// -----------------------------------------------------------
//
// This program is free software. It comes without any warranty, to
// the extent permitted by applicable law. You can redistribute it
// and/or modify it under the terms of the Do What The Fuck You Want
// To Public License, Version 2, as published by Sam Hocevar. See
// http://sam.zoy.org/wtfpl/COPYING for more details. 
//
// ***********************************************************


// this file accepts the following parameters:
// * async=1|0
//      1: perform asynchronous call to wp-cron.php
//      0: perform synchronouse call to wp-cron.php
//
// example: http://www.server.com/wp-cron-multi-blog.php?async=1

// Load wordpress api.
if ( !defined('ABSPATH') ) {
	/** Setup WordPress environment */
	require_once('./wp-load.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Cron</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<meta HTTP-EQUIV="Expires" CONTENT="-1" />
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<meta HTTP-EQUIV="Cache" Content="no store" />
</head> 
<body>
<?php

// store the start time so we can evaluate the total time of execution.
// specially usefull to prevent scheduling in shorter period than it takes to run.
$startTime = microtime(true);

// Extract an array of all active blogs
$blogs = crontab_get_blogs();
if ($blogs && count($blogs) > 0)
{
    // for each blog in the system
	foreach ($blogs as $blog)
	{
	    $cronStartTime = microtime(true);
	    
		echo date('Y/m/d H:i:s', $cronStartTime) . " Starting wp-cron for $blog->domain"."$blog->path...";
		$cron_url = $blog->domain . $blog->path . 'wp-cron.php?doing_wp_cron';
		// perform the call to wp-cron.php
		// wp_remote_post parameters values were taken from the original call in wordpress 
		// (see spawn_cron() function declaration)
		wp_remote_post( $cron_url, array('timeout' => 0.01, 'blocking' => false, 'sslverify' => false) );
		
    	$cronEndTime = microtime(true);
    	$duration = $cronEndTime - $cronStartTime;
    	echo " (completed in ". $duration ." seconds)<br/>\r\n";
	}
}

// delete_expired_db_transients();

$endTime = microtime(true);
$duration = $endTime - $startTime;
echo "<br/>\r\nAll tasks completed in ". $duration ." seconds<br/>\r\n";



// ***********************************************************************
//  Function declarations
// ***********************************************************************

function crontab_get_blogs() 
{
	global $wpdb;

	$sql = "SELECT blog_id, domain, path FROM $wpdb->blogs WHERE public = 1 AND deleted = 0 AND archived = '0' ORDER BY domain, path";
	$result = $wpdb->get_results($sql);
	return ($result);
}

function delete_expired_db_transients() {

    global $wpdb, $_wp_using_ext_object_cache;

    // if( $_wp_using_ext_object_cache )
    //     return;

    $time = isset ( $_SERVER['REQUEST_TIME'] ) ? (int)$_SERVER['REQUEST_TIME'] : time() ;
    $expired = $wpdb->get_col( "SELECT meta_key FROM wp_sitemeta WHERE meta_key LIKE '_site_transient_timeout%' AND meta_value < {$time};" );

    foreach( $expired as $transient ) {

        $key = str_replace('_site_transient_timeout_', '', $transient);
        delete_site_transient($key);
    }
}

?>
</body>
</html>
