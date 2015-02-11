<?php
/*
 * PHP code to export MySQL data to CSV
 * Adapted from http://salman-w.blogspot.com/2009/07/export-mysql-data-to-csv-using-php.html 
 *
 * Sends the result of a MySQL query as a CSV file for download
 */

#Establish database connection
$conn = mysql_connect('mysql.xxxx.xxxx', 'database', 'passwd') or die(mysql_error());
mysql_select_db('database', $conn) or die(mysql_error($conn));
mysql_set_charset('utf8', $conn);

#Get all distinct IDs from wp_postmeta which currently do not have a latitude/longitude (means they need one)
$query = sprintf("
                    SELECT DISTINCT (ID), post_title
                    FROM wp_posts
                    INNER JOIN wp_postmeta
                    ON wp_postmeta.post_id=wp_posts.ID
                    WHERE meta_key!='_mpv_location'
                    AND wp_posts.post_status='publish'
                    AND wp_posts.post_type='post'
                 ");
$result = mysql_query($query, $conn) or die(mysql_error($conn));


#Send response headers to the browser, following headers instruct the browser to treat the data as a csv file called export.csv
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=export.csv');

#Output header row (if atleast one row exists)
$row = mysql_fetch_assoc($result);
if ($row) {
    echocsv(array_keys($row));
}

#Output data rows (if atleast one row exists)
while ($row) {
    echocsv($row);
    $row = mysql_fetch_assoc($result);
}

/*
 * echo the input array as csv data maintaining consistency with most CSV implementations
 * - uses double-quotes as enclosure when necessary
 * - uses double double-quotes to escape double-quotes 
 * - uses CRLF as a line separator
 */

function echocsv($fields)
{
    $separator = '';
    foreach ($fields as $field) {
        if (preg_match('/\\r|\\n|,|"/', $field)) {
            $field = '"' . str_replace('"', '""', $field) . '"';
        }
        echo $separator . $field;
        $separator = ',';
    }
    echo "\r\n";
}
?>
