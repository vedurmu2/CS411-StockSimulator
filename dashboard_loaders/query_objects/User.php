<?php

require_once("Group.php");
require_once("Portfolio.php");
require_once("BoughtStock.php");

class User {

	private static $loaded_users;

	public $name;
	public $username;

	public $password;
	public $email;

	private $group_objects;
	private $portfolio_objects;

	public static function get_user_object($username) {
		if (is_null(User::$loaded_users))
			User::$loaded_users = array();

		if (isset(User::$loaded_users[$username]))
			return User::$loaded_users[$username];

		$new_user = new User(null, null, null, null);
		$new_user->username = $username;
		if (isset($_SESSION['con'])) $con = $_SESSION['con']; else $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
       

        $result = mysqli_query($con, 'SELECT * FROM Users WHERE username="' . $username . '"');
        if (!$row = mysqli_fetch_array($result)) 
            return null;
        
        $new_user->name = $row['name'];
        $new_user->password = $row['password'];
        $new_user->email = $row['email'];

        User::$loaded_users[$username] = $new_user;

        return $new_user;
	}

	public function __construct($username, $name, $password, $email) {
		if (is_null($username) || is_null($name) || is_null($password) || is_null($email))
			return

		$this->username = $username;
		$this->name = $name;
		$this->password = $password;
		$this->email = $email;

		if (isset($_SESSION['con'])) $con = $_SESSION['con']; else $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

        $stmt = mysqli_prepare($con, "INSERT INTO  Users (name, username, password, email) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $name, $username, $password, $email);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        	return null;
        }

        User::$loaded_users[$username] = $this;
	}

	public function join_group($GID, $portfolio_name, $password) {
		if (isset($_SESSION['con'])) $con = $_SESSION['con']; else $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con)) {
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
            return false;
    	}

    	$group = Group::get_group_object($GID);
    	if (!is_null($group->password) && strcmp($group->password, "") != 0) {
    		if (strcmp($group->password, $password) != 0) {
    			echo "Invalid password : ";
    			return false;
    		}
    	}
        
        $stmt = mysqli_prepare($con, "INSERT INTO  PartOf (GID, username) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "is", $GID, $this->username);
        if (!mysqli_stmt_execute($stmt)) {
            echo "JOIN GROUP ERROR Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        } 

        $this->get_groups();
        $my_new_group = Group::get_group_object($GID);;
        $this->group_objects[$GID] = $my_new_group;
        $this->get_portfolios();
        $this->portfolio_objects[$GID] = new Portfolio($this->username, $portfolio_name, $GID, $my_new_group->start_money);
        return true;
	}

	public function get_portfolios() {
		if (is_null($this->portfolio_objects)) {
			$this->portfolio_objects = array();
			
			$groups = $this->get_groups();
	        foreach ($groups as $group) {
	        	$this->portfolio_objects[$group->GID] = 
	        		Portfolio::get_portfolio_object(null, $group->GID, $this->username);
	        }
	    }
	    return $this->portfolio_objects;
	}

	public function get_groups() {
		if (is_null($this->group_objects)) {
			$this->group_objects = array();
			if (isset($_SESSION['con'])) $con = $_SESSION['con']; else $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
		    if (mysqli_connect_errno($con))
		        echo "Failed to connect to MySQL: " , mysqli_connect_error();

		    $result = mysqli_query($con, 'SELECT * FROM PartOf WHERE username="' . $this->username . '"');
	        while ($row = mysqli_fetch_array($result)) {
	        	$this->group_objects[$row['GID']] = Group::get_group_object($row['GID']);
	        }

	    }
	    return $this->group_objects;
	}

}

?>