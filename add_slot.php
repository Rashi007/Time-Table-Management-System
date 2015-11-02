<html>
<head>
	<title>Timetable</title>
	<link rel="stylesheet" type="text/css" href="mystyle.css">
</head>
</html>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//$fname=$_POST['fname'];
include 'db_conn.php';
//$fname="'".$_POST['fname']."'";
$cl_id="'".$_POST['Classroom']."'";
$c_id="'".$_POST['Subject']."'";
$day="'".$_POST['Day']."'";
$time="'".$_POST['Time']."'";
$batch="'".$_POST['Batch']."'";
$duration_form=$_POST['Duration'];
if($duration_form==0){
	//echo "asdsad111111111111";
}
if($_POST['submit']=="Delete"){
	//echo "hiii";
	$sql="DELETE FROM Booking
WHERE B_ID=$batch AND C_ID=$c_id AND Day=$day ";
$result=mysqli_query($link,$sql);
//$count=mysqli_num_rows($result);
//$row = mysqli_fetch_assoc($result);
	include 'add_slot.html';
	die();
}
$duration=1;
//echo $batch;
/*SELECT DISTINCT * FROM (SELECT CL_ID,Day,Time
FROM Classroom,Day,Time) as a
WHERE NOT EXISTS (SELECT b.CL_ID,b.Day,b.Time
                 FROM Booking b
                WHERE  (b.CL_ID = a.CL_ID) AND (b.Day = a.Day) AND  (b.Time = a.Time)
                 ) LIMIT 10*/
//nos constraint
$sql="SELECT Nos FROM Course WHERE C_ID=$c_id";
$result=mysqli_query($link,$sql);
$count=mysqli_num_rows($result);
$row = mysqli_fetch_assoc($result);
$nos=$row['Nos'];
$sql="SELECT * FROM Booking WHERE C_ID=$c_id && B_ID=$batch";
$result=mysqli_query($link,$sql);
$count=mysqli_num_rows($result);
if($count>=$nos){
	echo 'Maximum number of classes for this batch and course is alloted';
	include 'add_slot.html';
	die();
}
/*SELECT CL_ID,Day,Time
FROM Classroom,Day,Time
WHERE NOT EXISTS (SELECT b.CL_ID,b.Day,b.Time
                 FROM Booking b
                 )*/
