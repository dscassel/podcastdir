<?php

$files = scandir('.');

foreach( $files as $file ) {

    if ( substr( $file, -4 ) == ".mp3" ) {
        print $file;
    } 

}

?>
