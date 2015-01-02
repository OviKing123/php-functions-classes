<?php

class YoutubeSearch {

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
	 * Construtor da Classe YoutubeSearch
	 * @param string $search Vídeo que será buscado
	 * @param string $language Idioma do site
	 * @param string|integer $page Número da página
	 * @param string|array $filter Filtro aplicado à busca
	 */
	public function __construct( $search = null, $language = null, $page = null, $filter = null ) {

		$this->default = new StdClass();
		$this->input = new StdClass();
		$this->output = new StdClass();
		$this->http = new StdClass();
		$this->settings = new StdClass();

		$this->default->search = null;
		$this->default->search_encode = null;
		$this->default->language = 'ENUS';
		$this->default->language_hl = 'en';
		$this->default->page = '1';
		$this->default->filter = array();

		$this->default->proxies = array();
		$this->default->proxy = null;

		$this->default->retries = 0;

		$this->input->search = null;
		$this->input->search_encode = null;
		$this->input->language = null;
		$this->input->language_hl = null;
		$this->input->page = null;
		$this->input->filter = null;

		$this->output->ids = array();
		$this->output->titles = array();
		$this->output->descriptions = array();
		$this->output->durations = array();
		$this->output->views = array();
		$this->output->authors = array();
		$this->output->dates = array();
		$this->output->results = array();

		$this->http->search = new StdClass();

		$this->settings->use_cache = true;
		$this->settings->cache_root = false;
		$this->settings->use_proxy = false;
		$this->settings->max_retries = 5;

		isset( $search ) && $this->_setSearch( $search );
		isset( $language ) && $this->_setLanguage( $language );
		isset( $page ) && $this->_setPage( $page );
		isset( $filter ) && $this->_setFilter( $filter );

	}

	/**
	 * Métodos de Configuração
	 */

