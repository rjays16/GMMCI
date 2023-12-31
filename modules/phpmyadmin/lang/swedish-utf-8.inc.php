<?php
/* $Id: swedish-utf-8.inc.php,v 1.2 2005/10/29 20:08:12 kaloyan_raev Exp $ */

$charset = 'utf-8';
$allow_recoding = TRUE;
$text_dir = 'ltr';
$left_font_family = 'verdana, arial, helvetica, geneva, sans-serif';
$right_font_family = 'arial, helvetica, geneva, sans-serif';
$number_thousands_separator = ' ';
$number_decimal_separator = ',';
// shortcuts for Byte, Kilo, Mega, Giga, Tera, Peta, Exa
$byteUnits = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB');

$day_of_week = array('Sön', 'Mån', 'Tis', 'Ons', 'Tors', 'Fre', 'Lör');
$month = array('januari', 'februari', 'mars', 'april', 'maj', 'juni', 'juli', 'augusti', 'september', 'oktober', 'november', 'december');
// See http://www.php.net/manual/en/function.strftime.php to define the
// variable below
$datefmt = '%d %B %Y kl %H:%M';
$timespanfmt = '%s dagar, %s timmar, %s minuter och %s sekunder';

$strAPrimaryKey = 'En primär nyckel har lagts till för %s';
$strAbortedClients = 'Avbrutna';
$strAbsolutePathToDocSqlDir = 'Ange absolut sökväg på webbservern till docSQL-katalog';
$strAccessDenied = 'Åtkomst nekad';
$strAccessDeniedExplanation = 'phpMyAdmin försökte skapa en förbindelse till MySQL-servern, men servern nekade uppkopplingen. Kontrollera värd, användarnamn och lösenord i config.inc.php och förvissa dig om att de stämmer överens med informationen från administratören av MySQL-servern.';
$strAction = 'Åtgärd';
$strAddAutoIncrement = 'Lägg till AUTO_INCREMENT-värde';
$strAddDeleteColumn = 'Lägg till/ta bort fältkolumner';
$strAddDeleteRow = 'Lägg till/ta bort villkorsrader';
$strAddDropDatabase = 'Lägg till \'DROP DATABASE\'';
$strAddIntoComments = 'Lägg till i kommentarer';
$strAddNewField = 'Lägg till fält';
$strAddPriv = 'Lägg till ett nytt privilegium';
$strAddPrivMessage = 'Du har lagt till ett nytt privilegium.';
$strAddPrivilegesOnDb = 'Lägg till privilegier till följande databas';
$strAddPrivilegesOnTbl = 'Lägg till privilegier till följande tabell';
$strAddSearchConditions = 'Lägg till sökvillkor (uttryck i "where"-sats):';
$strAddToIndex = 'Lägg till&nbsp;%s&nbsp;kolumn(er) till index';
$strAddUser = 'Lägg till ny användare';
$strAddUserMessage = 'Du har lagt till en ny användare.';
$strAddedColumnComment = 'La till kommentar för kolumn';
$strAddedColumnRelation = 'La till relation för kolumn';
$strAdministration = 'Administration';
$strAffectedRows = 'Påverkade rader:';
$strAfter = 'Efter %s';
$strAfterInsertBack = 'Gå tillbaka till föregående sida';
$strAfterInsertNewInsert = 'Lägg till ytterligare en ny rad';
$strAll = 'Alla';
$strAllTableSameWidth = 'Visa alla tabeller med samma bredd';
$strAlterOrderBy = 'Sortera om tabellen efter';
$strAnIndex = 'Ett index har lagts till för %s';
$strAnalyzeTable = 'Analysera tabell';
$strAnd = 'Och';
$strAny = 'Vem som helst';
$strAnyColumn = 'Vilken kolumn som helst';
$strAnyDatabase = 'Vilken databas som helst';
$strAnyHost = 'Vilken värd som helst';
$strAnyTable = 'Vilken tabell som helst';
$strAnyUser = 'Vilken användare som helst';
$strArabic = 'Arabisk';
$strArmenian = 'Armenisk';
$strAscending = 'Stigande';
$strAtBeginningOfTable = 'I början av tabellen';
$strAtEndOfTable = 'I slutet av tabellen';
$strAttr = 'Attribut';
$strAutodetect = 'Autodetektera';
$strAutomaticLayout = 'Automatisk layout';

$strBack = 'Bakåt';
$strBaltic = 'Baltisk';
$strBeginCut = 'START URKLIPP';
$strBeginRaw = 'START RÅTEXT';
$strBinary = 'Binär';
$strBinaryDoNotEdit = 'Binär - ändra inte';
$strBookmarkDeleted = 'Bokmärket har raderats.';
$strBookmarkLabel = 'Etikett';
$strBookmarkQuery = 'Bokmärkt SQL-fråga';
$strBookmarkThis = 'Skapa bokmärke för den här SQL-frågan';
$strBookmarkView = 'Visa endast';
$strBrowse = 'Visa';
$strBrowseForeignValues = 'Visa främmande värden';
$strBulgarian = 'Bulgarisk';
$strBzError = 'phpMyAdmin kunde inte komprimera SQL-satserna på grund av en trasig Bz2-utvidgning i denna php-version. Det rekommenderas starkt att sätta direktivet <code>$cfg[\'BZipDump\']</code> i din phpMyAdmin-konfigurationsfil till <code>FALSE</code>. Om du vill använda Bz2-komprimering, bör du uppgradera till en senare php-version. Se php:s buggrapport %s för detaljer.';
$strBzip = '"bzippad"';

