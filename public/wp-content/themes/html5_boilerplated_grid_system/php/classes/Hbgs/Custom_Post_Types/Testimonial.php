<?php

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Custom_Post_Type
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Testimonial.php 2010-11-18 codyswann
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/html5_boilerplated_grid_system/php/classes/Hbgs/Custom_Post_Type.php';

class Hbgs_Custom_Post_Types_Testimonial extends Hbgs_Custom_Post_Type {
  public function setUpMenu() {
    $this->addField(array("label" => "From", "slug" => "_testimonial_from_meta"));
    $this->addField(array("label" => "Company", "slug" => "_testimonial_company_meta"));
    $this->addField(array("label" => "Title", "slug" => "_testimonial_title_meta"));
    $this->addField(array("label" => "URL", "slug" => "_testimonial_url_meta"));
  }
}