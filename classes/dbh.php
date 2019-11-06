<?php
	class DB {
		//Db connection
		private static function connect() {
			$pdo = new PDO('mysql:host=localhost;dbname=Camagru;charset=utf8', 'root', '123456');
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $pdo;
		}
		//Db query with prepared stmt and return data
		public static function query($query, $params = array()) {
			$stmt = self::connect()->prepare($query);
			$stmt->execute($params);

			if (explode(' ', $query)[0] == 'SELECT'){
				$data = $stmt->fetchAll();
				return $data;
			}
		}
	}
?>