<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = "cmsresponsiv";
$backup_path = "E:/wamp/www/misc/exportdb/exporttables/";
$conn = mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname);

if(!$conn)
{
  die('Could not connect: ' . mysql_error());
}

$sqlQuery = "SHOW TABLES";
$objQuery = mysql_query($sqlQuery);
$intRows = mysql_num_rows($objQuery);
if($intRows > 0)
{
	while($arr = mysql_fetch_assoc($objQuery))
	{
		$table_name = $arr["Tables_in_".$dbname];
		$strCreateTable = $arr["Create Table"];
		$strCreateTable .= "\n\n";
		
		$backup_file  = $backup_path.$table_name.".sql";
		$sql = "SELECT * INTO OUTFILE '$backup_file' FROM $table_name";
		$retval = mysql_query( $sql, $conn );
	}
}
echo "Backedup data successfully\n";
mysql_close($conn);
?>