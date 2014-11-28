<?php

class Regex {

	const ALL = 2;
	const HREF = 3;
	const SRC = 4;
	const DATAHREF = 5;
	const DATASRC = 6;

	const HREFALL = -3;
	const SRCALL = -4;
	const DATAHREFALL = -5;
	const DATASRCALL = -6;

	/**
	 * [$input description]
	 * @var [type]
	 */
	protected $input;

	/**
	 * [$current description]
	 * @var [type]
	 */
	protected $current;

	/**
	 * [__construct description]
	 * @param [type] $subject [description]
	 */
	public function __construct( $subject = null ) {

		$this->input = new StdClass();
		$this->current = new StdClass();

		isset( $subject ) && $this->setSubject( $subject );

	}

	/**
	 * [setSubject description]
	 * @param [type] $text [description]
	 */
	public function setSubject( $text ) {

		$this->_setSubject( $text );

		return $this;

	}

	/**
	 * [setPattern description]
	 * @param [type] $const [description]
	 */
	public function setPattern( $const ) {

		$this->_setPattern( $const );

		return $this;

	}

	/**
	 * [setDelimiters description]
	 * @param [type] $flags [description]
	 */
	public function setDelimiters( $delimiters ) {

		$this->_setDelimiters( $delimiters );

		return $this;

	}

	/**
	 * [setFlags description]
	 * @param [type] $flags [description]
	 */
	public function setFlags( $flags ) {

		$this->_setFlags( $flags );

		return $this;

	}

	/**
	 * [run description]
	 * @return [type] [description]
	 */
	public function run( $subject = null, $const = null, $flags = null ) {

		isset( $subject ) && $this->setSubject( $subject );
		isset( $const ) && $this->setPattern( $const );
		isset( $flags ) && $this->setFlags( $flags );

		if ( !isset( $this->current->subject ) ) {
			return false;
		}

		if ( !isset( $this->current->pattern ) ) {
			return false;
		}

		return $this->_run();

	}

	/**
	 * [getMatches description]
	 * @return [type] [description]
	 */
	public function getMatches() {
		return isset( $this->current->results ) && is_array( $this->current->results ) ? $this->current->results : array();
	}

	/**
	 * [_setSubject description]
	 * @param [type] $text [description]
	 */
	protected function _setSubject( $text ) {

		$this->input->subject = $text;
		$this->current->subject = $text;

		return $text;

	}

	/**
	 * [_setPattern description]
	 * @param [type] $text [description]
	 */
	protected function _setPattern( $const ) {

		$this->input->const = $const;
		$this->current->const = $const;
		$this->current->pattern = $this->_getPatternFromConst();

		return isset( $this->current->pattern );

	}

	/**
	 * [_setDelimiters description]
	 * @param [type] $delimiters [description]
	 */
	protected function _setDelimiters( $delimiters ) {

		$this->input->delimiters = $delimiters;
		$this->current->delimiters = $delimiters;
		$this->current->delimiters = $this->_getDelimitersFromDelimiters();

		return isset( $this->current->delimiters );

	}

	/**
	 * [_setFlags description]
	 * @param [type] $flags [description]
	 */
	protected function _setFlags( $flags ) {

		$this->input->flags = $flags;
		$this->current->flags = $flags;
		$this->current->flags = $this->_getFlagsFromFlags();

		return isset( $this->current->flags );

	}

	/**
	 * [_getDelimitersFromDelimiters description]
	 * @return [type] [description]
	 */
	protected function _getDelimitersFromDelimiters() {

		if ( $this->current->delimiters === 1 || $this->current->delimiters === true || $this->current->delimiters === 'i' || $this->current->delimiters === 'I' ) {
			return true;
		}

		return is_string( $this->current->delimiters ) ? $this->current->delimiters : null;

	}

	/**
	 * [_getFlagsFromFlags description]
	 * @return [type] [description]
	 */
	protected function _getFlagsFromFlags() {

		if ( $this->current->flags === PHP_INT_MAX ) {
			return PHP_INT_MAX;
		}

		if ( $this->current->flags === Regex::ALL ) {
			return Regex::ALL;
		}

		return is_string( $this->current->flags ) ? $this->current->flags : null;

	}

