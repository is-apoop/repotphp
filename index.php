<?php
require_once 'includes/global.inc.php';
require_once 'includes/functions.inc.php';
?>

<html>
<head>
	<title>Homepage</title>
	<link href="styles/style.css" rel="stylesheet" type="text/css"/>
	<script src="script/jquery-2.1.3.min.js" type="text/javascript"></script>
</head>
<body>

<?php if(isset($_SESSION['logged_in'])) : ?>
	<?php $user = unserialize($_SESSION['user']); ?>
	Hello, <?php echo $user->name; ?>. You are logged in. <a href="logout.php">Logout</a> <br/>


<?php

$link = mysqli_connect("localhost","root","Nikitos9305","report_php");

//create and fill array of existing user reports
//create array of yet not saved reports
$user_reports = array();
$user_weekly_reports = array();

$this_week_monday_date = date('Y-m-d',strtotime('monday this week'));
$this_week_friday_date = date('Y-m-d',strtotime('friday this week'));

//create arrays of days
$days_of_week = array (
	1 => "MO",
	2 => "TU",
	3 => "WE",
	4 => "TH",
	5 => "FR"	
	);

//create array of dates
$days_dates = array();
for ($i = 1; $i <= 5; $i++) {
	switch ($i) 
	{
		case 1:
			array_push($days_dates, date('Y-m-d',strtotime('monday this week')));
			break;
		case 2:
			array_push($days_dates, date('Y-m-d',strtotime('tuesday this week')));
			break;
		case 3:
			array_push($days_dates, date('Y-m-d',strtotime('wednesday this week')));
			break;
		case 4:
			array_push($days_dates, date('Y-m-d',strtotime('thursday this week')));
			break;
		case 5:
			array_push($days_dates, date('Y-m-d',strtotime('friday this week')));
			break;
	}
}

//at first delete, change and add records if there are any

for ($i = 1; $i <= 5; $i++){

	$ok_down = 0;

	if(!empty($_POST['ok'.$i])) 
	{	
		// mark an 'ok down' event
		$ok_down = 1;

		// first delete the records marked for deletion. Why? Because we don't want to process them in the code below
		if( !empty($_POST['delete_ids']) and is_array($_POST['delete_ids'])) {
			// you can optimize below into a single query, but let's keep it simple and clear for now:
			foreach($_POST['delete_ids'] as $id) {
				$sql = "DELETE FROM reports WHERE report_id=$id";
				$link->query($sql);
			}
		}
		
		// now, to edit the existing records, we have to select all the records of records of a recent week of an $i day
		$sql="SELECT * FROM reports WHERE day='$days_of_week[$i]' AND rep_date>='$this_week_monday_date' AND rep_date<='$this_week_friday_date' ORDER BY report_id";
		$result = $link->query($sql);
		
		//$reports = $db->select('reports', "day='$days_of_week[$i]' AND rep_date>='$this_week_monday_date' AND rep_date<='$this_week_friday_date' ORDER BY report_id");
		//print_r($reports);

		// now edit them
		while($report = mysqli_fetch_array($result)) {

			if(isset($_POST['vacation'.$report['report_id']])) $on_vac = 1;
			else $on_vac = 0;

			$post_task = $_POST['task'.$report['report_id']];
			$post_time = $_POST['hours'.$report['report_id']].":".$_POST['minutes'.$report['report_id']];
			$post_comment = $_POST['comment'.$report['report_id']];
			$post_day = $days_of_week[$i];
			$post_date = $days_dates[$i-1];
			$post_vacation = $on_vac;
			$post_report_id = $report['report_id'];

			// remember how we constructed the field names? This was with the idea to access the values easy now
			$sql = "UPDATE reports 
			SET task = '$post_task', time = '$post_time', comment = '$post_comment', day = '$post_day', rep_date = '$post_date', on_vacation = '$post_vacation'
			WHERE report_id = '$post_report_id'";	

			$link->query($sql);
		}
		// (feel free to optimize this so query is executed only when a product is actually changed)
		
		
		// adding new products
		if(!empty($_POST['task'])) {
			foreach($_POST['task'] as $key => $task) {
				if(isset($_POST['vacation'][$key])) $post_add_vacation = 1;
				else $post_add_vacation = 0;

				$post_add_task = $_POST['task'][$key];
				$post_add_time = $_POST['hours'][$key].':'.$_POST['minutes'][$key];
				$post_add_comment = $_POST['comment'][$key];
				$post_add_day = $days_of_week[$i];
				$post_add_date= $days_dates[$i-1];

				$sql = "INSERT INTO reports (user_id, task, time, comment, day, rep_date, on_vacation) 
				VALUES ($user->user_id, '$post_add_task', '$post_add_time', '$post_add_comment', '$post_add_day', '$post_add_date', '$post_add_vacation')";
				$link->query($sql);
			}
		}
	}

	if($ok_down == 1) break;
}

