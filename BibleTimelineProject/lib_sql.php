<?php //header("Cache-Control: no-cache, must-revalidate");//header("Pragma: no-cache");define("SINRI_MYSQL_USER_READONLY",'root');define("SINRI_MYSQL_USER_READWRITE",'root'); define("SINRI_MYSQL_USER_PASSWORD",'123456');$debug_mode=false;function debug($info) {	global $debug_mode;	if($debug_mode)echo("<p>DEBUG:{$info}</p>");}function sinri_sql_new_connect($need_write=false) {	if($need_write){		$con = mysql_connect('127.0.0.1:3306', SINRI_MYSQL_USER_READWRITE, SINRI_MYSQL_USER_PASSWORD);	}else{		$con = mysql_connect('127.0.0.1:3306', SINRI_MYSQL_USER_READONLY, SINRI_MYSQL_USER_PASSWORD);	}	mysql_select_db('hdm1320604_db'); 	return $con;}function sinri_sql_query_all($sql,$fromPost=false) {	//$con = mysql_connect('mysql-s', 'w2320418ro', '20070715');	$con=sinri_sql_new_connect(false);	if($fromPost){		debug("original sql=".$sql);		$sql=stripslashes($sql);	}	if($con){		debug("sinri_sql_query_all con got");		$result = mysql_query($sql, $con);		mysql_close($con);		if($result){			debug("sinri_sql_query_all result got");			$count = mysql_num_rows($result);			$rows=array();			for ($i = 0; $i < $count; $i++) {				$rows[$i]=mysql_fetch_assoc($result);			}			return $rows;		}else{			debug("sinri_sql_query_all result got not");			return array();		}	}else{		debug("sinri_sql_query_all con got not");		return false;	}}function sinri_sql_query_getCol($sql,$fromPost=false) {	//$con = mysql_connect('mysql-s', 'w2320418ro', '20070715');	$con=sinri_sql_new_connect(false);	if($fromPost){		debug("original sql=".$sql);		$sql=stripslashes($sql);	}	if($con){		debug("sinri_sql_query_all con got");		$result = mysql_query($sql, $con);		mysql_close($con);		if($result){			debug("sinri_sql_query_all result got");			$count = mysql_num_rows($result);			$rows=array();			for ($i = 0; $i < $count; $i++) {				$row=mysql_fetch_array($result);				$rows[$i]=$row[0];			}			return $rows;		}else{			debug("sinri_sql_query_all result got not");			return array();		}	}else{		debug("sinri_sql_query_all con got not");		return false;	}}function sinri_sql_query_getRow($sql,$fromPost=false) {	//$con = mysql_connect('mysql-s', 'w2320418ro', '20070715');	$con=sinri_sql_new_connect(false);	if($fromPost){		debug("original sql=".$sql);		$sql=stripslashes($sql);	}	if($con){		debug("sinri_sql_query_all con got");		$result = mysql_query($sql, $con);		mysql_close($con);		if($result){			debug("sinri_sql_query_all result got");			$row=mysql_fetch_assoc($result);			return $row;		}else{			debug("sinri_sql_query_all result got not");			return array();		}	}else{		debug("sinri_sql_query_all con got not");		return false;	}}function sinri_sql_query_getOne($sql,$fromPost=false) {	//$con = mysql_connect('mysql-s', 'w2320418ro', '20070715');	$con=sinri_sql_new_connect(false);	if($fromPost){		debug("original sql=".$sql);		$sql=stripslashes($sql);	}	if($con){		debug("sinri_sql_query_all con got");		$result = mysql_query($sql, $con);		mysql_close($con);		if($result){			debug("sinri_sql_query_all result got");			$row=mysql_fetch_array($result);			return $row[0];		}else{			debug("sinri_sql_query_all result got not");			return array();		}	}else{		debug("sinri_sql_query_all con got not");		return false;	}}function sinri_sql_execute($sql,$fromPost=false) {	//$con = mysql_connect('mysql-s', 'w2320418ro', '20070715');	$con=sinri_sql_new_connect(true);	if($fromPost){		debug("original sql=".$sql);		$sql=stripslashes($sql);	}	if($con){		debug("sinri_sql_query_all con got");		$result = mysql_query($sql, $con);		//$rows=mysql_affected_rows();		mysql_close($con);		return $result;	}else{		debug("sinri_sql_query_all con got not");		return false;	}}function sinri_execute_affected_rows($sql,$fromPost=false) {	$con=sinri_sql_new_connect(true);	if($fromPost){		debug("original sql=".$sql);		$sql=stripslashes($sql);	}	if($con){		debug("sinri_execute_affected_rows con got to run sql=".$sql);		$result = mysql_query($sql, $con);		debug("sinri_execute_affected_rows ".($result?"got [{$result}]":"got result false"));		$err = mysql_error(); 		debug("sinri_execute_affected_rows error=".$err);		$rows=mysql_affected_rows();		debug("sinri_execute_affected_rows sql done rows=".$rows);		mysql_close($con);		return $rows;	}else{		debug("sinri_execute_affected_rows con got not");		return false;	}}function displaySQL($sql,$fromPost=false){	if(!empty($sql)){		$r=sinri_sql_query_all($sql,$fromPost);		if($r){			echo "<div class='waku'>";			echo "<p>".$sql."</p>";			echo "<table border='1'>";			echo "<tr>";			foreach ($r[0] as $key => $value) {				echo "<th>$key</th>";			}			echo "</tr>";						foreach ($r as $rid => $rline) {				echo "<tr>";				foreach ($rline as $key => $value) {					echo "<td>$value</td>";				}				echo "</tr>";			}			echo "</table>";			echo "</div>";		}else{			echo "<div><p>".$sql."</p><p>No results</p></div>";		}	}}# TEST HERE/*if($debug_mode && $_GET['sinri_test']==SINRI_MYSQL_USER_PASSWORD){	$sql = "SELECT note_id, note_words, note_to_id, note_writer, note_time	        FROM s1931376_main.sinri_note	        WHERE note_id > 0	        ORDER BY note_id DESC 	        LIMIT 500";	$result=sinri_sql_query_all($sql);	if($result){		print_r($result);	}else{		echo("He qi liao");	}	echo("<hr>");	$sql = "SELECT note_id	        FROM s1931376_main.sinri_note	        WHERE note_id > 0	        ORDER BY note_id DESC 	        LIMIT 500";	$result=sinri_sql_query_getCol($sql);	if($result){		print_r($result);	}else{		echo("He qi liao");	}	echo("<hr>");	$sql = "SELECT note_id, note_words, note_to_id, note_writer, note_time	        FROM s1931376_main.sinri_note	        WHERE note_id > 0	        ORDER BY note_id DESC 	        LIMIT 1";	$result=sinri_sql_query_getRow($sql);	if($result){		print_r($result);	}else{		echo("He qi liao");	}	echo("<hr>");	$sql = "SELECT note_id	        FROM s1931376_main.sinri_note	        WHERE note_id > 0	        ORDER BY note_id DESC 	        LIMIT 1";	$result=sinri_sql_query_getOne($sql);	if($result){		print_r($result);	}else{		echo("He qi liao");	}}*/?>