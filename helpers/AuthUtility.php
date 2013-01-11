<?php
/**
 * AuthUtility class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.helpers
 * @version 1.3.0
 */

/**
 * Class-container for utility methods.
 */
class AuthUtility {

	/**
	 * UTF-8 aware version of PHP's built-in function <code>ucfirst</code>.
	 * Relies on <em>mbstring</em> PHP extension, will fallback to default <code>ucfirst</code> if <em>mbstring</em> is not installed/loaded.
	 * @param string $str The input string.
	 * @return string Returns a string with the first character of str capitalized, if that character is alphabetic.
	 * @see http://php.net/manual/en/function.ucfirst.php
	 * @see http://php.net/manual/en/book.mbstring.php
	 */
	public static function ucfirst($str) {
		if ( !extension_loaded('mbstring') ) {
			return ucfirst($str);
		}

		return mb_strtoupper(mb_substr($str, 0, 1, Yii::app()->charset), Yii::app()->charset)
				. mb_substr($str, 1, mb_strlen($str, Yii::app()->charset) - 1, Yii::app()->charset);
	}

}
