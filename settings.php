<?php
require('config.php');
mysql_connect("$dbhost", "$dbuser", "$dbpass")or die("<h2>Could not connect to database!</h2>");
mysql_select_db("$dbname")or die("<h2>Could not select database</h2>");
$settings = mysql_query("SELECT * FROM settings");
while($setting = mysql_fetch_assoc($settings))
{
	$values[$setting['setting']] = $setting['value'];
}

if(isset($_GET['set']))
{
	if($_GET['set'] == 1)
	{
		echo "<div class='title'>Settings have been saved</div>";
	}
}
?>
<div class='title'>Add New Server</div>
<div id='settings'>
<div id='body'>
<table>
<tr>
<form name='add_server' method='POST' action='index.php?p=gen_mon_script'>
<td><b>Server Name</b><br/>Does not actually effect monitoring but is used to name script.</td>
<td><input type='text' name='server_name'></td>
</tr><tr>
<td><b>OS Type</b><br/>Specify the type of operating system to be monitored.</td>
<td><select name='os_type'>
	<option value='linux'>Linux</option>
	<option value='bsd'>BSD</option>
	</select>
</td>
</tr><tr>
<td><b>Services</b><br/>Specify the name of processes to be monitored on the server.<br/> Multiple processes should be seperated using spaced. Ex. "postfix sshd httpd"</td>
<td><input type='text' name='services'></td>
</tr><tr>
<td><b>Network Interaface</b><br/>Specify the name of the servers primary network interface.<br/>This is used for bandwidth calculations.</td>
<td><input type='text' name='interface_name'></td>
</tr><tr>
<td></td>
<td><input type='submit' name='submit' value='Generate Script'></td>
</tr>
</form>
</table>
</div>
<div class='title'>Global Settings</div>
<div id='body'>
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
