<?php

class LegendasBrasilHttp {}
class LegendasBrasilHttpPost {}
class LegendasBrasilHttpGet {}
class LegendasBrasilHttpSingle {}
class LegendasBrasilHttpDownload {}
class LegendasBrasilRaw {}
class LegendasBrasilRawGet {}
class LegendasBrasilRawSingle {}
class LegendasBrasilNormal {}
class LegendasBrasilNormalGet {}
class LegendasBrasilNormalSingle {}
class LegendasBrasilSettings {}

class LegendasBrasil {

	/**
	 * [$error description]
	 * @var [type]
	 */
	protected $error;

	/**
	 * [$run description]
	 * @var [type]
	 */
	protected $run;

	/**
	 * [$download description]
	 * @var [type]
	 */
	protected $download;

	/**
	 * [$contents description]
	 * @var [type]
	 */
	protected $contents;

	/**
	 * [$input_search description]
	 * @var [type]
	 */
	protected $input_search;

	/**
	 * [$search description]
	 * @var [type]
	 */
	protected $search;

	/**
	 * [$input_language description]
	 * @var [type]
	 */
	protected $input_language;

	/**
	 * [$language description]
	 * @var [type]
	 */
	protected $language;

	/**
	 * [$mode description]
	 * @var [type]
	 */
	protected $mode;

	/**
	 * [$http description]
	 * @var [type]
	 */
	protected $http;

	/**
	 * [$raw description]
	 * @var [type]
	 */
	protected $raw;

	/**
	 * [$normal description]
	 * @var [type]
	 */
	protected $normal;

	/**
	 * [$settings description]
	 * @var [type]
	 */
	protected $settings;

	public function __construct( $search = null, $language = null ) {

		$this->http = new LegendasBrasilHttp();
		$this->http->post = new LegendasBrasilHttpPost();
		$this->http->get = new LegendasBrasilHttpGet();
		$this->http->single = new LegendasBrasilHttpSingle();
		$this->http->download = new LegendasBrasilHttpDownload();

		$this->raw = new LegendasBrasilRaw();
		$this->raw->get = new LegendasBrasilRawGet();
		$this->raw->single = new LegendasBrasilRawSingle();

		$this->normal = new LegendasBrasilNormal();
		$this->normal->get = new LegendasBrasilNormalGet();
		$this->normal->single = new LegendasBrasilNormalSingle();

		$this->settings = new LegendasBrasilSettings();
		$this->settings->language_locale = false;
		$this->settings->language_acronym = false;
		$this->settings->title_perfect = false;
		$this->settings->title_similar = false;
		$this->settings->title_alternative = false;

		isset( $search ) && $this->setSearch( $search );
		isset( $language ) && $this->setLanguage( $language );

		isset( $this->search ) && isset( $this->language ) && $this->run();

	}

	public function setSearch( $search ) {

		$this->input_search = $search;
		$this->search = $this->_setSearch( $search );

		return $this;

	}

	public function setLanguage( $language ) {

		$this->input_language = $language;
		$this->language = $this->_setLanguage( $language );

		return $this;

	}

	public function run( $search = null, $language = null ) {

		isset( $search ) && $this->setSearch( $search );
		isset( $language ) && $this->setLanguage( $language );

		if ( !isset( $this->language ) ) {
			$this->language = 'pt-br';
		}

		if ( !isset( $this->search ) ) {
			$this->_setError( 'Public_Run_Search_Undefined' );
			return false;
		}

		if ( !isset( $this->language ) ) {
			$this->_setError( 'Public_Run_Language_Undefined' );
			return false;
		}

		return $this->_run();

	}

	public function download( $search = null, $language = null ) {

		if ( !isset( $this->run ) ) {

			isset( $search ) && $this->setSearch( $search );
			isset( $language ) && $this->setLanguage( $language );

			if ( !$this->run() ) {
				$this->_setError( 'Public_Download_Run_Function_False' );
				return false;
			}

		}

		if ( !$this->_download() ) {
			$this->_setError( 'Public_Download_Download_Function_False' );
			return false;
		}

		return true;

	}

	public function getResults() {

		return isset( $this->normal->get->current_results ) ? $this->normal->get->current_results : array();

	}

	public function getResultsPrint() {

		return print_r( $this->getResults(), true );

	}

	public function getResultsPrintPre() {

		return '<pre>' . print_r( $this->getResults(), true ) . '</pre>';

	}

	public function hasError() {

		return isset( $this->error ) || !isset( $this->run );

	}

	public function getError() {

		return isset( $this->error ) ? $this->error : 'GetError';

	}

	/**
	 * Atalho para habilitar Locale e Acronym
	 * @return object LegendasBrasil
	 */
	public function enableLanguageAll() {

		$this->enableLanguageLocale();
		$this->enableLanguageAcronym();

		return $this;

	}

	/**
	 * Habilita download tanto de legendas PT-BR quanto PT-PT
	 * @return object LegendasBrasil
	 */
	public function enableLanguageLocale() {

		$this->settings->language_locale = true;

		return $this;

	}

	/**
	 * Habilita download dos sub-tipos POR e POB 
	 * @return object LegendasBrasil
	 */
	public function enableLanguageAcronym() {

		$this->settings->language_acronym = true;

		return $this;

	}

	/**
	 * Habilita download apenas de títulos perfeitos em comparação com o valor de $search
	 * @return object LegendasBrasil
	 */
	public function enableTitlePerfect() {

		$this->settings->title_perfect = true;

		return $this;

	}

	/**
	 * Habilita download baseado em títulos similares com diferenças mínimas (espaços, pontos, traços)
	 * @return object LegendasBrasil
	 */
	public function enableTitleSimilar() {

		$this->settings->title_similar = true;

		return $this;

	}

	/**
	 * Habilita download alternativo, baseado no título, RIP, fps e outros fatores
	 * @return object LegendasBrasil
	 */
	public function enableTitleAlternative() {

		$this->settings->title_alternative = true;

		return $this;

	}

	public function hasDownload() {

		if ( !isset( $this->http->download->cache_file ) || !file_exists( $this->http->download->cache_file ) ) {
			return false;
		}

		return true;

	}

