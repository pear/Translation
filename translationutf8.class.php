<?
// class that allows using Translation class with UTF-8 DB encoding

require_once "Translation/translation.class.php";

class TTranslationUTF8 extends Translation {
	function TTranslationUTF8($PageName, $LanguageID, $pear_DSN, $CustomTables = 0) {
		$this->Translation($PageName, $LanguageID, $pear_DSN, $CustomTables);
	}

	function gstr($StringName, $Params = array()) {
		return utf8_decode(parent::gstr($StringName, $Params));
	}
}
?>