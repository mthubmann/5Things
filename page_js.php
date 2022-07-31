<?php
header('Content-Type: text/javascript');
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
?>

document.addEventListener('DOMContentLoaded', () => {
	console.log("Lite version of JS loaded");
	document.getElementById("inputbox").innerHTML = ' \
		<div class="input-group mb-3"> \
			<span class="input-group-text" id="basic-addon3">Remember This</span> \
			<input type="text" class="form-control" id="inputText" name="inputText" aria-describedby="basic-addon3"> \
			<button class="btn btn-primary" type="submit">Submit</button> \
		</div> \
	';
});