	public function extractDownload() {

		if ( !$this->hasDownload() ) {
			return false;
		}

		if ( file_exists( dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'zip_list' ) ) {

			$filenames = explode( "\x0a", file_get_contents( dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'zip_list' ) );
			$folder_files = dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;

			$is_true = true;

			foreach ( $filenames as $filename ) {
				if ( !file_exists( $folder_files . $filename ) || filesize( $folder_files . $filename ) === 0 ) {
					$is_true = false;
					break;
				}
			}

			if ( $is_true ) {
				return true;
			}

		}

		if ( class_exists( 'ZipArchive' ) ) {

			$zip = new ZipArchive;

			if ( $zip->open( $this->http->download->cache_file ) !== true ) {
				$zip->close();
				return false;
			}

			$filename = '';
			$filenames = array();

			for ( $i = 0; $i < $zip->numFiles; $i++ ) {

				$filename = $zip->getNameIndex( $i );

				if ( strtolower( substr( $filename, -4 ) ) !== '.srt' ) {
					continue;
				}

				$filenames[] = $filename;

			}

			if ( !$filenames ) {
				return false;
			}

			$folder_files = dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'files';

			if ( !file_exists( $folder_files ) && !mkdir( $folder_files ) ) {
				return false;
			}

			if ( !$zip->extractTo( $folder_files, $filenames ) ) {
				$zip->close();
				return false;
			}

			if ( !$zip->close() ) {
				return false;
			}

			file_put_contents( dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'zip_list', implode( "\x0a", $filenames ) );

			return true;

		}

		return true;

	}

	public function fixSubtitle() {

		if ( !$this->hasDownload() ) {
			return false;
		}

		$folder_files = dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
		$filenames = array();

		if ( file_exists( dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'zip_list' ) ) {

			$filenames = explode( "\x0a", file_get_contents( dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'zip_list' ) );

			foreach ( $filenames as $key => $value ) {
				$filenames[$key] = $folder_files . $value;
			}

		} elseif ( is_dir( $folder_files ) ) {

			if ( ( $handle = opendir( $folder_files ) ) === false ) {
				closedir( $handle );
				return false;
			}

			$filenames = array();

			while ( ( $entry = readdir( $handle ) ) !== false ) {
				if ( $entry === '.' || $entry === '..' ) {
					continue;
				}
				$filenames[] = $folder_files . $entry;
			}

			closedir( $handle );

		} else {

			return false;

		}

		if ( empty( $filenames ) ) {
			return false;
		}

		foreach ( $filenames as $filename ) {

			if ( !is_file( $filename ) ) {
				return false;
			}

			$contents = file_get_contents( $filename );

			if ( stripos( $contents, 'http://' ) !== false ) {
				return false;
			}

			if ( stripos( $contents, 'http://' ) !== false ) {
				return false;
			}

		}

		return true;

	}

	public function getContents() {

		if ( isset( $this->contents ) ) {
			return $this->contents;
		}

		if ( !$this->hasDownload() ) {
			return false;
		}

		$folder_files = dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
		$filenames = array();

		if ( file_exists( dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'zip_list' ) ) {

			$filenames = explode( "\x0a", file_get_contents( dirname( $this->http->download->cache_file ) . DIRECTORY_SEPARATOR . 'zip_list' ) );

			foreach ( $filenames as $key => $value ) {
				$filenames[$key] = $folder_files . $value;
			}

		} elseif ( is_dir( $folder_files ) ) {

			if ( ( $handle = opendir( $folder_files ) ) === false ) {
				closedir( $handle );
				return false;
			}

			$filenames = array();

			while ( ( $entry = readdir( $handle ) ) !== false ) {
				if ( $entry === '.' || $entry === '..' ) {
					continue;
				}
				$filenames[] = $folder_files . $entry;
			}

			closedir( $handle );

		} else {

			return false;

		}

		if ( empty( $filenames ) ) {
			return false;
		}

		foreach ( $filenames as $filename ) {

			if ( !is_file( $filename ) ) {
				return false;
			}

			if ( ( $tmp = file_get_contents( $filename ) ) === false ) {
				return false;
			}

			return $this->contents = $tmp;

		}

		return false;

	}

	protected function _setSearch( $search ) {

		if ( preg_match( '/(?:[A-Za-z0-9]+\.)+(?:YIFY)/', $search ) ) {
			$search = str_replace( ' ', '.', $search );
		}

		return $search;

	}

	protected function _setLanguage( $language ) {

		static $languages;

		$language = strtolower( $language );

		$languages = $languages !== null ? $languages : array(

			'pt-br' => 'pt-br',
			'pt_br' => 'pt-br',
			'pt br' => 'pt-br',
			'ptbr' => 'pt-br',

			'pt-pt' => 'pt-pt',
			'pt_pt' => 'pt-pt',
			'pt pt' => 'pt-pt',
			'ptpt' => 'pt-pt',

			'en-us' => 'en-us',
			'en_us' => 'en-us',
			'en us' => 'en-us',
			'enus' => 'en-us',

			'es-es' => 'es-es',
			'es_es' => 'es-es',
			'es es' => 'es-es',
			'eses' => 'es-es',

			'brazil' => 'pt-br',
			'brasil' => 'pt-br',
			'br' => 'pt-br',

			'portugal' => 'pt-pt',
			'pt' => 'pt-pt',

			'spain' => 'es-es',
			'españa' => 'es-es',
			'espana' => 'es-es',
			'espanha' => 'es-es',
			'es' => 'es-es',

			'usa' => 'en-us',
			'eeuuaa' => 'en-us',
			'eeuu' => 'en-us',
			'eua' => 'en-us',
			'us' => 'en-us',

		);

		return isset( $languages[$language] ) ? $languages[$language] : null;

	}

	protected function _setError( $error ) {

		if ( isset( $this->error ) ) {
			$this->error .= '|' . strval( $error );
		} else {
			$this->error = strval( $error );
		}

		return true;

	}

	protected function _run() {

		if ( isset( $this->run ) ) {
			return true;
		}

		if ( !$this->_http_post() ) {
			$this->_setError( 'Protected_Run_Http_Post_Function_False' );
			return false;
		}

		if ( !$this->_http_get() ) {
			$this->_setError( 'Protected_Run_Http_Get_Function_False' );
			return false;
		}

		if ( !$this->_parse_get() ) {
			$this->_setError( 'Protected_Run_Parse_Get_Function_False' );
			return false;
		}

		$this->run = true;

		return true;

	}

	protected function _download() {

		if ( isset( $this->download ) ) {
			return true;
		}

		if ( !isset( $this->normal->get->current_results ) ) {
			$this->_setError( 'Protected_Download_This_Normal_Get_Current_Results_Undefined' );
			return false;
		}

		if ( !$this->_define_single() ) {
			$this->_setError( 'Protected_Download_Define_Single_Function_False' );
			return false;
		}

		if ( !$this->_http_single() ) {
			$this->_setError( 'Protected_Download_Http_Single_Function_False' );
			return false;
		}

		if ( !$this->_parse_single() ) {
			$this->_setError( 'Protected_Download_Parse_Single_Function_False' );
			return false;
		}

		if ( !$this->_define_download() ) {
			$this->_setError( 'Protected_Download_Define_Download_Function_False' );
			return false;
		}

		if ( !$this->_http_download() ) {
			$this->_setError( 'Protected_Download_Http_Download_Function_False' );
			return false;
		}

		$this->download = true;

		return true;

	}