$strCSVOptions = 'CSV-alternativ';
$strCannotLogin = 'Kan ej logga in på MySQL-server';
$strCantLoad = 'kan inte ladda %s-tillägg,<br />var god kontrollera PHP-konfigurationen.';
$strCantLoadMySQL = 'kan inte ladda MySQL-tillägg,<br />var god och kontrollera PHP-konfigurationen.';
$strCantLoadRecodeIconv = 'Kan inte ladda utökningarna iconv eller recode som behövs för teckenuppsättningsomvandling. Konfigurera php för att tillåta dessa utökningar eller stäng av teckenuppsättningsomvandling i phpMyAdmin.';
$strCantRenameIdxToPrimary = 'Kan inte byta namn på index till "PRIMARY"!';
$strCantUseRecodeIconv = 'Kan inte använda funktionerna iconv, libiconv eller recode_string när utökade rapporter ska laddas. Kontrollera din php-konfiguration.';
$strCardinality = 'Kardinalitet';
$strCarriage = 'Vagnretur: \\r';
$strCaseInsensitive = 'skiftlägesokänsligt';
$strCaseSensitive = 'skiftlägeskänsligt';
$strCentralEuropean = 'Centraleuropeisk';
$strChange = 'Ändra';
$strChangeCopyMode = 'Skapa en ny användare med samma privilegier och ...';
$strChangeCopyModeCopy = '... behåll den gamla.';
$strChangeCopyModeDeleteAndReload = ' ... ta bort den gamla från användartabellerna och ladda om privilegierna efteråt.';
$strChangeCopyModeJustDelete = ' ... ta bort den gamla från användartabellerna.';
$strChangeCopyModeRevoke = ' ... upphäv alla aktiva privilegier från dan gamla och ta bort den efteråt.';
$strChangeCopyUser = 'Ändra inloggningsinformation / Kopiera användare';
$strChangeDisplay = 'Välj fält som ska visas';
$strChangePassword = 'Byt lösenord';
$strCharset = 'Teckenuppsättning';
$strCharsetOfFile = 'Filens teckenuppsättning:';
$strCharsets = 'Teckenuppsättningar';
$strCharsetsAndCollations = 'Teckenuppsättningar och kollationeringar';
$strCheckAll = 'Markera alla';
$strCheckDbPriv = 'Kontrollera databasprivilegier';
$strCheckPrivs = 'Kontrollera privilegier';
$strCheckPrivsLong = 'Kontrollera privilegier för databas &quot;%s&quot;.';
$strCheckTable = 'Kontrollera tabell';
$strChoosePage = 'Välj en sida att redigera';
$strColComFeat = 'Visning av kolumnkommentarer';
$strCollation = 'Kollationering';
$strColumn = 'Kolumn';
$strColumnNames = 'Kolumn-namn';
$strColumnPrivileges = 'Kolumnspecifika privilegier';
$strCommand = 'Kommando';
$strComments = 'Kommentarer';
$strCompleteInserts = 'Kompletta infogningar';
$strCompression = 'Komprimering';
$strConfigFileError = 'phpMyAdmin kunde inte läsa din konfigurationsfil!<br />Detta kan inträffa om php hittar ett fel i den eller om php inte hittar filen.<br />Anropa konfigurationsfilen direkt mha länken nedan och läs php:s felmeddelande(n) som du erhåller. I de flesta fall saknas ett citationstecken eller ett semikolon någonstans.<br />Om du erhåller en tom sida är allt bra.';
$strConfigureTableCoord = 'Var god ange koordinaterna för tabellen %s';
$strConfirm = 'Vill du verkligen göra det?';
$strConnections = 'Uppkopplingar';
$strCookiesRequired = 'Kakor (cookies) måste tillåtas för att gå vidare.';
$strCopyTable = 'Kopiera tabellen till (databas<b>.</b>tabell):';
$strCopyTableOK = 'Tabellen %s har kopierats till %s.';
$strCopyTableSameNames = 'Kan inte kopiera tabell till samma namn!';
$strCouldNotKill = 'phpMyAdmin kunde inte döda tråd %s. Troligtvis har den redan avslutats.';
$strCreate = 'Skapa';
$strCreateIndex = 'Skapa ett index för&nbsp;%s&nbsp;kolumn(er)';
$strCreateIndexTopic = 'Skapa ett nytt index';
$strCreateNewDatabase = 'Skapa ny databas';
$strCreateNewTable = 'Skapa ny tabell i databas %s';
$strCreatePage = 'Skapa en ny sida';
$strCreatePdfFeat = 'Skapande av PDF-sidor';
$strCriteria = 'Villkor';
$strCroatian = 'Kroatisk';
$strCyrillic = 'Kyrillisk';
$strCzech = 'Tjeckisk';

