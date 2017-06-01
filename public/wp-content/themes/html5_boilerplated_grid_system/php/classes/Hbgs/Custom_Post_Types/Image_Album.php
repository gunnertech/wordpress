<?php

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Custom_Post_Type
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Audio_Album.php 2010-11-18 codyswann
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/html5_boilerplated_grid_system/php/classes/Hbgs/Custom_Post_Type.php';

class Hbgs_Custom_Post_Types_Image_Album extends Hbgs_Custom_Post_Type {
  public function setUpMenu() {
    $this->addField(array("label" => "Thumbnail Size", "slug" => "_image_album_thumbnail_size", "field_type" => "select", "options" => hbgs_image_sizes()));
  }
}