	protected function _hasError() {

		return isset( $this->error );

	}

	protected function _http_post() {

		if ( !isset( $this->search ) ) {
			$this->_setError( 'Protected_Http_Post_Search_Undefined' );
			return false;
		}

		$this->http->post->url = 'https://www.legendasbrasil.org/index.php';
		#$this->http->post->cache_hash = md5( $this->search );
		$this->http->post->cache_hash = urlencode( $this->search );

		if ( defined( 'ABSPATH' ) ) {
			$this->http->post->cache_folder = ABSPATH . 'wp-content/cache/class-legendas-brasil/post/';
		} else {
			$this->http->post->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/post/';
		}

		$this->http->post->cache_folder = $this->http->post->cache_folder . $this->http->post->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->post->cache_file = $this->http->post->cache_folder . 'body';

		if ( file_exists( $this->http->post->cache_folder . 'head' ) && strpos( file_get_contents( $this->http->post->cache_folder . 'head' ), 'HTTP/1.1 200 OK' ) === 0 && file_exists( $this->http->post->cache_file ) ) {
			$this->http->post->ch = null;
			$this->http->post->options = array();
			$this->http->post->exec = '';
			$this->http->post->info = unserialize( file_get_contents( $this->http->post->cache_folder . 'info' ) );
			$this->http->post->head = file_get_contents( $this->http->post->cache_folder . 'head' );
			$this->http->post->body = file_get_contents( $this->http->post->cache_folder . 'body' );
			$this->http->post->error = file_get_contents( $this->http->post->cache_folder . 'error' );
			$this->http->post->errno = intval( file_get_contents( $this->http->post->cache_folder . 'errno' ) );
			return $this->http->post->body;
		}

		if ( !file_exists( $this->http->post->cache_folder ) && !mkdir( $this->http->post->cache_folder, 0755, true ) ) {
			$this->_setError( 'Protected_Http_Post_File_Exists_Mk_Dir' );
			return false;
		}

		$this->http->post->params = array(
			'action' => 'dynamic_search',
			'query' => $this->search,
		);

		$this->http->post->fields = http_build_query( $this->http->post->params, '', '&' );

		set_time_limit(60);

		$this->http->post->ch = curl_init();

		$this->http->post->options = array(
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_ENCODING       => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => true,
			CURLOPT_NOPROGRESS     => false,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $this->http->post->fields,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_URL            => $this->http->post->url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
			CURLOPT_VERBOSE        => true,
		);

		curl_setopt_array( $this->http->post->ch, $this->http->post->options );

		$this->http->post->exec  = curl_exec( $this->http->post->ch );
		$this->http->post->info  = (object) curl_getinfo( $this->http->post->ch );
		$this->http->post->head  = substr( $this->http->post->exec, 0, $this->http->post->info->header_size );
		$this->http->post->body  = substr( $this->http->post->exec, $this->http->post->info->header_size );
		$this->http->post->error = curl_error( $this->http->post->ch );
		$this->http->post->errno = curl_errno( $this->http->post->ch );

		curl_close( $this->http->post->ch );

		if ( $this->http->post->exec === false ) {
			$this->_setError( 'Protected_Http_Post_Curl_Exec' );
			return false;
		}

		if ( $this->http->post->error !== '' ) {
			$this->_setError( 'Protected_Http_Post_Curl_Error' );
			return false;
		}

		if ( $this->http->post->errno ) {
			$this->_setError( 'Protected_Http_Post_Curl_Errno' );
			return false;
		}

		if ( $this->http->post->info->http_code !== 200 ) {
			$this->_setError( 'Protected_Http_Post_Curl_Info_Http_Code_Diff_200' );
			return false;
		}

		file_put_contents( $this->http->post->cache_folder . 'info', serialize( $this->http->post->info ) );
		file_put_contents( $this->http->post->cache_folder . 'head', $this->http->post->head );
		file_put_contents( $this->http->post->cache_folder . 'body', $this->http->post->body );
		file_put_contents( $this->http->post->cache_folder . 'error', $this->http->post->error );
		file_put_contents( $this->http->post->cache_folder . 'errno', $this->http->post->errno );
		file_put_contents( $this->http->post->cache_folder . 'url', $this->http->post->info->url );

		return $this->http->post->body ? $this->http->post->body : true;

	}

	protected function _http_get() {

		if ( !isset( $this->search ) ) {
			$this->_setError( 'Protected_Http_Get_Search_Undefined' );
			return false;
		}

		$this->http->get->url = 'https://www.legendasbrasil.org/legendas-' . urlencode( $this->search ) . '-0-all-1.htm';
		#$this->http->get->cache_hash = md5( $this->search );
		$this->http->get->cache_hash = urlencode( $this->search );

		if ( defined( 'ABSPATH' ) ) {
			$this->http->get->cache_folder = ABSPATH . 'wp-content/cache/class-legendas-brasil/get/';
		} else {
			$this->http->get->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/get/';
		}

		$this->http->get->cache_folder = $this->http->get->cache_folder . $this->http->get->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->get->cache_file = $this->http->get->cache_folder . 'body';

		if ( file_exists( $this->http->get->cache_file ) && filesize( $this->http->get->cache_file ) !== 0 ) {
			$this->http->get->ch = null;
			$this->http->get->options = array();
			$this->http->get->exec = '';
			$this->http->get->info = unserialize( file_get_contents( $this->http->get->cache_folder . 'info' ) );
			$this->http->get->head = file_get_contents( $this->http->get->cache_folder . 'head' );
			$this->http->get->body = file_get_contents( $this->http->get->cache_folder . 'body' );
			$this->http->get->error = file_get_contents( $this->http->get->cache_folder . 'error' );
			$this->http->get->errno = intval( file_get_contents( $this->http->get->cache_folder . 'errno' ) );
			return $this->http->get->body;
		}

		if ( !file_exists( $this->http->get->cache_folder ) && !mkdir( $this->http->get->cache_folder, 0755, true ) ) {
			$this->_setError( 'Protected_Http_Get_File_Exists_Mk_Dir' );
			return false;
		}

		set_time_limit(60);

		$this->http->get->ch = curl_init();

		$this->http->get->options = array(
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_ENCODING       => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => true,
			CURLOPT_HTTPGET        => true,
			CURLOPT_NOPROGRESS     => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_URL            => $this->http->get->url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
			CURLOPT_VERBOSE        => true,
		);

		curl_setopt_array( $this->http->get->ch, $this->http->get->options );

		$this->http->get->exec  = curl_exec( $this->http->get->ch );
		$this->http->get->info  = (object) curl_getinfo( $this->http->get->ch );
		$this->http->get->head  = substr( $this->http->get->exec, 0, $this->http->get->info->header_size );
		$this->http->get->body  = substr( $this->http->get->exec, $this->http->get->info->header_size );
		$this->http->get->error = curl_error( $this->http->get->ch );
		$this->http->get->errno = curl_errno( $this->http->get->ch );

		curl_close( $this->http->get->ch );

		if ( $this->http->get->exec === false ) {
			$this->_setError( 'Protected_Http_Get_Curl_Exec' );
			return false;
		}

		if ( $this->http->get->error !== '' ) {
			$this->_setError( 'Protected_Http_Get_Curl_Error' );
			return false;
		}

		if ( $this->http->get->errno ) {
			$this->_setError( 'Protected_Http_Get_Curl_Errno' );
			return false;
		}

		if ( $this->http->get->info->http_code !== 200 ) {
			$this->_setError( 'Protected_Http_Get_Curl_Info_Http_Code_Diff_200' );
			return false;
		}

		file_put_contents( $this->http->get->cache_folder . 'info', serialize( $this->http->get->info ) );
		file_put_contents( $this->http->get->cache_folder . 'head', $this->http->get->head );
		file_put_contents( $this->http->get->cache_folder . 'body', $this->http->get->body );
		file_put_contents( $this->http->get->cache_folder . 'error', $this->http->get->error );
		file_put_contents( $this->http->get->cache_folder . 'errno', $this->http->get->errno );
		file_put_contents( $this->http->get->cache_folder . 'url', $this->http->get->info->url );

		return $this->http->get->body ? $this->http->get->body : true;

	}

