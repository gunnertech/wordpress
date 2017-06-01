<?php

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Custom_Post_Type
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Product.php 2010-11-18 codyswann
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/html5_boilerplated_grid_system/classes/Hbgs/Custom_Post_Type.php';

class Hbgs_Custom_Post_Types_Product extends Hbgs_Custom_Post_Type {
  function __construct() {
    // $this->taxonomies = array(
    //   array(
    //     'internal_name' => "product_category",
    //     'label' => "Product Category",
    //     'query_var' => true,
    //     'rewrite' => true,
    //     'hierarchical' => true,
    //     'labels' => array(
    //       'name' => "Product Categories",
    //       'singular_name' => _x('Product Cateogry', 'product_category_singular_name'),
    //       'choose_from_most_used' => _x('Choose from the most used categories', 'product_category_choose_from_most_used'),
    //       'add_or_remove_items' => _x('Add or remove categories', 'product_category_add_or_remove_categories'),
    //     )
    //   )
    // );
    parent::__construct();
  }
  
  public function setUpMenu() {
    $this->addField(array("label" => "Price", "slug" => "_product_price"));
    $this->addField(array("label" => "SKU", "slug" => "_product_sku"));
    $this->addField(array("label" => "Quantity Available", "slug" => "_product_quantity_available"));
    $this->addField(array("label" => "In Stock?", "slug" => "_product_in_stock", 'field_type' => 'checkbox'));
    $this->addField(array("label" => "Product Thumbnail Size", "slug" => "_product_thumbnail_size", "field_type" => "select", "options" => hbgs_image_sizes()));
    $this->addField(array("label" => "Product Image Size", "slug" => "_product_image_size", "field_type" => "select", "options" => hbgs_image_sizes()));
  }
  
}