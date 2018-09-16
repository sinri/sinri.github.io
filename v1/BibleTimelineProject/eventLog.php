<?php
require_once('lib_sql.php');
$msg='';
$act=$_REQUEST['act'];
if($act=='add_event'){
	$title=$_REQUEST['title'];
	$description=$_REQUEST['description'];
	$reference=$_REQUEST['reference'];
	$r=addEvent($title,$description,$reference);
	if($r==1){
		$msg='Add Event Done.';

		$base_event_id=$_REQUEST['base_event'];
		$year=$_REQUEST['year'];
		$month=$_REQUEST['month'];
		$day=$_REQUEST['day'];

		if($base_event_id>0){
			$new_event_id=find_event_id_by_title($title);
			$r=addRelation($base_event_id,$new_event_id,$year,$month,$day,$reference);
			if($r==1){
				$msg.=' Add Relation Done.';
			}else{
				$msg.=' Add Relation Failed with return affected rows of '.$r;
			}
		}	
	}else{
		$msg='Add Event Failed with return affected rows of '.$r;
	}
}else if ($act=='add_relation') {
	$from_event_id=$_REQUEST['from_event'];
	$to_event_id=$_REQUEST['to_event'];
	$year=$_REQUEST['year'];
	$month=$_REQUEST['month'];
	$day=$_REQUEST['day'];
	$memo=$_REQUEST['memo'];
	$r=addRelation($from_event_id,$to_event_id,$year,$month,$day,$memo);
	if($r==1){
		$msg='Add Relation Done';
	}else{
		$msg='Add Relation Failed with return affected rows of '.$r;
	}
}



$event_list=getAllEvents();

?>