$user_reports = $db->select('reports', "user_id = $user->user_id");

//walk through all of user reports and choose those of recent week
foreach ($user_reports as $report) {
	if($report['rep_date'] <= $this_week_friday_date && $report['rep_date'] >= $this_week_monday_date)
	{
		array_push($user_weekly_reports, $report);		
	}
}

//if ther is not a single task per day -> create it
//else - leave it
$user_reports_number = count($user_weekly_reports);

for ($i = 1; $i <= 5; $i++) {
    $day_has_report = 0;

    for ($j = 0; $j < $user_reports_number; $j++) {
    	if($days_of_week[$i] == $user_weekly_reports[$j]["day"]){
    		$day_has_report = 1;
    		break;
    	}
    }

    if($day_has_report == 0){

		switch ($i) {
			case 1:
				$day_date = date('Y-m-d',strtotime('monday this week'));
				break;
			case 2:
				$day_date = date('Y-m-d',strtotime('tuesday this week'));
				break;
			case 3:
				$day_date = date('Y-m-d',strtotime('wednesday this week'));
				break;
			case 4:
				$day_date = date('Y-m-d',strtotime('thursday this week'));
				break;
			case 5:
				$day_date = date('Y-m-d',strtotime('friday this week'));
				break;
		}
    	
    	$sql = "INSERT INTO reports (user_id, task, time, comment, day, rep_date, on_vacation) VALUES ($user->user_id, 'no task', '00:00:00', 'no comments', '$days_of_week[$i]', '$day_date', 0)";
    	mysql_query($sql) or die(mysql_error());
    }
    else continue;
} 


//$result = $db->select('reports', "user_id=$user->user_id AND rep_date>='$this_week_monday_date' AND rep_date<='$this_week_friday_date' ORDER BY report_id");
//print_r(array_values($result));


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! в этом еще разберись !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//saveXLCfile();

?>

