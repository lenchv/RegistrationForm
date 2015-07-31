<?php
	setLocale(LC_ALL, 0);
	/*
		Класс проверяет введенные пользователем данные в регистрационной форме, 
		обрабатывает эти поля, и взводит флаги в случае возникновения ошибки
	*/
	class Validation {
		//Флаги ошибок
		protected $bErrorName = false;
		protected $bErrorEmail = false;
		protected $bErrorPassword = false;
		protected $bErrorReppassword = false;
		protected $bErrorGender = false;
		protected $bErrorCountry = false;
		protected $bErrorCity = false;
		protected $bErrorBirthDay = false;
		protected $bErrorBirthMonth = false;
		protected $bErrorBirthYear = false;
		//Массив с обработанными данными
		protected $aResult = array(	'name' => '', 
									'email' => '', 
									'password' => '', 
									'gender' => true, 
									'country' => '', 
									'city' => '', 
									'birth_day' => -1,
									'birth_month' => -1,
									'birth_year' => -1);

		/**
		* В конструкторе происходят все проверки, обработки и взведение флагов
		*/
		function __construct($aForm) {
			if ( empty($aForm['name']) 
				|| !$this->checkLength($aForm['name'], 2, 50) 
				|| !(preg_match("/[a-zA-Z\x20]/", $aForm['name']) 
				|| preg_match("/[а-яА-ЯЁё\x20]/u", $aForm['name'])))  {
				$this->bErrorName = true;
			} else {
				$this->bErrorName = false;
				$this->aResult['name'] = $this->cleanString($aForm['name']);
			}
			if ( empty($aForm['email']) || !filter_var($aForm['email'], FILTER_VALIDATE_EMAIL)) {
				$this->bErrorEmail = true;
			} else {
				$this->bErrorEmail = false;
				$this->aResult['email'] = $this->cleanString($aForm['email']);
			}
		 	if ( empty($aForm['password']) || !$this->checkLength($aForm['password'], 6, 32) || !preg_match("/[a-zA-Z0-9\_\-\.]/", $aForm['password'])) {
		 		$this->bErrorPassword = true;
			} else {
		 		$this->bErrorPassword = false;
		 		$this->aResult['password'] = md5(sha1($this->cleanString($aForm['password'])));
			}
			if ( empty($aForm['reppassword']) || strcmp($aForm['reppassword'], $aForm['password']) ) {
				$this->bErrorReppassword = true;
			} else {
				$this->bErrorReppassword = false;
			}
			if ( empty($aForm['gender']) || (strcmp($aForm['gender'], 'male') && strcmp($aForm['gender'], 'female'))) {
				$this->bErrorGender = true;
			} else {
				$this->bErrorGender = false;
				$this->aResult['gender'] = (!strcmp($aForm['gender'], 'male'))? true : false;
			} 
			if ( empty($aForm['country']) ) {
				$this->bErrorCountry = true;
			} else {
				$this->bErrorCountry = false;
				$this->aResult['country'] = $this->cleanString($aForm['country']);
			}
			if ( empty($aForm['city']) ) {
				$this->bErrorCity = true;
			} else {
				$this->bErrorCity = false;
				$this->aResult['city'] = $this->cleanString($aForm['city']);
			}
			if ( empty($aForm['birth_day']) || !filter_var($aForm['birth_day'], FILTER_VALIDATE_INT) || $aForm['birth_day'] < 1 || $aForm['birth_day'] > 31) {
				$this->bErrorBirthDay = true;
			} else {
				$this->bErrorBirthDay = false;
				$this->aResult['birth_day'] = intval($aForm['birth_day']);
			}
			if ( empty($aForm['birth_month']) || !filter_var($aForm['birth_month'], FILTER_VALIDATE_INT) || $aForm['birth_month'] < 1 || $aForm['birth_month'] > 12) {
				$this->bErrorBirthMonth = true;
			} else {
				$this->bErrorBirthMonth = false;
				$this->aResult['birth_month'] = intval($aForm['birth_month']);
			}
			if ( empty($aForm['birth_year']) || !filter_var($aForm['birth_year'], FILTER_VALIDATE_INT) || $aForm['birth_year'] < 1900 || $aForm['birth_year'] > 2015) {
				$this->bErrorBirthYear = true;
			} else {
				$this->bErrorBirthYear = false;
				$this->aResult['birth_year'] = intval($aForm['birth_year']);
			}
		}

		/**
		* Статический метод очищающий строку от лишних символов
		*/
		static function cleanString($sValue) {
			$sValue = trim($sValue);
		    $sValue = stripslashes($sValue);
		    $sValue = strip_tags($sValue);
		    $sValue = htmlspecialchars($sValue);
		    
		    return $sValue;
		}
		/**
		* Провекра длины строки
		*/
		static function checkLength($sValue = "", $iMin, $iMax) {
			$bResult = (mb_strlen($sValue) < $iMin || mb_strlen($sValue) > $iMax);
			return !$bResult;
		}	
		/**
		* Метод возвращает true если взведен хоть один флаг ошибки
		*/
		public function isError() {
			return 	$this->bErrorName || $this->bErrorEmail ||
					$this->bErrorPassword || $this->bErrorReppassword ||
					$this->bErrorGender || $this->bErrorCountry ||
					$this->bErrorCity || $this->bErrorBirthDay ||
					$this->bErrorBirthMonth || $this->bErrorBirthYear;
		}
		//Метод показывает была ли ошибка в имени		
		public function isName() {
			return $this->bErrorName;
		}
		//Метод показывает была ли ошибка в почте
		public function isEmail() {
			return $this->bErrorEmail;
		}
		//Метод показывает была ли ошибка в пароле
		public function isPassword() {
			return $this->bErrorPassword; 
		}
		//Метод показывает была ли ошибка в повторном пароле
		public function isReppassword() {
			return $this->bErrorReppassword;
		}
		//Метод показывает была ли ошибка в поле
		public function isGender() {
			return $this->bErrorGender;
		}
		//Метод показывает была ли ошибка в стране
		public function isCountry() {
			return $this->bErrorCountry;
		}
		//Метод показывает была ли ошибка в городе
		public function isCity() {
			return $this->bErrorCity;
		}
		//Метод показывает была ли ошибка в дне рождения
		public function isBirth_day() {
			return $this->bErrorBirthDay;
		}
		//Метод показывает была ли ошибка в месяце рождения
		public function isBirth_month() {
			return $this->bErrorBirthMonth;
		}
		//Метод показывает была ли ошибка в годе рождения
		public function isBirth_year() {
			return $this->bErrorBirthYear;
		}
		//Метод возвращает массив с обработанными данными
		public function getResult() {
			return $this->aResult;
		}
	}

