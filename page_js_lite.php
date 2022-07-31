<?php
//since the lite version will only be useed when the user is not logged, then it should only generate the password box...


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
header('Content-Type: text/javascript');
//$fileName="page_js_lite.js";
//file_put_contents($fileName, $script);
?>


document.addEventListener('DOMContentLoaded', () => {
	console.log("Lite version of JS loaded");
	document.getElementById("inputbox").innerHTML = ' \
		<div class="input-group mb-3"> \
			<span class="input-group-text" id="basic-addon3">Password</span> \
			<input type="password" class="form-control" id="PW" name="PW" aria-describedby="basic-addon3"> \
			<button class="btn btn-primary" type="submit">Submit</button> \
		</div> \
	';
});