//Strength CONSTRAINT
$sql="SELECT Strength FROM Batch WHERE B_ID=$batch";
$result=mysqli_query($link,$sql);
$count=mysqli_num_rows($result);
//echo $count;
$strength_batch=0;	
$row = mysqli_fetch_assoc($result);
$strength_batch=$row['Strength'];
//echo $strength_batch;		
//echo $cl_id;
$sql="SELECT Strength FROM Classroom WHERE CL_ID=$cl_id";
$result=mysqli_query($link,$sql);
//$count=mysqli_num_rows($result);
//echo $count;
$strength_class=0;	
$row = mysqli_fetch_assoc($result);
$strength_class=$row['Strength'];
//echo $strength_class;
if($strength_batch>$strength_class){
	echo 'Cant accomodate this quantity';
	include 'add_slot.html';
	die();
	
}
//For calculating duration
if($duration_form==0){
	$sql="SELECT Duration FROM Course WHERE C_ID=$c_id";
$result=mysqli_query($link,$sql);
$row = mysqli_fetch_assoc($result);
$duration=$row['Duration'];
}
else{
	$duration=$duration_form;
}
//echo $_POST['Time']+$duration;
$time2=$_POST['Time'];
//TIME CONSTRAINT
if($duration_form==0&&((($time2>13&&$time2<14.30)||$time2>17.30)||(($time2+$duration>13&&$time2+$duration<14.30)||$time2+$duration>17.30))){
	echo 'Cannot be scheduled during this time';
	include 'add_slot.html';
	die();
}
$sql="SELECT * FROM Booking WHERE Day=$day && C_ID=$c_id && B_ID=$batch";
$result=mysqli_query($link,$sql);
$count=mysqli_num_rows($result);
//echo $count;
if($count>=1){
	echo "More than oneclass can't be scheduled on one day".'<br>';
	include 'add_slot.html';
}
else{
	$flag=0;
	echo $duration;
	if($duration==1){
		$sql="SELECT * FROM Booking WHERE Day=$day && CL_ID=$cl_id && Time=$time";
		$result=mysqli_query($link,$sql);
		$count=mysqli_num_rows($result);
		echo $count;
		if($count>=1){
			echo 'Clasheddddds!!';
			$flag=1;
			//include 'add_slot.html';
			echo 'Alternatives...';
			
			include 'add_slot.html';
			$sql = "SELECT DISTINCT * FROM (SELECT CL_ID,Strength,Day,Time
					FROM Classroom,Day,Time) as a
					WHERE NOT EXISTS (SELECT b.CL_ID,b.Day,b.Time
                 FROM Booking b
                WHERE  (b.CL_ID = a.CL_ID) AND (b.Day = a.Day) AND  ((b.Time = a.Time))
            )   AND Strength>=$strength_batch  ORDER BY Day DESC, Time ASC  LIMIT 10";
			$result = mysqli_query($link,$sql);
			echo '<table border="1" table id="myTable" class="tablesorter" align="center" style="box-shadow: -1px 3px 6px 6px  rgba(0,0,0,0.8);border-radius:10px;width:67%;margin-left:0%">
			<th>Classroom</th>
			<th>Day</th>
			<th>Time</th>';
			/*Show the rows in the fetched resultset one by one*/
			while ($row = mysqli_fetch_assoc($result))
			{
			echo '<tr>
			<td>'.$row['CL_ID'].'</td>
			<td>'.$row['Day'].'</td>
			<td>'.$row['Time'].'</td>
			</tr>';
			}
			echo '</table></div>';
			
			die();
		}
		else{
			echo $c_id.$cl_id. $day. $time;
			$sql = "INSERT INTO Booking (B_ID,C_ID,CL_ID, Day, Time) VALUES ($batch,$c_id,$cl_id, $day, $time)";
			$result=mysqli_query($link,$sql);
			include 'add_slot.html';
			die();
		}			
	}
	else if($duration==2){
		$flag=0;
		$sql="SELECT * FROM Booking WHERE Day=$day && CL_ID=$cl_id && Time=$time";
		$result=mysqli_query($link,$sql);
		$count=mysqli_num_rows($result);
		if($count>=1){
			echo 'Clashed!!';
			$flag=1;
			//include 'add_slot.html';
			//die();
		}
		$time4=$time2+1;
		$time3="'".$time4."'";
		$sql="SELECT * FROM Booking WHERE Day=$day && CL_ID=$cl_id && Time=$time3";
		$result=mysqli_query($link,$sql);
		$count=mysqli_num_rows($result);
		if($count>=1){
			$flag=1;
			echo 'Clashed';
			//include 'add_slot.html';
			//die();
		}
		if($flag==1){
			include 'add_slot.html';
			$sql = "SELECT DISTINCT * FROM (SELECT CL_ID,Strength,Day,Time
					FROM Classroom,Day,Time) as a
					WHERE NOT EXISTS (SELECT b.CL_ID,b.Day,b.Time
                 FROM Booking b
                WHERE  (b.CL_ID = a.CL_ID) AND (b.Day = a.Day) AND  ((b.Time = a.Time) OR a.Time+1=b.Time)
                )    AND Strength>=$strength_batch  ORDER BY Day DESC, Time ASC LIMIT 10";
			$result = mysqli_query($link,$sql);
			echo '<table border="1" table id="myTable" class="tablesorter" align="center" style="box-shadow: -1px 3px 6px 6px  rgba(0,0,0,0.8);border-radius:10px;width:67%;margin-left:0%">
			<th>Classroom</th>
			<th>Day</th>
			<th>Time</th>';
			/*Show the rows in the fetched resultset one by one*/
			while ($row = mysqli_fetch_assoc($result))
			{
			echo '<tr>
			<td>'.$row['CL_ID'].'</td>
			<td>'.$row['Day'].'</td>
			<td>'.$row['Time'].'</td>
			</tr>';
			}
			echo '</table></div>';
			die();
			
			
		}
		$sql = "INSERT INTO Booking (B_ID,C_ID,CL_ID, Day, Time) VALUES ($batch,$c_id,$cl_id, $day, $time)";
		$result=mysqli_query($link,$sql);
		$sql = "INSERT INTO Booking (B_ID,C_ID,CL_ID, Day, Time) VALUES ($batch,$c_id,$cl_id, $day, $time3)";
		$result=mysqli_query($link,$sql);
		echo 'Succesful';
		include 'add_slot.html';
		die();
	}
	else{
		$flag=0;
		$sql="SELECT * FROM Booking WHERE Day=$day && CL_ID=$cl_id && Time=$time";
		$result=mysqli_query($link,$sql);
		$count=mysqli_num_rows($result);
		if($count>=1){
			echo 'Clashed!!';
			$flag=1;
			//include 'add_slot.html';
			//die();
		}
		$time4=$time2+1;
		$time3="'".$time4."'";
		$sql="SELECT * FROM Booking WHERE Day=$day && CL_ID=$cl_id && Time=$time3";
		$result=mysqli_query($link,$sql);
		$count=mysqli_num_rows($result);
		if($count>=1){
			$flag=1;
			echo 'Clashed';
			//include 'add_slot.html';
			//die();
		}
		$time41=$time2+2;
		$time31="'".$time41."'";
		$sql="SELECT * FROM Booking WHERE Day=$day && CL_ID=$cl_id && Time=$time31";
		$result=mysqli_query($link,$sql);
		$count=mysqli_num_rows($result);
		if($count>=1){
			$flag=1;
			echo 'Clashed';
			//include 'add_slot.html';
			//die();
		}
		if($flag==1){
			include 'add_slot.html';
			echo "---".$strength_batch;
			$sql = "SELECT DISTINCT * 
			FROM (SELECT CL_ID,Strength,Day,Time FROM Classroom,Day,Time) as a 
			WHERE NOT EXISTS (SELECT b.CL_ID,b.Day,b.Time 
			FROM Booking b WHERE (b.CL_ID = a.CL_ID) AND (b.Day = a.Day) AND ((b.Time = a.Time) OR a.Time+1=b.Time OR a.Time+2=b.Time) )AND Strength>=$strength_batch    ORDER BY Day DESC, Time ASC LIMIT 10";
			$result = mysqli_query($link,$sql);
			echo '<table border="1" table id="myTable" class="tablesorter" align="center" style="box-shadow: -1px 3px 6px 6px  rgba(0,0,0,0.8);border-radius:10px;width:67%;margin-left:0%">
			<th>Classroom</th>
			<th>Day</th>
			<th>Time</th>';
			/*Show the rows in the fetched resultset one by one*/
			while ($row = mysqli_fetch_assoc($result))
			{
			echo '<tr>
			<td>'.$row['CL_ID'].'</td>
			<td>'.$row['Day'].'</td>
			<td>'.$row['Time'].'</td>
			</tr>';
			}
			echo '</table></div>';
			die();
			
			
		}
		$sql = "INSERT INTO Booking (B_ID,C_ID,CL_ID, Day, Time) VALUES ($batch,$c_id,$cl_id, $day, $time)";
		$result=mysqli_query($link,$sql);
		$sql = "INSERT INTO Booking (B_ID,C_ID,CL_ID, Day, Time) VALUES ($batch,$c_id,$cl_id, $day, $time3)";
		$result=mysqli_query($link,$sql);
		$sql = "INSERT INTO Booking (B_ID,C_ID,CL_ID, Day, Time) VALUES ($batch,$c_id,$cl_id, $day, $time31)";
		$result=mysqli_query($link,$sql);
		include 'add_slot.html';
		die();
	}
	/*
	$query = "INSERT INTO Booking (CL_ID,C_ID,B_ID,Day,Time) VALUES ($cl_id,$subject,$b_id, $day,$time)";
	$result = mysqli_query($link,$query);
	if($_POST['Duration']==2){
		$query = "INSERT INTO Booking (CL_ID,C_ID,B_ID,Day,Time) VALUES ($cl_id,$subject,$b_id, $day,$time)";
		$result = mysqli_query($link,$query);
	}*/
}
/*
*/
?>
