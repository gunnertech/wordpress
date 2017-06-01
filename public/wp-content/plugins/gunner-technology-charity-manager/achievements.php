<?php
function gtcm_achievements() {
  if (isset($_GET['id'])) {
  	if ($_GET['action'] == 'show')
  	  gtcm_achievements_show();
  	elseif ($_GET['action'] == 'edit')
  	  gtcm_achievements_edit();
  	elseif ($_GET['action'] == 'delete')
      gtcm_achievements_delete();
  } else {
    if ($_GET['action'] == 'new')
      gtcm_achievements_new();
    elseif ($_POST['action'] == 'create')
      gtcm_achievements_create();
    elseif ($_POST['action'] == 'update')
  	  gtcm_achievements_update();
    else
      gtcm_achievements_index();
  }
}
?>

<?php
function gtcm_achievements_index() {
  global $wpdb;
  $achievements = $wpdb->get_results("SELECT * FROM ".GTCM_ACHIEVEMENTS_TABLE, ARRAY_A);
?>
<h1>Listing Achievements</h1>

<table class="gtcm-table">
  <thead>
    <tr>
      <th>Name</th>
      <th>Count</th>
      <th>Campaign</th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  
<?php foreach($achievements as $achievement) { 
  if (!$campaigns[$achievement['campaign_id']])
    $campaigns[$achievement['campaign_id']] = $wpdb->get_var("SELECT name FROM ".GTCM_CAMPAIGNS_TABLE." WHERE id = ".$achievement['campaign_id']);
?>
  <tbody>
    <tr>
      <td><?php echo $achievement['name']; ?></td>
      <td><?php echo $achievement['count']; ?></td>
      <td><?php echo $campaigns[$achievement['campaign_id']]; ?></td>
      <td><a href='<?php echo gtcm_base_url().'&action=show&id='.$achievement['id'] ?>'>Show</a></td>
      <td><a href='<?php echo gtcm_base_url().'&action=edit&id='.$achievement['id'] ?>'>Edit</a></td>
      <td><a href='#' class='gtcm-delete' id='gtcm-delete-<?php echo $achievement['id'] ?>'>Delete</a></td>
    </tr>
  </tbody>
<?php } ?>
</table>

<br />

<a href='<?php echo gtcm_base_url().'&action=new' ?>'>New Achievement</a>

<?php } ?>

<?php
function gtcm_achievements_new() {
  global $wpdb;
  
  $campaigns = $wpdb->get_results("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE, ARRAY_A);
  $options = '';
  foreach($campaigns as $campaign)
    $options .= "<option value='{$campaign['id']}'>{$campaign['name']}</option>";
?>

<form accept-charset="UTF-8" action="<?php echo gtcm_base_url() ?>" method="post">
  <div class="field">
    <label for="achievement_name">Name</label><br />
    <input id="achievement_name" name="achievement[name]" size="30" type="text" />
  </div>
  <div class="field">
    <label for="achievement_count">Count</label><br />
    <input id="achievement_count" name="achievement[count]" size="30" type="text" /> 
  </div>
  <div class="field">
    <label for="achievement_campaign_id">Campaign</label><br />
    <select id="achievement_campaign_id" name="achievement[campaign_id]" type="select">
      <?php echo $options ?>
    </select>
  </div>
  <div class="actions">
    <input type="hidden" name="action" value="create" />
    <input id="achievement_submit" name="commit" type="submit" value="Create Achievement" />
  </div>
</form>

<?php } ?>

<?php
function gtcm_achievements_create() {
  global $wpdb;
  $params = $_POST['achievement'];
  $wpdb->query("INSERT INTO ".GTCM_ACHIEVEMENTS_TABLE."
    (name, count, campaign_id)
    VALUES ('".implode("','",$params)."')");
  gtcm_achievements_index();
} ?>

<?php
function gtcm_achievements_show() {
  global $wpdb;
  $achievement = $wpdb->get_row("SELECT * FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE id = ".$_GET['id'], ARRAY_A);
  $campaign = $wpdb->get_row("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE." WHERE id = ".$achievement['campaign_id'], ARRAY_A);
?>

<p id="notice"></p>

<p>
  <b>Name:</b>
  <?php echo $achievement['name']; ?>
</p>

<p>
  <b>Count:</b>
  <?php echo $achievement['count']; ?>
</p>

<p>
  <b>Total:</b>
  <?php echo $campaign['name']; ?>
</p>

<a href='<?php echo gtcm_base_url().'&action=edit&id='.$achievement['id'] ?>'>Edit</a> | <a href='<?php echo gtcm_base_url() ?>'>Index</a>

<?php } ?>

<?php
function gtcm_achievements_edit() {
  global $wpdb;
  
  $achievement = $wpdb->get_row("SELECT * FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE id = ".$_GET['id'], ARRAY_A);
  $campaigns = $wpdb->get_results("SELECT * FROM ".GTCM_CAMPAIGNS_TABLE, ARRAY_A);
  $options = '';
  foreach($campaigns as $campaign) {
    $selected = ($campaign['id'] == $achievement['campaign_id']) ? " selected='selected'" : "";
    $options .= "<option value='{$campaign['id']}'{$selected}>{$campaign['name']}</option>";
  }
?>

<form accept-charset="UTF-8" action="<?php echo gtcm_base_url() ?>" method="post">
  <div class="field">
    <label for="achievement_name">Name</label><br /> 
    <input id="achievement_name" name="achievement[name]" size="30" type="text" value="<?php echo $achievement['name']; ?>"/> 
  </div>
  <div class="field">
    <label for="achievement_count">Count</label><br />
    <input id="achievement_count" name="achievement[count]" size="30" type="text" value="<?php echo $achievement['count']; ?>" /> 
  </div>
  <div class="field">
    <label for="achievement_campaign_id">Campaign</label><br />
    <select id="achievement_campaign_id" name="achievement[campaign_id]" type="select">
      <?php echo $options ?>
    </select>
  </div>
  <div class="actions">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="achievement[id]" value="<?php echo $achievement['id']; ?>" />
    <input id="achievement_submit" name="commit" type="submit" value="Update Achievement" />
  </div>
</form>

<?php } ?>

<?php
function gtcm_achievements_update() {
  global $wpdb;
  $params = $_POST['achievement'];
  $wpdb->query("UPDATE ".GTCM_ACHIEVEMENTS_TABLE." SET
    name='{$params['name']}',
    count={$params['count']},
    campaign_id={$params['campaign_id']}
    WHERE id={$params['id']}");
  gtcm_achievements_index();
} ?>

<?php
function gtcm_achievements_delete() {
  global $wpdb;
  $wpdb->query("DELETE FROM ".GTCM_ACHIEVEMENTS_TABLE." WHERE id=".$_GET['id']);
} ?>