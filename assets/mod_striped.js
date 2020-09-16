var updateAmount = function (evt) {
	var modid = this.dataset.modid;
	var inputEl = document.getElementById("amount-input"+modid);
	inputEl.value = this.value;
};

function formToJsonString(form) {
	var obj = {};
	var elements = form.querySelectorAll("input");
	for (var i = 0; i < elements.length; ++i) {
		var element = elements[i];
		var name = element.name;
		var value = element.value;

		if (name) {
			obj[name] = value;
		}
	}

	return JSON.stringify(obj);
}

// Create a Checkout Session with the selected amount
var createCheckoutSession = function (modid) {
	var inputEl = document.getElementById("amount-input"+modid);
	var amount = parseInt(inputEl.value);
	var ccdf = document.getElementById("dcdf-" + modid);
	var ccd = formToJsonString(ccdf);

	purl = 'index.php?option=com_ajax&ignoreMessages&module=striped&format=json';
	return fetch(purl, {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({
			amount: amount,
			modid: modid,
			ccd: ccd
		}),
	}).then(function (result) {
	console.log(result);
			return result.json();
		}, function(err){console.log(err);});
	};

// Handle any errors returned from Checkout
var handleResult = function (result) {
	if (result.error) {
		var displayError = document.getElementById("error-message");
		displayError.textContent = result.error.message;
	}
};
