<?php

	$oldusername = $_POST['oldusername'];
	$newusername = $_POST['newusername'];
	$password = $_POST['password'];
	$confirmpassword = $_COOKIE["wolf_of_siebel_password"];
	$username = $_COOKIE["wolf_of_siebel_username"];
	

    if (strcmp($oldusername, "") == 0)
        exit("Must enter Old Username");
    
    if (strcmp($newusername, "") == 0)
        exit("Must enter New Username");

    if (strcmp($password, "") == 0)
        exit("Must enter password");
    
    if (strcmp($oldusername, $username) != 0)
        exit("Incorrect Username");

    if (strcmp($password, $confirmpassword) == 0) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
    	if (mysqli_connect_errno($con))
        	echo "Failed to connect to MySQL: " , mysqli_connect_error();
    
    	$users = mysqli_prepare($con, "UPDATE Users SET username =? WHERE username =? and password=?");
   	mysqli_stmt_bind_param($users, "sss", $newusername, $username, $password);
   	$portfolios = mysqli_prepare($con, "UPDATE Portfolio SET username =? WHERE username =?");
   	mysqli_stmt_bind_param($portfolios, "ss", $newusername, $username);
   	$partof = mysqli_prepare($con, "UPDATE PartOf SET username =? WHERE username =?");
   	mysqli_stmt_bind_param($partof, "ss", $newusername, $username);
   	$groups = mysqli_prepare($con, "UPDATE Groups SET owner =? WHERE owner =?");
   	mysqli_stmt_bind_param($groups, "ss", $newusername, $username);
   	mysqli_stmt_execute($users);    
   	mysqli_stmt_execute($portfolios);    
   	mysqli_stmt_execute($partof);    
   	mysqli_stmt_execute($groups);        
        setcookie("wolf_of_siebel_username", $newusername, time() + 3600 * 24 * 14, "/");
    } else {
        exit("Invalid password");
    }
    setcookie("wolf_of_siebel_username", "", time() - 3600 * 24 * 14, "/");
    setcookie("wolf_of_siebel_password", "", time() - 3600 * 24 * 14, "/");
    setcookie("wolf_of_siebel_name", "", time() - 3600 * 24 * 14, "/");
	die();


?>