<html>
	<head>
		<title>Timeline Bible Helper</title>
		<style type="text/css">
			div.add_event, div.add_relation{
				float: left;
				width: 450px;
				height: 200px;
				margin: 50px;
			}
			div.clear{
				clear: both;
			}
			input[type='text'].longInput {
				width: 300px;
			}
			input[type='text'].shortInput {
				width: 40px;
			}
			table, td {
				border: 1px solid gray;
				border-collapse:collapse;
				font-size: 13px;
				text-align: center;
				padding: 5px;
			}
			th {
				background-color: #2899D6;/* #6CB8FF; */
				color: #EEEEEE;
				border: 1px solid gray;
				border-collapse:collapse;
				padding: 5px;
				text-align: center;
			}
		</style>
	</head>
	<body>
		<h1>Timeline Bible Helper</h1>
		<p><?php echo $msg; ?></p>
		<hr>
		<div class='add_event'>
			<h2>Add Event</h2>
			<form method='post'>
				<input type='hidden' name='act' value='add_event'>
				<p>
					Event Title: <input type='text' name='title' value='' class="longInput">
					<input type='submit' name='submit' value='add'>
				</p>
				<p>
					After: 
					<select name='base_event'>
						<option value='-1'>Unselected</option>
						<?php 
							foreach ($event_list as $index => $event) {
								$event_id=$event['event_id'];
								$title=$event['event_title'];
								echo "<option value='{$event_id}'>{$event_id} - {$title}</option>";
							}
						?>
					</select>
					<input type='text' name='year' value='0' class="shortInput"> Years
					<input type='text' name='month' value='0' class="shortInput"> Months
					<input type='text' name='day' value='0' class="shortInput"> Days
				</p>
				<p>
					Event Description: <input type='text' name='description' value='' class="longInput">
				</p>
				<p>
					Event Reference: <input type='text' name='reference' value='' class="longInput">
				</p>
			</form>
		</div>
		<div class='add_relation'>
			<h2>Add Event Relationship</h2>
			<form method='post'>
				<input type='hidden' name='act' value='add_relation'>
				<p>
					From: 
					<select name='from_event'>
						<option value='-1'>Unselected</option>
						<?php 
							foreach ($event_list as $index => $event) {
								$event_id=$event['event_id'];
								$title=$event['event_title'];
								echo "<option value='{$event_id}'>{$event_id} - {$title}</option>";
							}
						?>
					</select>
					To: 
					<select name='to_event'>
						<option value='-1'>Unselected</option>
						<?php 
							foreach ($event_list as $index => $event) {
								$event_id=$event['event_id'];
								$title=$event['event_title'];
								echo "<option value='{$event_id}'>{$event_id} - {$title}</option>";
							}
						?>
					</select>
				</p>
				<p>
					In Between 
					<input type='text' name='year' value='0' class="shortInput"> Years
					<input type='text' name='month' value='0' class="shortInput"> Months
					<input type='text' name='day' value='0' class="shortInput"> Days
					<input type='submit' name='submit' value='add'>
				</p>
				<p>
					Memo:
					<input type='text' name='memo' value=''>
				</p>
			</form>
		</div>
		<div class='clear'></div>
		<hr>
		<?php
			displaySQL("select * from `bible_timeline`.`event` order by event_id desc limit 20;");
		?>
		<hr>
		<?php
			displaySQL("SELECT
				r.relation_id,
				ef.event_title e1,
				et.event_title e2,
				r.`year`,
				r.`month`,
				r.`day`
			FROM
				`bible_timeline`.`relation` r
			LEFT JOIN `bible_timeline`.`event` ef ON r.from_event_id = ef.event_id
			LEFT JOIN `bible_timeline`.`event` et ON r.to_event_id = et.event_id
			ORDER BY relation_id desc limit 20
			;");
		?>
	</body>
</html>

<?php
function getAllEvents(){
	$sql="select * from `bible_timeline`.`event`  order by event_id desc;";
	$r=sinri_sql_query_all($sql);
	// print_r($r);
	return $r;
}
function getAllRelations(){
	$sql="SELECT
		r.relation_id,
		ef.event_title e1,
		et.event_title e2,
		r.`year`,
		r.`month`,
		r.`day`
	FROM
		`bible_timeline`.`relation` r
	LEFT JOIN `bible_timeline`.`event` ef ON r.from_event_id = ef.event_id
	LEFT JOIN `bible_timeline`.`event` et ON r.to_event_id = et.event_id;";
	$r=sinri_sql_query_all($sql);
	// print_r($r);
	return $r;
}
function addEvent($title,$description,$reference){
	if($title && $title!=''){
		if(!$description){
			$description='';
		}
		if(!$reference){
			$reference='';
		}
		$sql="INSERT INTO `bible_timeline`.`event` (
			`event`.`event_id`,
			`event`.`event_title`,
			`event`.`event_description`,
			`event`.`event_referenct`
		)
		VALUES
			(
				NULL,
				'{$title}',
				'{$description}',
				'{$reference}'
			)
		;";
		$r=sinri_execute_affected_rows($sql);
		return $r;
	}else{
		return false;
	}
}
function addRelation($from_event_id,$to_event_id,$year,$month,$day,$memo){
	if($from_event_id<=0 || $to_event_id<=0){
		return false;
	}
	if(!$year || $year<0) {
		$year=0;
	}
	if(!$month || $month<0) {
		$month=0;
	}
	if(!$day || $day<0) {
		$day=0;
	}
	if(!$memo){
		$memo='';
	}
	$sql="INSERT INTO `bible_timeline`.`relation` (
		relation.relation_id,
		relation.from_event_id,
		relation.to_event_id,
		relation.`YEAR`,
		relation.`MONTH`,
		relation.`DAY`,
		relation.memo
	)
	VALUES
		(NULL, {$from_event_id}, {$to_event_id}, {$year}, {$month}, {$day}, '{$memo}');";
	$r=sinri_execute_affected_rows($sql);
	return $r;
}

function find_event_id_by_title($title){
	$sql="select event_id from `bible_timeline`.`event` where event_title='{$title}' limit 1";
	$one=sinri_sql_query_getOne($sql);
	return $one;
}
?>