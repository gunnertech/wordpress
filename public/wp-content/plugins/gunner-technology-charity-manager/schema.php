<?php

$table_name = GTCM_ACHIEVEMENTS_TABLE;
$gtcm_db[$table_name]['columns']['id'] = "BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT";
$gtcm_db[$table_name]['columns']['name'] = "VARCHAR(255) NOT NULL";
$gtcm_db[$table_name]['columns']['count'] = "SMALLINT UNSIGNED NOT NULL DEFAULT 0";
$gtcm_db[$table_name]['columns']['campaign_id'] = "BIGINT(20) NOT NULL";
$gtcm_db[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY (id)";
$gtcm_db[$table_name]['indexes']['campaign_id'] = "KEY campaign_id (campaign_id)";

$table_name = GTCM_CAMPAIGNS_TABLE;
$gtcm_db[$table_name]['columns']['id'] = "BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT";
$gtcm_db[$table_name]['columns']['name'] = "VARCHAR(255) NOT NULL";
$gtcm_db[$table_name]['columns']['description'] = "TEXT NOT NULL DEFAULT ''";
$gtcm_db[$table_name]['columns']['start_date'] = "DATE";
$gtcm_db[$table_name]['columns']['end_date'] = "DATE";
$gtcm_db[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY (id)";

$table_name = GTCM_DONATIONS_TABLE;
$gtcm_db[$table_name]['columns']['id'] = "BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT";
$gtcm_db[$table_name]['columns']['campaign_id'] = "BIGINT(20) UNSIGNED NOT NULL";
$gtcm_db[$table_name]['columns']['amount'] = "DECIMAL(11,2) NOT NULL DEFAULT 0";
$gtcm_db[$table_name]['columns']['achievement_id'] = "BIGINT(20) UNSIGNED";
$gtcm_db[$table_name]['columns']['profile_id'] = "BIGINT(20) UNSIGNED";
$gtcm_db[$table_name]['columns']['payment_profile_id'] = "BIGINT(20) UNSIGNED";
$gtcm_db[$table_name]['columns']['shipping_address_id'] = "BIGINT(20) UNSIGNED";
$gtcm_db[$table_name]['columns']['ccv'] = "VARCHAR(255) NOT NULL";
$gtcm_db[$table_name]['columns']['charged'] = "BOOLEAN NOT NULL DEFAULT 0";
$gtcm_db[$table_name]['columns']['first_name'] = "VARCHAR(255) NOT NULL";
$gtcm_db[$table_name]['columns']['last_name'] = "VARCHAR(255) NOT NULL";
$gtcm_db[$table_name]['columns']['email'] = "VARCHAR(255) NOT NULL";
$gtcm_db[$table_name]['columns']['note'] = "TEXT";
$gtcm_db[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY (id)";
$gtcm_db[$table_name]['indexes']['campaign_id'] = "KEY campaign_id (campaign_id)";
$gtcm_db[$table_name]['indexes']['achievement_id'] = "KEY achievement_id (achievement_id)";
?>