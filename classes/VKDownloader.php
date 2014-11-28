<?php

class VKDownloader {

	/**
	 * [$input_url description]
	 * @var [type]
	 */
	protected $input_url;

	/**
	 * [$input_file description]
	 * @var [type]
	 */
	protected $input_file;

	/**
	 * [$input_quality description]
	 * @var [type]
	 */
	protected $input_quality;

	/**
	 * [$url description]
	 * @var [type]
	 */
	protected $url;

	/**
	 * [$file description]
	 * @var [type]
	 */
	protected $file;

	/**
	 * [$quality description]
	 * @var [type]
	 */
	protected $quality = '480p';

	/**
	 * [$oid description]
	 * @var [type]
	 */
	protected $oid;

	/**
	 * [$id description]
	 * @var [type]
	 */
	protected $id;

	/**
	 * [$hash description]
	 * @var [type]
	 */
	protected $hash;

	/**
	 * [$params description]
	 * @var [type]
	 */
	protected $params;

	/**
	 * [$file_url description]
	 * @var [type]
	 */
	protected $file_url;

	/**
	 * [$result description]
	 * @var boolean
	 */
	protected $result = false;

	/**
	 * [$curl description]
	 * @var [type]
	 */
	protected $curl;

	/**
	 * [$curls description]
	 * @var [type]
	 */
	protected $curls;

	/**
	 * [$content_length description]
	 * @var [type]
	 */
	protected $content_length;

	/**
	 * [$ranges description]
	 * @var [type]
	 */
	protected $ranges;

	/**
	 * [$sizes description]
	 * @var [type]
	 */
	protected $sizes;

	/**
	 * [$folder description]
	 * @var [type]
	 */
	protected $folder;

	/**
	 * [$fp description]
	 * @var [type]
	 */
	protected $fp;

	/**
	 * [$ch description]
	 * @var [type]
	 */
	protected $ch;

	/**
	 * [$mh description]
	 * @var [type]
	 */
	protected $mh;

	/**
	 * [$files description]
	 * @var [type]
	 */
	protected $files;

	/**
	 * [$jp description]
	 * @var [type]
	 */
	protected $jp;

	public function __construct( $url = null, $file = null, $quality = null ) {

		isset( $url ) && $this->setUrl( $url );
		isset( $file ) && $this->setFile( $file );
		isset( $quality ) && $this->setQuality( $quality );

		$this->curl = new StdClass();
		$this->curls = array();

	}

	public function setUrl( $url ) {

		$this->input_url = $url;
		$this->url = $this->_setUrl( $url );

	}

	public function setFile( $file ) {

		$this->input_file = $file;
		$this->file = $this->_setFile( $file );

	}

	public function setQuality( $quality ) {

		$this->input_quality = $quality;
		$this->quality = $this->_setQuality( $quality );

	}

	public function download( $url = null, $file = null, $quality = null ) {

		isset( $url ) && $this->setUrl( $url );
		isset( $file ) && $this->setFile( $file );
		isset( $quality ) && $this->setQuality( $quality );

		if ( !isset( $this->file ) && isset( $this->url ) ) {
			$this->file = $_SERVER['DOCUMENT_ROOT'] . '/tmp/classes/' . __CLASS__ . '/' . $this->oid . '_' . $this->id . '_' . $this->hash . '.mp4';
		}

		if ( !isset( $this->url, $this->file, $this->quality ) ) {
			return false;
		}

		if ( ( $filedir = dirname( $this->file ) ) && !file_exists( $filedir ) && !mkdir( $filedir, 0755, true ) ) {
			return false;
		}

		return $this->_download();

	}

	public function getUrl() {
		return $this->url;
	}

	public function getFile() {
		return $this->file;
	}

	public function getQuality() {
		return $this->quality;
	}

	public function getOid() {
		return $this->oid;
	}

	public function getId() {
		return $this->id;
	}

	public function getHash() {
		return $this->hash;
	}

	public function getFileUrl() {
		return $this->file_url;
	}

	protected function _setUrl( $url ) {

		if ( !is_string( $url ) ) {
			return null;
		}

		if ( preg_match( '/oid\=(\d+)(?:&amp;|&)id\=(\d+)(?:&amp;|&)hash\=([a-f0-9]+)/', $url, $match ) ) {
			$this->oid = $match[1];
			$this->id = $match[2];
			$this->hash = $match[3];
			return 'http://vk.com/video_ext.php?oid=' . $this->oid . '&id=' . $this->id . '&hash=' . $this->hash;
		}

		return null;

	}

