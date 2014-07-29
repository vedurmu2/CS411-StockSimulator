<?php
	require_once("../dashboard_loaders/query_objects/BoughtStock.php");

	$PID = $_POST['ports'];
	$num_shares = intval($_POST['numShares']);
	$ticker = $_POST['ticker'];

	if ($num_shares <= 0)
		exit( "Invalid number of shares" );

	$stock = new BoughtStock($num_shares, $ticker, $PID);
?>