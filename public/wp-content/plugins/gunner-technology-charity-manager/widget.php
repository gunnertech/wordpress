<?php 

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Widget
 * @subpackage Donations
 * @copyright  Copyright (c) 2011 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Donations.php 2011-03-28 merckens
 */



if(!class_exists("Hbgs_Widgets_Donations")) {
require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/html5_boilerplated_grid_system/classes/Hbgs/Widget.php';

class Hbgs_Widgets_Donations extends Hbgs_Widget {
  
  function Hbgs_Widgets_Donations() {
  	$widget_ops = array( 'classname' => 'donations-widget', 'description' => 'A widget that lets you receive user-submitted donations.' );
  	$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'donations-widget' );
  	$this->WP_Widget( 'donations-widget', 'Donations Widget', $widget_ops, $control_ops );
  	
  	add_action('wp_ajax_create_donation', array(&$this,"create_donation"));
  	add_action('wp_ajax_nopriv_create_donation', array(&$this,"create_donation"));
  }
  
  function create_donation($instance=null) {
    global $wpdb;
    
    $params = $_POST['donation'];

    require('AuthnetCIM.class.php');

    $cim = new AuthnetCIM('8BE4Vwp5ZQF', '89925XKkKdfX26A7');

    $cim->setParameter('email', $params['email']);
    $cim->createCustomerProfile();
    if ($cim->getProfileId()) {
      $profile_id = $cim->getProfileID();
    } else {
      echo("false\n");
      return;
    }

    $cim->setParameter('customerProfileId', $profile_id);
    $cim->setParameter('billToFirstName', $params['first_name']);
    $cim->setParameter('billToLastName', $params['last_name']);
    $cim->setParameter('billToAddress', $params['address']);
    $cim->setParameter('billToCity', $params['city']);
    $cim->setParameter('billToState', $params['state']);
    $cim->setParameter('billToZip', $params['zip']);
    $cim->setParameter('billToPhoneNumber', $params['phone']);
    $cim->setParameter('cardNumber', $params['credit_card']);
    $cim->setParameter('expirationDate', $params['expiration_date']);
    $cim->setParameter('validationMode', 'none');
    $cim->createCustomerPaymentProfile();
    if ($cim->getPaymentProfileId()) {
      $payment_profile_id = $cim->getPaymentProfileId();
    } else {
      $cim->setParameter('customerProfileId', $profile_id);
      $cim->deleteCustomerProfile();
      echo("false\n");
      return;
    }

    $cim->setParameter('customerProfileId', $profile_id);
    $cim->setParameter('shipToFirstName', $params['first_name']);
    $cim->setParameter('shipToLastName', $params['last_name']);
    $cim->setParameter('shipToAddress', $params['address']);
    $cim->setParameter('shipToCity', $params['city']);
    $cim->setParameter('shipToState', $params['state']);
    $cim->setParameter('shipToZip', $params['zip']);
    $cim->setParameter('shipToPhoneNumber', $params['phone']);
    $cim->createCustomerShippingAddress();
    if ($cim->getCustomerAddressId()) {
      $shipping_address_id = $cim->getCustomerAddressId();
    } else {
      $cim->setParameter('customerProfileId', $profile_id);
      $cim->deleteCustomerProfile();
      echo("false\n");
      return;
    }
    
    $params['amount'] = intval(preg_replace('/[^\d\.]/i',"",$params['amount']));
    $wpdb->query("INSERT INTO ".GTCM_DONATIONS_TABLE."
      (campaign_id, amount, achievement_id, profile_id, payment_profile_id, shipping_address_id, ccv, first_name, last_name, email, note)
      VALUES ({$params['campaign_id']}, {$params['amount']}, {$params['achievement_id']},
      {$profile_id}, {$payment_profile_id}, {$shipping_address_id}, {$params['ccv']},
      \"{$params['first_name']}\", \"{$params['last_name']}\", \"{$params['email']}\", \"{$params['note']}\");");
  }
  
  function print_default_scripts() { 
    ?>
    $(".donation-form").live('submit',function(){
      $_this = $(this);
      $.ajax({
        url:"/wp-admin/admin-ajax.php",
        type:'POST',
        data:'action=create_donation&'+$(this).serialize(),
        success:function(results) {
          if(results.match(/false/)) {
            $_this.html('<div class="error">Whoops! We\'re sorry. Something went wrong. Please try your pledge again later.</div>');
          } else {
            $_this.html('<div class="thanks">'+$_this.find(".donation-thank-you-message").val()+'</div>');
          }
        }
      });
      return false;
    });
    <?php
  }
  
  function render( $args, $instance ) {
    global $wpdb;
		extract( $args );
		
		$achievements = $wpdb->get_results("SELECT * FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE campaign_id=".$instance['campaign_id'], ARRAY_A);
		?>
		<?php echo $before_widget; ?>
		  <?php if ( $title ) echo $before_title . do_shortcode($title) . $after_title; ?>
		  <form accept-charset="UTF-8" class="donthyphenate donation-form" action="<?php echo gtcm_base_url() ?>" method="post">
		    <p>
          <span class="label-wrapper">
            <label>First Name</label>
            <span class="wpcf7-form-control-wrap your-name">
              <input type="text" size="32" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[first_name]" />
            </span>
          </span>
          
          <span class="label-wrapper">
            <label>Last Name</label>
            <span class="wpcf7-form-control-wrap title">
              <input type="text" size="32" class="wpcf7-text" value="" name="donation[last_name]" />
            </span>
          </span>
        </p>
        
        <p>
          <span class="label-wrapper">
            <label>Your Email</label>
            <span class="wpcf7-form-control-wrap your-email">
              <input type="text" size="32" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[email]" />
            </span>
          </span>
          
          <span class="label-wrapper">
            <label>Contact Phone #</label>
            <span class="wpcf7-form-control-wrap your-phone">
              <input type="text" size="32" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[phone]" />
            </span>
          </span>
        </p>
        
        <p>
          <span class="label-wrapper">
            <label>Address</label>
            <span class="wpcf7-form-control-wrap address1">
              <input type="text" size="64" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[address]" />
            </span>
          </span>
        </p>
        
        <p>
          <span class="label-wrapper">
            <label>City</label>
            <span class="wpcf7-form-control-wrap city">
              <input type="text" size="22" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[city]" />
            </span>
          </span>
          
          <span class="label-wrapper">
            <label>State or Province</label>
            <span class="wpcf7-form-control-wrap province">
              <input type="text" size="28" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[state]" />
            </span>
          </span>
          
          <span class="label-wrapper">
            <label>Zip</label>
            <span class="wpcf7-form-control-wrap zip">
              <input type="text" size="7" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[zip]" />
            </span>
          </span>
        </p>
        
        <p>
          <span class="label-wrapper">
            <label>Credit Card #</label>
            <span class="wpcf7-form-control-wrap credit_card">
              <input type="text" size="22" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[credit_card]" />
            </span>
          </span>
          
          <span class="label-wrapper">
            <label>Exp Date (YYYY-MM)</label>
            <span class="wpcf7-form-control-wrap expiration_date">
              <input type="text" size="28" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[expiration_date]" />
            </span>
          </span>
          
          <span class="label-wrapper">
            <label>CCV</label>
            <span class="wpcf7-form-control-wrap ccv">
              <input type="text" size="7" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[ccv]" />
            </span>
          </span>
        </p>
        
        <p>
          <span class="label-wrapper">
            <label>Pledge</label>
            <span class="wpcf7-form-control-wrap amount">
              <input type="text" size="32" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[amount]" id="gtcm-donation-pledge" />
            </span>
          </span>
          
          <span class="label-wrapper" id="gtcm-donation-achievements">
            <label>For Every</label>
            <span class="wpcf7-form-control-wrap province">
              <select name="donation[achievement_id]">
                <?php foreach($achievements as $achievement): ?>
                  <option value="<?php echo $achievement['id'] ?>"><?php echo $achievement['name'] ?></option>"
                <?php endforeach; ?>
              </select>
            </span>
          </span>
        </p>
        
        <p id="gtcm-donation-note">
          <span class="label-wrapper">
            <label id="gtcm-donation-note-text">Note</label>
            <span class="wpcf7-form-control-wrap note">
              <input type="text" size="70" class="wpcf7-text wpcf7-validates-as-required" value="" name="donation[note]" />
            </span>
          </span>
        </p>
        
        <p>
          <input type="hidden" name="donation[campaign_id]" value="<?php echo esc_attr($instance['campaign_id']) ?>" />
          <input type="hidden" class="donation-thank-you-message" value="<?php echo esc_attr($instance['thank_you_message']) ?>" />
          <input type="image" src="/wp-content/uploads/submit-button.png" />
        </p>
		  </form>
    <?php echo $after_widget ?>
    <?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['thank_you_message'] = $new_instance['thank_you_message'];
		$instance['campaign_id'] = intval($new_instance['campaign_id']);
		
		return $instance;
	}
	
	function form($instance) {
	  global $wpdb;
		$defaults = array('thank_you_message' => 'Thank you for your pledge!');
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		$campaigns = $wpdb->get_results("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE, ARRAY_A);
		?>
		<p>
		  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
  	</p>
	  <p>
      <label for="<?php echo $this->get_field_id('thank_you_message'); ?>"><?php _e('Thank you message:'); ?></label>
    	<input class="widefat" id="<?php echo $this->get_field_id('thank_you_message'); ?>" name="<?php echo $this->get_field_name('thank_you_message'); ?>" type="text" value="<?php echo esc_attr($instance['thank_you_message']); ?>" />
    </p>
	  <p>
		  <label for="<?php echo $this->get_field_id('campaign_id'); ?>"><?php _e('Campaign:'); ?></label>
		  <small>(<a href="/wp-admin/admin.php?page=gtcm-campaigns">Add a Campaign</a>)</small><br />
		  <select id="<?php echo $this->get_field_id('campaign_id'); ?>" name="<?php echo $this->get_field_name('campaign_id'); ?>">
		    <option value=""></option>
		    <?php foreach($campaigns as $campaign): ?>
		      <option value="<?php echo $campaign['id'] ?>" <?php selected($instance['campaign_id'], $campaign['id']) ?>><?php echo $campaign['name'] ?></option>
		    <?php endforeach; ?>
		  </select>
  	</p>
		<?php
  }
}
} ?>