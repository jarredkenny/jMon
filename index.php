<html>
<head>
<?php
if(!isset($_GET['p']))
{
	$url=$_SERVER['REQUEST_URI'];
	header("Refresh: 10; URL=$url");
}
?>
<link rel='stylesheet' type='text/css' href='style.css'>
<title>Server Monitor</title>
</head>

<body>
<div id='header'>
<div id='headwrap'>
	<div id='logo'>
		jMon | A Simple Server Monitor
	</div>

	<div id='nav'>
		<ul>
			<a href='/'><li>Monitor</li></a>
			<a href='?p=logs'><li>Logs</li></a>
			<a href='?p=settings'><li>Settings</li></a>
		</ul>
	</div>
</div>
</div>
<div id='wrap'>
<?php
if(isset($_GET['p']))
{
	require($_GET['p'].".php");
}else{
	require("monitor.php");
}
?>
</div>
</body>

</html>
