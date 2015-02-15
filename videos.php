<?php

ini_set('display_errors' , 'on');
include  'config.php';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "thalijw-db" , $password , "thalijw-db");

if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; 
} else {
	echo "Connection is successful<br><br>";
	}

if (isset($_POST['delete']) && isset($_POST['id'])){
	$id = get_post($mysqli , 'id');
	$query = "DELETE FROM Inventory WHERE id='$id'" ;
	$result = $mysqli->query($query);
	if (!$result) {
		echo "Delete failed : " . $mysqli->error . "<br><br>";
	}
}

if (isset($_POST['switch']) && isset($_POST['id'])){
	$id = get_post($mysqli , 'id');
	$query = "SELECT rented FROM Inventory WHERE id='$id'";
	$result	= $mysqli->query($query);
	if (!$result) {
		die ("Database access failed: " . $mysqli->error);
	}
	$row = $result->num_rows;
	$result ->data_seek(0);
	$row = $result->fetch_array(MYSQLI_NUM);
	if ($row[0] === '0'){

		$query = "UPDATE Inventory SET rented = TRUE WHERE id='$id'" ;

	}else{

		$query = "UPDATE Inventory SET rented = FALSE WHERE id='$id'" ;
	}

	$result = $mysqli->query($query);
	if (!$result) {
		echo "Update failed : " . $mysqli->error . "<br><br>";
	}
}

if (isset($_POST['deleteAll'])){

	$query = "TRUNCATE TABLE `Inventory` ";
	$result	= $mysqli->query($query);
	if (!$result) {
		die ("Database access failed: " . $mysqli->error);
	}

}

if ( (isset($_POST['name'])) && ($_POST['name'] != null)){

	$Name = get_post($mysqli , 'name');

	if (isset($_POST['category'])){
		$Category = get_post($mysqli , 'category');
	} else { $Category = "NULL" ;}

	if (isset($_POST['length'])) {
		$Length = get_post($mysqli , 'length');

	} else { $Length = "NULL" ;}

	$query = "INSERT INTO Inventory (name,category,length) VALUES" . "('$Name' , '$Category' , '$Length')";
	$result = $mysqli->query($query);

	if (!$result) {
		"Insert failed: " . $mysqli->error . "<br><br>";
	}
} else if (!isset($_POST['name']) && ( (isset($_POST['category'])) || (isset($_POST['length'])) ) ){
	echo "Invalid Entry Please Enter a Video Name!";
}
echo "
<fieldset>
<legend>Add Videos</legend>
<form action = 'videos.php' method = 'POST'>
<pre>
Video Name : <input type = 'text' name = 'name' > <br> 

Video Category : <input type = 'text' name = 'category' > <br> 

Length in minutes : <input type = 'text' name = 'length' size = '20' > <br> 
<input type='hidden' name = 'Filter' value='All'>
<input type ='submit' value ='Add Video' >
</pre>
</form>
<br>";

$query = "SELECT category FROM Inventory ";
$result	= $mysqli->query($query);
if (!$result) {
		die ("Database access failed: " . $mysqli->error);
}

$row = $result->num_rows;
$RowInt = intval($row);

$CatArray[] = '';
	
for ($b = $RowInt-1 ; $b >= 0 ; $b--){

		$result->data_seek($b);
		$row = $result->fetch_array(MYSQLI_NUM);
		$CatArray[$b] = $row[0];
}

$FilterArr = array_unique($CatArray);

echo '
<p>Please select a Category to display</p>
<form action = "videos.php" method = "POST">
<select name = "Filter"> ';

foreach($FilterArr as $option){
	echo "<option value = '$option' name = 'option'";
	echo " >$option </option>";
}
echo '
<option value = "All" selected = "selected" >All Movies</option>
</select>
	<input type=submit value = "Filter" >
</form>
</fieldset>';

