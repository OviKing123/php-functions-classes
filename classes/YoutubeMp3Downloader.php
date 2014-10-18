<?php

class YoutubeMp3Downloader {

	/**
	 * [$url description]
	 * @var [type]
	 */
	protected $input_url;

	/**
	 * [$url description]
	 * @var [type]
	 */
	protected $url;

	/**
	 * [$current_url description]
	 * @var [type]
	 */
	protected $current_url;

	/**
	 * [$id description]
	 * @var [type]
	 */
	protected $id;

	/**
	 * [$file description]
	 * @var [type]
	 */
	protected $file;

	/**
	 * [$folder description]
	 * @var [type]
	 */
	protected $folder;

	/**
	 * [$cookiesFolder description]
	 * @var [type]
	 */
	protected $cookiesFolder;

	/**
	 * [$curl description]
	 * @var [type]
	 */
	protected $curl;

	/**
	 * [$json description]
	 * @var [type]
	 */
	protected $json;

	public function __construct() {

		$this->cookiesFolder = rtrim( sys_get_temp_dir(), '\\/' ) . DIRECTORY_SEPARATOR . 'cookies' . DIRECTORY_SEPARATOR . __CLASS__ . DIRECTORY_SEPARATOR;
		$this->cookiesFolder = rtrim( dirname( __FILE__ ), '\\/' ) . DIRECTORY_SEPARATOR . 'cookies' . DIRECTORY_SEPARATOR . __CLASS__ . DIRECTORY_SEPARATOR;
		$this->curl = new StdClass();

		if ( !file_exists( $this->cookiesFolder ) && !mkdir( $this->cookiesFolder, 0755, true ) ) {
			throw new Exception( 'Error Processing Request', 1 );
		}

	}

	public function addUrl( $url ) {

		$this->input_url = $url;
		$this->url = $this->input_url;
		$this->current_url = $this->url;
		$this->id = preg_match( '/\?v=([A-Za-z0-9\-_]+)/', $this->current_url, $match ) ? $match[1] : $this->id;

		return $this;

	}

	public function saveFile( $file ) {

		$this->file = $file;
		$this->folder = rtrim( dirname( $this->file ), '\\/' ) . DIRECTORY_SEPARATOR;

		if ( !is_writable( $this->folder ) ) {
			$this->file = null;
			$this->folder = null;
			return false;
		}

		$this->http();

	}

	protected function http() {

		$this->http_youtube_mp3_org();

	}

	protected function http_youtube_mp3_org_1() {

		$this->current_url = 'http://www.youtube-mp3.org/';

		while ( true ) {

			$this->curl->ch = curl_init();

			$this->curl->options = array(
				CURLOPT_CONNECTTIMEOUT => 15,
				CURLOPT_COOKIEJAR => $this->cookiesFolder . 'youtube-mp3.org.txt',
				CURLOPT_COOKIEFILE => $this->cookiesFolder . 'youtube-mp3.org.txt',
				CURLOPT_ENCODING => '',
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_HTTPGET => true,
				CURLOPT_HEADER => true,
				CURLOPT_NOPROGRESS => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT => 15,
				CURLOPT_URL => $this->current_url,
				CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
				CURLOPT_VERBOSE => true,
			);

			if ( ini_get( 'safe_mode' ) || ini_get( 'open_basedir' ) ) {
				$this->curl->options[CURLOPT_FOLLOWLOCATION] = false;
			} else {
				$this->curl->options[CURLOPT_FOLLOWLOCATION] = true;
				$this->curl->options[CURLOPT_MAXREDIRS] = 5;
			}

			curl_setopt_array( $this->curl->ch, $this->curl->options );

			$this->curl->exec  = curl_exec( $this->curl->ch );
			$this->curl->info  = (object) curl_getinfo( $this->curl->ch );
			$this->curl->head  = substr( $this->curl->exec, 0, $this->curl->info->header_size );
			$this->curl->body  = substr( $this->curl->exec, $this->curl->info->header_size );
			$this->curl->error = curl_error( $this->curl->ch );
			$this->curl->errno = curl_errno( $this->curl->ch );

			curl_close( $this->curl->ch );

			if ( $this->curl->exec === false || $this->curl->error !== '' || $this->curl->errno ) {
				return false;
			}

			if ( $this->curl->info->http_code !== 200 ) {
				return false;
			}

			break;

		}

		return true;

	}

	protected function http_youtube_mp3_org_2() {

		$this->current_url = 'http://www.youtube-mp3.org/a/itemInfo/?video_id=' . $this->id . '&ac=www&t=grp';

		while ( true ) {

			$this->curl->ch = curl_init();

			$this->curl->options = array(
				CURLOPT_CONNECTTIMEOUT => 15,
				CURLOPT_COOKIEJAR => $this->cookiesFolder . 'youtube-mp3.org.txt',
				CURLOPT_COOKIEFILE => $this->cookiesFolder . 'youtube-mp3.org.txt',
				CURLOPT_ENCODING => '',
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_HTTPGET => true,
				CURLOPT_HEADER => true,
				CURLOPT_NOPROGRESS => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_URL => $this->current_url,
				CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
				CURLOPT_VERBOSE => true,
			);

			if ( ini_get( 'safe_mode' ) || ini_get( 'open_basedir' ) ) {
				$this->curl->options[CURLOPT_FOLLOWLOCATION] = false;
			} else {
				$this->curl->options[CURLOPT_FOLLOWLOCATION] = true;
				$this->curl->options[CURLOPT_MAXREDIRS] = 5;
			}

			curl_setopt_array( $this->curl->ch, $this->curl->options );

			$this->curl->exec  = curl_exec( $this->curl->ch );
			$this->curl->info  = (object) curl_getinfo( $this->curl->ch );
			$this->curl->head  = substr( $this->curl->exec, 0, $this->curl->info->header_size );
			$this->curl->body  = substr( $this->curl->exec, $this->curl->info->header_size );
			$this->curl->error = curl_error( $this->curl->ch );
			$this->curl->errno = curl_errno( $this->curl->ch );

			curl_close( $this->curl->ch );

			$body = $this->curl->body;
			$body = str_replace( 'info = { "', '{ "', $body );
			$body = substr( $body, 0, -1 );

			$this->json = json_decode( $body );

			if ( !isset( $this->json->h ) ) {
				return false;
			}

			if ( $this->curl->exec === false || $this->curl->error !== '' || $this->curl->errno ) {
				return false;
			}

			if ( $this->curl->info->http_code !== 200 ) {
				return false;
			}

			break;

		}

		return true;

	}

