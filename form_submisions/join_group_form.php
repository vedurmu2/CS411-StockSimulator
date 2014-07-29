<?php
    require_once("../dashboard_loaders/query_objects/Group.php");

	function isValid($str) {
    		return !preg_match('/[^A-Za-z0-9.#\\-$]/', $str);
	}

	$portfolio = $_POST['portfolio'];
	$password = $_POST['password'];
	$GID = intval($_POST['groupnum']);

    $username = $_COOKIE["wolf_of_siebel_username"];

	$portfolio = $_POST['portfolio'];
	if (strcmp($portfolio, "") == 0 || !isValid($portfolio))
		exit("Must enter valid portfolio name");

    $my_user = User::get_user_object($username);
	if ($my_user->join_group($GID, $portfolio, $password)) {
        
    } else {
    	echo "Could not join group";
    }
?>