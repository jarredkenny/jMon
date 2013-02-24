<?php


// Connect to DB
require('config.php');
mysql_connect("$dbhost", "$dbuser", "$dbpass")or die("Could not connect to DB");
mysql_select_db("$dbname");

// Build Settings Array
$_settings = mysql_query("SELECT * FROM settings");
while($setting = mysql_fetch_assoc($_settings))
{
	$settings[$setting['setting']] = $setting['value'];
}

// Build empty array to store issues
$issues = [];

// Check servers for issues that require alerts
$servers = mysql_query("SELECT * FROM servers");
while($server = mysql_fetch_assoc($servers))
{

	//Check for services that are down
	$issues[$server['hostname']] = [];
	$issues[$server['hostname']]['hostname'] = $server['hostname'];
	$issues[$server['hostname']]['services_down'] = [];
	$xml = new SimpleXMLElement($server['xml_data']);
	foreach($xml->Services as $node)
	{
		foreach($node as $service)
		{
			if(strtolower($service->Status) == "down")
			{
			$issues[$server['hostname']]['services_down'][] = $service->Name;
			}
		}
	}

	//Check Server load
	if($xml->Load > $settings['load_threshold'])
	{
		$issues[$server['hostname']]['load'] = $xml->Load;
	}

	//Check HDD
	if(str_replace("%", "", $xml->Disk_Percent_Used) > $settings['disk_usage_threshold'])
	{
		$issues[$server['hostname']]['disk_usage'] = $xml->Disk_Percent_Used;
	}

	if(empty($issues[$server['hostname']]['services_down']) && $xml->Load < $settings['load_threshold'] && str_replace("%", "", $xml->Disk_Percent_Used) < $settings['disk_usage_threshold'])
	{
		unset($issues[$server['hostname']]);
	}

}

//Use our issues array to compose a notification email
$message = "";
foreach($issues as $_server)
{
	$message .= "<h3>".$_server['hostname']."</h3>";
	if(isset($_server['services_down']))
	{
		foreach($_server['services_down'] as $service)
		{
			$message .= strtoupper($service)." IS DOWN!<br/>";
		}
	}

	if(isset($_server['load']))
	{
		$message .= "SERVER LOAD IS ".$_server['load']."!<br/>";
	}

	if(isset($_server['disk_usage']))
	{
		$message .= "DISK IS ".$_server['disk_usage']." FULL!<br/>";
	}
}

// Send email to alert contact
$to = $settings['alert_contact'];
$subject = "Server Monitor | Shit is down!";
$headers = "From: monitor@jr0d.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

mail($to, $subject, $message, $headers);
?>
