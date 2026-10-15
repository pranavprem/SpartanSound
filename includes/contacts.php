<section class="business-talking fullsize" id="contacts">
	<div class="container fullsize">
        <h2>Contact  &nbsp; the &nbsp; Dev</h2>
    <section class="main-section contact" id="contact">

        	<div>
            	<?php
                $contactFile = spartan_root() . '/txt/contacts.txt';
                $lines = is_file($contactFile) ? file($contactFile) : false;
                if ($lines === false) {
                    echo "Contacts are unavailable.";
                } else {
                    foreach ($lines as $line) {
                        echo spartan_e(rtrim($line, "\r\n"));
                        echo "<br/>\n";
                    }
                }
                ?>
            </div>


</section>
</div>
</section>
