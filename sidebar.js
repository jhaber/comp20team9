function fillSidebar() {
	var boxofficeURL = "http://api.rottentomatoes.com/api/public/v1.0/lists/movies/box_office.json";

	$.ajax({
		url: boxofficeURL + '?apikey=3vrpjzy849pk6v4qxgf3emzg',
		dataType: "jsonp",
		success: boxofficeCallback
	});
	
	function boxofficeCallback(data) {
		var boxofficeMovies = data.movies;
		var sideBarHTML = '';
		sideBarHTML += '<a href="login.php"><b>Log in</b></a><b> | </b><a href="logout.php"><b>Log out</b></a><b> | </b>';
		sideBarHTML += '<a href="account.php"><b>Sign up</b></a><b> | </b><a href="reactivate.php"><b>Reactivate</b></a>';
		sideBarHTML += '<br><br><b>BOX OFFICE HITS</b><br><table>';
		$.each(boxofficeMovies, function(index, movie) {
			var rating = movie.ratings.critics_score + '%';
			if (rating == "-1%") rating = "--";
			sideBarHTML += '<tr><td>' + rating + '</td>';
			sideBarHTML += '<td><a href="movie.php?id=' + movie.id + '">' + movie.title + '</a></td></tr>';
		});
		$('div#sidebar').html(sideBarHTML);
	}
	
	openingURL = "http://api.rottentomatoes.com/api/public/v1.0/lists/movies/opening.json";
	
	$.ajax({
		url: openingURL + '?apikey=3vrpjzy849pk6v4qxgf3emzg',
		dataType: "jsonp",
		success:openingCallback
	});
		
	function openingCallback(data) {
		var openingMovies = data.movies;
		var sideBarHTML = '<br><b>OPENING THIS WEEK</b><br><table>';
		$.each(openingMovies, function(index, movie) {
			rating = movie.ratings.critics_score + '%';
			if (rating == "-1%") rating = "--";
			sideBarHTML += '<tr><td>' + rating + '</td>';
			sideBarHTML += '<td><a href="movie.php?id=' + movie.id + '">' + movie.title + '</a></td></tr>';
		});
		$('div#sidebar').append(sideBarHTML);
	}
}