	protected function _parse_get() {

		if ( !isset( $this->http->get->body ) ) {
			$this->_setError( 'Protected_Parse_Get_Http_Get_Body_Undefined' );
			return false;
		}

		$html = $this->http->get->body;

		if ( !preg_match( '/\<div\b[^\x3e]*id\=\"divResult\"[^\x3e]*\>/', $html, $match, PREG_OFFSET_CAPTURE ) ) {
			$this->_setError( 'Protected_Parse_Get_Http_Get_Body_Undefined' );
			return false;
		}

		$html = substr( $html, $match[0][1] );

		if ( preg_match( '/\<div\b[^\x3e]*id\=\"footer\"[^\x3e]*\>/', $html, $match, PREG_OFFSET_CAPTURE ) ) {
			$html = substr( $html, 0, $match[0][1] );
		}

		$backup = $html;

		$offsets = array();
		$lengths = array();
		$starts = array();
		$start = 0;
		$offset = 0;
		$counter = 0;

		while ( true ) {

			if ( !preg_match( '/\<div\b[^\x3e]*class\=\"divResults\"[^\x3e]*\>/', substr( $html, $start ), $match, PREG_OFFSET_CAPTURE ) ) {
				break;
			}

			if ( $counter > 20 ) {
				break;
			}

			$offset = $match[0][1];
			$length = strlen( $match[0][0] );
			$start += $offset + $length;

			$offsets[] = $offset;
			$lengths[] = $length;
			$starts[] = $start;

			$counter++;

		}

		if ( empty( $offsets ) ) {
			$this->_setError( 'Protected_Parse_Get_Http_Get_Offsets_Empty' );
			return false;
		}

		$divs = array();
		$counter = 0;

		while ( true ) {

			if ( $counter > 20 ) {
				break;
			}

			if ( !isset( $starts[$counter] ) ) {
				break;
			}

			if ( !isset( $starts[$counter+1] ) ) {
				$divs[$counter] = substr( $html, $starts[$counter] - $lengths[$counter] );
				break;
			}

			$divs[$counter] = substr( $html, $starts[$counter] - $lengths[$counter], $starts[$counter+1] - $starts[$counter] );

			$counter++;

		}

		foreach ( $divs as $key => $value ) {
			if ( strpos( $divs[$key], 'Legenda:' ) === false ) {
				unset( $divs[$key] );
			}
		}

		$divs = array_values( $divs );

/*

\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+\.YIFY\-(?:por|pob)\.srt])</td>
\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+\.YIFY\.srt])</td>
\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+\.srt])</td>

1

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:\x54\xc3\xad\x74\x75\x6c\x6f|\x54\xed\x74\x75\x6c\x6f)[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>
[\x09\x0a\x0b\x0c\x0d\x20]*
\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<a\b[^\x3e]*href\=[\x22\x27]?([^\x22\x27]+)[\x22\x27]?[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(([^\x3c]+) \((\d{4})\))[\x09\x0a\x0b\x0c\x0d\x20]*\<\/a\>

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:\x54\xc3\xad\x74\x75\x6c\x6f|\x54\xed\x74\x75\x6c\x6f)[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<a\b[^\x3e]*href\=[\x22\x27]?([^\x22\x27]+)[\x22\x27]?[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(([^\x3c]+) \((\d{4})\))[\x09\x0a\x0b\x0c\x0d\x20]*\<\/a\>

2

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*Legenda[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>
[\x09\x0a\x0b\x0c\x0d\x20]*
\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*Legenda[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>

3

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:Idioma)[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>
[\x09\x0a\x0b\x0c\x0d\x20]*
\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<img\b[^\x3e]*src\=[\x22\x27]?[^\x22]*(portuguese\-br\.gif|portuguese\-pt\.gif)[^\x22]*[\x22\x27]?[^\x3e]*\>

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:Idioma)[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<img\b[^\x3e]*src\=[\x22\x27]?[^\x22]*(portuguese\-br\.gif|portuguese\-pt\.gif)[^\x22]*[\x22\x27]?[^\x3e]*\>

4

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*Formato[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>
[\x09\x0a\x0b\x0c\x0d\x20]*
\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*Formato[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>

5

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*Visualizações[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>
[\x09\x0a\x0b\x0c\x0d\x20]*
\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>

\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*Visualizações[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>

*/

		$td_pattern = '';

		$td_pattern .= '/';

		/**
		 * Título = (?:\x54\xc3\xad\x74\x75\x6c\x6f|\x54\xed\x74\x75\x6c\x6f)
		 * Visualizações = (?:\x56\x69\x73\x75\x61\x6c\x69\x7a\x61\xc3\xa7\xc3\xb5\x65\x73|\x56\x69\x73\x75\x61\x6c\x69\x7a\x61\xe7\xf5\x65\x73)
		 */

		$td_pattern .= '\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:\x54\xc3\xad\x74\x75\x6c\x6f|\x54\xed\x74\x75\x6c\x6f)[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<a\b[^\x3e]*href\=[\x22\x27]?(legendas\-([a-z0-9]+[a-z0-9\-]+[a-z0-9]+)\-(\d+)\-(\d+)\-pagina\-(\d+)\.htm)[\x22\x27]?[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(([^\x3c]+) \((\d{4})\))[\x09\x0a\x0b\x0c\x0d\x20]*\<\/a\>';

		$td_pattern .= '[\x09\x0a\x0b\x0c\x0d\x20]*';
		$td_pattern .= '.*';
		$td_pattern .= '\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*Legenda[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>';

		$td_pattern .= '[\x09\x0a\x0b\x0c\x0d\x20]*';
		$td_pattern .= '.*';
		$td_pattern .= '\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:Idioma)[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<img\b[^\x3e]*src\=[\x22\x27]?[^\x22]*(portuguese\-br\.gif|portuguese\-pt\.gif|portuguese\.gif)[^\x22]*[\x22\x27]?[^\x3e]*\>';

		$td_pattern .= '[\x09\x0a\x0b\x0c\x0d\x20]*';
		$td_pattern .= '.*';
		$td_pattern .= '\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:Formato)[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>';

		$td_pattern .= '[\x09\x0a\x0b\x0c\x0d\x20]*';
		$td_pattern .= '.*';
		$td_pattern .= '\<td\b[^\x3e]*class\=\"title\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:\x56\x69\x73\x75\x61\x6c\x69\x7a\x61\xc3\xa7\xc3\xb5\x65\x73|\x56\x69\x73\x75\x61\x6c\x69\x7a\x61\xe7\xf5\x65\x73)[\x09\x0a\x0b\x0c\x0d\x20]*\:[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>[\x09\x0a\x0b\x0c\x0d\x20]*\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/td\>';

		$td_pattern .= '[\x09\x0a\x0b\x0c\x0d\x20]*';
		$td_pattern .= '.*';
		$td_pattern .= '\<td\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*.+\<a\b[^\x3e]*href\=[\x22\x27]?(legenda\-([a-z0-9]+[a-z0-9\-]+[a-z0-9]+)-(\d+)-(\d+)\.htm)[\x22\x27]?[^\x3e]*>[\x09\x0a\x0b\x0c\x0d\x20]*Baixar Legenda[\x09\x0a\x0b\x0c\x0d\x20]*\<\/a\>';

		$td_pattern .= '/';

		/**
		 * Todos os resultados
		 * @var array
		 */
		$this->raw->get->all_results = array();

		/**
		 * Todos os IDs de resultados separados por idioma da legenda
		 * @var array
		 */
		$this->raw->get->languages['pt-br'] = array();
		$this->raw->get->languages['pt-pt'] = array();
		$this->raw->get->languages['en-us'] = array();
		$this->raw->get->languages['es-es'] = array();

		$this->raw->get->current_results = array();

		/**
		 * Todos os IDs de resultados separados por idioma da legenda
		 * @var array
		 */
		$this->raw->get->languages['por'] = array();
		$this->raw->get->languages['pob'] = array();

		$td_regex_error = false;
		$href_regex_error = false;
		$regex_error = false;

		foreach ( $divs as $key => $value ) {

			$value = str_replace( array( "\x0d\x0a", "\x0d", "\x0a" ), "\x20", $value );

			if ( !preg_match( $td_pattern, $value, $td_match ) ) {
				$td_regex_error = true;
				$regex_error = true;
				break;
			}

			$array = array();
			$array['id'] = $td_match[16];
			$array['url'] = $td_match[13];
			$array['subtitle'] = $td_match[9];
			$array['language'] = $td_match[10];
			$array['title'] = $td_match[6];

			$array['title_name'] = $td_match[7];
			$array['title_year'] = $td_match[8];
			$array['format'] = $td_match[11];
			$array['views'] = $td_match[12];
			$array['url_slug'] = $td_match[14];
			$array['url_year'] = $td_match[15];

			$array['permalink_url'] = $td_match[1];
			$array['permalink_slug'] = $td_match[2];
			$array['permalink_year'] = $td_match[3];
			$array['permalink_id'] = $td_match[4];
			$array['permalink_page'] = $td_match[5];

			/**
			 * id,url,subtitle,language
			 */

			if ( $array['language'] === 'portuguese-br.gif' ) {

				$this->raw->get->languages['pt-br'][] = $array;

			} elseif ( $array['language'] === 'portuguese-pt.gif' || $array['language'] === 'portuguese.gif' ) {

				$this->raw->get->languages['pt-pt'][] = $array;

			} else {

				$regex_error = true;
				break;

			}

			$eight = strtolower( substr( $array['subtitle'], -8 ) );

			if ( $eight === '-por.srt' ) {

				$this->raw->get->languages['por'][] = $array;

			} elseif ( $eight === '-pob.srt' ) {

				$this->raw->get->languages['pob'][] = $array;

			}

			$this->raw->get->all_results[] = $array;

			$counter++;

		}

		if ( $regex_error ) {
			$this->_setError( 'Protected_Parse_Get_Http_Get_Regex_Error' );
			return false;
		}

		if ( !isset( $this->raw->get->languages[$this->language] ) || empty( $this->raw->get->languages[$this->language] ) ) {

			if ( $this->language === 'pt-pt' && isset( $this->raw->get->languages['por'] ) && !empty( $this->raw->get->languages['por'] ) ) {

				$this->raw->get->current_results = $this->raw->get->languages['por'];

			} elseif ( $this->language === 'pt-br' && isset( $this->raw->get->languages['pob'] ) && !empty( $this->raw->get->languages['pob'] ) ) {

				$this->raw->get->current_results = $this->raw->get->languages['pob'];

			} else {

				return false;

			}

			$this->mode = 'ALTERNATIVE';

		} else {

			$this->raw->get->current_results = $this->raw->get->languages[$this->language];
			$this->mode = 'LANGUAGE';

		}

		return $this->_rawGetToNormal();

	}