$strDBComment = 'Databaskommentar: ';
$strDBGContext = 'Innehåll';
$strDBGContextID = 'Innehålls-ID';
$strDBGHits = 'Träffar';
$strDBGLine = 'Rad';
$strDBGMaxTimeMs = 'Max tid, ms';
$strDBGMinTimeMs = 'Min tid, ms';
$strDBGModule = 'Modul';
$strDBGTimePerHitMs = 'Tid/träff, ms';
$strDBGTotalTimeMs = 'Total tid, ms';
$strDanish = 'Dansk';
$strData = 'Data';
$strDataDict = 'Datalexikon';
$strDataOnly = 'Enbart data';
$strDatabase = 'Databas ';
$strDatabaseExportOptions = 'Exportalternativ för databas';
$strDatabaseHasBeenDropped = 'Databasen %s har tagits bort.';
$strDatabaseNoTable = 'Denna databas innehåller ingen tabell!';
$strDatabaseWildcard = 'Databas (jokertecken tillåtna):';
$strDatabases = 'Databaser';
$strDatabasesDropped = '%s databaser har tagits bort.';
$strDatabasesStats = 'Databas-statistik';
$strDatabasesStatsDisable = 'Stäng av statistik';
$strDatabasesStatsEnable = 'Slå på statistik';
$strDatabasesStatsHeavyTraffic = 'Anm: Att slå på databastatistik här kan orsaka tung trafik mellan webb- och MySQL-servern.';
$strDbPrivileges = 'Databasspecifika privilegier';
$strDbSpecific = 'databasspecifik';
$strDefault = 'Standard';
$strDefaultValueHelp = 'För standardvärden, ange endast ett enstaka värde, utan bakåtstreck eller citattecken, i formatet: a';
$strDelOld = 'Nuvarande sida har referenser till tabeller som inte längre existerar. Vill du ta bort dessa referenser?';
$strDelete = 'Radera';
$strDeleteAndFlush = 'Ta bort användarna och ladda om privilegierna efteråt.';
$strDeleteAndFlushDescr = 'Detta är det renaste sättet, men omladdning av privilegierna kan ta en stund.';
$strDeleteFailed = 'Raderingen misslyckades!';
$strDeleteUserMessage = 'Du har tagit bort användaren %s.';
$strDeleted = 'Raden har raderats';
$strDeletedRows = 'Raderade rader';
$strDeleting = 'Tar bort %s';
$strDescending = 'Fallande';
$strDescription = 'Beskrivning';
$strDictionary = 'lexikon';
$strDisabled = 'Avaktiverat';
$strDisplay = 'Visa';
$strDisplayFeat = 'Visningsfunktionaliteter';
$strDisplayOrder = 'Visningsordning:';
$strDisplayPDF = 'Visa PDF-schema';
$strDoAQuery = 'Utför en "Query by Example" (jokertecken: "%")';
$strDoYouReally = 'Vill du verkligen ';
$strDocu = 'Dokumentation';
$strDrop = 'Radera';
$strDropDB = 'Radera databas %s';
$strDropSelectedDatabases = 'Ta bort markerade databaser';
$strDropTable = 'Radera tabell';
$strDropUsersDb = 'Ta bort databaserna med samma namn som användarna.';
$strDumpComments = 'Inkludera kolumnkommentarer som kommentarer bland SQL-satserna';
$strDumpSaved = 'SQL-satserna har sparats till filen %s.';
$strDumpXRows = 'Visa %s rader med början på rad %s.';
$strDumpingData = 'Data i tabell';
$strDynamic = 'dynamisk';

$strEdit = 'Ändra';
$strEditPDFPages = 'Redigera PDF-sidor';
$strEditPrivileges = 'Ändra privilegier';
$strEffective = 'Effektivt';
$strEmpty = 'Töm';
$strEmptyResultSet = 'MySQL skickade tillbaka ett tomt resultat (dvs inga rader).';
$strEnabled = 'Aktiverat';
$strEnd = 'Slutet';
$strEndCut = 'SLUT URKLIPP';
$strEndRaw = 'SLUT RÅTEXT';
$strEnglish = 'Engelsk';
$strEnglishPrivileges = ' Obs! MySQL-privilegiumnamn anges på engelska ';
$strError = 'Fel';
$strEstonian = 'Estnisk';
$strExcelOptions = 'Excel-alternativ';
$strExecuteBookmarked = 'Utför bokmärkt fråga';
$strExplain = 'Förklara SQL-kod';
$strExport = 'Exportera';
$strExportToXML = 'Exportera till XML-format';
$strExtendedInserts = 'Utökade infogningar';
$strExtra = 'Extra';

$strFailedAttempts = 'Misslyckade försök';
$strField = 'Fält';
$strFieldHasBeenDropped = 'Fältet %s har tagits bort';
$strFields = 'Fält';
$strFieldsEmpty = ' Antalet fält är noll! ';
$strFieldsEnclosedBy = 'Fälten omges av';
$strFieldsEscapedBy = 'Specialtecken i fält föregås av';
$strFieldsTerminatedBy = 'Fälten avslutas med';
$strFileAlreadyExists = 'Filen %s finns redan på servern, ändra filnamnet eller kryssa i skriv över-alternativet.';
$strFileCouldNotBeRead = 'Filen kunde inte läsas';
$strFileNameTemplate = 'Mall för filnamn';
$strFileNameTemplateHelp = 'Använd __DB__ för databasnamn, __TABLE__ för tabellnamn och %sstrftime%s-alternativ för specificering av tid. Filändelse läggs till automagiskt. All annan text kommer att bevaras.';
$strFileNameTemplateRemember = 'kom ihåg mall';
$strFixed = 'fast';
$strFlushPrivilegesNote = 'Anm: phpMyAdmin hämtar användarnas privilegier direkt från MySQL:s privilegiumtabeller. Innehållet i dessa tabeller kan skilja sig från privilegierna som servern använder ifall manuella ändringar har gjorts. I detta fall bör du %sladda om privilegierna%s innan du fortsätter.';
$strFlushTable = 'Rensa tabellen ("FLUSH TABLE")';
$strFormEmpty = 'Värde saknas i formuläret!';
$strFormat = 'Format';
$strFullText = 'Fullständiga texter';
$strFunction = 'Funktion';

$strGenBy = 'Genererad av';
$strGenTime = 'Skapad';
$strGeneralRelationFeat = 'Allmänna relationsfunktionaliteter';
$strGerman = 'Tysk';
$strGlobal = 'global';
$strGlobalPrivileges = 'Globala privilegier';
$strGlobalValue = 'Globalt värde';
$strGo = 'Kör';
$strGrantOption = 'Grant';
$strGrants = 'Behörigheter';
$strGreek = 'Grekisk';
$strGzip = '"gzippad"';

$strHasBeenAltered = 'har ändrats.';
$strHasBeenCreated = 'har skapats.';
$strHaveToShow = 'Du måste välja minst en kolumn som ska visas';
$strHebrew = 'Hebreisk';
$strHome = 'Hem';
$strHomepageOfficial = 'phpMyAdmin:s officiella hemsida';
$strHomepageSourceforge = 'phpMyAdmin Sourceforge-nedladdningssida';
$strHost = 'Värd';
$strHostEmpty = 'Värdnamnet är tomt!';
$strHungarian = 'Ungersk';

