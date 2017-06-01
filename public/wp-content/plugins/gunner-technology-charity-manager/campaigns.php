<?php
function gtcm_campaigns() {
  if (isset($_GET['id'])) {
  	if ($_GET['action'] == 'show')
  	  gtcm_campaigns_show();
    elseif ($_GET['action'] == 'edit')
  	  gtcm_campaigns_edit();
    elseif ($_GET['action'] == 'delete')
      gtcm_campaigns_delete();
    elseif ($_GET['action'] == 'process_payments')
      gtcm_campaigns_process_payments();
  } else {
    if ($_GET['action'] == 'new')
      gtcm_campaigns_new();
    elseif ($_POST['action'] == 'create')
      gtcm_campaigns_create();
    elseif ($_POST['action'] == 'update')
  	  gtcm_campaigns_update();
    else
      gtcm_campaigns_index();
  }
}
?>

<?php
function gtcm_campaigns_index() {
  global $wpdb;
  $campaigns = $wpdb->get_results("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE, ARRAY_A);
?>
<h1>Listing Campaigns</h1>

<table class="gtcm-table">
  <thead>
    <tr>
      <th>Name</th>
      <th>Description</th>
      <th>Total</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  
<?php 
  foreach($campaigns as $campaign) {
    $total = 0;
    $donations = $wpdb->get_results("SELECT * FROM ".GTCM_DONATIONS_TABLE." WHERE campaign_id=".$campaign['id'], ARRAY_A);
    foreach($donations as $donation) {
      if ($donation['achievement_id'] > 0 and !$achievements[$donation['achievement_id']])
        $achievements[$donation['achievement_id']] = $wpdb->get_var("SELECT count FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE id = ".$donation['achievement_id']);
      $total += $achievements[$donation['achievement_id']] * $donation['amount'];
    }
?>
  <tbody>
    <tr>
      <td><?php echo $campaign['name']; ?></td>
      <td><?php echo $campaign['description']; ?></td>
      <td><?php echo number_format($total, 2); ?></td>
      <td><?php echo gtcm_date($campaign['start_date']); ?></td>
      <td><?php echo gtcm_date($campaign['end_date']); ?></td>
      <td><a href='<?php echo gtcm_base_url().'&action=show&id='.$campaign['id'] ?>'>Show</a></td>
      <td><a href='<?php echo gtcm_base_url().'&action=edit&id='.$campaign['id'] ?>'>Edit</a></td>
      <td><a href='#' class='gtcm-delete' id='gtcm-delete-<?php echo $campaign['id'] ?>'>Delete</a></td>
      <td><a href='<?php echo gtcm_base_url().'&action=process_payments&id='.$campaign['id'] ?>'>Process Payments</a></td>
    </tr>
  </tbody>
<?php } ?>
</table>

<br />

<a href='<?php echo gtcm_base_url().'&action=new' ?>'>New Campaign</a>

<?php } ?>

<?php
function gtcm_campaigns_new() {
?>

<form accept-charset="UTF-8" action="<?php echo gtcm_base_url() ?>" method="post">
  <div class="field">
    <label for="campaign_name">Name</label><br />
    <input id="campaign_name" name="campaign[name]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="campaign_description">Description</label><br>
    <textarea cols="40" id="campaign_description" name="campaign[description]" rows="20"></textarea>
  </div>
  <div class="field">
    <label for="campaign_start_date">Start Date</label>
    <input id="campaign_start_date" name="campaign[start_date]" class="date-pick dp-applied" />
  </div>
  <div class="field">
    <label for="campaign_end_date">End Date</label>
    <input id="campaign_end_date" name="campaign[end_date]" class="date-pick dp-applied" />
  </div>
  <div class="actions">
    <input type="hidden" name="action" value="create" />
    <input id="campaign_submit" name="commit" type="submit" value="Create Campaign" />
  </div>
</form>

<?php } ?>

<?php
function gtcm_campaigns_create() {
  global $wpdb;
  $params = $_POST['campaign'];
  $wpdb->query("INSERT INTO ".GTCM_CAMPAIGNS_TABLE."
    (name, description, start_date, end_date)
    VALUES ('".$params['name']."',
            '".$params['description']."',
            str_to_date('".$params['start_date']."', '%m/%d/%Y'),
            str_to_date('".$params['end_date']."', '%m/%d/%Y'))");
  gtcm_campaigns_index();
} ?>

<?php
function gtcm_campaigns_show() {
  global $wpdb;
  
  $campaign = $wpdb->get_row("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE." WHERE id = ".$_GET['id'], ARRAY_A);
  $donations = $wpdb->get_results("SELECT * FROM ".GTCM_DONATIONS_TABLE." WHERE campaign_id=".$campaign['id'], ARRAY_A);
  foreach($donations as $donation) {
    if ($donation['achievement_id'] > 0 and !$achievements[$donation['achievement_id']])
      $achievements[$donation['achievement_id']] = $wpdb->get_var("SELECT count FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE id = ".$donation['achievement_id']);
    $total += $achievements[$donation['achievement_id']] * $donation['amount'];
  }
?>

<p id="notice"></p>

<p>
  <b>Name:</b>
  <?php echo $campaign['name']; ?>
</p>

<p>
  <b>Description:</b>
  <?php echo $campaign['description']; ?>
</p>

<p>
  <b>Total:</b>
  <?php echo number_format($total, 2); ?>
</p>

<p>
  <b>Start Date:</b>
  <?php echo gtcm_date($campaign['start_date']); ?>
</p>

<p>
  <b>End Date:</b>
  <?php echo gtcm_date($campaign['end_date']); ?>
</p>

<a href='<?php echo gtcm_base_url().'&action=edit&id='.$campaign['id'] ?>'>Edit</a> | <a href='<?php echo gtcm_base_url() ?>'>Index</a>



<?php } ?>

<?php
function gtcm_campaigns_edit() {
  global $wpdb;
  $campaign = $wpdb->get_row("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE." WHERE id = ".$_GET['id'], ARRAY_A);
?>

<form accept-charset="UTF-8" action="<?php echo gtcm_base_url() ?>" method="post">
  <div class="field">
    <label for="campaign_name">Name</label><br /> 
    <input id="campaign_name" name="campaign[name]" size="30" type="text" value="<?php echo $campaign['name']; ?>" /> 
  </div> 
  <div class="field">
    <label for="campaign_description">Description</label><br>
    <textarea cols="40" id="campaign_description" name="campaign[description]" rows="20"><?php echo $campaign['description'] ?></textarea>
  </div>
  <div class="field">
    <label for="campaign_start_date">Start Date</label>
    <input id="campaign_start_date" name="campaign[start_date]" class="date-pick dp-applied" value="<?php echo gtcm_date($campaign['start_date']); ?>" />
  </div>
  <div class="field">
    <label for="campaign_end_date">End Date</label>
    <input id="campaign_end_date" name="campaign[end_date]" class="date-pick dp-applied" value="<?php echo gtcm_date($campaign['end_date']); ?>" />
  </div>
  <div class="actions">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="campaign[id]" value="<?php echo $campaign['id']; ?>" />
    <input id="campaign_submit" name="commit" type="submit" value="Update Campaign" />
  </div>
</form>

<?php } ?>

<?php
function gtcm_campaigns_update() {
  global $wpdb;
  $params = $_POST['campaign'];
  $wpdb->query("UPDATE ".GTCM_CAMPAIGNS_TABLE." SET
    name='{$params['name']}',
    description='{$params['description']}',
    start_date=str_to_date('{$params['start_date']}', '%m/%d/%Y'),
    end_date=str_to_date('{$params['end_date']}', '%m/%d/%Y')
    WHERE id={$params['id']}");
  gtcm_campaigns_index();
} ?>

<?php
function gtcm_campaigns_delete() {
  global $wpdb;
  $wpdb->query("DELETE FROM ".GTCM_CAMPAIGNS_TABLE." WHERE id=".$_GET['id']);
} ?>

<?php
function gtcm_campaigns_process_payments() {
  global $wpdb;
  $campaign = $wpdb->get_row("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE." WHERE id = ".$_GET['id'], ARRAY_A);
  if (!$_GET['confirm']) {
?>
<span id="gtcm-process-payments-warning">WARNING: Clicking "Process Payments" will charge donor's credit cards.</span>
<br /><br />
<span id="gtcm-process-payments-note">(Note: You will still be able to cancel payments manually on Authorize.net.)</span>
<br /><br />
<a href='<?php echo $_SERVER['REQUEST_URI'] ?>&confirm=true'><button type="button" id="gtcm-process-payments-button">Process Payments</button></a>
<?php 
  } else {
?>
<h2>PROCESSING PAYMENTS!</h2>
<span id="gtcm-process-payments-results">Results</span>
<br /><br />
<?php  
    require('AuthnetCIM.class.php');
    $donations = $wpdb->get_results("SELECT * FROM ".GTCM_DONATIONS_TABLE." WHERE charged = 0 AND campaign_id=".$_GET['id'], ARRAY_A);
    foreach($donations as $donation) {
      if ($donation['achievement_id'] != 0)
        $total = $wpdb->get_var("SELECT count FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE id = ".$donation['achievement_id']) * $donation['amount'];
      else
        $total = $donation['amount'];  
      try {
        $cim = new AuthnetCIM('8BE4Vwp5ZQF', '89925XKkKdfX26A7');
        $cim->setParameter('validationMode', 'liveMode');
        $cim->setParameter('amount', $total);
        $cim->setParameter('customerProfileId', $donation['profile_id']);
        $cim->setParameter('customerPaymentProfileId', $donation['payment_profile_id']);
        $cim->setParameter('customerShippingAddressId', $donation['shipping_address_id']);
        $cim->setLineItem('1', $campaign['name'], '', '1', $total);
        $cim->setParameter('cardCode', $donation['ccv']);
        $cim->createCustomerProfileTransaction();
        
        $name = "{$donation['first_name']} {$donation['last_name']}";
        if ($cim->isSuccessful()) {
          echo("<a href='mailto:\"{$name}\" <{$donation['email']}>'>{$name}</a> ({$donation['profile_id']}), \${$total}: Success!<br />");
          $wpdb->query("UPDATE ".GTCM_DONATIONS_TABLE." SET charged=1 WHERE id={$donation['id']}");
        } else {
          echo("<a href='mailto:\"{$name}\" <{$donation['email']}>'>{$name}</a> ({$donation['profile_id']}), \${$total}: Failed. {$cim->getResponse()} ({$cim->getCode()})<br />");
        }
      } catch (AuthnetCIMException $e) {
        echo($donation['profile_id'].', $'.$total.': '.$e.'<br />');
      }
    }
  }
}
?>