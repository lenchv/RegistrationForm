<?php
	/**
		Класс для работы с базой данных mydb
	*/
	require_once("php/model/Database.php");
	class MyDatabase extends Database {
		// Подключаемся к базе данных
		function __construct($sHost = "localhost", $sUser = "root", $sPassword = "") {
			parent::__construct("mydb", $sHost, $sUser, $sPassword);
		}
		/** 
		* 	Метод заполняет поле в таблице users регистрационными данными из aParams
		*	aParams - ассоциативный массив с регистрационными данными
		*	ключи - соответствуют названиям столбцов в тблице
		*/
		public function insert($aParams) {
			try {
				$oStmt = $this->oDB->prepare("INSERT INTO users (name, email, password, birth_date, photo, city_id, gender)
											VALUES ( :name, :email, :password, :birth_date, :photo, :city_id, :gender );");
				$oStmt->bindParam(":name",$aParams['name']);
				$oStmt->bindParam(":email",$aParams['email']);
				$oStmt->bindParam(":password",$aParams['password']);
				$sBirthDate = intval($aParams['birth_year'])."-".intval($aParams['birth_month'])."-".intval($aParams['birth_day']);
				$oStmt->bindParam(":birth_date", $sBirthDate);
				$oStmt->bindParam(":photo",$aParams['photo']);
				$oStmt->bindParam(":city_id", intval($aParams['city']));
				$gender = ($aParams['gender'])? 'male' : 'female';
				$oStmt->bindParam(":gender", $gender);

				$oStmt->execute();	
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}
		}
		/**
		* Метод возвращает по id пользователя имя, дату рождения, пол, адрес фото, город и страну пользователя
		* в виде ассоциативного массива 
		*/
		public function getField($id) {
			try {
				$oStmt = $this->oDB->prepare("	SELECT 	users.name as name,
														users.birth_date as birth_date,
														users.gender as gender,
														users.photo as photo,
														city.city as city,
														country.country as country
												FROM 	(users INNER JOIN city ON users.city_id = city.id)
														INNER JOIN  country ON city.country_id = country.id
												WHERE users.id = :id;");
				$oStmt->bindParam(":id", intval($id));
				$oStmt->execute();
				return $oStmt->fetch();
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}
		}
		/**
		*	Метода проверяет есть ли в таблице users пользователь с почтой sEmail
		*/
		public function getEmail($sEmail) {
			try {
				$oStmt = $this->oDB->prepare("SELECT users.email as email FROM users WHERE users.email = :email");
				$oStmt->bindParam(":email",$sEmail);
				$oStmt->execute();
				return $oStmt->fetchColumn();
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}	
		}
		/**
		*	Метод возвращает ИД пользователя по email
		*/
		public function getId($sEmail) {
			try {
				$oStmt = $this->oDB->prepare("SELECT users.id as email FROM users WHERE users.email = :email");
				$oStmt->bindParam(":email",$sEmail);
				$oStmt->execute();
				return $oStmt->fetchColumn();
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}	
		}
		/**
		*	Метод возвращет список стран в виде ассоциативного массива, где ключами являются ИД стран
		*/
		public function getCountryList() {
			try {
				return $this->oDB->query("SELECT id, country FROM country;")->fetchAll(PDO::FETCH_KEY_PAIR);
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}	
		}
		/**
		* Метод возвращает список городов по ИД страны, в виде ассоциативного массива, где ключами являются ИД городов
		*/
		public function getCityList($iCountryID) {
			try {
				$oStmt = $this->oDB->prepare("SELECT id, city FROM city WHERE city.country_id = :id;");
				$oStmt->bindParam(":id", intval($iCountryID));
				$oStmt->execute();
				return $oStmt->fetchAll(PDO::FETCH_KEY_PAIR);
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}	
		}

		/**
		* Метод добавляет в таблицу country страну и возвращает ИД этой страны
		*/
		public function addCountry($sCountry) {
			try {
				$oStmt = $this->oDB->prepare("INSERT INTO country (country) VALUES ( :country);");
				$oStmt->bindParam(":country",$sCountry);
				$oStmt->execute();
				$oStmt = $this->oDB->prepare("SELECT id FROM country WHERE country = :country;");
				$oStmt->bindParam(":country",$sCountry);
				$oStmt->execute();
				return $oStmt->fetchColumn();
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}	
		}
		
		/**
		* Метод добавляет в таблицу city город принадлижащий стране с ИД sCountryID и возвращает ИД этого города
		*/
		public function addCity($sCity, $sCountryID) {
			try {
				$oStmt = $this->oDB->prepare("INSERT INTO city (city, country_id) VALUES ( :city, :country_id);");
				$oStmt->bindParam(":city",$sCity);
				$oStmt->bindParam(":country_id", intval($sCountryID));
				$oStmt->execute();
				$oStmt = $this->oDB->prepare("SELECT id FROM city WHERE city = :city;");
				$oStmt->bindParam(":city",$sCity);
				$oStmt->execute();
				return $oStmt->fetchColumn();
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}	
		}

		/**
		*	Метод возвращает почту, пароль и ИД пользователя из таблицы users
		*/
		public function getAuthData($sEmail) {
			try {
				$oStmt = $this->oDB->prepare("SELECT email, password, id FROM users WHERE `email` = :email");
				$oStmt->bindParam(":email",$sEmail);
				$oStmt->execute();
				return $oStmt->fetch();
			} catch (PDOException $e) {
				$this->log($e->getMessage());
			}
		}
	}
?>