<div class = "week_report">
	<?php foreach ($days_of_week as $key => $value):
		$key2=$key-1;
		echo "Day: ".$value."  Date: ".$days_dates[$key2];
		?>
	    <form method="post" name="post<?=$key?>">
		    <div id="reportRows<?=$key?>">
				<ul>
				<li>Task:<input type="text" name="add_task" size="20"> </li>
				<li>Time:<input type="number" min="0" max="9" name="add_hours" size="1">:<input type="number" min="0" max="59" name="add_minutes" size="1"></li>
				<li>Comment:<textarea name="add_comment" rows="1" cols="50"></textarea> </li>
				<li>Day:<input type="text" name="add_day" size="1" value="<?=$value?>" disabled></li>
				<li>Date:<input type="text" name="add_date" size="6" value="<?=$days_dates[$key2]?>" disabled></li>
				<li>On Vacation:<input type="checkbox" name="add_vacation"></li>
				<li><input onclick="addRow(this.form,<?=$key?>,'<?=$value?>','<?=$days_dates[$key2]?>');" type="button" value="Add report"></li>
				</ul>

				<?php 
				
				$link = mysqli_connect("localhost","root","Nikitos9305","report_php");

				$sql ="SELECT * FROM reports WHERE user_id=$user->user_id AND day='$value' AND (rep_date>='$this_week_monday_date' AND rep_date<='$this_week_friday_date') ORDER BY report_id";
				$result = $link->query($sql);

				while($report = mysqli_fetch_array($result)): 
					$hours = substr($report['time'],0,2); 
					$minutes = substr($report['time'],3,2);?>

					<ul id="oldRow<?=$report['report_id']?>">
					<li>Task:<input type="text" name="task<?=$report['report_id']?>" size="20" value="<?=$report['task']?>"> </li>
					<li>Time:<input type="number" min="0" max="9" name="hours<?=$report['report_id']?>" size="1" value="<?=$hours?>">:<input type="number" min="0" max="50" name="minutes<?=$report['report_id']?>" size="1" value="<?=$minutes?>"></li>
					<li>Comment:<textarea name="comment<?=$report['report_id']?>" rows="1" cols="50"><?=$report['comment']?></textarea></li>
					
					<li>Day:<input type="text" name="day<?=$report['report_id']?>" size="1" value="<?=$report['day']?>" disabled></li>
					<li>Date:<input type="text" name="date<?=$report['report_id']?>" size="6" value="<?=$report['rep_date']?>" disabled></li>

					<?php if($report['on_vacation'] == 1):?>
					<li>On Vacation:<input type="checkbox" name="vacation<?=$report['report_id']?>" checked></li>
					<?php else:?>
					<li>On Vacation:<input type="checkbox" name="vacation<?=$report['report_id']?>"></li>
					<?php endif?>

					<li> Mark to delete<input type="checkbox" name="delete_ids[]" value="<?=$report['report_id']?>"></li>
					</ul>

				<?php endwhile;?>
			</div>
			<p><input type="submit" name="ok<?=$key?>" value="Save Changes"></p>
		</form>
		<br/>
	 	
	<?php endforeach?>
	
	<script type="text/javascript">
	
	var rowNum = 0;
	function addRow(frm,divID,rep_day,rep_date) {
		rowNum ++;
		
		var a = rep_day;
		var b = rep_date;

		frm.add_task.value = 'no task' ;
		frm.add_hours.value = '00';
		frm.add_minutes.value = '00';
		frm.add_comment.value = 'no comments';
		frm.add_day.value = rep_day;
		frm.add_date.value = rep_date;

		var row = '<ul id="rowNum'+rowNum+
			'"><li>Task:<input type="text" name="task[]" size="20" value="'+frm.add_task.value+
			'"> </li><li>Time:<input type="number" min="0" max="9" size="1" name="hours[]" value="'+frm.add_hours.value+
			'">:<input type="number" min="0" max="50" size="1" name="minutes[]" value="'+frm.add_minutes.value+
			'"> </li><li>Comment:<textarea name="comment[]" rows="1" cols="50">'+frm.add_comment.value+
			'</textarea> </li><li>Day:<input type="text" name="day[]" size="1" value="'+a+
			'" disabled> </li><li>Date:<input type="text" name="date[]" size="6" value="'+b +
			'" disabled> </li><li>On Vacation:<input type="checkbox" name="vacation[]"></li>'+
			'<li><input type="button" value="Remove" onclick="removeRow('+rowNum+
			');"> </li></ul>';
	
		switch(divID){
			case 1: jQuery('#reportRows1').append(row); break;
			case 2: jQuery('#reportRows2').append(row); break;
			case 3: jQuery('#reportRows3').append(row); break;
			case 4: jQuery('#reportRows4').append(row); break;
			case 5: jQuery('#reportRows5').append(row); break;
		}
	}

	function removeRow(rnum) {
		jQuery('#rowNum'+rnum).remove();
	}
	</script>


</div>

<?php else : ?>
	<div class = "login">
		You are not logged in. <a href="login.php">Log In</a>
	</div>
<?php endif; ?>

</body>
</html>