<?php
require('config.php');
mysql_connect("$dbhost", "$dbuser", "$dbpass")or die("<h2>Could not connect to database!</h2>");
mysql_select_db("$dbname")or die("<h2>Could not select database</h2>");
$settings = mysql_query("SELECT * FROM settings");
while($setting = mysql_fetch_assoc($settings))
{
	$values[$setting['setting']] = $setting['value'];
}
?>
<div class='title'>Settings</div>
<div id='body'>
<div id='settings'>
<form name='settings' action='set_settings.php' method='post'>
<table>
<tr>
<td><b>Load Threshold</b><br/>Minimim server load required to trigger an alert.</td>
<td><input type='number' name='load_threshold' value='<?php echo $values['load_threshold']; ?>'></td>
<tr></tr>
<td><b>Data Threshold</b><br/>Minimum amount of time in minutes since last update was received from server to trigger an alert.</td>
<td><input type='number' name='data_threshold' value='<?php echo $values['data_threshold']; ?>'></td>
</tr><tr>
<td><b>Disk Usage Threshold</b><br/>Maximum amount of disk space in percent that can be occupied before triggering an alert.</td>
<td><input type='number' name='disk_usage_threshold' value='<?php echo $values['disk_usage_threshold'];?>'></td>
</tr><tr>
<td><b>Alert Contact</b><br/>The email address to be contacted when an alert is triggered</td>
<td><input type='text' name='alert_contact' value='<?php echo $values['alert_contact']; ?>'></td>
</tr><tr>
<td></td>
<td><input type='submit' value='Save Settings'></td>
</table>
</form>
</div>
</div>
