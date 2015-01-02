<?php

class BingSearch {

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

	/**
	 * [__construct description]
	 * @param [type] $search   [description]
	 * @param [type] $language [description]
	 * @param [type] $page     [description]
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
		$this->default->language_mkt = 'en-US';
		$this->default->page = '1';
		$this->default->page_first = '1';

		$this->default->proxies = array();
		$this->default->proxy = null;

		$this->default->retries = 0;

		$this->input->search = null;
		$this->input->search_encode = null;
		$this->input->language = null;
		$this->input->language_mkt = null;
		$this->input->page = null;
		$this->input->page_first = null;

		$this->output->urls = array();
		$this->output->titles = array();
		$this->output->descriptions = array();
		$this->output->results = array();

		$this->http->search = new StdClass();

		$this->settings->use_cache = true;
		$this->settings->cache_root = false;
		$this->settings->use_proxy = false;
		$this->settings->max_retries = 5;

		isset( $search ) && $this->_setSearch( $search );
		isset( $language ) && $this->_setLanguage( $language );
		isset( $page ) && $this->_setPage( $page );

	}

	/**
	 * Métodos de Configuração
	 */

	/**
	 * Força pasta de cache no local $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/BingSearch/'
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
	public function setSearch( $search = null ) {
		return $this->_setSearch( $search );
	}

	/**
	 * [setLanguage description]
	 * @param [type] $language [description]
	 */
	public function setLanguage( $language = null ) {
		return $this->_setLanguage( $language );
	}

	/**
	 * [setPage description]
	 * @param [type] $page [description]
	 */
	public function setPage( $page = null ) {
		return $this->_setPage( $page );
	}

