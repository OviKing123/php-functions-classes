<?php

class DuckDuckGoSearch {

	/**
	 * Default input parameters
	 * @var object
	 */
	protected $default;

	/**
	 * Input parameters
	 * @var object
	 */
	protected $input;

	/**
	 * Output parameters
	 * @var object
	 */
	protected $output;

	/**
	 * Http Requests
	 * @var object
	 */
	protected $http;

	/**
	 * Configuration
	 * @var object
	 */
	protected $settings;

	/**
	 * Error message
	 * @var string
	 */
	protected $error = 'Error';

	/**
	 * Construtor
	 */

	public function __construct( $search = null, $language = null, $page = null ) {

		$this->default = new StdClass();
		$this->input = new StdClass();
		$this->output = new StdClass();
		$this->http = new StdClass();
		$this->settings = new StdClass();

		$this->default->search = null;
		$this->default->search_encode = null;
		$this->default->language = 'ENUS';
		$this->default->language_l = 'us-en';
		$this->default->page = '1';
		$this->default->page_s = '0';

		$this->default->proxies = array();
		$this->default->proxy = null;

		$this->default->retries = 0;

		$this->input->search = null;
		$this->input->search_encode = null;
		$this->input->language = null;
		$this->input->language_l = null;
		$this->input->page = null;
		$this->input->page_s = null;

		$this->output->links = array();
		$this->output->titles = array();
		$this->output->urls = array();
		$this->output->descriptions = array();
		$this->output->results = array();

		$this->http->get = new StdClass();
		$this->http->search = new StdClass();

		$this->settings->use_cache = true;
		$this->settings->cache_root = false;
		$this->settings->use_proxy = false;
		$this->settings->max_retries = 5;

		isset( $search ) && $this->setSearch( $search );
		isset( $language ) && $this->setLanguage( $language );
		isset( $page ) && $this->setPage( $page );

	}

	/**
	 * Métodos de Configuração
	 */

	/**
	 * Força pasta de cache no local $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/DuckDuckGoSearch/'
	 * @return [type] [description]
	 */
	public function enableCacheRoot() {
		$this->settings->cache_root = true;
		return $this;
	}

	/**
	 * Habilita o uso de proxy para contornar limitações por excessos de requisições 
	 * @return [type] [description]
	 */
	public function enableProxy() {
		$this->settings->use_proxy = true;
		return $this;
	}

	/**
	 * Define o número máximo de vezes que a conexão com proxy poderá ser executada em repetição
	 * @return [type] [description]
	 */
	public function enableMaxRetries( $retries ) {
		$this->settings->max_retries = ( $tmp = intval( $retries ) ) ? $tmp : $this->settings->max_retries;
		return $this;
	}

	/**
	 * Habilita o uso de cache nas requisições HTTP
	 * @return [type] [description]
	 */
	public function disableCache() {
		$this->settings->use_cache = false;
		return $this;
	}

	/**
	 * Métodos Públicos
	 */

	/**
	 * Métodos Públicos - Definição de Parâmetros de Entrada
	 */

	/**
	 * [setSearch description]
	 * @param [type] $search [description]
	 */
	public function setSearch( $search ) {
		return $this->_setSearch( $search );
	}

	/**
	 * [setLanguage description]
	 * @param [type] $language [description]
	 */
	public function setLanguage( $language ) {
		return $this->_setLanguage( $language );
	}

	/**
	 * [setPage description]
	 * @param [type] $page [description]
	 */
	public function setPage( $page ) {
		return $this->_setPage( $page );
	}

	/**
	 * Métodos Públicos - Execução da Classe
	 */

	/**
	 * [run description]
	 * @param  [type] $search   [description]
	 * @param  [type] $language [description]
	 * @param  [type] $page     [description]
	 * @return [type]           [description]
	 */
	public function run( $search = null, $language = null, $page = null ) {
		return $this->_run( $search, $language, $page );
	}

