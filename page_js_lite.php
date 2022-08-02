<?php
//since the lite version will only be useed when the user is not logged, then it should only generate the password box...
//header('Content-Type: text/javascript');
$script='
document.addEventListener("DOMContentLoaded", () => {
	console.log("Lite version of JS loaded");
	document.getElementById("inputbox").innerHTML = \' \
		<div class="input-group mb-3"> \
			<span class="input-group-text" id="basic-addon3">Password</span> \
			<input type="password" class="form-control" id="PW" name="PW" aria-describedby="basic-addon3"> \
			<button class="btn btn-primary" type="submit" id="PW_submit">Submit</button> \
		</div> \
	\';
	document.getElementById("PW_submit").addEventListener("click", ()=>{
		console.log("Click!");
		
	});
});
';


$fileName="page_js_lite.js";
file_put_contents($fileName, $script);
?>




