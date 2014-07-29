<?php

    include "../setting_loaders/query_functions/setting_query.php";

	$oldemail = $_POST['oldemail'];
	$newemail = $_POST['newemail'];
	$password = $_POST['password'];
	$confirmpassword = $_COOKIE["wolf_of_siebel_password"];
	$username = $_COOKIE["wolf_of_siebel_username"];
	$emailconfirm = get_email($username, $confirmpassword);

    if (strcmp($oldemail, "") == 0)
        exit("Must enter Old Email");
        
    if (strcmp($newemail, "") == 0)
        exit("Must enter New Email");

    if (strcmp($password, "") == 0)
        exit("Must enter password");    
        
    if (strcmp($oldemail, $emailconfirm) != 0)
    	exit("Incorrect Email");

    if (strcmp($password, $confirmpassword) == 0) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
    	if (mysqli_connect_errno($con))
        	echo "Failed to connect to MySQL: " , mysqli_connect_error();
    
    	$stmt = mysqli_prepare($con, "UPDATE Users SET email =? WHERE username =? and password=?");
   	mysqli_stmt_bind_param($stmt, "sss", $newemail, $username, $password);
   	mysqli_stmt_execute($stmt);        
    } else {
        exit("Invalid password");
    }


?>