	protected function _rawGetToNormal() {

		if ( !isset( $this->raw ) ) {
			$this->_setError( 'Protected_RawGetToNormal_Raw_Undefined' );
			return false;
		}

		if ( !isset( $this->raw ) ) {
			$this->_setError( 'Protected_RawGetToNormal_Raw_Undefined' );
			return false;
		}

		foreach ( $this->raw->get->current_results as $key => $value ) {

			$array = array();
			$array['id'] = $value['id'];

			if ( preg_match( '/(legenda\-([a-z0-9]+[a-z0-9\-]+[a-z0-9]+)-(\d+)-(\d+)\.htm)/', $value['url'], $match ) ) {
				$value['url'] = 'https://www.legendasbrasil.org/' . $match[1];
			}

			$array['url'] = $value['url'];
			$array['subtitle'] = $value['subtitle'];

			if ( in_array( $value['language'], array( 'portuguese-br.gif' ) ) ) {
				$value['language'] = 'pt-br';
			} elseif ( in_array( $value['language'], array( 'portuguese-pt.gif', 'portuguese.gif' ) ) ) {
				$value['language'] = 'pt-pt';
			}

			$this->normal->get->current_results[$key] = $array;

		}

		return isset( $this->normal->get->current_results ) && !empty( $this->normal->get->current_results );

	}

