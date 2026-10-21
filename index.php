<?php

require_once __DIR__ . '/includes/dbconnect.php';
?>
<!doctype html>
<html lang="en">
<!-- This website is built modifying styles and themes originally created by bootstrapmade-->

    <head>
        <?php require __DIR__ . "/includes/head.php" ?>
        <title>SpartanSound</title>
    </head>
    <body>
        <?php require __DIR__ . "/includes/newuser.php" ?>
        <?php require __DIR__ . "/includes/home.php" ?>
        <?php require __DIR__ . "/includes/navbar.php" ?>
        <?php require __DIR__ . "/includes/tops.php" ?>
        <?php require __DIR__ . "/includes/about.php" ?>
        <?php require __DIR__ . "/includes/services.php" ?>
        <?php require __DIR__ . "/includes/products.php" ?>
        <?php require __DIR__ . "/includes/news.php" ?>
        <?php require __DIR__ . "/includes/contacts.php" ?>
        <?php require __DIR__ . "/includes/users.php" ?>
        <?php require __DIR__ . "/includes/login.php" ?>
        <?php require __DIR__ . "/includes/searchuser.php" ?>

<footer class="footer">
    <div class="container">
        <div class="credits">
            This website is based on styles and animations from <a href="https://bootstrapmade.com/free-business-bootstrap-themes-website-templates/">Business Bootstrap Themes</a> by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
    </div>
</footer>


<script type="text/javascript">
    $(document).ready(function(e) {
        $('#test').scrollToFixed();
        $('.res-nav_click').click(function(){
            $('.main-nav').slideToggle();
            return false

        });

    });
</script>

  <script>
    wow = new WOW(
      {
        animateClass: 'animated',
        offset:       100
      }
    );
    wow.init();
  </script>


<script type="text/javascript">
	$(window).load(function(){

		$('.main-nav li a, .servicelink').bind('click',function(event){
			var $anchor = $(this);
			if ($anchor.attr('data-toggle') === 'modal') {
				return;
			}
			var href = $anchor.attr('href');
			if (!href || href === '#') {
				return;
			}
			var $target = $(href);
			if (!$target.length || !$target.offset()) {
				return;
			}

			$('html, body').stop().animate({
				scrollTop: $target.offset().top - 102
			}, 1500,'easeInOutExpo');
			/*
			if you don't want to use the easing effects:
			$('html, body').stop().animate({
				scrollTop: $($anchor.attr('href')).offset().top
			}, 1000);
			*/
			event.preventDefault();
		});
	})
</script>

<script type="text/javascript">

$(window).load(function(){


  var $container = $('.portfolioContainer'),
      $body = $('body'),
      colW = 375,
      columns = null;


  $container.isotope({
    // disable window resizing
    resizable: true,
    masonry: {
      columnWidth: colW
    }
  });

  $(window).smartresize(function(){
    // check if columns has changed
    var currentColumns = Math.floor( ( $body.width() -30 ) / colW );
    if ( currentColumns !== columns ) {
      // set new column count
      columns = currentColumns;
      // apply width to container manually, then trigger relayout
      $container.width( columns * colW )
        .isotope('reLayout');
    }

  }).smartresize(); // trigger resize to set container width
  $('.portfolioFilter a').click(function(){
        $('.portfolioFilter .current').removeClass('current');
        $(this).addClass('current');

        var selector = $(this).attr('data-filter');
        $container.isotope({

            filter: selector,
         });
         return false;
    });

});

</script>

</body>
</html>
