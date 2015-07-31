<?php
	/**
		Скрипт обработки регистрации пользователя
	*/
	require_once("php/model/validation.php"); 	//валидация регистрационных полей
	require_once("php/model/MyDatabase.php");	//класс работы с базой данных mydb
	//Добавления языкового конфига
	session_start();
	if ($_SESSION['en']) {
		require_once("php/language/en.php");
	} else {
		require_once("php/language/ru.php");
	}
	session_write_close();
	
 	$sDirectory = "uploads/img/";	//директория для загрузки пользовательских фотографий
	$sDefaultImage = "no_photo.png";	//картинка по умолчанию
	$formData = $_POST['RegistrationForm'];//данные регистрационной формы
	$myDB = new MyDatabase();		//объект базы данных mydb
	$validForm = null; 				//объект валидации формы
	$result = array('error' => array(), 'email' => "", 'id' => -1);	 //массив с результатом, передается клиенту
	
	//если пользователь ввел страну, то перезаписать в массиве 	
	if (!empty($formData['other_country'])) {
		$formData['other_country'] = Validation::cleanString($formData['other_country']);
		$formData['country'] = $myDB->addCountry($formData['other_country']);
	}
	//если пользователь ввел город, то перезаписать в массиве 	
	if (!empty($formData['other_city'])) {
		$formData['other_city'] = Validation::cleanString($formData['other_city']);
		$formData['city'] = $myDB->addCity($formData['other_city'], $formData['country']);
	}

	$validForm = new Validation($formData);
	//Проверка, найдены ли ошибки в веденных данных
	if ($validForm->isError()) {
		//если найдены, то определяется какое поле было введено неправильно, и это поле в массиве результата устанавливается в true
		$arr = array('Name', 'Email', 'Password', 'Reppassword', 'Gender', 'Country', 'City', 'Birth_day', 'Birth_month', 'Birth_year');
		foreach($arr as $v) {
			$meth = "is".$v;
			$result['error'][lcfirst($v)] = $validForm->$meth();
		}
	} else {
		//если все введно верно, то получаем обработаный массив данных формы
		$res = $validForm->getResult();
		//проверка, есть ли в базе пользователь с такой электронной почтой, 
		if ($myDB->getEmail($res['email'])) {
			$result['email'] = $lang["error"]["same_email"]; 	//если есть, то в результат записываем ошибку
		} else {
			//если фото выбрано, то сохраняем его
			if(isset($_FILES["photo"]['name']) && preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)|(gif)|(GIF)|(png)|(PNG)$/',$_FILES['photo']['name'])) {
				$sFileName = time();
			 	if(preg_match('/[.](GIF)|(gif)$/', $_FILES["photo"]['name'])) {
			    	$sExt = ".gif";
			    }
			    if(preg_match('/[.](PNG)|(png)$/', $_FILES["photo"]['name'])) {
			    	$sExt = ".png";
			    }  
			    if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/', $_FILES["photo"]['name'])) {
			    	$sExt = ".jpg"; 
			    }
			    move_uploaded_file($_FILES['photo']['tmp_name'], $sDirectory.$sFileName.$sExt);
			    $res['photo'] = $sDirectory.$sFileName.$sExt;
			} else {
			    $res['photo'] = $sDirectory.$sDefaultImage;
			}
			//запись в БД информации о пользователе
			$myDB->insert($res);
			//получение ИД пользователя и запись в результирующий массив
			$result['id'] = $myDB->getId($res['email']);
			//Сохранение в сессии текущего пользователя
			session_start();
			$_SESSION['id'] = $result['id'];
			session_write_close();
		}
	}
	//отправка клиенту результирующий массив преобразованный в JSON
	echo json_encode($result); 
?>