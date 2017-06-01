<?php
//If delete sidebar
if(isset($_POST['sidebar_id']) && !empty($_POST['sidebar_id']))
{
	$current_sidebar = get_option('nm_sidebar');
	
	if(isset($current_sidebar[ $_POST['sidebar_id'] ]))
	{
		unset($current_sidebar[ $_POST['sidebar_id'] ]);
		update_option( "nm_sidebar", $current_sidebar );
	}
	
	echo 1;
	exit;
}
?>