<?php
define( DATA_HOME, realpath( dirname(__file__) ).'/' );
define( CACHE_DIR, DATA_HOME );
define( 'INI_FILE', DATA_HOME.'pukiwiki.ini.php' );
require_once( DATA_HOME.'func.php' );
require_once( DATA_HOME.'file.php' );
require_once( DATA_HOME.'plugin.php' );
require_once( DATA_HOME.'html.php' );
require_once( DATA_HOME.'convert_html.php' );
require_once( DATA_HOME.'make_link.php' );

require_once( DATA_HOME.'pukiwiki.ini.php' );
require_once( DATA_HOME.'default.ini.php' );

/**
 * from init.php
 */
/////////////////////////////////////////////////
// INI_FILE: LANG に基づくエンコーディング設定

// MB_LANGUAGE: mb_language (for mbstring extension)
//   'uni'(means UTF-8), 'English', or 'Japanese'
// SOURCE_ENCODING: Internal content encoding (for mbstring extension)
//   'UTF-8', 'ASCII', or 'EUC-JP'
// CONTENT_CHARSET: Internal content encoding = Output content charset (for skin)
//   'UTF-8', 'iso-8859-1', 'EUC-JP' or ...

switch (LANG){
case 'en': define('MB_LANGUAGE', 'English' ); break;
case 'ja': define('MB_LANGUAGE', 'Japanese'); break;
//UTF-8:case 'ko': define('MB_LANGUAGE', 'Korean'  ); break;
//UTF-8:	// See BugTrack2/13 for all hack about Korean support,
//UTF-8:	// and give us your report!
default: die_message('No such language "' . LANG . '"'); break;
}

//UTF-8:define('PKWK_UTF8_ENABLE', 1);
if (defined('PKWK_UTF8_ENABLE')) {
	define('SOURCE_ENCODING', 'UTF-8');
	define('CONTENT_CHARSET', 'UTF-8');
} else {
	switch (LANG){
	case 'en':
		define('SOURCE_ENCODING', 'ASCII');
		define('CONTENT_CHARSET', 'iso-8859-1');
		break;
	case 'ja':
		define('SOURCE_ENCODING', 'EUC-JP');
		define('CONTENT_CHARSET', 'EUC-JP');
		break;
	}
}

mb_language(MB_LANGUAGE);
mb_internal_encoding(SOURCE_ENCODING);
ini_set('mbstring.http_input', 'pass');
mb_http_output('pass');
mb_detect_order('auto');

// BugTrack/304暫定対処
$WikiName = '(?:[A-Z][a-z]+){2,}(?!\w)';

// $BracketName = ':?[^\s\]#&<>":]+:?';
$BracketName = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';

// InterWiki
$InterWikiName = '(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])';

// 注釈
$NotePattern = '/\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)/ex';

/////////////////////////////////////////////////
// 初期設定(ユーザ定義ルール読み込み)
require(DATA_HOME . 'rules.ini.php');

/////////////////////////////////////////////////
// 初期設定(その他のグローバル変数)

// 現在時刻
$now = format_date(UTIME);

// 日時置換ルールを$line_rulesに加える
if ($usedatetime) $line_rules += $datetime_rules;
unset($datetime_rules);

// フェイスマークを$line_rulesに加える
if ($usefacemark) $line_rules += $facemark_rules;
unset($facemark_rules);

// 実体参照パターンおよびシステムで使用するパターンを$line_rulesに加える
//$entity_pattern = '[a-zA-Z0-9]{2,8}';
$entity_pattern = trim(join('', file(CACHE_DIR . 'entities.dat')));

$line_rules = array_merge(array(
	'&amp;(#[0-9]+|#x[0-9a-f]+|' . $entity_pattern . ');' => '&$1;',
	"\r"          => '<br />' . "\n",	/* 行末にチルダは改行 */
), $line_rules);

/**
 * form *.lng.php
 */
// Symbols
$_symbol_anchor   = '&dagger;';
$_symbol_noexists = '?';


/**
 * main function
 *
 * @since  2006-03-19
 * @param  array $lines
 * @return string
 */
function pukiparser( $lines ) {
  // force convert argv into array
  if ( !is_array( $lines ) ) {
    $lines = preg_split( '/(\r\n|[\r\n])/', $lines );
  }

  global $line_rules, $foot_explain, $note_hr;

  $body = &new Body( ++$contents_id );
  $body->parse( $lines );
  $parsed = $body->toString();

  // List of footnotes
  ksort($foot_explain, SORT_NUMERIC);
  $notes = ! empty($foot_explain) ? $note_hr . join("\n", $foot_explain) : '';

  return $parsed . (($notes) ? "\n$notes" : '');
}
?>
