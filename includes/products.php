<section class="main-section paddind" id="products"><!--main-section-start-->
	<div class="container">
    	<h2>Products</h2>
      <div class="portfolioFilter">
        <ul class="Portfolio-nav wow fadeIn delay-02s">
            <li><a href="#" data-filter="*" class="current" >All</a></li>
            <?php
                $products = spartan_products($conn);
                foreach (spartan_collect_keywords($products) as $token) {
                    echo "<li><a href='#' data-filter='." . spartan_e($token) . "' >" . spartan_e($token) . "</a></li>\n";
                }
            ?>
        </ul>
       </div>

	</div>
    <div class="portfolioContainer wow fadeInUp delay-04s">
            	<?php
                    foreach ($products as $row) {
                        $classes = implode(' ', spartan_keyword_tokens((string) $row['keywords']));
                        echo "<div class=' Portfolio-box " . spartan_e($classes) . "'>\n";
                        echo "<a href='./viewproduct.php?id=" . (int) $row['id'] . "'>";
                        echo "<img style='width:50%' src='" . spartan_e((string) $row['imgUrl']) . "' alt='" . spartan_e((string) $row['name']) . "'>\n";
                        echo "</a>";
                        echo "<h3>" . spartan_e((string) $row['name']) . "</h3>\n";
                        echo "<p>$" . spartan_e((string) $row['price']) . "</p>\n";
                        if ((string) $row['ud1'] !== '') {
                            echo "<audio controls><source src='" . spartan_e((string) $row['ud1']) . "'></audio>\n";
                        }
                        echo "</div>\n\n";
                    }
                ?>


    </div>
</section>