if (isset($_REQUEST['Filter']) ) {

	if ($_REQUEST['Filter'] == 'All'){

		$query = "SELECT * FROM Inventory ";
	}
	else{
		$CatOption = $_REQUEST['Filter'];
		$query = "SELECT * FROM Inventory WHERE category = '$CatOption'";
	}

	$result	= $mysqli->query($query);
	if (!$result) {
		die ("Database access failed: " . $mysqli->error);
	}

	$row = $result->num_rows;
	$RowInt = intval($row);
		echo "<br><br>
	<div>
	<table>
		<tr> 
		<th>ID</th>
		<th>Video Name</th>
		<th>Category</th>
		<th>Length</th>
		<th>Availablity</th>";
	
	for($m = 0 ; $m < $RowInt ; $m++)
	{
		echo "<tr>";
		$result ->data_seek($m);
		$row = $result->fetch_array(MYSQLI_NUM);
		for($k = 0 ; $k < 5 ; $k++)
		{
			if($k === 4){
				if($row[$k] === '0'){
					echo "<td>Available</td>";
				}else
					echo "<td>Rented</td>";
			}
			else
				echo "<td>$row[$k]</td>";
		}
		echo "
		<td>
			<form action = 'videos.php' method = 'POST'>
				<input type='hidden' name = 'delete' value = 'yes'>
				<input type='hidden' name = 'id' value='$row[0]'>
				<input type='hidden' name = 'Filter' value='All'>
				<input type='submit' value='Delete Video' >
			</form>
		</td>" ;
		echo "
		<td>
			<form action = 'videos.php' method = 'POST'>
				<input type='hidden' name = 'switch' value = 'yes'>
				<input type='hidden' name = 'id' value='$row[0]'>
				<input type='hidden' name = 'Filter' value='All'>
			<input type='submit' value='checkin / checkout' >
			</form>
		</td>" ;
		echo "</tr>";

	}
	echo "
	</table>
	</div>";

	}else{

	$query = "SELECT * FROM Inventory ";
	$result	= $mysqli->query($query);
	if (!$result) {
		die ("Database access failed: " . $mysqli->error);
	}

	$row = $result->num_rows;
	$RowInt = intval($row);
		echo "<br><br>
	<div>
	<table>
		<tr> 
		<th>ID</th>
		<th>Video Name</th>
		<th>Category</th>
		<th>Length</th>
		<th>Availablity</th>";
	
	for($m = $RowInt-1 ; $m >= 0 ; $m--)
	{
		echo "<tr>";
		$result ->data_seek($m);
		$row = $result->fetch_array(MYSQLI_NUM);
		for($k = 0 ; $k < 5 ; $k++)
		{
			if($k === 4){
				if($row[$k] === '0'){
					echo "<td>Available</td>";
				}else
					echo "<td>Rented</td>";
			}
			else
				echo "<td>$row[$k]</td>";
		}
		echo "
		<td>
			<form action = 'videos.php' method = 'POST'>
				<input type='hidden' name = 'delete' value = 'yes'>
				<input type='hidden' name = 'id' value='$row[0]'>
				<input type='hidden' name = 'Filter' value='All'>
				<input type='submit' value='Delete Video' >
			</form>
		</td>" ;
		echo "
		<td>
			<form action = 'videos.php' method = 'POST'>
				<input type='hidden' name = 'switch' value = 'yes'>
				<input type='hidden' name = 'id' value='$row[0]'>
				<input type='hidden' name = 'Filter' value='All'>
			<input type='submit' value='checkin / checkout' >
			</form>
		</td>" ;
		echo "</tr>";

	}
	echo "
	</table>
	</div>";

	}

	echo "
<div>			
<form action = 'videos.php' method = 'POST'>
<input type='hidden' name = 'deleteAll' value = 'yes'>
<input type='hidden' name = 'Filter' value='All'>
<input type='submit' value='Delete All Video' >
</form>
</div>";

	$mysqli->close();

	function get_post($mysqli , $var){
		return $mysqli->real_escape_string($_POST[$var]);
	}
?>
