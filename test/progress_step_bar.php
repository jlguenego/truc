<?php
	$steps = array('petit', 'moins petit', 'text____ normal', 'gros_____', 'step5');
	$current = 0;
	if (isset($_POST['step'])) {
		$current = $_POST['step'];
	}
	if ($current < 0) {
		$current = 0;
	}
	if ($current >= count($steps)) {
		$current = count($steps) - 1;
	}

	$progress = <<<EOF
<ul class="track">
EOF;
	$i = 0;
	for ($i; $i < $current; $i++) {
		$step = $steps[$i];
		$progress .= '<li class="track_done">'.$step.'</li>';
	}
	$step = $steps[$i];
	$progress .= '<li class="track_current">'.$step.'</li>';
	$i++;
	for ($i; $i < count($steps); $i++) {
		$step = $steps[$i];
		$progress .= '<li class="track_todo">'.$step.'</li>';
	}
	$progress .= <<<EOF
</ul>
EOF;
?>

<html>
	<head>
		<style>
.track {
	display: inline;
	margin: 0px;
	padding: 0px;
}

.track li {
	position: relative;
	display: inline-block;
	margin: 0px;
	padding: 10px 20px;
	width: 100px;
	text-align: center;
}

.track_done {
	color: #838383;
	border-top: 1px solid #838383;
}

.track_current {
	color: #00C600;
	border-top: 1px solid #00C600;
}

.track li:before {
	position: absolute;
	top: -20px;
	left: 60px;
	content: "\25cf";
	font-size: 30px;
	font-family: arial;
}

.track_todo {
	color: #EEEEEE;
	border-top: 1px solid #EEEEEE;
}
		</style>
	</head>
	<body>
		<br/><br/><br/><br/>
		<?php echo $progress; ?>
		<form action="" method="post">
			<input type="hidden" name="step" value="<?php echo ($current-1)?>" />
			<input type="submit" value="Back" />
		</form>

		<form action="" method="post">
			<input type="hidden" name="step" value="<?php echo ($current+1)?>" />
			<input type="submit" value="Next" />
		</form>
	</body>
</html>