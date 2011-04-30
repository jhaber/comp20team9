<?php 
	session_start();
?>
<!DOCTYPE html>

<head>
<title>Search</title>
<link rel="stylesheet" href="main.css" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Amaranth' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="scroll.css">

<script src="http://cdn.jquerytools.org/1.2.5/jquery.tools.min.js"></script>
<script type="text/javascript" src="commandline.js"></script>
<script type="text/javascript" src="sidebar.js"></script>
<script type="text/javascript">
	var query = getQuery("q");
	if (query) {
		var moviesSearchUrl = "http://api.rottentomatoes.com/api/public/v1.0/movies.json";

		$(document).ready(function() {
			$.ajax({
				url: moviesSearchUrl + '?apikey=3vrpjzy849pk6v4qxgf3emzg&q=' + encodeURI(query),
				dataType: "jsonp",
				success: searchCallback
			});
			fillSidebar();
		});
	}

	function searchCallback(data) {
		var movies = data.movies;
		$('#resultsheader').html('<h2>Found ' + data.total + ' results</h2>');
		var searchHTML = '';
		$.each(movies, function(index, movie) {
			searchHTML += '<tr><td><img width="61" height="91" src="' + movie.posters.thumbnail + '" /></td>';
			searchHTML += '<td><a href="movie.php?id=' + movie.id + '">' + movie.title + ' (' + movie.year + ')</a></td></tr>';
		});
		$('#searchtable').html(searchHTML);
	}
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
			<div id="resultsheader"></div>
			<table id="searchtable"></table>
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
