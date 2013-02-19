<?php
if(isset($_POST['XML_DATA']))
{
	//Get the XML data from the POST
	$XML_DATA = $_POST['XML_DATA'];

	//Parse XML data for server hostname
	$xml = new SimpleXMLElement($XML_DATA);
	$hostname = $xml->Hostname;

	//Parse Acess Key from XML
	$accesskey = $xml->Access_Key;

	//Get current time to store with data
	//$time = date("G:i:s");
	$time = time();

	//Pull in Database info, and connect to database
	require('config.php');
	$con = mysql_connect("$dbhost", "$dbuser", "$dbpass")or die("Could not connect to database");
	mysql_select_db("$dbname")or die("Could not select database");

	//Remove existing entry with same hostname
	mysql_query("DELETE FROM servers WHERE hostname = '$hostname'")or die(mysql_error());

	//Add new entry for hostname
	mysql_query("INSERT INTO servers (hostname, check_time, xml_data) VALUES ('$hostname', '$time', '$XML_DATA')")or die(mysql_error());
	

	//Close connection to database
	mysql_close();


}else{
	echo "<h2>This page only accepts post data</h2>";
}
?>