	protected function _setFile( $file ) {

		$dirname = dirname( $file );

		if ( !is_writable( $dirname ) && $dirname === '/' ) {
			$dirname = rtrim( $_SERVER['DOCUMENT_ROOT'], '\\/' ) . DIRECTORY_SEPARATOR;
			$file = $dirname . rtrim( $file, '\\/' );
		}

		if ( is_string( $file ) && is_writable( $dirname ) ) {
			touch( $file );
			$this->file = realpath( $file );
			unlink( $file );
			return $file;
		}

	}

	protected function _setQuality( $quality ) {

		static $haystack;

		$haystack = $haystack !== null ? $haystack : array(
			'480p', '360p', '240p', '720p', '1080p'
		);

		return in_array( $quality, $haystack ) ? $quality : null;

	}

	protected function _download() {

		if ( file_exists( $this->file ) ) {
			return true;
		}

		if ( isset( $this->result ) && $this->result ) {
			return true;
		}

		return $this->downloadPage() && $this->downloadFile();

	}

	protected function downloadPage() {

		if ( !isset( $this->url ) ) {
			return false;
		}

		$url = $this->url;

		while ( true ) {

			$this->curl->ch = curl_init();

			$this->curl->options = array(
				CURLOPT_CONNECTTIMEOUT => 30,
				CURLOPT_ENCODING       => '',
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_HTTPGET        => true,
				CURLOPT_HEADER         => true,
				CURLOPT_NOPROGRESS     => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT        => 60,
				CURLOPT_URL            => $url,
				CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
				CURLOPT_VERBOSE        => true,
			);

			curl_setopt_array( $this->curl->ch, $this->curl->options );

			$this->curl->exec  = curl_exec( $this->curl->ch );
			$this->curl->info  = (object) curl_getinfo( $this->curl->ch );
			$this->curl->head  = substr( $this->curl->exec, 0, $this->curl->info->header_size );
			$this->curl->body  = substr( $this->curl->exec, $this->curl->info->header_size );
			$this->curl->error = curl_error( $this->curl->ch );
			$this->curl->errno = curl_errno( $this->curl->ch );

			curl_close( $this->curl->ch );

			if ( $this->curl->exec === false || $this->curl->error !== '' || $this->curl->errno || $this->curl->info->http_code !== 200 ) {
				return false;
			}

			if ( !preg_match( '/\<param name\=\"flashvars\" value\=\"(uid\=([^\x22]+))/', $this->curl->body, $match ) ) {
				return false;
			}

			$str = $match[1];
			$str = htmlspecialchars_decode( $str );

			parse_str( $str, $this->params );

			$this->params = (object) $this->params;

			$this->curls[] = $this->curl;
			$this->curl = new StdClass();

			return true;

			break;

		}

	}

	protected function downloadFile() {

		if ( !isset( $this->params ) || !is_object( $this->params ) ) {
			return false;
		}

		if ( $this->quality === '480p' ) {
			$vars = array( 'url480', 'cache480', 'url360', 'cache360', 'url240', 'cache240' );
		}

		foreach ( $vars as $var ) {
			if ( isset( $this->params->$var ) ) {
				$url = $this->params->$var;
				$this->quality = substr( $var, -3 ) . 'p';
				break;
			}
		}

		if ( !isset( $url ) ) {
			return false;
		}

		$this->file_url = $url;

		while ( true ) {

			$this->curl->ch = curl_init();

			$this->curl->options = array(
				CURLOPT_CONNECTTIMEOUT => 30,
				CURLOPT_ENCODING       => '',
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_HTTPGET        => true,
				CURLOPT_HEADER         => true,
				CURLOPT_NOBODY         => true,
				CURLOPT_NOPROGRESS     => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT        => 6000,
				CURLOPT_URL            => $url,
				CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
				CURLOPT_VERBOSE        => true,
			);

			curl_setopt_array( $this->curl->ch, $this->curl->options );

			$this->curl->exec  = curl_exec( $this->curl->ch );
			$this->curl->info  = (object) curl_getinfo( $this->curl->ch );
			$this->curl->head  = substr( $this->curl->exec, 0, $this->curl->info->header_size );
			$this->curl->body  = substr( $this->curl->exec, $this->curl->info->header_size );
			$this->curl->error = curl_error( $this->curl->ch );
			$this->curl->errno = curl_errno( $this->curl->ch );

			curl_close( $this->curl->ch );

			if ( $this->curl->exec === false || $this->curl->error !== '' || $this->curl->errno || $this->curl->info->http_code !== 200 ) {
				return false;
			}

			$this->curls[] = $this->curl;

			if ( strpos( $this->curl->head, 'Accept-Ranges: bytes' ) !== false && preg_match( '/Content\-Length[\x20]*\:[\x20]*(\d+)/', $this->curl->head, $match ) ) {
				$this->content_length = $match[1];
				return $this->_downloadFileRange();
			}

			return $this->_downloadFileDirect();

			break;

		}

		return false;

	}

