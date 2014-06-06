<?php
	error_reporting("E_ALL & ~E_NOTICE");
	//$dl = new DataLayer();
	$database = 'ncrcinfo';
	$user = 'ncrcinfo';
	$password = 'rqudt2zv';
	/* Specify the server and connection string attributes. */
	$serverName = "database.ncl.ac.uk";
	/* Connect using SQL Server Authentication. */
	$conn= dl::connect($serverName,$user,$password,$database);
?>