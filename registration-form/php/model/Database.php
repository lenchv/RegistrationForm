<?php
	/**
	* Абстрактный класс для работы с базой данных
	*/
	abstract class Database {
		
		protected $oDB;

		function __construct($sDBName, $sHost = "localhost", $sUser = "root", $sPassword = "") {
			try {
				$opt = array(
				    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);
				$this->oDB = new PDO("mysql:host=$sHost; dbname=$sDBName", $sUser, $sPassword, $opt);
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}
		}

		protected function log($sError) {
				file_put_contents("log_db.txt", $sError, FILE_APPEND);
		}

		abstract function insert($aParams);

		abstract function getField($id);
	}
?>