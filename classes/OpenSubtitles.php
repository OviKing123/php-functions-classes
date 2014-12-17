<?php

class OpenSubtitles {

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
	 * Http Requests: Search, Search2, Get, Posts, Post
	 * @var object
	 */
	protected $http;

	/**
	 * Error message
	 * @var string
	 */
	protected $error = 'Error';

	public function __construct( $name = null, $year = null, $group = null, $language = null ) {

		$this->input = new StdClass();
		$this->output = new StdClass();
		$this->http = new StdClass();
		$this->settings = new StdClass();

		$this->output->full_perfect = array();
		$this->output->good = array();
		$this->output->full_perfect_pt_br = array();
		$this->output->full_perfect_pt_pt = array();
		$this->output->full_perfect_en_us = array();
		$this->output->full_perfect_es_es = array();
		$this->output->pt_br = array();
		$this->output->pt_pt = array();
		$this->output->en_us = array();
		$this->output->es_es = array();
		$this->output->title_year_group = array();
		$this->output->title_year = array();
		$this->output->title_group = array();
		$this->output->title = array();

		$this->http->search = new StdClass();
		$this->http->search2 = new StdClass();
		$this->http->get = new StdClass();

		$this->settings->cache_root = false;

	}

	public function enableCacheRoot() {

		$this->settings->cache_root = true;

		return $this;

	}

	public function setName( $name ) {
		return $this->_setName( $name );
	}

	public function setYear( $year ) {
		return $this->_setYear( $year );
	}

	public function setGroup( $group ) {
		return $this->_setGroup( $group );
	}

	public function setLanguage( $group ) {
		return $this->_setLanguage( $group );
	}

	public function run( $name = null, $year = null, $group = null, $language = null ) {
		return $this->_run( $name, $year, $group, $language );
	}

	public function hasError() {
		return isset( $this->error ) && $this->error !== false;
	}

	public function getError() {
		return $this->error;
	}

	public function hasSubtitle() {
		if ( $this->output->results ) {
			return true;
		}
		$this->___setError( 'Subtitle not found' );
		return false;
	}

	public function hasFullPerfect() {
		if ( $this->output->full_perfect ) {
			return true;
		}
		$this->___setError( 'Perfect subtitle not found' );
		return false;
	}

	public function getFullPerfect() {
		if ( !$this->output->full_perfect ) {
			return false;
		}
		return current( $this->output->full_perfect )['url'];
	}

	protected function _setName( $name ) {
		$this->input->name = $name;
		return $this;
	}

	protected function _setYear( $year ) {
		$this->input->year = $year;
		return $this;
	}

	protected function _setGroup( $group ) {
		$this->input->group = $group;
		return $this;
	}

	protected function _setLanguage( $language ) {
		$this->input->language = $language;
		return $this;
	}

	protected function ___setError( $msg ) {
		$this->error = sprintf( 'Error: %s', $msg );
	}

	protected function ___setErrorMethod( $method, $msg ) {
		$this->error = sprintf( 'Error: Method: %s Reason: %s', $method, $msg );
	}

	protected function ___setErrorUndefined( $var ) {
		$this->error = sprintf( 'Notice: Undefined variable: %s', $var );
	}

