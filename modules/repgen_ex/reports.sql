-- MySQL dump 8.21
--
-- Host: localhost    Database: schorg
---------------------------------------------------------
-- Server version	3.23.48-log


--
-- Table structure for table 'numbers'
--

CREATE TABLE numbers (
  id char(10) NOT NULL default '',
  beschreibung char(20) NOT NULL default '',
  quantity int(11) default NULL,
  price decimal(10,2) default NULL
) TYPE=MyISAM;

--
-- Dumping data for table 'numbers'
--


INSERT INTO numbers VALUES ('1','Schraube M3',5,0.21);
INSERT INTO numbers VALUES ('1','Schraube M5',5,0.23);
INSERT INTO numbers VALUES ('1','Schraube M10',15,0.40);
INSERT INTO numbers VALUES ('1','Schraube M13',7,0.43);
INSERT INTO numbers VALUES ('1','Schraube M15',3,0.65);
INSERT INTO numbers VALUES ('2','Hammer',105,5.23);
INSERT INTO numbers VALUES ('2','Zange',5,10.23);
INSERT INTO numbers VALUES ('2','Rechen',35,14.00);
INSERT INTO numbers VALUES ('2','Schaufel',10,10.00);
INSERT INTO numbers VALUES ('3','Besen',5,11.23);

--
-- Table structure for table 'reports'
--

CREATE TABLE reports (
  id char(10) NOT NULL default '',
  typ char(6) NOT NULL default '',
  attrib char(255) NOT NULL default ''
) TYPE=MyISAM;

--
-- Dumping data for table 'reports'
--


INSERT INTO reports VALUES ('1','item','String|PF|Times-Roman|8|10l|300|10|printed at||||');
INSERT INTO reports VALUES ('F2','funct','atime|2002-12-05|Bauer|Actual Time|function atime() {return date(\"h:i:s a\");}');
INSERT INTO reports VALUES ('F1','funct','datum|2002-12-05|Bauer|Date|function datum() {return date(\"m-d-Y\");}');
INSERT INTO reports VALUES ('1','item','DB|DE|Helvetica|12|6l|100|0|typ||||');
INSERT INTO reports VALUES ('B1','item','String|PH|Helvetica|6||100|0|Stringitem||||');
INSERT INTO reports VALUES ('F4','funct','oldgroup|2002-02-10|bauer|Value of old group|function oldgroup($db,$it){return $it_group_old();}');
INSERT INTO reports VALUES ('F5','funct','newgroup|2002-12-05|bauer|new group value|function newgroup($db,$it){return $it->get_group_new();}');
INSERT INTO reports VALUES ('F3','funct','seite|2002-12-05|Bauer|Pagenumber|function seite($db,$it){$it->set_font(\"Helvetica.afm\");$it->pdf->ezStartPageNumbers(500,40,8,\"left\",\'{PAGENUM} of {TOTALPAGENUM}\');return ;}');
INSERT INTO reports VALUES ('1','item','String|PF|Helvetica|8|4l|450|10|Page||||||||');
INSERT INTO reports VALUES ('1','item','Term|PF|Helvetica|8|20l|460|10|seite||||||||');
INSERT INTO reports VALUES ('1','item','Term|PF|Times-Roman|8|8l|380|10|atime||||');
INSERT INTO reports VALUES ('1','item','Term|PF|Times-Roman|8||335|10|datum||||');
INSERT INTO reports VALUES ('B1','block','block1|2002-06-11|Bauer|Block 1');
INSERT INTO reports VALUES ('1','item','String|PH|Helvetica|14|40l|200|0|Stored Report|||1|40');
INSERT INTO reports VALUES ('1','item','DB|DE|Helvetica|12|15l|150|0|attrib|||1|15');
INSERT INTO reports VALUES ('1','item','DB|DE|Helvetica|12|15l|150|0|attrib||||');
INSERT INTO reports VALUES ('2','item','DB|DE|Helvetica-Bold|12||100|600|attrib||||');
INSERT INTO reports VALUES ('1','item','DB|DE|Helvetica|12|5l|350|0|attrib||||');
INSERT INTO reports VALUES ('1','item','DB|PH|Helvetica|14|6l|360|0|id|||1|6');
INSERT INTO reports VALUES ('1','item','String|GH|Times-Bold|14|10l|200|0|Neue Gruppe||||');
INSERT INTO reports VALUES ('1','item','DB|GH|Times-Bold|12|6l|285|0|id||||||||');
INSERT INTO reports VALUES ('6','item','DB|DE|Helvetica|12|3l|100|0|id||||');
INSERT INTO reports VALUES ('6','item','String|GH|Helvetica|6|20l|300|0|Gruppenkopf||||');
INSERT INTO reports VALUES ('F6','funct','rec_count|2002-12-05|bauer|total number of records|function rec_count($db,$it) {return $it->count;}');
INSERT INTO reports VALUES ('6','item','String|RF|Helvetica|12|20l|100|0|Satz-Zahl:||||');
INSERT INTO reports VALUES ('6','item','Term|RF|Helvetica|12|4l|200|0|rec_count||||');
INSERT INTO reports VALUES ('6','item','String|GF|Helvetica|12|20l|100|0|Obige Zahl:||||');
INSERT INTO reports VALUES ('6','item','DB|DE|Times-Roman|12|10r|250|0|quantity|||||true|true|true|true');
INSERT INTO reports VALUES ('6','item','Term|GF|Helvetica|12|4l|200|0|subcount||||');
INSERT INTO reports VALUES ('6','item','DB|DE|Helvetica|12|10c|350|0|price|||||true||true|true');
INSERT INTO reports VALUES ('6','item','DB|DE|Helvetica|12|20l|150|0|beschreibung||||||||');
INSERT INTO reports VALUES ('F7','funct','subcount|2002-12-05|bauer|total number of records of a group|function subcount($db,$it) {return $it->subcount;}');
INSERT INTO reports VALUES ('6','info','Zahlen|2002-12-04|Bauer|Zahlen testen|portrait|a4|classgrid');
INSERT INTO reports VALUES ('6','select','select * from numbers order by id');
INSERT INTO reports VALUES ('6','group','id|nopage');
INSERT INTO reports VALUES ('1','info','Reports|2002-12-05|Bauer|Report List of all stored Reports|portrait|a4|classgrid');
INSERT INTO reports VALUES ('1','select','select * from reports order by id');
INSERT INTO reports VALUES ('1','group','id|newpage');
INSERT INTO reports VALUES ('2','info','single|2002-12-04|Bauer|Single page per Record|portrait|a4|single');
INSERT INTO reports VALUES ('2','select','select * from reports');
INSERT INTO reports VALUES ('2','group','|nopage');


--
-- Table structure for table 'schluessel'
--

CREATE TABLE schluessel (
  art char(10) NOT NULL default '',
  begriff char(5) NOT NULL default '',
  bezeichnung char(35) NOT NULL default '',
  PRIMARY KEY  (art,begriff)
) TYPE=MyISAM;

--
-- Dumping data for table 'schluessel'
--


INSERT INTO schluessel VALUES ('typ','info','Report Record');
INSERT INTO schluessel VALUES ('typ','selec','SQL-statement');
INSERT INTO schluessel VALUES ('typ','block','BLOCK record');
INSERT INTO schluessel VALUES ('typ','funct','Term Record');
INSERT INTO schluessel VALUES ('typ','item','Item Record');