$strId = 'ID';
$strIdxFulltext = 'Heltext';
$strIfYouWish = 'Om du vill ladda enbart några av tabellens kolumner, ange en kommaseparerad fältlista.';
$strIgnore = 'Ignorera';
$strIgnoringFile = 'Ignorerar fil %s';
$strImportDocSQL = 'Importera docSQL-filer';
$strImportFiles = 'Importera filer';
$strImportFinished = 'Importen avslutad';
$strInUse = 'används';
$strIndex = 'Index';
$strIndexHasBeenDropped = 'Index %s har tagits bort';
$strIndexName = 'Indexnamn&nbsp;:';
$strIndexType = 'Indextyp&nbsp;:';
$strIndexes = 'Index';
$strInnodbStat = 'InnoDB-status';
$strInsecureMySQL = 'Din konfigurationsfil innehåller inställningar (root-konto utan lösenord) som motsvarar MySQL:s privilegierade standardkonto. Din MySQL-server körs med denna standardinställning och är öppen för intrång, så du bör verkligen täppa till detta säkerhetshål.';
$strInsert = 'Lägg till';
$strInsertAsNewRow = 'Lägg till som ny rad';
$strInsertNewRow = 'Lägg till ny rad';
$strInsertTextfiles = 'Importera data från textfil till tabellen';
$strInsertedRowId = 'Tillagd rads id:';
$strInsertedRows = 'Tillagda rader:';
$strInstructions = 'Instruktioner';
$strInvalidName = '"%s" är ett reserverat ord, du kan inte använda det som ett namn på en databas/tabell/fält.';

$strJapanese = 'Japansk';
$strJumpToDB = 'Hoppa till databas &quot;%s&quot;.';
$strJustDelete = 'Ta bara bort användarna från privilegiumtabellerna.';
$strJustDeleteDescr = 'De &quot;borttagna&quot; användarna kommer fortfarande kunna komma åt servern som vanligt tills privilegierna laddas om.';

$strKeepPass = 'Ändra inte lösenordet';
$strKeyname = 'Nyckel';;
$strKill = 'Döda';
$strKorean = 'Koreansk';

$strLaTeX = 'LaTeX';
$strLaTeXOptions = 'LaTeX-alternativ';
$strLandscape = 'Liggande';
$strLength = 'Längd';
$strLengthSet = 'Längd/Värden*';
$strLimitNumRows = 'Rader per sida';
$strLineFeed = 'Radframmatning: \\n';
$strLines = 'Rader';
$strLinesTerminatedBy = 'Raderna avslutas med';
$strLinkNotFound = 'Länk ej funnen';
$strLinksTo = 'Länkar till';
$strLithuanian = 'Litauisk';
$strLoadExplanation = 'Den bästa metoden är förvald, men du kan byta om den inte fungerar.';
$strLoadMethod = 'LOAD-metod';
$strLocalhost = 'Lokal';
$strLocationTextfile = 'Textfilens plats';
$strLogPassword = 'Lösenord:';
$strLogUsername = 'Användarnamn:';
$strLogin = 'Logga in';
$strLoginInformation = 'Inloggningsinformation';
$strLogout = 'Logga ut';

$strMIME_MIMEtype = 'MIME-typ';
$strMIME_available_mime = 'Tillgängliga MIME-typer';
$strMIME_available_transform = 'Tillgängliga omvandlingar';
$strMIME_description = 'Beskrivning';
$strMIME_file = 'Filnamn';
$strMIME_nodescription = 'Beskrivning för denna omvandling finns inte tillgänglig.<br />Vänligen fråga upphovsmannen vad %s gör.';
$strMIME_transformation = 'Webbläsaromvandling';
$strMIME_transformation_note = 'För en lista med tillgängliga omvandlingsparametrar och deras MIME-typomvandlingar, klicka på %somvandlingsbeskrivningar%s';
$strMIME_transformation_options = 'Omvandlingsparametrar';
$strMIME_transformation_options_note = 'Ange värdena för omvandlingsparametrar enligt följande format: \'a\',\'b\',\'c\'...<br />Om du behöver lägga till ett bakåtstreck ("\") eller ett enkelcitat ("\'") i värdena, skriv ett bakåtstreck före tecknet (t.ex. \'\\\\xyz\' eller \'a\\\'b\').';
$strMIME_without = 'Kursiverade MIME-typer har inte någon separat omvandlingsfunktion';
$strMissingBracket = 'Parantes saknas';
$strModifications = 'Ändringarna har sparats';
$strModify = 'Ändra';
$strModifyIndexTopic = 'Ändra ett index';
$strMoreStatusVars = 'Fler statusvariabler';
$strMoveTable = 'Flytta tabellen till (databas<b>.</b>tabell):';
$strMoveTableOK = 'Tabellen %s har flyttats till %s.';
$strMoveTableSameNames = 'Kan inte flytta tabell till samma namn!';
$strMultilingual = 'flerspråkig';
$strMustSelectFile = 'Välj filen som du vill importera.';
$strMySQLCharset = 'MySQL teckenuppsättning';
$strMySQLReloaded = 'MySQL har startats om.';
$strMySQLSaid = 'MySQL sa: ';
$strMySQLServerProcess = 'MySQL %pma_s1% körs på %pma_s2% som %pma_s3%';
$strMySQLShowProcess = 'Visa processer';
$strMySQLShowStatus = 'Visa MySQL-körningsinformation';
$strMySQLShowVars = 'Visa MySQL:s systemvariabler';

