<?php
/*
DO THE FOLLOWINGS BEFORE RUNNING THIS FILE
1. Save this file in c:/xampp/htdocs along with simple_html_dom.php file.
2. To run this file, open a web browser and type http://localhost:80/DimMovie.php in the address bar if you are using the default port 80 for Apache XAMPP.
Reference:
https://github.com/PeratX/SimpleHtmlDom/wiki/PHP-Simple-HTML-DOM-Parser-Manual
https://simplehtmldom.sourceforge.io/manual_api.htm
*/
define('MAX_FILE_SIZE', 6000000);
require 'simple_html_dom.php';
set_time_limit ( 0 ); //No run time lumit

for($i=1;$i<=2;$i++) {
	$html = file_get_html('https://www.imdb.com/list/ls041799464/?sort=list_order,asc&st_dt=&mode=detail&page='.$i);

	foreach($html-> find('div[class=lister-item-content]') as $Element){
    	
		$title = $Element->find('h3 a',0)->plaintext;
		if ($Element->find('span[class=certificate]',0))
			$certificate = $Element->find('span[class=certificate]',0)->plaintext;
		else $certificate = '';
		$runtimeStr = $Element->find('span[class=runtime]',0)->plaintext;
		$runtime = str_replace(' min','',$runtimeStr);
		$genreStr = $Element->find('span[class=genre]',0)->plaintext;
		$genre = trim($genreStr);
		$rating = $Element->find('span[class=ipl-rating-star__rating]',0)->plaintext;
				
	/*ADD YOUR CODE HERE TO APPEND THE SCRAPED COLUMNS IN A Rating.txt file
	  USE APPROPRIATE COLUMN SEPARATOR AND LINE SEPARATOR */
	$file_row = "$title|$certificate|$runtime|$genre|$rating\n";
	
	file_put_contents('rating.txt', $file_row, FILE_APPEND); //Adds one row for each movie in the movie.txt file.
	echo $title . "<br>";
	}
}
?>