	/**
	 * [_getPatternFromConst description]
	 * @return [type] [description]
	 */
	protected function _getPatternFromConst() {

		if ( !isset( $this->current->const ) ) {
			return null;
		}

		static $constants;

		$constants = $constants !== null ? $constants : array(
			Regex::HREF => '/\<a\b[^\x3e]*(?!\a)href\=(?:\x22([^\x22]+)\x22|\x27([^\x27]+)\x27|([^\x20\x22\x27\x3c\x3e]+))/',
			Regex::SRC => '/\<img\b[^\x3e]*src\=(?:\x22([^\x22]+)\x22|\x27([^\x27]+)\x27|([^\x20\x22\x27\x3c\x3e]+))/',
			Regex::DATAHREF => '/\<a\b[^\x3e]*data\-href\=(?:\x22([^\x22]+)\x22|\x27([^\x27]+)\x27|([^\x20\x22\x27\x3c\x3e]+))/',
			Regex::DATASRC => '/\<img\b[^\x3e]*data\-src\=(?:\x22([^\x22]+)\x22|\x27([^\x27]+)\x27|([^\x20\x22\x27\x3c\x3e]+))/',
			Regex::SRCALL => '/\<img\b[^\x3e]*(?:data\-)?src\=(?:\x22([^\x22]+)\x22|\x27([^\x27]+)\x27|([^\x20\x22\x27\x3c\x3e]+))(?:[^\x3e]*(?:data\-)?src\=(?:\x22([^\x22]+)\x22|\x27([^\x27]+)\x27|([^\x20\x22\x27\x3c\x3e]+)))?/',
		);

		return isset( $constants[$this->current->const] ) ? $constants[$this->current->const] : null;

	}

	/**
	 * [_run description]
	 * @return [type] [description]
	 */
	protected function _run() {

		if ( !isset( $this->current->pattern ) ) {
			return false;
		}

		if ( $this->current->delimiters === true ) {
			$this->current->pattern .= 'i';
		}

		if ( $this->current->flags === Regex::ALL ) {
			$this->setPattern( Regex::SRCALL );
		} elseif ( $this->current->flags !== PHP_INT_MAX ) {
			if ( $this->current->const === self::HREF ) {
				$this->current->subject = str_replace( 'data-href', '_____', $this->current->subject );
			} elseif ( $this->current->const === self::SRC ) {
				$this->current->subject = str_replace( 'data-src', '_____', $this->current->subject );
			}
		}

		if ( preg_match_all( $this->current->pattern, $this->current->subject, $this->current->matches ) === false ) {
			return false;
		}

		var_dump( $this->current );

		if ( !$this->_setResults() ) {
			return false;
		}

		return isset( $this->current->results ) && !empty( $this->current->results );

	}

	/**
	 * [_setResults description]
	 */
	protected function _setResults() {

		if ( !isset( $this->current->matches ) || empty( $this->current->matches ) ) {
			return false;
		}

		$this->current->results = $this->current->matches[1];

		$count = count( $this->current->matches ) - 1;

		for ( $i = 1; $i <= $count; $i++ ) {
			foreach ( $this->current->matches[$i] as $key => $value ) {
				if ( $this->current->results[$key] !== '' ) {
					continue;
				}
				$this->current->results[$key] = $value;
				continue;
			}
		}

		return true;

	}

	/**
	 * [getHref description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	public static function getHref( $subject = null, $delimiters = null, $flags = null ) {
		return Regex::_get( $subject, REGEX::HREF, $delimiters, $flags );
	}

	/**
	 * [getDataHref description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	public static function getDataHref( $subject = null, $delimiters = null, $flags = null ) {
		return Regex::_get( $subject, REGEX::DATAHREF, $delimiters, $flags );
	}

	/**
	 * [getSrc description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	public static function getSrc( $subject = null, $delimiters = null, $flags = null ) {
		return Regex::_get( $subject, REGEX::SRC, $delimiters, $flags );
	}

	/**
	 * [getDataSrc description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	public static function getDataSrc( $subject = null, $delimiters = null, $flags = null ) {
		return Regex::_get( $subject, REGEX::SRC, $delimiters, $flags );
	}

	/**
	 * [_get description]
	 * @param  [type] $subject [description]
	 * @param  [type] $const   [description]
	 * @return [type]          [description]
	 */
	protected static function _get( $subject = null, $const = null, $delimiters = null, $flags = null ) {

		/**
		 * Ajustar o parâmetro $flags quando for usado a flag PHP_INT_MAX no lugar do parâmetro $delimiters
		 */

		if ( $delimiters === PHP_INT_MAX ) {

			if ( $flags === null ) {
				$delimiters = null;
				$flags = PHP_INT_MAX;
			} elseif ( $flags === true || $flags === 1 || $flags === 'i' || $flags === 'I' ) {
				$delimiters = true;
				$flags = PHP_INT_MAX;
			}

		} elseif ( $delimiters === Regex::ALL ) {

			if ( $flags === null ) {
				$delimiters = null;
				$flags = Regex::ALL;
			}

		}

		$regex = new Regex();
		$regex->setSubject( $subject );
		$regex->setPattern( $const );
		$regex->setDelimiters( $delimiters );
		$regex->setFlags( $flags );
		$regex->run();

		return $regex->getMatches();

	}


}

?>
