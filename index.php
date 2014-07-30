<?php 
header('Content-type: text/xml');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

?>

<rss version="2.0">
<channel>
 <title>My Random Podcasts</title>
 <description>Randomly collected audio files from around the Internet.</description>
 <link>http://flyingsquirrel.ca/podcastdir</link>
 <lastBuildDate>Mon, 06 Sep 2010 00:01:00 +0000 </lastBuildDate>
 <pubDate>Mon, 06 Sep 2009 16:20:00 +0000 </pubDate>
 <ttl>1800</ttl>


<?php

print $SERVER;
$files = scandir('.');

foreach( $files as $file ) {

    if ( strtolower( substr( $file, -4 ) ) == ".mp3" ) {

	$id3 = id3_get_tag($file);
	print_r($id3);
        $mp3 = array();

        $mp3['url'] = "http://flyingsquirrel.ca/podcastdir/" . $file;
	
?>

 <item>
  <title>Example entry</title>
  <description>Here is some text containing an interesting description.</description>
  <link><? echo $mp3['url']?></link>
  <guid>unique string per item</guid>
  <pubDate>Mon, 06 Sep 2009 16:20:00 +0000 </pubDate>
  <enclosure length="<?=$mp3['length']?>" type="audio/mp3"
    url="<?=$mp3['url']?>"></enclosure>
 </item>

<?php 
    } 

}

?>
</channel>
</rss>

