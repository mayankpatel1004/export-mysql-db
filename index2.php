<?php
ini_set("display_errors","1");
ini_set("memory_limit","512M");
ini_set("max_execution_time","0");

$strHost = "localhost";
$strUser = "root";
$strPassword = "";
$strDb = "cmsresponsiv";
backup_tables($strHost,$strUser,$strPassword,$strDb);


/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	$return = "";
	$link = mysql_connect($host,$user,$pass) or die("database not connected");
	mysql_select_db($name,$link) or die("database not found");
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		//$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = str_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$handle = fopen('exporttables/db-backup-'.date("Ymdhis").'-'.$name.'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}