	/**
	 * Métodos Públicos - Checagem de Erro e Retorno de Erro
	 */

	/**
	 * [hasError description]
	 * @return boolean [description]
	 */
	public function hasError() {
		return isset( $this->error ) && $this->error !== false;
	}

	/**
	 * [getError description]
	 * @return [type] [description]
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Métodos Públicos - Retorno dos Resultados
	 */

	/**
	 * [getLinks description]
	 * @return [type] [description]
	 */
	public function getLinks() {
		return $this->output->links;
	}

	/**
	 * [getTitles description]
	 * @return [type] [description]
	 */
	public function getTitles() {
		return $this->output->titles;
	}

	/**
	 * [getUrls description]
	 * @return [type] [description]
	 */
	public function getUrls() {
		return $this->output->urls;
	}

	/**
	 * [getDescriptions description]
	 * @return [type] [description]
	 */
	public function getDescriptions() {
		return $this->output->descriptions;
	}

	/**
	 * [getResults description]
	 * @return [type] [description]
	 */
	public function getResults() {
		return $this->output->results;
	}

	/**
	 * [getAllUrls description]
	 * @return [type] [description]
	 */
	public function getAllUrls() {
		static $urls;
		$urls = $urls !== null ? $urls : array_values( array_unique( array_merge( $this->output->links, $this->output->urls ) ) );
		return $urls;
	}

	/**
	 * Métodos Protegidos
	 */

	/**
	 * Métodos Protegidos - Definição de Parâmetros de Entrada
	 */

	/**
	 * [_setSearch description]
	 * @param [type] $search [description]
	 */
	protected function _setSearch( $search ) {
		if ( isset( $this->input->search ) ) {
			return $this;
		}
		$this->input->search = $this->___setSearch( $search, $search_encode );
		$this->input->search_encode = $search_encode;
		return $this;
	}

	/**
	 * [_setLanguage description]
	 * @param [type] $language [description]
	 */
	protected function _setLanguage( $language ) {
		if ( isset( $this->input->language ) ) {
			return $this;
		}
		$this->input->language = $this->___setLanguage( $language, $language_l );
		$this->input->language_l = $language_l;
		return $this;
	}

	/**
	 * [_setPage description]
	 * @param [type] $page [description]
	 */
	protected function _setPage( $page ) {
		if ( isset( $this->input->page ) ) {
			return $this;
		}
		$this->input->page = $this->___setPage( $page, $page_s );
		$this->input->page_s = $page_s;
		return $this;
	}

	/**
	 * Métodos Protegidos - Unset
	 */

	/**
	 * [_unsetLanguage description]
	 * @return [type] [description]
	 */
	protected function _unsetLanguage() {
		$this->input->language = $this->input->language->l = null;
	}

	/**
	 * Métodos Protegidos - Dependências dos Métodos Protegidos de Definição de Parâmetros de Entrada
	 */

	/**
	 * [___setSearch description]
	 * @param  [type] $search         [description]
	 * @param  [type] &$search_encode [description]
	 * @return [type]                 [description]
	 */
	protected function ___setSearch( $search, &$search_encode ) {
		if ( !is_string( $search ) || ( $search = trim( $search ) ) === '' ) {
			return $this->default->search;
		}
		return $this->____setSearch( $search, $search_encode );
	}

	/**
	 * [___setLanguage description]
	 * @param  [type] $language    [description]
	 * @param  [type] &$language_l [description]
	 * @return [type]              [description]
	 */
	protected function ___setLanguage( $language, &$language_l ) {
		if ( !is_string( $language ) || ( $language = trim( $language ) ) === '' ) {
			$language = $this->default->language;
			$language_l = $this->default->language_l;
			return $language;
		}
		return $this->____setLanguage( $language, $language_l );
	}