$strName = 'Namn';
$strNext = 'Nästa';
$strNo = 'Nej';
$strNoDatabases = 'Inga databaser';
$strNoDatabasesSelected = 'Inga databaser markerade.';
$strNoDescription = 'Ingen beskrivning';
$strNoDropDatabases = '"DROP DATABASE"-instruktioner är avstängda.';
$strNoExplain = 'Utan förklaring';
$strNoFrames = 'phpMyAdmin fungerar tyvärr endast med webbläsare som hanterar ramar.';
$strNoIndex = 'Inga index är definierade!';
$strNoIndexPartsDefined = 'Inga delar av index är definierade!';
$strNoModification = 'Ingen förändring';
$strNoOptions = 'Detta format har inga alternativ';
$strNoPassword = 'Inget lösenord';
$strNoPermission = 'Webbservern har inte tillåtelse att spara filen %s.';
$strNoPhp = 'Utan PHP-kod';
$strNoPrivileges = 'Inga privilegier';
$strNoQuery = 'Ingen SQL-fråga!';
$strNoRights = 'Du har inte tillräcklig behörighet för att vara här!';
$strNoSpace = 'Otillräckligt utrymme för att spara filen %s.';
$strNoTablesFound = 'Inga tabeller funna i databasen.';
$strNoUsersFound = 'Hittade ingen användare.';
$strNoUsersSelected = 'Inga användare markerade.';
$strNoValidateSQL = 'Utan validering';
$strNone = 'Inget';
$strNotNumber = 'Det är inte ett nummer!';
$strNotOK = 'Inte OK';
$strNotSet = '<b>%s</b>-tabellen ej funnen eller ej angiven i %s';
$strNotValidNumber = ' är inte ett giltigt radnummer!';
$strNull = 'Null';
$strNumSearchResultsInTable = '%s träff(ar) i tabell <i>%s</i>';
$strNumSearchResultsTotal = '<b>Totalt:</b> <i>%s</i> träff(ar)';
$strNumTables = 'Tabeller';

$strOK = 'OK';
$strOftenQuotation = 'Ofta citattecken. Frivilligt innebär att bara fält av typ \'char\' och \'varchar\' omges av angivet tecken.';
$strOperations = 'Operationer';
$strOptimizeTable = 'Optimera tabell';
$strOptionalControls = 'Frivilligt. Styr hur läsning och skrivning av specialtecken utförs.';
$strOptionally = 'Frivilligt';
$strOptions = 'Alternativ';
$strOr = 'Eller';
$strOverhead = 'Outnyttjat';
$strOverwriteExisting = 'Skriv över befintlig fil(er)';

$strPHP40203 = 'Du använder PHP 4.2.3, vilken har en allvarlig bugg med multi-byte-strängar (mbstring). Se PHP:s buggrapport 19404. Denna version av PHP är inte rekommenderad för användning tillsammans med phpMyAdmin.';
$strPHPVersion = 'PHP-version';
$strPageNumber = 'Sida:';
$strPaperSize = 'Pappersstorlek';
$strPartialText = 'Avkortade texter';
$strPassword = 'Lösenord';
$strPasswordChanged = 'Lösenordet för %s har ändrats.';
$strPasswordEmpty = 'Lösenordet är tomt!';
$strPasswordNotSame = 'Lösenorden är inte lika!';
$strPdfDbSchema = 'Schema för databasen "%s" - Sidan %s';
$strPdfInvalidPageNum = 'Odefinierat PDF-sidnummer!';
$strPdfInvalidTblName = 'Tabellen "%s" finns inte!';
$strPdfNoTables = 'Inga tabeller';
$strPerHour = 'per timme';
$strPerMinute = 'per minut';
$strPerSecond = 'per sekund';
$strPhoneBook = 'telefonbok';
$strPhp = 'Skapa PHP-kod';
$strPmaDocumentation = 'phpMyAdmin dokumentation';
$strPmaUriError = 'Variabeln <tt>$cfg[\'PmaAbsoluteUri\']</tt> MÅSTE anges i din konfigurationsfil!';
$strPortrait = 'Stående';
$strPos1 = 'Början';
$strPrevious = 'Föregående';
$strPrimary = 'Primär';
$strPrimaryKey = 'Primärnyckel';
$strPrimaryKeyHasBeenDropped = 'Den primära nyckeln har tagits bort';
$strPrimaryKeyName = 'Primärnyckelns namn måste vara "PRIMARY"!';
$strPrimaryKeyWarning = '("PRIMARY" <b>måste</b> vara namnet på och <b>endast på</b> en primärnyckel!)';
$strPrint = 'Skriv ut';
$strPrintView = 'Utskriftsvänlig visning';
$strPrivDescAllPrivileges = 'Inkluderar alla privilegier utom GRANT.';
$strPrivDescAlter = 'Tillåter ändring av befintliga tabellers struktur.';
$strPrivDescCreateDb = 'Tillåter skapande av nya databaser och tabeller.';
$strPrivDescCreateTbl = 'Tillåter skapande av nya tabeller.';
$strPrivDescCreateTmpTable = 'Tillåter skapande av temporära tabeller.';
$strPrivDescDelete = 'Tillåter borttagning av data.';
$strPrivDescDropDb = 'Tillåter borttagning av databaser och tabeller.';
$strPrivDescDropTbl = 'Tillåter borttagning av tabeller.';
$strPrivDescExecute = 'Tillåter körning av lagrade procedurer; Har ingen verkan i denna MySQL-version.';
$strPrivDescFile = 'Tillåter import av data från och export av data till filer.';
$strPrivDescGrant = 'Tillåter tillägg av användare och privilegier utan omladdning av privilegiumtabellerna.';
$strPrivDescIndex = 'Tillåter skapande och borttagning av index.';
$strPrivDescInsert = 'Tillåter infogning och ersättning av data.';
$strPrivDescLockTables = 'Tillåter låsning av tabeller för gällande tråd.';
$strPrivDescMaxConnections = 'Begränsar antalet nya förbindelser användaren kan öppna per timme.';
$strPrivDescMaxQuestions = 'Begränsar antalet frågor användaren kan skicka till servern per timme.';
$strPrivDescMaxUpdates = 'Begränsar antalet kommandon, vilka ändrar någon tabell eller databas, som användaren kan utföra per timme.';
$strPrivDescProcess3 = 'Tillåter dödande av andra användares processer.';
$strPrivDescProcess4 = 'Tillåter visning av fullständiga frågor i processlistan.';
$strPrivDescReferences = 'Har ingen verkan i denna MySQL-version.';
$strPrivDescReload = 'Tillåter omladdning av serverinställningar och rensning av serverns cache.';
$strPrivDescReplClient = 'Ger användaren rätt att fråga var slavarna / huvudservrarna är.';
$strPrivDescReplSlave = 'Nödvändig för replikationsslavar.';
$strPrivDescSelect = 'Tillåter läsning av data.';
$strPrivDescShowDb = 'Ger tillgång till den fullständiga databaslistan.';
$strPrivDescShutdown = 'Tillåter avstängning av servern.';
$strPrivDescSuper = 'Tillåter uppkoppling, även om maximala antalet förbindelser är nådd; Nödvändig för de flesta administrativa funktioner, som att sätta globala variabler eller döda andra användares trådar.';
$strPrivDescUpdate = 'Tillåter ändring av data.';
$strPrivDescUsage = 'Inga privilegier.';
$strPrivileges = 'Privilegier';
$strPrivilegesReloaded = 'Privilegierna har laddats om.';
$strProcesslist = 'Processlista';
$strProperties = 'Inställningar';
$strPutColNames = 'Ange fältnamn på första raden';