	/**
	 * [setProxies description]
	 * @param [type] $file [description]
	 */
	public function setProxies( $file = null ) {
		return $this->_setProxies( $file );
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
	 * [getUrls description]
	 * @return [type] [description]
	 */
	public function getUrls() {
		return $this->output->urls;
	}

	/**
	 * [getTitles description]
	 * @return [type] [description]
	 */
	public function getTitles() {
		return $this->output->titles;
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
		$this->input->language = $this->___setLanguage( $language, $language_mkt );
		$this->input->language_mkt = $language_mkt;
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
		$this->input->page = $this->___setPage( $page, $page_first );
		$this->input->page_first = $page_first;
		return $this;
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
	 * @param  [type] &$language_mkt [description]
	 * @return [type]              [description]
	 */
	protected function ___setLanguage( $language, &$language_mkt ) {
		if ( !is_string( $language ) || ( $language = trim( $language ) ) === '' ) {
			$language = $this->default->language;
			$language_mkt = $this->default->language_mkt;
			return $language;
		}
		return $this->____setLanguage( $language, $language_mkt );
	}

	/**
	 * [___setPage description]
	 * @param  [type] $page    [description]
	 * @param  [type] &$page_first [description]
	 * @return [type]          [description]
	 */
	protected function ___setPage( $page, &$page_first ) {
		if ( !is_numeric( $page ) || $page <= 0 || ( $page = trim( $page ) ) === '' ) {
			$page = $this->default->page;
			$page_first = $this->default->page_first;
			return $page;
		}
		return $this->____setPage( $page, $page_first );
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
	 * @param  [type] &$language_mkt [description]
	 * @return [type]              [description]
	 */
	protected function ____setLanguage( $language, &$language_mkt ) {
		if ( !$this->____isLanguage( $language, $language_mkt ) ) {
			$language = $this->default->language;
			$language_mkt = $this->default->language_mkt;
		}
		return $language;
	}

	/**
	 * [____setPage description]
	 * @param  [type] $page    [description]
	 * @param  [type] &$page_first [description]
	 * @return [type]          [description]
	 */
	protected function ____setPage( $page, &$page_first ) {
		$page_first = strval( ( ( $page - 1 ) * 10 ) + 1 );
		return $page;
	}

	/**
	 * [____isLanguage description]
	 * @param  [type] &$language   [description]
	 * @param  [type] &$language_mkt [description]
	 * @return [type]              [description]
	 */
	protected function ____isLanguage( &$language, &$language_mkt ) {
		static $languages;
		$language = strtoupper( $language );
		$languages = isset( $languages ) ? $languages : array(
			'EN-US' => array( 'ENUS', 'en-US' ),
			'ENUS' => array( 'ENUS', 'en-US' ),
			/*
			'PT-BR' => array( 'ENUS', 'pt-BR' ),
			'PTBR' => array( 'ENUS', 'pt-BR' ),
			*/
		);
		if ( !isset( $languages[$language] ) ) {
			return false;
		}
		$language = $languages[$language][0];
		$language_mkt = $languages[$language][1];
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

		isset( $search ) && $this->_setSearch( $search );
		isset( $language ) && $this->_setLanguage( $language );
		isset( $page ) && $this->_setPage( $page );

		!isset( $this->input->language ) && $this->setLanguage(null);
		!isset( $this->input->page ) && $this->setPage(null);

		$vars = array( 'search', 'language', 'page' );

		foreach ( $vars as $var ) {
			if ( !isset( $this->input->$var ) ) {
				$this->___setErrorUndefined( $var );
				return false;
			}
		}

		if ( !$this->_http_search() ) {
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

	/**
	 * [_http_search description]
	 * @return [type] [description]
	 */
	protected function _http_search() {

		$this->http->search->url = $this->_http_search_extract_url();
		$this->http->search->current_url = $this->http->search->url;
		$this->http->search->cache_hash = $this->_http_search_extract_foldername();

		if ( defined( 'ABSPATH' ) && !$this->settings->cache_root ) {
			$this->http->search->cache_folder = ABSPATH . 'wp-content/cache/class-bing-search/search/';
		} else {
			$this->http->search->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/search/';
		}

		$this->http->search->cache_folder = $this->http->search->cache_folder . $this->http->search->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->search->cache_file_head = $this->http->search->cache_folder . 'head';

		$this->http->search->headers = array(
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
			'Accept-Language: en-US,en;q=0.8,pt;q=0.6',
			'Referer: http://www.bing.com/',
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
		return $this->_parse_search_get_results();
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
			if ( $this->settings->use_proxy ) {
				$this->_setProxies();
				$this->_nextProxy();
				$this->default->retries++;
				return $this->_http_search_main();
			}
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
		file_put_contents( $this->http->search->cache_folder . 'content_type', $this->http->search->info->content_type );
		file_put_contents( $this->http->search->cache_folder . 'http_code', $this->http->search->info->http_code );
		file_put_contents( $this->http->search->cache_folder . 'redirect_url', $this->http->search->info->redirect_url );

		return $this->http->search->body ? $this->http->search->body : true;

	}

	/**
	 * Métodos Protegidos - Dependências das Dependências dos Métodos Principais
	 */

	/**
	 * [_http_search_extract_url description]
	 * @return [type] [description]
	 */
	protected function _http_search_extract_url() {

		if ( $this->input->page === '1' ) {
			$url = sprintf( 'http://www.bing.com/search?q=%s', $this->input->search_encode );
		} else {
			$url = sprintf( 'http://www.bing.com/search?q=%s&first=%s', $this->input->search_encode, $this->input->page_first );
		}

		return $url;

	}

	/**
	 * [_http_search_extract_foldername description]
	 * @return [type] [description]
	 */
	protected function _http_search_extract_foldername() {

		$foldername = sprintf( '%s/%s/%s', $this->input->language, $this->input->search_encode, $this->input->page );
		$foldername = strlen( $foldername ) <= 250 ? $foldername : md5( $foldername );

		return $foldername;

	}

	/**
	 * [_parse_search_get_results description]
	 * @return [type] [description]
	 */
	protected function _parse_search_get_results() {

		$body = $this->http->search->body;
		$body = str_replace( "\x0a", "\x20", $body );

		$needles_open = array(
			'Narrow by region<span class="sw_ddbk"></span> </a></span></div>',
			'Narrow by region<span class="sw_ddbk"></span>',
			'<div id="b_content">',
		);

		$needles_close = array(
			'<ol id="b_context"><li class="b_ans"><h2>Related searches</h2>',
			'<h2>Related searches for <strong>',
			'<h2>Related searches</h2>',
		);

		$needles_end = array(
			'<li class="b_ans"><div class="b_rs"><h2>Related searches for',
			'<div class="b_rs"><h2>Related searches for',
			'<h2>Related searches for',
		);

		foreach ( $needles_open as $needle ) {
			if ( ( $pos = strpos( $body, $needle ) ) !== false ) {
				$body = ltrim( substr( $body, $pos + strlen( $needle ) ) );
				break;
			}
		}

		foreach ( $needles_close as $needle ) {
			if ( ( $pos = strpos( $body, $needle ) ) !== false ) {
				$body = rtrim( substr( $body, 0, $pos ) );
				break;
			}
		}

		foreach ( $needles_end as $needle ) {
			if ( ( $pos = strpos( $body, $needle ) ) !== false ) {
				$body = rtrim( substr( $body, 0, $pos ) );
				break;
			}
		}

		$body = str_replace( '<li', "\x0a" . '<li', $body );

		$pattern = '/\<h2\>\<a\b[^\x3e]*href\=[\x22]([^\x22]+)[\x22][^\x3e]*\>(.+)\<\/a\>.*?\<a\b[^\x3e]*href\=[\x22]([^\x22]+)[\x22][^\x3e]*\>.+\<\/a\>.*?\<cite\b[^\x3e]*\>(.+)\<\/cite\>.*?\<p\>(.+)\<\/p\>/';

		if ( !preg_match_all( $pattern, $body, $matches ) ) {
			$this->___setErrorMethod( __FUNCTION__, 'regex_error' );
			return false;
		}

		$this->output->urls = $matches[1];
		$this->output->titles = $matches[2];
		$this->output->descriptions = $matches[5];

		$this->_parse_search_fix_titles();
		$this->_parse_search_fix_descriptions();

		$this->_parse_search_set_results();

		return true;

	}

	/**
	 * [_parse_search_fix_titles description]
	 * @return [type] [description]
	 */
	protected function _parse_search_fix_titles() {

		$this->output->titles = array_map( 'strip_tags', $this->output->titles );

		foreach ( $this->output->titles as $key => $value ) {
			/* … */
			$value = str_replace( "\xe2\x80\xa6", '...', $value );
			$value = str_replace( "\x85", '...', $value );
			$value = str_replace( '&hellip;', '...', $value );
			/* – */
			$value = str_replace( "\xe2\x80\x93", '-', $value );
			$value = str_replace( "\x96", '', $value );
			$value = str_replace( '&ndash;', '', $value );
			/* — */
			$value = str_replace( "\xe2\x80\x94", '-', $value );
			$value = str_replace( "\x97", '', $value );
			$value = str_replace( '&mdash;', '', $value );
			/* » */
			$value = str_replace( "\xc2\xbb", '>', $value );
			$value = str_replace( "\xbb", '>', $value );
			$value = str_replace( '&raquo;', '>', $value );
			/* trim */
			$value = rtrim( $value );
			$value = substr( $value, -3 ) === '...' ? rtrim( substr( $value, 0, -3 ) ) : $value;
			$value = substr( $value, 0, 3 ) === '...' ? ltrim( substr( $value, 3 ) ) : $value;
			$value = trim( $value, "\x20\x2d\x3b\x7c" );
			$value = ( $tmp = html_entity_decode( $value ) ) !== '' ? $tmp : $value;
			$this->output->titles[$key] = $value;
		}

	}

	/**
	 * [_parse_search_fix_descriptions description]
	 * @return [type] [description]
	 */
	protected function _parse_search_fix_descriptions() {

		$this->output->descriptions = array_map( 'strip_tags', $this->output->descriptions );

		foreach ( $this->output->descriptions as $key => $value ) {
			/* … */
			$value = str_replace( "\xe2\x80\xa6", '...', $value );
			$value = str_replace( "\x85", '...', $value );
			$value = str_replace( '&hellip;', '...', $value );
			/* – */
			$value = str_replace( "\xe2\x80\x93", '-', $value );
			$value = str_replace( "\x96", '', $value );
			$value = str_replace( '&ndash;', '', $value );
			/* — */
			$value = str_replace( "\xe2\x80\x94", '-', $value );
			$value = str_replace( "\x97", '', $value );
			$value = str_replace( '&mdash;', '', $value );
			/* » */
			$value = str_replace( "\xc2\xbb", '>', $value );
			$value = str_replace( "\xbb", '>', $value );
			$value = str_replace( '&raquo;', '>', $value );
			/* trim */
			$value = rtrim( $value );
			$value = substr( $value, -3 ) === '...' ? rtrim( substr( $value, 0, -3 ) ) : $value;
			$value = substr( $value, 0, 3 ) === '...' ? ltrim( substr( $value, 3 ) ) : $value;
			$value = trim( $value, "\x20\x2d\x3b\x7c" );
			$value = ( $tmp = html_entity_decode( $value ) ) !== '' ? $tmp : $value;
			$this->output->descriptions[$key] = $value;
		}

	}

	/**
	 * [_parse_search_set_results description]
	 * @return [type] [description]
	 */
	protected function _parse_search_set_results() {

		foreach ( $this->output->urls as $key => $value ) {
			$array = array(
				'url' => $this->output->urls[$key],
				'title' => $this->output->titles[$key],
				'description' => $this->output->descriptions[$key],
			);
			$this->output->results[$key] = $array;
		}

		return true;

	}

	/**
	 * Métodos Protegidos - Globais Executados Por Qualquer Método
	 */

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

}

?>
