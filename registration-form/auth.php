<?php
	/**
		Скрипт авторизации пользователя
	*/
	require_once("php/model/MyDatabase.php");//класс базы данных
	require_once("php/model/validation.php");//класс валидации
	//Подключаем языковой файл
	session_start();
	if ($_SESSION['en']) {
		require_once("php/language/en.php");
	} else {
		require_once("php/language/ru.php");
	}
	session_write_close();

	$oMyDB = new MyDatabase();					//объект базы данных
	$aFormData = $_POST['AuthorizationForm'];	//данные формы авторизации
	$aResult = array("error" => "", 'id' => -1);//массив результата
	//проверяем правильно ли введена почта
	if ( !empty($aFormData['email']) && filter_var($aFormData['email'], FILTER_VALIDATE_EMAIL)) {
		$aFormData['email'] = Validation::cleanString($aFormData['email']);//убираем лишнее из строки эл. почты
		$data = $oMyDB->getAuthData($aFormData['email']);//из базы данных получаем по эл.почте пароль и id
		//если ничего не получили значит такой пользователь не зарегистрирован
		if ($data != NULL) {
			//хешируем введенный пароль
			$password = md5(sha1($aFormData['password']));
			//сравниваем хеш из базы данных с хешем введенного пароля
			if (!strcmp($password, $data['password'])) {
				//если хеши совпадают, то
				session_start();
				$_SESSION['id'] = $data['id'];	//сохраняем ИД в сессии
				$aResult['id'] = $data['id'];	//передаем ИД клиенту
				session_write_close();
			} else {
				$aResult['error'] = $lang['auth']['error']['password'];	
			}
		} else {
			$aResult['error'] = $lang['auth']['error']['not_found_email'];			
		}
	} else {
		$aResult['error'] = $lang['auth']['error']['wrong_email'];
	}
	// передача клиенту результата в виде JSON
	echo json_encode($aResult);

?>