	/**
	 * [___setPage description]
	 * @param  [type] $page    [description]
	 * @param  [type] &$page_s [description]
	 * @return [type]          [description]
	 */
	protected function ___setPage( $page, &$page_s ) {
		if ( !is_numeric( $page ) || $page <= 0 || ( $page = trim( $page ) ) === '' ) {
			$page = $this->default->page;
			$page_s = $this->default->page_s;
			return $page;
		}
		return $this->____setPage( $page, $page_s );
	}

	/**
	 * Métodos Protegidos - Dependências das Dependências dos Métodos Protegidos de Definição de Parâmetros de Entrada
	 * Edite Aqui
	 */

	/**
	 * [____setSearch description]
	 * @param  [type] $search         [description]
	 * @param  [type] &$search_encode [description]
	 * @return [type]                 [description]
	 */
	protected function ____setSearch( $search, &$search_encode ) {
		$search_encode = urlencode( $search );
		return $search;
	}

	/**
	 * [____setLanguage description]
	 * @param  [type] $language    [description]
	 * @param  [type] &$language_l [description]
	 * @return [type]              [description]
	 */
	protected function ____setLanguage( $language, &$language_l ) {
		if ( !$this->____isLanguage( $language, $language_l ) ) {
			$language = $this->default->language;
			$language_l = $this->default->language_l;
		}
		return $language;
	}

	/**
	 * [____setPage description]
	 * @param  [type] $page    [description]
	 * @param  [type] &$page_s [description]
	 * @return [type]          [description]
	 */
	protected function ____setPage( $page, &$page_s ) {
		$page_s = strval( ( $page * 30 ) - 30 );
		return $page;
	}

	/**
	 * [____isLanguage description]
	 * @param  [type] &$language   [description]
	 * @param  [type] &$language_l [description]
	 * @return [type]              [description]
	 */
	protected function ____isLanguage( &$language, &$language_l ) {
		static $languages;
		$language = strtoupper( $language );
		$languages = isset( $languages ) ? $languages : array(
			'US-EN' => array( 'ENUS', 'us-en' ),
			'EN-US' => array( 'ENUS', 'us-en' ),
			'USEN' => array( 'ENUS', 'us-en' ),
			'ENUS' => array( 'ENUS', 'us-en' ),

			'BR-PT' => array( 'PTBR', 'br-pt' ),
			'PT-BR' => array( 'PTBR', 'br-pt' ),
			'BRPT' => array( 'PTBR', 'br-pt' ),
			'PTBR' => array( 'PTBR', 'br-pt' ),
		);
		if ( !isset( $languages[$language] ) ) {
			return false;
		}
		$language = $languages[$language][0];
		$language_l = $languages[$language][1];
		return true;
	}

	/**
	 * Métodos Protegidos - Definição de Erro
	 */

	/**
	 * [___setError description]
	 * @param  [type] $msg [description]
	 * @return [type]      [description]
	 */
	protected function ___setError( $msg ) {
		$this->error = sprintf( 'Error: %s', $msg );
	}

	/**
	 * [___setErrorMethod description]
	 * @param  [type] $method [description]
	 * @param  [type] $msg    [description]
	 * @return [type]         [description]
	 */
	protected function ___setErrorMethod( $method, $msg ) {
		$this->error = sprintf( 'Error: Method: %s Reason: %s', $method, $msg );
	}

	/**
	 * [___setErrorUndefined description]
	 * @param  [type] $var [description]
	 * @return [type]      [description]
	 */
	protected function ___setErrorUndefined( $var ) {
		$this->error = sprintf( 'Notice: Undefined variable: %s', $var );
	}

	/**
	 * Métodos Protegidos - Execução da Classe - Executando Métodos Protegidos Principais Primários
	 */