	protected function _parse_single() {

		if ( !isset( $this->http->single->body ) ) {
			$this->_setError( 'Protected_Parse_Single_This_Http_Single_Body_Undefined' );
			return false;
		}

		if ( !preg_match( '/http\:\/\/www\.getsubtitle\.com\/webService\/download_subtitle\.php\?post_date\=(\d+)\-(\d+)\-(\d+)(?:&amp;|&)cod_bsplayer\=(\d+)/', $this->http->single->body, $match ) ) {
			$this->_setError( 'Protected_Parse_Single_Current_Download_Url_Preg_Match_False' );
			return false;
		}

		$this->raw->single->current_download_url = $match[0];

		if ( preg_match( '/\<img\b[^\x3e]*id\=[\x22\x27]?ktposter[\x22\x27]?[^\x3e]*src\=(?:\x22([^\x22]+)\x22|\x27([^\x27]+)\x27|([^\x20\x22\x27]+))/', $this->http->single->body, $match ) ) {
			$this->raw->single->current_cover_url = $match[1];
		}

		if ( preg_match( '/\<div\b[^\x3e]*id\=[\x22\x27]?preview[\x22\x27]?[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<h3\>[\x09\x0a\x0b\x0c\x0d\x20]*(?:\x50\x72\xc3\xa9\x2d\x76\x69\x73\x75\x61\x6c\x69\x7a\x61\xc3\xa7\xc3\xa3\x6f|\x50\x72\xe9\x2d\x76\x69\x73\x75\x61\x6c\x69\x7a\x61\xe7\xe3\x6f)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/h3\>[\x09\x0a\x0b\x0c\x0d\x20]*\<div\b[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([\s\S]+)(?:\<\/div\>[\x09\x0a\x0b\x0c\x0d\x20]*\<br\/\>[\x09\x0a\x0b\x0c\x0d\x20]*\<\/div\>|\<div\b[^\x3e]*style\=\"clear\:both;margin\:10px 0 0 15px;\"[^\x3e]*\>\<script\b[^\x3e]*(?:async)?[^\x3e]*src\=\"\/\/pagead2\.googlesyndication\.com\/pagead\/js\/adsbygoogle\.js\"[^\x3e]*\>\<\/script\>)/', $this->http->single->body, $match ) ) {
			$this->raw->single->current_preview = $match[1];
		}

		if ( preg_match( '/\<div\b[^\x3e]*id\=[\x22\x27]sinopse[\x22\x27][^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<h3\>[\x09\x0a\x0b\x0c\x0d\x20]*Sinopse\<\/h3\>[\x09\x0a\x0b\x0c\x0d\x20]*\<div\b[^\x3e]*id=[\x22\x27]?plot[\x22\x27]?[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([\s\S]+)\<\/div\>[\x09\x0a\x0b\x0c\x0d\x20]*\<\/div\>[\x09\x0a\x0b\x0c\x0d\x20]*\<br\/\>[\x09\x0a\x0b\x0c\x0d\x20]*\<div\b[^\x3e]*id\=[\x22\x27]?top\-subtitles[\x22\x27]?[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<h3\>[\x09\x0a\x0b\x0c\x0d\x20]*Legendas mais baixadas[\x09\x0a\x0b\x0c\x0d\x20]*\<\/h3\>/', $this->http->single->body, $match ) ) {
			$description = trim( $match[1] );
			$this->raw->single->current_description = $description;
		} elseif ( preg_match( '/\<div\b[^\x3e]*id\=[\x22\x27]sinopse[\x22\x27][^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*\<h3\>[\x09\x0a\x0b\x0c\x0d\x20]*Sinopse\<\/h3\>[\x09\x0a\x0b\x0c\x0d\x20]*\<div\b[^\x3e]*id=[\x22\x27]?plot[\x22\x27]?[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+[^\x09\x0a\x0b\x0c\x0d\x20\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*/', $this->http->single->body, $match ) ) {
			$description = trim( $match[1] );
			$this->raw->single->current_description = $description;
		}

		return $this->_rawSingleToNormal();

	}

	protected function _rawSingleToNormal() {

		if ( !isset( $this->raw->single ) ) {
			$this->_setError( 'Protected_RawSingleToNormal_This_Raw_Single_Undefined' );
			return false;
		}

		$this->normal->single->current_download_url = isset( $this->raw->single->current_download_url ) ? $this->raw->single->current_download_url : null;
		$this->normal->single->current_cover_url = isset( $this->raw->single->current_cover_url ) ? $this->raw->single->current_cover_url : null;
		$this->normal->single->current_preview = isset( $this->raw->single->current_preview ) ? $this->raw->single->current_preview : null;
		$this->normal->single->current_description = isset( $this->raw->single->current_description ) ? $this->raw->single->current_description : null;

		if ( isset( $this->normal->single->current_cover_url ) ) {
			$this->normal->single->current_cover_url = substr( $this->normal->single->current_cover_url, 0, 6 ) === 'cover-' ? 'https://www.legendasbrasil.org/' . $this->normal->single->current_cover_url : $this->normal->single->current_cover_url;
			$this->normal->single->current_cover_url = $this->normal->single->current_cover_url;
		}

		if ( isset( $this->normal->single->current_preview ) ) {
			$this->normal->single->current_preview = str_replace( "\x0d", "", $this->normal->single->current_preview );
			$this->normal->single->current_preview = str_replace( "\x0a", "", $this->normal->single->current_preview );
			$this->normal->single->current_preview = str_replace( '<br/>', "\x0a", $this->normal->single->current_preview );
		}

		if ( isset( $this->normal->single->current_description ) ) {

			$description_manual = $this->normal->single->current_description;
			$description_manual = str_replace( '&quot;', '"', $description_manual );

			$description_decode = $this->normal->single->current_description;
			$description_decode = htmlspecialchars_decode( $description_decode );

			if ( $description_manual === $description_decode ) {
				$this->normal->single->current_description = $description_manual;
			} else {
				$this->normal->single->current_description = $description_decode;
			}

		}

		return isset( $this->normal->single->current_download_url ) && $this->normal->single->current_download_url !== '';

	}

