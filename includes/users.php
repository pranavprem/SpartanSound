<section class="main-section alabaster" id="users">
	<div class="container fullsize">
               <?php if (($_GET['user'] ?? '') === 'missing') { ?>
               <p>A user needs a first name and a last name.</p>
               <?php } ?>
               <table align="center">
                   <tr>

                        <td class="tableheader"><strong>First Name</strong></td>
                        <td class="tableheader"><strong>Last Name</strong></td>
                        <td class="tableheader"><strong>Email</strong></td>
                        <td class="tableheader"><strong>Home Address</strong></td>
                        <td class="tableheader"><strong>Home Phone</strong></td>
                        <td class="tableheader"><strong>Cell Phone</strong></td>

                   </tr>
                    <?php
                        $searching = isset($_POST['search']);
                        $rows = spartan_search_users($conn, $searching ? $_POST : []);
                        if ($rows === []) {
                            echo "<tr><td colspan='6'>No users matched that search.</td></tr>";
                        }
                        foreach ($rows as $row) {
                            echo "<tr>";
                            foreach (['firstname', 'lastname', 'email', 'homeaddress', 'homephone', 'cellphone'] as $column) {
                                echo "<td>" . spartan_e((string) $row[$column]) . "</td>";
                            }
                            echo "</tr>";
                        }
                    ?>
               </table>
        <br/>
        <br/>
        <center>
            <a class="link animated fadeInUp delay-2s servicelink" href="#" data-toggle="modal" data-target="#create-modal">ADD</a>
            <a class="link animated fadeInDown delay-1s servicelink" href="#" data-toggle="modal" data-target="#search-modal">SEARCH</a>
        </center>
           </div>
</section>
