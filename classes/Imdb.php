<?php

class ImdbSettings {}
class ImdbHttp {}
class ImdbRaw {}
class ImdbNormal {}

class Imdb {

	/**
	 * [$error description]
	 * @var [type]
	 */
	protected $error;

	/**
	 * [$input_id description]
	 * @var [type]
	 */
	protected $input_id;

	/**
	 * [$id description]
	 * @var [type]
	 */
	protected $id;

	/**
	 * [$settings description]
	 * @var [type]
	 */
	protected $settings;

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

	public function __construct( $id = null ) {

		isset( $id ) && $this->setId( $id );

		$this->settings = new ImdbSettings();
		$this->http = new ImdbHttp();
		$this->raw = new ImdbRaw();
		$this->normal = new ImdbNormal();

		$this->settings->only_english = true;

	}

	public function setId( $id ) {

		$this->input_id = $id;
		$this->id = $this->_setId( $id );

		return $this;

	}

	public function run( $id = null ) {

		isset( $id ) && $this->setId( $id );

		if ( !isset( $this->id ) ) {
			$this->_setError( 'Public_Run_Id_Undefined' );
			return false;
		}

		return $this->_run();

	}

	public function getResults() {

		return isset( $this->normal ) ? $this->normal : array();

	}

	public function hasError() {

		return isset( $this->error ) || !isset( $this->normal->imdb_id ) || !$this->normal->imdb_id;

	}

	public function getError() {

		if ( isset( $this->error ) ) {
			return $this->error;
		}

		if ( !isset( $this->normal ) || !$this->normal ) {
			return 'Notice: Undefined property: ' . __CLASS__ . '::$normal';
		}

		if ( !isset( $this->normal ) || !$this->normal ) {
			return 'Empty: Property: ' . __CLASS__ . '::$normal';
		}

		return $this->error;

	}

	public function getErrorH1() {

		return '<h1>' . $this->getError() . '</h1>';

	}

	protected function _setId( $id ) {

		return preg_match( '/(tt\d+)/', $id, $match ) ? $match[1] : null;

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

		if ( !$this->_http() ) {
			$this->_setError( 'Protected_Http_False' );
			return false;
		}

		if ( !$this->_parse() ) {
			$this->_setError( 'Protected_Parse_False' );
			return false;
		}

		return true;

	}

	protected function _http() {

		if ( defined( 'ABSPATH' ) ) {
			$this->http->ttid_cache_folder = ABSPATH . 'wp-content/cache/class-imdb/ttid/';
		} else {
			$this->http->ttid_cache_folder = $_SERVER['DOCUMENT_ROOT'] . '/cache/classes/' . __CLASS__ . '/ttid/';
		}

		if ( !file_exists( $this->http->ttid_cache_folder ) && !mkdir( $this->http->ttid_cache_folder, 0755, true ) ) {
			$this->_setError( 'Protected_Run_File_Exists_Mk_Dir' );
			return false;
		}

		$imdb_index_url = 'http://akas.imdb.com/title/' . $this->id . '/';
		$imdb_releaseinfo_url = 'http://akas.imdb.com/title/' . $this->id . '/releaseinfo/';
		$imdb_fullcredits_url = 'http://akas.imdb.com/title/' . $this->id . '/fullcredits/';
		$imdb_plotsummary_url = 'http://akas.imdb.com/title/' . $this->id . '/plotsummary/';

		$this->imdb_languages = array(
			'en-US', 'pt-BR', 'es-ES', 'pt-PT', 'fr-FR', 'it-IT', 'de-DE', 'jp-JP'
		);

		$this->imdb_languages = array(
			'en-US', 'pt-BR', 'pt-PT'
		);

		$this->imdb_languages = array(
			'en-US', 'pt-BR'
		);

		/*
		if ( $this->imdb_language === 'en-US' ) {
			break;
		}
		*/

		foreach ( $this->imdb_languages as $this->imdb_language ) {

			$this->http->id_cache_folder = $this->http->ttid_cache_folder . $this->id;
			$this->http->language_cache_folder = $this->http->id_cache_folder . '/' . $this->imdb_language . '/';

			if ( !is_dir( $this->http->language_cache_folder ) && !mkdir( $this->http->language_cache_folder, 0755, true ) ) {
				$this->_setError( 'Protected_Run_File_Exists_Mk_Dir' );
				return false;
			}

			$imdb_index_folder[$this->imdb_language] = $this->http->id_cache_folder . '/' . $this->imdb_language . '/' . 'index' . '/';
			$imdb_releaseinfo_folder[$this->imdb_language] = $this->http->id_cache_folder . '/' . $this->imdb_language . '/' . 'releaseinfo' . '/';
			$imdb_fullcredits_folder[$this->imdb_language] = $this->http->id_cache_folder . '/' . $this->imdb_language . '/' . 'fullcredits' . '/';
			$imdb_plotsummary_folder[$this->imdb_language] = $this->http->id_cache_folder . '/' . $this->imdb_language . '/' . 'plotsummary' . '/';

			/**
			 * Imdb Index Body
			 */

			if ( !file_exists( $imdb_index_folder[$this->imdb_language] . 'body' ) || filesize( $imdb_index_folder[$this->imdb_language] . 'body' ) === 0 ) {

				$imdb_http_result[$this->imdb_language] = $this->_curl_get( $imdb_index_url, $this->imdb_language, $imdb_index_folder[$this->imdb_language] );
				$this->imdb_index_body[$this->imdb_language] = isset( $imdb_http_result[$this->imdb_language]['body'] ) && trim( $imdb_http_result[$this->imdb_language]['body'] ) !== '' ? $imdb_http_result[$this->imdb_language]['body'] : ( ( $tmp = file_get_contents( $imdb_index_folder[$this->imdb_language] . 'body' ) ) !== false && trim( $tmp ) !== '' ? $tmp : null );

			} else {

				$this->imdb_index_body[$this->imdb_language] = ( ( $tmp = file_get_contents( $imdb_index_folder[$this->imdb_language] . 'body' ) ) !== false && trim( $tmp ) !== '' ? $tmp : null );

			}

			/**
			 * Imdb Release Info
			 */

			if ( !file_exists( $imdb_releaseinfo_folder[$this->imdb_language] . 'body' ) || filesize( $imdb_releaseinfo_folder[$this->imdb_language] . 'body' ) === 0 ) {

				$imdb_http_result[$this->imdb_language] = $this->_curl_get( $imdb_releaseinfo_url, $this->imdb_language, $imdb_releaseinfo_folder[$this->imdb_language] );
				$this->imdb_releaseinfo_body[$this->imdb_language] = isset( $imdb_http_result[$this->imdb_language]['body'] ) && trim( $imdb_http_result[$this->imdb_language]['body'] ) !== '' ? $imdb_http_result[$this->imdb_language]['body'] : ( ( $tmp = file_get_contents( $imdb_releaseinfo_folder[$this->imdb_language] . 'body' ) ) !== false && trim( $tmp ) !== '' ? $tmp : null );

			} else {

				$this->imdb_releaseinfo_body[$this->imdb_language] = ( ( $tmp = file_get_contents( $imdb_releaseinfo_folder[$this->imdb_language] . 'body' ) ) !== false && trim( $tmp ) !== '' ? $tmp : null );

			}

			/**
			 * Imdb Full Credits
			 */

			if ( !file_exists( $imdb_fullcredits_folder[$this->imdb_language] . 'body' ) || filesize( $imdb_fullcredits_folder[$this->imdb_language] . 'body' ) === 0 ) {

				if ( isset( $this->settings->only_english ) && $this->settings->only_english && $this->imdb_language !== 'en-US' ) {
					$this->imdb_fullcredits_body[$this->imdb_language] = isset( $this->imdb_fullcredits_body['en-US'] ) ? $this->imdb_fullcredits_body['en-US'] : '';
				} else {
					$imdb_http_result[$this->imdb_language] = $this->_curl_get( $imdb_fullcredits_url, $this->imdb_language, $imdb_fullcredits_folder[$this->imdb_language] );
					$this->imdb_fullcredits_body[$this->imdb_language] = isset( $imdb_http_result[$this->imdb_language]['body'] ) && trim( $imdb_http_result[$this->imdb_language]['body'] ) !== '' ? $imdb_http_result[$this->imdb_language]['body'] : ( ( $tmp = file_get_contents( $imdb_fullcredits_folder[$this->imdb_language] . 'body' ) ) !== false && trim( $tmp ) !== '' ? $tmp : null );
				}

			} else {

				$this->imdb_fullcredits_body[$this->imdb_language] = ( ( $tmp = file_get_contents( $imdb_fullcredits_folder[$this->imdb_language] . 'body' ) ) !== false && trim( $tmp ) !== '' ? $tmp : null );

			}

			/**
			 * Imdb Plot Summary
			 */

			if ( !file_exists( $imdb_plotsummary_folder[$this->imdb_language] . 'body' ) || filesize( $imdb_plotsummary_folder[$this->imdb_language] . 'body' ) === 0 ) {

				if ( isset( $this->settings->only_english ) && $this->settings->only_english && $this->imdb_language !== 'en-US' ) {
					$this->imdb_plotsummary_body[$this->imdb_language] = isset( $this->imdb_plotsummary_body['en-US'] ) ? $this->imdb_plotsummary_body['en-US'] : '';
				} else {
					$imdb_http_result[$this->imdb_language] = $this->_curl_get( $imdb_plotsummary_url, $this->imdb_language, $imdb_plotsummary_folder[$this->imdb_language] );
					$this->imdb_plotsummary_body[$this->imdb_language] = isset( $imdb_http_result[$this->imdb_language]['body'] ) && trim( $imdb_http_result[$this->imdb_language]['body'] ) !== '' ? $imdb_http_result[$this->imdb_language]['body'] : ( ( $tmp = file_get_contents( $imdb_plotsummary_folder[$this->imdb_language] . 'body' ) ) !== false && trim( $tmp ) !== '' ? $tmp : null );
				}

			} else {

				$this->imdb_plotsummary_body[$this->imdb_language] = ( ( $tmp = file_get_contents( $imdb_plotsummary_folder[$this->imdb_language] . 'body' ) ) !== false && trim( $tmp ) !== '' ? $tmp : null );

			}

		}

		return true;

	}

