<!DOCTYPE html>

<head>
<title>Movies</title>
<link rel="stylesheet" href="main.css" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Amaranth' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="scroll.css">


<script src="http://cdn.jquerytools.org/1.2.5/jquery.tools.min.js"></script>
<script type="text/javascript" src="commandline.js"></script>
<script type="text/javascript" src="sidebar.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
	var address = '';
	function getLocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(found, notfound);
		} else {
			alert("Geolocation is not supported by your web browser.");
		}
	}

		//found and notfound serving as wrapper functions
	function found(position) {listings(position, true);}
	function notfound(position) {listings(position, false);}

	//everythings confined in here because the map var would not cooperate...
	function listings(position, found) {

 		if(found) {
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
		} else {
			var lat = 42.3599611;	//defaults to faneuil hall
			var lng = -71.0567528;	//if location isn't found
		}

		var latlng = new google.maps.LatLng(lat, lng);
 		var geocoder = new google.maps.Geocoder();

		geocoder.geocode({'latLng': latlng}, function(results, status) {
	 		if (status == google.maps.GeocoderStatus.OK) {
	   			if (results[1]) {
		   			address = results[1].formatted_address;
	   		 	 } else {
	 			 	alert("No results found");
	  			 }
	  		} else {
	 			alert("Geocoder failed due to: " + status);
  			}
		});

   	}

	function fillBody() {
		if (getQuery("id") == undefined) {
			var upcomingURL = "http://api.rottentomatoes.com/api/public/v1.0/lists/movies/upcoming.json";

			$.ajax({
				url: upcomingURL + '?apikey=3vrpjzy849pk6v4qxgf3emzg',
				dataType: "jsonp",
				success: upcomingCallback
			});
		}
	}

	function upcomingCallback(data) {
		var movies = data.movies;
		var upcomingHTML = '<h1>Upcoming Movies</h1><table>';
		$.each(movies, function(index, movie) {
			var imgURL = '<img width="61" height="91" src="' + movie.posters.thumbnail + '" />';
			var movieLink = '<a href="movie.php?id=' + movie.id + '">' + movie.title + ' (' + movie.year + ')</a>';
			var rating = movie.ratings.critics_score + '%';
			if (rating == "-1%") rating = "--";
			if (index%2 == 0) {
				upcomingHTML += '<tr><td>' + imgURL + '</td>';
				upcomingHTML += '<td>' + movieLink + '<br>' + rating + '</td>';
			} else {
				upcomingHTML += '<td>' + imgURL + '</td>';
				upcomingHTML += '<td>' + movieLink + '<br>' + rating + '</td></tr>';
			}
		});
		upcomingHTML += '</table>';		$("div#contentbody").html(upcomingHTML);
	}

</script>

</head>

