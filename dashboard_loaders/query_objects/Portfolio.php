<?php

require_once("User.php");
require_once("Group.php");
require_once("BoughtStock.php");

class Portfolio {

	private static $loaded_portfolios;

	private $owner_user_object;
	public $username;

	public $portfolio_name;

	public $GID;
	private $group_object;

	public $money_left;

	public $PID;

	private $bought_stocks_objects;

	private $all_transactions;

	public static function get_portfolio_object($PID, $GID, $username) {
		if (is_null(Portfolio::$loaded_portfolios))
			Portfolio::$loaded_portfolios = array();

		if (!is_null($PID) && isset(Portfolio::$loaded_portfolios[$PID]))
			return Portfolio::$loaded_portfolios[$PID];

		$new_port = new Portfolio(null, null, null, null);
		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
        
        $result = null;
        if (is_null($PID)) 
        	$result = mysqli_query($con, 'SELECT * FROM Portfolio WHERE GID="' . $GID . '" AND username="' . $username . '"');
        else 
        	$result = mysqli_query($con, 'SELECT * FROM Portfolio WHERE PID="' . $PID . '"');
        
        if (!$row = mysqli_fetch_array($result)) 
            return null;
        
        $new_port->username = $row['username'];
        $new_port->GID = $row['GID'];
        $new_port->money_left = $row['moneyLeft'];
        $new_port->portfolio_name = $row['pName'];
        $new_port->PID = $row['PID'];

        Portfolio::$loaded_portfolios[$PID] = $new_port;

        return $new_port;
	}

	public function __construct($username, $portfolio_name, $GID, $money_left) {
		if (is_null($username) || is_null($username) || is_null($GID) || is_null($money_left))
			return;

		$this->username = $username;
		$this->portfolio_name = $portfolio_name;
		$this->GID = $GID;
		$this->money_left = $money_left;

		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();

        $stmt = mysqli_prepare($con, "INSERT INTO  Portfolio (GID, moneyLeft, pName, username) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiss", $GID, $money_left, $portfolio_name, $username);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        	return;
        }

        $this->PID = mysqli_insert_id($con);
        Portfolio::$loaded_portfolios[$this->PID] = $this;
	}

	public function get_owner_user_object() {
		if (is_null($this->owner_user_object)) {
			$this->owner_user_object = User::get_user_object($this->username);
		}
		return $this->owner_user_object;
	}

	public function get_group_object() { 
		if (is_null($this->group_object)) {
			$this->group_object = Group::get_group_object($this->GID);
		}
		return $this->group_object;
	}

	public function remove_bought_stock($ticker) {
		if (is_null($this->bought_stocks_objects)) 
			return;

		unset($this->bought_stocks_objects[$ticker]);
	} 

	public function get_profit() {
		return $this->get_networth() - $this->get_group_object()->start_money;
	}

	public function get_networth() {
		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
	    if (mysqli_connect_errno($con))
	        echo "Failed to connect to MySQL: " , mysqli_connect_error();
	        
	    $result = mysqli_query($con, 'SELECT SUM(numShares * price) as total FROM BoughtStock NATURAL JOIN Stock WHERE PID="' . $this->PID . '" GROUP BY PID');
	    if (!$row = mysqli_fetch_array($result)) 
            return $this->money_left;

        return $row['total'] + $this->money_left;
	}

		public function get_reccomended_stock() {
		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
	    if (mysqli_connect_errno($con))
	        echo "Failed to connect to MySQL: " , mysqli_connect_error();

	    $result = mysqli_query($con, 'SELECT sector FROM BoughtStock NATURAL JOIN Stock WHERE PID="' . $this->PID . '" GROUP BY sector ORDER BY count(*) DESC LIMIT 1');
	    if (!$row = mysqli_fetch_array($result)) 
            return null;

        $common_sec = $row['sector'];

        $result = mysqli_query($con, 'SELECT AVG(boughtPrice) as avg FROM BoughtStock WHERE PID="' . $this->PID . '"');
	    if (!$row = mysqli_fetch_array($result)) 
            return null;

        $common_price = $row['avg'];
        $result = mysqli_query($con, 'SELECT ticker FROM Stock WHERE sector="' . $common_sec. '" AND price>"' . ($common_price - 20) . '" AND price<"' . ($common_price + 20) . '" ORDER BY RAND() LIMIT 20');

        $file = "http://download.finance.yahoo.com/d/quotes.csv?s=";
        while ($row = mysqli_fetch_array($result)) 
            $file = $file . $row['ticker'] . "+";

        if (strcmp($file, "http://download.finance.yahoo.com/d/quotes.csv?s=") == 0) {
        	$result = mysqli_query($con, 'SELECT ticker FROM Stock WHERE sector="' . $common_sec. '" ORDER BY RAND() LIMIT 20');
		    $file = "http://download.finance.yahoo.com/d/quotes.csv?s=";
		    while ($row = mysqli_fetch_array($result)) 
		            $file = $file . $row['ticker'] . "+";
        }
        
        $file = $file . "&f=sm8&e=.csv";
        $file_handle = fopen($file, 'r');
        while (!feof($file_handle) ) {
        	$val = fgetcsv($file_handle, 1024);
            $line_of_text[$val[0]] = doubleval(substr($val[1], 0, -1));
        }  
        fclose($file_handle);

        arsort($line_of_text);
        foreach ($line_of_text as $key=>$val) {
        	return $key;
        }
        return null;
	}


	public function get_rank() {

		$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
	    if (mysqli_connect_errno($con))
	        echo "Failed to connect to MySQL: " , mysqli_connect_error();

	    $ret = array();
		$result = mysqli_query($con, 'SELECT PID, moneyLeft FROM Portfolio WHERE GID="' . $this->GID . '"');
		while ($row = mysqli_fetch_array($result)) {
			$ret[$row['PID']] = doubleval($row['moneyLeft']);
		}

	    $result = mysqli_query($con, 'SELECT SUM(numShares * boughtPrice) as total, PID FROM Portfolio NATURAL JOIN BoughtStock WHERE GID="' . $this->GID . '" GROUP BY PID');
	    while ($row = mysqli_fetch_array($result)) 
			$ret[$row['PID']] = $ret[$row['PID']] + doubleval($row['total']);
		

	    asort($ret);

	    $i = 0;
	    foreach ($ret as $key => $value) {
	    	if ($key == $this->PID)
	    		return $i;

            $i ++;
    	}
    	return -1;
	}

	public function get_all_transactions() {
		if (is_null($this->all_transactions)) {
			$this->all_transactions = Transaction::get_all_transactions($this->PID);
		}
		return $this->all_transactions;
	}

	public function get_bought_stocks() {
		if (is_null($this->bought_stocks_objects)) {
			$con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
	        if (mysqli_connect_errno($con))
	            echo "Failed to connect to MySQL: " , mysqli_connect_error();
	        
	        $result = mysqli_query($con, 'SELECT * FROM BoughtStock WHERE PID="' . $this->PID . '"');
	        $this->bought_stocks_objects = array();
	        while ($row = mysqli_fetch_array($result))
	            $this->bought_stocks_objects[$row['ticker']] = BoughtStock::get_bought_stock_object($row['ticker'], $row['PID']);
		}
		return $this->bought_stocks_objects;
	}

}

?>