	protected function _downloadFileDirect() {

		$file = fopen( $this->file, 'w+' );

		while ( true ) {

			$this->curl->ch = curl_init();

			$this->curl->options = array(
				CURLOPT_CONNECTTIMEOUT => 30,
				CURLOPT_ENCODING       => '',
				CURLOPT_FILE           => $file,
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_HTTPGET        => true,
				CURLOPT_HEADER         => false,
				CURLOPT_NOPROGRESS     => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT        => 6000,
				CURLOPT_URL            => $this->file_url,
				CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
				CURLOPT_VERBOSE        => true,
			);

			curl_setopt_array( $this->curl->ch, $this->curl->options );

			$this->curl->exec  = curl_exec( $this->curl->ch );
			$this->curl->info  = (object) curl_getinfo( $this->curl->ch );
			$this->curl->head  = substr( $this->curl->exec, 0, $this->curl->info->header_size );
			$this->curl->body  = substr( $this->curl->exec, $this->curl->info->header_size );
			$this->curl->error = curl_error( $this->curl->ch );
			$this->curl->errno = curl_errno( $this->curl->ch );

			curl_close( $this->curl->ch );

			fclose( $file );

			if ( $this->curl->exec === false || $this->curl->error !== '' || $this->curl->errno || $this->curl->info->http_code !== 200 ) {
				return false;
			}

			return $this->result = file_exists( $this->file );

			break;

		}

	}

	protected function _downloadFileRange() {

		$this->_setRanges();

		$defaults = array(
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_ENCODING	   => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER		   => false,
			CURLOPT_HTTPGET		   => true,
			CURLOPT_NOPROGRESS	   => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT		   => 1200,
			CURLOPT_USERAGENT	   => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1951.5 Safari/537.36',
			CURLOPT_VERBOSE		   => true
		);

		$ini_get_safe_mode = ini_get( 'safe_mode' );
		$ini_get_safe_mode = is_numeric( $ini_get_safe_mode ) ? strval( intval( $ini_get_safe_mode ) ) : strtolower( $ini_get_safe_mode );

		$ini_get_open_basedir = ini_get( 'open_basedir' );
		$ini_get_open_basedir = is_numeric( $ini_get_open_basedir ) ? strval( intval( $ini_get_open_basedir ) ) : strtolower( $ini_get_open_basedir );

		$can_follow_location = !( $ini_get_safe_mode === '1' || $ini_get_open_basedir || $ini_get_safe_mode === 'on' );

		if ( $can_follow_location ) {
			$defaults[CURLOPT_FOLLOWLOCATION] = true;
			$defaults[CURLOPT_MAXREDIRS] = 5;
		} else {
			$defaults[CURLOPT_FOLLOWLOCATION] = false;
		}

		$this->folder = $_SERVER['DOCUMENT_ROOT'] . '/' . md5( $this->url . $this->quality ) . '/';

		if ( !file_exists( $this->folder ) && !mkdir( $this->folder, 0755, true ) ) {
			return false;
		}

		foreach ( $this->ranges as $i => $range ) {

			$mode = 'w+';

			if ( file_exists( $this->folder . $i ) ) {

				list( $start, $end ) = explode( '-', $range );

				$start = intval( $start );
				$end = intval( $end );

				$filesize = filesize( $this->folder . $i );

				if ( $filesize === $this->sizes[$i] ) {
					continue;
				}

				$new_start = $start + $filesize;

				$range = $new_start . '-' . $end;

				$mode = 'a+';

			}

			$this->fp[$i] = fopen( $this->folder . $i, $mode );

			$options[$i] = $defaults;
			$options[$i][CURLOPT_URL] = $this->file_url;
			$options[$i][CURLOPT_RANGE] = $range;
			$options[$i][CURLOPT_FILE] = $this->fp[$i];

			$this->ch[$i] = curl_init();

			if ( !isset( $this->mh ) ) {
				$this->mh = curl_multi_init();
			}

			curl_setopt_array( $this->ch[$i], $options[$i] );
			curl_multi_add_handle( $this->mh, $this->ch[$i] );

		}

		if ( isset( $this->mh ) ) {

			set_time_limit(6000);

			$running = 1;

			while ( $running > 0 ) {
				curl_multi_exec( $this->mh, $running );
				curl_multi_select( $this->mh );
			}

			foreach ( $this->ranges as $i => $range ) {
				isset( $this->ch[$i] ) && curl_multi_remove_handle( $this->mh, $this->ch[$i] );
			}

			curl_multi_close( $this->mh );

			if ( isset( $this->fp ) && is_array( $this->fp ) && !empty( $this->fp ) ) {
				array_map( function( $fp ) { is_resource( $fp ) && fclose( $fp ); }, $this->fp );
			}

		}

		foreach ( $this->ranges as $i => $range ) {
			if ( !file_exists( $this->folder . $i ) ) {
				return false;
			}
		}

		return $this->result = $this->_joinFiles();

	}

