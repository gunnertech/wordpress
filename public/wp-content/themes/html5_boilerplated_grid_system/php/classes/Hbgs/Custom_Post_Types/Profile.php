<?php

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Custom_Post_Type
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Profile.php 2010-11-18 codyswann
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/html5_boilerplated_grid_system/classes/Hbgs/Custom_Post_Type.php';

class Hbgs_Custom_Post_Types_Profile extends Hbgs_Custom_Post_Type {
  public function setUpMenu() {
    $this->addField(array("label" => "Name", "slug" => "_profile_name_meta"));
    $this->addField(array("label" => "Title", "slug" => "_profile_title_meta"));
    $this->addField(array("label" => "Twitter URL", "slug" => "_twitter_url_meta"));
    $this->addField(array("label" => "Facebook URL", "slug" => "_facebook_url_meta"));
    $this->addField(array("label" => "LinkedIn URL", "slug" => "_linkedin_url_meta"));
  }
}