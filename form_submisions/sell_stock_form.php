<?php
	require_once("../dashboard_loaders/query_objects/BoughtStock.php");

	$PID = $_POST['PID'];
	$num_shares = intval($_POST['numShares']);
	$ticker = $_POST['ticker'];

	$stock = BoughtStock::get_bought_stock_object($ticker, $PID);
	$stock->sell_stock($num_shares);
?>