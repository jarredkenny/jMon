<?php

//Connect to DB
require("config.php");
mysql_connect("$dbhost", "$dbuser", "$dbpass")or die("Could not connect to database.");
mysql_select_db("$dbname")or die("Could not select DB.");

//Store new settings in DB
foreach($_POST as $setting=>$value)
{
	mysql_query("UPDATE settings SET value='$value' WHERE setting='$setting'");
}

//Return to settings page
header("location: /?p=settings&set=1");
?>
