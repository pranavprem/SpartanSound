<section class="main-section paddind" id="products"><!--main-section-start-->
	<div class="container">
    	<h2>Products</h2>
      <div class="portfolioFilter">  
        <ul class="Portfolio-nav wow fadeIn delay-02s">
            <li><a href="#" data-filter="*" class="current" >All</a></li>
            <?php
                $sql = "SELECT * FROM products";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $keywords = $row["keywords"];
                while($row=$result->fetch_assoc()){
                    if(strpos($keywords," ".$row["keywords"]." ")===false){
                        $keywords=$keywords.", ".$row["keywords"];
                    }
                }
                
                foreach (explode(", ",$keywords) as $key){
                    str_replace(" ","_",$key);
                    echo "<li><a href='#' data-filter='.".$key."' >".$key."</a></li>";
                    
                }
                
            ?>
        </ul>
       </div> 
        
	</div>
    <div class="portfolioContainer wow fadeInUp delay-04s">
            	<?php
                    $sql = "SELECT * FROM products";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                        $str="";
                        foreach(explode(", ",$row["keywords"]) as $key){
                            str_replace(" ","_",$key);
                            $str=$str." ".$key;
                        }          
                        
                        
                        echo "<div class=' Portfolio-box ".$str."'>\n";
                        echo "<a href='./viewproduct?id=".$row["id"]."'>";
                        echo "<img style='width:50%' src = '".$row["imgUrl"]."'>\n";
                        echo "</a>";
                        echo "<h3>".$row["name"]."</h3>\n";    
                        echo "<p>$".$row["price"]."</p>\n";
                        if($row["ud1"]!=""){
                            echo "<audio controls><source src='".$row["ud1"]."'></audio>\n";
                        }
                        echo "</div>\n\n";
                    }
                ?>
                
                
    </div>
</section>