	/**
	 * [_run description]
	 * @param  [type] $search   [description]
	 * @param  [type] $language [description]
	 * @param  [type] $page     [description]
	 * @return [type]           [description]
	 */
	protected function _run( $search, $language, $page ) {

		if ( $this->error === false ) {
			return $this;
		}

		isset( $search ) && $this->setSearch( $search );
		isset( $language ) && $this->setLanguage( $language );
		isset( $page ) && $this->setPage( $page );

		!isset( $this->input->language ) && $this->setLanguage(null);
		!isset( $this->input->page ) && $this->setPage(null);

		$vars = array( 'search', 'language', 'page' );

		foreach ( $vars as $var ) {
			if ( !isset( $this->input->$var ) ) {
				$this->___setErrorUndefined( $var );
				return false;
			}
		}

		#$this->_http_get();
		#$this->_parse_get();

		if ( !$this->_http_search() ) {
			return $this;
		}

		if ( !$this->_parse_search() ) {
			return $this;
		}

		if ( !$this->_parse_search() ) {
			return $this;
		}

		$this->error = false;

		return $this;

	}

	/**
	 * Métodos Protegidos - Métodos Principais
	 */

	protected function _http_get() {}
	protected function _parse_get() {}

	/**
	 * [_http_search description]
	 * @return [type] [description]
	 */
	protected function _http_search() {

		$q = $this->input->search_encode;
		$t = 'D';
		$l = $this->input->language_l;
		$p = '1';
		$s = $this->input->page_s;

		$this->http->search->url = sprintf( 'https://duckduckgo.com/d.js?q=%s&t=%s&l=%s&p=%s&s=%s', $q, $t, $l, $p, $s );
		$this->http->search->current_url = $this->http->search->url;
		$this->http->search->cache_hash = $this->_http_search_extract_foldername();

		if ( defined( 'ABSPATH' ) && !$this->settings->cache_root ) {
			$this->http->search->cache_folder = ABSPATH . 'wp-content/cache/class-duckduckgo-search/search/';
		} else {
			$this->http->search->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/search/';
		}

		$this->http->search->cache_folder = $this->http->search->cache_folder . $this->http->search->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->search->cache_file_head = $this->http->search->cache_folder . 'head';

		$this->http->search->headers = array(
			'Accept: */*',
			'Accept-Language: en-US,en;q=0.8,pt;q=0.6',
			'Referer: https://duckduckgo.com/',
		);

		if ( $this->settings->use_cache && is_file( $this->http->search->cache_file_head ) && filesize( $this->http->search->cache_file_head ) !== 0 ) {

			#$this->http->search->ch = null;
			#$this->http->search->options = array();
			#$this->http->search->exec = '';
			#$this->http->search->info = unserialize( file_get_contents( $this->http->search->cache_folder . 'info' ) );
			#$this->http->search->head = file_get_contents( $this->http->search->cache_folder . 'head' );
			$this->http->search->body = file_get_contents( $this->http->search->cache_folder . 'body' );
			#$this->http->search->error = file_get_contents( $this->http->search->cache_folder . 'error' );
			#$this->http->search->errno = intval( file_get_contents( $this->http->search->cache_folder . 'errno' ) );

			return $this->http->search->body ? $this->http->search->body : true;

		}

		return $this->_http_search_main();

	}

	/**
	 * [_parse_search description]
	 * @return [type] [description]
	 */
	protected function _parse_search() {

		$this->_parse_search_get_links();
		$this->_parse_search_get_all();

		return true;

	}

	/**
	 * Métodos Protegidos - Dependências dos Métodos Principais
	 */