	protected function _parse() {

		foreach ( $this->imdb_languages as $this->imdb_language ) {

			$this->raw->index_document_title[$this->imdb_language] = null;
			$this->raw->index_image[$this->imdb_language] = null;
			$this->raw->index_image_full[$this->imdb_language] = null;
			$this->raw->index_image_320[$this->imdb_language] = null;
			$this->raw->index_h1_title[$this->imdb_language] = null;
			$this->raw->extra_title[$this->imdb_language] = null;
			#$this->raw->pg[$this->imdb_language] = null;
			$this->raw->index_duration[$this->imdb_language] = null;
			$this->raw->index_genres[$this->imdb_language] = null;
			$this->raw->index_year[$this->imdb_language] = null;
			$this->raw->index_release_year[$this->imdb_language] = null;
			$this->raw->index_release_date[$this->imdb_language] = null;
			$this->raw->index_release_date_country = isset( $this->raw->index_release_date_country ) ? $this->raw->index_release_date_country : array();

			$this->raw->index_rating[$this->imdb_language] = null;

			#$this->raw->index_votes[$this->imdb_language] = null;
			#$this->raw->index_metascore[$this->imdb_language] = null;
			#$this->raw->index_reviews[$this->imdb_language] = null;
			#$this->raw->index_critics[$this->imdb_language] = null;
			#$this->raw->index_metacritic[$this->imdb_language] = null;

			$this->raw->index_description[$this->imdb_language] = null;
			$this->raw->index_directors[$this->imdb_language] = null;
			$this->raw->index_director[$this->imdb_language] = null;
			$this->raw->index_writers[$this->imdb_language] = null;
			$this->raw->index_writer[$this->imdb_language] = null;
			$this->raw->index_actors[$this->imdb_language] = null;
			$this->raw->index_actor[$this->imdb_language] = null;

			#$this->raw->index_videos[$this->imdb_language] = null;
			#$this->raw->index_photos[$this->imdb_language] = null;
			#$this->raw->index_cast[$this->imdb_language] = null;
			#$this->raw->index_related_ids[$this->imdb_language] = null;
			#$this->raw->index_related_titles[$this->imdb_language] = null;

			#$this->raw->index_storyline_description[$this->imdb_language] = null;
			#$this->raw->index_storyline_keywords[$this->imdb_language] = null;
			#$this->raw->index_storyline_taglines[$this->imdb_language] = null;
			#$this->raw->index_storyline_genres[$this->imdb_language] = null;
			#$this->raw->index_storyline_mpaa[$this->imdb_language] = null;

			#$this->raw->index_details_country[$this->imdb_language] = null;
			#$this->raw->index_details_language[$this->imdb_language] = null;
			#$this->raw->index_details_release_date[$this->imdb_language] = null;
			#$this->raw->index_details_also[$this->imdb_language] = null;
			#$this->raw->index_details_locations[$this->imdb_language] = null;

			#$this->raw->index_box_office_budget[$this->imdb_language] = null;
			#$this->raw->index_box_office_weekend[$this->imdb_language] = null;
			#$this->raw->index_box_office_gross[$this->imdb_language] = null;

			#$this->raw->index_credits_production[$this->imdb_language] = null;

			#$this->raw->index_technical_runtime[$this->imdb_language] = null;
			#$this->raw->index_technical_sound_mix[$this->imdb_language] = null;
			#$this->raw->index_technical_color[$this->imdb_language] = null;
			#$this->raw->index_technical_aspect_ratio[$this->imdb_language] = null;

			$this->raw->info_original_title[$this->imdb_language] = null;
			$this->raw->info_english_title[$this->imdb_language] = null;
			$this->raw->info_brazil_title[$this->imdb_language] = null;
			$this->raw->info_portugal_title[$this->imdb_language] = null;

			$this->raw->info_original_title_2[$this->imdb_language] = null;
			$this->raw->info_english_title_2[$this->imdb_language] = null;
			$this->raw->info_brazil_title_2[$this->imdb_language] = null;
			$this->raw->info_portugal_title_2[$this->imdb_language] = null;

			$this->raw->info_original_title_3[$this->imdb_language] = null;
			$this->raw->info_english_title_3[$this->imdb_language] = null;
			$this->raw->info_brazil_title_3[$this->imdb_language] = null;
			$this->raw->info_portugal_title_3[$this->imdb_language] = null;

			$this->raw->info_original_title_4[$this->imdb_language] = null;
			$this->raw->info_english_title_4[$this->imdb_language] = null;
			$this->raw->info_brazil_title_4[$this->imdb_language] = null;
			$this->raw->info_portugal_title_4[$this->imdb_language] = null;

			$this->_setIndexDocumentTitle();
			$this->_setIndexImage();
			$this->_setIndexH1Title();
			$this->_compareIndexTitles();
			$this->_setIndexDuration();
			$this->_setIndexGenres();
			$this->_setIndexReleaseDate();
			$this->_setIndexRating();

			#$this->_setIndexVotes();
			#$this->_setIndexMetascore();
			#$this->_setIndexReviews();
			#$this->_setIndexCritics();
			#$this->_setIndexMetacritic();

			$this->_setIndexDescription();
			$this->_setIndexDirectors();
			$this->_setIndexWriters();
			$this->_setIndexActors();
			$this->_setIndexVideos();
			$this->_setIndexPhotos();
			$this->_setIndexCast();
			$this->_setIndexRelated();

			#$this->_setWriters();
			#$this->_setStars();
			#$this->_setVideos();
			#$this->_setPhotos();
			#$this->_setRelated();
			#$this->_setCast();
			#$this->_setStoryline();
			#$this->_setDetails();
			#$this->_setBoxOffice();
			#$this->_setCompanyCredits();
			#$this->_setTechnicalSpecs();

			#$this->_setAkas();
			#$this->_setDirectors();
			#$this->_setWriters();
			#$this->_setActors();
			#$this->_setDescriptions();

		}

		var_dump( $this->raw );

		$this->_rawToNormal();

		return true;

	}

	protected function _rawToNormal() {

		if ( !isset( $this->raw ) ) {
			$this->_setError( 'Protected_RawToNormal_Raw_Undefined' );
			return false;
		}

		echo 'RawToNormal';

	}

	/**
	 * HTTP Functions
	 */

	protected function _curl_get( $url, $language, $dir ) {

		if ( !is_dir( $dir ) && !mkdir( $dir, 0755, true ) ) {
			return false;
		}

		if ( file_exists( $dir . 'body' ) ) {
			var_dump( $this );
		}

		$ch = curl_init();

		$headers = array(
			'Accept-Language: ' . $language,
		);

		$options = array(
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_ENCODING => '',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER => true,
			CURLOPT_HTTPGET => true,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_NOPROGRESS => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 15,
			CURLOPT_URL => $url,
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

		file_put_contents( $dir . 'info', serialize( $info ) );
		file_put_contents( $dir . 'head', $head );
		file_put_contents( $dir . 'body', $body );
		file_put_contents( $dir . 'error', $error );
		file_put_contents( $dir . 'errno', $errno );
		file_put_contents( $dir . 'url', $info['url'] );

		return true;

	}

	/**
	 * Helper Functions
	 */

	function _convertGenresToGeneros( $genres, $sort = false ) {

		$generos = $genres;

		foreach ( $generos as $key => $value ) {

			switch ( strtolower( $value ) ) {
				case 'action': $value = 'Ação'; break;
				case 'adventure': $value = 'Aventura'; break;
				case 'animation': $value = 'Animação'; break;
				case 'biography': $value = 'Biografia'; break;
				case 'comedy': $value = 'Comédia'; break;
				case 'documentary': $value = 'Documentário'; break;
				case 'family': $value = 'Família'; break;
				case 'fantasy': $value = 'Fantasia'; break;
				case 'history': $value = 'História'; break;
				case 'horror': $value = 'Terror'; break;
				case 'music': $value = 'Musical'; break;
				case 'mystery': $value = 'Mistério'; break;
				case 'sci-fi': $value = 'Ficção'; break;
				case 'sport': $value = 'Esportes'; break;
				case 'sports': $value = 'Esportes'; break;
				case 'thriller': $value = 'Suspense'; break;
				case 'war': $value = 'Guerra'; break;
				case 'western': $value = 'Faroeste'; break;
				default: $value = ucfirst( strtolower( $value ) ); break;
			}

			$generos[$key] = $value;

		}

		$sort && sort( $generos );

		return $generos;

	}

	protected function _esc_attr( $value ) {

		if ( defined( 'ABSPATH' ) && function_exists( 'esc_attr' ) ) {
			return esc_attr( $value );
		}

		if ( ( $tmp = htmlspecialchars( $value ) ) !== '' ) {
			return $tmp;
		}

		$value = str_replace( '"', '&#34;', $value );
		$value = str_replace( "'", '&#39;', $value );

		return $value;

	}

	/**
	 * Set Index - Reescrevendo algumas coisas e padronizando outras
	 */

	protected function _setIndexDocumentTitle() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_setIndexDocumentTitle_Index_Body_Or_Language_Undefined' );
			return false;
		}

		if ( !preg_match_all( '/\<title\>[\x20]*([^\x20]+[^\x3c]+[^\x20]+)[\x20]*\<\/title\>/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_document_title_matches[$this->imdb_language] ) ) {
			$this->_setError( 'Protected_setIndexDocumentTitle_Document_Title_Preg_Match_All_False_' . $this->imdb_language );
			return false;
		}

		if ( !isset( $imdb_page_index_document_title_matches[$this->imdb_language][1] ) ) {
			$this->_setError( 'Protected_setIndexDocumentTitle_Document_Title_Matches_Undefined_' . $this->imdb_language );
			return false;
		}

		if ( count( $imdb_page_index_document_title_matches[$this->imdb_language][1] ) !== 1 ) {
			$this->_setError( 'Protected_setIndexDocumentTitle_Document_Title_Count_Diff_1_' . $this->imdb_language );
			return false;
		}

		$imdb_page_index_document_title_matches[$this->imdb_language][1][0] = trim( preg_replace( '/^([^\x20]+[^\x28\x29]+[^\x20\]+[^\x20]+) \((?:TV Mini\-Series\x20|TV Movie\x20|TV Episode\x20|TV Short\x20|Video\x20)?(?:201[0-5]|200[0-9]|19[6-9][0-9])\) \- IMDb$/', '$1', $imdb_page_index_document_title_matches[$this->imdb_language][1][0], -1, $prcount[$this->imdb_language] ) );
		$imdb_page_index_document_title_matches[$this->imdb_language][1][0] = $prcount[$this->imdb_language] === 1 ? $imdb_page_index_document_title_matches[$this->imdb_language][1][0] : trim( preg_replace( '/^([^\x20]+.+[^\x20]+[^\x20]+) \((?:Video\x20)?(?:201[0-5]|200[0-9]|19[6-9][0-9])\) \- IMDb$/', '$1', $imdb_page_index_document_title_matches[$this->imdb_language][1][0], -1, $prcount[$this->imdb_language] ) );

		if ( $prcount[$this->imdb_language] === 0 ) {
			$this->_setError( 'Protected_setIndexDocumentTitle_Preg_Replace_Count_Equal_Zero_' . $this->imdb_language );
			return false;
		}

		$this->raw->index_document_title[$this->imdb_language] = $imdb_page_index_document_title_matches[$this->imdb_language][1][0];

		return true;

	}

