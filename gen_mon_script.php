<?php
if(isset($_POST['os_type']) && isset($_POST['services']) && isset($_POST['interface_name']) && isset($_POST['server_name']))
{
	$os_type = $_POST['os_type'];
	$services = $_POST['services'];
	$interface_name = $_POST['interface_name'];
	$server_name = $_POST['server_name'];
	$listen_url = "http://".$_SERVER['HTTP_HOST']."/listen.php";
	$script = file_get_contents("scripts/mon_".$os_type.".sh");

	$script = str_replace("{{{interface_name}}}", $interface_name, $script);
	$script = str_replace("{{{services}}}", $services, $script);
	$script = str_replace("{{{listen_url}}}", $listen_url, $script);

	file_put_contents("scripts/generated/".$server_name."_mon.sh", $script);
?>
<div class='title'>Download Script</div>
<div id='body'>
A monitoring script has been generated for <?php echo $server_name; ?> and can be downloading using the following:<br/><br/>
<code>wget <?php echo "http://".$_SERVER['HTTP_HOST']."/scripts/generated/".$server_name."_mon.sh"; ?></code>
<br/><br/>
You should set up a cron job to run the script as often as youw want the server updated in the monitor. If you are not familier with how to do so a guide can be found here:
<br/><br/>
<a href='http://www.cyberciti.biz/faq/how-do-i-add-jobs-to-cron-under-linux-or-unix-oses/'>http://www.cyberciti.biz/faq/how-do-i-add-jobs-to-cron-under-linux-or-unix-oses/</a>
</div>

<div class='title'>View Script</div>
<div id='body'>
<textarea readonly><?php echo $script; ?></textarea>
</div>
<?php
}else{
	header("location: /?p=settings&error=1");
}
?>


