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
                $topProducts = spartan_top_products($conn, 5);
                if ($topProducts === []) {
                    echo "<tr><td colspan='2'>No products yet.</td></tr>";
                }
                foreach ($topProducts as $row) {
                    echo "<tr>";
                    echo "<td><a href='./viewproduct.php?id=" . (int) $row['id'] . "'>" . spartan_e((string) $row['name']) . "</a></td>";
                    echo "<td>" . spartan_e((string) $row['hits']) . "</td>";
                    echo "</tr>";
                }
                ?>
                </table>

            </div>
        	<div class="col-lg-7 col-sm-8 wow fadeInRight">
            	<h2>Your top viewed</h2>
                <?php
                    $cookieIds = isset($_COOKIE['lastids']) ? (string) $_COOKIE['lastids'] : '';
                    $ranked = $cookieIds === '' ? [] : spartan_top_viewed_ids($cookieIds, 5);
                    $rankedRows = spartan_products_by_ids($conn, array_column($ranked, 'id'));
                    if ($ranked === []) {
                        echo "You have not viewed any products";
                    } else {
                        echo "<table>";
                        echo "<tr>";
                        echo "<th>Name</th>";
                        echo "<th>Your views</th>";
                        echo "</tr>";
                        foreach ($ranked as $entry) {
                            if (!isset($rankedRows[$entry['id']])) {
                                continue;
                            }
                            $row = $rankedRows[$entry['id']];
                            echo "<tr>";
                            echo "<td><a href='./viewproduct.php?id=" . (int) $row['id'] . "'>" . spartan_e((string) $row['name']) . "</a></td>";
                            echo "<td>" . (int) $entry['views'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                ?>

            </div>
        	<div class="col-lg-7 col-sm-12 wow fadeInRight">
            	<h2>Your Last 5 viewed</h2>

                <?php
                    $recent = $cookieIds === '' ? [] : spartan_recent_ids($cookieIds, 5);
                    $recentRows = spartan_products_by_ids($conn, $recent);
                    if ($recent === []) {
                        echo "You have not viewed any products";
                    } else {
                        echo "<table>";
                        echo "<tr>";
                        echo "<th>Name</th>";
                        echo "<th>Description</th>";
                        echo "</tr>";
                        foreach ($recent as $recentId) {
                            if (!isset($recentRows[$recentId])) {
                                continue;
                            }
                            $row = $recentRows[$recentId];
                            echo "<tr>";
                            echo "<td><a href='./viewproduct.php?id=" . (int) $row['id'] . "'>" . spartan_e((string) $row['name']) . "</a></td>";
                            echo "<td>" . spartan_e((string) $row['description']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                ?>

            </div>
        </div>
	</div>



</section>