	protected function _setIndexImage() {

		$imdb_page_index_td_id_img_primary_html[$this->imdb_language] = $this->imdb_index_body[$this->imdb_language];
		$imdb_page_index_td_id_img_primary_open_pos[$this->imdb_language] = stripos( $imdb_page_index_td_id_img_primary_html[$this->imdb_language], '<td rowspan="2" id="img_primary">' );

		if ( $imdb_page_index_td_id_img_primary_open_pos[$this->imdb_language] !== false ) {
			$imdb_page_index_td_id_img_primary_html[$this->imdb_language] = substr( $imdb_page_index_td_id_img_primary_html[$this->imdb_language], $imdb_page_index_td_id_img_primary_open_pos[$this->imdb_language] );
			$imdb_page_index_td_id_img_primary_close_pos[$this->imdb_language] = stripos( $imdb_page_index_td_id_img_primary_html[$this->imdb_language], '</td>' );
			if ( $imdb_page_index_td_id_img_primary_close_pos[$this->imdb_language] !== false ) {
				$imdb_page_index_td_id_img_primary_html[$this->imdb_language] = substr( $imdb_page_index_td_id_img_primary_html[$this->imdb_language], 0, $imdb_page_index_td_id_img_primary_close_pos[$this->imdb_language] );
			}
			$imdb_page_index_td_id_img_primary_html[$this->imdb_language] = trim( preg_replace( '/[\x09\x0a\x0b\x0c\x0d]/', "\x20", $imdb_page_index_td_id_img_primary_html[$this->imdb_language] ) );
			if ( preg_match( '/\<img\b[^\x3e]*src\=(?:\x22([^\x22]+)\x22|\x27([^\x27]+)\x27|([^\x20\x22\x27]+)) itemprop=\"image\"/', $imdb_page_index_td_id_img_primary_html[$this->imdb_language], $imdb_page_index_image_matches[$this->imdb_language] ) ) {
				$this->raw->index_image[$this->imdb_language] = $imdb_page_index_image_matches[$this->imdb_language][1];
				$this->raw->index_image_full[$this->imdb_language] = $this->raw->index_image[$this->imdb_language];
				$this->raw->index_image_320[$this->imdb_language] = $this->raw->index_image[$this->imdb_language];
				$this->raw->index_image_full[$this->imdb_language] = preg_replace( '/([@]+)(\._(.+)\.jpg)/', '$1.jpg', $this->raw->index_image_full[$this->imdb_language] );
				$this->raw->index_image_320[$this->imdb_language] = preg_replace( '/V(1)_S(Y)317_CR(0)\,(0)\,214\,317_AL_\.jpg/', 'V$1_S$2;;;474_CR$3,$4,320,474_AL_.jpg', $this->raw->index_image_320[$this->imdb_language] );
				$this->raw->index_image_320[$this->imdb_language] = str_replace( ';;;', '', $this->raw->index_image_320[$this->imdb_language] );
			}
		}

		return true;

	}

