<?php
require_once("php/model/MyDatabase.php");

session_start();
//Если был запрос на выход то из сессии удаляется ИД пользователя
if (isset($_GET['exit'])) {
	unset($_SESSION['id']);
}
//Выбираем какой языковой файл подключать
if (isset($_GET['en']) && $_GET['en']) {
	$_SESSION['en'] = true;
	include("php/language/en.php");		
} else if (isset($_GET['ru']) && $_GET['ru']){
	$_SESSION['en'] = false;
	include("php/language/ru.php");	
} else if (isset($_SESSION['en']) && $_SESSION['en']) { 
	include("php/language/en.php");
} else {
	$_SESSION['en'] = false;
	include("php/language/ru.php");	
}
session_write_close();

include("php/view/header.php");				//заголовочный файл
include("php/view/lang_switch.php");		//переключатель языка
//Если в сессии установлен ИД пользовтаеля то
if (isset($_SESSION['id'])) {
	include("php/view/user.php");			//загружаем страницу пользователя
} else {
	include("php/view/registration_form.php");	//иначе регистрационную форму
}
include("php/view/footer.php");				//подвла сайта
?>