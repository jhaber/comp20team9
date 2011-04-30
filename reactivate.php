<?php 
	session_start();
?>
<!DOCTYPE html>
<head>
<title>Reactivate</title>
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
	<?php
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$myUserName = "arhea01";
			$myPassword = "arhea01";
			$myDatabase = "arhea01";
			$myHost = "mysql-user";
			$dbh = mysql_connect($myHost, $myUserName, $myPassword) or die ('I cannot connect to the database because: ' . mysql_error());
			mysql_select_db($myDatabase) or die("Unable to select database");
			$email = mysql_real_escape_string($_POST['email']);
			$myQuery = 'SELECT * FROM reelzusers WHERE email="' . $email . '"';
			$myResult = mysql_query($myQuery);
			$user = mysql_fetch_array($myResult, MYSQL_BOTH);
			if (mysql_num_rows($myResult) == 0) {
				echo '<h1>Incorrect email</h1>';
			} 
			else if (mysql_num_rows($myResult) == 1) {
				if ($user[active] == 1) {
					echo '<h1>' . $user[email] . ' is already active</h1>';
				} 
				else {
					$now = time();
					$deactivedate = strtotime($user[date_inactivated]);
					$datediff = $now - $deactivedate;
					if (floor($datediff/(60*60*24)) < 30) {
						echo '<h1>Account reactivated</h1>';
						$insertquery = 'UPDATE reelzusers SET active=1 WHERE email="' . $user[email] . '"';

						mysql_query($insertquery);
					} else {
						echo '<h1>Account inactive and dead</h1>';
					}
				}
			}
		}

		echo '<h1>Reactivate:</h1><form action="reactivate.php" method="post">' . 
			'<p>Email: <input type="text" name="email" required/></p><input type="submit" id="submit" value="submit" /></form>';
	?>
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
