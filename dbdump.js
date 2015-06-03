var setup = function() {

	var xhr = new XMLHttpRequest();
	xhr.open("GET", "/api/subscriptions", true);
	xhr.onload = function() {

		var tb = document.getElementsByTagName("tbody")[0];

		//parse the response data
		for (var i = 0; i < this.response.length; i++) {
			var row = document.createElement("tr");
			var name = document.createElement("td");
			name.textContent = this.response[i].name;
			row.appendChild(name);
			var email = document.createElement("td");
			email.textContent = this.response[i].email;
			row.appendChild(email);
			var pub = document.createElement("td");
			if (this.response[i]["publication_id"] == "1") {
				pub.textContent = "Make Magazine";
			}
			else if (this.response[i]["publication_id"] == "2") {
				pub.textContent = "Hackaday Daily Digest";
			}
			else if (this.response[i]["publication_id"] == "3") {
				pub.textContent = "The Onion";
			}
			row.appendChild(pub);
			tb.appendChild(row);
		}
	}
	xhr.responseType = "json";
	xhr.send();
}


//Trigger our setup once we are all loaded up
window.onload = setup;