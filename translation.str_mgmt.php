<?php
/**
* Additional functions for Translation class
* 
* Functions allowing user to create the new language, translation for specific strings and full management of the languages database.
* 
* @autor Wojciech Zieliñski <voyteck@caffe.com.pl>
* @version 1.2
* @access public
* @package Translation
*/

require_once 'DB.php';

/**
  * New language creation
  * 
  * Creates new language in the system. Creates language entry in the languages table and the table for language strings. If other languages has been created before and their tables were filled with strings, function addTranslation should be executed for each of the added strings just after calling this function and before using the Translation class for any purpose.
  * @param string $LangID			Language identifier
  * @param string $LangName	Language name - it is preferred so this name should be in language, which it describes. This name can be later retrieved by calling getLangName and getOtherLangs methods and used for hyperlinks changing the site language.
  * @param string $METATags		Tags that may describe the language codepage etc. These tags can be retrieved by calling getMetaTags method.
  * @param string pear_DSN		PEAR DSN string for database connection
  * @return integer $result				1 if everything went OK or PEAR DB_Error object if something goes wrong.
  */
function createNewLang($LangID, $LangName, $METATags, $pear_DSN) {
	$db = DB::connect($pear_DSN);
	if (DB::isError($db))
		return $db;
	$result = $db->query("INSERT INTO tr_langsavail (lang_id, name, metatags)
			VALUES (\"$LangID\", \"$LangName\", \"$METATags\")");
	if (DB::isError($result))
		return $result;
	$result = $db->query("CREATE TABLE tr_strings_$LangID 
			(page_id varchar(16) default NULL,
			string_id varchar(32) NOT NULL,
			string longtext,
			UNIQUE KEY page_id (page_id, string_id))");
	if (DB::isError($result)) {
		$delresult = $db->query("DELETE FROM tr_langsavail 
				WHERE lang_id=\"$LangID\"");
		return $result;
	}
	return 1;
}

/**
  * Language removal
  * 
  * Removes language from system. This function should be used with much carefull - this will permanently remove all the strings that has been added to language table by dropping this table.
  * @param string $LangID			Language identifier
  * @param string $pear_DSN		PEAR DSN string for database connection
  * @return integer $result				1 if everything went OK or PEAR DB_Error object if something goes wrong.
  */
function removeLang($LangID, $pear_DSN) {
	$db = DB::connect($pear_DSN);
	if (DB::isError($db))
		return $db;
	$result = $db->query("DROP TABLE tr_strings_$LangID");
	if (DB::isError($result))
		return $result;
	$result = $db->query("DELETE FROM tr_langsavail WHERE lang_id=\"$langID\"");
	if (DB::isError($result))
		return $result;
	return 1;
}

/**
  * Translation adding
  * 
  * Adds string to one or more language tables.
  * @param string $PageID			page identifier. Might be "" if the string is to be available from any page, independendly from translation object creation parameters.
  * @param string $StringID			string identifier. Must be unique for the same PageID and strings that were created without PageID's. This rule must be used to prevent the situation of 2 same StringID's in one Translation object.
  * @param array string $String	array of strings - the array keys should be languages id's, the values - the sttrings in these	languages - e.g.:	("en"->"English text", "pl"->"Tekst polski", ...)
  * @param string $pear_DSN		PEAR DSN string for database connection
  * @return integer $result				1 if everything went OK or PEAR DB_Error object if something goes wrong
  */
function addTranslation($PageID, $StringID, $String, $pear_DSN) {
	$db = DB::connect($pear_DSN);
	if (DB::isError($db))
		return $db;
	foreach ($String as $LangID => $Text)
		$data[] = array("tr_strings_$LangID", $Text);
	$result = $db->executeMultiple(($db->prepare("INSERT INTO ! (page_id, string_id, string) VALUES ('$PageID', '$StringID', ?)")), $data);
	if (DB::isError($result))
		return $result;
	return 1;
}

/**
  * Translation removal
  * 
  * Removes string from all of string tables
  * @param string $PageID		page identifier.
  * @param string $StringID		string identifier.
  * @param string $pear_DSN	PEAR DSN string for database connection
  * @return integer $result			1 if everything went OK or PEAR DB_Error object if something goes wrong
  */
function removeTranslation($PageID, $StringID, $pear_DSN) {
	$db = DB::connect($pear_DSN);
	if (DB::isError($db))
		return $db;
	$result = $db->query("SELECT lang_id FROM tr_langsavail");
	if (DB::isError($result))
		return $result;
	while ($row = $result->fetchRow())
		$languages[] = "tr_strings_" . $row[0];
	//print_r($languages);
	$result = $db->executeMultiple(($db->prepare("DELETE FROM ! WHERE page_id = '$PageID' and string_id = '$StringID'")), $languages);
	if (DB::isError($result))
		return $result;
	return $result;
}
?>