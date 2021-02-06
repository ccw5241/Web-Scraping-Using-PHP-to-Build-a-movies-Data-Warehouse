<?php
/*
DO THE FOLLOWINGS BEFORE RUNNING THIS FILE
1. Save this file in c:/xampp/htdocs along with simple_html_dom.php file.
2. Create the Movie database with tables in SQL server. The SQL queries are provided in Blackboard.
3. Change the server_name to your server name (e.g. WIN10SQL-10) and change the database name from AdventureWorksDW2017 to Movie.
4. To run this file, open a web browser and type http://localhost:80/movie.php in the address bar if you are using the default port 80 for Apache XAMPP.
Reference:
https://github.com/PeratX/SimpleHtmlDom/wiki/PHP-Simple-HTML-DOM-Parser-Manual
https://simplehtmldom.sourceforge.io/manual_api.htm
*/

set_time_limit ( 0 ); //No run time lumit
require 'simple_html_dom.php';

$conn = sqlsrv_connect( "WIN10SQL-15", array( "Database"=>"Movie"));
if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

$html = file_get_html('https://www.boxofficemojo.com/year/2010/'); //Get the webpage in html form

foreach($html-> find('td a[!target]') as $element){ //search for tag 'td' with child tag 'a' that does not contain 'target' attribute. This element has the movie name and movie link reference.
	$href = $element->href; //save the movie link reference.
	$html_movie = file_get_html('https://www.boxofficemojo.com'.$href); //Get the webpage of a movie
	$Name = $html_movie -> getElementsByTagName('h1')->plaintext; //header 'h1' contains the movie name in text.
	$MovieName = str_replace("'",'', $Name); //Replaces Valentine's Day to Valentines Day

	//The first three 'div' tags with attribute 'class=a-section a-spacing-none' contain the revenues.
	for ($i = 0; $i <= 2; $i++) {
    $Element = $html_movie->getElementsByTagName('div[class=a-section a-spacing-none]',$i);
	$Value = $Element->firstChild()->plaintext;
	$Text = $Element->lastChild()->plaintext;
	$Revenue = floatval(str_replace(array(',','$'), '', $Text)); //Remove $ and comma from the revenue as it interferes while loading. Also, the revenue range is more than that of the integer datatype hence save it with float datatype.
	
		switch ($Value) {
			case strpos($Value, 'Domestic')!==false: $DomesticRevenue = $Revenue; 
			break;
			case strpos($Value, 'International')!==false:  $InternationalRevenue = $Revenue; 
			break;
			case strcmp($Value, 'Worldwide')!==false: $WorldwideRevenue = $Revenue; 
			break;
		}
	}
	//The other 'div' tags with attribute 'class=a-section a-spacing-none' contain other information such as Distributor name, Opening revenue etc.
	for ($i = 3; $i <= 11; $i++) {
    $Element = $html_movie->getElementsByTagName('div[class=a-section a-spacing-none]',$i);
	if(isset($Element)) { //Checks if the element exists. Not all movie webpages contain all the informations. For example, Toy Story 3 does not have MPAA rating.
		$Value = $Element->firstChild()->plaintext;

		switch ($Value) {
			case 'Distributor':
			$Text = $Element->children(1)->plaintext;
			$Distributor = trim(str_replace('See full company information','', $Text));
			break;
			case 'Opening':
			$Text = $Element->lastChild()->children(0)->plaintext;
			$OpeningRevenue = floatval(str_replace(array(',','$'), '', $Text));
			break;
			case 'Budget':
			$Text = $Element->lastChild()->plaintext;
			$Budget = floatval(str_replace(array(',','$'), '', $Text));
			break;
			case 'Release Date':
			$hrefFrom = $Element->lastChild()->children(0)->href;
			$date = substr($hrefFrom, 6, 10); //The 6th to 16th characters contain the date in the link.
			$ReleaseFromDate = date( 'Y-m-d', strtotime($date));
			$hrefTo = $Element->lastChild()->children(1)->href;
			$date = substr($hrefTo, 6, 10);
			$ReleaseToDate = date( 'Y-m-d', strtotime($date));
			break;
			case 'MPAA':
			$MPAA = $Element->lastChild()->plaintext;
			break;
			case 'Genres':
			$Text = $Element->lastChild()->plaintext;
			$Genres = preg_replace('/\s+/', ' ', $Text); //removes extra whitespaces between the genres.
			break;
			case 'Widest Release':
			$Text = $Element->lastChild()->plaintext;
			$WidestRelease = intval(str_replace(array(' theaters',','), '', $Text));
			break;
		}
	}
	}
	$file_row = "$MovieName|$Distributor|$ReleaseFromDate|$ReleaseToDate|$MPAA|$Genres|$WidestRelease|$Budget|$OpeningRevenue|$DomesticRevenue|$InternationalRevenue|$WorldwideRevenue\n";
	
	file_put_contents('movie.txt', $file_row, FILE_APPEND); //Adds one row for each movie in the movie.txt file.

$sql_row = "INSERT INTO MovieFact
(MovieName, Distributor, ReleaseFromDate, ReleaseToDate, MPAA, Genres, WidestRelease, Budget, OpeningRevenue, DomesticRevenue, InternationalRevenue, WorldwideRevenue)
 VALUES('$MovieName', '$Distributor', '$ReleaseFromDate', '$ReleaseToDate', '$MPAA', '$Genres', $WidestRelease, $Budget, $OpeningRevenue, $DomesticRevenue, $InternationalRevenue, $WorldwideRevenue);";	

//Adds one row for each movie in the MovieFact table.
sqlsrv_query($conn,$sql_row) 
or die(print_r( sqlsrv_errors(), true));

echo $MovieName ." Inserted!<br>";
}
?>