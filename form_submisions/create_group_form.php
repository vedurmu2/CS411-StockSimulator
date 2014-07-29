<?php
	require_once("../dashboard_loaders/query_objects/Group.php");

	$groupname = $_POST['groupname'];
	$start_money = intval($_POST['startmoney']);
	$password = $_POST['password'];

	if (strcmp($groupname, "") == 0)
        exit("Must enter group name");

    if ($start_money <= 1000)
        exit("Invalid start money");

	$portfolio = $_POST['portfolio'];
	if (strcmp($portfolio, "") == 0)
		exit("Must enter portfolio name");

 	$username = $_COOKIE["wolf_of_siebel_username"];

 	$user_object = User::get_user_object($username);

 	$new_group = new Group($groupname, $start_money, $username, $password);
 	if (is_null($new_group->GID))
 		exit ("Could not create group :(");

	if ($user_object->join_group($new_group->GID, $portfolio, $password)) {

    }
?>