<?php
require 'config.php';
require 'vendor/autoload.php';

use ezsql\Database;

//print_r($db_host_data);
$db = Database::initialize('pdo', [$db_host_data, $db_user, $db_password]);
$db->prepareOn();
$db->setDebug_Echo_Is_On(true);
$db->connect();

$ShortTermMem=array("http://localhost/5Things/", "https://www.youtube.com/watch?v=RHzhw97F7n4","Order Number: W882903276","https://github.com/ezSQL/ezsql","http://192.168.0.253/HomeNAS");
	if(isset($_POST['action'])){
		//print_r($_POST);
		//print_r($_SERVER);
		switch($_POST['action']){
			case 'addItem':
				$values = [];
				$values['itemtext'] = $_POST['inputText'];
				$values['unixtime'] = time();
				$values['ipsubmit'] = $_SERVER['REMOTE_ADDR'];
				if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
					$values['ipforward'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				else{
					$values['ipforward'] = null;
				};
				//print_r($values);
				$db->insert('things', $values);
			break;
		case 'removeItem':
			echo $_POST['id'];
			$db->query_prepared('UPDATE things SET active=0 WHERE id = ?;',[$_POST['id']]);
			//$db->debug();
			break;
	};
};





//echo $_POST['action'];

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="bootstrap.css">
	<script type="text/javascript" src="bootstrap.min.js"></script>
</head>
<body>
<div class="container">
	<header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
	<a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
	<span class="fs-4">Last 5 Things</span>
	</a>

	<ul class="nav nav-pills">
		<li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Home</a></li>
		<li class="nav-item"><a href="#" class="nav-link">Clear</a></li>
	</ul>
	</header>
</div>
  
<div class="container" style="max-width: 970px;">
	<div class="row justify-content-center">
		<div class="col">
			<form action="http://localhost/5Things/" method="POST">
				<input type="hidden" name="action" value="addItem">
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon3">Remember This</span>
					<input type="text" class="form-control" id="inputText" name="inputText" aria-describedby="basic-addon3">
					<button class="btn btn-primary" type="submit">Submit form</button>
					
				</div>
			</form>
			<p class="h1">Here's the last 5 things for you to remember</p>
<?php
for($ii = 0;$ii<count($ShortTermMem);$ii++){
	//echo $ii;
	echo '<div class="card">';
	echo '	<div class="card-header">';
	echo '		Item #' , $ii;
	echo '	</div>';
	echo '	<div class="card-body">';
	echo '		<!--<h5 class="card-title">Card title</h5>!-->';
	echo '		<p class="card-text">' , $ShortTermMem[$ii] , '</p>';
	
	echo '		<form action="http://localhost/5Things/" method="POST">';
	echo '			<input type="hidden" name="action" value="removeItem">';
	echo '			<input type="hidden" name="id" value="' , $ii , '">';
	echo '			<button class="btn btn-primary" type="submit">Remove</button>';
	echo '		</form>';
	
	//echo '		<a href="#" class="btn btn-primary">Remove</a>';
	echo '	</div>';
	echo '	<div class="card-footer text-muted">';
	echo '		5 Minutes Ago';
	echo '	</div>';
	echo '</div>';
	echo '<br>';
};
?>
		</div>
	</div>
</div>

<?php //print_r($ShortTermMem); ?>

<nav class="navbar fixed-bottom navbar-expand-sm navbar-dark bg-dark">
	<div class="container-fluid">
		<a class="navbar-brand" href="#">Last 5 Things</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarCollapse">
			<ul class="navbar-nav">
				<li class="nav-item">
				<a class="nav-link active" aria-current="page" href="https://github.com/mthubmann/5Things">5 Things on GitHub</a>
				</li>

			</ul>
		</div>
	</div>
</nav>
</body>
</html>