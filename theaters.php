<!DOCTYPE html>


<?php
			require_once('simple_html_dom.php');


            if($_GET) {
			$url = "http://www.google.com/movies?near=" . urlencode($_GET["loc"]);
            }
            else $url = "http://www.google.com/movies?";
			$content = file_get_contents($url);

				$html = new simple_html_dom();
				$html->load($content);

			//	$location = $html->find('h1[id="title_bar"]', 0)->innertext;


                $locationlist = array();

				foreach($html->find('div[class="theater"]') as $theater) {
				    $theatername = $theater->find('h2[class="name"] a', 0)->innertext;
				    $theateraddress = $theater->find('div[class="info"]', 0)->innertext;
                    $theaterinfo = array();
                    $theaterinfo['name'] = $theatername;
                    $temp = explode(" -", $theateraddress);
                    $theaterinfo['adr'] = $temp[0];
                    array_push($locationlist, $theaterinfo);
				    }


				$html->clear();
				unset($html);

    global $foodterm;
	global $placeterm;

	//if logged in, get user ID, call it $id
	//for now, i'll just set id to myself
	$id = 3;

	$myUserName = "arhea01";
	$myPassword = "arhea01";
	$myDatabase = "arhea01";
	$myHost = "mysql-user";
	$dbh = mysql_connect($myHost, $myUserName, $myPassword) or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db($myDatabase) or die("Unable to select database");

	$myQuery = sprintf("SELECT * FROM reelzusers WHERE user_id='%s'",
           			 mysql_real_escape_string($id));
	$myResult = mysql_query($myQuery);
	$user = mysql_fetch_array($myResult, MYSQL_BOTH);

	if ($user[fav_food]!=NULL){
		$foodterm = $user[fav_food];
	}
	else{
		$foodterm = "food";
	}

	if ($user[def_location]!=NULL){
		$placeterm = $user[def_location];
	}
    else if ($_GET['loc']) $placeterm = $_GET['loc'];
    else ($placeterm="Davis Square, Somerville MA");
	//geolocate option?

	$url = "http://api.yelp.com/business_review_search?term=".urlencode($foodterm)."&location=".urlencode($placeterm)."&ywsid=5aI7kCD2Q4IHiHjGca1dPg";
	$content = file_get_contents($url);

    $yelparray = array();

		$yelpinfo = json_decode($content);
		if ($yelpinfo == NULL) die('Error retrieving movie information');
		foreach ($yelpinfo->businesses as $eatery){
           //print_r($eatery);
            $business=array();

            $business['name'] = $eatery->name;
            $business['adr'] = $eatery->address1 . ' ' . $eatery->city . ' ' . $eatery->state;
            array_push($yelparray,$business);
		/*	echo "<a href=".$eatery->url.">".$eatery->name."</a><br/>";
			echo $eatery->address1.", ".$eatery->city.", ".$eatery->state."<br/>";
			echo "Phone Number: ".$eatery->phone."<br/>";
			echo "Average Yelp! Rating: ".$eatery->avg_rating;
			echo "<br/><br/>";*/
		//	$lat = $eatery->latitude;
		//	$lon = $eatery->longitude;
	}



?>



<head>
<title>Theaters</title>
<link rel="stylesheet" href="main.css" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Amaranth' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="scroll.css">


<script src="http://cdn.jquerytools.org/1.2.5/jquery.tools.min.js"></script>

