<?php
$ShortTermMem=array("http://localhost/5Things/", "https://www.youtube.com/watch?v=RHzhw97F7n4","Order Number: W882903276","https://github.com/ezSQL/ezsql","http://192.168.0.253/HomeNAS");


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
	echo '		<a href="#" class="btn btn-primary">Remove</a>';
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