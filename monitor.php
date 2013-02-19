<div id='monitor'>
<?php
require('config.php');
mysql_connect("$dbhost", "$dbuser", "$dbpass")or die("<h2>Could not connect to database!</h2>");
mysql_select_db("$dbname");
$servers = mysql_query("SELECT * FROM servers");
while ($server = mysql_fetch_assoc($servers)) 
{
	//Create parsable XML element 
	$xml = new SimpleXMLElement($server['xml_data']);

	//Start table
	echo "<div class='title'>".$server['hostname']."</div>";
	echo "<table><tr><th>Uptime</th><th>Last Update</th><th>Services</th><th>Load</th><th>Updates</th><th>HDD</th><th>RAM</th><th>Traffic</th></tr>";

	//Uptime
	echo "<tr><td>".$xml->Uptime."</td>";

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
	else{echo floor($time_since/3600)." minutes ago";}
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
	echo "<td>".$xml->Load."</td>";


	//Updates
	echo "<td>".$xml->Updates_Avail."</td>";

	//HDD
	echo "<td>";
	echo "Free: ".$xml->Disk_Free;
	echo "</br>";
	echo "Used: ".$xml->Disk_Used." (".$xml->Disk_Percent_Used.")";
	echo "</br>";
	echo "Total: ".$xml->Disk_Total;
	echo "</td>";

	//RAM
	echo "<td>";
	echo "Free: ".$xml->Ram_Free;
	echo "</br>";
	echo "Total: ".$xml->Ram_Total;
	echo "</td>";

	//Traffic
	echo "<td>";
	echo "RX: ".$xml->Rx_Bytes;
	echo "</br>";
	echo "TX: ".$xml->Tx_Bytes;
	echo "</td>";

//	echo htmlentities($server['xml_data']);
	echo "</table>";
}
mysql_close();
?>
</div>
