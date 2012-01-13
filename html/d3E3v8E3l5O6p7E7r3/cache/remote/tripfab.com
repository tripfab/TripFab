<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
		<title>Tripfab - Making your trip fabulous!</title>
		<link type="text/css" rel="stylesheet" href="/css3/reset.css" />
		<link type="text/css" rel="stylesheet" href="/css3/all.css" />
		<link rel="icon" type="image/png" href="/images3/favicon.png" />
	</head>
	<body>
		<div id="main">
			<div class="wrapper">
				<div class="left">
					<img class="icon" src="/images3/ico-1.png" alt="" />
					<img class="logo" src="/images3/logo.png" alt="" />
				</div>
				<!-- closes left -->
				<div class="right">
					<img src="/images3/ico-2.png" alt="" />
					<p>The travel industry is going to <img src="/images3/ico-3.png" alt="" /> its pants. <span>Seriously</span></p>
					<div class="form-wrapper">
						<div class="w3">
							<form action="" class="email-form">
								<fieldset>
									<input name="email" type="text" class="required email" value="Get news and updates..." />
									<input class="send" type="submit" value="" />
								</fieldset>
							</form>
							<img src="/images3/ico-4.png" alt="" class="curiousity" />
						</div>
						<!-- closes w3 -->
					</div>
					<!-- closes form-wrapper -->
					<div class="text">
						<strong>Why should I enter my email?</strong> 
						<em>Enter your email and be our first BETA testers, along with getting some cool surprise stuff from us as a thank you. </em>
					</div>
					<!-- closes text -->
				</div>
				<!-- closes right -->
				<img src="/images3/bg.png" alt="" class="bg" />
				<div class="clear"></div>
			</div>
			<!-- closes wrapper -->
			<div class="social">
				<div class="w2">
					<ul>
						<li><a href="https://twitter.com/#!/TripFab"><img src="/images3/ico-7.png" alt="" />Follow us</a></li>
						<li><a href="https://www.facebook.com/TripFab"><img src="/images3/ico-6.png" alt="" />Become our fan</a></li>
						<li><a href="mailto:hello@tripfab.com"><img src="/images3/ico-8.png" alt="" />hello@tripfab.com</a></li>
						<li><a href="http://blog.tripfab.com/"><img src="/images3/ico-10.png" alt="" />Read Our Blog</a></li>
						<li><a href="https://plus.google.com/108576021199856132768"><img src="/images3/ico-11.png" alt="" />+1 us</a></li>
					</ul>
				</div>
				<!-- closes w2 -->
			</div>
			<!-- closes wrapper -->
			<div id="footer">
				<div class="w1">
					<div class="wrapper">
						<img src="/images3/logo-2.png" alt="" />
						<p>Copyright &copy; 2012 Tripfab - All rights reserved</p>
						<p>Made in a tropical paradise</p>
					</div>
					<!-- closes wrapper -->
				</div>
				<!-- closes w1 -->
			</div>
			<!-- closes footer -->
		</div>
		<!-- closes main -->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script type="text/javascript" src="/js3/jquery.validate.min.js"></script>
		<script type="text/javascript" src="/js3/main.js"></script>
        <script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-24981182-1']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>
	</body>
</html>