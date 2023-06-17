README.txt for REPGEN
======================
This program generates PDF-reports based on data, created by an SQL-statement.
It consists of two parts: The HTML-Definition of the report and the print-machine, which 
creates the PDF-printout.

The database modules are used from phplib 7.02d(sascha@schumann.cx).
The PDF classes are from Wayne Munro(http://www.ros.co.nz)
Many thanks for their work!!

Installation:
-------------
This program is based on PHP 4.05 and phplib.
The files with the extension '*.inc' should be included in the include-path of PHP (to set in php.ini!).
You can achieve this by setting '.;' at the beginning of the include-path. 
The constant REPGENDIR in the file 'repgen_def.inc' points to the directory, where the *.php - programs 
are stored (should be under the directory htdocs). REPGENDIR has now the value 'repgen', so the 
programs are assumed to be in htdocs/repgen/.

You can use the databases Mysql, Postgresql and ODBC if you decomment the specific constant DBDRIVER on the file 'repgen_def.inc'.
If you want the program to work in another language, copy the file repgen_const_<language>.inc over repgen_const.inc.
Until now,there exist only german and english ('repgen_const_german.inc' and 'repgen_const_english.inc').


The creation of an report is produced by calling 

'create_report($database, $host, $user, $password, $id, $file)' 

after including 'repgen.inc'.

$id is the id-number of the report, the other values are used for the connection to the database.
$file is the name of a file, where the pdf-file is stored and can be shown in an browser wiht Acrobat Reader plug-in.

With reports.sql you can create the database tables 'reports', 'schluessel' and 'numbers' with test data. The table 'reports' stores
the definition of test reports and the other tables can be used for testing.

Call the file 'sample-mysql.php' with your browser, after changing the values of the database-variables, to test the system (or sample-pgsql.php or sample-odbc.php).

All bugs, wishes for improvement etc. can be reported to 

werner_bauer@aon.at

Thanks for your interest
