<?php
/*
* Footer
*/
?>

<div class="clear"></div>
<?php
global $fullscreen_status,$fullscreen_type;
if ( $fullscreen_status<>"enable" ) {
echo '</div>';
echo '</div>';
echo '<span class="control-underlay"></span>';
echo '<div class="slidshowbackground-panel"></div>';
}
?>
<?php
if ( $fullscreen_type != "Fullscreen-Video" ) {
?>
<div id="fullscr-copyright">
	<ul>
	<?php
	$copyright_text= stripslashes_deep( of_get_option('footer_copyright') );
	 
	$eachline=explode("\n",$copyright_text);
	 
	foreach($eachline as $key=>$value)
	{
	   $value=trim($value);
	   if  (!empty($value))
	   {
			echo '<li>'.$value.'</li>';
	   }
	}
	?>
	</ul>
</div>
<?php
}
?>
<?php
wp_footer();
?>
<?php echo stripslashes_deep( of_get_option ( 'footer_scripts' ) ); ?>
</body>
</html>