	/**
	 * [_http_search_main description]
	 * @return [type] [description]
	 */
	protected function _http_search_main() {

		if ( !is_dir( $this->http->search->cache_folder ) && !mkdir( $this->http->search->cache_folder, 0755, true ) ) {
			$this->___setErrorMethod( __FUNCTION__, 'mkdir' );
			return false;
		}

		if ( $this->default->retries > $this->settings->max_retries ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_blocked' );
			return false;
		}

		set_time_limit(30);

		$this->http->search->ch = curl_init();

		$this->http->search->options = array(
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_ENCODING       => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => true,
			CURLOPT_HTTPGET        => true,
			CURLOPT_HTTPHEADER     => $this->http->search->headers,
			CURLOPT_NOPROGRESS     => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 15,
			CURLOPT_URL            => $this->http->search->current_url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',
			CURLOPT_VERBOSE        => true,
		);

		if ( $this->settings->use_proxy && $this->default->proxy ) {
			$this->http->search->options[CURLOPT_PROXY] = $this->default->proxy;
			$this->http->search->options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
		}

		curl_setopt_array( $this->http->search->ch, $this->http->search->options );

		$this->http->search->exec  = curl_exec( $this->http->search->ch );
		$this->http->search->info  = (object) curl_getinfo( $this->http->search->ch );
		$this->http->search->head  = substr( $this->http->search->exec, 0, $this->http->search->info->header_size );
		$this->http->search->body  = substr( $this->http->search->exec, $this->http->search->info->header_size );
		$this->http->search->error = curl_error( $this->http->search->ch );
		$this->http->search->errno = curl_errno( $this->http->search->ch );

		curl_close( $this->http->search->ch );

		if ( $this->http->search->exec === false ) {
			$this->_setProxies();
			$this->_nextProxy();
			$this->default->retries++;
			return $this->_http_search_main();
			$this->___setErrorMethod( __FUNCTION__, 'curl_exec' );
			return false;
		}

		if ( $this->http->search->error !== '' ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_error' );
			return false;
		}

		if ( $this->http->search->errno ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_errno' );
			return false;
		}

		if ( $this->http->search->info->http_code !== 200 ) {
			if ( $this->http->search->info->http_code === 301 ) {
				$this->_setProxies();
				$this->_nextProxy();
				$this->default->retries++;
				return $this->_http_search_main();
				if ( preg_match( '/[Ll][Oo][Cc][Aa][Tt][Ii][Oo][Nn][\x20]*\:[\x20]*([^\x0d\x0a]+)/', $this->http->search->head, $match ) ) {
					if ( $match[1] === 'https://rundmc.duckduckgo.com:3433/' ) {
					}
				}
			}
			$this->___setErrorMethod( __FUNCTION__, 'curl_http_code_diff_200' );
			return false;
		}

		if ( !$this->settings->use_cache ) {
			return $this->http->search->body ? $this->http->search->body : true;
		}

		file_put_contents( $this->http->search->cache_folder . 'info', serialize( $this->http->search->info ) );
		file_put_contents( $this->http->search->cache_folder . 'head', $this->http->search->head );
		file_put_contents( $this->http->search->cache_folder . 'body', $this->http->search->body );
		file_put_contents( $this->http->search->cache_folder . 'error', $this->http->search->error );
		file_put_contents( $this->http->search->cache_folder . 'errno', $this->http->search->errno );
		file_put_contents( $this->http->search->cache_folder . 'url', $this->http->search->info->url );
		file_put_contents( $this->http->search->cache_folder . 'redirect_url', $this->http->search->info->redirect_url );

		return $this->http->search->body ? $this->http->search->body : true;

	}

	/**
	 * Métodos Protegidos - Dependências das Dependências dos Métodos Principais
	 */

	/**
	 * [_http_search_extract_foldername description]
	 * @return [type] [description]
	 */
	protected function _http_search_extract_foldername() {

		$foldername = sprintf( '%s/%s/%s', $this->input->search_encode, $this->input->language_l, $this->input->page );
		#$foldername = md5( $foldername );

		return $foldername;

	}

	/**
	 * [_setProxies description]
	 * @param [type] $file [description]
	 */
	protected function _setProxies( $file = null ) {

		if ( $this->default->proxies ) {
			return $this->default->proxies;
		}

		$_file = $_SERVER['DOCUMENT_ROOT'] . '/classes/resources/' . __CLASS__ . '/proxylist.txt';

		if ( isset( $file ) && is_file( $file ) ) {
			$file = $file;
		} elseif ( is_file( $_file ) ) {
			$file = $_file;
		}

		if ( !is_file( $file ) ) {
			return array();
		}

		$this->default->proxies = explode( "\x0a", trim( file_get_contents( $file ) ) );

		return $this->default->proxies;

	}

