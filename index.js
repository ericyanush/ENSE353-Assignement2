var setup = function() {
	var submit = document.getElementById("submit");

	var submitHandler = function() {
		
		var subReq = {};

		//Get the contents of the name box
		subReq.name = document.getElementById("name-input").value;
		//Get the contents of the email box
		subReq.email = document.getElementById("email-input").value;

		//Get the state of each publication we want
		// Note our backend has a pretty bad design and needs a request to be made for each new pub sub
		var pubs = document.getElementsByTagName("paper-checkbox");
		for (var i = 0; i < pubs.length; i++) {
			if (pubs[i].checked) {
				subReq["publication_id"] = Number(pubs[i].dataset.pubId);
				(function(reqDat) {
					var xhr = new XMLHttpRequest();
					xhr.open("POST", "/api/subscriptions", true);
					xhr.onload = (function(req) {
							return (function() {
							console.log("Successfuly subscribed to pub: " + req);
							});
					}(reqDat["publication_id"]));
					xhr.send(JSON.stringify(reqDat));
				}(subReq));	
			}
		}
		submit.disabled = true;
	}

	submit.onclick = submitHandler;
}


//Trigger our setup once we are all loaded up
window.onload = setup;