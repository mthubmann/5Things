<?php
	session_start();
	require 'config.php';
	require 'vendor/autoload.php';

	use ezsql\Database;
	//print_r($_SESSION);
	if(!isset($_SESSION['login_attempt'])){$_SESSION['login_attempt'] = 0;};
	if(!isset($_SESSION['last_attempt'])){$_SESSION['last_attempt'] = 0;};
	if(!isset($_SESSION['rand'])){$_SESSION['login_attempt'] = 0;};
	if(!isset($_SESSION['lockout'])){$_SESSION['lockout'] = false;};
	if(!isset($_SESSION['key'])){$_SESSION['key'] = "";};
	//print_r($_SESSION);
	//print_r($db_host_data);
	$db = Database::initialize('pdo', [$db_host_data, $db_user, $db_password]);
	$db->prepareOn();
	$db->setDebug_Echo_Is_On(true);
	$db->connect();

	//check the session key to make sure that the logged status is valid.
	
	$db->query_prepared('SELECT * FROM security WHERE param="key"',[]);
	$qryRslt = $db->queryResult();
	//$db->queryResult();
	//$db->debug();
	if($_SESSION['key'] <> $qryRslt[0]->value){
		$_SESSION['logged'] = false;
		$_SESSION['key'] = "";
	};
	$qryRslt = null;
// */

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
				$newPW = password_hash($_POST['PW'], PASSWORD_DEFAULT);
				//echo $newPW;
				$db->query_prepared('UPDATE security SET value=? WHERE param = "PW_hash";',[$newPW]);
				//$db->queryResult();
				//$db->debug();
				//generate the key variable
				$str = rand();
				$key = hash("sha256", $str);
				$db->query_prepared('UPDATE security SET value=? WHERE param = "key";',[$key]);
				$_SESSION['key'] = $key;
				$_SESSION['logged'] = true;
			}
			else{
				//check the login
				$db->query_prepared('SELECT * FROM security WHERE param="PW_hash"',[]);
				$result = $db->queryResult();
				//set the password submitted to blank if too many attempts have been made.
				//if($_SESSION['last_attempt'] > time()-3000){echo "True";};
				if($_SESSION['last_attempt'] < time()-300){
					//resets the login counter after 5 minutes
					//echo time()-300;
					$_SESSION['login_attempt'] = 0;
					$_SESSION['lockout'] = false;
				};
				
				if(isset($_SESSION['lockout']) && $_SESSION['lockout'] == true){$_POST['PW'] = "";};
				if(password_verify($_POST['PW'],$result[0]->value)){
					$db->query_prepared('SELECT * FROM security WHERE param="key"',[]);
					$result = $db->queryResult();
					$_SESSION['key'] = $result[0]->value;
					$_SESSION['logged'] = true;
				}
				else{
					$_SESSION['logged'] = false;
					if(!isset($_SESSION['login_attempt'])){$_SESSION['login_attempt'] = 1;};
					$_SESSION['last_attempt'] = time();
					$_SESSION['login_attempt'] = $_SESSION['login_attempt'] + 1;
					echo $_SESSION['login_attempt'];
					if($_SESSION['login_attempt'] >=$login_attempt_limit){
						$_SESSION['lockout'] = true;
					};
				};
			};
			break;
		case 'logout':
			$_SESSION['logged'] = false;
			$_SESSION['key'] = "";
			break;
		case 'clearAll':
			if($_SESSION['logged'] == true){
				$db->query_prepared('UPDATE things SET active=0 ',[]);
			};
			break;
		};
	};
	
	$result = null;
	$row = null;
	//$db->debug();
	//$db->query_prepared('SELECT * FROM things',[]);//maybe this will work?
	//$db->debug();
	$result = null;
	$row = null;
	$db->query_prepared('SELECT * FROM things WHERE active=1 ORDER BY id DESC LIMIT 5',[]);
	//$db->debug();
	$result = $db->queryResult();
	//print_r($result);
	if(isset($result[0]->value)){$result = [];};
	$ShortTermMem = [];
	//echo count($result);
	//if(count($result) <> 0){
	if(is_array($result)){
		foreach ($result as $row) {
			//print_r($row);
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
	
	
</head>
<body>
<div class="container">
	<header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
	<a href="<?php echo $address;?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
	<image src="<?php echo $logo;?>" height="32">
	<span class="fs-4">Last 5 Things</span>
	</a>

	<ul class="nav nav-pills">
		<div class="dropdown">
		  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Menu
		  </button>
		  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
			<form action="<?php echo $address;?>" method="POST">
			<input type="hidden" name="rand" value="<?php echo $_SESSION['rand'];?>">
			<input type="hidden" name="action" value="logout">
			<button class="dropdown-item" href="#" type="submit">Logout</button>
			</form>
			<form action="<?php echo $address;?>" method="POST">
			<input type="hidden" name="rand" value="<?php echo $_SESSION['rand'];?>">
			<input type="hidden" name="action" value="clearAll">
			<button class="dropdown-item" href="#" type="submit">Clear All</button>
			</form>
		  </div>
		</div>
		
		
		<!--<li class="nav-item"><a href="#" class="nav-link">Clear</a></li>!-->
	</ul>
	</header>
</div>
  
<div class="container" style="max-width: 970px;">
	<div class="row justify-content-center">
		<div class="col">
			
			
<?php
	if(($PW_req==true && $_SESSION['logged']==true) || $PW_req==false){
		//serve the input item
		echo '<form action="' , $address , '" method="POST">';
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
		echo '<form action="' , $address , '" method="POST">';
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
	
	echo '		<form action="' , $address , '" method="POST">';
	echo '			<input type="hidden" name="action" value="removeItem">';
	echo '			<input type="hidden" name="rand" value="' , $_SESSION['rand'] , '">';
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
					<a class="nav-link" ><?php echo $disp_address;?> &copy; <?php echo date("Y");?> </a>
				</li>
			</ul>
		</div>
	</div>
</nav>
<!--<script type="text/javascript" src="jquery.js"></script>!-->
<!--<script type="text/javascript" src="bootstrap.bundle.min.js"></script>!-->

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>