<?php

require_once("query_objects/Portfolio.php");

function show_group_user_list($group) {
  $_SESSION['con'] = mysqli_connect("engr-cpanel-mysql.engr.illinois.edu", "wolfofsiebel_usr", "qwertyuiop1", "wolfofsiebel_db");;

  $users = $group->get_group_users();
  foreach ($users as $user) {
    $quick_view_code = "<a href=\"?page=Group&GID=" . $group->GID . "&showmore" . $user->username . "\"> Portfolio Quick View </a>";

    if (isset($_GET["showmore" . $user->username])) 
     $quick_view_code = "<a href=\"?page=Group&GID=" . $group->GID . "\"> Hide </a>";

   $portfolio = Portfolio::get_portfolio_object(null, $group->GID, $user->username);
   echo "<tr>";
   echo "  <td>" . $portfolio->portfolio_name . "</td>\n";
   echo "  <td>" . $user->name . "</td>\n";
   echo "  <td> " . count($portfolio->get_all_transactions()) . "</td>\n";
   echo "  <td> ~" . intval($portfolio->get_profit()) .  "</td>\n";
   echo "  <td> " . $quick_view_code . " </td>";
   echo "</tr>";

    if (isset($_GET["showmore" . $user->username])) {
      echo "<tr> <td colspan=\"5\" style=\"
    padding-left: 30px;
    padding-right: 30px;
    padding-bottom: 20px;
    \">";
        show_portfolio($portfolio);
      echo "</td> </tr>";
    }
  }
  unset($_SESSION['con']);
} 

function modaljs() {
    echo '<script type="text/javascript">
                  $(document).ready(function(){
              $("#SellStock").submit(function(event){
                $.post($("#SellStockForm").attr("action"), $("#SellStockForm").serialize(), function(data){ 
                    if (data != "") {
                      $("#errormessegesell").html(data);
                    } else {
                      $("#SellStock").modal("hide");
                      window.location.reload()
                    }

                  }) .fail(function() {
                  });
                  //$("#SignUp").modal("hide");
                  
                  event.preventDefault();
              });
          });      
      </script>';

    echo "<script type=\"text/javascript\">
       function modalType(name, ticker, price, boughtPrice, PID) { 
          $(\"#modalPID\")[0].value = PID;
          $(\"#modalticker\")[0].value = ticker;
          $(\"#stockname\").html(\"Stock Name:\" + name);
          $(\"#stockticker\").html(\"Stock Ticker:\" + ticker);
          $(\"#stockprice\").html(\"Stock Price:\" + price);
          $(\"#stockchange\").html(\"Stock Change:\" + (price - boughtPrice));
       }
    </script>";
  }

function sell_stock_modal() {
  echo '<div class="modal fade" id="SellStock" tabindex="-1" role="dialog" aria-labelledby="purchaseLabel" aria-hidden="true">
        <div class="modal-dialog">  
            <form id="SellStockForm" action="/~wolfofsiebel/form_submisions/sell_stock_form.php" method="post">
              <input type="hidden" id="modalPID" name="PID" value="0">
              <input type="hidden" id="modalticker" name="ticker" value="0">
              <div class="modal-content">
                <div class="modal-header">
                  <h3> Sell Stock! </h3>
                  <h5 id="stockname"> </h3>
                  <h5 id="stockticker"> </h3>
                  <h5 id="stockprice"> </h3>
                  <h5 id="stockchange"> </h3>
                  <h5 id="errormessegesell"> </h3>
                </div>
                <div class="modal-body" style= "padding-top: 0px;" >
                  <div class="divDialogElements">
                    <h4>Number of Shares</h4>
                    <input class="xlarge" id="xlInput" name="numShares" value="" type="text">
                  </div>
                </div>
                <div class="modal-footer">
                  <a href="#" class="btn btn-primary" data-dismiss="modal">Cancel</a>
                  <input type="submit" id="submit" class="btn btn-primary" value="Submit"><br>
                </div>
              </div>
          </form>
        </div>
      </div>';
}

function show_portfolio($portfolio) {

  echo '<table class="table table-striped">
  <thead>
  <tr>
  <th>Ticker</th>
  <th>Full Name</th>
  <th>Industry</th>
  <th>Market</th>
  <th>Bought Time</th>
  <th>Bought Price</th>
  <th># Shares</th>
  </tr>
  </thead>
  <tbody>';

  $username = $_COOKIE["wolf_of_siebel_username"];
  $stocks_in_port = $portfolio->get_bought_stocks();
  foreach ($stocks_in_port as $stock) {
    echo "<tr>";
    echo "  <td>" . $stock->ticker . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->full_name . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->sector . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->exchange . "</td>\n";
    echo "  <td>" . $stock->bought_time . "</td>\n";
    echo "  <td>" . $stock->bought_price . "</td>\n";
    echo "  <td>" . $stock->number_of_shares. "</td>\n";
    if (strcmp($portfolio->username, $username) == 0)
      echo '  <td> <a data-toggle="modal" href="#SellStock" onclick="modalType(\'' . $stock->get_stock_object()->full_name . '\' , \'' . 
      $stock->ticker . '\', \'' . $stock->get_stock_object()->get_price() . '\', \'' . $stock->bought_price . '\' , \'' . $portfolio->PID . '\')"> Sell </a> </td>';
    else 
      echo ' <td></td>';

    echo "</tr>";
  }

  echo '</tbody>
  </table>';
}


function display() {
  $group = Group::get_group_object($_GET["GID"]);

  modaljs();
  sell_stock_modal();

  echo '<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <h2 class="sub-header">' . $group->group_name . '</h2> <br>
  <h3 class="sub-header"> Owned by ' . $group->owner_username . '</h3>
  <h3 class="sub-header"> Start money: ' . $group->start_money . '</h3>
  <div class="table-responsive">
  <table class="table table-striped">
  <thead>
  <tr>
  <th>Port Name</th>
  <th>Owner </th>
  <th>Number of Transactions</th>
  <th> Profit </th>
  <th></th>
  </tr>
  </thead>
  <tbody>';
  show_group_user_list($group);
  echo '    </tbody>
  </table>
  </div>
  </div>';
}

?>