<?php 
	session_start();
?>
<!DOCTYPE html>

<?php

	$myUserName = "arhea01";
	$myPassword = "arhea01";
	$myDatabase = "arhea01";
	$myHost = "mysql-user";
	$dbh = mysql_connect($myHost, $myUserName, $myPassword) or die
			('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db($myDatabase) or die("Unable to select database");

    if ($_POST) {

	$legitimate = true;
    $missingItem;

	function notGiven($item) {
	global $legitimate;
	$legitimate = false;
        global $missingItem;
        $missingItem = $item;
	}

	if(!$_POST['firstname']) notGiven("first name");
	$firstname = $_POST['firstname'];
	$firstname = mysql_real_escape_string($firstname);
	if(!$_POST['lastname']) notGiven("last name");
	$lastname = $_POST['lastname'];
	$lastname = mysql_real_escape_string($lastname);
	if(!$_POST['email']) notGiven("email");
	$email = $_POST['email'];
	$email = mysql_real_escape_string($email);
	if(!$_POST['password']) notGiven("password");
	$password = $_POST['password'];
	$password = mysql_real_escape_string($password);
	if($_POST['location']) $location = $_POST['location'];
	$location = mysql_real_escape_string($location);
	if($_POST['genre']) $genre = $_POST['genre'];
	$genre = mysql_real_escape_string($genre);
	if($_POST['movie']) $movie = $_POST['movie'];
	$movie = mysql_real_escape_string($movie);
	if($_POST['cuisine']) $cuisine = $_POST['cuisine'];
	$cuisine = mysql_real_escape_string($cuisine);
	if($_POST['theater']) $theater = $_POST['theater'];
	$theater = mysql_real_escape_string($theater);

	if ($legitimate) {


		$myQuery = 'INSERT INTO reelzusers (first_name, last_name, email, password, def_location, ' .
				   'fav_genre, fav_movie, fav_food, fav_theater)' .
				   ' VALUES ("' . $firstname . '","' .
							$lastname . '","' .
							$email . '","' .
							sha1($password) . '","' .
							$location . '","' .
							$genre . '","' .
							$movie . '","' .
							$cuisine . '","' .
							$theater . '")';

	//echo $myQuery;
		$myResult = mysql_query($myQuery);
		mysql_close($dbh);
	}
	else if(isset($_POST['deactivate']) && isset($_SESSION['username'])) {
		$insertquery = 'UPDATE reelzusers SET active=0 WHERE email="' . $_SESSION['username'] . '"';
		$myResult = mysql_query($insertquery);
		$insertquery = 'UPDATE reelzusers SET date_inactivated = NOW() WHERE email="' . $_SESSION['username'] . '"';
		$myResult = mysql_query($insertquery);
		session_destroy();
		mysql_close($dbh);
	}
	else mysql_close($dbh);





    }

?>





<head>
<title>Account</title>
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
				<li><a href="account.php" title="" class="current"><p>Account</p></a></li>
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

    function form () {
         echo
    '<h1>Register:</h1>' .
    '<form action="account.php" method="post">' .
    '<p>First Name: <input type="text" name="firstname" required/></p>' .
    '<p>Last Name: <input type="text" name="lastname" required/></p>' .
    '<p>Email: <input type="text" name="email" required/></p>' .
    '<p>Password: <input type="password" name="password" required/></p>' .
    '<p>Home Location (optional): <input type="text" name="location"></p>' .
    '<p>Favorite Genre of Movie (optional):<input type="text" name="genre"/></p>' .
    '<p>Favorite Movie (optional): <input type="text" name="movie"/></p>' .
    '<p>Favorite Cuisine: (optional): <select name="cuisine">' .
    '<option value="bagels">Bagels</option>' .
    '<option value="bakeries">Bakeries</option>' .
    '<option value="beer_and_wine">Beer, Wine, Spirits</option>' .
    '<option value="breweries">Breweries</option>' .
    '<option value="coffee">Coffee & Tea</option>'.
    '<option value="desserts">Desserts</option>'.
    '<option value="diyfood">Do-It-Yourself Food</option>'.
    '<option value="farmersmarket">Farmers Market</option>'.
    '<option value="icecream">Ice Cream!</option>'.
    '<option value="juicebars">Juices & Smoothies</option>'.
    '<option value="candy">Candy</option>'.
    '<option value="cheese">Cheese!</option>'.
    '<option value="ethnicmarkets">Ethnic</option>'.
    '<option value="seafoodmarkets">Seafood</option>'.
    '<option value="tea">Tea</option>'.
    '<option value="wineries">More Wine</option>'.
    '</select></p>'.
    '<p>Favorite Theater (optional): <input type="text" name="theater"/></p>' .
    '<input type="submit" id="submit" value="submit" />' .
    '</form>' .
    '</div>';
    }

    if ($_POST) {

        if ($legitimate) echo "<h2>Thank You for your post!</h2>";
        else if (!isset($_SESSION['username'])) { echo "<h2>You didn't enter: " . $missingItem . " </h2>"; form(); }

    }	else if (isset($_SESSION['username'])) {
	echo '<h2>Email</h2><p>' . $_SESSION['username'] . '</p>';
	echo '<h2>Location</h2><p>' . $_SESSION['location'] . '</p>';
	echo '<h2>Food</h2><p>' . $_SESSION['food'] . '</p>';
	echo '<form action="account.php" method="post">' .
	     '<p>Deactivate (enter your password) <input type="password" name="deactivate"/></p>' .
	     '<input type="submit" id="submit" value="submit" /></form>';
    }   else { 
		echo '<h1>Login:</h1>
 		<form action="login.php" method="post">
		<p>Email: <input type="text" name="email" required/></p>
    		<p>Password: <input type="password" name="password" required/></p>
    		<input type="submit" id="submit" value="submit" />
    		</form>';
		form(); 
	}

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
