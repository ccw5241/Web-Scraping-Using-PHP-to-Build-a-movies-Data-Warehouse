<?php
/*
DO THE FOLLOWINGS BEFORE RUNNING THIS FILE
1. Save this file in c:/xampp/htdocs along with simple_html_dom.php file.
2. To run this file, open a web browser and type http://localhost:80/FactMovie.php in the address bar if you are using the default port 80 for Apache XAMPP.
Reference:
https://github.com/PeratX/SimpleHtmlDom/wiki/PHP-Simple-HTML-DOM-Parser-Manual
https://simplehtmldom.sourceforge.io/manual_api.htm
*/
define('MAX_FILE_SIZE', 6000000);
require 'simple_html_dom.php';
set_time_limit ( 0 ); //No run time lumit

$conn = sqlsrv_connect( "WIN10SQL-3", array( "Database"=>"Movie"));
if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

$html = file_get_html('https://www.boxofficemojo.com/daily/2019/?view=year'); //Get the webpage in html form

	foreach($html-> find('tr[!style]') as $Element){
    	
		if($Element->find('a[class=a-link-normal]',0)) {
			$href = $Element->find('a[class=a-link-normal]',0)->href;
			$datestr = substr($href, 6, 10); //The 6th to 16th characters contain the date in the link.
			$date = date( 'Y-m-d', strtotime($datestr));
			$day = $Element->find('a[class=a-link-normal]',2)->plaintext;
			$moneyText = $Element->find('td',3)->plaintext;
			$top10gross = floatval(str_replace(array(',','$'), '', $moneyText)); //Remove $ and comma from the revenue as it interferes while loading. Also, the revenue range might be more than that of the integer datatype hence save it with float datatype.
			$title = $Element->find('a[class=a-link-normal]',3)->plaintext;
			$moneyText = $Element->find('td',8)->plaintext;
			$top1gross = floatval(str_replace(array(',','$'), '', $moneyText));
		
		/* ADD YOUR CODE HERE TO APPEND THE SCRAPED COLUMNS IN A Fact.txt FILE
		   USE APPROPRIATE COLUMN SEPARATOR AND LINE SEPARATOR */
		$file_row = "$date|$day|$top10gross|$title|$top1gross\n";
	
	    file_put_contents('fact.txt', $file_row, FILE_APPEND); //Adds one row for each movie in the movie.txt file.
		echo $title . "<br>";
		}
	}
	
?>