<?php
	session_start();
	require 'config.php';
	require 'vendor/autoload.php';

	use ezsql\Database;

	//print_r($db_host_data);
	$db = Database::initialize('pdo', [$db_host_data, $db_user, $db_password]);
	$db->prepareOn();
	$db->setDebug_Echo_Is_On(true);
	$db->connect();

	if(isset($_POST['action']) && $_SESSION['rand'] == $_POST['rand']){
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
		case 'login':
			$db->query_prepared('SELECT * FROM security WHERE param="PW_hash"',[]);
			$result = $db->queryResult();
			if($result[0]->value == 'null'){
				//do something here to create the new password...
			}
			else{
				//check the login
			};
			break;
		};
	};
	
	
	$db->query_prepared('SELECT * FROM things WHERE active=1 ORDER BY id DESC LIMIT 5',[]);
	$result = $db->queryResult();
	//$db->debug();
	//print_r($result);
	$ShortTermMem = [];
	//echo count($result);
	//if(count($result) <> 0){
	if(is_array($result)){
		foreach ($result as $row) {
			array_push($ShortTermMem,['id'=>$row->id,'text'=>$row->itemtext,'time'=>$row->unixtime]);
		};
	};
	//print_r($ShortTermMem);





//echo $_POST['action'];
$_SESSION['rand'] = rand();
if(isset($_SESSION['logged']) == false){$_SESSION['logged'] = false;};
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
	<image src="<?php echo $logo;?>" height="32">
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
			
			
<?php
	if(($PW_req==true && $_SESSION['logged']==true) || $PW_req==false){
		//serve the input item
		echo '<form action="http://localhost/5Things/" method="POST">';
		echo '	<input type="hidden" name="action" value="addItem">';
		echo '	<input type="hidden" name="rand" value="' , $_SESSION['rand'] , '">';
		echo '	<div class="input-group mb-3">';
		echo '		<span class="input-group-text" id="basic-addon3">Remember This</span>';
		echo '		<input type="text" class="form-control" id="inputText" name="inputText" aria-describedby="basic-addon3">';
		echo '		<button class="btn btn-primary" type="submit">Submit form</button>';
		echo '	</div>';
		echo '</form>';	
	}
	else{
		echo '<form action="http://localhost/5Things/" method="POST">';
		echo '	<input type="hidden" name="action" value="login">';
		echo '	<input type="hidden" name="rand" value="' , $_SESSION['rand'] , '">';
		echo '	<div class="input-group mb-3">';
		echo '		<span class="input-group-text" id="basic-addon3">Password</span>';
		echo '		<input type="text" class="form-control" id="PW" name="PW" aria-describedby="basic-addon3">';
		echo '		<button class="btn btn-primary" type="submit">Submit form</button>';
		echo '	</div>';
		echo '</form>';	
	};



?>

			
			
			
			<p class="h1">Here's the last 5 things for you to remember</p>
<?php
for($ii = 0;$ii<count($ShortTermMem);$ii++){
	//echo $ii;
	echo '<div class="card">';
	echo '	<div class="card-header">';
	echo '		Item #' , $ii+1;
	echo '	</div>';
	echo '	<div class="card-body">';
	echo '		<!--<h5 class="card-title">Card title</h5>!-->';
	echo '		<p class="card-text">' , $ShortTermMem[$ii]['text'] , '</p>';
	
	echo '		<form action="http://localhost/5Things/" method="POST">';
	echo '			<input type="hidden" name="action" value="removeItem">';
	echo '			<input type="hidden" name="id" value="' , $ShortTermMem[$ii]['id'] , '">';
	echo '			<button class="btn btn-primary" type="submit">Remove</button>';
	echo '		</form>';
	
	//echo '		<a href="#" class="btn btn-primary">Remove</a>';
	echo '	</div>';
	echo '	<div class="card-footer text-muted">';
	echo '		',time()-$ShortTermMem[$ii]['time'],' Seconds Ago';
	echo '	</div>';
	echo '</div>';
	echo '<br>';
};
?>
		</div>
	</div>
</div>
<br><br>
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
				<li class="nav-item">
					<a class="nav-link" ><?php echo $address;?> &copy; <?php echo date("Y");?> </a>
				</li>
			</ul>
		</div>
	</div>
</nav>
</body>
</html>