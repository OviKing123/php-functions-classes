<?php

class YandexTranslate {

	/**
	 * [$input_from description]
	 * @var [type]
	 */
	protected $input_from;

	/**
	 * [$input_to description]
	 * @var [type]
	 */
	protected $input_to;

	/**
	 * [$input_text description]
	 * @var [type]
	 */
	protected $input_text;

	/**
	 * [$from description]
	 * @var [type]
	 */
	protected $from;

	/**
	 * [$to description]
	 * @var [type]
	 */
	protected $to;

	/**
	 * [$text description]
	 * @var [type]
	 */
	protected $text;

	/**
	 * [$html description]
	 * @var [type]
	 */
	protected $html;

	/**
	 * [$json description]
	 * @var [type]
	 */
	protected $json;

	/**
	 * [$translation description]
	 * @var [type]
	 */
	protected $translation;

	public function __construct( $from = null, $to = null, $text = null ) {

		isset( $from ) && $this->setFrom( $from );
		isset( $to ) && $this->setTo( $to );
		isset( $text ) && $this->setText( $text );

	}

	public function setFrom( $from ) {
		$this->input_from = $from;
		$this->from = $this->_setFrom( $from );
		return $this;
	}

	public function setTo( $to ) {
		$this->input_to = $to;
		$this->to = $this->_setTo( $to );
		return $this;
	}

	public function setText( $text ) {
		$this->input_text = $text;
		$this->text = $this->_setText( $text );
		return $this;
	}

	public function addText( $text ) {
		return $this->setText( $text );
	}

	public function run() {
		if ( !isset( $this->from, $this->to, $this->text ) ) {
			return false;
		}
		return $this->_run();
	}

	public function getJson() {
		return $this->json;
	}

	public function getTranslation() {
		return $this->translation;
	}

	protected function _setFrom( $from ) {

		static $haystack;

		$haystack = $haystack !== null ? $haystack : array(
			'en', 'pt'
		);

		return in_array( $from, $haystack ) ? $from : null;

	}

	protected function _setTo( $to ) {

		static $haystack;

		$haystack = $haystack !== null ? $haystack : array(
			'en', 'pt'
		);

		return in_array( $to, $haystack ) ? $to : null;

	}

	protected function _setText( $text ) {

		$this->input_text = $text;

		$text = trim( $text );
		$text = str_replace( "\x20\x20", "\x20", $text );

		return $text;

	}

	protected function _addText( $text ) {
		return $this->_setText( $text );
	}

	protected function _run() {

		$this->text = trim( $this->text );

		$text = $this->text;

		if ( defined( 'ABSPATH' ) ) {
			$cache_folder = ABSPATH . 'wp-content/cache/class-yandex-translate/yandex/';
		} else {
			$cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/';
		}
		
		$cache_file = $cache_folder . md5( $text );
		$cache_files[] = $cache_file;

		foreach ( $cache_files as $cache_file ) {
			if ( file_exists( $cache_file ) ) {
				$this->json = file_get_contents( $cache_file );
				$this->_parserTranslation();
				return $this->translation === false ? false : true;
			}
		}

		if ( !file_exists( $cache_folder ) && !mkdir( $cache_folder, 0755, true ) ) {
			return false;
		}

		if ( $this->_getId() === false ) {
			return false;
		}

		if ( $this->_doRequest() === false ) {
			return false;
		}

		foreach ( $cache_files as $cache_file ) {
			if ( file_put_contents( $cache_file, $this->json ) === false ) {
				return false;
			}
		}

		$this->_parserTranslation();

		return $this->translation === false ? false : true;

	}

	protected function _getId() {

		return true;

	}

	protected function _doRequest() {

		if ( isset( $this->json ) ) {
			return true;
		}

		$params = array(
			'callback' => 'ya_.json.c(7)',
			'lang' => $this->from . '-' . $this->to,
			'text' => '["' . $this->text . '"]',
			'srv' => 'tr-text',
			'id' => '',
			'reason' => 'paste',
			'options' => '4',
		);

		$query_data = $params;

		$ch = curl_init();

		$options = array(
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_ENCODING => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HTTPGET => true,
			CURLOPT_HEADER => true,
			CURLOPT_NOPROGRESS => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_URL => 'https://translate.yandex.net/api/v1/tr.json/translate?' . http_build_query( $query_data, '', '&' ),
			CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
			CURLOPT_VERBOSE => true,
		);

		curl_setopt_array( $ch, $options );

		$exec  = curl_exec( $ch );
		$info  = curl_getinfo( $ch );
		$head  = substr( $exec, 0, $info['header_size'] );
		$body  = substr( $exec, $info['header_size'] );
		$error = curl_error( $ch );
		$errno = curl_errno( $ch );

		curl_close( $ch );

		if ( $exec === false || $error !== '' || $errno || $info['http_code'] !== 200 ) {
			return false;
		}

		$this->json = $body;
		$this->json = substr( $this->json, 0, 3 ) === "\xef\xbb\xbf" ? trim( substr( $this->json, 3 ) ) : $this->json;

		if ( strpos( $this->json, '"code":200' ) === false ) {
			$this->json = null;
			return false;
		}

		return true;

	}

	protected function _parserTranslation() {

		if ( !isset( $this->json ) ) {
			return false;
		}

		if ( isset( $this->translation ) ) {
			return $this->translation;
		}

		$string = $this->json;
		$string = trim( $string );
		$string = substr( $string, 0, 3 ) === "\xef\xbb\xbf" ? trim( substr( $string, 3 ) ) : $string;
		$string = trim( preg_replace( '/^ya_\.json\.c\([0-9]+\)\(/', '', $string, -1, $count ) );
		$string = $count && substr( $string, -2 ) === ');' ? trim( substr( $string, 0, -2 ) ) : ( substr( $string, -1 ) === ')' ? trim( substr( $string, 0, -1 ) ) : $string );
		$object = json_decode( $string );

		if ( isset( $object->text ) && is_array( $object->text ) ) {

			$result = '';

			foreach ( $object->text as $text ) {
				if ( ( $arr = json_decode( $text ) ) && is_array( $arr ) ) {
					foreach ( $arr as $value ) {
						$result .= $value;
					}
				} elseif ( substr( $text, 0, 2 ) === '["' && substr( $text, -2 ) === '"]' ) {
					$result .= substr( $text, 2, -2 );
				} else {
					$result = str_replace( array( '[', ']' ), '', $text );
				}
			}

		} elseif ( isset( $object->text ) && is_string( $object->text ) ) {

			$result = $object->text;

		} else {

			$result = false;

		}

		if ( $result ) {
			$this->translation = $result;
			return true;
		}

		return false;

	}

	public static function staticTranslate( $source_language, $target_language, $original_text, &$result = false ) {

		$result = false;

		$bt = new YandexTranslate();
		$bt->setFrom( $source_language );
		$bt->setTo( $target_language );
		$bt->setText( $original_text );
		$bt->run();

		$result = $bt->getTranslation();

		return $result;

	}

}

?>
