<?php
function gtcm_donations() {
  if (isset($_GET['id'])) {
  	if ($_GET['action'] == 'show')
  	  gtcm_donations_show();
    elseif ($_GET['action'] == 'edit')
  	  gtcm_donations_edit();
    elseif ($_GET['action'] == 'delete')
      gtcm_donations_delete();
  } else {
    if ($_GET['action'] == 'new')
      gtcm_donations_new();
    elseif ($_POST['action'] == 'create')
      gtcm_donations_create();
    elseif ($_POST['action'] == 'update')
  	  gtcm_donations_update();
    else
      gtcm_donations_index();
  }
}
?>

<?php
function gtcm_donations_index() {
  global $wpdb;
  $donations = $wpdb->get_results("SELECT * FROM ".GTCM_DONATIONS_TABLE, ARRAY_A);
?>
<h1>Listing Donations</h1>

<table class="gtcm-table">
  <thead>
    <tr>
      <th>Campaign</th>
      <th>Amount</th>
      <th>Achievement</th>
      <th>Total</th>
      <th>Charged</th>
      <th>Name</th>
      <th>Email</th>
      <th>Note</th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
  </thead>

<?php foreach($donations as $donation) { 
  $campaign = $wpdb->get_row("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE." WHERE id = ".$donation['campaign_id'], ARRAY_A);
  $achievement = $wpdb->get_row("SELECT * FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE id = ".$donation['achievement_id'], ARRAY_A);
?>
  <tbody>
    <tr>
      <td><?php echo $campaign['name']; ?></td>
      <td><?php echo $donation['amount']; ?></td>
      <td><?php echo $achievement['name']; ?></td>
      <td><?php echo number_format($donation['amount'] * $achievement['count'], 2); ?></td>
      <td><?php echo $donation['charged'] ? 'Yes' : 'No'; ?></td>
      <td><?php echo "{$donation['first_name']} {$donation['last_name']}"; ?></td>
      <td><?php echo $donation['email']; ?></td>
      <td><?php echo $donation['note']; ?></td>
      <td><a href='<?php echo gtcm_base_url().'&action=show&id='.$donation['id'] ?>'>Show</a></td>
      <td><a href='<?php echo gtcm_base_url().'&action=edit&id='.$donation['id'] ?>'>Edit</a></td>
      <td><a href='#' class='gtcm-delete' id='gtcm-delete-<?php echo $donation['id'] ?>'>Delete</a></td>
    </tr>
  </tbody>
<?php } ?>
</table>

<br />

<a href='<?php echo gtcm_base_url().'&action=new' ?>'>New Donation</a>

<?php } ?>

<?php
function gtcm_donations_new() {
  global $wpdb;
  
  $campaigns = $wpdb->get_results("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE, ARRAY_A);
  $campaign_options = '';
  foreach($campaigns as $campaign)
    $campaign_options .= "<option value='{$campaign['id']}'>{$campaign['name']}</option>";
    
  $achievements = $wpdb->get_results("SELECT * FROM ".GTCM_ACHIEVEMENTS_TABLE, ARRAY_A);
  foreach($achievements as $achievement)
    $achievement_options[$achievement['campaign_id']] .= "<option value='{$achievement['id']}'>{$achievement['name']}</option>";
  foreach((array)$achievement_options as $campaign_id => $options)
    echo <<<EOL
    <div id="achievements-{$campaign_id}" style="display:none">
      <label for="donation_achievement_id">Achievement</label><br />
      <select id="donation_achievement_id" name="donation[achievement_id]" type="select">
        <option />
        {$options}
      </select>
    </div>
EOL;
?>

<form accept-charset="UTF-8" action="<?php echo gtcm_base_url() ?>" method="post">
  <div class="field">
    <label for="donation_campaign_id">Campaign</label><br />
    <select id="donation_campaign_id" name="donation[campaign_id]" type="select">
      <option>Choose Campaign</option>
      <?php echo $campaign_options ?>
    </select>
  </div>
  <div class="field">
    <label for="donation_amount">Amount</label><br />
    <input id="donation_amount" name="donation[amount]" size="30" type="text" />
  </div>
  <div class="field" id="donation-achievement">
    <input type="hidden" name="donation[achievement_id]" value="" />
  </div>
  <div class="field">
    <label for="donation_first_name">First Name</label><br /> 
    <input id="donation_first_name" name="donation[first_name]" size="30" type="text" /> 
  </div>
  <div class="field">
    <label for="donation_last_name">Last Name</label><br />
    <input id="donation_last_name" name="donation[last_name]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="donation_email">Email</label><br />
    <input id="donation_email" name="donation[email]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="donation_phone">Phone</label><br />
    <input id="donation_phone" name="donation[phone]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="donation_address">Address</label><br />
    <input id="donation_address" name="donation[address]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="donation_city">City</label><br />
    <input id="donation_city" name="donation[city]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="donation_state">State</label><br />
    <input id="donation_state" name="donation[state]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="donation_zip">Zip</label><br />
    <input id="donation_zip" name="donation[zip]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="donation_note">Note</label><br>
    <textarea cols="40" id="donation_note" name="donation[note]" rows="20"></textarea>
  </div>
  <div class="field">
    <label for="donation_credit_card">Credit Card Number</label><br>
    <input id="donation_credit_card" name="donation[credit_card]" size="30" />
  </div>
  <div class="field">
    <label for="donation_expiration_date">Expiration Date (YYYY-MM)</label><br>
    <input id="donation_expiration_date" name="donation[expiration_date]" size="30" />
  </div>
  <div class="field">
    <label for="donation_ccv">CCV</label><br>
    <input id="donation_ccv" name="donation[ccv]" size="30" />
  </div>
  <div class="actions">
    <input type="hidden" name="action" value="create" />
    <input id="donation_submit" name="commit" type="submit" value="Create Donation" />
  </div>
</form>

<?php } ?>

<?php
function gtcm_donations_create() {
  global $wpdb;
  
  $params = $_POST['donation'];

  require('AuthnetCIM.class.php');

  $cim = new AuthnetCIM('8BE4Vwp5ZQF', '89925XKkKdfX26A7');

  $cim->setParameter('email', $params['email']);
  $cim->createCustomerProfile();
  if ($cim->getProfileId()) {
    $profile_id = $cim->getProfileID();
  } else {
    gtcm_donations_index();
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
    gtcm_donations_index();
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
    $shipping_profile_id = $cim->getCustomerAddressId();
  } else {
    $cim->setParameter('customerProfileId', $profile_id);
    $cim->deleteCustomerProfile();
    gtcm_donations_index();
  }
  
  $wpdb->query("INSERT INTO ".GTCM_DONATIONS_TABLE."
    (campaign_id, amount, achievement_id, profile_id, payment_profile_id, customer_address_id, ccv, first_name, last_name, email, note)
    VALUES ({$params['campaign_id']}, {$params['amount']}, {$params['achievement_id']},
    {$profile_id}, {$payment_profile_id}, {$shipping_profile_id}, {$params['ccv']},
    \"{$params['first_name']}\", \"{$params['last_name']}\", \"{$params['email']}\", \"{$params['note']}\");");
    
  gtcm_donations_index();
} ?>

<?php
function gtcm_donations_show() {
  global $wpdb;
  $donation = $wpdb->get_row("SELECT * FROM ".GTCM_DONATIONS_TABLE." WHERE id = ".$_GET['id'], ARRAY_A);
  $campaign = $wpdb->get_row("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE." WHERE id = ".$donation['campaign_id'], ARRAY_A);
  $achievement = $wpdb->get_row("SELECT * FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE id = ".$donation['achievement_id'], ARRAY_A);
?>

<p id="notice"></p>

<p>
  <b>Campaign:</b>
  <?php echo $campaign['name']; ?>
</p>

<p>
  <b>Amount:</b>
  <?php echo $donation['amount']; ?>
</p>

<p>
  <b>Achievement:</b>
  <?php echo $achievement['name']; ?>
</p>

<p>
  <b>Total:</b>
  <?php echo number_format($donation['amount'] * $achievement['count'], 2); ?>
</p>

<p>
  <b>Profile ID:</b>
  <?php echo $donation['profile_id'] ?>
</p>

<p>
  <b>Payment Profile ID:</b>
  <?php echo $donation['payment_profile_id'] ?>
</p>

<p>
  <b>Shipping Address ID:</b>
  <?php echo $donation['customer_address_id'] ?>
</p>

<p>
  <b>CCV:</b>
  <?php echo $donation['ccv'] ?>
</p>

<p>
  <b>Charged:</b>
  <?php echo $donation['charged'] ? 'Yes' : 'No'; ?>
</p>

<p>
  <b>Note:</b>
  <?php echo $donation['note'] ?>
</p>

<a href='<?php echo gtcm_base_url().'&action=edit&id='.$donation['id'] ?>'>Edit</a> | <a href='<?php echo gtcm_base_url() ?>'>Index</a>

<?php } ?>

<?php
function gtcm_donations_edit() {
  global $wpdb;
  $donation = $wpdb->get_row("SELECT * FROM ".GTCM_DONATIONS_TABLE." WHERE id = ".$_GET['id'], ARRAY_A);
  
  $campaigns = $wpdb->get_results("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE, ARRAY_A);
  $campaign_options = '';
  foreach($campaigns as $campaign) {
    $selected = $campaign['id'] == $donation['campaign_id'] ? " selected='selected'" : "";
    $campaign_options .= "<option value='{$campaign['id']}'{$selected}>{$campaign['name']}</option>";
  }

  $achievements = $wpdb->get_results("SELECT * FROM ".GTCM_ACHIEVEMENTS_TABLE, ARRAY_A);
  foreach($achievements as $achievement) {
    $selected = $achievement['id'] == $donation['achievement_id'] ? " selected='selected'" : "";
    $achievement_options[$achievement['campaign_id']] .= "<option value='{$achievement['id']}'{$selected}>{$achievement['name']}</option>";
  }
  foreach((array)$achievement_options as $campaign_id => $options)
    echo <<<EOL
    <div id="achievements-{$campaign_id}" style="display:none">
      <label for="donation_achievement_id">Achievement</label><br />
      <select id="donation_achievement_id" name="donation[achievement_id]" type="select">
        <option value='' />
        {$options}
      </select>
    </div>
EOL;
?>

<form accept-charset="UTF-8" action="<?php echo gtcm_base_url() ?>" method="post">
  <div class="field">
    <label for="donation_campaign_id">Campaign</label><br />
    <select id="donation_campaign_id" name="donation[campaign_id]" type="select">
      <option>Choose Campaign</option>
      <?php echo $campaign_options ?>
    </select>
  </div>
  <div class="field">
    <label for="donation_amount">Amount</label><br />
    <input id="donation_amount" name="donation[amount]" size="30" type="text" value="<?php echo $donation['amount']; ?>" />
  </div>
  <div class="field" id="donation-achievement">
    <input type="hidden" name="donation[achievement_id]" value="0" />
  </div>
  <div class="field">
    <label for="donation_profile_id">Profile ID</label><br /> 
    <input id="donation_profile_id" name="donation[profile_id]" size="30" type="text" value="<?php echo $donation['profile_id']; ?>" /> 
  </div>
  <div class="field">
    <label for="donation_payment_profile_id">Payment Profile ID</label><br /> 
    <input id="donation_payment_profile_id" name="donation[payment_profile_id]" size="30" type="text" value="<?php echo $donation['payment_profile_id']; ?>" /> 
  </div>
  <div class="field">
    <label for="donation_customer_address_id">Customer Address ID</label><br /> 
    <input id="donation_customer_address_id" name="donation[customer_address_id]" size="30" type="text" value="<?php echo $donation['customer_address_id']; ?>" /> 
  </div>
  <div class="field">
    <label for="donation_ccv">CCV</label><br /> 
    <input id="donation_ccv" name="donation[ccv]" size="30" type="text" value="<?php echo $donation['ccv']; ?>" /> 
  </div>
  <div class="field">
    <label for="donation_charged">Charged</label><br>
    <input id="donation_charged" name="donation[charged]" type="checkbox" <?php if ($donation['charged']) {echo "checked='checked'";} ?>/>
  </div>
  <div class="field">
    <label for="donation_note">Note</label><br>
    <textarea cols="40" id="donation_note" name="donation[note]" rows="20"><?php echo $donation['note']; ?></textarea>
  </div>
  <div class="actions">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="donation[id]" value="<?php echo $donation['id']; ?>" />
    <input id="donation_submit" name="commit" type="submit" value="Update Donation" />
  </div>
</form>

<?php } ?>

<?php
function gtcm_donations_update() {
  global $wpdb;
  $params = $_POST['donation'];
  $charged = $params['charged'] ? 1 : 0;
  $wpdb->query("UPDATE ".GTCM_DONATIONS_TABLE." SET
    campaign_id={$params['campaign_id']},
    amount={$params['amount']},
    achievement_id={$params['achievement_id']},
    profile_id={$params['profile_id']},
    payment_profile_id={$params['payment_profile_id']},
    customer_address_id={$params['customer_address_id']},
    ccv='{$params['ccv']}',
    charged='{$charged}',
    note='{$params['note']}'
    WHERE id={$params['id']}");
  gtcm_donations_index();
} ?>

<?php
function gtcm_donations_delete() {
  global $wpdb;
  $wpdb->query("DELETE FROM ".GTCM_DONATIONS_TABLE." WHERE id=".$_GET['id']);
}
?>