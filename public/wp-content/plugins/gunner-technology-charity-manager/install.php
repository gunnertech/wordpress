<?php

function gtcm_install() {
  global $wpdb;
  
  gtcm_create_tables();
}

function gtcm_create_tables() {
  global $wpdb;
  
  include(GTCM_FILE_PATH.'/schema.php');
  
  foreach((array)$gtcm_db as $table_name => $table_data) {
    if(!$wpdb->get_var("SHOW TABLES LIKE {$table_name}")) {
      $constructed_sql_parts = array();
      $constructed_sql = "CREATE TABLE {$table_name} (\n";
    
      foreach((array)$table_data['columns'] as $column => $properties)
        $constructed_sql_parts[] = "$column $properties";
      foreach((array)$table_data['indexes'] as $properties)
        $constructed_sql_parts[] = "$properties";
      $constructed_sql .= implode(",\n", $constructed_sql_parts);
      $constructed_sql .= "\n) ENGINE=MyISAM CHARSET=utf8;";
      
      $wpdb->query($constructed_sql);
    }
  }
}

?>