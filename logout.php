<?php 
	session_start();
?>
<!DOCTYPE html>
<head>
<title>Log in</title>
<link rel="stylesheet" href="main.css" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Amaranth' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>

<link rel="stylesheet" type="text/css" href="scroll.css">

<script src="http://cdn.jquerytools.org/1.2.5/jquery.tools.min.js"></script>
<script type="text/javascript" src="commandline.js"></script>
<script type="text/javascript" src="sidebar.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		fillSidebar();
	});
</script>
</head>

<body>
<div id="header-wrap">
	<!--<div id="header-container">
		<div id="header">
			<h1>Fixed Header &amp; Footer<em>even IE6 behaves</em></h1>-->
			<ul id="navbar">
				<li><span class="start"><a href="index.php" title=""><img src="images/logo.png" alt="logo"/></a></span></li>
				<li><a href="movie.php" title=""><p>Movies</p></a></li>
				<li><a href="theaters.php" title=""><p>Theaters</p></a></li>
				<li><a href="about.php" title=""><p>About</p></a></li>
				<li><a href="account.php" title=""><p>Account</p></a></li>
				<li><form action="search.php" method="get">
					<input type="text" name="q" placeholder="Enter movie name" />
					<input type="submit" id="searchbutton" value="Submit" />
					</form></li>
			</ul>
	<!--	</div>
	</div>-->

</div>
	<div id="container">
    <div id="content">
   <?php session_destroy(); ?>
    <h1>Logged out</h1>
    </div>
		<div id="sidebar">
		</div>
	</div>
<div id="footer-wrap">
	<div id="footer-container">
		<div id="footer">
		</div>
	</div>
</div>
</body>
</html>
