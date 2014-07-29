<?php

  require_once("query_objects/Portfolio.php");

  function show_leaderboard() {
        if (isset($_SESSION['con'])) $con = $_SESSION['con']; else $con = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");
        if (mysqli_connect_errno($con))
            echo "Failed to connect to MySQL: " , mysqli_connect_error();
      
        $result = mysqli_query($con, 'SELECT * FROM Portfolio');
        $vals = array();
        while ($row = mysqli_fetch_array($result)) {
              $port = Portfolio::get_portfolio_object($row['PID'], null, null);
              $vals[$port->get_profit()] = $port;
        }

        krsort($vals);
        foreach ($vals as $key => $value) {
            echo '<tr>';
            echo "  <td> <a href=\"?page=Portfolio&PID=" . $value->PID . "\">" . 
                $value->portfolio_name . " </a> </td>\n";
            echo "  <td>" . $value->username . "</td>\n";
            echo "  <td> " . $key . "</td>\n";
            echo "</tr>";
        }
    } 

    function display() {
      echo '<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h2 class="sub-header">LeaderBoards</h2>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Portfolio</th>
                      <th>Owner</th>
                      <th>Profit</th>
                    </tr>
                  </thead>
                  <tbody>';
                    show_leaderboard();
        echo '    </tbody>
                </table>
              </div>
             </div>';
    }

?>