$strQBE = 'Skapa fråga';
$strQBEDel = 'Ta bort';
$strQBEIns = 'Infoga';
$strQueryFrame = 'Frågefönster';
$strQueryFrameDebug = 'Avlusningsinformation';
$strQueryFrameDebugBox = 'Aktiva variabler för frågeformuläret:\nDB: %s\nTabell: %s\nServer: %s\n\nAktuella variabler för frågeformuläret:\nDB: %s\nTabell: %s\nServer: %s\n\nÖppnarens plats: %s\nRamverkets plats: %s.';
$strQueryOnDb = 'SQL-fråga i databas <b>%s</b>:';
$strQuerySQLHistory = 'SQL-historik';
$strQueryStatistics = '<b>Frågestatistik</b>: %s frågor har skickats till servern sedan den startade.';
$strQueryTime = 'Frågan tog %01.4f sek';
$strQueryType = 'Typ av fråga';
$strQueryWindowLock = 'Skriv inte över denna fråga utifrån detta fönster';

$strReType = 'Skriv om';
$strReceived = 'Mottagna';
$strRecords = 'Rader';
$strReferentialIntegrity = 'Kontrollera referensintegritet:';
$strRelationNotWorking = 'Den extra funktionaliteten för att hantera länkade tabeller har avaktiverats. %sVisa orsaken%s.';
$strRelationView = 'Visa relationer';
$strRelationalSchema = 'Relationsschema';
$strRelations = 'Relationer';
$strReloadFailed = 'Omladdning av MySQL misslyckades.';
$strReloadMySQL = 'Ladda om MySQL';
$strReloadingThePrivileges = 'Laddar om privilegierna';
$strRememberReload = 'Kom ihåg att ladda om MySQL.';
$strRemoveSelectedUsers = 'Ta bort markerade användare';
$strRenameTable = 'Döp om tabellen till';
$strRenameTableOK = 'Tabell %s har döpts om till %s';
$strRepairTable = 'Reparera tabell';
$strReplace = 'Ersätt';
$strReplaceNULLBy = 'Ersätt NULL med';
$strReplaceTable = 'Ersätt data i tabell';
$strReset = 'Nollställ';
$strResourceLimits = 'Resursbegränsningar';
$strRevoke = 'Upphäv';
$strRevokeAndDelete = 'Upphäv användarnas alla aktiva privilegier och ta bort användarna efteråt.';
$strRevokeAndDeleteDescr = 'Användarna kommer fortfarande ha kvar privilegiet USAGE tills privilegierna laddas om.';
$strRevokeGrant = 'Upphäv Grant';
$strRevokeGrantMessage = 'Du har upphävt Grant-privilegiet för %s';
$strRevokeMessage = 'Du har upphävt privilegierna för %s';
$strRevokePriv = 'Upphäv privilegier';
$strRowLength = 'Radlängd';
$strRowSize = 'Radstorlek';
$strRows = 'Rader';
$strRowsFrom = 'rader med början från';
$strRowsModeFlippedHorizontal = 'vågrätt (roterade rubriker)';
$strRowsModeHorizontal = 'vågrätt';
$strRowsModeOptions = 'i %s format och upprepa rubrikerna efter %s celler';
$strRowsModeVertical = 'lodrätt';
$strRowsStatistic = 'Radstatistik';
$strRunQuery = 'Kör fråga';
$strRunSQLQuery = 'Kör SQL-fråga/frågor i databasen %s';
$strRunning = 'körs på %s';
$strRussian = 'Rysk';

