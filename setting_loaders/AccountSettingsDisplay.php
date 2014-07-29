<?php
require_once("setting_loaders/query_functions/setting_query.php");

  function display() {
  $name = $_COOKIE["wolf_of_siebel_name"];
  $username = $_COOKIE["wolf_of_siebel_username"];
  $email = get_email($username, $_COOKIE["wolf_of_siebel_password"]);
  
    echo '
      <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Account Settings</h1>
		<div class="row placeholders">
            		<div class="col-xs-6 col-sm-3 placeholder">
              		<h1>Name</h2>
             		 <h4>' . $name . '</h4>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <h1>Email</h1>
              <h4>' . $email . '</h4>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <h1>Username</h1>
              <h4>' . $username . '</h4>
            </div>
          </div>

          </div>
          
      </div>';
  }
?>