<?php
ini_set("memory_limit" , -1);
$config = require_once 'config.php';

$rows = array();

$db_host = $config['db_host'];
$db_user = $config['db_user'];
$db_pass = $config['db_pass'];
$db_name = $config['db_name'];
$db_table = $config['db_table'];

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
	die('Connection failed: ' . $conn->connect_error);
}

function table($data) {
echo (?>
<tr>
	<td><?php echo $data['number']; ?></td>
	<td><?php echo $data['date']; ?></td>
	<td><?php echo $data['noti_type']; ?></td>
	<td><?php echo $data['osu_id']; ?></td>
	<td><?php echo $data['osu_mode']; ?></td>
	<td><?php echo $data['twitter_id']; ?></td>
	<td><?php echo $data['twitter_email']; ?></td>
	<td><?php echo $data['email_address']; ?></td>
	<td><?php echo $data['email_verifying_key']; ?></td>
	<td><?php echo $data['email_verified']; ?></td>
	<td><?php echo $data['web_ip']; ?></td>
	<td><?php echo $data['cf_ip']; ?></td>
	<td><?php echo $data['passed']; ?></td>
</tr>
<?php);
}

$sql = "SELECT * FROM `".$db_table."`;";
$result = $conn->query($sql);
if(!$result) {
	echo $conn->error;
	die();
}

echo (?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>number</th>
			<th>date</th>
			<th>noti_type</th>
			<th>osu_id</th>
			<th>osu_mode</th>
			<th>twitter_id</th>
			<th>twitter_email</th>
			<th>email_address</th>
			<th>email_verifying_key</th>
			<th>email_verified</th>
			<th>web_ip</th>
			<th>cf_ip</th>
			<th>passed</th>
		</tr>
	</thead>
	<tbody>
<?php);
while($row = $result->fetch_array(MYSQLI_ASSOC))
	$rows[] = $row;
foreach($rows as $row) {
	echo '<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
	table($row);
	echo '<br>';
}
echo (?>
	</tbody>
</table>
<?php)