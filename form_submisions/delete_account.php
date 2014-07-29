<?php

	require_once("/home/wolfofsiebel/public_html/dashboard_loaders/query_objects/Portfolio.php");
	require_once("/home/wolfofsiebel/public_html/dashboard_loaders/query_objects/User.php");
	require_once("/home/wolfofsiebel/public_html/dashboard_loaders/query_objects/Group.php");


	$username = $_POST['username'];
	$password = $_POST['password'];
	$passwordre = $_POST['passwordre'];
	$passwordconfirm = $_COOKIE["wolf_of_siebel_password"];
	$usernameconfirm = $_COOKIE["wolf_of_siebel_username"];
	$PIDS = "";
	
	$portfolios = User::get_user_object($usernameconfirm)->get_portfolios();
	

    if (strcmp($username, "") == 0)
        exit("Must enter username");

    if (strcmp($password, "") == 0)
        exit("Must enter password");
        
    if (strcmp($passwordre, "") == 0)
        exit("Must re-enter password");
    
    if (strcmp($username, $usernameconfirm) != 0)
        exit("Incorrect Username");
        
    if (strcmp($password, $passwordre) != 0)
        exit("Password Mismatch");    
        
    $PIDS = "(";
    foreach ($portfolios as $portfolio){
  		$PIDS = $PIDS . (string)$portfolio->PID . ", ";}
    $PIDS = substr($PIDS, 0, -2);
    $PIDS = $PIDS . ")";
    

/*    if (strcmp($password, $passwordconfirm) == 0) {
        $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
    	if (mysqli_connect_errno($con))
        	echo "Failed to connect to MySQL: " , mysqli_connect_error();
        	
        $groups = Group::get_all_groups($username);
	foreach ($groups as $group) {
	$names = $group->get_group_users();	
        if(strcmp($group->owner_username, $username) == 0 and count($names) > 1){
        	$newowner = "";
        	foreach($names as $name) {
        		if($name != $username){
        			$newowner = $name;
        			break;}}		
        	$groupdel = mysqli_prepare($con, "UPDATE Groups SET owner =? WHERE owner =? and GID=?");
   		mysqli_stmt_bind_param($groupdel, "si", $newowner, $group->GID);
   		mysqli_stmt_execute($groupdel);
        }
        if(strcmp($group->owner_username, $username) == 0 and count($names) == 1){
        $groupdel = mysqli_prepare($con, "DELETE FROM Groups WHERE owner =? and GID=?");
   	mysqli_stmt_bind_param($groupdel, "si", $username, $group->GID);
   	mysqli_stmt_execute($groupdel);
        }}
        */
	

   				
				
       
    
    	$users = mysqli_prepare($con, "DELETE FROM Users WHERE username =? and password=?");
   	mysqli_stmt_bind_param($users, "ss", $username, $password);
   	$partof = mysqli_prepare($con, "DELETE FROM PartOf WHERE username =?");
   	mysqli_stmt_bind_param($partof, "s", $username);
   	mysqli_stmt_execute($users);       
   	mysqli_stmt_execute($partof);
   	
	if (count($portfolios) > 0){
   	$portfolios = mysqli_prepare($con, "DELETE FROM Portfolio WHERE PID in " . $PIDS);
   	$transaction = mysqli_prepare($con, "DELETE FROM Transaction WHERE PID in " . $PIDS);
   	$boughtstock = mysqli_prepare($con, "DELETE FROM BoughtStock WHERE PID in " . $PIDS); 
   	mysqli_stmt_execute($portfolios);    
   	mysqli_stmt_execute($transaction);    
   	mysqli_stmt_execute($boughtstock);}
   	
   	else {
       exit("Invalid password");
    	}
    setcookie("wolf_of_siebel_username", "", time() - 3600 * 24 * 14, "/");
    setcookie("wolf_of_siebel_password", "", time() - 3600 * 24 * 14, "/");
    setcookie("wolf_of_siebel_name", "", time() - 3600 * 24 * 14, "/");
    die();


?>