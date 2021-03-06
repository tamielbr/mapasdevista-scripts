<?php
#This file is part of a process to input serialized latitude and longitude into Wordpress using a "Mapas de Vista" theme.
# It is used with GET_CSV.php, as a final step to insert data directly into the database and avoid double serializing by Wordpress.

#Connects to your Database 
$conn = mysql_connect('mysql.xxxx.xxxx', 'database', 'passwd') or die(mysql_error());
mysql_select_db('database', $conn) or die(mysql_error($conn));
mysql_set_charset('utf8', $conn);


#http://php.net/manual/en/function.fgetcsv.php

#Open the file and creates a multimensional array
if (($handle = fopen("file.csv", "r")) !== FALSE) {
    # Set the parent multidimensional array key to 0.
    $nn = 0;
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        # Count the total keys in the row.
        $c = count($data);
        # Populate the multidimensional array.
        for ($x=0;$x<$c;$x++)
        {
           $csvarray[$nn][$x] = $data[$x];
        }
        $nn++;
    }
    # Close the File.
    fclose($handle);
}

#Array that will receive the serialized version of latitude and longitude
$array_latlon=array();

#Gets CSV file (file.csv) from the server and parses it into an array.
#The file should have four columns: post_id, post_name (ignored), lat, lon, map number (from the wp_postmeta setting)
foreach( $csvarray as $row ) {
   $id = $row['0'];
   $post_name = $row['1'];
   $array_latlon[lat]=floatval($row['2']);
   $array_latlon[lon]=floatval($row['3']);
   $mapa = $row['4'];
   $latlon=serialize($array_latlon);
   

   #Inputs the serialized version of latitude and longitude into wp_postmeta. Does so directly to avoid a "second" serialization by WP.
   mysql_query("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES ('$id','_mpv_location','$latlon')");
   
   #Inputs the map number into wp_postmeta
   mysql_query("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES ('$id','_mpv_inmap ','$mapa')");       
          
    }

?>
</pre>
