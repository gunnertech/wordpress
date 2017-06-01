<?php

global $current_user;
get_currentuserinfo();
$user_info = cjpopups_user_info($current_user->ID);
$item_name = cjpopups_item_info('item_name');
$item_type = cjpopups_item_info('item_type');

$quick_setup_quide_link = '<a href="'.cjpopups_item_info('quick_start_guide_url').'" target="_blank">Quick Start Guide</a>';
$menu_icon = '<img src="'.cjpopups_item_path('framework_url').'/assets/admin/img/menu-icon.png" width="16" />';

$welcome_msg = <<<EOF
<p>
<b>Hello {$user_info['display_name']}</b>,<br>
Thank you for using our {$item_name} WordPress plugin.
</p>
<p>
I am here to assist you setting up this plugin on your website and I'll also show you some awesome features that comes with this plugin.
</p>
<p>
If you like, you can also check out our {$quick_setup_quide_link} to setup this pugin and learn more about the features.
</p>
EOF;

$end_tour_msg = <<<EOF
<p>
<b>Thank You, {$user_info['display_name']}</b>,<br>
</p>
<p>
Its been nice interacting with you. In case you need me again,<br> you can call me from <b>Help & Support</b> menu from the navigation on plugin settigns page.
</p>
<p>
Here are a few useful links for further help and contact with CSSJockey team.
</p>
<ul>
<li><a href="http://demo.cssjockey.com/cjpopups/quick-start-guide/" target="_blank">Quick Start Guide</a></li>
<li><a href="http://demo.cssjockey.com/cjpopups" target="_blank">Documentation</a></li>
<li><a href="http://cssjockey.com/support" target="_blank">Support Fourm</a></li>
<li><a href="http://cssjockey.com/support" target="_blank">Feature Requests</a></li>
<li><a href="http://cssjockey.com/support" target="_blank">Report Bugs</a></li>
</ul>
<p>
<h4>Good luck with your project.</h4>
</p>
EOF;

$manage_popups = <<<EOF
<h3>Create or Edit Pop-Ups</h3>
<p>
Here you can create, edit and delete Pop-Ups as we normally do with other posts or pages.
</p>
<p>
Once you are on create new or edit screen, You will see some additional meta boxes to specify HTML,CSS or Javascript and manage Pop-Up options.
</p>
<p>
Plese go through these options and try creating a Pop-Up with different options to see all the awesome stuff that comes with this plugin.
</p>
<p>Check out <a href="http://demo.cssjockey.com/cjpopups/popup-options-overview/" target="_blank">Pop-Up options overview</a> here to learn more about each option for a pop-up.</p>
EOF;



$cjpopups_assistant_steps[] = array(
	'id' => 'welcome',
	'text' => $welcome_msg,
	'button_text' => 'Manage Pop-Ups',
	'callback' => admin_url('edit.php?post_type=popups'),
	'cancel_text' => 'No, I will check out my self.',
	'cancel_link' => cjpopups_assistant_url('end-tour', cjpopups_callback_url('core_welcome')),
);
$cjpopups_assistant_steps[] = array(
	'id' => 'manage-popups',
	'text' => $manage_popups,
	'button_text' => 'Ok, Let\'s create a Pop-Up',
	'callback' => admin_url('post-new.php?post_type=popups'),
	'cancel_text' => 'End Tour',
	'cancel_link' => cjpopups_assistant_url('end-tour', cjpopups_callback_url('core_welcome')),
);