	protected function _joinFiles() {

		$files_int = count( $this->ranges );
		$this->files = array();

		for ( $i = 0; $i < $files_int; $i++ ) {
			if ( !file_exists( $this->folder . $i ) ) {
				continue;
			}
			$this->files[$i] = realpath( $this->folder . $i );
		}

		if ( $this->_joinFilesRead() ) {
			$this->_deleteFiles() && $this->_deleteFolder();
			return true;
		}

		if ( $this->_joinFilesCat() ) {
			$this->_deleteFiles() && $this->_deleteFolder();
			return true;
		}

		return false;

	}

	protected function _joinFilesRead() {

		$this->jp = fopen( $this->file, 'w+' );

		foreach ( $this->files as $file ) {
			$fp = fopen( $file, 'r' );
			while( !feof( $fp ) ) {
				fwrite( $this->jp, fread( $fp, 32768 ) );
			}
			fclose( $fp );
			unset( $fp );
		}

		fclose( $this->jp );
		unset( $this->jp );

		return file_exists( $this->file );

	}

	protected function _joinFilesCat() {

		$executable = 'cat';
		$implode = '"' . implode( '" "', $this->files ) . '"';
		$command = sprintf( '%s %s > %s', $executable, $implode, $this->file );
		$exec = exec( $command, $output, $return_var );

		return ( $exec === '' && empty( $output ) && $return_var === 0 ) && file_exists( $this->file );

	}

	protected function _deleteFiles() {

		foreach ( $this->files as $file ) {
			file_exists( $file ) && unlink( $file );
		}

		return true;

	}

	protected function _deleteFolder() {

		return rmdir( $this->folder );

	}

	protected function _setRanges() {

		if ( !isset( $this->content_length ) || !is_numeric( $this->content_length ) || $this->content_length <= 0 ) {
			return array();
		}

		$content_length_int = $this->content_length;
		$connections = 5;

		if ( $content_length_int % $connections === 0 ) {
			$range_length = intval( $content_length_int / $connections );
			$start = 0;
			$end = $range_length - 1;
			$this->ranges = array(
				$start . '-' . $end
			);
			$this->sizes = array(
				( $end - $start ) + 1
			);
			for ( $i = 0; $i < $connections - 1; $i++ ) {
				$start += $range_length;
				$end += $range_length;
				$this->ranges[] = $start . '-' . $end;
				$this->sizes[] = ( $end - $start ) + 1;
			}
		} else {
			$range_length = intval( $content_length_int / $connections ) + 1;
			$last_length = $content_length_int - ( $range_length * 4 );
			$start = 0;
			$end = $range_length - 1;
			$this->ranges = array(
				$start . '-' . $end
			);
			$this->sizes = array(
				( $end - $start ) + 1
			);
			for ( $i = 0; $i < $connections - 2; $i++ ) {
				$start += $range_length;
				$end += $range_length;
				if ( $end >= $content_length_int ) {
					$end = $content_length_int - 1;
					if ( $start > $end ) {
						break;
					}
					$this->ranges[] = $start . '-' . $end;
					$this->sizes[] = ( $end - $start ) + 1;
					break;
				}
				$this->ranges[] = $start . '-' . $end;
				$this->sizes[] = ( $end - $start ) + 1;
			}
			$start = $end + 1;
			$end = $content_length_int - 1;
			if ( $start < $content_length_int ) {
				$this->ranges[] = $start . '-' . $end;
				$this->sizes[] = ( $end - $start ) + 1;
			}
		}

		return $this->ranges;

	}

	public static function staticDownload( $url, $file ) {

		$vk = new VKDownloader();
		$vk->setUrl( $url );
		$vk->setFile( $file );
		$vk->download();

		return $vk->result;

	}

}

?>