	protected function _http_download() {

		if ( !isset( $this->http->download->current_download_url ) ) {
			$this->_setError( 'Protected_Http_Download_This_Http_Download_Current_Download_Url_Undefined' );
			return false;
		}

		$this->http->download->url = $this->http->download->current_download_url;
		$this->http->download->cache_hash = urlencode( $this->http->download->url );

		if ( defined( 'ABSPATH' ) ) {
			$this->http->download->cache_folder = ABSPATH . 'wp-content/cache/class-legendas-brasil/download/';
		} else {
			$this->http->download->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/download/';
		}

		$this->http->download->cache_folder = $this->http->download->cache_folder . $this->http->download->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->download->cache_file = $this->http->download->cache_folder . 'body';

		if ( file_exists( $this->http->download->cache_file ) && filesize( $this->http->download->cache_file ) !== 0 ) {
			$this->http->download->ch = null;
			$this->http->download->options = array();
			$this->http->download->exec = '';
			$this->http->download->info = unserialize( file_get_contents( $this->http->download->cache_folder . 'info' ) );
			$this->http->download->head = file_get_contents( $this->http->download->cache_folder . 'head' );
			$this->http->download->body = null;
			$this->http->download->error = file_get_contents( $this->http->download->cache_folder . 'error' );
			$this->http->download->errno = intval( file_get_contents( $this->http->download->cache_folder . 'errno' ) );
			return $this->http->download->body ? $this->http->download->body : true;
		}

		if ( !file_exists( $this->http->download->cache_folder ) && !mkdir( $this->http->download->cache_folder, 0755, true ) ) {
			$this->_setError( 'Protected_Http_Download_File_Exists_Mk_Dir' );
			return false;
		}

		set_time_limit(120);

		$this->http->download->ch = curl_init();

		$this->http->download->options = array(
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_ENCODING       => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => true,
			CURLOPT_HTTPGET        => true,
			CURLOPT_NOPROGRESS     => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 120,
			CURLOPT_URL            => $this->http->download->url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
			CURLOPT_VERBOSE        => true,
		);

		curl_setopt_array( $this->http->download->ch, $this->http->download->options );

		$this->http->download->exec  = curl_exec( $this->http->download->ch );
		$this->http->download->info  = (object) curl_getinfo( $this->http->download->ch );
		$this->http->download->head  = substr( $this->http->download->exec, 0, $this->http->download->info->header_size );
		$this->http->download->body  = substr( $this->http->download->exec, $this->http->download->info->header_size );
		$this->http->download->error = curl_error( $this->http->download->ch );
		$this->http->download->errno = curl_errno( $this->http->download->ch );

		curl_close( $this->http->download->ch );

		if ( $this->http->download->exec === false ) {
			$this->_setError( 'Protected_Http_Download_Curl_Exec' );
			return false;
		}

		if ( $this->http->download->error !== '' ) {
			$this->_setError( 'Protected_Http_Download_Curl_Error' );
			return false;
		}

		if ( $this->http->download->errno ) {
			$this->_setError( 'Protected_Http_Download_Curl_Errno' );
			return false;
		}

		if ( $this->http->download->info->http_code !== 200 ) {
			$this->_setError( 'Protected_Http_Download_Curl_Info_Http_Code_Diff_200' );
			return false;
		}

		file_put_contents( $this->http->download->cache_folder . 'info', serialize( $this->http->download->info ) );
		file_put_contents( $this->http->download->cache_folder . 'head', $this->http->download->head );
		file_put_contents( $this->http->download->cache_folder . 'body', $this->http->download->body );
		file_put_contents( $this->http->download->cache_folder . 'error', $this->http->download->error );
		file_put_contents( $this->http->download->cache_folder . 'errno', $this->http->download->errno );
		file_put_contents( $this->http->download->cache_folder . 'url', $this->http->download->info->url );

		return $this->http->download->body ? $this->http->download->body : true;

	}

	protected function _define_single() {

		$search[] = $this->search;
		$search[] = str_replace( "\x20", '.', $this->search );
		$search[] = str_replace( "BluRay.x264", 'BrRip.x264', $this->search );

		foreach ( $search as $key => $value ) {
			$search[] = str_replace( '.1080p.', '.720p.', $value );
			$search[] = str_replace( '.720p.', '.1080p.', $value );
		}

		foreach ( $search as $key => $value ) {
			$search[] = str_ireplace( '.x264.YIFY', '.x264-YIFY', $value );
		}

		foreach ( $search as $key => $value ) {
			$search[] = str_replace( '.', "\x20", $value );
			$search[] = str_ireplace( 'x264 YIFY', 'x264-YIFY', str_replace( '.', "\x20", $value ) );
		}

		foreach ( $search as $key => $value ) {
			if ( stripos( $value, 'BrRip' ) !== false ) {
				$search[] = str_replace( 'BrRip', "BluRay", $value );
			} elseif ( stripos( $value, 'BluRay' ) !== false ) {
				$search[] = str_replace( 'BluRay', "BrRip", $value );
			}
		}

		$search = array_values( array_unique( $search ) );

		$others = $search;

		foreach ( $others as $key => $value ) {

			$others[$key] = $value;

			$others[$key] = str_replace( '.1080p.BluRay.x264.YIFY', '.DvDRip.FxM', $others[$key] );
			$others[$key] = str_replace( '.1080p.BrRip.x264.YIFY', '.DvDRip.FxM', $others[$key] );
			$others[$key] = str_replace( '.1080p.BluRay.x264-YIFY', '.DvDRip-FxM', $others[$key] );
			$others[$key] = str_replace( '.1080p.BrRip.x264-YIFY', '.DvDRip-FxM', $others[$key] );
			$others[$key] = str_replace( '1080p BluRay x264 YIFY', 'DvDRip FxM', $others[$key] );
			$others[$key] = str_replace( '1080p BluRay x264-YIFY', 'DvDRip-FxM', $others[$key] );
			$others[$key] = str_replace( '1080p BrRip x264 YIFY', 'DvDRip FxM', $others[$key] );
			$others[$key] = str_replace( '1080p BrRip x264-YIFY', 'DvDRip-FxM', $others[$key] );

			$others[$key] = str_replace( '.720p.BluRay.x264.YIFY', '.DvDRip.FxM', $others[$key] );
			$others[$key] = str_replace( '.720p.BrRip.x264.YIFY', '.DvDRip.FxM', $others[$key] );
			$others[$key] = str_replace( '.720p.BluRay.x264-YIFY', '.DvDRip-FxM', $others[$key] );
			$others[$key] = str_replace( '.720p.BrRip.x264-YIFY', '.DvDRip-FxM', $others[$key] );
			$others[$key] = str_replace( '720p BluRay x264 YIFY', 'DvDRip FxM', $others[$key] );
			$others[$key] = str_replace( '720p BluRay x264-YIFY', 'DvDRip-FxM', $others[$key] );
			$others[$key] = str_replace( '720p BrRip x264 YIFY', 'DvDRip FxM', $others[$key] );
			$others[$key] = str_replace( '720p BrRip x264-YIFY', 'DvDRip-FxM', $others[$key] );

		}

		$others = array_values( array_unique( $others ) );

		$perfects = array();
		$similars = array();
		$alternatives = array();
		$alternatives_similars = array();

		foreach ( $this->normal->get->current_results as $key => $value ) {

			$subtitle = $value['subtitle'];
			$subtitle = str_replace( '(SubRip).srt', '', $subtitle );
			$subtitle = str_replace( '.srt', '', $subtitle );

			if ( in_array( $subtitle, $search ) ) {
				$perfects[] = $value['url'];
				continue;
			}

			$subtitle = $value['subtitle'];

			if ( $this->language === 'pt-br' ) {

				$subtitle = str_ireplace( '-pob.srt', '', $subtitle );
				$subtitle = str_ireplace( '-pob(1).srt', '', $subtitle );
				$subtitle = str_ireplace( '-pob(2).srt', '', $subtitle );
				$subtitle = str_ireplace( '-pob(3).srt', '', $subtitle );

				if ( in_array( $subtitle, $search ) ) {
					$similars[] = $value['url'];
					continue;
				}

			} elseif ( $this->language === 'pt-pt' ) {

				$subtitle = str_ireplace( '-por.srt', '', $subtitle );
				$subtitle = str_ireplace( '-por(1).srt', '', $subtitle );
				$subtitle = str_ireplace( '-por(2).srt', '', $subtitle );
				$subtitle = str_ireplace( '-por(3).srt', '', $subtitle );

				if ( in_array( $subtitle, $search ) ) {
					$similars[] = $value['url'];
					continue;
				}

			}

			if ( in_array( $subtitle, $others ) ) {
				$alternatives[] = $value['url'];
				continue;
			}

		}

		if ( $perfects ) {
			$this->http->single->current_single_url = current( $perfects );
			return true;
		}

		if ( $similars ) {
			$this->http->single->current_single_url = current( $similars );
			return true;
		}

		if ( isset( $this->settings->title_alternative ) && $this->settings->title_alternative && $alternatives ) {
			$this->http->single->current_single_url = current( $alternatives );
			return true;
		}

		return false;

	}

