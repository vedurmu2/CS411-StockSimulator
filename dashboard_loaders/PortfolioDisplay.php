<?php

require_once("query_objects/Group.php");
require_once("query_objects/User.php"); 

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
  <th># Shares</th>';
  $username = $_COOKIE["wolf_of_siebel_username"];
  if (strcmp($username, $portfolio->username) == 0)
    echo "<th></th>";

  echo '</tr>
  </thead>
  <tbody>';
  $stocks = $portfolio->get_bought_stocks();
  foreach ($stocks as $stock) {
    echo "<tr>";
    echo "  <td>" . $stock->ticker . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->full_name . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->sector . "</td>\n";
    echo "  <td>" . $stock->get_stock_object()->exchange . "</td>\n";
    echo "  <td>" . $stock->bought_time . "</td>\n";
    echo "  <td>" . $stock->bought_price . "</td>\n";
    echo "  <td>" . $stock->number_of_shares . "</td>\n";
    if (strcmp($username, $portfolio->username) == 0)
      echo '  <td> <a data-toggle="modal" href="#SellStock" onclick="modalType(\'' . $stock->get_stock_object()->full_name . '\' , \'' . 
      $stock->ticker . '\', \'' . $stock->get_stock_object()->get_price() . '\', \'' . $stock->bought_price . '\' , \'' . $portfolio->PID . '\')"> Sell </a> </td>';

    echo "</tr>";
  }

  echo '</tbody>
  </table>';
}

function recommend_modal($stock) {
	if (is_null($stock)) return;

  echo '<div class="modal fade" id="Recommend" tabindex="-1" role="dialog" aria-labelledby="purchaseLabel" aria-hidden="true">
        <div class="modal-dialog">  
              <div class="modal-content">
                <div class="modal-header">
                  <h3> You have been reccomended: ' . $stock->full_name . '</h3>
                  <h4> Ticker: ' . $stock->ticker . '</h4>
                  <h4> Price: ' . $stock->get_price() . '</h4>
                  <h4> Sector: ' . $stock->sector . '</h4>
                  <br>
                  <a href="#" class="btn btn-primary" data-dismiss="modal">Cancel</a>
                  <a href="dashboard.php?page=Stocks&partname=' . $stock->full_name . '&min=' . ($stock->get_price() - 1) .'&max=' . ($stock->get_price() + 1) .'" id="submit" class="btn btn-primary" >Buy?</a><br>
                </div>
              </div>
          </form>
        </div>
      </div>';

}

function display() {
  modaljs();
  sell_stock_modal();

  $PID = $_GET["PID"];
  $portfolio = Portfolio::get_portfolio_object($PID, null, null);
  $username = $_COOKIE["wolf_of_siebel_username"];
  $stockname = $portfolio->get_reccomended_stock();
  echo $stockname;
  recommend_modal(Stock::get_stock_object($stockname));
  echo '<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <h2 class="sub-header">Portfolio : ' . $portfolio->portfolio_name . '</h2>
  <h5 class="sub-header">Money Left : ' . $portfolio->money_left . '</h5>
  <h5 class="sub-header">Networth : ' . $portfolio->get_networth() . '</h5>';
  
  if (!is_null($stockname) && strcmp($username, $portfolio->username) == 0) 
    echo  '<a data-toggle="modal" class="btn btn-primary" href="#Recommend" > Recommend Me a Stock! </a>'; 

  


  echo '<div class="table-responsive">';
  show_portfolio($portfolio);
  echo '</div>
  </div>';
}

?>