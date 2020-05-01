<?php

/**
 * 	
 * MySQL osztály
 * @version 1.0
 * @copyright 200 Muzslay András 
 **/
	
	// MySQL csatlakozási osztaly
	class DB_Mysql
	{
		// A paraméterek
		var $ip;
		var $db_user;
		var $db_pass;
		var $db_ci;
		var $db_db;
		var $params = array();

		//-------------------------------------------------------------------------------		
		// A konstruktor, amely inicializálja a példány paramétereit, csatlakozik és adatbázist is választ
		function DB_Mysql($_host, $_user, $_pass, $_db)
		{
			// Beállítjuk a paraméterek alapértékét
			$this->ip = $_host;
			$this->db_user = $_user;
			$this->db_pass = $_pass;
			$this->db_db = $_db;

			// Csatlakozás
			$ka = mysql_connect($this->ip, $this->db_user, $this->db_pass);
			
			// Hibakezelés
			if (!$ka)
			{
				$this->params['connection'] = false;
				return false;
			}
			else
			{	
				// A kapcsolat azonosítót beállítjuk
				$this->db_ci = $ka;
				$this->params['connection'] = true;
						
				// Adatbázis választás
				if (mysql_select_db($this->db_db, $this->db_ci))
				{
					$this->params['select_db'] = true;
					return true;
				}
				else
				{
					// Ha a csatlakozás és a db választás is OK, akkor visszatérünk
					$this->params['select_db'] = false;
					return false;
				}
			}
		}
		/*
		// A csatlakozó tagfüggvény
		function connect()
		{
			// Csatlakozás
			$ka = @mysql_connect($this->$ip, $this->$db_user, $this->$db_pass);
			
			// Hibakezelás
			if (!$ka)
			{
				$this->params['error_msg'] = "Sikertelen csatlakozás!";
				return false;
			}
			else
			{	
				// A kapcsolat azonosítót beállítjuk
				$this->db_ci = $ka;
				return true;
			}
		}
		
		// Az adatbázisválasztó tagfüggvény
		function select_db($db, $ci)
		{
			// Paraméter beállítás
			$this->db_db = $db;
			
			// Adatbázis választás
			if (!@mysql_select_db($db, $ci))
			{
				$this->params['error_msg'] = "Nincs ilyen adatbázis!";
				return false;
			}
			else
			{
				return true;
			}
		}
		
		// Adatbázis kezelõ adatait lekérdezõ tagfüggvény
		function db_params()
		{
			if (!empty($this->db_ci))
			{
				$this->params['Server info'] = mysql_get_server_info($this->db_ci);
				
				
			}
		}
*/		
		//-------------------------------------------------------------------------------								
		// Lekérdezõ függvény input:SQL, output:eredménytábla
		function db_query($sql)
		{
			// A params tömb data részét ki kell üríteni
			unset($this->params['data']);
			
			// Megvizsgáljuk az SQL-t, hogy milyen típusú DML, ha SELECT, akkor beállítunk egy paramétert
			if (preg_match("/SELECT.+/", $sql))
			{
				$this->params['select'] = true;
			}
			else
			{
				$this->params['select'] = false;
			}
			
			// A lekérdezés lefuttatása
			$et = mysql_query($sql);
			
			if (!$et)
			{
				$this->params['error_msg'] = "Hiba a lek?rdez?sben!";
				return false;
			}
			else
			{
			/*
				// Vagy eredm?nyt?bla van, vagy affected rows
				if (!($this->params['num_rows'] = mysql_num_rows($et)))
				{
					$this->params['affected_rows'] = mysql_affected_rows();
				}
			*/
			
				// Csak akkor ellenõrzünk eredménytáblát, ha SELECT volt
				if ($this->params['select'])
				{
					// Be kell írni a mysql_num_rows()-t a paraméterek közé
					$this->params['num_rows'] = mysql_num_rows($et);
						
					// Hibakezelés - ha nincs eredménytábla, és affected rows is 0, akkor hiba
					if (mysql_num_rows($et) == 0)
					{
						$this->params['query_msg'] = "Nincs adat!";
						
						return true;
					}
					else // Ha van fetch-elhet? eredménytábla
					{
						while ($record = mysql_fetch_array($et))
						{
							$data[] = $record;
						}
						
						$this->params['query_msg'] = "Sikeres lek?rdez?s!";
						
						// Az eredménytábla bekerül a params tömbbe
						$this->params['data'] = $data;
						
						return true;
					}
				} // ha select volt - vége
				else
				{
						// Lehet hogy sikeres a lekérdezés, csak nem SELECT volt a query
						if (mysql_affected_rows() > 0)
						{
							$this->params['query_msg'] = mysql_affected_rows() . " sor módosult!";
							$this->params['affected_rows'] = mysql_affected_rows();
							return true;
						}
						else
						{
							$this->params['error_msg'] = "Egy sor sem módosult!";
							return false;
						}				
				} // ha nem select volt - vége			
			} // ha van eredm?nyt?bla - vége
		} // function - vége 
	} // class vége
?>