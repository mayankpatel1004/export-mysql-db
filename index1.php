<?php
ini_set("display_errors","1");
ini_set("memory_limit","512M");
ini_set("max_execution_time","0");
global $db;
$strHost = "localhost";
$strUser = "root";
$strPassword = "";
$strDb = "cmsresponsiv";
$dbpath = "exporttables/db-backup-".date("Ymdhis")."-".$strDb.".sql";

backup_tables($strHost,$strUser,$strPassword,$strDb,"*",$dbpath);

function backup_tables($host,$user,$pass,$name,$tables = '*',$dbpath)
{
	$strHost = "localhost";
	$strUser = "root";
	$strPassword = "";
	$strDb = "cmsresponsiv";
	$return = "";
	$db = new PDO('mysql:host='.$strHost.';dbname='.$strDb, $strUser, $strPassword);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$sql_query = "SHOW TABLES";
		$stmt = $db->prepare($sql_query);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($stmt->rowCount() > 0)
		{
			foreach($stmt->fetchAll() as $key => $value)
			{
				$tables[] = $value["Tables_in_".$strDb];
			}
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	//cycle through
	foreach($tables as $table)
	{
		$sth = $db->prepare('SELECT * FROM '.$table);
		$sth->execute();
		$num_fields = $sth->columnCount();
		
		
		$sth2 = $db->prepare('SHOW CREATE TABLE '.$table);
		$sth2->execute();
		$row2 = $sth2->fetch(PDO::FETCH_NUM);
		
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			foreach($sth->fetchAll() as $row)
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
	$handle = fopen($dbpath,'w+');
	fwrite($handle,$return);
	fclose($handle);
}