	protected function http_youtube_mp3_org_3() {

		$this->t = strval( ( intval( microtime( true ) * 1000 ) ) );
		$this->r = strval( $this->cc( $this->id . $this->t ) );
		$this->current_url = 'http://www.youtube-mp3.org/get?ab=128&video_id=' . $this->id . '&h=' . $this->json->h . '&r=' . $this->t . '.' . $this->r;

		$retry = 0;

		set_time_limit(600);

		while ( true ) {

			if ( $retry === 5 ) {
				return false;
			}

			$this->curl->ch = curl_init();

			$this->curl->options = array(
				CURLOPT_CONNECTTIMEOUT => 15,
				CURLOPT_COOKIEJAR => $this->cookiesFolder . 'youtube-mp3.org.txt',
				CURLOPT_COOKIEFILE => $this->cookiesFolder . 'youtube-mp3.org.txt',
				CURLOPT_ENCODING => '',
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_HTTPGET => true,
				CURLOPT_HEADER => true,
				CURLOPT_NOPROGRESS => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT => 600,
				CURLOPT_URL => $this->current_url,
				CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
				CURLOPT_VERBOSE => true,
			);

			if ( ini_get( 'safe_mode' ) || ini_get( 'open_basedir' ) ) {
				#$this->curl->options[CURLOPT_FOLLOWLOCATION] = false;
			} else {
				#$this->curl->options[CURLOPT_FOLLOWLOCATION] = true;
				#$this->curl->options[CURLOPT_MAXREDIRS] = 5;
			}

			curl_setopt_array( $this->curl->ch, $this->curl->options );

			$this->curl->exec  = curl_exec( $this->curl->ch );
			$this->curl->info  = (object) curl_getinfo( $this->curl->ch );
			$this->curl->head  = substr( $this->curl->exec, 0, $this->curl->info->header_size );
			$this->curl->body  = substr( $this->curl->exec, $this->curl->info->header_size );
			$this->curl->error = curl_error( $this->curl->ch );
			$this->curl->errno = curl_errno( $this->curl->ch );

			curl_close( $this->curl->ch );

			if ( $this->curl->exec === false || $this->curl->error !== '' || $this->curl->errno ) {
				return false;
			}

			if ( $this->curl->info->http_code === 302 ) {
				if ( !preg_match( '/Location[\x20]*:[\x20]*([^\x0d\x0a]+)/', $this->curl->head, $match ) ) {
					return false;
				}
				$this->current_url = $match[1];
				$retry++;
				continue;
			}

			if ( $this->curl->info->http_code !== 200 ) {
				return false;
			}

			break;

		}

		return true;

	}

	protected function http_youtube_mp3_org() {

		if ( !$this->http_youtube_mp3_org_1() ) {
			return false;
		}

		if ( !$this->http_youtube_mp3_org_2() ) {
			return false;
		}

		if ( !$this->http_youtube_mp3_org_3() ) {
			return false;
		}

		if ( !file_exists( $this->folder ) && !mkdir( $this->folder, 0755, true ) ) {
			return false;
		}

		if ( file_put_contents( $this->file, $this->curl->body ) === false ) {
			return false;
		}

		return true;

	}

	protected function charCodeAt( $str, $num ) {
		return $this->utf8_ord( $this->utf8_charAt( $str, $num ) );
	}

	protected function utf8_ord( $ch ) {
		$len = strlen( $ch );
		if( $len <= 0 ) return false;
		$h = ord( $ch{0} );
		if ( $h <= 0x7F ) return $h;
		if ( $h < 0xC2 ) return false;
		if ( $h <= 0xDF && $len > 1 ) return ( $h & 0x1F ) << 6 | ( ord( $ch{1} ) & 0x3F );
		if ( $h <= 0xEF && $len > 2 ) return ( $h & 0x0F ) << 12 | ( ord( $ch{1} ) & 0x3F ) << 6 | ( ord( $ch{2} ) & 0x3F );
		if ( $h <= 0xF4 && $len > 3 ) return ( $h & 0x0F ) << 18 | ( ord( $ch{1} ) & 0x3F ) << 12 | ( ord( $ch{2} ) & 0x3F ) << 6 | ( ord( $ch{3} ) & 0x3F );
		return false;
	}

	protected function utf8_charAt( $str, $num ) {
		return mb_substr( $str, $num, 1, 'UTF-8' );
	}

	protected function cc( $a ) {

		$b = 1;
		$c = 0;
		$d = null;
		$e = null;

		$length = strlen( $a );

		for ( $e = 0; $e < $length; $e++ ) {
			$d = $this->charCodeAt( $a, $e );
			$b = ( $b + $d ) % 65521;
			$c = ( $c + $b ) % 65521;
		}

		$c = $c << 16 | $b;

		return $c;

	}

}

?>
