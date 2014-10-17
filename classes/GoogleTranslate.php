<?php

class GoogleTranslate {

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

	/**
	 * [$isHtml description]
	 * @var [type]
	 */
	protected $isHtml;

	/**
	 * [$isJson description]
	 * @var [type]
	 */
	protected $isJson;

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
		$this->to = $this->_setFrom( $to );
		return $this;
	}

	public function setText( $text ) {
		$this->input_text = $text;
		$this->text = $this->_setText( $text );
		return $this;
	}

	public function run() {
		if ( !isset( $this->from, $this->to, $this->text ) ) {
			return false;
		}
		return $this->_run();
	}

	public function getHtml() {
		return $this->html;
	}

	public function getHtmlEntities() {
		return ( $html = htmlentities( $this->html ) ) ? $html : $this->html;
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
			$cache_folder = ABSPATH . 'wp-content/cache/class-google-translate/google/';
		} else {
			$cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/';
		}
		
		$cache_file = $cache_folder . md5( $text );
		$cache_files[] = $cache_file;

		foreach ( $cache_files as $cache_file ) {
			if ( file_exists( $cache_file . '.html' ) ) {
				$this->isHtml = true;
				$this->html = file_get_contents( $cache_file . '.html' );
				$this->_parserTranslation();
				return $this->translation === false ? false : true;
			}
			if ( file_exists( $cache_file . '.json' ) ) {
				$this->isJson = true;
				$this->html = file_get_contents( $cache_file . '.json' );
				$this->_parserTranslation();
				return $this->translation === false ? false : true;
			}
			if ( file_exists( $cache_file ) ) {
				$this->html = file_get_contents( $cache_file );
				$this->_parserTranslation();
				return $this->translation === false ? false : true;
			}
		}

		if ( $this->_doRequest() === false ) {
			return false;
		}

		if ( !file_exists( $cache_folder ) && !mkdir( $cache_folder, 0755, true ) ) {
			return false;
		}

		if ( $this->isHtml ) {

			foreach ( $cache_files as $cache_file ) {
				if ( file_put_contents( $cache_file . '.html', $this->html ) === false ) {
					return false;
				}
			}

		} elseif ( $this->isJson ) {

			foreach ( $cache_files as $cache_file ) {
				if ( file_put_contents( $cache_file . '.json', $this->json ) === false ) {
					return false;
				}
			}

		} else {

			foreach ( $cache_files as $cache_file ) {
				if ( file_put_contents( $cache_file, $this->html ) === false ) {
					return false;
				}
			}

		}

		$this->_parserTranslation();

		return $this->translation === false ? false : true;

	}

	protected function _doRequest() {

		if ( $this->_doRequestHtml() ) {
			$this->isHtml = true;
			return true;
		}

		if ( $this->_doRequestJson() ) {
			$this->isJson = true;
			return true;
		}

		if ( $this->_doRequestCookies() ) {
			$this->isHtml = true;
			return true;
		}

		if ( $this->_doRequestProxy() ) {
			$this->isHtml = true;
			return true;
		}

		return false;

	}

	protected function _doRequestHtml() {

		if ( isset( $this->html ) ) {
			return true;
		}

		$params = array(
			'edit-text' => '',
			'file' => '',
			'hl' => 'en',
			'ie' => 'UTF-8',
			'js' => 'n',
			'prev' => '_t',
			'sl' => $this->from,
			'text' => $this->text,
			'tl' => $this->to,
		);

		$ch = curl_init();

		$options = array(
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_ENCODING => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HTTPGET => false,
			CURLOPT_HEADER => true,
			CURLOPT_NOPROGRESS => false,
			CURLOPT_POSTFIELDS => http_build_query( $params, '', '&' ),
			CURLOPT_REFERER => 'https://translate.google.com/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 15,
			CURLOPT_URL => 'https://translate.google.com/',
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

		if ( strpos( $body, 'short_text' ) === false && strpos( $body, 'long_text' ) === false ) {
			return false;
		}

		$this->html = $body;

		return true;

	}

	protected function _doRequestJson() {}

	protected function _doRequestCookies() {}

	protected function _doRequestProxy() {}

	protected function _parserTranslation() {

		if ( $this->isHtml && $this->_parserTranslationHtml() ) {
			return true;
		}

		if ( $this->isJson && $this->_parserTranslationJson() ) {
			return true;
		}

		if ( $this->_parserTranslationHtml() ) {
			$this->isHtml = true;
			return true;
		}

		if ( $this->_parserTranslationJson() ) {
			$this->isJson = true;
			return true;
		}

		if ( $this->_parserTranslationCookies() ) {
			return true;
		}

		if ( $this->_parserTranslationProxy() ) {
			return true;
		}

		return false;

	}

	protected function _parserTranslationHtml() {

		if ( !isset( $this->html ) ) {
			return false;
		}

		if ( isset( $this->translation ) ) {
			return $this->translation;
		}

		$string = $this->html;
		$open = false;

		if ( ( $open = strpos( $string, '<span id=result_box class="long_text">' ) ) !== false ) {
			$offset = 38;
		} elseif ( ( $open = strpos( $string, 'long_text">' ) ) !== false ) {
			$offset = 11;
		} elseif ( ( $open = strpos( $string, '<span id=result_box class="short_text">' ) ) !== false ) {
			$offset = 39;
		} elseif ( ( $open = strpos( $string, 'short_text">' ) ) !== false ) {
			$offset = 12;
		}

		if ( $open !== false ) {
			$string = trim( substr( $string, $open ) );
			$string = substr( $string, $offset );
			$close = false;
			$closes = array( '</span></span></div></div>', '<textarea', '</textarea', '<div', '</div>', '</span>', '</' );
			foreach ( $closes as $needle ) {
				if ( ( $close = strpos( $string, $needle ) ) !== false ) {
					break;
				}
			}
			$string = $close !== false ? trim( substr( $string, 0, $close ) ) : $string;
			$string = strip_tags( $string );
			$this->translation = $string;
			return true;
		}

		return false;

	}

	protected function _parserTranslationJson() {}

	protected function _parserTranslationCookies() {}

	protected function _parserTranslationProxy() {}

	public static function staticTranslate( $source_language, $target_language, $original_text, &$result = false ) {

		$result = false;

		$gt = new GoogleTranslate();
		$gt->setFrom( $source_language );
		$gt->setTo( $target_language );
		$gt->setText( $original_text );
		$gt->run();

		$result = $gt->getTranslation();

		return $result;

	}

}

?>
