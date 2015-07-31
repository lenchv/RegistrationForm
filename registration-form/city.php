<?php
	/**
		Скрипт получает ИД страны, и возвращает список городов в виде элементов выпадающего списка select
	*/
	require_once("php/model/MyDatabase.php");	//класс для работы с базой mydb
	$myDB = new MyDatabase();
	//проверяем верно ли передан id страны
	if (isset($_POST['country']) && filter_var($_POST['country'], FILTER_VALIDATE_INT) && intval($_POST['country']) > 0 ) {
		//если верно, то получаем список городов в этой стране
		$cities = $myDB->getCityList($_POST['country']);
		//и отправляем на клиент в виде элементов выпадающего списка города
		foreach ($cities as $id => $value) {
			echo "<option value='$id'>$value</option>\n";			
		}
	}
?>