	protected function _http_search() {

		$moviename = urlencode( $this->input->name . "\x20" . $this->input->year );

		$this->http->search->url = sprintf( 'http://www.opensubtitles.org/en/search2?MovieName=%s&action=search&SubLanguageID=pob&SubLanguageID=por&SubLanguageID=pob,por', $moviename );
		$this->http->search->current_url = $this->http->search->url;
		$this->http->search->cache_hash = $moviename;

		if ( defined( 'ABSPATH' ) && !$this->settings->cache_root ) {
			$this->http->search->cache_folder = ABSPATH . 'wp-content/cache/class-open-subtitles/search/';
		} else {
			$this->http->search->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/search/';
		}

		$this->http->search->cache_folder = $this->http->search->cache_folder . $this->http->search->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->search->cache_file_head = $this->http->search->cache_folder . 'head';

		if ( is_file( $this->http->search->cache_file_head ) && filesize( $this->http->search->cache_file_head ) !== 0 ) {

			$this->http->search->ch = null;
			$this->http->search->options = array();
			$this->http->search->exec = '';
			$this->http->search->info = unserialize( file_get_contents( $this->http->search->cache_folder . 'info' ) );
			$this->http->search->head = file_get_contents( $this->http->search->cache_folder . 'head' );
			$this->http->search->body = file_get_contents( $this->http->search->cache_folder . 'body' );
			$this->http->search->error = file_get_contents( $this->http->search->cache_folder . 'error' );
			$this->http->search->errno = intval( file_get_contents( $this->http->search->cache_folder . 'errno' ) );

			$this->_http_search_set_location();

			return $this->http->search->body ? $this->http->search->body : true;

		}

		return $this->_http_search_main();

	}

	protected function _http_search2() {

		$this->http->search2->url = $this->http->search->location;
		$this->http->search2->current_url = $this->http->search2->url;
		$this->http->search2->cache_hash = $this->_http_search2_extract_foldername();

		if ( defined( 'ABSPATH' ) && !$this->settings->cache_root ) {
			$this->http->search2->cache_folder = ABSPATH . 'wp-content/cache/class-open-subtitles/location/';
		} else {
			$this->http->search2->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/location/';
		}

		$this->http->search2->cache_folder = $this->http->search2->cache_folder . $this->http->search2->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->search2->cache_file_head = $this->http->search2->cache_folder . 'head';

		if ( is_file( $this->http->search2->cache_file_head ) && filesize( $this->http->search2->cache_file_head ) !== 0 ) {

			$this->http->search2->ch = null;
			$this->http->search2->options = array();
			$this->http->search2->exec = '';
			$this->http->search2->info = unserialize( file_get_contents( $this->http->search2->cache_folder . 'info' ) );
			$this->http->search2->head = file_get_contents( $this->http->search2->cache_folder . 'head' );
			$this->http->search2->body = file_get_contents( $this->http->search2->cache_folder . 'body' );
			$this->http->search2->error = file_get_contents( $this->http->search2->cache_folder . 'error' );
			$this->http->search2->errno = intval( file_get_contents( $this->http->search2->cache_folder . 'errno' ) );

			$this->_http_search2_set_location();

			return $this->http->search2->body ? $this->http->search2->body : true;

		}

		return $this->_http_search2_main();

	}

	protected function _http_get_extract_foldername() {

		if ( preg_match( '/imdbid\-(\d+)\/sublanguageid\-([^\x2f]+)\/moviename\-(.+)/', $this->http->get->current_url, $match ) ) {
			return $match[1] . '-' . $match[2] . ',' . urldecode( $match[3] );
		}

		return md5( $this->http->get->current_url );

	}

	protected function _http_get() {

		$this->http->get->url = $this->http->search2->location;
		$this->http->get->current_url = $this->http->get->url;
		$this->http->get->current_url = str_replace( "\x20", '%20', $this->http->get->current_url );;
		$this->http->get->cache_hash = $this->_http_get_extract_foldername();;

		if ( defined( 'ABSPATH' ) && !$this->settings->cache_root ) {
			$this->http->get->cache_folder = ABSPATH . 'wp-content/cache/class-open-subtitles/get/';
		} else {
			$this->http->get->cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/get/';
		}

		$this->http->get->cache_folder = $this->http->get->cache_folder . $this->http->get->cache_hash . DIRECTORY_SEPARATOR;
		$this->http->get->cache_file_head = $this->http->get->cache_folder . 'head';

		if ( is_file( $this->http->get->cache_file_head ) && filesize( $this->http->get->cache_file_head ) !== 0 ) {

			$this->http->get->ch = null;
			$this->http->get->options = array();
			$this->http->get->exec = '';
			$this->http->get->info = unserialize( file_get_contents( $this->http->get->cache_folder . 'info' ) );
			$this->http->get->head = file_get_contents( $this->http->get->cache_folder . 'head' );
			$this->http->get->body = file_get_contents( $this->http->get->cache_folder . 'body' );
			$this->http->get->error = file_get_contents( $this->http->get->cache_folder . 'error' );
			$this->http->get->errno = intval( file_get_contents( $this->http->get->cache_folder . 'errno' ) );

			return $this->http->get->body ? $this->http->get->body : true;

		}

		return $this->_http_get_main();

	}