$strSQL = 'SQL';
$strSQLOptions = 'SQL-alternativ';
$strSQLParserBugMessage = 'Det är möjligt att du har hittat en bugg i SQL-analysatorn. Var god granska din fråga noga och kontrollera att citationstecknen är korrekta och matchar varandra. En annan möjlig felorsak kan vara att du överför en fil med binärkod som inte ligger inom citationstecken. Du kan även testa din fråga i MySQL:s kommandoradsgränssnitt. Felmeddelandet från MySQL-servern nedan, om det finns något, kan också hjälpa dig att analysera problemet. Om du fortfarande har problem eller om SQL-analysatorn misslyckas när kommandoradsgränssnittet lyckas, var vänlig reducera din inmatning av SQL-frågor till den enda fråga som orsakar problem och skicka en buggrapport med datastycket i URKLIPP-sektionen nedan:';
$strSQLParserUserError = 'Det verkar vara ett fel i din SQL-fråga. Felmeddelandet från MySQL-servern nedan, om det finns något, kan också hjälpa dig att analysera problemet.';
$strSQLQuery = 'SQL-fråga';
$strSQLResult = 'SQL-resultat';
$strSQPBugInvalidIdentifer = 'Ogiltig identifierare';
$strSQPBugUnclosedQuote = 'Oavslutat citat';
$strSQPBugUnknownPunctuation = 'Okänd interpunktion i sträng';
$strSave = 'Spara';
$strSaveOnServer = 'Spara på servern i katalogen %s';
$strScaleFactorSmall = 'Skalfaktorn är för liten för att schemat ska få plats på en sida';
$strSearch = 'Sök';
$strSearchFormTitle = 'Sök i databas';
$strSearchInTables = 'I tabell(er):';
$strSearchNeedle = 'Ord eller värde(n) att söka efter (jokertecken: "%"):';
$strSearchOption1 = 'minst ett av orden';
$strSearchOption2 = 'alla ord';
$strSearchOption3 = 'den exakta frasen';
$strSearchOption4 = 'som reguljärt uttryck';
$strSearchResultsFor = 'Resultat av sökning efter "<i>%s</i>" %s:';
$strSearchType = 'Hitta:';
$strSecretRequired = 'Konfigurationsfilen behöver nu ett hemligt lösenord (blowfish_secret).';
$strSelect = 'Välj';
$strSelectADb = 'Välj en databas';
$strSelectAll = 'Markera alla';
$strSelectFields = 'Välj fält (minst ett):';
$strSelectNumRows = 'i fråga';
$strSelectTables = 'Välj tabeller';
$strSend = 'Spara som fil';
$strSent = 'Skickade';
$strServer = 'Server %s';
$strServerChoice = 'Serverval';
$strServerStatus = 'Körningsinformation';
$strServerStatusUptime = 'Denna MySQL-server har körts i %s. Den startade den %s.';
$strServerTabProcesslist = 'Processer';
$strServerTabVariables = 'Variabler';
$strServerTrafficNotes = '<b>Servertrafik</b>: Dessa variabler visar statistik för nätverkstrafiken hos denna MySQL-server sedan den startade.';
$strServerVars = 'Servervariabler och inställningar';
$strServerVersion = 'Serverversion';
$strSessionValue = 'Sessionsvärde';
$strSetEnumVal = 'Om en fälttyp är "enum" eller "set", var god ange värden enligt följande format: \'a\',\'b\',\'c\'...<br />Om du behöver lägga till ett bakåtstreck ("\") eller ett enkelcitat ("\'") i värdena, skriv ett bakåtstreck före tecknet (till exempel \'\\\\xyz\' eller \'a\\\'b\').';
$strShow = 'Visa';
$strShowAll = 'Visa alla';
$strShowColor = 'Visa färger';
$strShowCols = 'Visa kolumner';
$strShowDatadictAs = 'Format för datalexikon';
$strShowFullQueries = 'Visa fullständiga frågor';
$strShowGrid = 'Visa rutnät';
$strShowPHPInfo = 'Visa PHP-information';
$strShowTableDimension = 'Visa tabellers dimensioner';
$strShowTables = 'Visa tabeller';
$strShowThisQuery = ' Visa frågan här igen ';
$strShowingRecords = 'Visar rader ';
$strSimplifiedChinese = 'Förenklad kinesiska';
$strSingly = '(ensam)';
$strSize = 'Storlek';
$strSort = 'Sortering';
$strSpaceUsage = 'Utrymmesanvändning';
$strSplitWordsWithSpace = 'Ord separeras med mellanslag (" ").';
$strStatCheckTime = 'Senaste kontroll';
$strStatCreateTime = 'Skapades';
$strStatUpdateTime = 'Senaste uppdatering';
$strStatement = 'Uppgift';
$strStatus = 'Status';
$strStrucCSV = 'CSV-data';
$strStrucData = 'Struktur och data';
$strStrucDrop = 'Lägg till \'radera tabell\'';
$strStrucExcelCSV = 'CSV för MS Excel-data';
$strStrucOnly = 'Enbart struktur';
$strStructPropose = 'Föreslå tabellstruktur';
$strStructure = 'Struktur';
$strSubmit = 'Sänd';
$strSuccess = 'Din SQL-fråga utfördes korrekt';
$strSum = 'Summa';
$strSwedish = 'Svensk';
$strSwitchToTable = 'Byt till kopierad tabell';

