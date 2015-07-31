document.addEventListener("DOMContentLoaded", function() {
	
	/*******************************************/
	/*  Переключение табов                     */
	document.querySelector(".nav-tabs").addEventListener("click", 
		function(e) {
			e = e || window.event;
			var elem = e.target.parentNode;
			if (!elem.classList.contains("active")) {
				var children = elem.parentNode.children;
				for (var i = 0; i < children.length; i++) {
					children[i].classList.remove("active");
					document.getElementById("tab-"+(i+1)).style.display = 'none';
					if (elem === children[i]) {
						document.getElementById("tab-"+(i+1)).style.display = 'block';
					}
				}
				elem.classList.add("active");
			}
		}, 
		false);
	/*******************************************/
	
	/*******************************************/
	/********       Верификация        *********/
	
	/** Добавляет ошибку
	*	element - элемент к которому добавляется ошибка
	* error - текст ошибки
	*/ 
	var addError = function(element, error) {
		element.classList.add("error");
		element.setAttribute("data-error", error);
	}
	/** Удаляет ошибку с элемента
	* element - элемент с которого необходимо убрать ошибку
	*/
	var removeError = function(element) {
		element.classList.remove("error");
		element.removeAttribute("data-error");
	}
	/**
	* Обработчик верификации ввода имени
	*/
	var errorHandlerName = function(e) {
		e = e || window.event;
		var error = lang.error.name;
	
		if (e.target.value === "") {
			addError(e.target.parentNode, error[0]);
		} else if (/[^a-zA-Zа-яА-ЯЁё\x20]/.test(e.target.value)) {
			addError(e.target.parentNode, error[1])
		} else if (e.target.value.length < 2) {
			addError(e.target.parentNode, error[2]);
		} else {
			removeError(e.target.parentNode);
		}
	}
	/**
	* Обработчик верификации ввода почты
	*/
	var errorHandlerEmail = function(e) {
		e = e || window.event;
		var error = lang.error.email;
		
		var regexp = /[a-z0-9!$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum|ua|com.ua|ru|com.ru)\b/;
		if (e.target.value === "") {
			addError(e.target.parentNode, error[1]);
		} else if (!regexp.test(e.target.value)) {
			addError(e.target.parentNode, error[0]);
		} else {
			removeError(e.target.parentNode);
		}
	}

	/**
	* Обработчик верификации ввода пароля
	*/
	var errorHandlerPassword = function(e) {
		e = e || window.event;
		var error = lang.error.password;

		var regexp = /[^a-zA-Z0-9\_\-\.]/;
		if (e.target.value === "") {
			addError(e.target.parentNode, error[1]);
		} else if (regexp.test(e.target.value)) {
			addError(e.target.parentNode, error[0]);
		} else if (e.target.value.length < 6 ) {
			addError(e.target.parentNode, error[2]);
		} else {
			removeError(e.target.parentNode);
		}
	}

	var errorHandlerSelect = function(e) {
			if (e.target.value == "") {
				addError(e.target.parentNode, "");
			} else {
				removeError(e.target.parentNode);
			}
	};

	document.getElementById('RegistrationForm_name').addEventListener("blur", errorHandlerName, false);	
	document.getElementById('RegistrationForm_email').addEventListener("blur", errorHandlerEmail, false);	
	document.getElementById('RegistrationForm_password').addEventListener("blur", errorHandlerPassword, false);	
	document.getElementById('RegistrationForm_reppassword').addEventListener("blur", function(e) {
		var fstPass = document.getElementById('RegistrationForm_password').value;
		if (e.target.value !== fstPass) {
			addError(e.target.parentNode, lang.error.reppassword[0]);
		} else {
			removeError(e.target.parentNode);
		}
	}, false);	

	document.getElementById('RegistrationForm_country').addEventListener("blur", errorHandlerSelect, false);	
	document.getElementById('RegistrationForm_day').addEventListener("blur", errorHandlerSelect, false);	
	document.getElementById('RegistrationForm_month').addEventListener("blur", errorHandlerSelect, false);	
	document.getElementById('RegistrationForm_year').addEventListener("blur", errorHandlerSelect, false);	

	/*******************************************/
	/******* Переключатель пола ****************/
	document.querySelector(".gender-switch").addEventListener("click", function(e) {
		e = e || window.event;
		var target = (e.target.childElementCount > 0)? e.target : e.target.parentNode;
		var curRadio = target.previousElementSibling;
		var anyRadio = target.nextElementSibling || e.target.parentNode.firstElementChild;
		if (!curRadio.checked) {
			curRadio.checked = true;
			anyRadio.checked = false;	
		}
	}, false);
	/*******************************************/

	/*******************************************/
	/**********  Загрузка фотографии  **********/
	var selectPhoto = null;

	var uploadImageHandler = function(e) {
		var fileReader = new FileReader();
		var image = e.currentTarget.files || e.dataTransfer.files;
		if (image.length < 1) return;
		selectPhoto = image[0];			//Сохраняем фото для отправки на сервер
		var el = (this.id === "RegistrationForm_photo")? this.parentNode : this;
		fileReader.onload = (function(imageEl) {
			return function(e) {
				var sibl = imageEl.nextElementSibling;
				imageEl.style.display = "none";
				sibl.style.display = 'block';
				sibl.style.background = "url("+this.result+")";
				sibl.style.backgroundSize = 'cover';
			}
		})(el);
		fileReader.readAsDataURL(image[0]);
	}
	document.querySelector(".upload-label").addEventListener("drop", uploadImageHandler, false);
	document.querySelector(".upload-label #RegistrationForm_photo").addEventListener("change", uploadImageHandler, false);
	
	document.querySelector(".close").addEventListener("click", function(e) {
		this.parentNode.style.background = "none";
		this.parentNode.style.display = "none";
		this.parentNode.previousSibling.previousSibling.style.display = "block";
		selectPhoto = null;
	}, false);
	/*******************************************/
	/****** Подгрузка городов ******************/
	
	/** Функция, которая добавляет в element прелоадер
	*	element - элемент, в который добавляется прелоадер
	*	return - id setInterval для очистки таймера  
	*/
	var addPreloader = function(element) {
		var pLoader = document.createElement("div");
		pLoader.className = "preloader";
		element.appendChild(pLoader);
		var deg = 0;
		return setInterval(function() {
			if (deg < -360) deg = 0;
			deg-=10;
			pLoader.style.cssText = "transform: rotate("+deg+"deg);";
		}, 50);
	}
	/** Функция удаляет прелоадер с element
	*	element - элемент с прелоадером
	* intervalId - идентификатор setInterval для очистки таймера
	*/
	var removePreloader = function(element, intervalId) {
		clearInterval(intervalId);
		element.removeChild(element.querySelector(".preloader"));
	}
	/** Очищает элемент от дочерних узлов
	*	element - элемент, который необходимо очистить
	*/
	var clearElement = function(element) {
		while(element.firstChild) {
			element.removeChild(element.firstChild);
		}
	}

	document.getElementById("RegistrationForm_country").addEventListener("change", function(e) {
		if (this.value === "other") {
			var otherCountry = this.parentNode.parentNode.nextElementSibling;
			otherCountry.style.display = "block";
			otherCountry.nextElementSibling.style.display = "none";
			otherCountry.nextElementSibling.nextElementSibling.style.display = "block";
		} else {	
			var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
			var xhr = new XHR();
			var nextFormBox = this.parentNode.parentNode.nextElementSibling.nextElementSibling;
			nextFormBox.children[nextFormBox.children.length-1].firstElementChild.style.display = "none";
			var idInterval = addPreloader(nextFormBox);
			
			nextFormBox.previousElementSibling.style.display = "none";
			nextFormBox.previousElementSibling.lastElementChild.firstElementChild.value = "";
			nextFormBox.nextElementSibling.style.display = "none";
			nextFormBox.nextElementSibling.lastElementChild.firstElementChild.value = "";
			nextFormBox.style.display = "block";
			xhr.onload = (function (element, id) {
				return function(e) {
					removePreloader(element, id);
					if (e.target.status == 200) {
						element.style.display = "block";
						var selectCity = element.children[element.children.length-1].firstElementChild;
						selectCity.style.display = "block";
						clearElement(selectCity);
						selectCity.insertAdjacentHTML('beforeEnd', "<option disabled selected value=''>"+lang.city+"</option>"+e.target.responseText);
						selectCity.insertAdjacentHTML('beforeEnd', "<option value='other'>"+lang.other_city+"</option>");
						selectCity.onblur = errorHandlerSelect;
					}
				};
			})(nextFormBox, idInterval);

			xhr.open("POST", "city.php", true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.send("country="+encodeURIComponent(this.value));
		}
	}, false);
	
	document.getElementById("RegistrationForm_city").addEventListener("change", function(e) {
		var nextFormBox = this.parentNode.parentNode.nextElementSibling;	
		if (this.value === "other") {		
			nextFormBox.style.display = "block";
		} else {
			nextFormBox.style.display = "none";
			nextFormBox.lastElementChild.firstElementChild.value = "";
		}
	}, false);
	/*******************************************/
	/*******************************************/
	/*******	Регистрация				****************/
	document.forms.RegistrationForm.onsubmit = function(e) {
		(e.preventDefault)? e.preventDefault() : e.returnValue = false;
		
		var keys = [
			"RegistrationForm_name",
			"RegistrationForm_email",
			"RegistrationForm_password",
			"RegistrationForm_reppassword",
			"RegistrationForm_country",
			"RegistrationForm_city",
			"RegistrationForm_day",
			"RegistrationForm_month",
			"RegistrationForm_year"
		];
		for (var i = 0; i < keys.length; i++) {
			if (this.elements[keys[i]]) {
				this.elements[keys[i]].focus();
				this.elements[keys[i]].blur();			
			}
		}
		if (!this.querySelector(".error")) {
			var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
			var xhr = new XHR();
			var body = new FormData(this);
			body.append("photo", selectPhoto);
			
			var idInterval = addPreloader(this);
			var thisForm = this;
			
			xhr.upload.onprogress = function(e) {
				var loaded = (e.loaded / e.total)*100;
				document.querySelector(".loader").style.width = loaded+"%";
				console.log(e.loaded+"/"+e.total);
			}

			xhr.onload = (function(myForm, id) {
				return function(e) {
					removePreloader(myForm, id);
					console.log(e.target.responseText);
					var result = JSON.parse(e.target.responseText);
					
					for (var key in result['error']) {
						var tmp = myForm.elements.namedItem("RegistrationForm["+key+"]"); 
						if (tmp instanceof RadioNodeList) {
							tmp = tmp[0];
						}
						if (result['error'][key]) {
							addError(tmp.parentNode, "");
						} else {
							removeError(tmp.parentNode);
						}
					}

					if (result['email']) {
						addModalError(myForm,result['email']);
					}

					if (result['id'] > 0) {
						window.location.replace("?enter");
					}
 				}
			})(thisForm, idInterval);

			xhr.open("POST", "register.php", true);
			xhr.send(body);
		}
	};

	/** Функция добавляет модальное окно на 2 секунды в element с надписью error
	* 750ms - время появления окна
	*/
	var addModalError = function(element, error) {
		var div = document.createElement("div");
		div.className = "modal-error";
		div.style.opacity = 0;
		div.innerHTML = error;
		element.appendChild(div);
		setTimeout(function() {
			div.style.opacity = 1;}, 10);
		
		setTimeout((function(div) {
			return function() {
				div.style.opacity = 0;
		}})(div), 2750);
		div.addEventListener("transitionend", function(e) {
			if (this.style.opacity == 0) {
				this.parentNode.removeChild(this);
			}
		}, false);
	};
	/*******************************************/

	/*******************************************/
	/*****   Авторизация      ******************/
	document.getElementById('AuthorizationForm_email').onchange = errorHandlerEmail;
	
	document.forms.AuthorizationForm.onsubmit = function (e) {
		(e.preventDefault)? e.preventDefault() : e.returnValue = false;
		if (!this.querySelector(".error")) {
			var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
			var xhr = new XHR();
			var body = new FormData(this);
			var intervalId = addPreloader(this);
			xhr.onload = (function(myForm, id) {
				return function (e) {
					removePreloader(myForm, intervalId);
					var result = JSON.parse(this.responseText);
					if (result['error']) {
						addModalError(myForm, result['error']);
					} else {
						window.location.replace("?enter");
					}
				};
			})(this, intervalId);

			xhr.open("POST", "auth.php", true);
			xhr.send(body);
		}
	};
	/*******************************************/
}, false);

