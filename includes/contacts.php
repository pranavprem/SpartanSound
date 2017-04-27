<section class="business-talking fullsize" id="contacts">
	<div class="container fullsize">
        <h2>Contact  &nbsp; the &nbsp; Dev</h2>
    <section class="main-section contact" id="contact">
	
        	<div>
            	<?php 
                $contacts = fopen("txt/contacts.txt", "r");
                while(($line=fgets($contacts))!==false){
                    echo $line;
                    echo "<br/>";
                }
                fclose($contacts)
                ?>
            </div>
        	
        
</section>
</div>