$strTable = 'Tabell';
$strTableComments = 'Tabellkommentarer';
$strTableEmpty = 'Tabellnamnet är tomt!';
$strTableHasBeenDropped = 'Tabellen %s har tagits bort';
$strTableHasBeenEmptied = 'Tabellen %s har tömts';
$strTableHasBeenFlushed = 'Tabellen %s har rengjorts';
$strTableMaintenance = 'Tabellunderhåll';
$strTableOfContents = 'Innehållsförteckning';
$strTableOptions = 'Tabell-alternativ';
$strTableStructure = 'Struktur för tabell';
$strTableType = 'Tabelltyp';
$strTables = '%s tabell(er)';
$strTblPrivileges = 'Tabellspecifika privilegier';
$strTextAreaLength = ' På grund av dess längd,<br /> kanske detta fält inte kan redigeras ';
$strThai = 'Thailändsk';
$strTheContent = 'Filens innehåll har importerats.';
$strTheContents = 'Filens innehåll ersätter den valda tabellens rader som har identiska primära eller unika nycklar.';
$strTheTerminator = 'Fältavslutare.';
$strThisHost = 'Denna värd';
$strThisNotDirectory = 'Detta var inte en katalog';
$strThreadSuccessfullyKilled = 'Tråd %s dödades med framgång.';
$strTime = 'Tid';
$strToggleScratchboard = 'Visa/dölj skisstavla';
$strTotal = 'totalt';
$strTotalUC = 'Totalt';
$strTraditionalChinese = 'Traditionell kinesiska';
$strTraffic = 'Trafik';
$strTransformation_image_jpeg__inline = 'Visar en klickbar tumnagelbild; parametrar: bredd,höjd i pixlar (behåller originalproportionerna)';
$strTransformation_image_jpeg__link = 'Visar en länk till denna bild (dvs direkt blob-nedladdning).';
$strTransformation_image_png__inline = 'Se image/jpeg: inline';
$strTransformation_text_plain__dateformat = 'Tar ett TIME, TIMESTAMP eller DATETIME-fält och formaterar det enligt ditt lokala datumformat. Första parametern är förskjutningen (i timmar) som kommer att läggas till tidsstämpeln (standardvärde: 0). Andra parametern är ett annorlunda datumformat enligt tillgängliga parametrar för PHP:s strftime().';
$strTransformation_text_plain__external = 'ENDAST LINUX: Startar en extern applikation och skickar fältdata till den via standard-indata. Returnerar applikationens standard-utdata. Standard är Tidy, för att snygga till HTML-kod. Av säkerhetsskäl måste du manuellt redigera filen libraries/transformations/text_plain__external.inc.php och infoga verktygen du tillåter ska få köras. Den första parametern är då numret för det program som du vill använda och den andra parametern är parametrarna för programmet. Om den tredje parametern sätts till 1 kommer utdata konverteras mha htmlspecialchars() (standard är 1). Om den fjärde parametern sätts till 1 kommer en NOWRAP läggas till innehållscellen så att hela utdata kommer att visas utan omformatering (standard är 1).';
$strTransformation_text_plain__formatted = 'Bevarar fältets originalformatering. Utan bakåtstreck före specialtecken.';
$strTransformation_text_plain__imagelink = 'Visar en bild och en länk, fältet innehåller filnamnet; första parametern är ett prefix såsom "http://domain.com/", andra parametern är bredden i pixlar, tredje är höjden.';
$strTransformation_text_plain__link = 'Visar en länk, fältet innehåller filnamnet; första parametern är ett prefix såsom "http://domain.com/", andra parametern är en titel för länken.';
$strTransformation_text_plain__substr = 'Visar endast del av en sträng. Första parametern specificerar var i texten utdata startar (standardvärde: 0). Andra parametern specificerar hur mycket text som returneras. Om det utelämnas, returneras all resterande text. Den tredje parametern definierar vilka tecken som kommer att läggas till i slutet på den returnerade delsträngen  (standardvärde: ...).';
$strTransformation_text_plain__unformatted = 'Visar HTML-kod som HTML-specialtecken. HTML-formatering visas inte.';
$strTruncateQueries = 'Korta av visade frågor';
$strTurkish = 'Turkisk';
$strType = 'Typ';

$strUkrainian = 'Ukrainsk';
$strUncheckAll = 'Avmarkera alla';
$strUnicode = 'Unicode';
$strUnique = 'Unik';
$strUnknown = 'okänd';
$strUnselectAll = 'Avmarkera alla';
$strUpdComTab = 'Se dokumentationen för uppdatering av din tabell Column_comments';
$strUpdatePrivMessage = 'Du har uppdaterat privilegierna för %s.';
$strUpdateProfile = 'Uppdatera profil:';
$strUpdateProfileMessage = 'Profilen har uppdaterats.';
$strUpdateQuery = 'Uppdatera fråga';
$strUsage = 'Användning';
$strUseBackquotes = 'Använd bakåtcitat runt tabell- och fältnamn';
$strUseHostTable = 'Använd värdtabell';
$strUseTables = 'Använd tabeller';
$strUseTextField = 'Använd textfältet';
$strUseThisValue = 'Använd detta värde';
$strUser = 'Användare';
$strUserAlreadyExists = 'Användaren %s finns redan!';
$strUserEmpty = 'Användarnamnet är tomt!';
$strUserName = 'Användarnamn';
$strUserNotFound = 'Den markerade användaren kunde inte hittas i privilegiumtabellen.';
$strUserOverview = 'Användaröversikt';
$strUsers = 'Användare';
$strUsersDeleted = 'De markerade användarna har tagits bort.';
$strUsersHavingAccessToDb = 'Användare som har tillgång till &quot;%s&quot;';

$strValidateSQL = 'Validera SQL-kod';
$strValidatorError = 'SQL-validatorn kunde inte initieras. Kontrollera att du har installerat de nödvändiga PHP-utökningarna enligt %sdokumentationen%s.';
$strValue = 'Värde';
$strVar = 'Variabel';
$strViewDump = 'Visa SQL-satser för tabellen';
$strViewDumpDB = 'Visa SQL-satser för databasen';
$strViewDumpDatabases = 'Visa SQL-satser för databaser';

$strWebServerUploadDirectory = 'Uppladdningskatalog på webbserver';
$strWebServerUploadDirectoryError = 'Katalogen som du konfigurerat för uppladdning kan inte nås';
$strWelcome = 'Välkommen till %s';
$strWestEuropean = 'Västeuropeisk';
$strWildcard = 'jokertecken';
$strWindowNotFound = 'Målfönstret kunde inte uppdateras. Orsaken kan vara att du stängt föräldrafönstret eller att din webbläsares säkerhetsinställningar blockerar uppdateringar mellan fönster.';
$strWithChecked = 'Med markerade:';
$strWritingCommentNotPossible = 'Skrivning av kommentar inte möjlig';
$strWritingRelationNotPossible = 'Skrivning av relation inte möjlig';
$strWrongUser = 'Fel användarnamn/lösenord. Åtkomst nekad.';

$strXML = 'XML';

$strYes = 'Ja';

$strZeroRemovesTheLimit = 'Anm: Genom att sätta dessa alternativ till 0 (noll) tas begränsningarna bort.';
$strZip = '"zippad"';
// To translate

?>
