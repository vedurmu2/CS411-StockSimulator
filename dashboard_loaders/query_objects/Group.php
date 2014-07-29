<?php

require_once("User.php");

class Group {

	private static $loaded_groups;

	private $owner_user_object;
	public $owner_username;

	public $GID;
	public $group_name;

	public $start_money;

	public $password;

	public static function get_group_object($GID) {
		if (is_null(Group::$loaded_groups))
			Group::$loaded_groups = array();

		if (isset(Group::$loaded_groups[$GID]))
			return Group::$loaded_groups[$GID];

		$new_group = new Group(null, null, null, null);
		$new_group->GID = $GID;
		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = mysqli_query($con, 'SELECT * FROM Groups WHERE GID="' . $GID . '"');
        if (!$row = mysqli_fetch_array($result)) 
            return null;
        
        $new_group->start_money = $row['startMoney'];
        $new_group->owner_username = $row['owner'];
        $new_group->group_name = $row['groupName'];
        $new_group->password = $row['password'];

        Group::$loaded_groups[$GID] = $new_group;

        return $new_group;
	}

	public static function get_all_groups($ommit_username) {
		if (is_null(Group::$loaded_groups))
			Group::$loaded_groups = array();

		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = mysqli_query($con, 'SELECT DISTINCT Groups.GID as GID, owner, startMoney, groupName FROM (Groups LEFT OUTER JOIN (SELECT * FROM PartOf WHERE username="' . 
        							$ommit_username . '") as temp1 on Groups.GID=temp1.GID) WHERE temp1.GID IS NULL');
        $ret_value = array();
        while ($row = mysqli_fetch_array($result)) {
        	if (isset(Group::$loaded_groups[$row['GID']])) {
        		$ret_value[$row['GID']] = Group::$loaded_groups[$row['GID']];
        	} else {
        		$new_group = new Group(null, null, null, null);
        		$new_group->GID = $row['GID'];
	        	$new_group->start_money = $row['startMoney'];
	        	$new_group->owner_username = $row['owner'];
	       		$new_group->group_name = $row['groupName'];
	       		$new_group->password = $row['password'];
	       		Group::$loaded_groups[$row['GID']] = $new_group;
	       		$ret_value[$row['GID']] = $new_group;
       		}
        }
        return $ret_value;
	}

	public function __construct($group_name, $start_money, $owner_username, $password) {
		if (is_null($group_name) || is_null($start_money) || is_null($owner_username) || is_null($password))
			return;

		$this->owner_username = $owner_username;
		$this->group_name = $group_name;
		$this->start_money = $start_money;

		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

        $stmt = mysqli_prepare($con, "INSERT INTO Groups (startMoney, groupName, owner, password) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $start_money, $group_name, $owner_username, $password);
        if (!mysqli_stmt_execute($stmt)) {
            echo "BAD GROUP Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        	return null;
        }

        $this->GID = mysqli_insert_id($con);

        Group::$loaded_groups[$this->GID] = $this;
	}

	public function get_owner_user_object() {
		if (is_null($this->owner_user_object)) {
			$this->owner_user_object = User::get_user_object($this->owner_username);
		}
		return $this->owner_user_object;
	}

	public function get_group_users() { 
		$group_users = array();
		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
	    if (mysqli_connect_errno($con))
	        echo "Failed to connect to MySQL: " , mysqli_connect_error();

	    $result = mysqli_query($con, 'SELECT * FROM PartOf WHERE GID="' . $this->GID . '"');
        while ($row = mysqli_fetch_array($result)) {
        	$group_users[$row['username']] = User::get_user_object($row['username']);
        }
		
		return $group_users;
	}

}

?>