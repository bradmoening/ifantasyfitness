<?php
if(!isset($_GET['id']) and !isset($_POST['id'])) header('Location: http://www.ifantasyfitness.com/records');
if(!isset($_COOKIE['iff-id'])) header('Location: http://www.ifantasyfitness.com');
$id = filter_var($_COOKIE['iff-id'], FILTER_SANITIZE_NUMBER_INT);

# Validate the user
include('../../php/db.php');
$check_q = @mysqli_query($db, "SELECT * FROM users WHERE id=$id");
if(mysqli_num_rows($check_q) > 0) {
	$user = mysqli_fetch_array($check_q);
	# confirm with social token
	$valid = false;
	if(isset($_COOKIE['iff-google']) and $_COOKIE['iff-google'] === $user['google']) $valid = true;
	if(isset($_COOKIE['iff-facebook']) and $_COOKIE['iff-facebook'] === $user['facebook']) $valid = true;
	if(!$valid) header('Location: http://www.ifantasyfitness.com');
}

# Grab the record
if(!isset($_GET['id'])) {
	$rid = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
} else {
	$rid = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);
}

$record_fetcher = @mysqli_query($db, "SELECT * FROM records WHERE id=$rid");
if(mysqli_num_rows($record_fetcher) == 0) header('Location: http://www.ifantasyfitness.com/records'); # Record doesn't exist
$record = mysqli_fetch_array($record_fetcher);

# User is valid
$title = 'Edit Record';
$connected = true;
include('../../php/head-auth.php');
?>
<div class="row">
	<div class="col-xs-12">
		<h2>Edit Record</h2>
		<p><a href="/records">&larr; Back to records list</a></p>
		<table class="table">
			<thead>
				<tr>
					<th class="col-xs-6">Activity Type</th>
					<th class="col-xs-3">Duration</th>
					<th class="col-xs-3">Points awarded</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$record_types = array("run" => "Running", "run_team" => "Running at Monument", "rollerski" => "Rollerskiing", "walk" => "Walking", "hike" => "Hiking with packs", "bike" => "Biking", "swim" => "Swimming", "paddle" => "Paddling, Rowing or Kayaking", "strength" => "Strength or core training", "sports" => "Aerobic sports");
				$use_minutes = array('paddle','strength','sports');
				foreach($record_types as $data=>$disp) {
					$points = $data . '_p';
					if($record[$data] != 0) {
						echo '<tr>
						<td>'.$disp.'</td>
						<td>'.round($record[$data],2);
						if(in_array($data, $use_minutes)) {
							echo ' minute';
						} else {
							echo ' mile';
						}
						if($record[$data] != 1) echo 's';
						echo '</td>
						<td>'.round($record[$points],2).'</td>
						</tr>';
					}
				}
				?>
			</tbody>
		</table>
		<?php
		if($record['altitude'] != 1) echo '<p><strong>Altitude bonus awarded:</strong> x'.$record['altitude'].'</p>';
		if(!empty($record['comments'])) echo '<p><strong>Comment:</strong> '.$record['comments'].'</p>';
		echo '<p>Total: '.round($record['total'],2).' point';
		if($record['total'] != 1) echo 's';
		echo '<span class="pull-right">Posted: '.date('F j, Y g:i:s a',$record['timestamp']).'</span></p>';
		?>
		<form method="post">
			<div class="row">
				<div class="col-xs-6">
					<a href="/records" class="btn btn-block btn-default">Cancel</a>
				</div>
				<div class="col-xs-6">
					<input type="submit" value="Save Record" class="btn btn-primary btn-block">
				</div>
			</div>
			<input type="hidden" value="<?=$rid?>" name="id">
			<input type="hidden" name="go" value="<?php echo rand();?>">
		</form>
	</div>
</div>
<?php
include('../../php/foot.php');
?>