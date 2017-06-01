<?php

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Custom_Post_Type
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Custom_Post_Type.php 2010-11-18 codyswann
 */

abstract class Hbgs_Custom_Post_Type {
  protected $data = array();
  protected $include_category_support = true;
  
  public static function pluralize( $string ) {

    $plural = array(
      array( '/(quiz)$/i',               "$1zes"   ),
      array( '/^(ox)$/i',                "$1en"    ),
      array( '/([m|l])ouse$/i',          "$1ice"   ),
      array( '/(matr|vert|ind)ix|ex$/i', "$1ices"  ),
      array( '/(x|ch|ss|sh)$/i',         "$1es"    ),
      array( '/([^aeiouy]|qu)y$/i',      "$1ies"   ),
      array( '/([^aeiouy]|qu)ies$/i',    "$1y"     ),
      array( '/(hive)$/i',               "$1s"     ),
      array( '/(?:([^f])fe|([lr])f)$/i', "$1$2ves" ),
      array( '/sis$/i',                  "ses"     ),
      array( '/([ti])um$/i',             "$1a"     ),
      array( '/(buffal|tomat)o$/i',      "$1oes"   ),
      array( '/(bu)s$/i',                "$1ses"   ),
      array( '/(alias|status)$/i',       "$1es"    ),
      array( '/(octop|vir)us$/i',        "$1i"     ),
      array( '/(ax|test)is$/i',          "$1es"    ),
      array( '/s$/i',                    "s"       ),
      array( '/$/',                      "s"       )
    );

    $irregular = array(
      array( 'move',   'moves'    ),
      array( 'sex',    'sexes'    ),
      array( 'child',  'children' ),
      array( 'man',    'men'      ),
      array( 'person', 'people'   )
    );

    $uncountable = array( 
      'sheep', 
      'fish',
      'series',
      'species',
      'money',
      'rice',
      'information',
      'equipment'
    );

    // save some time in the case that singular and plural are the same
    if ( in_array( strtolower( $string ), $uncountable ) )
      return $string;

    // check for irregular singular forms
    foreach ( $irregular as $noun ) {
      if ( strtolower( $string ) == $noun[0] )
        return $noun[1];
    }

    // check for matches using regular expressions
    foreach ( $plural as $pattern ) {
      if ( preg_match( $pattern[0], $string ) )
        return preg_replace( $pattern[0], $pattern[1], $string );
    }
      
    return $string;
  }
  
  public function __set($name, $value) {
    $this->data[$name] = $value;
  }
  
  protected function nameToType() {
    $class_name = str_replace("Hbgs_Custom_Post_Types_","",str_replace("Hbgs_Custom_Post_Types_","",get_class($this)));
    return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $class_name));
  }
  
  protected function registerTaxonomies() {
    if($this->include_category_support) {
      register_taxonomy_for_object_type('category',$this->nameToType());
    }
    if(isset($this->taxonomies) && is_array($this->taxonomies)) {
      foreach($this->taxonomies as $taxonomy) {
        register_taxonomy(
        	$taxonomy['internal_name'],
        	$this->nameToType(),
        	array(
        		'hierarchical' => $taxonomy['hierarchical'],
        		'label' => $taxonomy['label'],
        		'query_var' => $taxonomy['query_var'],
        		'rewrite' => $taxonomy['rewrite'],
        		'labels' => $taxonomy['labels']
        	)
        );
      }
    }
  }
  
  public function __get($name) {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }
    return null;
  }
  
  public function __isset($name) {
    return isset($this->data[$name]);
  }
  
  public function __unset($name) {
    unset($this->data[$name]);
  }
  
  function __construct($register_params=array()) {
    $this->register_params = $register_params;
  }
  
  public function getItems($args=array()) {
    return get_posts(array_merge(array("post_type" => $this->nameToType()), $args));
  }
  
  public function getItemsForLoop($args=array()) {
    $q = new WP_Query();
    $q->query(array_merge(array("post_type" => $this->nameToType()), $args));
    return $q;
  }
  
  public function __call($method_name,$args) { 
    global $post;
    $field = str_replace("save_", "", $method_name);
    update_post_meta($post->ID, $field, $_POST[$field]);
  }
  
  public function register() {
    register_post_type( $this->nameToType() , $this->getRegisterArgs() );
    $this->registerTaxonomies();
    // admin_init
    add_action("admin_menu", array($this, 'setUpMenu'));
  }
  
  public function setUpMenu() {
    
  }
  
  protected function getRegisterArgs() {
    $type = $this->nameToType();
    $name = ucwords(str_replace("_"," ",$type));
    $lower_name = strtolower($name);
    $class = get_class($this);
    $plural_name = Hbgs_Custom_Post_Type::pluralize($name);
    
    $labels = array(
  		'name' => _x($plural_name, 'post type general name'),
  		'singular_name' => _x($name.' Item', 'post type singular name'),
  		'add_new' => _x('Add New', $lower_name.' item'),
  		'add_new_item' => __('Add New '.$name),
  		'edit_item' => __('Edit '.$name.' Item'),
  		'new_item' => __('New '.$name.' Item'),
  		'view_item' => __('View '.$name.' Item'),
  		'search_items' => __('Search '.$plural_name),
  		'not_found' =>  __('Nothing found'),
  		'not_found_in_trash' => __('Nothing found in Trash'),
  		'parent_item_colon' => ''
  	);
    
  	return array_merge(array(
  		'labels' => $labels,
  		'public' => true,
  		'publicly_queryable' => true,
  		'show_ui' => true,
  		'query_var' => true,
  		'menu_icon' => get_template_directory_uri() . '/images/'.$type.'.gif',
  		'rewrite' => array( 'slug' => str_replace(" ","-",$lower_name), 'with_front' => false ),
  		'capability_type' => 'post',
  		'hierarchical' => false,
  		'menu_position' => null,
  		'supports' => array('title','editor','author','thumbnail','excerpt','comments')
  	),$this->register_params);
  }
  
  public function customMeta($post,$args) { $custom = get_post_custom($post->ID); $value = $custom[$args['args']["slug"]][0];?>
      <label><?php echo $args['args']["label"] ?>:</label>
      <?php if($args['args']['field_type'] == 'select'): ?>
        <select name="<?php echo $args['args']["slug"] ?>">
          <?php if($args['args']['options'] && is_array($args['args']['options'])): ?>
            <?php foreach($args['args']['options'] as $option): ?>
              <option value="<?php echo htmlentities($option); ?>" <?php selected(htmlentities($value),$option) ?>><?php echo $option ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      <?php elseif($args['args']['field_type'] == 'checkbox'): ?>
        <input type="checkbox" name="<?php echo $args['args']["slug"] ?>" <?php checked(htmlentities($value)=='on') ?> />
      <?php else: ?>
        <input type="text" name="<?php echo $args['args']["slug"] ?>" value="<?php echo htmlentities($value); ?>" />
      <?php endif; ?>
  <?php }
  
  public function addField($args) {
    add_meta_box($args["slug"], $args["label"], array($this,"customMeta"), $this->nameToType(), "side", "high",$args);
    add_action('save_post', array($this,"save_".$args["slug"]));
  }
}