	protected function _define_download() {

		if ( !isset( $this->normal->single->current_download_url ) ) {
			$this->_setError( 'Protected_Define_Download_This_Normal_Single_Current_Download_Url_Undefined' );
			return false;
		}

		$this->http->download->current_download_url = $this->normal->single->current_download_url;

		return true;

	}

	protected function _http_single() {

		if ( !isset( $this->http->single->current_single_url ) ) {
			$this->_setError( 'Protected_Http_Single_This_Http_Single_Current_Single_Url_Undefined' );
			return false;
		}

		$this->http->single->url = $this->http->single->current_single_url;
		#$this->http->single->cache_hash = md5( $this->search );
		$this->http->single->cache_hash = urlencode( $this->http->single->url );

		if ( defined( 'ABSPATH' ) ) {
			$this->http->single->cache_folder = ABSPATH . 'wp-content/cache/class-legendas-brasil/single/';
		} else {
			$this->http->single->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/single/';
		}

		$this->http->single->cache_folder = $this->http->single->cache_folder . $this->http->single->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->single->cache_file = $this->http->single->cache_folder . 'body';

		if ( file_exists( $this->http->single->cache_file ) && filesize( $this->http->single->cache_file ) !== 0 ) {
			$this->http->single->ch = null;
			$this->http->single->options = array();
			$this->http->single->exec = '';
			$this->http->single->info = unserialize( file_get_contents( $this->http->single->cache_folder . 'info' ) );
			$this->http->single->head = file_get_contents( $this->http->single->cache_folder . 'head' );
			$this->http->single->body = file_get_contents( $this->http->single->cache_folder . 'body' );
			$this->http->single->error = file_get_contents( $this->http->single->cache_folder . 'error' );
			$this->http->single->errno = intval( file_get_contents( $this->http->single->cache_folder . 'errno' ) );
			return $this->http->single->body;
		}

		if ( !file_exists( $this->http->single->cache_folder ) && !mkdir( $this->http->single->cache_folder, 0755, true ) ) {
			$this->_setError( 'Protected_Http_Single_File_Exists_Mk_Dir' );
			return false;
		}

		set_time_limit(60);

		$this->http->single->ch = curl_init();

		$this->http->single->options = array(
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_ENCODING       => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => true,
			CURLOPT_HTTPGET        => true,
			CURLOPT_NOPROGRESS     => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_URL            => $this->http->single->url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:32.0) Gecko/20100101 Firefox/32.0',
			CURLOPT_VERBOSE        => true,
		);

		curl_setopt_array( $this->http->single->ch, $this->http->single->options );

		$this->http->single->exec  = curl_exec( $this->http->single->ch );
		$this->http->single->info  = (object) curl_getinfo( $this->http->single->ch );
		$this->http->single->head  = substr( $this->http->single->exec, 0, $this->http->single->info->header_size );
		$this->http->single->body  = substr( $this->http->single->exec, $this->http->single->info->header_size );
		$this->http->single->error = curl_error( $this->http->single->ch );
		$this->http->single->errno = curl_errno( $this->http->single->ch );

		curl_close( $this->http->single->ch );

		if ( $this->http->single->exec === false ) {
			$this->_setError( 'Protected_Http_Single_Curl_Exec' );
			return false;
		}

		if ( $this->http->single->error !== '' ) {
			$this->_setError( 'Protected_Http_Single_Curl_Error' );
			return false;
		}

		if ( $this->http->single->errno ) {
			$this->_setError( 'Protected_Http_Single_Curl_Errno' );
			return false;
		}

		if ( $this->http->single->info->http_code !== 200 ) {
			$this->_setError( 'Protected_Http_Single_Curl_Info_Http_Code_Diff_200' );
			return false;
		}

		file_put_contents( $this->http->single->cache_folder . 'info', serialize( $this->http->single->info ) );
		file_put_contents( $this->http->single->cache_folder . 'head', $this->http->single->head );
		file_put_contents( $this->http->single->cache_folder . 'body', $this->http->single->body );
		file_put_contents( $this->http->single->cache_folder . 'error', $this->http->single->error );
		file_put_contents( $this->http->single->cache_folder . 'errno', $this->http->single->errno );
		file_put_contents( $this->http->single->cache_folder . 'url', $this->http->single->info->url );

		return $this->http->single->body ? $this->http->single->body : true;

	}

}

$lb = new LegendasBrasil();
$lb->setSearch( 'If.I.Stay.2014.720p.BluRay.x264.YIFY' );
$lb->setLanguage( 'pt-br' );
#$lb->enableTitleAlternative();
$lb->run();

if ( $lb->hasError() ) {
	echo $lb->getError();
	die();
}

$lb->download();

if ( $lb->hasError() ) {
	echo $lb->getError();
	die();
}

if ( !$lb->hasDownload() ) {
	echo 'Zip file not found';
	die();
}

if ( !$lb->extractDownload() ) {
	echo 'Zip extract error';
	die();
}

if ( !$lb->fixSubtitle() ) {
	echo 'Fix subtitle error';
	die();
}

$lb->getContents();

die( 'Die' );

?>
