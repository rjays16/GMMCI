<?php
/* $Id: czech-windows-1250.inc.php,v 1.2 2005/10/29 20:08:11 kaloyan_raev Exp $ */

/**
 * Czech language file by
 *   Michal �iha� <nijel at users.sourceforge.net>
 */

$charset = 'windows-1250';
$text_dir = 'ltr';
$left_font_family = 'verdana, arial, helvetica, geneva, sans-serif';
$right_font_family = 'tahoma, arial, helvetica, geneva, sans-serif';
$number_thousands_separator = ' ';
$number_decimal_separator = '.';
// shortcuts for Byte, Kilo, Mega, Giga, Tera, Peta, Exa
$byteUnits = array('bajt�', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB');

$day_of_week = array('Ned�le', 'Pond�l�', '�ter�', 'St�eda', '�tvrtek', 'P�tek', 'Sobota');
$month = array('ledna', '�nora', 'b�ezna', 'dubna', 'kv�tna', '�ervna', '�ervence', 'srpna', 'z���', '��jna', 'listopadu', 'prosince');
// See http://www.php.net/manual/en/function.strftime.php to define the
// variable below
$datefmt = '%a %d. %b %Y, %H:%M';

$timespanfmt = '%s dn�, %s hodin, %s minut a %s sekund';

$strAPrimaryKey = 'V&nbsp;tabulce %s byl vytvo�en prim�rn� kl��';
$strAbortedClients = 'P�eru�en�';
$strAbsolutePathToDocSqlDir = 'Zadejte absolutn� cestu docSQL adres��e na serveru';
$strAccessDenied = 'P��stup odep�en';
$strAccessDeniedExplanation = 'phpMyAdmin se pokusil p�ipojit k&nbsp;MySQL serveru, a ten odm�tl p�ipojen�. Zkontrolujte jm�no serveru, u�ivatelsk� jm�no a heslo v&nbsp;souboru config.inc.php a ujist�te se, �e jsou toto�n� s&nbsp;t�mi co m�te od administr�tora MySQL serveru.';
$strAction = 'Akce';
$strAddAutoIncrement = 'P�idat hodnotu AUTO_INCREMENT';
$strAddDeleteColumn = 'P�idat/Smazat sloupec';
$strAddDeleteRow = 'P�idat/Smazat ��dek s&nbsp;podm�nkou';
$strAddDropDatabase = 'P�idat DROP DATABASE';
$strAddIntoComments = 'P�idat do koment���';
$strAddNewField = 'P�idat nov� sloupec';
$strAddPriv = 'P�idat nov� privilegium';
$strAddPrivMessage = 'Opr�vn�n� bylo p�id�no.';
$strAddPrivilegesOnDb = 'P�idat opr�vn�n� pro datab�zi';
$strAddPrivilegesOnTbl = 'P�idat opr�vn�n� pro tabulku';
$strAddSearchConditions = 'P�idat vyhled�vac� parametry (obsah dotazu po p��kazu "WHERE"):';
$strAddToIndex = 'P�idat do indexu  &nbsp;%s&nbsp;sloupc�';
$strAddUser = 'P�idat nov�ho u�ivatele';
$strAddUserMessage = 'U�ivatel byl p�id�n.';
$strAddedColumnComment = 'P�id�n koment�� ke sloupci';
$strAddedColumnRelation = 'P�id�na relace pro sloupec';
$strAdministration = 'Spr�va';
$strAffectedRows = 'Ovlivn�n� ��dky:';
$strAfter = 'Po %s';
$strAfterInsertBack = 'Zp�t';
$strAfterInsertNewInsert = 'Vlo�it dal�� ��dek';
$strAll = 'V�echno';
$strAllTableSameWidth = 'Zobrazit v�echny tabulky stejnou ���kou';
$strAlterOrderBy = 'Zm�nit po�ad� tabulky podle';
$strAnIndex = 'K&nbsp;tabulce %s byl p�id�n index';
$strAnalyzeTable = 'Analyzovat tabulku';
$strAnd = 'a';
$strAny = 'Jak�koliv';
$strAnyColumn = 'Jak�koliv sloupec';
$strAnyDatabase = 'Jak�koliv datab�ze';
$strAnyHost = 'Jak�koliv po��ta�';
$strAnyTable = 'Jak�koliv tabulka';
$strAnyUser = 'Jak�koliv u�ivatel';
$strArabic = 'Arab�tina';
$strArmenian = 'Arm�n�tina';
$strAscending = 'Vzestupn�';
$strAtBeginningOfTable = 'Na za��tku tabulky';
$strAtEndOfTable = 'Na konci tabulky';
$strAttr = 'Vlastnosti';
$strAutodetect = 'Automaticky zjistit';
$strAutomaticLayout = 'automatick� rozvr�en�';

$strBack = 'Zp�t';
$strBaltic = 'Baltick�';
$strBeginCut = 'ZA��TEK V�PISU';
$strBeginRaw = 'ZA��TEK V�PISU';
$strBinary = ' Bin�rn� ';
$strBinaryDoNotEdit = ' Bin�rn� - neupravujte ';
$strBookmarkDeleted = 'Polo�ka byla smaz�na z&nbsp;obl�ben�ch.';
$strBookmarkLabel = 'N�zev';
$strBookmarkQuery = 'Obl�ben� SQL dotaz';
$strBookmarkThis = 'P�idat tento SQL dotaz do obl�ben�ch';
$strBookmarkView = 'Jen zobrazit';
$strBrowse = 'Proj�t';
$strBrowseForeignValues = 'Proj�t hodnoty ciz�ch kl���';
$strBulgarian = 'Bulharsky';
$strBzError = 'phpMyAdminovi se nepoda�ilo zkomprimovat v�pis, proto�e roz���en� pro kompresi Bz2 je v&nbsp;t�to verzi php chybn�. Doporu�ujeme nastavit <code>$cfg[\'BZipDump\']</code> v&nbsp;nastaven�ch phpMyAdmina na <code>FALSE</code>. Pokud chcete pou��vat kompresi Bz2, m�li byste nainstalovat nov�j�� verzi php. V�ce informac� o&nbsp;tomto probn�lu je u popisu chyby %s.';
$strBzip = '"zabzipov�no"';

$strCSVOptions = 'Nastaven� CSV exportu';
$strCannotLogin = 'Nepoda�ilo se p�ihl�en� k MySQL serveru';
$strCantLoad = 'nelze nahr�t roz���en� %s,<br />pros�m zkontolujte nastaven� PHP';
$strCantLoadMySQL = 'nelze nahr�t roz���en� pro MySQL,<br />pros�m zkontrolujte nastaven� PHP.';
$strCantLoadRecodeIconv = 'Nelze nahr�t roz���en� iconv ani recode pot�ebn� pro p�evod znakov�ch sad. Upravte nastaven� php tak aby umo��ovalo pou��t tyto roz���en� nebo vypn�te p�evod znakov�ch sad v&nbsp;phpMyAdminu.';
$strCantRenameIdxToPrimary = 'Index nem��ete p�ejmenovat na "PRIMARY"!';
$strCantUseRecodeIconv = 'Nelze pou��t funkce iconv ani libiconv ani recode_string, p�esto�e roz���en� jsou nahr�na. Zkontrolujte nastaven� php.';
$strCardinality = 'Mohutnost';
$strCarriage = 'N�vrat voz�ku (CR): \\r';
$strCaseInsensitive = 'nerozli�ovat velk� a mal� p�smena';
$strCaseSensitive = 'rozli�ovat velk� a mal� p�smena';
$strCentralEuropean = 'St�edn� Evropa';
$strChange = 'Zm�nit';
$strChangeCopyMode = 'Vytvo�it nov�ho u�ivatele se stejn�mi opr�vn�n�mi a ...';
$strChangeCopyModeCopy = '... zachovat p�vodn�ho u�ivatele.';
$strChangeCopyModeDeleteAndReload = ' ... smazat u�ivatele a pot� znovu na��st opr�vn�n�.';
$strChangeCopyModeJustDelete = ' ... smazat p�vodn�ho u�ivatele ze v�ech tabulek.';
$strChangeCopyModeRevoke = ' ... odebrat v�echna opr�vn�n� p�odn�mu u�ivateli a pot� ho smazat.';
$strChangeCopyUser = 'Zm�nit informace o&nbsp;u�ivateli / Kop�rovat u�ivatele';
$strChangeDisplay = 'Zvolte kter� sloupce zobrazit';
$strChangePassword = 'Zm�nit heslo';
$strCharset = 'Znakov� sada';
$strCharsetOfFile = 'Znakov� sada souboru:';
$strCharsets = 'Znakov� sady';
$strCharsetsAndCollations = 'Znakov� sady a porovn�v�n�';
$strCheckAll = 'Za�krtnout v�e';
$strCheckDbPriv = 'Zkontrolovat opr�vn�n� pro datab�zi';
$strCheckPrivs = 'Zkontrolovat opr�vn�n�';
$strCheckPrivsLong = 'Zkontrolovat opr�vn�n� pro datab�zi &quot;%s&quot;.';
$strCheckTable = 'Zkontrolovat tabulku';
$strChoosePage = 'Zvolte str�nku, kterou chcete zm�nit';
$strColComFeat = 'Zobrazuji koment��e sloupc�';
$strCollation = 'Porovn�v�n�';
$strColumn = 'Sloupec';
$strColumnNames = 'N�zvy sloupc�';
$strColumnPrivileges = 'Opr�vn�n� pro jednotliv� sloupce';
$strCommand = 'P��kaz';
$strComments = 'Koment��e';
$strCompleteInserts = '�pln� inserty';
$strCompression = 'Komprese';
$strConfigFileError = 'phpMyAdmin nemohl na��st konfigura�n� soubor!<br />Tato chyba m��e nastat pokud v&nbsp;n�m php najde chybu nebo nem��e tento soubor naj�t.<br />Po kliknut� na n�sleduj�c� odkaz se konfigurace spust� a budou zobrazeny informace o&nbsp;chyb�, ke kter� do�lo. Pak opravte tuto chybu (nej�ast�ji se jedn� o&nbsp;chyb�j�c� st�edn�k).<br />Pokud z�sk�te pr�zdnou str�nku, v�echno je v&nbsp;po��dku.';
$strConfigureTableCoord = 'Pros�m, nastavte sou�adnice pro tabulku %s';
$strConfirm = 'Opravdu chcete toto prov�st?';
$strConnections = 'P�ipojen�';
$strCookiesRequired = 'B�hem tohoto kroku mus�te m�t povoleny cookies.';
$strCopyTable = 'Kop�rovat tabulku do (datab�ze<b>.</b>tabulka):';
$strCopyTableOK = 'Tabulka %s byla zkop�rov�na do %s.';
$strCopyTableSameNames = 'Nelze kop�rovat tabulku na sebe samu!';
$strCouldNotKill = 'phpMyAdminovi se nepoda�ilo zab�t vl�kno %s. Pravd�podobne jeho b�h ji� skon�il.';
$strCreate = 'Vytvo�it';
$strCreateIndex = 'Vytvo�it index na&nbsp;%s&nbsp;sloupc�ch';
$strCreateIndexTopic = 'Vytvo�it nov� index';
$strCreateNewDatabase = 'Vytvo�it novou datab�zi';
$strCreateNewTable = 'Vytvo�it novou tabulku v&nbsp;datab�zi %s';
$strCreatePage = 'Vytvo�it novou str�nku';
$strCreatePdfFeat = 'Vytv��en� PDF';
$strCriteria = 'Podm�nka';
$strCroatian = 'Chorvatsky';
$strCyrillic = 'Cyrilika';
$strCzech = '�esky';

$strDBComment = 'Koment�� k datab�zi: ';
$strDBGContext = 'Kontext';
$strDBGContextID = 'Kontext ID';
$strDBGHits = 'Z�sah�';
$strDBGLine = '��dka';
$strDBGMaxTimeMs = 'Min. �as, ms';
$strDBGMinTimeMs = 'Max. �as, ms';
$strDBGModule = 'Modul';
$strDBGTimePerHitMs = '�as/Z�sah, ms';
$strDBGTotalTimeMs = 'Celkov� �as, ms';
$strDanish = 'D�nsky';
$strData = 'Data';
$strDataDict = 'Datov� slovn�k';
$strDataOnly = ' Jen data';
$strDatabase = 'Datab�ze ';
$strDatabaseExportOptions = 'Nastaven� exportu datab�z�';
$strDatabaseHasBeenDropped = 'Datab�ze %s byla zru�ena.';
$strDatabaseNoTable = 'Tato datab�ze neobsahuje ��dn� tabulky!';
$strDatabaseWildcard = 'Datab�ze (z�stupn� znaky povoleny):';
$strDatabases = 'Datab�ze';
$strDatabasesDropped = '%s datab�ze byla �sp�n� zru�ena.';
$strDatabasesStats = 'Statistiky datab�z�';
$strDatabasesStatsDisable = 'Skr�t podrobnosti';
$strDatabasesStatsEnable = 'Zobrazit podrobnosti';
$strDatabasesStatsHeavyTraffic = 'Pozn�mka: Zobrazen� podrobnost� o&nbsp;datab�ch�ch m��e zp�sobit zna�n� zv��en� provozu mezi webserverem a MySQL serverem.';
$strDbPrivileges = 'Opr�vn�n� pro jednotliv� datab�ze';
$strDbSpecific = 'z�visl� na datab�zi';
$strDefault = 'V�choz�';
$strDefaultValueHelp = 'V�choz� hodnotu zadejte jen jednu hodnotu bez uvozovek a escapov�n� znak�, nap��klad: a';
$strDelOld = 'Aktu�ln� str�nka se odkazuje na tabulky, kter� ji� neexistuj�. Chcete odstranit tyto odkazy?';
$strDelete = 'Smazat';
$strDeleteAndFlush = 'Odstranit u�ivatele a znovuna��st opr�vn�n�.';
$strDeleteAndFlushDescr = 'Toto je nej�ist�� �e�en�, ale na��t�n� opr�vn�n� m��e trvat dlouho.';
$strDeleteFailed = 'Smaz�n� selhalo!';
$strDeleteUserMessage = 'Byl smaz�n u�ivatel %s.';
$strDeleted = '��dek byl smaz�n';
$strDeletedRows = 'Smazan� ��dky:';
$strDeleting = 'Odstra�uji %s';
$strDescending = 'Sestupn�';
$strDescription = 'Popis';
$strDictionary = 'slovn�k';
$strDisabled = 'Vypnuto';
$strDisplay = 'Zobrazit';
$strDisplayFeat = 'Zobrazen� funkc�';
$strDisplayOrder = 'Se�adit podle:';
$strDisplayPDF = 'Zobrazit jako sch�ma v&nbsp;PDF';
$strDoAQuery = 'Prov�st "dotaz podle p��kladu" (z�stupn� znak: "%")';
$strDoYouReally = 'Opravdu si p�ejete vykonat p��kaz';
$strDocu = 'Dokumentace';
$strDrop = 'Odstranit';
$strDropDB = 'Odstranit datab�zi %s';
$strDropSelectedDatabases = 'Zru�it vybranou datab�zi';
$strDropTable = 'Smazat tabulku';
$strDropUsersDb = 'Odstranit datab�ze se stejn�mi jm�ny jako u�ivatel�.';
$strDumpComments = 'Vlo�it koment��e ke sloupc�m jako SQL koment��e';
$strDumpSaved = 'V�pis byl ulo�en do souboru %s.';
$strDumpXRows = 'Vypsat %s ��dk� od %s.';
$strDumpingData = 'Vypisuji data pro tabulku';
$strDynamic = 'dynamick�';

$strEdit = 'Upravit';
$strEditPDFPages = 'Upravit PDF str�nky';
$strEditPrivileges = 'Upravit opr�vn�n�';
$strEffective = 'Efektivn�';
$strEmpty = 'Vypr�zdnit';
$strEmptyResultSet = 'MySQL vr�til pr�zdn� v�sledek (tj. nulov� po�et ��dk�).';
$strEnabled = 'Zapnuto';
$strEnd = 'Konec';
$strEndCut = 'KONEC V�PISU';
$strEndRaw = 'KONEC V�PISU';
$strEnglish = 'Anglicky';
$strEnglishPrivileges = 'Pozn�mka: n�zvy opr�vn�n� v&nbsp;MySQL jsou uv�d�ny anglicky';
$strError = 'Chyba';
$strEstonian = 'Estonsky';
$strExcelOptions = 'Nastaven� exportu do Excelu';
$strExecuteBookmarked = 'Spustit obl�ben� dotaz';
$strExplain = 'Vysv�tlit (EXPLAIN) SQL';
$strExport = 'Export';
$strExportToXML = 'Export do XML';
$strExtendedInserts = 'Roz���en� inserty';
$strExtra = 'Extra';

$strFailedAttempts = 'Nepoveden�ch pokus�';
$strField = 'Sloupec';
$strFieldHasBeenDropped = 'Sloupec %s byl odstran�n';
$strFields = 'Sloupce';
$strFieldsEmpty = ' Nebyl zad�n po�et sloupc�! ';
$strFieldsEnclosedBy = 'N�zvy sloupc� uzav�en� do';
$strFieldsEscapedBy = 'N�zvy sloupc� escapov�ny';
$strFieldsTerminatedBy = 'Sloupce odd�len�';
$strFileAlreadyExists = 'Soubor %s ji� na serveru existuje, zm�nt� jm�no souboru, nebo zvolte p�eps�n� souboru.';
$strFileCouldNotBeRead = 'Soubor nelze p�e��st';
$strFileNameTemplate = 'Vzor pro jm�no souboru';
$strFileNameTemplateHelp = 'Pou�ijte __DB__ pro jm�no datab�ze, __TABLE__ pro jm�no tabulky a jak�koliv parametry pro %sfunkci strftime%s pro vlo�en� data. P��pona souboru bude automaticky p�id�na podle typu. Jak�koliv jin� text bude zachov�n.';
$strFileNameTemplateRemember = 'zapamatovat si hodnotu';
$strFixed = 'pevn�';
$strFlushPrivilegesNote = 'Pozn�mka: phpMyAdmin z�sk�v� opr�vn�n� p��mo z&nbsp;tabulek MySQL. Obsah t�chto tabulek se m��e li�it od opr�vn�n�, kter� server pr�v� pou��v�, pokud byly tyto tabulky upravov�ny. V&nbsp;tomto p��pad� je vhodn� prov�st %sznovuna�ten� opr�vn�n�%s p�ed pokra�ov�n�m.';
$strFlushTable = 'Vypr�zdnit vyrovn�vac� pam� pro tabulku ("FLUSH")';
$strFormEmpty = 'Chyb�j�c� hodnota ve formul��i!';
$strFormat = 'Form�t';
$strFullText = 'Cel� texty';
$strFunction = 'Funkce';

$strGenBy = 'Vygeneroval';
$strGenTime = 'Vygenerov�no';
$strGeneralRelationFeat = 'Obecn� funkce relac�';
$strGerman = 'N�mecky';
$strGlobal = 'glob�ln�';
$strGlobalPrivileges = 'Glob�ln� opr�vn�n�';
$strGlobalValue = 'Glob�ln� hodnota';
$strGo = 'Prove�';
$strGrantOption = 'P�id�lov�n�';
$strGrants = 'P�id�lov�n�';
$strGreek = '�e�tina';
$strGzip = '"zagzipov�no"';

$strHasBeenAltered = 'byla zm�n�na.';
$strHasBeenCreated = 'byla vytvo�ena.';
$strHaveToShow = 'Mus�te volit alespo� jeden sloupec, kter� chcete zobrazit.';
$strHebrew = 'Hebrejsky';
$strHome = 'Hlavn� strana';
$strHomepageOfficial = 'Ofici�ln� str�nka phpMyAdmina';
$strHomepageSourceforge = 'Nov� str�nka phpMyAdmina';
$strHost = 'Po��ta�';
$strHostEmpty = 'Jm�no po��ta�e je pr�zdn�!';
$strHungarian = 'Ma�arsky';

$strId = 'ID';
$strIdxFulltext = 'Fulltext';
$strIfYouWish = 'Pokud si p�ejete nat�hnout jen vybran� sloupce z&nbsp;tabulky, napi�te je jako seznam sloupc� odd�len�ch ��rkou.';
$strIgnore = 'Ignorovat';
$strIgnoringFile = 'Ignoruji soubor %s';
$strImportDocSQL = 'Importovat soubory docSQL';
$strImportFiles = 'Importovat soubory';
$strImportFinished = 'Import ukon�en';
$strInUse = 'pr�v� se pou��v�';
$strIndex = 'Index';
$strIndexHasBeenDropped = 'Index %s byl odstran�n';
$strIndexName = 'Jm�no indexu&nbsp;:';
$strIndexType = 'Typ indexu&nbsp;:';
$strIndexes = 'Indexy';
$strInnodbStat = 'Stav InnoDB';
$strInsecureMySQL = 'V� konfigura�n� soubor obsahuje nastaven� (u�ivatel root bez hesla), kter� je v�choz� pro MySQL. V� MySQL server s&nbsp;t�mto v�choz�m nastaven�m je snadno napadnuteln�, a proto byste m�li zm�nit toto nastaven� a t�m podstatn� zv��it bezpe�nost Va�eho serveru.';
$strInsert = 'Vlo�it';
$strInsertAsNewRow = 'Vlo�it jako nov� ��dek';
$strInsertNewRow = 'Vlo�it nov� ��dek';
$strInsertTextfiles = 'Vlo�it textov� soubory do tabulky';
$strInsertedRowId = 'Id vlo�en�ho ��dku:';
$strInsertedRows = 'Vlo�eno ��dk�:';
$strInstructions = 'Instrukce';
$strInvalidName = '"%s" je rezervovan� slovo a proto ho nem��ete po��t jako jm�no datab�ze/tabulky/sloupce.';

$strJapanese = 'Japon�tina';
$strJumpToDB = 'Na dattab�zi &quot;%s&quot;.';
$strJustDelete = 'Jen odstranit u�ivatele z tabulek s opr�vn�n�mi.';
$strJustDeleteDescr = 'Odstran�n� u�ivatel� st�le budou m�t p�istup na server dokud nebudou znovuna�tena opr�vn�n�.';

$strKeepPass = 'Nem�nit heslo';
$strKeyname = 'Kl��ov� n�zev';
$strKill = 'Zab�t';
$strKorean = 'Korej�tina';

$strLaTeX = 'LaTeX';
$strLaTeXOptions = 'Nastaven� exportu do LaTeXu';
$strLandscape = 'Na ���ku';
$strLength = 'D�lka';
$strLengthSet = 'D�lka/Mno�ina*';
$strLimitNumRows = 'z�znam� na str�nku';
$strLineFeed = 'Ukon�en� ��dku (Linefeed): \\n';
$strLines = '��dek';
$strLinesTerminatedBy = '��dky ukon�en�';
$strLinkNotFound = 'Odkaz nenalezen';
$strLinksTo = 'Odkazuje na';
$strLithuanian = 'Litevsky';
$strLoadExplanation = 'Automaticky jsou zvoleny nejvhodn�j�� parametry, pokud toto nastaven� sel�e, m��ete zkusit druhou mo�nost.';
$strLoadMethod = 'Parametry pro p��kaz LOAD';
$strLocalhost = 'Lok�ln�';
$strLocationTextfile = 'textov� soubor';
$strLogPassword = 'Heslo:';
$strLogUsername = 'Jm�no:';
$strLogin = 'P�ihl�en�';
$strLoginInformation = 'P�ihla�ov�n�';
$strLogout = 'Odhl�sit se';

$strMIME_MIMEtype = 'MIME typ';
$strMIME_available_mime = 'Dostupn� MIME typy';
$strMIME_available_transform = 'Dostupn� tranformace';
$strMIME_description = 'Popis';
$strMIME_file = 'Jm�no souboru';
$strMIME_nodescription = 'Pro tuto transformaci nen� dostupn� ��dn� popis.<br />Zeptejte se autora co %s d�l�.';
$strMIME_transformation = 'Transformace p�i prohl�en�';
$strMIME_transformation_note = 'Pro seznam dostupn�ch parametr� transformac� a jejich MIME typ� klikn�te na %spopisy transformac�%s';
$strMIME_transformation_options = 'Parametry transformace';
$strMIME_transformation_options_note = 'Zadejte parametry transformac� v&nbsp;n�sleduj�c�m tvaru: \'a\',\'b\',\'c\'...<br />Pokud pot�ebujete pou��t zp�tn� lom�tko ("\") nebo jednoduch� uvozovky ("\'") mezi t�mito hodnotami, vlo�te p�ed n� zp�tn� lom�tko (nap��klad \'\\\\xyz\' or \'a\\\'b\').';
$strMIME_without = 'MIME typy zobrazen� kurz�vou nemaj� vlastn� transforma�n� funkci';
$strMissingBracket = 'Chyb� z�vorka';
$strModifications = 'Zm�ny byly ulo�eny';
$strModify = '�pravy';
$strModifyIndexTopic = 'Upravit index';
$strMoreStatusVars = 'Dal�� informace o&nbsp;stavu';
$strMoveTable = 'P�esunout tabulku do (datab�ze<b>.</b>tabulka):';
$strMoveTableOK = 'Tabulka %s byla p�esunuta do %s.';
$strMoveTableSameNames = 'Nelze p�eunout tabulku na sebe samu!';
$strMultilingual = 'mnohojazy�n�';
$strMustSelectFile = 'Zvolte soubor, kter� chcete vlo�it.';
$strMySQLCharset = 'Znakov� sada v&nbsp;MySQL';
$strMySQLReloaded = 'MySQL znovu na�teno.';
$strMySQLSaid = 'MySQL hl�s�: ';
$strMySQLServerProcess = 'MySQL %pma_s1% spu�t�n� na %pma_s2%, p�ihl�en %pma_s3%';
$strMySQLShowProcess = 'Zobrazit procesy';
$strMySQLShowStatus = 'Uk�zat MySQL informace o&nbsp;b�hu';
$strMySQLShowVars = 'Uk�zat MySQL syst�mov� prom�nn�';

$strName = 'N�zev';
$strNext = 'Dal��';
$strNo = 'Ne';
$strNoDatabases = '��dn� datab�ze';
$strNoDatabasesSelected = 'Nebyla vybr�na ��dn� datab�ze.';
$strNoDescription = '��dn� popisek';
$strNoDropDatabases = 'P��kaz "DROP DATABASE" je vypnut�.';
$strNoExplain = 'Bez vysv�tlen� (EXPLAIN) SQL';
$strNoFrames = 'phpMyAdmin se l�pe pou��v� v&nbsp;prohl�e�i podporuj�c�m r�my ("FRAME").';
$strNoIndex = '��dn� index nebyl definov�n!';
$strNoIndexPartsDefined = '��dn� ��st indexu nebyla definov�na!';
$strNoModification = '��dn� zm�na';
$strNoOptions = 'Tento form�t nem� ��dn� nastaven�';
$strNoPassword = '��dn� heslo';
$strNoPermission = 'Web server nem� opr�vn�n� ulo�it v�pis do souboru %s.';
$strNoPhp = 'Bez PHP k�du';
$strNoPrivileges = 'Bez opr�vn�n�';
$strNoQuery = '��dn� SQL dotaz!';
$strNoRights = 'Nem�te dostate�n� pr�va na proveden� t�to akce!';
$strNoSpace = 'Nedostatek m�sta pro ulo�en� souboru %s.';
$strNoTablesFound = 'V&nbsp;datab�zi nebyla nalezena ani jedna tabulka.';
$strNoUsersFound = '��dn� u�ivatel nenalezen.';
$strNoUsersSelected = 'Nebyl vybr�n ��dn� u�ivatel.';
$strNoValidateSQL = 'Bez kontroly SQL';
$strNone = '��dn�';
$strNotNumber = 'Toto nen� ��slo!';
$strNotOK = 'nen� OK';
$strNotSet = '<b>%s</b> tabulka nenalezena nebo nen� nastavena v&nbsp;%s';
$strNotValidNumber = ' nen� platn� ��slo ��dku!';
$strNull = 'Nulov�';
$strNumSearchResultsInTable = '%s odpov�daj�c�(ch) z�znam(�) v&nbsp;tabulce <i>%s</i>';
$strNumSearchResultsTotal = '<b>Celkem:</b> <i>%s</i> odpov�daj�c�(ch) z�znam(�)';
$strNumTables = 'Tabulek';

$strOK = 'OK';
$strOftenQuotation = '�asto uvozuj�c� znaky. Voliteln� znamen�, �e pouze polo�ky u kter�ch je to nutn� (obvykle typu CHAR a VARCHAR) jsou uzav�eny do uzav�rac�ch znak�.';
$strOperations = '�pravy';
$strOptimizeTable = 'Optimalizovat tabulku';
$strOptionalControls = 'Voliteln�. Ur�uje jak zapisovat nebo ��st speci�ln� znaky.';
$strOptionally = 'Voliteln�';
$strOptions = 'Vlastnosti';
$strOr = 'nebo';
$strOverhead = 'Nav�c';
$strOverwriteExisting = 'P�epsat existuj�c� soubor(y)';

$strPHP40203 = 'Pou��v�te PHP 4.2.3, kter� m� z�va�nou chybu p�i pr�ci s&nbsp;v�cebajtov�mi znaky (mbsting), jedn� se o&nbsp;chybu PHP ��slo 19404. Nedoporu�ujeme pou��vat tuto verzi PHP s&nbsp;phpMyAdminem.';
$strPHPVersion = 'Verze PHP';
$strPageNumber = 'Strana ��slo:';
$strPaperSize = 'Velikost str�nky';
$strPartialText = 'Zkr�cen� texty';
$strPassword = 'Heslo';
$strPasswordChanged = 'Heslo pro %s bylo �sp�n� zm�n�no.';
$strPasswordEmpty = 'Heslo je pr�zdn�!';
$strPasswordNotSame = 'Hesla nejsou stejn�!';
$strPdfDbSchema = 'Sch�ma datab�ze "%s" - Strana %s';
$strPdfInvalidPageNum = 'Nedefinovan� ��slo str�nky v&nbsp;PDF!';
$strPdfInvalidTblName = 'Tabulka "%s" neexistuje!';
$strPdfNoTables = '��dn� tabulky';
$strPerHour = 'za hodinu';
$strPerMinute = 'za minutu';
$strPerSecond = 'za sekundu';
$strPhoneBook = 'adres��';
$strPhp = 'Zobrazit PHP k�d';
$strPmaDocumentation = 'Dokumentace phpMyAdmina';
$strPmaUriError = 'Parametr <tt>$cfg[\'PmaAbsoluteUri\']</tt> MUS� b�t nastaven v&nbsp;konfigura�n�m souboru!';
$strPortrait = 'Na v��ku';
$strPos1 = 'Za��tek';
$strPrevious = 'P�edchoz�';
$strPrimary = 'Prim�rn�';
$strPrimaryKey = 'Prim�rn� kl��';
$strPrimaryKeyHasBeenDropped = 'Prim�rn� kl�� byl odstran�n';
$strPrimaryKeyName = 'Jm�no prim�rn�ho kl��e mus� b�t "PRIMARY"!';
$strPrimaryKeyWarning = '("PRIMARY" <b>mus�</b> b�t jm�no <b>pouze</b> prim�rn�ho kl��e!)';
$strPrint = 'Vytisknout';
$strPrintView = 'N�hled k vyti�t�n�';
$strPrivDescAllPrivileges = 'V�echna opr�vn�n� krom� GRANT.';
$strPrivDescAlter = 'Umo��uje m�nit strukturu existuj�c�ch tabulek.';
$strPrivDescCreateDb = 'Umo��uje vytv��et nov� datab�ze a tabulky.';
$strPrivDescCreateTbl = 'Umo��uje vytv��et nov� tabulky.';
$strPrivDescCreateTmpTable = 'Umo��uje vytv��et do�asn� tabulky.';
$strPrivDescDelete = 'Umo��uje mazat data.';
$strPrivDescDropDb = 'Umo��uje odstranit datab�ze a tabulky.';
$strPrivDescDropTbl = 'Umo��uje odstranit tabulky.';
$strPrivDescExecute = 'Umo�nuje spou�t�t ulo�en� procedury. V&nbsp;t�to verzi MySQL se nepou��v�.';
$strPrivDescFile = 'Umo��uje importovat a exportovat data z/do soubor� na serveru.';
$strPrivDescGrant = 'Umo��uje p�id�vat u�ivatele a opr�vn�n� bez znovuna��t�n� tabulek s&nbsp;opr�vn�n�mi.';
$strPrivDescIndex = 'Umo��uje vytv��et a ru�it indexy.';
$strPrivDescInsert = 'Umo��uje vkl�data a p�episovat data.';
$strPrivDescLockTables = 'Umo��uje zamknout tabulku pro aktu�ln� thread.';
$strPrivDescMaxConnections = 'Omez� po�et nov�ch p�ipojen�, kter� m��e u�ivatel vytvo�it za hodinu.';
$strPrivDescMaxQuestions = 'Omez� kolik dotaz� m��e u�ivatel odeslat serveru za hodinu.';
$strPrivDescMaxUpdates = 'Omez� kolik dotaz�, kter� m�n� nejakou tabulku nebo datab�zi, m��e u�ivatel spustit za hodinu.';
$strPrivDescProcess3 = 'Umo��uje zab�jet procesy jin�m u�ivatel�m.';
$strPrivDescProcess4 = 'Umo��uje vid�t kompletn� dotazy v&nbsp;seznamu proces�.';
$strPrivDescReferences = 'Nem� ��dn� vliv v&nbsp;t�to verzi MySQL.';
$strPrivDescReload = 'Umo��uje znovuna��t�n� nastaven� serveru a vypr�zdn�n� vyrovn�vac�ch pam�t� serveru.';
$strPrivDescReplClient = 'Umo�n� u�ivateli zjistit kde je hlavn� / pomocn� server.';
$strPrivDescReplSlave = 'Pot�ebn� pro replikaci pomocn�ch server�.';
$strPrivDescSelect = 'Umo��uje ��st data.';
$strPrivDescShowDb = 'D�v� p��stup k&nbsp;�pln�mu seznamu datab�z�.';
$strPrivDescShutdown = 'Umo��uje vypnout server.';
$strPrivDescSuper = 'Umo��uje p�ipojen� i kdy� je dosa�en maxim�ln� po�et p�ipojen�. Pot�ebn� pro v�t�inu operac� pro spr�vu serveru jako nastavov�n� glob�ln�ch prom�nn�ch a zab�jen� thread� jin�ch u�ivatel�.';
$strPrivDescUpdate = 'Umo��uje m�nit data.';
$strPrivDescUsage = '��dn� opr�vn�n�.';
$strPrivileges = 'Opr�vn�n�';
$strPrivilegesReloaded = 'Opr�vn�n� byla znovuna�tena �sp�n�.';
$strProcesslist = 'Seznam proces�';
$strProperties = 'Vlastnosti';
$strPutColNames = 'P�idat jm�na sloupc� na prvn� ��dek';

$strQBE = 'Dotaz';
$strQBEDel = 'smazat';
$strQBEIns = 'p�idat';
$strQueryFrame = 'SQL okno';
$strQueryFrameDebug = 'Ladic� informace';
$strQueryFrameDebugBox = 'Aktivn� prom�nn� pro formul�� dotazu:\nDB: %s\nTabulka: %s\nServer: %s\n\nSou4asn0 hodnoty prom2nn7ch pro formul85 dotazu:\nDB: %s\nTabulke: %s\nServer: %s\n\nOtev�eno z: %s\nFrameset: %s.';
$strQueryOnDb = 'SQL dotaz na datab�zi <b>%s</b>:';
$strQuerySQLHistory = 'SQL historie';
$strQueryStatistics = '<b>Statistika dotaz�</b>: Od spu�t�n� bylo serveru posl�no %s dotaz�.';
$strQueryTime = 'Dotaz zabral %01.4f sekund';
$strQueryType = 'Typ dotazu';
$strQueryWindowLock = 'Nep�episovat tento dotaz z&nbsp;hlavn�ho okna';

$strReType = 'Heslo znovu';
$strReceived = 'P�ijato';
$strRecords = 'Z�znam�';
$strReferentialIntegrity = 'Zkontrolovat integritu odkaz�:';
$strRelationNotWorking = 'N�kter� funkce pro pr�ci s&nbsp;propojen�mi tabulkami byly vypnuty. %sZde%s zjist�te pro�.';
$strRelationView = 'Zobrazit relace';
$strRelationalSchema = 'Rela�n� sch�ma';
$strRelations = 'Relace';
$strReloadFailed = 'Znovuna�ten� MySQL selhalo.';
$strReloadMySQL = 'Znovuna�ten� MySQL';
$strReloadingThePrivileges = 'Znovuna��t�m opr�vn�n�';
$strRememberReload = 'Nezapome�te znovu na��st server.';
$strRemoveSelectedUsers = 'Odstranit vybran� u�ivatele';
$strRenameTable = 'P�ejmenovat tabulku na';
$strRenameTableOK = 'Tabulka %s byla p�ejmenov�na na %s';
$strRepairTable = 'Opravit tabulku';
$strReplace = 'P�epsat';
$strReplaceNULLBy = 'Nahradit NULL hodnoty';
$strReplaceTable = 'P�epsat data tabulky souborem';
$strReset = 'P�vodn�';
$strResourceLimits = 'Omezen� zdroj�';
$strRevoke = 'Zru�it';
$strRevokeAndDelete = 'Odebrat u�ivatel�m ve�ker� opr�vn�n� a pot� je odstranit z tabulek.';
$strRevokeAndDeleteDescr = 'U�ivatel� budou m�t opr�vn�n� "USAGE" (pou��v�n�) dokud nebudou znovuna�tena opr�vn�n�.';
$strRevokeGrant = 'Zru�it opr�vn�n� p�id�lovat pr�va';
$strRevokeGrantMessage = 'Bylo zru�eno opr�vn�n� p�id�lovat pr�va pro %s';
$strRevokeMessage = 'Byla zru�ena pr�va pro %s';
$strRevokePriv = 'Zru�it pr�va';
$strRowLength = 'D�lka ��dku';
$strRowSize = ' Velikost ��dku ';
$strRows = '��dk�';
$strRowsFrom = '��dk� za��naj�c� od';
$strRowsModeFlippedHorizontal = 'vodorovn�m (oto�en� hlavi�ky)';
$strRowsModeHorizontal = 'vodorovn�m';
$strRowsModeOptions = 've %s re�imu a opakovat hlavi�ky po %s ��dc�ch.';
$strRowsModeVertical = 'svisl�m';
$strRowsStatistic = 'Statistika ��dk�';
$strRunQuery = 'Prov�st dotaz';
$strRunSQLQuery = 'Spustit SQL dotaz(y) na datab�zi %s';
$strRunning = 'na %s';
$strRussian = 'Ru�tina';

$strSQL = 'SQL';
$strSQLOptions = 'Nastaven� SQL exportu';
$strSQLParserBugMessage = 'Je mo�n�, �e jste na�li chybu v&nbsp;SQL parseru. Pros�m prozkoumejte podrobn� SQL dotaz, p�edev��m jestli jsou spr�vn� uvozovky a jestli nejsou proh�zen�. Dal�� mo�nost selh�n� je pokud nahr�v�te soubor s&nbsp;bin�rn�mi daty nezapsan�mi v&nbsp;uvozovk�ch. M��ete tak� vyzkou�et p��kazovou ��dku MySQL. N�e uveden� v�stup z&nbsp;MySQL serveru (pokud je n�jak�) V�m tak� m��e pomoci p�i zkoum�n� probl�mu. Pokud st�le m�te probl�my nebo pokud SQL parser ohl�s� chybu u dotazu, kter� na p��kazov� ��dce funguje, pros�m pokuste se zredukovat dotaz na co nejmen��, ve kter�m se probl�m je�t� vyskytne, a ohla�te chybu na str�nk�ch phpMyAdmina spolu se sekc� V�PIS uvedenou n�e:';
$strSQLParserUserError = 'Pravd�podobn� m�te v&nbsp;SQL dotazu chybu. N�e uveden� v�stup MySQL serveru (pokud je n�jak�) V�m tak� m��e pomoci p�i zkoum�n� probl�mu';
$strSQLQuery = 'SQL-dotaz';
$strSQLResult = 'V�sledek SQL dotazu';
$strSQPBugInvalidIdentifer = 'Chybn� identifik�tor';
$strSQPBugUnclosedQuote = 'Neuzav�en� uvozovky';
$strSQPBugUnknownPunctuation = 'Nezn�m� interpunk�n� znam�nko';
$strSave = 'Ulo�';
$strSaveOnServer = 'Ulo�it na serveru v adres��i %s';
$strScaleFactorSmall = 'M���tko je p��li� mal�, aby se sch�ma ve�lo na jednu str�nku';
$strSearch = 'Vyhled�v�n�';
$strSearchFormTitle = 'Vyhled�v�n� v&nbsp;datab�zi';
$strSearchInTables = 'V&nbsp;tabulk�ch:';
$strSearchNeedle = 'Slova nebo hodnoty, kter� chcete vyhledat (z�stupn� znak: "%"):';
$strSearchOption1 = 'alespo� jedno ze slov';
$strSearchOption2 = 'v�echna slova';
$strSearchOption3 = 'p�esnou fr�zi';
$strSearchOption4 = 'jako regul�rn� v�raz';
$strSearchResultsFor = 'V�sledny vyhled�v�n� pro "<i>%s</i>" %s:';
$strSearchType = 'Naj�t:';
$strSecretRequired = 'Nastavte kl�� pro �ifrov�n� cookies v&nbsp;kofigura�n�m souboru (blowfish_secret).';
$strSelect = 'Vybrat';
$strSelectADb = 'Pros�m vyberte datab�zi';
$strSelectAll = 'Vybrat v�e';
$strSelectFields = 'Zvolte sloupec (alespo� jeden):';
$strSelectNumRows = 'v&nbsp;dotazu';
$strSelectTables = 'Vybrat tabulky';
$strSend = 'Do souboru';
$strSent = 'Odesl�no';
$strServer = 'Server %s';
$strServerChoice = 'V�b�r serveru';
$strServerStatus = 'Stav serveru';
$strServerStatusUptime = 'Tento MySQL server be�� %s. �as spu�t�n�: %s.';
$strServerTabProcesslist = 'Procesy';
$strServerTabVariables = 'Prom�nn�';
$strServerTrafficNotes = '<b>Provoz serveru</b>: Informace o&nbsp;s�ov�m provozu MySQL serveru od jeho spu�t�n�.';
$strServerVars = 'Prom�nn� a nastaven� serveru';
$strServerVersion = 'Verze MySQL';
$strSessionValue = 'Hodnota sezen�';
$strSetEnumVal = 'Pokud je sloupec typu "enum" nebo "set", zad�vejte hodnoty v&nbsp;n�sleduj�c�m form�tu: \'a\',\'b\',\'c\'...<br />Pokud pot�ebujete zadat zp�tn� lom�tko ("\") nebo jednoduch� uvozovky ("\'") mezi t�mito hodnotami, napi�te p�ed n� zp�tn� lom�tko (p��klad: \'\\\\xyz\' nebo \'a\\\'b\').';
$strShow = 'Zobrazit';
$strShowAll = 'Zobrazit v�e';
$strShowColor = 'Barevn� �ipky';
$strShowCols = 'Zobrazit sloupce';
$strShowDatadictAs = 'Form�t datov�ho slovn�ku';
$strShowFullQueries = 'Zobrazit cel� dotazy';
$strShowGrid = 'Zobrazit m��ku';
$strShowPHPInfo = 'Zobrazit informace o&nbsp;PHP';
$strShowTableDimension = 'Rozm�ry tabulek';
$strShowTables = 'Zobrazit tabulky';
$strShowThisQuery = 'Zobrazit zde tento dotaz znovu';
$strShowingRecords = 'Zobrazeny z�znamy';
$strSimplifiedChinese = 'Zjedodu�en� ��n�tina';
$strSingly = '(po jednom)';
$strSize = 'Velikost';
$strSort = '�adit';
$strSpaceUsage = 'Vyu�it� m�sta';
$strSplitWordsWithSpace = 'Slova jsou odd�lena mezerou (" ").';
$strStatCheckTime = 'Posledn� kontrola';
$strStatCreateTime = 'Vytvo�en�';
$strStatUpdateTime = 'Posledn� zm�na';
$strStatement = '�daj';
$strStatus = 'Stav';
$strStrucCSV = 'CSV data';
$strStrucData = 'Strukturu a data';
$strStrucDrop = 'P�idej \'DROP TABLE\'';
$strStrucExcelCSV = 'CSV data pro MS Excel';
$strStrucOnly = 'Pouze strukturu';
$strStructPropose = 'Navrhnout strukturu tabulky';
$strStructure = 'Struktura';
$strSubmit = 'Ode�li';
$strSuccess = 'V� SQL-dotaz byl �sp�n� vykon�n';
$strSum = 'Celkem';
$strSwedish = '�v�dsky';
$strSwitchToTable = 'P�epnout na zkop�rovanou tabulku';

$strTable = 'Tabulka';
$strTableComments = 'Koment�� k tabulce';
$strTableEmpty = 'Jm�no tabulky je pr�zdn�!';
$strTableHasBeenDropped = 'Tabulka %s byla odstran�na';
$strTableHasBeenEmptied = 'Tabulka %s byla vypr�zdn�na';
$strTableHasBeenFlushed = 'Vyrovn�vac� pam� pro tabulku %s byla vypr�zdn�na';
$strTableMaintenance = ' �dr�ba tabulky ';
$strTableOfContents = 'Obsah';
$strTableOptions = 'Parametry tabulky';
$strTableStructure = 'Struktura tabulky';
$strTableType = 'Typ tabulky';
$strTables = '%s tabulek';
$strTblPrivileges = 'Opr�vn�n� pro jednotliv� tabulky';
$strTextAreaLength = 'Tento sloupec mo�n� nep�jde <br />(kv�li d�lce) upravit ';
$strThai = 'Thaj�tina';
$strTheContent = 'Obsah souboru byl vlo�en';
$strTheContents = 'Obsah souboru p�ep�e obsah zvolen� tabulky v&nbsp;t�ch ��dc�ch, kde je stejn� prim�rn� nebo unik�tn� kl��.';
$strTheTerminator = 'Sloupce jsou odd�leny t�mto znakem.';
$strThisHost = 'Tento po��ta�';
$strThisNotDirectory = 'Nebyl zad�n adres��';
$strThreadSuccessfullyKilled = 'Vl�kno %s bylo �sp�ne zabito.';
$strTime = '�as';
$strToggleScratchboard = 'Zobrazit grafick� n�vrh';
$strTotal = 'celkem';
$strTotalUC = 'Celkem';
$strTraditionalChinese = 'Tradi�n� ��n�tina';
$strTraffic = 'Provoz';
$strTransformation_image_jpeg__inline = 'Zobraz� n�hled obr�zku s&nbsp;odkazem na obr�zek; parametry ���ka a v��ka v&nbsp;bodech (pom�r stran obr�zku z�stane zachov�n)';
$strTransformation_image_jpeg__link = 'Zobraz� odkaz na obr�zek (nap��klad st�hnut� pole blob).';
$strTransformation_image_png__inline = 'Viz image/jpeg: inline';
$strTransformation_text_plain__dateformat = 'Zobraz� datum nebo �as (TIME, TIMESTAMP a DATETIME) podle m�stn�ho nastaven�. Prvn� parametr je posun (v&nbsp;hodin�ch), kter� bude p�id�n k&nbsp;�asu (v�choz� je 0). Druh� parametr je form�tovac� �et�zec pro funkci strftime().';
$strTransformation_text_plain__external = 'JEN PRO LINUX: Spust� extern� program, na jeho standardn� vstup po�le obsah pole a zobraz� v�stup programu. V�choz� je program Tidy, kter� p�kn� zform�tuje HTML. Z&nbsp;bezpe�nostn�ch d�vod� mus�te jm�na povolen�ch program� zapsat do souboru libraries/transformations/text_plain__external.inc.php. Prvn� parametr je ��slo programu, kter� m� b�t spu�t�n a druh� parametr ud�v� parametry tohoto programu. T�et� parametr ur�uje, zda maj� b�t ve v�stupu nahrazeny HTML entity (nap�. pro zobrazen� zdrojov�ho k�du HTML) (v�choz� je 1, tedy p�ev�d�t na entity), �tvrt� (p�i nastaven� na 1) zajist� p�id�n� parametru NOWRAP k&nbsp;vypisovan�mu textu, ��m� se zachov� form�tov�n� (v�choz� je 1).';
$strTransformation_text_plain__formatted = 'Zachov� p�vodn� form�tov�n� sloupce, tak jak je ulo�en v&nbsp;datab�zi.';
$strTransformation_text_plain__imagelink = 'Zobraz� obr�zek a odkaz z&nbsp;pole obsahuj�c�ho odkaz na obr�zek. Prvn� parametr je prefix URL (nap��klad "http://mojedomena.cz/", druh� a t�et� ���ku a v��ku obr�zku.';
$strTransformation_text_plain__link = 'Zobraz� odkaz z&nbsp;pole obsahuj�c� odkaz. Prvn� parametr je prefix URL (nap��klad "http://mojedomena.cz/", druh� text odkazu.';
$strTransformation_text_plain__substr = 'Zobraz� jen ��st textu. Prvn� parametr je posun od za��tku (v�choz� je 0) a druh� ur�uje d�lku textu, kter� se m� zobrazit, pokud nen� uveden, bude zobrazen zbytek textu. T�et� parametr u�uje jak� text m� b�t p�id�n za zkr�cen� text (v�choz� je ...).';
$strTransformation_text_plain__unformatted = 'Zobraz� text pomoc� HTML entit, p��padn� HTML se zobraz� v&nbsp;p�vodn�m tvaru.';
$strTruncateQueries = 'Zobrazit zkr�cen� dotazy';
$strTurkish = 'Turecky';
$strType = 'Typ';

$strUkrainian = 'Ukrajinsky';
$strUncheckAll = 'Od�krtnout v�e';
$strUnicode = 'Unicode';
$strUnique = 'Unik�tn�';
$strUnknown = 'nezn�m�';
$strUnselectAll = 'Odzna�it v�e';
$strUpdComTab = 'Pod�vejte se pros�m do dokumentace, jak aktualizovat tabulku s&nbsp;informacemi o&nbsp; sloupc�ch (Column_comments Table)';
$strUpdatePrivMessage = 'Byla aktualizovana opr�vn�n� pro %s.';
$strUpdateProfile = 'Zm�ny p��stupu:';
$strUpdateProfileMessage = 'P��stup byl zm�n�n.';
$strUpdateQuery = 'Aktualizovat dotaz';
$strUsage = 'Pou��v�';
$strUseBackquotes = 'Pou��t zp�tn� uvozovky u&nbsp;jmen tabulek a sloupc�';
$strUseHostTable = 'Pou��t tabulku s&nbsp;po��ta�i';
$strUseTables = 'Pou��t tabulky';
$strUseTextField = 'Pou��t textov� pole';
$strUseThisValue = 'Pou��t tuto hodnotu';
$strUser = 'U�ivatel';
$strUserAlreadyExists = 'U�ivatel %s ji� existuje!';
$strUserEmpty = 'Jm�no u�ivatele je pr�zdn�!';
$strUserName = 'Jm�no u�ivatele';
$strUserNotFound = 'Zvolen� u�ivatel nebyl nalezen v&nbsp;tabulce opr�vn�n�.';
$strUserOverview = 'P�ehled u�ivatel�';
$strUsers = 'U�ivatel�';
$strUsersDeleted = 'Vybran� u�ivatel� byli �sp�n� odstran�ni.';
$strUsersHavingAccessToDb = 'U�ivatel� maj�c� p��stup k&nbsp;&quot;%s&quot;';

$strValidateSQL = 'Zkontrolovat SQL';
$strValidatorError = 'SQL validator nemohl b�t inicializov�n. Pros�m zkontrolujte jestli m�te nainstalovan� pot�ebn� roz���en� php, jak je pops�no v&nbsp;%sdokumentaci%s.';
$strValue = 'Hodnota';
$strVar = 'Prom�nn�';
$strViewDump = 'Export tabulky';
$strViewDumpDB = 'Export datab�ze';
$strViewDumpDatabases = 'Export datab�z�';

$strWebServerUploadDirectory = 'soubor z&nbsp;adres��e pro upload';
$strWebServerUploadDirectoryError = 'Adres�� ur�en� pro upload soubor� nemohl b�t otev�en';
$strWelcome = 'V�tej v&nbsp;%s';
$strWestEuropean = 'Z�padn� Evropa';
$strWildcard = 'maska';
$strWindowNotFound = 'C�lov� onko prohl�e�e nemohlo b�y aktualizov�no. Mo�n� jste zav�el rodi�ovsk� okno, nebo prohl�e� blokuje operace mezi okny z d�vodu bezpe�nostn�ch nastaven�.';
$strWithChecked = 'Za�krtnut�:';
$strWritingCommentNotPossible = 'Nelze zapsat koment��';
$strWritingRelationNotPossible = 'Nelze zapsat relaci';
$strWrongUser = '�patn� u�ivatelsk� jm�no nebo heslo. P��stup odep�en.';

$strXML = 'XML';

$strYes = 'Ano';

$strZeroRemovesTheLimit = 'Pozn�mka: Nastaven� t�chto parametr� na 0 (nulu) odstran� omezen�.';
$strZip = '"zazipov�no"';
// To translate

?>