	protected function _parse_get() {

		$table = '';

		if ( ( $pos = strpos( $this->http->get->body, '<table id="search_results">' ) ) !== false ) {
			$table = substr( $this->http->get->body, $pos );
			if ( ( $pos = strpos( $table, '</table></form><center></center><fieldset><legend>' ) ) !== false ) {
				$table = substr( $table, 0, $pos + 8 );
			} elseif ( ( $pos = strpos( $table, '</table></form><center></center><fieldset>' ) ) !== false ) {
				$table = substr( $table, 0, $pos + 8 );
			} elseif ( ( $pos = strpos( $table, '</table></form><center></center>' ) ) !== false ) {
				$table = substr( $table, 0, $pos + 8 );
			} elseif ( ( $pos = strpos( $table, '</table></form><center>' ) ) !== false ) {
				$table = substr( $table, 0, $pos + 8 );
			} elseif ( ( $pos = strpos( $table, '</table></form>' ) ) !== false ) {
				$table = substr( $table, 0, $pos + 8 );
			}
		}

		$table = preg_replace( '/[\x00-\x20\x7f]{2,}/', "\x20", $table );
		#$table = str_replace( '<tr', "\x0a" . '<tr', $table );

		$table = str_replace( '<tr onclick="servOC(', "\x0a" . '<tr onclick="servOC(', $table );
		#$table = str_replace( '</tbody></table>', "\x0a" . '</tbody></table>', $table );

		$trs = explode( "\x0a", $table );

		if ( count( $trs ) > 2 && substr( $trs[0], 0, 27 ) === '<table id="search_results">' ) {
			$trs = array_slice( $trs, 1 );
		}

		$results = array();

		foreach ( $trs as $tr ) {

			$array = array();

			/**
			 * Com FPS
			 * Com Uploader
			 */
			if ( preg_match( '/servOC\([\x20]*(\d+).+\>[\x20]*(([^\x28\x20][^\x28]+[^\x28\x20])[\x20]*\x28(\d{4})\x29).+\<br[\x20]*\/\>[\x20]*([^\x3c]+[^\x20\x3c])[\x20]*\<br[\x20]*\/\>.+\<a\b[^\x3e]title\=\x22(Portuguese\-BR|Portuguese|Spanish|English)\x22[^\x3e]*\>.+\>(1CD).+\<td\b[^\x3e]*title\=\x22([0-9\:]+)\x22[^\x3e]*\>((\d+)\/(\d+)\/(\d+)).+\>(23.976).+\<a\b[^\x3e]href\=\x22(\/en\/subtitleserve\/sub\/(\d+))\x22[^\x3e]*\>(\d+)x.+\>[\x20]*(srt)[\x20]*.+\>[\x20]*([0-9\.]+)[\x20]*\<.+\>[\x20]*([0-9]+)[\x20]*\<.+\<a\b[^\x3e]*title\=\x22(\d+)[^\x22]*\x22[^\x3e]*href\=\x22[^\x22]+(tt\d+)[^\x22]*\x22[^\x3e]*\>[\x20]*([0-9\.]+)[\x20]*\<.+\<a\b[^\x3e]*href\=\x22[^\x22]+iduser\-(\d+)[^\x22]*\x22[^\x3e]*\>[\x20]*([^\x3c]+)[\x20]*\</', $tr, $match ) ) {

				$array = array(
					'id' => $match[1], 'title' => $match[2], 'name' => $match[3], 'year' => $match[4], 'filename' => $match[5],
					'language' => $match[6], 'discs' => $match[7], 'date' => $match[9], 'fps' => $match[13], 'url' => $match[14],
					'downloads' => $match[16], 'format' => $match[17], 'rating' => $match[18], 'comments' => $match[19], 'votes' => $match[20],
					'imdb_id' => $match[21], 'imdb_rating' => $match[22], 'user_id' => $match[23], 'user_name' => $match[24],
				);

				$array['url'] = substr( $array['url'], 0, 4 ) === '/en/' ? 'http://www.opensubtitles.org' . $array['url'] : $array['url'];

			/**
			 * Sem FPS
			 * Com Uploader
			 */
			} elseif ( preg_match( '/servOC\([\x20]*(\d+).+\>[\x20]*(([^\x28\x20][^\x28]+[^\x28\x20])[\x20]*\x28(\d{4})\x29).+\<br[\x20]*\/\>[\x20]*([^\x3c]+[^\x20\x3c])[\x20]*\<br[\x20]*\/\>.+\<a\b[^\x3e]title\=\x22(Portuguese\-BR|Portuguese|Spanish|English)\x22[^\x3e]*\>.+\>(1CD).+\<td\b[^\x3e]*title\=\x22([0-9\:]+)\x22[^\x3e]*\>((\d+)\/(\d+)\/(\d+)).+\>.+\<a\b[^\x3e]href\=\x22(\/en\/subtitleserve\/sub\/(\d+))\x22[^\x3e]*\>(\d+)x.+\>[\x20]*(srt)[\x20]*.+\>[\x20]*([0-9\.]+)[\x20]*\<.+\>[\x20]*([0-9]+)[\x20]*\<.+\<a\b[^\x3e]*title\=\x22(\d+)[^\x22]*\x22[^\x3e]*href\=\x22[^\x22]+(tt\d+)[^\x22]*\x22[^\x3e]*\>[\x20]*([0-9\.]+)[\x20]*\<.+\<a\b[^\x3e]*href\=\x22[^\x22]+iduser\-(\d+)[^\x22]*\x22[^\x3e]*\>[\x20]*([^\x3c]+)[\x20]*\</', $tr, $match ) ) {

				$array = array(
					'id' => $match[1], 'title' => $match[2], 'name' => $match[3], 'year' => $match[4], 'filename' => $match[5],
					'language' => $match[6], 'discs' => $match[7], 'date' => $match[9], 'fps' => '', 'url' => $match[13],
					'downloads' => $match[15], 'format' => $match[16], 'rating' => $match[17], 'comments' => $match[18], 'votes' => $match[19],
					'imdb_id' => $match[20], 'imdb_rating' => $match[21], 'user_id' => $match[22], 'user_name' => $match[23],
				);

				$array['url'] = substr( $array['url'], 0, 4 ) === '/en/' ? 'http://www.opensubtitles.org' . $array['url'] : $array['url'];

			/**
			 * Com FPS
			 * Sem Uploader
			 */
			} elseif ( preg_match( '/servOC\([\x20]*(\d+).+\>[\x20]*(([^\x28\x20][^\x28]+[^\x28\x20])[\x20]*\x28(\d{4})\x29).+\<br[\x20]*\/\>[\x20]*([^\x3c]+[^\x20\x3c])[\x20]*\<br[\x20]*\/\>.+\<a\b[^\x3e]title\=\x22(Portuguese\-BR|Portuguese|Spanish|English)\x22[^\x3e]*\>.+\>(1CD).+\<td\b[^\x3e]*title\=\x22([0-9\:]+)\x22[^\x3e]*\>((\d+)\/(\d+)\/(\d+)).+\>(23.976).+\<a\b[^\x3e]href\=\x22(\/en\/subtitleserve\/sub\/(\d+))\x22[^\x3e]*\>(\d+)x.+\>[\x20]*(srt)[\x20]*.+\>[\x20]*([0-9\.]+)[\x20]*\<.+\>[\x20]*([0-9]+)[\x20]*\<.+\<a\b[^\x3e]*title\=\x22(\d+)[^\x22]*\x22[^\x3e]*href\=\x22[^\x22]+(tt\d+)[^\x22]*\x22[^\x3e]*\>[\x20]*([0-9\.]+)[\x20]*\<.+\<a\b[^\x3e]*href\=\x22[^\x22]+iduser\-(\d+)[^\x22]*\x22[^\x3e]*\>[\x20]*([^\x3c]+)?[\x20]*\</', $tr, $match ) ) {

				$array = array(
					'id' => $match[1], 'title' => $match[2], 'name' => $match[3], 'year' => $match[4], 'filename' => $match[5],
					'language' => $match[6], 'discs' => $match[7], 'date' => $match[9], 'fps' => $match[13], 'url' => $match[14],
					'downloads' => $match[16], 'format' => $match[17], 'rating' => $match[18], 'comments' => $match[19], 'votes' => $match[20],
					'imdb_id' => $match[21], 'imdb_rating' => $match[22], 'user_id' => $match[23], 'user_name' => '',
				);

				$array['url'] = substr( $array['url'], 0, 4 ) === '/en/' ? 'http://www.opensubtitles.org' . $array['url'] : $array['url'];

			/**
			 * Sem FPS
			 * Sem Uploader
			 */
			} elseif ( preg_match( '/servOC\([\x20]*(\d+).+\>[\x20]*(([^\x28\x20][^\x28]+[^\x28\x20])[\x20]*\x28(\d{4})\x29).+\<br[\x20]*\/\>[\x20]*([^\x3c]+[^\x20\x3c])[\x20]*\<br[\x20]*\/\>.+\<a\b[^\x3e]title\=\x22(Portuguese\-BR|Portuguese|Spanish|English)\x22[^\x3e]*\>.+\>(1CD).+\<td\b[^\x3e]*title\=\x22([0-9\:]+)\x22[^\x3e]*\>((\d+)\/(\d+)\/(\d+)).+\>.+\<a\b[^\x3e]href\=\x22(\/en\/subtitleserve\/sub\/(\d+))\x22[^\x3e]*\>(\d+)x.+\>[\x20]*(srt)[\x20]*.+\>[\x20]*([0-9\.]+)[\x20]*\<.+\>[\x20]*([0-9]+)[\x20]*\<.+\<a\b[^\x3e]*title\=\x22(\d+)[^\x22]*\x22[^\x3e]*href\=\x22[^\x22]+(tt\d+)[^\x22]*\x22[^\x3e]*\>[\x20]*([0-9\.]+)[\x20]*\<.+\<a\b[^\x3e]*href\=\x22[^\x22]+iduser\-(\d+)[^\x22]*\x22[^\x3e]*\>[\x20]*([^\x3c]+)?[\x20]*\</', $tr, $match ) ) {

				$array = array(
					'id' => $match[1], 'title' => $match[2], 'name' => $match[3], 'year' => $match[4], 'filename' => $match[5],
					'language' => $match[6], 'discs' => $match[7], 'date' => $match[9], 'fps' => '', 'url' => $match[13],
					'downloads' => $match[15], 'format' => $match[16], 'rating' => $match[17], 'comments' => $match[18], 'votes' => $match[19],
					'imdb_id' => $match[20], 'imdb_rating' => $match[21], 'user_id' => $match[22], 'user_name' => '',
				);

				$array['url'] = substr( $array['url'], 0, 4 ) === '/en/' ? 'http://www.opensubtitles.org' . $array['url'] : $array['url'];

			}

			$array && $results[$array['id']] = $array;

		}

		$this->output->results = $results;

		return (bool) $results;

	}

