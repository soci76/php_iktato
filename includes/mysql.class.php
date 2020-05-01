<?php

/**
 * 	
 * MySQL oszt�ly
 * @version 1.0
 * @copyright 200 Muzslay Andr�s 
 **/
	
	// MySQL csatlakoz�si osztaly
	class DB_Mysql
	{
		// A param�terek
		var $ip;
		var $db_user;
		var $db_pass;
		var $db_ci;
		var $db_db;
		var $params = array();

		//-------------------------------------------------------------------------------		
		// A konstruktor, amely inicializ�lja a p�ld�ny param�tereit, csatlakozik �s adatb�zist is v�laszt
		function DB_Mysql($_host, $_user, $_pass, $_db)
		{
			// Be�ll�tjuk a param�terek alap�rt�k�t
			$this->ip = $_host;
			$this->db_user = $_user;
			$this->db_pass = $_pass;
			$this->db_db = $_db;

			// Csatlakoz�s
			$ka = mysql_connect($this->ip, $this->db_user, $this->db_pass);
			
			// Hibakezel�s
			if (!$ka)
			{
				$this->params['connection'] = false;
				return false;
			}
			else
			{	
				// A kapcsolat azonos�t�t be�ll�tjuk
				$this->db_ci = $ka;
				$this->params['connection'] = true;
						
				// Adatb�zis v�laszt�s
				if (mysql_select_db($this->db_db, $this->db_ci))
				{
					$this->params['select_db'] = true;
					return true;
				}
				else
				{
					// Ha a csatlakoz�s �s a db v�laszt�s is OK, akkor visszat�r�nk
					$this->params['select_db'] = false;
					return false;
				}
			}
		}
		/*
		// A csatlakoz� tagf�ggv�ny
		function connect()
		{
			// Csatlakoz�s
			$ka = @mysql_connect($this->$ip, $this->$db_user, $this->$db_pass);
			
			// Hibakezel�s
			if (!$ka)
			{
				$this->params['error_msg'] = "Sikertelen csatlakoz�s!";
				return false;
			}
			else
			{	
				// A kapcsolat azonos�t�t be�ll�tjuk
				$this->db_ci = $ka;
				return true;
			}
		}
		
		// Az adatb�zisv�laszt� tagf�ggv�ny
		function select_db($db, $ci)
		{
			// Param�ter be�ll�t�s
			$this->db_db = $db;
			
			// Adatb�zis v�laszt�s
			if (!@mysql_select_db($db, $ci))
			{
				$this->params['error_msg'] = "Nincs ilyen adatb�zis!";
				return false;
			}
			else
			{
				return true;
			}
		}
		
		// Adatb�zis kezel� adatait lek�rdez� tagf�ggv�ny
		function db_params()
		{
			if (!empty($this->db_ci))
			{
				$this->params['Server info'] = mysql_get_server_info($this->db_ci);
				
				
			}
		}
*/		
		//-------------------------------------------------------------------------------								
		// Lek�rdez� f�ggv�ny input:SQL, output:eredm�nyt�bla
		function db_query($sql)
		{
			// A params t�mb data r�sz�t ki kell �r�teni
			unset($this->params['data']);
			
			// Megvizsg�ljuk az SQL-t, hogy milyen t�pus� DML, ha SELECT, akkor be�ll�tunk egy param�tert
			if (preg_match("/SELECT.+/", $sql))
			{
				$this->params['select'] = true;
			}
			else
			{
				$this->params['select'] = false;
			}
			
			// A lek�rdez�s lefuttat�sa
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
			
				// Csak akkor ellen�rz�nk eredm�nyt�bl�t, ha SELECT volt
				if ($this->params['select'])
				{
					// Be kell �rni a mysql_num_rows()-t a param�terek k�z�
					$this->params['num_rows'] = mysql_num_rows($et);
						
					// Hibakezel�s - ha nincs eredm�nyt�bla, �s affected rows is 0, akkor hiba
					if (mysql_num_rows($et) == 0)
					{
						$this->params['query_msg'] = "Nincs adat!";
						
						return true;
					}
					else // Ha van fetch-elhet? eredm�nyt�bla
					{
						while ($record = mysql_fetch_array($et))
						{
							$data[] = $record;
						}
						
						$this->params['query_msg'] = "Sikeres lek?rdez?s!";
						
						// Az eredm�nyt�bla beker�l a params t�mbbe
						$this->params['data'] = $data;
						
						return true;
					}
				} // ha select volt - v�ge
				else
				{
						// Lehet hogy sikeres a lek�rdez�s, csak nem SELECT volt a query
						if (mysql_affected_rows() > 0)
						{
							$this->params['query_msg'] = mysql_affected_rows() . " sor m�dosult!";
							$this->params['affected_rows'] = mysql_affected_rows();
							return true;
						}
						else
						{
							$this->params['error_msg'] = "Egy sor sem m�dosult!";
							return false;
						}				
				} // ha nem select volt - v�ge			
			} // ha van eredm?nyt?bla - v�ge
		} // function - v�ge 
	} // class v�ge
?>