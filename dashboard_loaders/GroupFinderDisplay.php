<?php

	require_once("query_objects/Group.php");

	function show_all_groups() {
    $username = $_COOKIE["wolf_of_siebel_username"];
		$groups = Group::get_all_groups($username);
    foreach ($groups as $group) {
      echo "<tr>";
      echo "  <td>" . $group->group_name . "</td>\n";
      echo "  <td>" . $group->owner_username . "</td>\n";
      echo "  <td>" . count($group->get_group_users()) . "</td>\n";
      echo '  <td> <a data-toggle="modal" href="#JoinGroup" onclick="modalType(' . $group->GID . ', \'' . $group->group_name . '\')"> Join </a> </td>';
      echo "</tr>";
    }
	}

  function modaljs() {
    echo "<script type=\"text/javascript\">
       function modalType(index, name) { 
          $(\"#modalnum\")[0].value = index;
          $(\"#modaltitle\").html(\"Join Group: \" + name);
       }
    </script>";
  }

  function formjs() {
    echo '<script type="text/javascript">
                  $(document).ready(function(){
              $("#CreateGroup").submit(function(event){
                $.post($("#CreateGroupForm").attr("action"), $("#CreateGroupForm").serialize(), function(data){ 
                    if (data != "") {
                      $("#errormessegecreate").html(data);
                    } else {
                      $("#CreateGroup").modal("hide");
                    }

                  }) .fail(function() {
                  });
                  //$("#SignUp").modal("hide");
                  
                  event.preventDefault();
              });
          });      
      </script>';

      echo '<script type="text/javascript">
                  $(document).ready(function(){
              $("#JoinGroup").submit(function(event){
                $.post($("#JoinGroupForm").attr("action"), $("#JoinGroupForm").serialize(), function(data){ 
                    if (data != "") {
                      $("#errormessegejoin").html(data);
                    } else {
                      $("#JoinGroup").modal("hide");
                    }

                  }) .fail(function() {
                  });
                  //$("#SignUp").modal("hide");
                  
                  event.preventDefault();
              });
          });      
      </script>';
  }

  function create_modal_display() {
    echo '<div class="modal fade" id="CreateGroup" tabindex="-1" role="dialog" aria-labelledby="purchaseLabel" aria-hidden="true">
        <div class="modal-dialog">  
            <form id="CreateGroupForm" action="/~wolfofsiebel/form_submisions/create_group_form.php" method="post">
              <div class="modal-content">
                <div class="modal-header">
                  <h3> Create New Group: </h3>
                  <h3 id="errormessegecreate"> </h3>
                </div>
                <div class="modal-body" style= "padding-top: 0px;" >
                  <div class="divDialogElements">
                    <h4>Group Name</h4>
                    <input class="xlarge" id="xlInput" name="groupname" value="" type="text">
                    <h4>Password (Optional) </h4>
                    <input class="xlarge" id="xlInput" name="password" value="" type="password">
                    <h4>Initial Money</h4>
                    <input class="xlarge" id="xlInput" name="startmoney" value="" type="text">

                    <hr style="height:1px;border:none;color:#333;background-color:#333;">
                    <h4>Your Portfolio Name</h4>
                    <input class="xlarge" id="xlInput" name="portfolio" value="" type="text">
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

  function join_modal_display() {

    echo '<div class="modal fade" id="JoinGroup" tabindex="-1" role="dialog" aria-labelledby="purchaseLabel" aria-hidden="true">
        <div class="modal-dialog">  
            <form id="JoinGroupForm" action="/~wolfofsiebel/form_submisions/join_group_form.php" method="post">
              <input type="hidden" id="modalnum" name="groupnum" value="0">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 id="modaltitle"> </h3>
                  <h3 id="errormessegejoin"> </h3>
                </div>
                <div class="modal-body" style= "padding-top: 0px;" >
                  <div class="divDialogElements">
                    <h4>Portfolio Name</h4>
                    <input class="xlarge" id="xlInput" name="portfolio" value="" type="text">
                    <h4>Password</h4>
                    <input class="xlarge" id="xlInput" name="password" value="" type="text">
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


	function display() {
    formjs();
    join_modal_display();
    create_modal_display();
    modaljs();

		echo '<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h2 class="sub-header">Group Finder</h2>
              <a data-toggle="modal" class="btn btn-primary" href="#CreateGroup"> Create Group </a>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Group Name</th>
                      <th>Owner</th>
                      <th>Members</th>
                      <th>Join!</th>
                    </tr>
                  </thead>
                  <tbody>';
                    show_all_groups();
    echo '        </tbody>
                </table>
              </div>
             </div>';
	}

?>