	protected function _set_output() {

		$this->output->pt_br = array();

		foreach ( $this->output->results as $result ) {

			if ( $result['language'] === 'Portuguese-BR' ) {
				$this->output->pt_br[] = $result;
			} elseif ( $result['language'] === 'Portuguese' || $result['language'] === 'Portuguese-PT' ) {
				$this->output->pt_pt[] = $result;
			} elseif ( $result['language'] === 'English' ) {
				$this->output->en_us[] = $result;
			} elseif ( $result['language'] === 'Spanish' ) {
				$this->output->es_es[] = $result;
			}

			if ( preg_match( '/.+[\-]([A-Za-z][A-Za-z0-9]+)\-por\(\d+\)?(?:\.srt)?$/', $result['filename'], $match ) ) {
				$this->output->{$match[1]}[] = $result['id'];
			} elseif ( preg_match( '/.+[\x20]([A-Za-z][A-Za-z0-9]+)\-por\(\d+\)(?:\.srt)?$/', $result['filename'], $match ) ) {
				$this->output->{$match[1]}[] = $result['id'];
			} elseif ( preg_match( '/.+[\-]([A-Za-z][A-Za-z0-9]+)\-por(?:\.srt)?$/', $result['filename'], $match ) ) {
				$this->output->{$match[1]}[] = $result['id'];
			} elseif ( preg_match( '/.+[\-]([A-Za-z][A-Za-z0-9]+)(?:\.srt)?$/', $result['filename'], $match ) ) {
				$this->output->{$match[1]}[] = $result['id'];
			} elseif ( preg_match( '/.+\x20\[([A-Za-z][A-Za-z0-9]+\-[A-Za-z0-9]+)\]$/', $result['filename'], $match ) ) {
				$this->output->{$match[1]}[] = $result['id'];
			} elseif ( preg_match( '/.+[\.]([A-Za-z][A-Za-z0-9]+)$/', $result['filename'], $match ) ) {
				$this->output->{$match[1]}[] = $result['id'];
			}

			#isset( $match[1] ) && var_dump( $match[1] );

		}

		if ( isset( $this->output->{$this->input->group} ) && !empty( $this->output->{$this->input->group} ) ) {
			$ids = $this->output->{$this->input->group};
			foreach ( $ids as $id ) {
				if ( isset( $this->output->results[$id] ) ) {
					$result = $this->output->results[$id];
					if ( $result['language'] === 'Portuguese-BR' ) {
						$this->output->full_perfect_pt_br[$id] = $this->output->results[$id];
					} elseif ( $result['language'] === 'Portuguese' || $result['language'] === 'Portuguese-PT' ) {
						$this->output->full_perfect_pt_pt[$id] = $this->output->results[$id];
					} elseif ( $result['language'] === 'English' ) {
						$this->output->full_perfect_en_us[$id] = $this->output->results[$id];
					} elseif ( $result['language'] === 'Spanish' ) {
						$this->output->full_perfect_es_es[$id] = $this->output->results[$id];
					}
				}
			}
			if ( $this->input->language === 'PORTUGUESE' ) {
				$this->output->full_perfect = array_merge( $this->output->full_perfect_pt_br, $this->output->full_perfect_pt_pt );
			} elseif ( $this->input->language === 'PTBR' ) {
				$this->output->full_perfect = $this->output->full_perfect_pt_br;
			} elseif ( $this->input->language === 'PTPT' ) {
				$this->output->full_perfect = $this->output->full_perfect_pt_pt;
			} elseif ( $this->input->language === 'ENUS' ) {
				$this->output->full_perfect = $this->output->full_perfect_en_us;
			} elseif ( $this->input->language === 'ESES' ) {
				$this->output->full_perfect = $this->output->full_perfect_es_es;
			}
		}

		$this->error = false;

		return true;

	}

