<?php

ini_set('display_errors' , 'on');
include  'config.php';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "thalijw-db" , $password , "thalijw-db");

if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; 
} else {
	echo "Connection is seccussful<br>";
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
	echo "$row[0]" . "  <br>" ;
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

if ( (isset($_POST['name'])) && ($_POST['name'] != null) ){

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
} else if (!isset($_POST['name']) || ($_POST['name'] === null) || ($_POST['name'] === "") ){
	echo "Invalid Entry Please Enter a Video Name!";
}
echo "
<fieldset>
	<legend>Add Videos</legend>
	<form action = 'videos.php' method = 'POST'>
		<pre>
		 Video Name : <input type = 'text' name = 'name' > <br> 
		 <br> 
		 Video Category : <input type = 'text' name = 'category' > <br> 
		 <br> 
		 Length in minutes : <input type = 'text' name = 'length' size = '20' > <br> 
		 <br> 
		 <input type ='submit' value ='Add Video' >
		</pre>
	</form>
</fieldset>";

	$query = "SELECT * FROM Inventory";
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


	for($j = $RowInt-1 ; $j >= 0 ; $j--)
	{

		$result ->data_seek($j);
		$row = $result->fetch_array(MYSQLI_NUM);

		echo "<tr>";

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
				<input type='submit' value='Delete Video' >
			</form>
		</td>" ;
		echo "
		<td>
			<form action = 'videos.php' method = 'POST'>
				<input type='hidden' name = 'switch' value = 'yes'>
				<input type='hidden' name = 'id' value='$row[0]'>
			<input type='submit' value='checkin / checkout' >
			</form>
		</td>" ;
		echo "</tr>";

	}
	echo "
	</table>
	</div>";

	$result->close();
	$mysqli->close();

	function get_post($mysqli , $var){
		return $mysqli->real_escape_string($_POST[$var]);
	}

/*if ($_POST){
	$Name = $_POST['name'];
	$Category = $_POST['category'];
	$Length = $_POST['length'];

		echo "Name is : " . $Name . "  " . "Category is " . $Category . "  " . "Length is " . $Length;
	}
	// Prepared statement
	if (!($stmt = $mysqli->prepare("INSERT INTO Inventory(name,category,length) VALUES (?,?,?)"))) {
		echo "Prepare statement failed: (" . $mysqli->error;
	}

	if (!$stmt->bind_param("ssi" , $Name , $Category, $Length)){
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		//echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		echo "\nInvalid Entry: " . $stmt->error;
	}*/

//}
?>