<body onload="getLocation()">
<div id="header-wrap">
	<!--<div id="header-container">
		<div id="header">
			<h1>Fixed Header &amp; Footer<em>even IE6 behaves</em></h1>-->
			<ul id="navbar">
				<li><span class="start"><a href="index.php" title=""><img src="images/logo.png" alt="logo"/></a></span></li>
				<li><a href="movie.php" title="" class="current"><p>Movies</p></a></li>
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
			global $descriptionbody;
			if (isset($_GET["id"])) {

				global $headerbody;
				global $status;
				$status = 200;
				$myUserName = "arhea01";
				$myPassword = "arhea01";
				$myDatabase = "arhea01";
				$myHost = "mysql-user";
				$dbh = mysql_connect($myHost, $myUserName, $myPassword) or die ('I cannot connect to the database because: ' . mysql_error());
				mysql_select_db($myDatabase) or die("Unable to select database");
				$myQuery = sprintf("SELECT * FROM reelzmovies WHERE movie_id='%s'",
           			 mysql_real_escape_string($_GET['id']));
				$myResult = mysql_query($myQuery);
				$movie = mysql_fetch_array($myResult, MYSQL_BOTH);
				if (mysql_num_rows($myResult) == 0){
					$url = "http://api.rottentomatoes.com/api/public/v1.0/movies/" . $_GET["id"] . ".json?apikey=3vrpjzy849pk6v4qxgf3emzg";
					$content = file_get_contents($url);

					if ($status == 200) {
						$movieinfo = json_decode($content);
						if ($movieinfo == NULL) die('Error retrieving movie information');

						$movieposter = $movieinfo->posters->detailed;
						$bigposter = $movieinfo->posters->original;
						$movietitle = $movieinfo->title;
						$movieyear = $movieinfo->year;
						$reviewslink = $movieinfo->links->reviews;
						$rtscore = $movieinfo->ratings->critics_score . "%";
						if ($rtsynopsis == "Synopsis: ") $rtsynopsis = "";
						if ($rtscore == "-1%") $rtscore = "No rating available";

						$headerbody .= "<table id=movieheader><tr><td><a target=\"_blank\" href=\"$bigposter\"><img src=\"$movieposter\" /></a></td>";
						$headerbody .= "<td><h1>$movietitle ($movieyear)</h1>";
						$headerbody .= "<a target=\"_blank\" href=\"" . $movieinfo->links->alternate . "\"><h2>RottenTomatoes: $rtscore</h2></a>";
						$url = "http://www.imdbapi.com/?t=" . urlencode($movietitle) . "&y=" . $movieyear . "&plot=full";
						$content = file_get_contents($url);

						if ($status == 200) {
							$movieimdb = json_decode($content);
							if (movieimdb != NULL) {
								$imdbscore = $movieimdb->Rating . " / 10";
								$imdbplot = $movieimdb->Plot;
								if ($imdbplot == "") $imdbplot = $movieinfo->synopsis;
								if ($imdbscore == "N/A / 10") $imdbscore = "No rating available";
								$headerbody .= "<a target=\"_blank\" href=\"http://www.imdb.com/title/" . $movieimdb->ID . "\"><h2>IMDb: $imdbscore</h2></a>";
							}
						}

						$headerbody .= "</td></tr></table>";

						$headerbody .= "<div id=\"subnav\"><ul id=\"movienav\"><li><a id=\"main\"><b>Movie description</b></a></li>";
						$headerbody .= "<li><a id=\"showtimes\"><b>Showtimes</b></a></li>";
						$headerbody .= "<li><a id=\"reviews\"><b>Reviews</b></a></li>";
						$headerbody .= "<li><a target='_blank' href='http://www.apple.com/search/?q=".urlencode($movietitle)."&section=ipoditunes&geo=us'><b>iTunes</b></a></li>";
						$headerbody .= "<li><a target='_blank' href='http://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Ddvd&field-keywords=".urlencode($movietitle)."&x=0&y=0'><b>Amazon</b></a></li>";
						$headerbody .= "<li><a target='_blank' href='http://www.netflix.com/Search?v1=".urlencode($movietitle)."'><b>Netflix</b></a></li></ul></div>";


						echo $headerbody;

						$descriptionbody .= "<h2>Genre</h2>";
						foreach ($movieinfo->genres as $genre) {
							$descriptionbody .= "$genre, ";
						}
						$descriptionbody = substr($descriptionbody, 0, -2);
						$descriptionbody .= "<br><h2>Plot</h2> $imdbplot<br><h2>Cast</h2>";
						$descriptionbody .= "<table id=\"actortable\">";
						foreach ($movieinfo->abridged_cast as $actor) {
							$name = $actor->name;
							$character = $actor->characters[0];
							$descriptionbody .= "<tr><td><b>$name</b></td><td>$character</td></tr>";
						}
						$descriptionbody .= "</table>";



						$insertquery = "INSERT INTO reelzmovies (movie_id, title, year, html_header, html_desc, reviews) VALUES(".$_GET['id'].", '".$movietitle."', ".$movieyear.", '".$headerbody."', '".$descriptionbody."', '".$reviewslink."')";

						mysql_query($insertquery);

					}
				}

				else if (mysql_num_rows($myResult) == 1){
					echo $movie[html_header];
					$descriptionbody = $movie[html_desc];
					$reviewslink = $movie[reviews];
					$movietitle = $movie[title];
					$viewnum = $movie[views] + 1;
					$viewquery = "UPDATE reelzmovies SET views='".$viewnum."' WHERE movie_id='".$_GET['id']."'";
					mysql_query($viewquery);
				}



				include('simple_html_dom.php');

				$url = "http://www.google.com/movies?date=1&q=" . urlencode($movietitle) . "&near=" . urlencode($_GET["loc"]);
				$content = file_get_contents($url);

				if ($status == 200) {
					$html = new simple_html_dom();
					$html->load($content);
					global $showtimebody;
					$showtimebody = "<b>" . $html->find('h1[id="title_bar"]', 0)->innertext . "</b>";
					$showtimebody .= "<form name=\"changeloc\" action=\"movie.php\" method=\"get\">";
					$showtimebody .= "<input type=\"hidden\" name=\"id\" value=\"" . $_GET["id"] . "\" />";
					$showtimebody .= "<input type=\"text\" name=\"loc\" placeholder=\"Change location\" />";
					$showtimebody .= "<input type=\"submit\" value=\"Submit\" /></form>";

					foreach($html->find('div[class="theater"]') as $div) {
						    $theatername = $div->find('div[class="name"] a', 0)->innertext;
						    $theateraddress = $div->find('div[class="address"]', 0)->innertext;
						    $theatertimes = $div->find('div[class="times"]', 0)->innertext;
						    $theatertimes = str_replace('/url?q=', "", $theatertimes);
						    $theatertimes = str_replace('<a href=', "<a target=\"_blank\" href=", $theatertimes);
						    $theatertimes = urldecode($theatertimes);
						    $showtimebody .= "<p><b>$theatername</b><br>$theateraddress<br>$theatertimes</p>";
					}

					$html->clear();
					unset($html);
				}
			mysql_close($dbh);
			}
		?>

		<div id="contentbody"></div>
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
<script type="text/javascript">
	$(document).ready(function() {
		$("div#contentbody").html("<?php echo str_replace('"', "'", $descriptionbody) ?>");
		fillSidebar();
		fillBody();
	});

	$("a#main").click(function() {
		$("div#contentbody").html("<?php echo str_replace('"', "'", $descriptionbody) ?>");
	});

	$("a#showtimes").click(function() {
		$("div#contentbody").html("<?php echo str_replace('"', "'", $showtimebody) ?>");
		if (address != '') $('div#contentbody').prepend('<a href="movie.php?id=' + getQuery("id") + '&loc=' + encodeURI(address) + '">Locate Me</a><br>');
	});

	$("a#reviews").click(function() {
		$("div#contentbody").html('<p>Loading reviews...</p>');
		$.ajax({
			url: '<?php echo $reviewslink ?>' + '?apikey=3vrpjzy849pk6v4qxgf3emzg',
			dataType: "jsonp",
			success: reviewsCallback
		});
	});

	function reviewsCallback(data) {
		$("div#contentbody").html('');
		var reviewHTML = '<table id="reviewtable">';
		//$("div#content").html(JSON.stringify(data));
		var reviews = data.reviews;
		$.each(reviews, function(index, review) {
			var score;
			if (review.original_score == undefined) score = "";
			else score = '<br><b>Rating:</b> ' + review.original_score;

			var quote;
			if (review.quote == "") quote = "";
			else quote = '<br>' + review.quote;

			var author;
			if (review.critic == "") author = "";
			else author = '<b>Author:</b> ' + review.critic;

			var link;
			if (review.links.review == undefined) link = review.publication;
			else link = '<a target="_blank" href="' + review.links.review + '">' + review.publication + '</a>';

			if (index%2 == 0) {
				reviewHTML += '<tr><td width="50%">' + author + '<br><b>Publication:</b> ' + link + score + quote + '<br><br></td>';
			} else {
				reviewHTML += '<td width="50%">' + author + '<br><b>Publication:</b> ' + link + score + quote + '<br><br></td></tr>';
			}
		});
		reviewHTML += '</table>';
		$("div#contentbody").html(reviewHTML);
	}
</script>
</body>
</html>
