<?php
	//version 0.1 https://github.com/mthubmann/5Things
	session_start();
	require 'config.php';
	require 'vendor/autoload.php';
	require_once 'TimeElapsed.php';

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
	if($mode == "dev"){$db->setDebug_Echo_Is_On(true);};
	$db->connect();

	//check the session key to make sure that the logged status is valid.
	
	$db->query_prepared('SELECT * FROM security WHERE param="key"',[]);
	$qryRslt = $db->queryResult();
	//$db->queryResult();
	//$db->debug();
	if(isset($_COOKIE['key']) == false){
		setcookie("key","");
		$_COOKIE['key'] = "";
	};
	if($_SESSION['key'] == $qryRslt[0]->value || $_COOKIE['key'] == $qryRslt[0]->value ){
		$_SESSION['logged'] = true;
		$_SESSION['key'] = $qryRslt[0]->value;
	}
	else{
		$_SESSION['logged'] = false;
		$_SESSION['key'] = "";
		setcookie("key","");
	};
	$qryRslt = null;
// */

	if(isset($_POST['action']) && $_SESSION['rand'] == $_POST['rand']){
		//print_r($_POST);
		//print_r($_SERVER);
		switch($_POST['action']){
			case 'addItem':
				if($_SESSION['logged'] == true){
					$values = [];
					$inText = strip_tags($_POST['inputText']);
					$values['itemtext'] = $inText;
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
				};
			break;
			case 'removeItem':
				if($_SESSION['logged'] == true){
					//echo $_POST['id'];
					$db->query_prepared('UPDATE things SET active=0 WHERE id = ?;',[$_POST['id']]);
					//$db->debug();
				};
				
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
					setcookie('key',$key);
				}
				else{
					//check the login
					$db->query_prepared('SELECT * FROM security WHERE param="PW_hash"',[]);
					$result = $db->queryResult();
					//set the password submitted to blank if too many attempts have been made.
					//if($_SESSION['last_attempt'] > time()-3000){echo "True";};
					if($_SESSION['last_attempt'] < time()-$login_lockout_duration){
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
						setcookie('key',$result[0]->value);
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
				setcookie('key',"");
				break;
			case 'logoutAll':
				$_SESSION['logged'] = false;
				$str = rand();
				$key = hash("sha256", $str);
				$db->query_prepared('UPDATE security SET value=? WHERE param = "key";',[$key]);
				//$_SESSION['key'] = $key;
				$_SESSION['key'] = "";
				setcookie('key',"");
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
	<?php
	if($_SESSION['logged'] == true){
		echo '<script type="text/javascript" src="' . $address . 'page_js.php"></script>';
	}
	else{
		echo '<script type="text/javascript" src="' . $address . 'page_js_lite.js"></script>';
	};
	?>
	
	
	<meta name="5things-Version" content="0.1">
	 <link rel="icon" type="image/x-icon" href="<?php echo $favicon?>">
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
<?php 
	if(($PW_req==true && $_SESSION['logged']==true) || $PW_req==false){
		echo	'<form action="', $address, '" method="POST">';
		echo	'<input type="hidden" name="rand" value="' ,$_SESSION["rand"],'">';
		echo	'<input type="hidden" name="action" value="logout">';
		echo	'<button class="dropdown-item" href="#" type="submit">Logout</button>';
		echo	'</form>';
		echo	'<form action="', $address, '" method="POST">';
		echo	'<input type="hidden" name="rand" value="' ,$_SESSION["rand"],'">';
		echo	'<input type="hidden" name="action" value="logoutAll">';
		echo	'<button class="dropdown-item" href="#" type="submit">Logout All</button>';
		echo	'</form>';
		echo	'<form action="', $address, '" method="POST">';
		echo	'<input type="hidden" name="rand" value="' ,$_SESSION["rand"],'">';
		echo	'<input type="hidden" name="action" value="clearAll">';
		echo	'<button class="dropdown-item" href="#" type="submit">Clear All</button>';
		echo	'</form>';
	};
?>
			
		  </div>
		</div>
		
		
		<!--<li class="nav-item"><a href="#" class="nav-link">Clear</a></li>!-->
	</ul>
	</header>
</div>

<?php echo '	<input type="hidden" name="rand" value="' , $_SESSION['rand'] , '" id="rand">'; ?>

<div class="container" style="max-width: 970px;">
	<div class="row justify-content-center">
		<div class="col">
			<div id="inputbox">
				<div class="input-group mb-3">
				<span class="input-group-text" id="basic-addon3">Wait Wait</span>
				<input type="text" class="form-control" id="inputText" name="inputText" aria-describedby="basic-addon3" disabled>
				<button class="btn btn-primary" type="submit" disabled>Submit</button>
				</div>
			</div>
		
<?php
	//since this is to be handled by the JS files, this is now commented
	
	// if(($PW_req==true && $_SESSION['logged']==true) || $PW_req==false){
		// //serve the input item
		// echo '<form action="' , $address , '" method="POST">';
		// echo '	<input type="hidden" name="action" value="addItem">';
		// echo '	<input type="hidden" name="rand" value="' , $_SESSION['rand'] , '">';
		// echo '	<div class="input-group mb-3">';
		// echo '		<span class="input-group-text" id="basic-addon3">Remember This</span>';
		// echo '		<input type="text" class="form-control" id="inputText" name="inputText" aria-describedby="basic-addon3">';
		// echo '		<button class="btn btn-primary" type="submit">Submit</button>';
		// echo '	</div>';
		// echo '</form>';	
	// }
	// else{
		// echo '<form action="' , $address , '" method="POST">';
		// echo '	<input type="hidden" name="action" value="login">';
		// echo '	<input type="hidden" name="rand" value="' , $_SESSION['rand'] , '">';
		// echo '	<div class="input-group mb-3">';
		// echo '		<span class="input-group-text" id="basic-addon3">Password</span>';
		// echo '		<input type="password" class="form-control" id="PW" name="PW" aria-describedby="basic-addon3">';
		// echo '		<button class="btn btn-primary" type="submit">Submit</button>';
		// echo '	</div>';
		// echo '</form>';	
	// };



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
	$pretty_time = "@" . $ShortTermMem[$ii]['time'];
	echo '		',time_elapsed_string($pretty_time);
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

<script type="text/javascript" src="<?php echo $address;?>jquery.js"></script>
<script type="text/javascript" src="<?php echo $address;?>popper.min.js"></script>
<script type="text/javascript" src="<?php echo $address;?>bootstrap.min.js"></script>!-->
<!--<!-- !-->
<!--
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
<!-- !-->
<script>

if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
</body>
</html>