<script type="text/javascript" src="sidebar.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">

    var localtheaters = <?php echo json_encode($locationlist); ?>;
    var localeats = <?php echo json_encode($yelparray); ?>;
    var geocoder;
    var map;


    function getLocation() {
				fillSidebar();
        geocoder = new google.maps.Geocoder();

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



		//$('div#content').prepend('<a href="theaters.php?loc=' + encodeURI(address) + '">Find Me!</a><br>');


		var initLoc = new google.maps.LatLng(lat, lng);
		var infowindow = new google.maps.InfoWindow();
		var myOptions = {
				zoom: 13, // The larger the zoom number, the bigger the zoom
				center: initLoc,
				mapTypeId: google.maps.MapTypeId.HYBRID
		};

			// Create the map in the "map_canvas" <div>
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

			// Create a home marker
		var image = 'person.png';
		var marker = new google.maps.Marker({
			position: initLoc,
			title: "You Are Here!"
			//icon: image
		});
			marker.setMap(map);
			attachToMark(marker, infowindow, map);
			infowindow.setContent(marker.title);    //set to open at start
			infowindow.open(map,marker);

	    	postResults(localtheaters,infowindow);
	    	postResults(localeats,infowindow);
		}

        function attachToMark(marker, infowindow, map) {
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.setContent(marker.title);
				infowindow.open(map, marker);
			});
		}



        function postResults (locations,infowindow) {

			getLoc(infowindow,locations,0);
		}

		function getLoc(infowindow,search,index) {

			var address = search[index]['adr'];
			geocoder.geocode( { 'address': address}, function(results, status) {
     			if (status == google.maps.GeocoderStatus.OK) {

     				search[index]['lat'] = results[0].geometry.location.Da;
     				search[index]['lng'] = results[0].geometry.location.Ea;

                    console.log(search[index]['name']);
          			postResult(search, index, infowindow);

          			if (index < 3) getLoc(infowindow,search,++index);

      			} else {
        		alert("Geocode was not successful for the following reason: " + status);
      			}
    		});
		}

		function postResult (search, key, infowindow) {

			var content = '<h2>' + search[key]['name'] + '</h2>';
					//	  '<a href="good.php?id= ' + search[key]['good_id'] + '"/>Link</a>';
			var landmark = new google.maps.LatLng(search[key]['lat'],search[key]['lng']);

            var marker = new google.maps.Marker({
           	          title: content,
           			  position: landmark,
      				});
       		marker.setMap(map);

       		attachToMark(marker, infowindow, map);
		}


















   //	}
/*
    function thiy () {
        var h = <?php print_r($locationlist); ?>;
    }*/

</script>

</head>

<body onload="getLocation()">



<div id="header-wrap">
	<!--<div id="header-container">
		<div id="header">
			<h1>Fixed Header &amp; Footer<em>even IE6 behaves</em></h1>-->
			<ul id="navbar">
				<li><span class="start"><a href="index.php" title=""><img src="images/logo.png" alt="logo"/></a></span></li>
				<li><a href="movie.php" title=""><p>Movies</p></a></li>
				<li><a href="theaters.php" title="" class="current"><p>Theaters</p></a></li>
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


       <div id="map_canvas"></div>

	<div id="container">

		<div id="content">


		<?php

			$url = "http://www.google.com/movies?near=" . urlencode($_GET["loc"]);
			$content = file_get_contents($url);
				$html = new simple_html_dom();
				$html->load($content);

				$location = $html->find('h1[id="title_bar"]', 0)->innertext;
				echo ("<b>$location</b>");
				echo ("<form name=\"changeloc\" action=\"theaters.php\" method=\"get\">");
				echo ("<input type=\"text\" name=\"loc\" placeholder=\"Change location\" />");
				echo ("<input type=\"submit\" value=\"Submit\" /></form>");


                $locationlist = array();

				foreach($html->find('div[class="theater"]') as $theater) {
				    $theatername = $theater->find('h2[class="name"] a', 0)->innertext;
				    $theateraddress = $theater->find('div[class="info"]', 0)->innertext;
                    $theaterinfo = array();
                    $theaterinfo['name'] = $theatername;
                    $temp = explode(" -", $theateraddress);
                    $theaterinfo['adr'] = $temp[0];
                    array_push($locationlist, $theaterinfo);
				    $theaterid = $theater->find('div[class="desc"]')->id;
				    echo ("<p><h3>$theatername<br>$theateraddress</h3>");
				    foreach($theater->find('div[class="movie"]') as $movie) {
					$moviename = $movie->find('div[class="name"] a', 0)->innertext;
					$movietimes = $movie->find('div[class="times"]', 0)->innertext;
				    	//$theatertimes = $theater->find('div[class="times"]', 0)->innertext;
				    	$movietimes = str_replace('/url?q=', "", $movietimes);
				    	$movietimes = str_replace('<a href=', "<a target=\"_blank\" href=", $movietimes);
				    	$movietimes = urldecode($movietimes);
				    	echo ("<b>$moviename</b><br>$movietimes<br>");
				    }
				    echo ("</p>");
				}

				$html->clear();
				unset($html);

        ?>


<script type="text/javascript"> //thiy(); </script>
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
