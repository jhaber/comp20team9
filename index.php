<?php 
	session_start();
?>
<!DOCTYPE html>

<head>
<title>Welcome to reelz</title>
<link rel="stylesheet" type="text/css" href="mobile.css"  />
<style type="text/css" media="screen and (min-width: 481px)">
<!--
@import url("main.css");
-->
</style>
<!--[if IE]--><link rel="stylesheet" type="text/css" href="main.css"  media="screen" /><!--[endif]-->
<link href="mobile.css" rel="stylesheet" type="text/css" media="handheld, only screen and (max-device-width: 480px)" />
<link href='http://fonts.googleapis.com/css?family=Amaranth' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="scroll.css">

<script src="http://cdn.jquerytools.org/1.2.5/jquery.tools.min.js"></script>
<script type="text/javascript" src="commandline.js"></script>
<script type="text/javascript" src="sidebarandscroll.js"></script>
<script type="text/javascript">
		var upcomingURL = "http://api.rottentomatoes.com/api/public/v1.0/lists/movies/upcoming.json";

		$(document).ready(function() {
			$.ajax({
				url: upcomingURL + '?apikey=3vrpjzy849pk6v4qxgf3emzg',
				dataType: "jsonp",
				success: upcomingCallback
			});
			fillSidebar();
		});

	function upcomingCallback(data) {
		var movies = data.movies;
		$('#upcomingheader').html('<h1>Upcoming Movies</h1>');
		var upcomingHTML = '';
		$.each(movies, function(index, movie) {
			var imgURL = '<img width="61" height="91" src="' + movie.posters.thumbnail + '" />';
			var movieLink = '<a href="movie.php?id=' + movie.id + '">' + movie.title + ' (' + movie.year + ')</a>';
			var rating = movie.ratings.critics_score + '%';
			if (rating == "-1%") rating = "--";

			if (index < 10) {
				if (index%2 == 0) {
					upcomingHTML += '<tr><td>' + imgURL + '</td>';
					upcomingHTML += '<td>' + movieLink + '<br>' + rating + '</td>';
				} else {
					upcomingHTML += '<td>' + imgURL + '</td>';
					upcomingHTML += '<td>' + movieLink + '<br>' + rating + '</td></tr>';
				}
			}
		});
		$('#upcomingtable').html(upcomingHTML);
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
	<h1>Top Movies Scrollbar</h1>

	<a class="prev browse left"></a>

	<!-- root element for scrollable -->
	<div class="scrollable">

   	<!-- root element for the items -->
   	<div class="items">

      	<!-- 1-5 -->
      	<div id="scroll1">

        </div>

      <!-- 5-10 -->
      <div id="scroll2">

      </div>

   </div>

</div>

<!-- "next page" action -->
<a class="next browse right"></a>



<!-- javascript coding -->


<script>
// execute your scripts when the DOM is ready. this is mostly a good habit
$(function() {

	// initialize scrollable
	//$(".scrollable").scrollable();

	$(".scrollable").scrollable({circular: true}).autoscroll(1500);

});
</script>

<br><br><br>

<h1>Movie Popularity Chart (by week)</h1>


      <div id="theme_container">

	<!-- This version plays nicer with older browsers,
	     but requires JavaScript to be enabled.
	     http://java.sun.com/javase/6/docs/technotes/guides/jweb/deployment_advice.html -->
	<script type="text/javascript"
		src="http://www.java.com/js/deployJava.js"></script>
	<script type="text/javascript">
	  /* <![CDATA[ */

	  var attributes = {
            code: 'theme.class',
            archive: 'theme.jar',
            width: 900,
            height: 400,
            image: 'loading.gif'
          };
          var parameters = { };
          var version = '1.5';
          deployJava.runApplet(attributes, parameters, version);

          /* ]]> */
        </script>

	<noscript> <div>
	  <!--[if !IE]> -->
	  <object classid="java:theme.class"
            	  type="application/x-java-applet"
            	  archive="theme.jar"
            	  width="900" height="400"
            	  standby="Loading Processing software..." >

	    <param name="archive" value="theme.jar" />

	    <param name="mayscript" value="true" />
	    <param name="scriptable" value="true" />

	    <param name="image" value="loading.gif" />
	    <param name="boxmessage" value="Loading Processing software..." />
	    <param name="boxbgcolor" value="#FFFFFF" />

	    <param name="test_string" value="outer" />
	  <!--<![endif]-->

	    <!-- For more instructions on deployment,
		 or to update the CAB file listed here, see:
		 http://java.sun.com/javase/6/webnotes/family-clsid.html
		 http://java.sun.com/javase/6/webnotes/install/jre/autodownload.html -->
	    <object classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"
		    codebase="http://java.sun.com/update/1.6.0/jinstall-6u20-windows-i586.cab"
		    width="900" height="400"
		    standby="Loading Processing software..."  >

	      <param name="code" value="theme" />
	      <param name="archive" value="theme.jar" />

	      <param name="mayscript" value="true" />
	      <param name="scriptable" value="true" />

	      <param name="image" value="loading.gif" />
	      <param name="boxmessage" value="Loading Processing software..." />
	      <param name="boxbgcolor" value="#FFFFFF" />

	      <param name="test_string" value="inner" />

	      <p>
		<strong>
		  This browser does not have a Java Plug-in.
		  <br />
		  <a href="http://www.java.com/getjava" title="Download Java Plug-in">
		    Get the latest Java Plug-in here.
		  </a>
		</strong>
	      </p>

	    </object>

	  <!--[if !IE]> -->
	  </object>
	  <!--<![endif]-->

	</div> </noscript>
		<div id="content">
			<div id="upcomingheader"></div>
			<table id="upcomingtable"></table>
		</div>
		<div id="sidebar">
		</div>
	</div></div>
<div id="footer-wrap">
	<div id="footer-container">
		<div id="footer">
		</div>
	</div>
</div>
</body>
</html>
