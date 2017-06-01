<?php
  function gtcm_base_url() {
    $components = explode("?", $_SERVER['REQUEST_URI']);
    $page = explode("&", $components[1]);
    return $components[0]."?".$page[0];
  }
  
  function gtcm_date($date) {
    return date('m/d/Y', strtotime($date));
  }
  
  function gtcm_widgets_init() {
    include_once(GTCM_FILE_PATH."/widget.php");
    register_widget("Hbgs_Widgets_Donations");
  }
  
  if(!function_exists('_log')){
    function _log( $message ) {
      if( WP_DEBUG === true ){
        if( is_array( $message ) || is_object( $message ) ){
          error_log( print_r( $message, true ) );
        } else {
          error_log( $message );
        }
      }
    }
  }
?>