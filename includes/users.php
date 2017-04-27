<section class="main-section alabaster" id="users">
	<div class="container fullsize">
               <table align = center>
                   <tr>
                       
                        <td class="tableheader"><strong>First Name</strong></td>
                        <td class="tableheader"><strong>Last Name</strong></td>
                        <td class="tableheader"><strong>Email</strong></td>
                        <td class="tableheader"><strong>Home Address</strong></td>
                        <td class="tableheader"><strong>Home Phone</strong></td>
                        <td class="tableheader"><strong>Cell Phone</strong></td>
                        
                   </tr>
                    <?php
                        if(isset($_POST["search"])){
                            $sql = "SELECT * FROM users WHERE";
                            if(isset($_POST["name"]) and $_POST["name"]!=""){
                                $sql=$sql." firstname LIKE '%".$_POST["name"]."%' OR lastname LIKE '%".$_POST["name"]."%'";
                            }
                            else{
                                $sql=$sql." firstname = '' OR lastname= ''";
                            }
                            if(isset($_POST["email"]) and $_POST["email"] != "" ){
                                $sql=$sql."OR email LIKE '%".$_POST["email"]."%'";
                            }
                            if(isset($_POST["phone"]) and $_POST["phone"] != ""){
                                $sql=$sql."OR homephone LIKE'%".$_POST["phone"]."%' OR cellphone LIKE '%".$_POST["phone"]."%';";
                            }
                            
                            $result = $conn->query($sql);
                            }
                   
                        else{
                            $sql = "SELECT * FROM users";
                            $result = $conn->query($sql);
                        }
                   
                        
                        while($row = $result -> fetch_assoc()){
                            echo "<tr>";
                                echo "<td>";
                                    echo $row["firstname"];
                                echo "</td>";
                                echo "<td>";
                                    echo $row["lastname"];
                                echo "</td>";
                                echo "<td>";
                                    echo $row["email"];
                                echo "</td>";
                                echo "<td>";
                                    echo $row["homeaddress"];
                                echo "</td>";
                                echo "<td>";
                                    echo $row["homephone"];
                                echo "</td>";
                                echo "<td>";
                                    echo $row["cellphone"];
                                echo "</td>";
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