	protected function _setIndexH1Title() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_setIndexH1Title_Index_Body_Or_Language_Undefined' );
			return false;
		}

		if ( !preg_match_all( '/\<h1 class\=\"header\"\>[\x20]*\<span class\=\"itemprop\" itemprop\=\"name\"\>[\x20]*([^\x20]+[^\x3c]+[^\x20]+)[\x20]*\<\/span\>/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_h1_title_matches[$this->imdb_language] ) ) {
			$this->_setError( 'Protected_setIndexH1Title_H1_Title_Preg_Match_All_False_' . $this->imdb_language );
			return false;
		}

		if ( !isset( $imdb_page_index_h1_title_matches[$this->imdb_language][1] ) ) {
			$this->_setError( 'Protected_setIndexH1Title_H1_Title_Matches_Undefined_' . $this->imdb_language );
			return false;
		}

		if ( count( $imdb_page_index_h1_title_matches[$this->imdb_language][1] ) !== 1 ) {
			$this->_setError( 'Protected_setIndexH1Title_H1_Title_Count_Matches_Diff_1_' . $this->imdb_language );
			return false;
		}

		$this->raw->index_h1_title[$this->imdb_language] = $imdb_page_index_h1_title_matches[$this->imdb_language][1][0];

		return true;

	}

	protected function _compareIndexTitles() {

		if ( isset( $this->raw->index_document_title[$this->imdb_language] ) && isset( $this->raw->h1_title[$this->imdb_language] ) ) {

			if ( $this->raw->index_document_title[$this->imdb_language] !== $this->raw->h1_title[$this->imdb_language] ) {
				$this->_setError( 'Protected_CompareIndexTitles_Titles_Dont_Match_' . $this->imdb_language );
				return false;
			}

		} elseif ( isset( $this->raw->index_document_title[$this->imdb_language] ) && isset( $this->raw->extra_title[$this->imdb_language] ) ) {

			if ( $this->raw->index_document_title[$this->imdb_language] !== $this->raw->extra_title[$this->imdb_language] ) {
				$this->_setError( 'Protected_CompareIndexTitles_Titles_Dont_Match_' . $this->imdb_language );
				return false;
			}

		} else {

			return false;

		}

		return true;

	}

	protected function _setIndexDuration() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_SetIndexDuration_Index_Body_Or_Language_Undefined_' );
			return false;
		}

		$imdb_page_index_infobar_html[$this->imdb_language] = $this->imdb_index_body[$this->imdb_language];
		$imdb_page_index_infobar_open_pos[$this->imdb_language] = stripos( $imdb_page_index_infobar_html[$this->imdb_language], '<div class="infobar">' );
		$imdb_page_index_infobar_close_pos[$this->imdb_language] = stripos( $imdb_page_index_infobar_html[$this->imdb_language], '<div class="star-box giga-star">' );

		if ( $imdb_page_index_infobar_open_pos[$this->imdb_language] === false || $imdb_page_index_infobar_close_pos[$this->imdb_language] === false ) {
			$this->_setError( 'Protected_SetIndexDuration_Open_Or_Close_Equal_False_' . __LINE__ );
			return false;
		}

		$imdb_page_index_infobar_html[$this->imdb_language] = trim( substr( $imdb_page_index_infobar_html[$this->imdb_language], $imdb_page_index_infobar_open_pos[$this->imdb_language] + 21, $imdb_page_index_infobar_close_pos[$this->imdb_language] - $imdb_page_index_infobar_open_pos[$this->imdb_language] - 21 ) );
		$imdb_page_index_infobar_html[$this->imdb_language] = trim( preg_replace( '/[\x09\x0a\x0b\x0c\x0d]/', "\x20", $imdb_page_index_infobar_html[$this->imdb_language] ) );

		if ( ( !isset( $this->settings->duration_is_manual ) || !$this->settings->duration_is_manual ) && !preg_match_all( '/\<time itemprop=\"duration\" datetime\=\"PT([0-9]+)M\"[^\x3e]*\>[\x20]*([0-9]+) min[\x20]*\<\/time\>/', $imdb_page_index_infobar_html[$this->imdb_language], $imdb_page_index_duration_matches[$this->imdb_language], PREG_SET_ORDER ) ) {
			$this->_setError( 'Protected_SetIndexDuration_Open_Or_Close_Equal_False_' . __LINE__ );
			return false;
		}

		if ( ( !isset( $this->settings->duration_is_manual ) || !$this->settings->duration_is_manual ) && !isset( $imdb_page_index_duration_matches[$this->imdb_language][0][1] ) ) {
			$this->_setError( 'Protected_SetIndexDuration_Matches_Undefined_' . __LINE__ );
			return false;
		}

		$this->raw->index_duration[$this->imdb_language] = isset( $imdb_page_index_duration_matches[$this->imdb_language][0][1] ) ? $imdb_page_index_duration_matches[$this->imdb_language][0][1] : null;

		return true;

	}

	protected function _setIndexGenres() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_SetIndexGenres_Index_Body_Or_Language_Undefined' );
			return false;
		}

		if ( !preg_match_all( '/\<span class\=\"itemprop\" itemprop\=\"genre\"\>[\x20]*([^\x20]+[^\x3c]+[^\x20]+)[\x20]*\<\/span\>/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_genres_matches[$this->imdb_language] ) ) {
			$this->_setError( 'Protected_SetIndexGenres_Preg_Match_All_False_' . $this->imdb_language );
			return false;
		}

		if ( !isset( $imdb_page_index_genres_matches[$this->imdb_language][1] ) ) {
			$this->_setError( 'Protected_SetIndexGenres_Matches_Undefined_' . $this->imdb_language );
			return false;
		}

		$this->raw->index_genres[$this->imdb_language] = $imdb_page_index_genres_matches[$this->imdb_language][1];

		return true;

	}

	protected function _setIndexReleaseDate() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_SetIndexReleaseDate_Index_Body_Or_Language_Undefined' );
			return false;
		}

		$imdb_page_index_span_class_nobr_count[$this->imdb_language] = substr_count( $this->imdb_index_body[$this->imdb_language], '<span class="nobr">(<a href="/year/' );

		if ( !$imdb_page_index_span_class_nobr_count[$this->imdb_language] ) {
			if ( !preg_match( '/\<span class\=\"nobr\"\>\([0-9]+\)\<\/span\>[\x09\x0a\x0b\x0c\x0d\x20]+\<\/h1\>/', $this->imdb_index_body[$this->imdb_language] ) ) {
				if ( !preg_match( '/\<span class\=\"nobr\"\>\(201[0-5]|200[0-9]|19[6-9][0-9]\)\<\/span\>[\x09\x0a\x0b\x0c\x0d\x20]*\<br\/\>\<span class\=\"title\-extra\" itemprop\=\"name\"\>/', $this->imdb_index_body[$this->imdb_language] ) ) {
					$this->_setError( 'Protected_SetIndexReleaseDate_Substr_Count_Preg_Match_Both_Error_' . $this->imdb_language );
					return false;
				}
			}
			$imdb_page_index_span_class_nobr_count[$this->imdb_language] = 1;
		}

		if ( $imdb_page_index_span_class_nobr_count[$this->imdb_language] !== 1 ) {
			$this->_setError( 'Protected_SetIndexReleaseDate_NoBr_Count_Diff_1_' . $this->imdb_language );
			return false;
		}

		if ( !preg_match_all( '/\<span class\=\"nobr\"\>\(\<a href\=\"\/year\/(201[0-5]|200[0-9]|19[6-9][0-9])/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_years_nobr_matches[$this->imdb_language] ) ) {
			if ( !preg_match_all( '/\<span class\=\"nobr\"\>\((201[0-5]|200[0-9]|19[6-9][0-9])\)\<\/span\>[\x09\x0a\x0b\x0c\x0d\x20]+\<\/h1\>/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_years_nobr_matches[$this->imdb_language] ) ) {
				if ( !preg_match_all( '/<span class=\"nobr\"\>\((201[0-5]|200[0-9]|19[6-9][0-9])\)\<\/span\>[\x09\x0a\x0b\x0c\x0d\x20]*\<br\/>\<span class\="title\-extra\" itemprop\=\"name\"\>[\x09\x0a\x0b\x0c\x0d\x20]*\"?([^\x22\x3c]+)\"?[\x09\x0a\x0b\x0c\x0d\x20]*\<i\>[\x09\x0a\x0b\x0c\x0d\x20]*\(original title\)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/i\>/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_years_nobr_matches[$this->imdb_language] ) ) {
					if ( !preg_match_all( '/tv_header\"\>[\s\S]+\<span class\=\"nobr\"\>\([0-9]+ (?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\. (201[0-5]|200[0-9]|19[6-9][0-9])\)\<\/span\>/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_years_nobr_matches[$this->imdb_language] ) ) {
						$this->_setError( 'Protected_SetIndexReleaseDate_Preg_Match_All_False_' . $this->imdb_language );
						return false;
					}
				}
			}
		}

		if ( !isset( $imdb_page_index_years_nobr_matches[$this->imdb_language][1] ) ) {
			$this->_setError( 'Protected_SetIndexReleaseDate_Matches_Undefined_' . $this->imdb_language );
			return false;
		}

		if ( count( $imdb_page_index_years_nobr_matches[$this->imdb_language][1] ) !== 1 ) {
			$this->_setError( 'Protected_SetIndexReleaseDate_Count_Matches_Diff_1_' . $this->imdb_language );
			return false;
		}

		$this->raw->index_year[$this->imdb_language] = isset( $imdb_page_index_years_nobr_matches[$this->imdb_language][1][0] ) && is_numeric( $imdb_page_index_years_nobr_matches[$this->imdb_language][1][0] ) ? $imdb_page_index_years_nobr_matches[$this->imdb_language][1][0] : ( isset( $imdb_page_index_years_nobr_matches[$this->imdb_language][1][1] ) && is_numeric( $imdb_page_index_years_nobr_matches[$this->imdb_language][1][1] ) ? $imdb_page_index_years_nobr_matches[$this->imdb_language][1][1] : null );

		$imdb_page_index_meta_datePublished_pos[$this->imdb_language] = stripos( $this->imdb_index_body[$this->imdb_language], '<meta itemprop="datePublished"' );

		$imdb_page_index_meta_datepublished_html[$this->imdb_language] = $this->imdb_index_body[$this->imdb_language];
		$imdb_page_index_meta_datepublished_html[$this->imdb_language] = trim( substr( $imdb_page_index_meta_datepublished_html[$this->imdb_language], $imdb_page_index_meta_datePublished_pos[$this->imdb_language] - 256, 512 ) );
		$imdb_page_index_meta_datepublished_html[$this->imdb_language] = trim( preg_replace( '/[\x09\x0a\x0b\x0c\x0d]/', "\x20", $imdb_page_index_meta_datepublished_html[$this->imdb_language] ) );

		/**
		 * 28 January 2011<meta itemprop="datePublished" content="2011-01-28" /> (USA) <
		 */

		$this->settings->duration_is_manual = isset( $this->settings->duration_is_manual ) ? $this->settings->duration_is_manual : false;

		if ( $this->id === 'tt0831280' ) {
			$imdb_page_index_meta_datepublished_html[$this->imdb_language] = str_replace( '2010<meta itemprop="datePublished" content="2010"', '12 July 2011<meta itemprop="datePublished" content="2011-07-12"', $imdb_page_index_meta_datepublished_html[$this->imdb_language] );
			$imdb_page_index_duration_matches[$this->imdb_language][0][1] = '89';
		} elseif ( $this->id === 'tt0345777' ) {
			$imdb_page_index_meta_datepublished_html[$this->imdb_language] = '1 January 2004<meta itemprop="datePublished" content="2004-01-01" /> (USA) </a>';
		} elseif ( $this->id === 'tt2446040' ) {
			$imdb_page_index_meta_datepublished_html[$this->imdb_language] = '16 October 2013<meta itemprop="datePublished" content="2013-10-16" /> (USA) </a>';
		} elseif ( $this->id === 'tt2391094' ) {
			$imdb_page_index_duration_matches[$this->imdb_language][0][1] = '90';
		} elseif ( $this->id === 'tt1784359' ) {
			$imdb_page_index_duration_matches[$this->imdb_language][0][1] = '102';
		} elseif ( $this->id === 'tt1921149' ) {
			$imdb_page_index_duration_matches[$this->imdb_language][0][1] = '108';
		} elseif ( $this->id === 'tt2376272' ) {
			$imdb_page_index_duration_matches[$this->imdb_language][0][1] = '90';
		} elseif ( $this->id === 'tt1131747' ) {
			$imdb_page_index_duration_matches[$this->imdb_language][0][1] = '104';
			$imdb_page_index_meta_datepublished_html[$this->imdb_language] = '1 October 2008<meta itemprop="datePublished" content="2008-09-01" /> (USA) </a>';
		} elseif ( $this->id === 'tt1335992' ) {
			$imdb_page_index_duration_matches[$this->imdb_language][0][1] = '87';
		} elseif ( $this->id === 'tt1051253' ) {
			$imdb_page_index_duration_matches[$this->imdb_language][0][1] = '84';
			$imdb_page_index_meta_datepublished_html[$this->imdb_language] = '1 January 2010<meta itemprop="datePublished" content="2010-01-01" /> (USA) </a>';
		} elseif ( $this->id === 'tt1754799' ) {

			if ( $this->imdb_language === 'en-US' ) {
				$imdb_page_index_meta_datepublished_html[$this->imdb_language] = '25 March 2011<meta itemprop="datePublished" content="2011-03-25" /> (USA) </a>';
			} elseif ( $this->imdb_language === 'pt-BR' ) {
				$imdb_page_index_meta_datepublished_html[$this->imdb_language] = '12 May 2013<meta itemprop="datePublished" content="2013-05-12" /> (Brazil) </a>';
			} elseif ( $this->imdb_language === 'pt-PT' ) {
				$imdb_page_index_meta_datepublished_html[$this->imdb_language] = '25 March 2011<meta itemprop="datePublished" content="2011-03-25" /> (Portugal) </a>';
			}

		}

		$this->settings->duration_is_manual = isset( $imdb_page_index_duration_matches[$this->imdb_language][0][1] );

		/**
		 * 13 June 2014
		 * 13
		 * June
		 * 2014
		 * 2014-06-13
		 * 2014
		 * 06
		 * 13
		 * USA
		 */

		if ( preg_match_all( '/((3[0-1]|[1-2][0-9]|[1-9]) (January|February|March|April|May|June|July|August|September|October|November|December) (201[0-5]|200[0-9]|19[6-9][0-9])).+\=[\x22\x27]{0,1}((201[0-5]|200[0-9]|19[6-9][0-9])\-(1[0-2]|0[1-9])\-(3[0-1]|[1-2][0-9]|0[1-9]))[\x22\x27]{0,1}[^\x3e]*\/?\>[\x20]*\((West Germany|South Africa|USA|Brazil|Mexico|Croatia|Indonesia|Hong Kong|Spain|Norway|Portugal|France|Italy|Germany|Japan|Canada|Netherlands|Ireland|China|UK|Argentina|Belgium)\)[\x20]*(?:&ndash;)?[\x20]*\x3c/', $imdb_page_index_meta_datepublished_html[$this->imdb_language], $imdb_page_index_meta_datepublished_matches[$this->imdb_language], PREG_SET_ORDER ) ) {
		} elseif ( preg_match_all( '/((.) (January|February|March|April|May|June|July|August|September|October|November|December) (201[0-5]|200[0-9]|19[6-9][0-9])).+\=[\x22\x27]{0,1}((201[0-5]|200[0-9]|19[6-9][0-9])\-(1[0-2]|0[1-9])(?:\-(.))?)[\x22\x27]{0,1}[^\x3e]*\/?\>[\x20]*\((West Germany|South Africa|USA|Brazil|Mexico|Croatia|Indonesia|Hong Kong|Spain|Norway|Portugal|France|Italy|Germany|Japan|Canada|Netherlands|Ireland|China|UK|Argentina|Belgium)\)[\x20]*(?:&ndash;)?[\x20]*\x3c/', $imdb_page_index_meta_datepublished_html[$this->imdb_language], $imdb_page_index_meta_datepublished_matches[$this->imdb_language], PREG_SET_ORDER ) ) {
			$imdb_page_index_meta_datepublished_matches[$this->imdb_language][0][1] = str_replace( '>', '1', $imdb_page_index_meta_datepublished_matches[$this->imdb_language][0][1] );
			$imdb_page_index_meta_datepublished_matches[$this->imdb_language][0][2] = '1';
			$imdb_page_index_meta_datepublished_matches[$this->imdb_language][0][5] .= '-01';
			$imdb_page_index_meta_datepublished_matches[$this->imdb_language][0][8] = '01';
		} else {

			$this->_setError( 'Protected_SetIndexReleaseDate_DatePublished_Not_Found_' . $this->imdb_language );
			return false;

		}

		$this->raw->index_release_year[$this->imdb_language] = $imdb_page_index_meta_datepublished_matches[$this->imdb_language][0][4];

		$country_key[$this->imdb_language] = $imdb_page_index_meta_datepublished_matches[$this->imdb_language][0][9];

		$imdb_page_index_meta_datapublicada[$this->imdb_language] = $imdb_page_index_meta_datepublished_matches[$this->imdb_language][0][1];
		$imdb_page_index_meta_datapublicada[$this->imdb_language] = str_ireplace( array( ' January ', ' February ', ' March ', ' April ', ' May ', ' June ', ' July ', ' August ', ' September ', ' October ', ' November ', ' December ' ), array( ' de Janeiro de ', ' de Fevereiro de ', ' de Março de ', ' de Abril de ', ' de Maio de ', ' de Junho de ', ' de Julho de ', ' de Agosto de ', ' de Setembro de ', ' de Outubro de ', ' de Novembro de ', ' de Dezembro de ' ), $imdb_page_index_meta_datapublicada[$this->imdb_language] );
		$imdb_page_index_meta_datapublicada[$this->imdb_language] = str_ireplace( array( ' January ', ' February ', ' March ', ' April ', ' May ', ' June ', ' July ', ' August ', ' September ', ' October ', ' November ', ' December ' ), array( ' de janeiro de ', ' de fevereiro de ', ' de março de ', ' de abril de ', ' de maio de ', ' de junho de ', ' de julho de ', ' de agosto de ', ' de setembro de ', ' de outubro de ', ' de novembro de ', ' de dezembro de ' ), $imdb_page_index_meta_datapublicada[$this->imdb_language] );

		$this->raw->index_release_date[$this->imdb_language] = $imdb_page_index_meta_datapublicada[$this->imdb_language];

		if ( isset( $this->raw->index_release_date_country[$country_key[$this->imdb_language]] ) ) {
			if ( $this->raw->index_release_date_country[$country_key[$this->imdb_language]] !== $imdb_page_index_meta_datapublicada[$this->imdb_language] ) {
				$this->_setError( 'Protected_SetIndexReleaseDate_Release_Date_Country_Dont_Match_' . $this->imdb_language );
				return false;
			}
		}

		$this->raw->index_release_date_country[$country_key[$this->imdb_language]] = $imdb_page_index_meta_datapublicada[$this->imdb_language];

		return true;

	}

	protected function _setIndexRating() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_SetIndexRating_Index_Body_Or_Language_Undefined_' );
			return false;
		}

		if ( !( preg_match_all( '/<div class\=\"titlePageSprite star\-box\-giga\-star\"\>[\x20]*([0-9\.]+)[\x20]*/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_rating_matches[$this->imdb_language], PREG_SET_ORDER ) || preg_match_all( '/\<span itemprop\=\"ratingValue\"\>[\x20]*([0-9\.]+)[\x20]*\<\/span\>/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_rating_matches[$this->imdb_language], PREG_SET_ORDER ) ) ) {

			$this->raw->index_rating[$this->imdb_language] = null;

			return true;

		}

		if ( count( $imdb_page_index_rating_matches[$this->imdb_language] ) !== 1 ) {
			$this->_setError( 'Protected_SetIndexRating_Count_Diff_1_' . __LINE__ );
			return false;
		}

		if ( !isset( $imdb_page_index_rating_matches[$this->imdb_language][0][1] ) ) {
			$this->_setError( 'Protected_SetIndexRating_Matches_Undefined_' . __LINE__ );
			return false;
		}

		$this->raw->index_rating[$this->imdb_language] = $imdb_page_index_rating_matches[$this->imdb_language][0][1];

		return true;

	}

	protected function _setIndexDescription() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_SetIndexDescription_Index_Body_Or_Language_Undefined_' );
			return false;
		}

		if ( !( preg_match_all( '/\<p\b[^\x3e]*itemprop\=\"description\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\<\/p\>/', $this->imdb_index_body[$this->imdb_language], $imdb_page_index_description_matches[$this->imdb_language], PREG_SET_ORDER ) ) ) {

			#$this->raw->index_description[$this->imdb_language] = null;
			#return true;

			return false;

		}

		if ( count( $imdb_page_index_description_matches[$this->imdb_language] ) !== 1 ) {
			$this->_setError( 'Protected_SetIndexDescription_Count_Diff_1_' . __LINE__ );
			return false;
		}

		if ( !isset( $imdb_page_index_description_matches[$this->imdb_language][0][1] ) ) {
			$this->_setError( 'Protected_SetIndexDescription_Matches_Undefined_' . __LINE__ );
			return false;
		}

		$this->raw->index_description[$this->imdb_language] = $imdb_page_index_description_matches[$this->imdb_language][0][1];

		return true;

	}

	protected function _setIndexDirectors() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_SetIndexDirectors_Index_Body_Or_Language_Undefined_' );
			return false;
		}

		$html = $this->imdb_index_body[$this->imdb_language];
		$open = false;
		$close = false;

		if ( ( $open = stripos( $html, '<h4 class="inline">Director:</h4>' ) ) === false ) {
			if ( ( $open = stripos( $html, '<h4 class="inline">Directors:</h4>' ) ) === false ) {
			}
		}

		if ( ( $close = stripos( $html, '<h4 class="inline">Writers:</h4>' ) ) === false ) {
			if ( ( $close = stripos( $html, '<h4 class="inline">Writers:</h4>' ) ) === false ) {
			}
		}

		if ( $open === false || $close === false ) {
			$this->_setError( 'Protected_SetIndexDirectors_Open_Or_Close_False_' . __LINE__ );
			return false;
		}

		$html = substr( $html, $open, $close - $open );

		if ( !preg_match_all( '/itemprop\=\"name\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\</', $html, $matches ) ) {
			$this->_setError( 'Protected_SetIndexDirectors_Preg_Match_All_False_' . __LINE__ );
			return false;
		}

		$this->raw->index_directors[$this->imdb_language] = $matches[1];
		$this->raw->index_director[$this->imdb_language] = $matches[1][0];

		return true;

	}

	protected function _setIndexWriters() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_SetIndexWriters_Index_Body_Or_Language_Undefined_' );
			return false;
		}

		$html = $this->imdb_index_body[$this->imdb_language];
		$open = false;
		$close = false;

		if ( ( $open = stripos( $html, '<h4 class="inline">Writers:</h4>' ) ) === false ) {
			if ( ( $open = stripos( $html, '<h4 class="inline">Writers:</h4>' ) ) === false ) {
			}
		}

		if ( ( $close = stripos( $html, '<h4 class="inline">Stars:</h4>' ) ) === false ) {
			if ( ( $close = stripos( $html, '<h4 class="inline">Stars:</h4>' ) ) === false ) {
			}
		}

		if ( $open === false || $close === false ) {
			$this->_setError( 'Protected_SetIndexWriters_Open_Or_Close_False_' . __LINE__ );
			return false;
		}

		$html = substr( $html, $open, $close - $open );

		if ( !preg_match_all( '/itemprop\=\"name\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\</', $html, $matches ) ) {
			$this->_setError( 'Protected_SetIndexWriters_Preg_Match_All_False_' . __LINE__ );
			return false;
		}

		$this->raw->index_writers[$this->imdb_language] = $matches[1];
		$this->raw->index_writer[$this->imdb_language] = $matches[1][0];

		return true;

	}

	protected function _setIndexActors() {

		if ( !isset( $this->imdb_index_body[$this->imdb_language] ) || !isset( $this->imdb_language ) ) {
			$this->_setError( 'Protected_SetIndexActors_Index_Body_Or_Language_Undefined_' );
			return false;
		}

		$html = $this->imdb_index_body[$this->imdb_language];
		$open = false;
		$close = false;

		if ( ( $open = stripos( $html, '<h4 class="inline">Stars:</h4>' ) ) === false ) {
			if ( ( $open = stripos( $html, '<h4 class="inline">Stars:</h4>' ) ) === false ) {
			}
		}

		if ( ( $close = stripos( $html, 'See full cast and crew' ) ) === false ) {
			if ( ( $close = stripos( $html, '<span class="btn2_text">Watch Trailer</span>' ) ) === false ) {
				if ( ( $close = stripos( $html, '<h3>Quick Links</h3>' ) ) === false ) {
					if ( ( $close = stripos( $html, '>Full Cast and Crew</a>' ) ) === false ) {
					}
				}
			}
		}

		if ( $open === false || $close === false ) {
			$this->_setError( 'Protected_SetIndexActors_Open_Or_Close_False_' . __LINE__ );
			return false;
		}

		$html = substr( $html, $open, $close - $open );

		if ( !preg_match_all( '/itemprop\=\"name\"[^\x3e]*\>[\x09\x0a\x0b\x0c\x0d\x20]*([^\x3c]+)[\x09\x0a\x0b\x0c\x0d\x20]*\</', $html, $matches ) ) {
			$this->_setError( 'Protected_SetIndexActors_Preg_Match_All_False_' . __LINE__ );
			return false;
		}

		$this->raw->index_actors[$this->imdb_language] = $matches[1];
		$this->raw->index_actor[$this->imdb_language] = $matches[1][0];

		return true;

	}

	protected function _setIndexVideos() {

		return true;

	}

	protected function _setIndexPhotos() {

		return true;

	}

	protected function _setIndexCast() {

		return true;

	}

	protected function _setIndexRelated() {

		return true;

	}

	/**
	 * Set Info - Reescrevendo toda essa parte debaixo
	 */

	protected function _setAkas() {

		/**
		 * Imdb - Release Info - Akas
		 */

		if ( !isset( $this->imdb_releaseinfo_body[$this->imdb_language] ) ) {
			$this->_setError( 'Protected_Parse_ReleaseInfo_Body_Language_Undefined_' . $this->imdb_language );
			return false;
		}

		$imdb_page_releaseinfo_table_id_akas_open_pos[$this->imdb_language] = stripos( $this->imdb_releaseinfo_body[$this->imdb_language], '<table id="akas"' );

		if ( $imdb_page_releaseinfo_table_id_akas_open_pos[$this->imdb_language] === false && stripos( $this->imdb_releaseinfo_body[$this->imdb_language], '"akas"' ) !== false ) {
			if ( stripos( $this->imdb_releaseinfo_body[$this->imdb_language], 'AKAs for this title yet' ) === false ) {
				$this->_setError( 'Protected_Parse_ReleaseInfo_Body_Error_Number_2_' . $this->imdb_language );
				return false;
			}
		}

		if ( $imdb_page_releaseinfo_table_id_akas_open_pos[$this->imdb_language] !== false ) {

			$imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] = trim( substr( $this->imdb_releaseinfo_body[$this->imdb_language], $imdb_page_releaseinfo_table_id_akas_open_pos[$this->imdb_language] ) );
			$imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] = ( $imdb_page_releaseinfo_table_id_akas_close_pos[$this->imdb_language] = stripos( $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language], '</table>' ) ) !== false ? trim( substr( $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language], 0, $imdb_page_releaseinfo_table_id_akas_close_pos[$this->imdb_language] + 8 ) ) : $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language];
			$imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] = trim( preg_replace( '/[\x09\x0a\x0b\x0c\x0d]/', "\x20", $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] ) );

			$imdb_page_releaseinfo_td_open_count[$this->imdb_language] = substr_count( strtolower( $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] ), '<td' );
			$imdb_page_releaseinfo_td_close_count[$this->imdb_language] = substr_count( strtolower( $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] ), '</td' );

			if ( !preg_match_all( '/\<td\>[\x20]*([^\x3c]+)[\x20]*\<\/td\>[\x20]*\<td\>[\x20]*([^\x3c]+)[\x20]*\<\/td\>/', $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language], $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language] ) ) {
				$this->_setError( 'Protected_Parse_ReleaseInfo_Table_Akas_Error_Number_1_' . $this->imdb_language );
				$this->_setError( '<pre>' . htmlentities( $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] ) . '</pre>' );
				return false;
			}

			if ( $imdb_page_releaseinfo_td_open_count[$this->imdb_language] !== $imdb_page_releaseinfo_td_close_count[$this->imdb_language] ) {
				$this->_setError( 'Protected_Parse_ReleaseInfo_Table_Akas_Error_Number_2_' . $this->imdb_language );
				$this->_setError( '<pre>' . htmlentities( $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] ) . '</pre>' );
				return false;
			}

			if ( !isset( $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][1] ) || ( ( count( $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][1] ) * 2 ) !== $imdb_page_releaseinfo_td_open_count[$this->imdb_language] ) ) {
				if ( !isset( $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][1] ) || ( ( ( count( $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][1] ) + 1 ) * 2 ) !== $imdb_page_releaseinfo_td_open_count[$this->imdb_language] ) ) {
					if ( !isset( $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][1] ) || ( ( ( count( $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][1] ) + 2 ) * 2 ) !== $imdb_page_releaseinfo_td_open_count[$this->imdb_language] ) ) {
						$this->_setError( 'Protected_Parse_ReleaseInfo_Table_Akas_Error_Number_3_' . $this->imdb_language );
						$this->_setError( '<pre>' . htmlentities( $imdb_page_releaseinfo_table_id_akas_html[$this->imdb_language] ) . '</pre>' );
						return false;
					} else {
						$imdb_is_skipped = true;
					}
				} else {
					$imdb_is_skipped = true;
				}
			}

			$imdb_page_releaseinfo_has_brazil_title[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_portugal_title[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_usa_title[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_english_title[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_original_title[$this->imdb_language] = false;

			$imdb_page_releaseinfo_has_brazil_others_title[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_portugal_others_title[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_usa_others_title[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_english_others_title[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_original_others_title[$this->imdb_language] = false;

			$imdb_page_releaseinfo_has_brazil_titles[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_portugal_titles[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_usa_titles[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_english_titles[$this->imdb_language] = false;
			$imdb_page_releaseinfo_has_original_titles[$this->imdb_language] = false;

			foreach ( $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][1] as $key[$this->imdb_language] => $country[$this->imdb_language] ) {
				if ( in_array( $country[$this->imdb_language], array(
						'Argentina', 'Azerbaijan',
						'Belgium', 'Bulgaria',
						'Canada', 'Chile', 'Croatia', 'Czech Republic',
						'Denmark',
						'Estonia',
						'Finland', 'France',
						'Georgia', 'Germany', 'Greece',
						'Hungary',
						'Iceland', 'Italy',
						'Japan',
						'Lithuania',
						'Mexico',
						'Netherlands', 'Norway',
						'Panama', 'Peru', 'Poland',
						'Romania', 'Russia',
						'Serbia', 'Slovenia', 'Spain', 'Sweden',
						'Taiwan',
						'Ukraine', 'Uruguay',
						'Venezuela', 'Vietnam'
					) ) ) {
					continue;
				}
				if ( in_array( $country[$this->imdb_language], array(
						'Bulgaria (Bulgarian title)', 'Canada (French title)', 'China (Mandarin title)',
						'Germany (alternative title)', 'Greece (DVD title)', 'Greece (transliterated ISO-LATIN-1 title)',
						'Hong Kong (Cantonese title)', 'Italy (alternative title)', 'Italy (dvd title)', 'Luxembourg (French title)',
						'Mexico (informal title)',
						'Romania (long title)',
						'Turkey (Turkish title)', 'Turkey (DVD title) (Turkish title)', 'Greece (video title)',
					) ) ) {
					continue;
				}
				if ( in_array( $country[$this->imdb_language], array(
						'Lithuania (3-D version)', 'Turkey (3-D version)',
					) ) ) {
					continue;
				}
				if ( in_array( $country[$this->imdb_language], array(
						'USA (fake working title)', 'Turkey (3-D version)',
					) ) ) {
					continue;
				}
				if ( in_array( $country[$this->imdb_language], array( 'Israel (alternative title) (Hebrew title)', 'Israel (Hebrew title)', 'Spain (alternative spelling)' ) ) ) {
					continue;
				}
				if ( in_array( $country[$this->imdb_language], array( 'UK (DVD title)', 'USA (alternative title)' ) ) ) {
					continue;
				}
				if ( $country[$this->imdb_language] === 'Brazil' ) {
					if ( isset( $this->raw->info_brazil_title[$this->imdb_language] ) ) {
						$imdb_page_releaseinfo_brazil_titles[$this->imdb_language][] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
						$imdb_page_releaseinfo_has_brazil_titles[$this->imdb_language] = true;
						if ( $this->id === 'tt0087727' ) {
							$this->raw->info_brazil_title[$this->imdb_language] = 'Braddock: O Super Comando';
							continue;
						}
						if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
							$imdb_is_skipped = true;
							continue;
						}
						$this->_setError( 'Protected_Parse_ReleaseInfo_Brazil_Duplicated_' . $this->imdb_language );
						return false;
					}
					$this->raw->info_brazil_title[$this->imdb_language] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
					$imdb_page_releaseinfo_has_brazil_title[$this->imdb_language] = true;
					continue;
				}
				if ( $country[$this->imdb_language] === 'Portugal' ) {
					if ( isset( $this->raw->info_portugal_title[$this->imdb_language] ) ) {
						$imdb_page_releaseinfo_portugal_titles[$this->imdb_language][] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
						$imdb_page_releaseinfo_has_portugal_titles[$this->imdb_language] = true;
						if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
							$imdb_is_skipped = true;
							continue;
						}
						$this->_setError( 'Protected_Parse_ReleaseInfo_Portugal_Duplicated_' . $this->imdb_language );
						return false;
					}
					$this->raw->info_portugal_title[$this->imdb_language] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
					$imdb_page_releaseinfo_has_portugal_title[$this->imdb_language] = true;
					continue;
				}
				if ( $country[$this->imdb_language] === '(original title)' ) {
					if ( isset( $imdb_page_releaseinfo_original_title[$this->imdb_language] ) ) {
						$imdb_page_releaseinfo_original_titles[$this->imdb_language][] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
						$imdb_page_releaseinfo_has_original_titles[$this->imdb_language] = true;
						if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
							$imdb_is_skipped = true;
							continue;
						}
						$this->_setError( 'Protected_Parse_ReleaseInfo_Original_Duplicated_' . $this->imdb_language );
						return false;
					}
					$imdb_page_releaseinfo_original_title[$this->imdb_language] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
					$imdb_page_releaseinfo_has_original_title[$this->imdb_language] = true;
					continue;
				}
				if ( in_array( $country[$this->imdb_language], array( 'USA', 'USA (working title)', 'USA (informal title)', 'USA (short title)', 'USA (DVD title)', 'USA (English title) (informal title)', 'Jamaica (English title)', 'Ireland (English title)', 'Singapore (alternative title) (English title)', 'Philippines (English title)' ) ) ) {
					if ( isset( $imdb_page_releaseinfo_usa_title[$this->imdb_language] ) ) {
						$imdb_page_releaseinfo_usa_titles[$this->imdb_language][] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
						$imdb_page_releaseinfo_has_usa_titles[$this->imdb_language] = true;
						if ( $this->id === 'tt1631867' ) {
							$imdb_page_releaseinfo_english_title[$this->imdb_language] = 'Live Die Repeat: Edge of Tomorrow';
							$imdb_page_releaseinfo_usa_title[$this->imdb_language] = 'Live Die Repeat: Edge of Tomorrow';
							continue;
						}
						if ( $this->id === 'tt0865556' ) {
							$imdb_page_releaseinfo_english_title[$this->imdb_language] = 'The Forbidden Kingdom';
							$imdb_page_releaseinfo_usa_title[$this->imdb_language] = 'Jackie Chan/Jet Li Project';
							continue;
						}
						if ( $this->id === 'tt0062622' ) {
							$imdb_page_releaseinfo_english_title[$this->imdb_language] = 'Two Thousand and One: A Space Odyssey';
							$imdb_page_releaseinfo_usa_title[$this->imdb_language] = 'How the Solar System Was Won';
							continue;
						}
						if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
							$imdb_is_skipped = true;
							continue;
						}
						$this->_setError( 'Protected_Parse_ReleaseInfo_USA_Duplicated_' . $this->imdb_language );
						return false;
					}
					$imdb_page_releaseinfo_usa_title[$this->imdb_language] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
					$imdb_page_releaseinfo_has_usa_title[$this->imdb_language] = true;
					continue;
				}
				if ( in_array( $country[$this->imdb_language], array( 'World-wide (alternative title) (English title)', 'World-wide (English title)', 'Japan (English title)', 'World-wide (English title) (theatrical title)', 'Hong Kong (English title) (literal title)', 'Japan (English title) (alternative transliteration)', 'Canada (festival title) (English title)', 'Malaysia (working title) (English title)' ) ) ) {
					if ( isset( $imdb_page_releaseinfo_english_title[$this->imdb_language] ) ) {
						$imdb_page_releaseinfo_english_titles[$this->imdb_language][] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
						$imdb_page_releaseinfo_has_english_titles[$this->imdb_language] = true;
						if ( $this->id === 'tt1245112' ) {
							$imdb_page_releaseinfo_english_title[$this->imdb_language] = 'REC 2: Fear Revisited';
							continue;
						}
						if ( $this->id === 'tt1631867' ) {
							$imdb_page_releaseinfo_english_title[$this->imdb_language] = 'Live Die Repeat: Edge of Tomorrow';
							$imdb_page_releaseinfo_usa_title[$this->imdb_language] = 'Live Die Repeat: Edge of Tomorrow';
							continue;
						}
						if ( $this->id === 'tt0865556' ) {
							$imdb_page_releaseinfo_english_title[$this->imdb_language] = 'The Forbidden Kingdom';
							$imdb_page_releaseinfo_usa_title[$this->imdb_language] = 'Jackie Chan/Jet Li Project';
							continue;
						}
						if ( mb_strtolower( $imdb_page_releaseinfo_english_title[$this->imdb_language] ) === mb_strtolower( $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]] ) ) {
							continue;
						}
						if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
							$imdb_is_skipped = true;
							continue;
						}
						$this->_setError( 'Protected_Parse_ReleaseInfo_English_Duplicated_' . $this->imdb_language );
						return false;
					}
					$imdb_page_releaseinfo_english_title[$this->imdb_language] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
					$imdb_page_releaseinfo_has_english_title[$this->imdb_language] = true;
					continue;
				}
				if ( in_array( $country[$this->imdb_language], array( 'Australia (TV title)' ) ) ) {
					continue;
				}
				if ( stripos( $country[$this->imdb_language], 'Brazil' ) !== false || stripos( $country[$this->imdb_language], 'Portugal' ) !== false ) {
					if ( in_array( $country[$this->imdb_language], array( 'Brazil (working title)', 'Brazil (DVD title)', 'Brazil (alternative title)', 'Brazil (original subtitled version)' ) ) ) {
						if ( isset( $this->raw->info_brazil_title[$this->imdb_language] ) ) {
							$imdb_page_releaseinfo_brazil_titles[$this->imdb_language][] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
							$imdb_page_releaseinfo_has_brazil_titles[$this->imdb_language] = true;
							if ( $this->id === 'tt1647668' ) {
								$this->raw->info_brazil_title[$this->imdb_language] = 'Um Braço de Um Milhão de Dólares';
								continue;
							}
							if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
								$imdb_is_skipped = true;
								continue;
							}
							$this->_setError( 'Protected_Parse_ReleaseInfo_Brazil_Country_Duplicated_1_' . $this->imdb_language );
							return false;
						}
						$this->raw->info_brazil_title[$this->imdb_language] = $imdb_page_releaseinfo_table_akas_titles_matches[$this->imdb_language][2][$key[$this->imdb_language]];
						$imdb_page_releaseinfo_has_brazil_title[$this->imdb_language] = true;
						continue;
					}
					if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
						$imdb_is_skipped = true;
						continue;
					}
					$this->_setError( 'Protected_Parse_ReleaseInfo_Brazil_Country_Duplicated_2_' . $this->imdb_language );
					return false;
				}
				if ( stripos( $country[$this->imdb_language], 'ENGLISH' ) !== false ) {
					if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
						$imdb_is_skipped = true;
						continue;
					}
					$this->_setError( 'Protected_Parse_ReleaseInfo_English_Country_Duplicated_1_' . $this->imdb_language );
					return false;
				}
				if ( isset( $imdb_bypass ) && $imdb_bypass ) {
					continue;
				}
				if ( isset( $imdb_skip_titles ) && $imdb_skip_titles ) {
					$imdb_is_skipped = true;
					continue;
				}
				$this->_setError( 'Protected_Parse_ReleaseInfo_Default_Country_Duplicated_3_' . $this->imdb_language );
				return false;
			}

			/**
			 * Todos titulos extraídos
			 * 
			 * 1 = Países
			 * 2 = Títulos
			 * 
			 */

		}

		return true;

	}

	protected function _setDirectors() {

		/**
		 * Full Credits - Director
		 */

		if ( ( $imdb_page_fullcredits_directed_by_open_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 class="dataHeaderWithBorder">Directed by&nbsp;</h4>' ) ) === false ) {
			if ( ( $imdb_page_fullcredits_directed_by_open_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 class="dataHeaderWithBorder">Series Directed by&nbsp;</h4>' ) ) === false ) {
			}			
		}

		if ( ( $imdb_page_fullcredits_directed_by_close_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 class="dataHeaderWithBorder">Writing Credits' ) ) === false ) {
			if ( ( $imdb_page_fullcredits_directed_by_close_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 class="dataHeaderWithBorder">Series Writing Credits' ) ) === false ) {
				if ( stripos( $this->imdb_fullcredits_body[$this->imdb_language], 'Writing Credits' ) === false && stripos( $this->imdb_fullcredits_body[$this->imdb_language], 'Series Writing Credits' ) === false ) {
					if ( ( $imdb_page_fullcredits_directed_by_close_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 name="cast" id="cast" class="dataHeaderWithBorder">' ) ) === false ) {
					}
				}
			}
		}

		if ( $imdb_page_fullcredits_directed_by_open_pos[$this->imdb_language] === false || $imdb_page_fullcredits_directed_by_close_pos[$this->imdb_language] === false ) {
			$this->_setError( 'Protected_Parse_ReleaseInfo_Director_Not_Found_' . $this->imdb_language );
			$this->_setError( '<textarea>' . $this->_esc_attr( $this->imdb_fullcredits_body[$this->imdb_language] ) . '</textarea>' );
			return false;
		}

		$imdb_page_fullcredits_directed_by_html[$this->imdb_language] = $this->imdb_fullcredits_body[$this->imdb_language];
		$imdb_page_fullcredits_directed_by_html[$this->imdb_language] = trim( substr( $imdb_page_fullcredits_directed_by_html[$this->imdb_language], $imdb_page_fullcredits_directed_by_open_pos[$this->imdb_language] + 55, $imdb_page_fullcredits_directed_by_close_pos[$this->imdb_language] - $imdb_page_fullcredits_directed_by_open_pos[$this->imdb_language] - 55 ) );
		$imdb_page_fullcredits_directed_by_html[$this->imdb_language] = trim( preg_replace( '/[\x09\x0a\x0b\x0c\x0d]{1,}/', "\x20", $imdb_page_fullcredits_directed_by_html[$this->imdb_language] ) );

		$imdb_page_fullcredits_directed_by_anchors_open_count[$this->imdb_language] = substr_count( strtolower( $imdb_page_fullcredits_directed_by_html[$this->imdb_language] ), '<a' );
		$imdb_page_fullcredits_directed_by_anchors_close_count[$this->imdb_language] = substr_count( strtolower( $imdb_page_fullcredits_directed_by_html[$this->imdb_language] ), '</a>' );

		if ( !$imdb_page_fullcredits_directed_by_anchors_open_count[$this->imdb_language] || !$imdb_page_fullcredits_directed_by_anchors_close_count[$this->imdb_language] || ( $imdb_page_fullcredits_directed_by_anchors_open_count[$this->imdb_language] !== $imdb_page_fullcredits_directed_by_anchors_close_count[$this->imdb_language] ) ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Release Info - Director - 2 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_directed_by_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		if ( $imdb_page_fullcredits_directed_by_anchors_close_count[$this->imdb_language] !== 1 && $imdb_page_fullcredits_directed_by_anchors_close_count[$this->imdb_language] !== 2 ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Release Info - Director - 3 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_directed_by_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		if ( !preg_match_all( '/\<a\b[^\x3e]*\>[\x20]*([^\x20]+[^\x3c]+[^\x20]+)[\x20]*\<\/a\>/', $imdb_page_fullcredits_directed_by_html[$this->imdb_language], $imdb_directed_matches[$this->imdb_language] ) ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Release Info - Director - 4 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_directed_by_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		if ( !isset( $imdb_directed_matches[$this->imdb_language][1] ) ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Release Info - Director - 5 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_directed_by_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		if ( count( $imdb_directed_matches[$this->imdb_language][1] ) !== 1 && count( $imdb_directed_matches[$this->imdb_language][1] ) !== 2 ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Release Info - Director - 6 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_directed_by_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		$imdb_directors[$this->imdb_language] = array_unique( $imdb_directed_matches[$this->imdb_language][1] );
		$imdb_directors_str[$this->imdb_language] = implode( ', ', $imdb_directors[$this->imdb_language] );
		$imdb_director[$this->imdb_language] = $imdb_directed_matches[$this->imdb_language][1][0];

	}

	protected function _setWriters() {

		/**
		 * Full Credits - Writers
		 */

		if ( ( $imdb_page_fullcredits_writers_open_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 class="dataHeaderWithBorder">Writing Credits' ) ) === false ) {
			if ( ( $imdb_page_fullcredits_writers_open_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 class="dataHeaderWithBorder">Series Writing Credits' ) ) === false ) {
			}
		}

		if ( ( $imdb_page_fullcredits_writers_close_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 name="cast" id="cast" class="dataHeaderWithBorder">' ) ) === false ) {
			if ( ( $imdb_page_fullcredits_writers_close_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 name="cast" id="cast" class="dataHeaderWithBorder">' ) ) === false ) {
			}
		}

		if ( !( stripos( $this->imdb_fullcredits_body[$this->imdb_language], 'Writing Credits' ) === false && stripos( $this->imdb_fullcredits_body[$this->imdb_language], 'Series Writing Credits' ) === false ) ) {

			if ( $imdb_page_fullcredits_writers_open_pos[$this->imdb_language] === false || $imdb_page_fullcredits_writers_close_pos[$this->imdb_language] === false ) {
				$this->_setError( 'Error' . __LINE__ );
				#echo '<h2>Imdb - Full Credits - Writers - 1 - ' . $url . '</h2>';
				return false;
			}

			$imdb_page_fullcredits_writers_html[$this->imdb_language] = $this->imdb_fullcredits_body[$this->imdb_language];
			$imdb_page_fullcredits_writers_html[$this->imdb_language] = trim( substr( $imdb_page_fullcredits_writers_html[$this->imdb_language], $imdb_page_fullcredits_writers_open_pos[$this->imdb_language] + 48 ) );
			$imdb_page_fullcredits_writers_html[$this->imdb_language] = trim( substr( $imdb_page_fullcredits_writers_html[$this->imdb_language], 0, $imdb_page_fullcredits_writers_close_pos[$this->imdb_language] - $imdb_page_fullcredits_writers_open_pos[$this->imdb_language] ) );
			$imdb_page_fullcredits_writers_html[$this->imdb_language] = trim( preg_replace( '/[\x09\x0a\x0b\x0c\x0d]{1,}/', "\x20", $imdb_page_fullcredits_writers_html[$this->imdb_language] ) );
			$imdb_page_fullcredits_writers_html[$this->imdb_language] = trim( str_replace( '<a href="http://www.imdb.com/help/show_leaf?wga&ref_=wga" >WGA</a>', '', $imdb_page_fullcredits_writers_html[$this->imdb_language] ) );

			$imdb_page_fullcredits_writers_anchors_open_count[$this->imdb_language] = substr_count( strtolower( $imdb_page_fullcredits_writers_html[$this->imdb_language] ), '<a' );
			$imdb_page_fullcredits_writers_anchors_close_count[$this->imdb_language] = substr_count( strtolower( $imdb_page_fullcredits_writers_html[$this->imdb_language] ), '</a>' );

			if ( !$imdb_page_fullcredits_writers_anchors_open_count[$this->imdb_language] || !$imdb_page_fullcredits_writers_anchors_close_count[$this->imdb_language] || ( $imdb_page_fullcredits_writers_anchors_open_count[$this->imdb_language] !== $imdb_page_fullcredits_writers_anchors_close_count[$this->imdb_language] ) ) {
				$this->_setError( 'Error' . __LINE__ );
				#echo '<h2>Imdb - Full Credits - Writers - 2 - ' . $url . '</h2>';
				#echo '<pre>' . htmlentities( $imdb_page_fullcredits_writers_html[$this->imdb_language] ) . '</pre>';
				return false;
			}

			if ( !preg_match_all( '/\<a\b[^\x3e]*\>[\x20]*([^\x20]+[^\x3c]+[^\x20]+)[\x20]*\<\/a\>/', $imdb_page_fullcredits_writers_html[$this->imdb_language], $imdb_page_fullcredits_writers_matches[$this->imdb_language] ) ) {
				$this->_setError( 'Error' . __LINE__ );
				#echo '<h2>Imdb - Full Credits - Writers - 3 - ' . $url . '</h2>';
				#echo '<pre>' . htmlentities( $imdb_page_fullcredits_writers_html[$this->imdb_language] ) . '</pre>';
				return false;
			}

			if ( !isset( $imdb_page_fullcredits_writers_matches[$this->imdb_language][1] ) ) {
				$this->_setError( 'Error' . __LINE__ );
				#echo '<h2>Imdb - Full Credits - Writers - 4 - ' . $url . '</h2>';
				#echo '<pre>' . htmlentities( $imdb_page_fullcredits_writers_html[$this->imdb_language] ) . '</pre>';
				return false;
			}

			$this->raw->writers[$this->imdb_language] = array_unique( $imdb_page_fullcredits_writers_matches[$this->imdb_language][1] );
			$this->raw->writers_str[$this->imdb_language] = implode( ', ', $this->raw->writers[$this->imdb_language] );
			$this->raw->writer[$this->imdb_language] = $imdb_page_fullcredits_writers_matches[$this->imdb_language][1][0];

		}

		return true;

	}

	protected function _setActors() {

		/**
		 * Imdb - Full Credits - Actors
		 */

		if ( ( $imdb_page_fullcredits_actors_open_pos[$this->imdb_language] = strpos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 name="cast" id="cast" class="dataHeaderWithBorder">' ) ) === false ) {
		}

		if ( ( $imdb_page_fullcredits_actors_close_pos[$this->imdb_language] = strpos( $this->imdb_fullcredits_body[$this->imdb_language], '<div class="full_cast form-box">' ) ) === false ) {
			if ( ( $imdb_page_fullcredits_actors_close_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 class="dataHeaderWithBorder">Produced by&nbsp;</h4>' ) ) === false ) {
				if ( ( $imdb_page_fullcredits_actors_close_pos[$this->imdb_language] = stripos( $this->imdb_fullcredits_body[$this->imdb_language], '<h4 class="dataHeaderWithBorder">Produced by </h4>' ) ) === false ) {
				}
			}
		}

		if ( $imdb_page_fullcredits_actors_open_pos[$this->imdb_language] === false || $imdb_page_fullcredits_actors_close_pos[$this->imdb_language] === false ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Full Credits - Actors - 1 - ' . $url . '</h2>';
			#echo '<pre>'; var_dump( $this->id ); echo '</pre>';
			#echo '<textarea>' . esc_attr( $this->imdb_fullcredits_body[$this->imdb_language] ) . '</textarea>';
			return false;
		}

		$imdb_page_fullcredits_actors_html[$this->imdb_language] = $this->imdb_fullcredits_body[$this->imdb_language];
		$imdb_page_fullcredits_actors_html[$this->imdb_language] = trim( substr( $imdb_page_fullcredits_actors_html[$this->imdb_language], $imdb_page_fullcredits_actors_open_pos[$this->imdb_language] + 55 ) );
		$imdb_page_fullcredits_actors_html[$this->imdb_language] = trim( substr( $imdb_page_fullcredits_actors_html[$this->imdb_language], 0, $imdb_page_fullcredits_actors_close_pos[$this->imdb_language] - $imdb_page_fullcredits_actors_open_pos[$this->imdb_language] ) );
		$imdb_page_fullcredits_actors_html[$this->imdb_language] = trim( preg_replace( '/[\x09\x0a\x0b\x0c\x0d]{1,}/', "\x20", $imdb_page_fullcredits_actors_html[$this->imdb_language] ) );

		$imdb_page_fullcredits_actors_anchors_itemprop_name_count[$this->imdb_language] = substr_count( strtolower( $imdb_page_fullcredits_actors_html[$this->imdb_language] ), 'itemprop="name"' );

		if ( !$imdb_page_fullcredits_actors_anchors_itemprop_name_count[$this->imdb_language] ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Full Credits - Actors - 2 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_actors_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		if ( !preg_match_all( '/\<span class\=\"itemprop\" itemprop\=\"name\"\>[\x20]*([^\x20]+[^\x3c]+[^\x20]+)[\x20]*\<\/span\>/', $imdb_page_fullcredits_actors_html[$this->imdb_language], $imdb_page_fullcredits_actors_matches[$this->imdb_language] ) ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Full Credits - Actors - 3 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_actors_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		if ( !isset( $imdb_page_fullcredits_actors_matches[$this->imdb_language][1] ) ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Full Credits - Actors - 4 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_actors_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		if ( count( $imdb_page_fullcredits_actors_matches[$this->imdb_language][1] ) !== $imdb_page_fullcredits_actors_anchors_itemprop_name_count[$this->imdb_language] ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Full Credits - Actors - 5 - ' . $url . '</h2>';
			#echo '<pre>' . htmlentities( $imdb_page_fullcredits_actors_html[$this->imdb_language] ) . '</pre>';
			return false;
		}

		$this->raw->actors[$this->imdb_language] = array_unique( $imdb_page_fullcredits_actors_matches[$this->imdb_language][1] );
		$this->raw->actors_str[$this->imdb_language] = implode( ', ', $this->raw->actors[$this->imdb_language] );
		$this->raw->actor[$this->imdb_language] = $this->raw->actors[$this->imdb_language][1][0];

		return true;

	}

	protected function _setDescriptions() {

		/**
		 * Plot Summary
		 */

		$imdb_page_plotsummary_open_pos[$this->imdb_language] = strpos( $this->imdb_plotsummary_body[$this->imdb_language], '<ul class="zebraList">' );
		$imdb_page_plotsummary_close_pos[$this->imdb_language] = strpos( $this->imdb_plotsummary_body[$this->imdb_language], '<div class="article" id="see_also">' );

		if ( $imdb_page_plotsummary_open_pos[$this->imdb_language] === false || $imdb_page_plotsummary_close_pos[$this->imdb_language] === false ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Plot Summary - 1 - ' . $url . '</h2>';
			return false;
		}

		$imdb_page_plotsummary_html[$this->imdb_language] = $this->imdb_plotsummary_body[$this->imdb_language];
		$imdb_page_plotsummary_html[$this->imdb_language] = trim( substr( $imdb_page_plotsummary_html[$this->imdb_language], $imdb_page_plotsummary_open_pos[$this->imdb_language] + 22 ) );
		$imdb_page_plotsummary_html[$this->imdb_language] = trim( substr( $imdb_page_plotsummary_html[$this->imdb_language], 0, $imdb_page_plotsummary_close_pos[$this->imdb_language] - $imdb_page_plotsummary_open_pos[$this->imdb_language] ) );
		$imdb_page_plotsummary_html[$this->imdb_language] = trim( preg_replace( '/[\x09\x0a\x0b\x0c\x0d]{1,}/', "\x20", $imdb_page_plotsummary_html[$this->imdb_language] ) );
		$imdb_page_plotsummary_html[$this->imdb_language] = trim( str_replace( '</p>', '</p>' . "\x0a", $imdb_page_plotsummary_html[$this->imdb_language] ) );

		if ( !preg_match_all( '/\<p class\=\"plotSummary\"\>[\x20]*([^\x20]+.+[^\x20]+)[\x20]*\<\/p\>/', $imdb_page_plotsummary_html[$this->imdb_language], $imdb_page_plotsummary_matches[$this->imdb_language] ) ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Plot Summary - 2 - ' . $url . '</h2>';
			return false;
		}

		if ( !isset( $imdb_page_plotsummary_matches[$this->imdb_language][1] ) ) {
			$this->_setError( 'Error' . __LINE__ );
			#echo '<h2>Imdb - Plot Summary - 3 - ' . $url . '</h2>';
			return false;
		}

		$this->raw->descriptions[$this->imdb_language] = $imdb_page_plotsummary_matches[$this->imdb_language][1];

		foreach ( $this->raw->descriptions[$this->imdb_language] as $imdb_description_tmp[$this->imdb_language] ) {
			if ( strpos( $imdb_description_tmp[$this->imdb_language], "\x0d" ) !== false || strpos( $imdb_description_tmp[$this->imdb_language], "\x0a" ) !== false ) {
				$this->_setError( 'Error' . __LINE__ );
				#echo '<h2>Imdb - Plot Summary - ODOA - ' . $url . '</h2>';
				return false;
			}
			if ( stripos( $imdb_description_tmp[$this->imdb_language], '<p' ) !== false ) {
				$this->_setError( 'Error' . __LINE__ );
				#echo '<h2>Imdb - Plot Summary - &lt;p&gt; - ' . $url . '</h2>';
				return false;
			}
		}

		return true;

	}

}

?>