	protected function _run( $name, $year, $group, $language ) {

		$vars = array( 'name', 'year', 'group', 'language' );

		foreach ( $vars as $var ) {
			if ( !isset( $this->input->$var ) ) {
				$this->___setErrorUndefined( $var );
				return false;
			}
		}

		$this->_http_search();
		$this->_http_search2();
		$this->_http_get();
		$this->_parse_get();
		$this->_set_output();

		return $this;

	}

	protected function _http_search_main() {

		if ( !is_dir( $this->http->search->cache_folder ) && !mkdir( $this->http->search->cache_folder, 0755, true ) ) {
			$this->___setErrorMethod( __FUNCTION__, 'mkdir' );
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
			CURLOPT_NOPROGRESS     => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_URL            => $this->http->search->current_url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',
			CURLOPT_VERBOSE        => true,
		);

		curl_setopt_array( $this->http->search->ch, $this->http->search->options );

		$this->http->search->exec  = curl_exec( $this->http->search->ch );
		$this->http->search->info  = (object) curl_getinfo( $this->http->search->ch );
		$this->http->search->head  = substr( $this->http->search->exec, 0, $this->http->search->info->header_size );
		$this->http->search->body  = substr( $this->http->search->exec, $this->http->search->info->header_size );
		$this->http->search->error = curl_error( $this->http->search->ch );
		$this->http->search->errno = curl_errno( $this->http->search->ch );

		curl_close( $this->http->search->ch );

		if ( $this->http->search->exec === false ) {
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

		if ( $this->http->search->info->http_code !== 301 ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_http_code_diff_301' );
			return false;
		}

		file_put_contents( $this->http->search->cache_folder . 'info', serialize( $this->http->search->info ) );
		file_put_contents( $this->http->search->cache_folder . 'head', $this->http->search->head );
		file_put_contents( $this->http->search->cache_folder . 'body', $this->http->search->body );
		file_put_contents( $this->http->search->cache_folder . 'error', $this->http->search->error );
		file_put_contents( $this->http->search->cache_folder . 'errno', $this->http->search->errno );
		file_put_contents( $this->http->search->cache_folder . 'url', $this->http->search->info->url );
		file_put_contents( $this->http->search->cache_folder . 'redirect_url', $this->http->search->info->redirect_url );

		$this->_http_search_set_location();

		return $this->http->search->body ? $this->http->search->body : true;

	}

	protected function _http_search_set_location() {

		if ( preg_match( '/[Ll][Oo][Cc][Aa][Tt][Ii][Oo][Nn][\x20]*\:[\x20]*([^\x0d\x0a]+)/', $this->http->search->head, $match ) ) {
			$this->http->search->location = $match[1];
		} else {
			$this->http->search->location = '';
		}

	}

	protected function _http_search2_main() {

		if ( !is_dir( $this->http->search2->cache_folder ) && !mkdir( $this->http->search2->cache_folder, 0755, true ) ) {
			$this->___setErrorMethod( __FUNCTION__, 'mkdir' );
			return false;
		}

		set_time_limit(30);

		$this->http->search2->ch = curl_init();

		$this->http->search2->options = array(
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_ENCODING       => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => true,
			CURLOPT_HTTPGET        => true,
			CURLOPT_NOPROGRESS     => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_URL            => $this->http->search2->current_url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',
			CURLOPT_VERBOSE        => true,
		);

		curl_setopt_array( $this->http->search2->ch, $this->http->search2->options );

		$this->http->search2->exec  = curl_exec( $this->http->search2->ch );
		$this->http->search2->info  = (object) curl_getinfo( $this->http->search2->ch );
		$this->http->search2->head  = substr( $this->http->search2->exec, 0, $this->http->search2->info->header_size );
		$this->http->search2->body  = substr( $this->http->search2->exec, $this->http->search2->info->header_size );
		$this->http->search2->error = curl_error( $this->http->search2->ch );
		$this->http->search2->errno = curl_errno( $this->http->search2->ch );

		curl_close( $this->http->search2->ch );

		if ( $this->http->search2->exec === false ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_exec' );
			return false;
		}

		if ( $this->http->search2->error !== '' ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_error' );
			return false;
		}

		if ( $this->http->search2->errno ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_errno' );
			return false;
		}

		if ( $this->http->search2->info->http_code !== 301 ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_http_code_diff_301' );
			return false;
		}

		file_put_contents( $this->http->search2->cache_folder . 'info', serialize( $this->http->search2->info ) );
		file_put_contents( $this->http->search2->cache_folder . 'head', $this->http->search2->head );
		file_put_contents( $this->http->search2->cache_folder . 'body', $this->http->search2->body );
		file_put_contents( $this->http->search2->cache_folder . 'error', $this->http->search2->error );
		file_put_contents( $this->http->search2->cache_folder . 'errno', $this->http->search2->errno );
		file_put_contents( $this->http->search2->cache_folder . 'url', $this->http->search2->info->url );
		file_put_contents( $this->http->search2->cache_folder . 'redirect_url', $this->http->search2->info->redirect_url );

		$this->_http_search2_set_location();

		return $this->http->search2->body ? $this->http->search2->body : true;

	}

	protected function _http_search2_set_location() {

		if ( preg_match( '/[Ll][Oo][Cc][Aa][Tt][Ii][Oo][Nn][\x20]*\:[\x20]*([^\x0d\x0a]+)/', $this->http->search2->head, $match ) ) {
			$this->http->search2->location = $match[1];
		} else {
			$this->http->search2->location = '';
		}

	}

	protected function _http_search2_extract_foldername() {

		if ( preg_match( '/sublanguageid\-([^\x2f]+)\/moviename\-(.+)/', $this->http->search2->current_url, $match ) ) {
			return $match[2] . '+' . $match[1];
		}

		return md5( $this->http->search2->current_url );

	}

	protected function _http_get_main() {

		if ( !is_dir( $this->http->get->cache_folder ) && !mkdir( $this->http->get->cache_folder, 0755, true ) ) {
			$this->___setErrorMethod( __FUNCTION__, 'mkdir' );
			return false;
		}

		set_time_limit(30);

		$this->http->get->ch = curl_init();

		$this->http->get->options = array(
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_ENCODING       => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => true,
			CURLOPT_HTTPGET        => true,
			CURLOPT_NOPROGRESS     => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_URL            => $this->http->get->current_url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',
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
			$this->___setErrorMethod( __FUNCTION__, 'curl_exec' );
			return false;
		}

		if ( $this->http->get->error !== '' ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_error' );
			return false;
		}

		if ( $this->http->get->errno ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_errno' );
			return false;
		}

		if ( $this->http->get->info->http_code !== 200 ) {
			$this->___setErrorMethod( __FUNCTION__, 'curl_http_code_diff_200' );
			return false;
		}

		file_put_contents( $this->http->get->cache_folder . 'info', serialize( $this->http->get->info ) );
		file_put_contents( $this->http->get->cache_folder . 'head', $this->http->get->head );
		file_put_contents( $this->http->get->cache_folder . 'body', $this->http->get->body );
		file_put_contents( $this->http->get->cache_folder . 'error', $this->http->get->error );
		file_put_contents( $this->http->get->cache_folder . 'errno', $this->http->get->errno );
		file_put_contents( $this->http->get->cache_folder . 'url', $this->http->get->info->url );
		file_put_contents( $this->http->get->cache_folder . 'redirect_url', $this->http->get->info->redirect_url );

		return $this->http->get->body ? $this->http->get->body : true;

	}

}

?>
