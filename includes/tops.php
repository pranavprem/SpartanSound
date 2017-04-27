<section class="main-section alabaster" id="tops">
	<div class="container fullsize">
    	<div class="row">
			<div class="col-lg-5 col-sm-4 wow fadeInLeft">
            	<h2>Global top viewed</h2>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Hits</th>
                    </tr>
                <?php
                $sql = "SELECT * FROM products ORDER BY hits DESC";
                $results = $conn->query($sql);
                for($i=0; $i<5; $i++){
                    $row = $results->fetch_assoc();
                    echo "<tr>";
                    echo "<td><a href='./viewproduct.php?id=".$row["id"]."'>".$row["name"]."</a></td>";
                    echo "<td>".$row["hits"]."</td>";
                    echo "</tr>";
                }
                
                ?>
                </table>
                
            </div>
        	<div class="col-lg-7 col-sm-8 wow fadeInRight">
            	<h2>Your top viewed</h2>
                <?php
                    if(isset($_COOKIE["lastids"])){
                        echo "<table>";
                        echo "<tr>";
                        echo "<th>Name</th>";
                        echo "<th>Hits</th>";
                        echo "</tr>";        
                     $heatmap=array();
                     foreach (explode(",",$_COOKIE["lastids"]) as $id){
                         if(isset($heatmap[$id])){
                             $heatmap[$id] = $heatmap[$id] + 1;
                         }
                         else {
                             $heatmap[$id] = 1;
                         }
                     }
                    for($i=0;$i<5;$i++){
                     $max=0;
                     $maxid=0;
                    foreach ($heatmap as $key => $value){
                         if($value>$max){
                             $max = $value;
                             $maxid = $key;
                         }
                     }
                    $result = $conn->query("SELECT * FROM products where id = ".$maxid.";");
                    $row = $result->fetch_assoc();
                    echo "<tr>";
                    echo "<td><a href='./viewproduct.php?id=".$row["id"]."'>".$row["name"]."</a></td>";
                    echo "<td>".$max."</td>";
                    echo "</tr>";
                    unset($heatmap[$maxid]);    
                    }
                     
                    }
                    else{
                        echo "You have not viewed any products";
                          
                    }
                    echo "</table>";
                ?>
                
            </div>
        	<div class="col-lg-7 col-sm-12 wow fadeInRight">
            	<h2>Your Last 5 viewed</h2>
                
                <?php
                    if(isset($_COOKIE["lastids"])){
                        echo "<table>";
                        echo "<tr>";
                        echo "<th>Name</th>";
                        echo "<th>Description</th>";
                        echo "</tr>"; 
                        $hits = explode(",",$_COOKIE["lastids"]);
                        $viewed = array();
                        for($i=0; $i<5 and $i<sizeof($hits); $i++){
                            $result = $conn->query("SELECT * FROM products where id = ".$hits[$i].";");
                            $row = $result->fetch_assoc();
                            echo "<tr>";
                            echo "<td><a href='./viewproduct.php?id=".$row["id"]."'>".$row["name"]."</a></td>";
                            echo "<td>".$row["description"]."</td>";
                            echo "</tr>";
                            array_push($viewed,$hits[$i]);
                        }
                        echo "</table>";
                    }
                    else{
                        echo "You have not viewed any products";
                    }
                ?>
                
            </div>
        </div>
	</div>
   
    
    
</section>