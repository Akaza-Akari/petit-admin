<?php
ini_set("memory_limit" , -1);
$config = require_once 'config.php';
require('osu-framework/include.php');

$osu = new OsuTournament\Check();

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

if($_POST['number']) {
	$sql = 'SELECT * FROM `'.$config['db_table'].'` WHERE `number` = '.$_POST['number'];
	$first = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
	if(!$first) {
		echo $conn->error;
		die();
	}

	$first['passed'] ?
		$sql = 'UPDATE `'.$config['db_table'].'` SET `passed` = \'0\' WHERE `'.$config['db_table'].'`.`number` = '.$_POST['number'] :
		$sql = 'UPDATE `'.$config['db_table'].'` SET `passed` = \'1\' WHERE `'.$config['db_table'].'`.`number` = '.$_POST['number']; 
	$after = $conn->query($sql);
	if(!$after) {
		echo $conn->error;
		die();
	}
	$passed_status = (int) !$first['passed'];
	$passed_status ? $passed_status = 'true' : $passed_status = 'false';
	echo('{ "data" : "'.$passed_status.'" }');
	die();
}

function changeFalse($data, $value) {
	return $data ? $data : $value;
}

function osuModeFancy($modeInt) {
	switch($modeInt) {
		case(0):
			return 'osu!';
			break;
		case(1):
			return 'Taiko';
			break;
		case(2):
			return 'CatchTheBeat';
			break;
		case(3):
			return 'osu!mania';
			break;
		default:
			return 'Not Defined Mode';
			break;
	}
}

function getData($username, $mode) {
	global $osu;

	$osu->CheckUser($username, $mode);
	isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $cf = $_SERVER['HTTP_CF_CONNECTING_IP'] : $cf = false;
	return(array(
		'pc' => $osu->Playcount,
		'pp' => $osu->Performance,
		'rank' => $osu->Rank,
		'need_set' => $osu->Occupation_Set,
		'now_set' => $osu->Occupation,
		'username' => $osu->RealID,
		'id' => $osu->OsuID,
		'mode' => osuModeFancy($mode),
		'mode_code' => $mode,
		'ip' => $_SERVER['REMOTE_ADDR'],
		'cf' => $cf,
		)
	);
}

function notitypeFancy($noti_type) {
	switch($noti_type) {
		case 'twitter':
			return 'Twitter';
			break;
		default:
			return 'Undefined Notification Type : '.$noti_type;
			break;
	}
}

function obfuscate_email($email) {
	$em   = explode('@',$email);
	$name = implode(array_slice($em, 0, count($em)-1), '@');
	//$len  = floor(strlen($name)/2);
	$len = 3;

	return substr($name, 0, -3).str_repeat('*', $len).'@'.end($em);
}

function table($data, $osu_data) { ?>
<tr>
	<td><?php echo $data['number']; ?></td>
	<td><?php echo $data['noti_type']; ?></td>
	<td><?php echo $osu_data['username']; ?></td>
	<td><?php echo osuModeFancy($data['osu_mode']); ?></td>
	<td><?php echo $data['twitter_id']; ?></td>
	<td><?php echo obfuscate_email($data['twitter_email']); ?></td>
	<td><?php echo changeFalse($data['web_ip'], 'No IP Data'); ?></td>
	<td><?php echo changeFalse($data['cf_ip'], 'Not Connected with CloudFlare'); ?></td>
	<td id="data-num<?php echo $data['number']; ?>"><?php echo $data['passed'] ? 'true' : 'false'; ?></td>
	<td><a href="javascript:ajaxPost(<?php echo $data['number']; ?>)"<button type="button" class="btn btn-default">revert</button></td>
</tr>
<?php }

$sql = 'SELECT * FROM `'.$db_table.'`;';
$result = $conn->query($sql);
if(!$result) {
	echo $conn->error;
	die();
}

?>
<style>
	table {
		overflow: scroll;
	}
</style>
<script>
	function updatePassed(number, json) {
		//$.get(window.location.href, function(data) {
			$("#data-num"+number).html(json.data.toString());
		//});
	}

	function ajaxPost(number){
		$.ajax({
			type: 'post',
			url: '/',
			data: { 'number' : number },
			dataType: 'json',
			error: function(xhr, status, error){
				alert(error);
			},
			success: function(json){
				updatePassed(number, json);
			},
		});
	}
</script>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Count</th>
			<th>Notification Type</th>
			<th>osu!username</th>
			<th>osu!mode</th>
			<th>Twitter ID</th>
			<th>Twitter Email</th>
			<th>Web IP</th>
			<th>CloudFlare IP</th>
			<th>Passed</th>
			<th>Passed Change</th>
		</tr>
	</thead>
	<tbody>
<?php
while($row = $result->fetch_array(MYSQLI_ASSOC))
	$rows[] = $row;
foreach($rows as $row) {
	echo '<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
	$osu_data = getData($row['osu_id'], $row['osu_mode']);
	table($row, $osu_data);
	echo '<br>';
}
?>
	</tbody>
</table>
