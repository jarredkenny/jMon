<div id='monitor'>
<?php
require('config.php');
mysql_connect("$dbhost", "$dbuser", "$dbpass")or die("<h2>Could not connect to database!</h2>");
mysql_select_db("$dbname");

//Build settings array
$_settings = mysql_query("SELECT * FROM settings");
while($setting = mysql_fetch_assoc($_settings))
{
        $settings[$setting['setting']] = $setting['value'];
}

//Build Monitor table
$servers = mysql_query("SELECT * FROM servers");
while ($server = mysql_fetch_assoc($servers)) 
{
	//Create parsable XML element 
	$xml = new SimpleXMLElement($server['xml_data']);

	//Start table
	echo "<div class='title'>".$server['hostname']."<div class='os'>".$xml->OS."</div></div>";
	echo "<table><tr><th>Uptime</th><th>Last Update</th><th>Services</th><th>Load</th><th>Updates</th><th>HDD</th><th>RAM</th><th>Traffic</th></tr>";

	//Uptime
	echo "<tr><td>";
	$uptime = $xml->Uptime;
	switch($uptime){
	case($uptime < 60):
	if(floor($uptime) == 1){echo floor($uptime)." second";}
	else{echo floor($uptime). " seconds";}
	break;

	case($uptime > 60 && $uptime < 3600):
	if(floor($uptime/60) == 1){echo floor($uptime/60)." minute";}
	else{echo floor($uptime/60). "minutes";}
	break;

	case($uptime > 3600 && $uptime < 86400):
	if(floor($uptime/3600) == 1){echo floor($uptime/3600). "hour";}
	else{echo floor($uptime/3600). "hours";}
	break;

	case($uptime > 86400):
	if(floor($uptime/86400) == 1){echo floor($uptime/86400)." day";}
	else{echo floor($uptime/86400)." days";}
	}

	//Time of last check
	echo "<td>";
	$time_since = time() - $server['check_time'];

	switch($time_since){
	case($time_since < 60):
	if($time_since == 1){echo $time_since." second ago";}
	else{echo $time_since." seconds ago";}
	break;

	case($time_since > 60 && $time_since < 3600):
	if(floor($time_since/60) == 1){echo floor($time_since/60)." minute ago";}
	else{echo floor($time_since/60)." minutes ago";}
	break;

	case($time_since > 3600):
	if(floor($time_since/3600) == 1){echo floor($time_since/3600)." hour ago";}
	else{echo floor($time_since/3600)." hours ago";}
	break;
	}
	echo "</td>";


	//Services
	echo "<td>";
	foreach($xml->Services as $node)
	{
		foreach($node as $service)
		{
			if(strtolower($service->Status) == "up" ){echo $service->Name." <span class='up'>up</span>";}
			if(strtolower($service->Status) == "down"){echo $service->Name." <span class='down'>down</span>";}
			echo "</br>";
		}
	}
	echo "</td>";

	//Load
	if($xml->Load
 < 2.00)
	{
		echo "<td>".$xml->Load."</td>";
	}else{
		echo "<td><div class='red'>".$xml->Load."</div></td>";
	}

	//Updates
	echo "<td>".$xml->Updates_Avail."</td>";

	//HDD
	echo "<td>";
	echo "<meter min='0' max='100' high='".$settings['disk_usage_threshold']."' value='".str_replace("%", "", $xml->Disk_Percent_Used)."'></meter></br></br>";
	echo "Free: ".$xml->Disk_Free;
	echo "</br>";
	echo "Used: ".$xml->Disk_Used." (".$xml->Disk_Percent_Used.")";
	echo "</br>";
	echo "Total: ".$xml->Disk_Total;
	echo "</td>";

	//RAM
	echo "<td>";
	$Ram_Used = $xml->Ram_Total - $xml->Ram_Free;
	echo "<meter min='0' max='".$xml->Ram_Total."' value='".$Ram_Used."'></meter></br></br>";
	echo "Free: ".$xml->Ram_Free;
	echo "</br>";
	echo "Used: ".$Ram_Used;
	echo "</br>";
	echo "Total: ".$xml->Ram_Total;
	echo "</td>";

	//Traffic
	echo "<td>";
	echo "<img src='img/traffic_down.png' alt='Traffic Down' width='15px' height='15px'> ".floor($xml->Rx_Bytes / 1048576)." MB";
	echo "</br></br>";
	echo "<img src='img/traffic_up.png' alt='Traffic Up' width='15px' height='15px'> ".floor($xml->Tx_Bytes / 1048576). " MB";
	echo "</td>";

//	echo htmlentities($server['xml_data']);
	echo "</table>";
}
mysql_close();
?>
</div>
