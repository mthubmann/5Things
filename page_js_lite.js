
document.addEventListener("DOMContentLoaded", () => {
	console.log("Lite version of JS loaded");
	document.getElementById("inputbox").innerHTML = ' \
		<div class="input-group mb-3"> \
			<span class="input-group-text" id="basic-addon3">Password</span> \
			<input type="password" class="form-control" id="PW" name="PW" aria-describedby="basic-addon3"> \
			<button class="btn btn-primary" type="submit" id="PW_submit">Submit</button> \
		</div> \
	';
	document.getElementById("PW_submit").addEventListener("click", ()=>{
		//console.log("Click!");
		pw = document.getElementById('PW').value;
		rand = document.getElementById('rand').value;
		test_data = {'pw':pw,'rand':rand,'req':'login'};
		//test_data = [pw,rand];
		API_Request(test_data)
	});
});

function API_Request(data){
	let xhr = new XMLHttpRequest();
	url = window.location.href + "api.php";
	//console.log(url);
	data = JSON.stringify(data);
	//console.log(data);
	xhr.open("POST", url);

	xhr.setRequestHeader("Accept", "application/json");
	xhr.setRequestHeader("Content-Type", "application/json");

	xhr.onload = () => read_response(xhr.responseText);
	xhr.send(data);
};

function read_response(data){
	response = JSON.parse(data);
	console.log(response);
};