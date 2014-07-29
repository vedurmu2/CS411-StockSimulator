<?php


	$oldpassword = $_POST['oldpassword'];
	$newpassword = $_POST['newpassword'];
	$passwordre = $_POST['passwordre'];
	$confirmpassword = $_COOKIE["wolf_of_siebel_password"];
	$username = $_COOKIE["wolf_of_siebel_username"];
	

    if (strcmp($oldpassword, "") == 0)
        exit("Must enter Old Password");

    if (strcmp($newpassword, "") == 0)
        exit("Must enter New Password");
        
    if (strcmp($passwordre, "") == 0)
        exit("Must reenter password");
        
    if (strcmp($newpassword, $passwordre) != 0)
        exit("Password Mismatch");

    if (strcmp($oldpassword, $confirmpassword) == 0) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
    	if (mysqli_connect_errno($con))
        	echo "Failed to connect to MySQL: " , mysqli_connect_error();
    
    	$stmt = mysqli_prepare($con, "UPDATE Users SET password =? WHERE username =? and password=?");
   	mysqli_stmt_bind_param($stmt, "sss", $newpassword, $username, $confirmpassword);
   	mysqli_stmt_execute($stmt);        
        setcookie("wolf_of_siebel_password", $newpassword, time() + 3600 * 24 * 14, "/");
    } else {
        exit("Invalid password");
    }
    setcookie("wolf_of_siebel_username", "", time() - 3600 * 24 * 14, "/");
    setcookie("wolf_of_siebel_password", "", time() - 3600 * 24 * 14, "/");
    setcookie("wolf_of_siebel_name", "", time() - 3600 * 24 * 14, "/");
	die();


?>