	/**
	 * Força pasta de cache no local $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/YoutubeSearch/'
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
	 * Desabilita o uso de cache nas requisições HTTP
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
	 * [setFilter description]
	 * @param [type] $page [description]
	 */
	public function setFilter( $filter = null ) {
		return $this->_setFilter( $filter );
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
	public function run( $search = null, $language = null, $page = null, $filter = null ) {
		return $this->_run( $search, $language, $page, $filter );
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
	 * [getIds description]
	 * @return [type] [description]
	 */
	public function getIds() {
		return $this->output->ids;
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
	 * [getViews description]
	 * @return [type] [description]
	 */
	public function getViews() {
		return $this->output->views;
	}

	/**
	 * [getAuthors description]
	 * @return [type] [description]
	 */
	public function getAuthors() {
		return $this->output->authors;
	}

	/**
	 * [getDurations description]
	 * @return [type] [description]
	 */
	public function getDurations() {
		return $this->output->durations;
	}

	/**
	 * [getDates description]
	 * @return [type] [description]
	 */
	public function getDates() {
		return $this->output->dates;
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
		$this->input->language = $this->___setLanguage( $language, $language_hl );
		$this->input->language_hl = $language_hl;
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
		$this->input->page = $this->___setPage( $page );
		return $this;
	}

	/**
	 * [_setFilter description]
	 * @param [type] $search [description]
	 */
	protected function _setFilter( $filter ) {
		if ( isset( $this->input->filter ) ) {
			return $this;
		}
		$this->input->filter = $this->___setFilter( $filter );
		return $this;
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
	 * @param  [type] &$language_hl [description]
	 * @return [type]              [description]
	 */
	protected function ___setLanguage( $language, &$language_hl ) {
		if ( !is_string( $language ) || ( $language = trim( $language ) ) === '' ) {
			$language = $this->default->language;
			$language_hl = $this->default->language_hl;
			return $language;
		}
		return $this->____setLanguage( $language, $language_hl );
	}

	/**
	 * [___setPage description]
	 * @param  [type] $page    [description]
	 * @return [type]          [description]
	 */
	protected function ___setPage( $page ) {
		if ( !is_numeric( $page ) || $page <= 0 || ( $page = trim( $page ) ) === '' ) {
			$page = $this->default->page;
			return $page;
		}
		return $this->____setPage( $page );
	}

	/**
	 * [___setFilter description]
	 * @param  [type] $search         [description]
	 * @param  [type] &$search_encode [description]
	 * @return [type]                 [description]
	 */
	protected function ___setFilter( $filter ) {
		if ( !is_array( $filter ) ) {
			if ( is_string( $filter ) ) {
				$filter = array( $filter );
			}
		}
		if ( !is_array( $filter ) || empty( $filter ) ) {
			return $this->default->filter;
		}
		return $this->____setFilter( $filter );
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
	 * @param  [type] &$language_hl [description]
	 * @return [type]              [description]
	 */
	protected function ____setLanguage( $language, &$language_hl ) {
		if ( !$this->____isLanguage( $language, $language_hl ) ) {
			$language = $this->default->language;
			$language_hl = $this->default->language_hl;
		}
		return $language;
	}

	/**
	 * [____setPage description]
	 * @param  [type] $page    [description]
	 * @return [type]          [description]
	 */
	protected function ____setPage( $page ) {
		return $page;
	}

	/**
	 * [____setFilter description]
	 * @param  [type] $filter    [description]
	 * @return [type]              [description]
	 */
	protected function ____setFilter( $filter ) {
		$filters = array();
		foreach ( $filter as $value ) {
			if ( $this->____isFilter( $value, $val ) ) {
				$filters[] = $val;
			}
		}
		return $filters;
	}

	/**
	 * [____isLanguage description]
	 * @param  [type] &$language   [description]
	 * @param  [type] &$language_hl [description]
	 * @return [type]              [description]
	 */
	protected function ____isLanguage( &$language, &$language_hl ) {
		static $languages;
		$language = strtoupper( $language );
		$languages = isset( $languages ) ? $languages : array(
			'EN-US' => array( 'ENUS', 'en' ),
			'ENUS' => array( 'ENUS', 'en' ),
		);
		if ( !isset( $languages[$language] ) ) {
			return false;
		}
		$language = $languages[$language][0];
		$language_hl = $languages[$language][1];
		return true;
	}

	/**
	 * [____isFilter description]
	 * @param  [type] &$filter   [description]
	 * @param  [type] &$filter_val [description]
	 * @return [type]              [description]
	 */
	protected function ____isFilter( &$filter, &$filter_val ) {
		static $filters;
		$filter = strtoupper( $filter );
		$filters = isset( $filters ) ? $filters : array(
			'VIDEO' => array( 'VIDEO', 'video' ),
		);
		if ( !isset( $filters[$filter] ) ) {
			return false;
		}
		$filter = $filters[$filter][0];
		$filter_val = $filters[$filter][1];
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
	protected function _run( $search, $language, $page, $filter ) {

		if ( $this->error === false ) {
			return $this;
		}

		isset( $search ) && $this->_setSearch( $search );
		isset( $language ) && $this->_setLanguage( $language );
		isset( $page ) && $this->_setPage( $page );
		isset( $filter ) && $this->_setFilter( $filter );

		!isset( $this->input->language ) && $this->_setLanguage(null);
		!isset( $this->input->page ) && $this->_setPage(null);
		!isset( $this->input->filter ) && $this->_setFilter(null);

		$vars = array( 'search', 'language', 'page', 'filter' );

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
			$this->http->search->cache_folder = ABSPATH . 'wp-content/cache/class-youtube-search/search/';
		} else {
			$this->http->search->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/search/';
		}

		$this->http->search->cache_folder = $this->http->search->cache_folder . $this->http->search->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->search->cache_file_head = $this->http->search->cache_folder . 'head';

		$this->http->search->headers = array(
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
			'Accept-Language: ' . $this->input->language_hl,
			'Referer: https://www.youtube.com/',
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

	protected function _http_search_extract_url() {

		if ( $this->input->filter ) {
			if ( count( $this->input->filter ) === 1 && $this->input->filter[0] === 'video' ) {
				if ( $this->input->page === '1' ) {
					$result = sprintf( 'https://www.youtube.com/results?search_query=%s&hl=%s&filters=video&lclk=video', $this->input->search_encode, $this->input->language_hl );
				} else {
					$result = sprintf( 'https://www.youtube.com/results?search_query=%s&hl=%s&filters=video&lclk=video&page=%s', $this->input->search_encode, $this->input->language_hl, $this->input->page );
				}
			}
		}

		if ( isset( $result ) ) {
			return $result;
		}

		if ( $this->input->page === '1' ) {
			$result = sprintf( 'https://www.youtube.com/results?search_query=%s&hl=%s', $this->input->search_encode, $this->input->language_hl );
		} else {
			$result = sprintf( 'https://www.youtube.com/results?search_query=%s&hl=%s&page=%s', $this->input->search_encode, $this->input->language_hl, $this->input->page );
		}

		return $result;

	}

	/**
	 * [_http_search_extract_foldername description]
	 * @return [type] [description]
	 */
	protected function _http_search_extract_foldername() {

		if ( $this->input->filter ) {
			$foldername = sprintf( '%s/%s/%s/%s', $this->input->language, $this->input->search_encode, implode( ',', $this->input->filter ), $this->input->page );
		}

		$foldername = isset( $foldername ) ? $foldername : sprintf( '%s/%s/%s', $this->input->language, $this->input->search_encode, $this->input->page );
		$foldername = strlen( $foldername ) <= 250 ? $foldername : md5( $foldername );

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
	 * Extrai resultados da página de busca definindo urls, títulos e descrições
	 * @return boolean Retorna true caso a página seja válida e/ou sejam encontrados resultados válidos, caso contrário retorna false
	 */
	protected function _parse_search_get_results() {

		$body = $this->http->search->body;
		$body = str_replace( "\x0a", "\x20", $body );

		$needles_open = array(
			'<ol id="section-list" class="section-list">',
			'<ol class="item-section">',
		);

		$needles_close = array(
			'<div class="yt-uix-pager search-pager branded-page-box spf-link " role="navigation">',
			'<span class="yt-uix-button-content">Next',
			'<div id="ad_creative_1"',
		);

		foreach ( $needles_open as $needle ) {
			if ( ( $pos = strpos( $body, $needle ) ) !== false ) {
				$body = ltrim( substr( $body, $pos ) );
				break;
			}
		}

		foreach ( $needles_close as $needle ) {
			if ( ( $pos = strpos( $body, $needle ) ) !== false ) {
				$body = rtrim( substr( $body, 0, $pos ) );
				break;
			}
		}

		$body = str_replace( '<li><div class="yt-lockup', "\x0a" . '<li><div class="yt-lockup', $body );
		$body = str_replace( '', '', $body );

		$tile_count = substr_count( $body, '<li><div class="yt-lockup yt-lockup-tile' );

		$pattern1 = '/.*?data\-context\-item\-id\x3d\x22([^\x22]+)\x22.*?href\x3d\x22\/watch\?v\x3d([^\x22]+)\x22.*?\<span\b[^\x3e]*class\x3d\x22video\-time\x22[^\x3e]*\>[\x00-\x20\x7f]*([0-9\:]+)[\x00-\x20\x7f]*\<\/span\>.*?\<a\b[^\x3e]*class\x3d\x22[^\x22\x3e]*yt\-uix\-tile\-link[^\x22\x3e]*\x22[^\x3e]*title\x3d\x22([^\x22\x3e]+)\x22[^\x3e]*\>[\x00-\x20\x7f]*([^\x20\x3c]+.+[^\x20\x3c]+)[\x00-\x20\x7f]*\<\/a>[\x00-\x20\x7f]*<span\b[^\x3e]*class\x3d\x22accessible\-description\x22[^\x3e]*\>[\x00-\x20\x7f]*[^\x3c]+[\x00-\x20\x7f]*\<\/span\>\<\/h3\>.*?\<a\b[^\x3e]*href\x3d\x22(\/(user|channel)\/([^\x22]+))\x22[^\x3e]*\>([^\x3c]+)\<\/a\>.*?\<li\>(((\d+) (years?|months?|weeks?|days?|hours?|seconds?)) ago)\<\/li>.*?\<li\>(([0-9\.\,]+|No|[^\x3c]+) views|No views|[^\x3c]+)\<\/li\>.*?\<div\b[^\x3e]*class\x3d\x22yt\-lockup\-description[^\x22]*?\x22[^\x3e]*\>(.+)[\x00-\x20\x7f]*\<\/div\>[\x00-\x20\x7f]*\<\/div\>[\x00-\x20\x7f]*\<\/div\>[\x00-\x20\x7f]*\<\/div\>[\x00-\x20\x7f]*\<\/li\>/';
		$pattern2 = '/.*?data\-context\-item\-id\x3d\x22([^\x22]+)\x22.*?href\x3d\x22\/watch\?v\x3d([^\x22]+)\x22.*?\<span\b[^\x3e]*class\x3d\x22video\-time\x22[^\x3e]*\>[\x00-\x20\x7f]*([0-9\:]+)[\x00-\x20\x7f]*\<\/span\>.*?\<a\b[^\x3e]*class\x3d\x22[^\x22\x3e]*yt\-uix\-tile\-link[^\x22\x3e]*\x22[^\x3e]*title\x3d\x22([^\x22\x3e]+)\x22[^\x3e]*\>[\x00-\x20\x7f]*([^\x20\x3c]+.+[^\x20\x3c]+)[\x00-\x20\x7f]*\<\/a>[\x00-\x20\x7f]*<span\b[^\x3e]*class\x3d\x22accessible\-description\x22[^\x3e]*\>[\x00-\x20\x7f]*[^\x3c]+[\x00-\x20\x7f]*\<\/span\>\<\/h3\>.*?\<a\b[^\x3e]*href\x3d\x22(\/(user|channel)\/([^\x22]+))\x22[^\x3e]*\>([^\x3c]+)\<\/a\>.*?\<li\>(((\d+) (years?|months?|weeks?|days?|hours?|seconds?)) ago)\<\/li>.*?\<li\>(([0-9\.\,]+|No|[^\x3c]+) views|No views|[^\x3c]+)\<\/li\>.*?\<div\b[^\x3e]*class\x3d\x22yt\-lockup\-description[^\x22]*?\x22[^\x3e]*\>(.+)[\x00-\x20\x7f]*/';

		if ( preg_match_all( $pattern1, $body, $matches ) && count( $matches[0] ) === $tile_count ) {
		} elseif ( preg_match_all( $pattern2, $body, $matches ) && count( $matches[0] ) === $tile_count ) {
		} else {
			$this->___setErrorMethod( __FUNCTION__, 'regex_error' );
			return false;
		}

		$this->output->ids = $matches[1];
		$this->output->authors = $matches[8];
		$this->output->titles = $matches[4];
		$this->output->views = $matches[15];
		$this->output->dates = $matches[11];
		$this->output->descriptions = $matches[16];
		$this->output->durations = $matches[3];

		$this->_parse_search_fix_authors();
		$this->_parse_search_fix_titles();
		$this->_parse_search_fix_views();
		$this->_parse_search_fix_descriptions();
		$this->_parse_search_fix_durations();

		$this->_parse_search_set_results();

		return true;

	}

	/**
	 * [_parse_search_fix_authors description]
	 * @return [type] [description]
	 */
	protected function _parse_search_fix_authors() {

		foreach ( $this->output->authors as $key => $value ) {
			$this->output->authors[$key] = $value;
		}

	}

	/**
	 * [_parse_search_fix_views description]
	 * @return [type] [description]
	 */
	protected function _parse_search_fix_views() {

		foreach ( $this->output->views as $key => $value ) {
			$value = $value === 'No' ? '0' : $value;
			$value = str_replace( ',', '', $value );
			$this->output->views[$key] = $value;
		}

	}

	/**
	 * [_parse_search_fix_durations description]
	 * @return [type] [description]
	 */
	protected function _parse_search_fix_durations() {

		foreach ( $this->output->durations as $key => $value ) {
			$this->output->durations[$key] = $value;
		}

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

		foreach ( $this->output->ids as $key => $value ) {
			$array = array(
				'id' => $this->output->ids[$key],
				'title' => $this->output->titles[$key],
				'description' => $this->output->descriptions[$key],
				'duration' => $this->output->durations[$key],
				'views' => $this->output->views[$key],
				'author' => $this->output->authors[$key],
				'date' => $this->output->dates[$key],
			);
			$this->output->results[$key] = $array;
		}

		return true;

	}

}

?>
