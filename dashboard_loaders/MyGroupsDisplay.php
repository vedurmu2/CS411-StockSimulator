<?php

  require_once("query_objects/Portfolio.php");

  function show_group_list() {
        $username = $_COOKIE["wolf_of_siebel_username"];
        $user_object = User::get_user_object($username);
        $groups = $user_object->get_groups();
        $portfolios = $user_object->get_portfolios();
        foreach ($groups as $group) {
            $quick_view_code = "<a href=\"?page=MyGroups&showmore" . $group->GID . "\"> More Info </a>";

            if (isset($_GET["show" . $group->GID])) {
               $quick_view_code = "<a href=\"?page=MyGroups\"> Hide </a>";
            }
           

            echo "<tr>";
            echo "  <td> <a href=\"?page=Group&GID=" . $group->GID . "\">" . $group->group_name . " </a> </td>\n";
            echo "  <td> <a href=\"?page=Portfolio&PID=" . $portfolios[$group->GID]->PID . "\">" . 
                $portfolios[$group->GID]->portfolio_name . " </a> </td>\n";
            echo "  <td>" . $portfolios[$group->GID]->get_rank() . "</td>\n";
            echo "  <td> " . count($group->get_group_users()) . "</td>\n";
            //echo "  <td> " . $quick_view_code .  " </td>";
            echo "</tr>";

            if (isset($_GET["showmore" . $group->GID])) {
                echo "<tr> <td>";
                //show_portfolio($group);
                echo "</td> </tr>";
            }
        }
    } 

    function display() {
      echo '<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h2 class="sub-header">Your Groups</h2>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Group Name</th>
                      <th>Portfolio</th>
                      <th>Rank</th>
                      <th>Members</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>';
                    show_group_list();
        echo '    </tbody>
                </table>
              </div>
             </div>';
    }

?>