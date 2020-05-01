CREATE TABLE iktato (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  ugyintezo_id INTEGER UNSIGNED NOT NULL,
  ugyfel_id INTEGER UNSIGNED NOT NULL,
  irat_id INTEGER UNSIGNED NOT NULL,
  foszam INTEGER UNSIGNED NOT NULL,
  alszam INTEGER UNSIGNED NOT NULL,
  targy VARCHAR(20) NULL,
  kelt DATE NULL,
  melleklet VARCHAR(5) NULL,
  irany VARCHAR(8) NULL,
  megjegyzes VARCHAR(255) NULL,
  PRIMARY KEY(id),
  INDEX termek_FKIndex2(irat_id),
  INDEX iktato_FKIndex3(ugyfel_id),
  INDEX iktato_FKIndex4(ugyintezo_id)
);

CREATE TABLE irat (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  tipus VARCHAR(20) NOT NULL,
  megjegyzes VARCHAR(50) NULL,
  PRIMARY KEY(id)
);

CREATE TABLE ugyfel (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nev VARCHAR(20) NULL,
  irsz VARCHAR(4) NULL,
  varos VARCHAR(20) NULL,
  utca VARCHAR(20) NULL,
  hazszam VARCHAR(10) NULL,
  email VARCHAR(20) NULL,
  telefon VARCHAR(20) NULL,
  fax VARCHAR(20) NULL,
  allapot BOOL NULL,
  PRIMARY KEY(id)
);

CREATE TABLE ugyintezo (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nev VARCHAR(50) NOT NULL,
  jelszo VARCHAR(30) NOT NULL,
  beosztas VARCHAR(30) NOT NULL,
  teljes_nev VARCHAR(50) NOT NULL,
  szint INTEGER UNSIGNED NULL,
  kod INTEGER UNSIGNED NULL,
  PRIMARY KEY(id)
);
INSERT INTO ugyintezo (nev, jelszo, beosztas, teljes_nev, szint, kod) VALUES('soci','sorgodor','2','Sölétormos Ottó','5','');



