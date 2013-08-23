<?php 
header('Content-type: text/xml');

$baselink = "http://www.flyingsquirrel.ca";

function makelink( $item ) {
    global $baselink;

    if ( $item->type == 0 ) {
	//journal entry

	$link = $baselink . "/archive.php?article=" . $item->id;

    } else {
	//blog entry

	$link = $baselink . "/stuff.php?id=". $item->id;

    }

    return $link;
}

function maketitle( $item ) {
    
    if ( $item->type == 0 ) {
	//journal entry

	$title = strftime( "%b %e: ", $item->time );
	$title .= stripslashes( $item->headline );

    } else {
	//blog entry
	
	if ( $item->headline ) {
	    $title = "stuff: " . stripslashes( $item->headline );
	} else {
	    $title = "stuff";
	}

    }
    return $title;
}

function makedescription( $item ) {

    global $baselink;
    
    if ( $item->type == 0 ) {
	//journal entry

	$desc = stripslashes( $item->description );

    } else {
	//blog entry
	
	$desc = "";
	if ( $item->link ) {
	    $desc = "<p><a href=\"{$item->link}\">{$item->link}</a></p>\n\n";
	}

	if ( $item->photo ) {
	    $desc .= "<img src=\"$baselink/photos-t/{$item->photo}\" 
		align=\"right\" border=\"0\" alt=\"[photo]\" />\n";
	}

	if ( $item->description ) {
	    $desc .= stripslashes( "<p><i>{$item->description}</i></p>" );
	    $desc .= "\n\n";
	}

	if ( $item->more ) {
	    $desc .= stripslashes( $item->more );
	}
    }
    return htmlspecialchars( $desc );
}
	

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";

?>
<rdf:RDF
         xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/"
         xmlns:mn="http://usefulinc.com/rss/manifest/"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
>
<channel rdf:about="<?= $baselink . $_SERVER['PHP_SELF'] ?>">
<title>flying squirrel</title>
<link><?= $baselink ?></link>
<description>Comments posted to the flying squirrel's journal</description>
<items>
<rdf:Seq>
<?php
    
    mysql_connect('localhost', 'dscassel', 'bbsapollo');
    $result = mysql_db_query( "Squirrel", 
        "(SELECT id, 1 as type, headline, link, photo, description, more, 
		UNIX_TIMESTAMP(time) as time from `blog`) 
	    UNION
	    (SELECT id, 0, title, NULL, NULL, message, NULL, 
		UNIX_TIMESTAMP(submitted) FROM `mainpage`)
	    ORDER BY time DESC LIMIT 15;");

	while ( $item = mysql_fetch_object( $result ) ) {
		$link = makelink( $item );

		echo "<rdf:li rdf:resource=\"$link\"/>\n";
	}
	
?>
</rdf:Seq>
</items>
</channel>
<?php 
	mysql_data_seek( $result, 0 );

	while ( $item = mysql_fetch_object( $result ) ) {
		$timestamp = strftime( "%Y-%m-%dT%T", $item->time );
		//I'll assume I'm always in an on-the-hour timezone.
		//At least until I upgrade to PHP 5, anyway.
		$timestamp .= substr( date( "O", $item->time ), 0, 3) . ":00";
		

		$link = makelink( $item );
		$title = maketitle( $item );
		$description = makedescription( $item );

?>
<item rdf:about="<?=$link?>">
  <link><?=$link?></link>
  <title><?=$title?></title>
  <dc:creator>the flying squirrel</dc:creator>
  <dc:date><?=$timestamp?></dc:date>
  <description><?=$description?></description>
</item>
<?php

	} //end while
	
	
?>
<rdf:Description rdf:ID="manifest">
<mn:channels>
<rdf:Seq>
<rdf:li rdf:resource="<?=$baselink . $_SERVER['PHP_SELF']?>"/>
</rdf:Seq>
</mn:channels>
</rdf:Description>
</rdf:RDF>
