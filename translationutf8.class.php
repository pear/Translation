<?php
/**
 * File translationutf8.class.php
 *
 * PHP version 4
 *
 * @category Translation
 * @package  Translation
 * @author   Wojciech Zieliñski <voyteck@caffe.com.pl>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/Translation
 */
/**
 * require base class
 */
require_once 'Translation'.DIRECTORY_SEPARATOR.'translation.class.php';

/**
 * class that allows using Translation class with UTF-8 DB encoding
 *
 * @category Translation
 * @package  Translation
 * @author   Wojciech Zieliñski <voyteck@caffe.com.pl>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 * @link     http://pear.php.net/package/Translation
 * @access   public
 */
class TTranslationUTF8 extends Translation
{
    function TTranslationUTF8($PageName, $LanguageID, $pear_DSN, $CustomTables = 0)
    {
        $this->Translation($PageName, $LanguageID, $pear_DSN, $CustomTables);
    }

    function gstr($StringName, $Params = array())
    {
        return utf8_decode(parent::gstr($StringName, $Params));
    }
}
?>