	/**
	 * [_nextProxy description]
	 * @param  [type] &$proxy [description]
	 * @return [type]         [description]
	 */
	protected function _nextProxy( &$proxy = null ) {

		if ( !$this->default->proxies ) {
			return;
		}

		if ( ( $tmp = current( $this->default->proxies ) ) === false ) {
			reset( $this->default->proxies );
			$tmp = current( $this->default->proxies );
		}

		next( $this->default->proxies );

		$this->default->proxy = $proxy = $tmp;

		return $proxy;

	}

	/**
	 * [_parse_search_get_links description]
	 * @return [type] [description]
	 */
	protected function _parse_search_get_links() {

		$body = $this->http->search->body;

		if ( substr( $body, 0, 48 ) === "DDG.inject('DDG.Data.languages.resultLanguages'," ) {
			$body = ltrim( substr( $body, 48 ) );
			if ( ( $pos = strpos( $body, ");if (nrn) nrn('d'," ) ) !== false ) {
				$body = rtrim( substr( $body, 0, $pos ) );
			}
		} elseif ( ( $pos = strpos( $body, '{' ) ) !== false ) {
			$body = rtrim( substr( $body, $pos ) );
			if ( ( $pos = strpos( $body, '}' ) ) !== false ) {
				$body = rtrim( substr( $body, 0, $pos + 1 ) );
			}
		}

		if ( ( $body_decode = json_decode( $body ) ) === false || $body_decode === null ) {
			return false;
		}

		if ( ( $tmp = current( $body_decode ) ) === false || !is_array( $tmp ) ) {
			return false;
		}

		$this->output->links = $tmp;

		return true;

	}

	/**
	 * [_parse_search_get_all description]
	 * @return [type] [description]
	 */
	protected function _parse_search_get_all() {

		$body = $this->http->search->body;

		/**
		 * Skip javascript code
		 */

		if ( ( $pos = strpos( $body, "if (nrn) nrn('d'," ) ) !== false ) {
			$body = ltrim( substr( $body, $pos + 17 ) );
		} elseif ( preg_match( '/if[\x00-\x20\x7f]*\([\x00-\x20\x7f]*nrn[\x00-\x20\x7f]*\)[\x00-\x20\x7f]*nrn[\x00-\x20\x7f]*\([\x00-\x20\x7f]*[\x22\x27]{0,}[^\x22\x27]+[\x22\x27]{0,}[\x00-\x20\x7f]*\,[\x00-\x20\x7f]*/', $body, $match, PREG_OFFSET_CAPTURE ) ) {
			$body = ltrim( substr( $body, $match[0][1] + strlen( $match[0][0] ) ) );
			if ( substr( $body, -2 ) === ');' ) {
				$body = rtrim( substr( $body, 0, -2 ) );
			}
		}

		/**
		 * Remove ) and ;
		 */

		if ( $body !== $this->http->search->body ) {
			if ( substr( $body, -2 ) === ');' ) {
				$body = rtrim( substr( $body, 0, -2 ) );
			}
		}

		if ( ( $body_decode = json_decode( $body ) ) === false || $body_decode === null ) {
			return false;
		}

		if ( ( $tmp = end( $body_decode ) ) === false || !is_object( $tmp ) ) {
			return false;
		}

		if ( !isset( $tmp->a ) ) {
			$body_decode = array_slice( $body_decode, 0, -1 );
		}

		foreach ( $body_decode as $entry ) {
			$url = strip_tags( $entry->c );
			if ( in_array( $url, $this->output->urls ) ) {
				continue;
			}
			$title = strip_tags( $entry->t );
			$description = strip_tags( $entry->a );
			$this->output->titles[] = $title;
			$this->output->urls[] = $url;
			$this->output->descriptions[] = $description;
		}

		return true;

	}

	/**
	 * Métodos Protegidos Globais - Funções Usadas Por Qualquer Outro Método
	 */

}

?>
