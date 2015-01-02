<?php

class Decipher {

	/**
	 * Input
	 * @var string
	 */
	protected $input;

	/**
	 * Current
	 * @var string
	 */
	protected $current;

	/**
	 * Length
	 * @var int
	 */
	protected $length;

	/**
	 * Compressed format
	 * @var string
	 */
	protected $compressed_format;

	/**
	 * Compressed format
	 * @var string
	 */
	protected $image_format;

	/**
	 * [$file description]
	 * @var [type]
	 */
	protected $extracted_files;

	/**
	 * Output
	 * @var string
	 */
	protected $output;

	/**
	 * Values
	 * @var array
	 */
	protected $values = array();

	/**
	 * [$whiles description]
	 * @var [type]
	 */
	protected $whiles = 0;

	/**
	 * Error
	 * @var string
	 */
	protected $error;

	public function __construct( $input = null ) {

		isset( $input ) && $this->setInput( $input );

	}

	public function setInput( $input = null ) {

		$this->_setInput( $input );

		return $this;

	}

	public function run( $input = null ) {

		$this->_run( $input );

		return $this;

	}

	public function hasError() {
		return !isset( $this->error ) || $this->error !== false;
	}

	public function getError() {
		return isset( $this->error ) ? ( $this->error === true ? null : $this->error ) : 'Error';
	}

	public function getResult() {
		return $this->output;
	}

	protected function _setInput( $input = null ) {

		$this->input = $input;

		return true;

	}

	protected function _run( $input = null ) {

		isset( $input ) && $this->setInput( $input );

		if ( !isset( $this->input ) ) {
			return false;
		}

		return $this->___run();

	}

	protected function setError( $error ) {

		$this->error = $error;

		return true;

	}

	private function ___run() {

		$this->current = $this->input;
		$this->values[] = $this->current;
		$this->length = strlen( $this->current );

		while ( true ) {

			if ( $this->whiles === 9 ) {
				break;
			}

			if ( $this->isCaesarCipherWWW() ) {
				if ( $this->decodeCaesarCipherWWW() ) {
					if ( $this->isWWW() ) {
						if ( $this->isCyberLockerWWW() ) {
						 	if ( !$this->isFileSetError( basename( $this->current ), $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR, 1 ) ) {
						 		return false;
						 	}
							if ( $this->isCompressedFile() ) {
								if ( $this->compressed_format === 'RAR' ) {
									if ( $this->extractRARFile() ) {
										$extracted_files = current( $this->extracted_files );
										if ( !$this->isFileSetError( current( $extracted_files ), $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR, 2 ) ) {
											return false;
										}
										if ( $this->isImageFile() ) {
											if ( $this->image_format === 'PNG' ) {
												echo 'PE-ENE-GE';
												var_dump( $this );
												return;
											}
										}
									}
								}
							}
						}
					}
				}
			} elseif ( $this->isBase64() ) {

				var_dump( $this );

			}

			$this->whiles++;

		}

		$this->setError( 'Falha geral' );

		return false;

	}

	protected function currentIsFile() {

			if ( !isset( $this->current ) || !is_file( $this->current ) ) {
				return false;
			}

			$this->current = realpath( $this->current );

			return true;

	}

	protected function isCaesarCipherWWW() {

		if ( $this->length < 4 ) {
			return false;
		}

		return $this->current[0] === $this->current[1] && $this->current[1] && $this->current[2] && $this->current[3] === '.';

	}

	protected function decodeCaesarCipherWWW() {

		$diff = ord( 'w' ) - ord( $this->current[0] );
		$shift = 26 - $diff;
		$word = '';

		for ( $i = 0; $i < $this->length; $i++ ) {

			if ( $this->current[$i] >= 'a' && $this->current[$i] <= 'z' ) {

				$ord = ord( $this->current[$i] );

				if ( $ord + $diff > 122 ) {
					$word .= chr( $ord - $shift );
				} else {
					$word .= chr( $ord + $diff );
				}

			} elseif ( $this->current[$i] >= 'A' && $this->current[$i] <= 'Z' ) {

				$ord = ord( $this->current[$i] );

				if ( $ord + $diff > 90 ) {
					$word .= chr( $ord - $shift );
				} else {
					$word .= chr( $ord + $diff );
				}

			} else {

				$word .= $this->current[$i];
				continue;

			}

		}

		$this->current = $word;
		$this->values[] = $this->current;

		return true;

	}

	protected function isBase64() {

		static $pattern;

		$pattern = $pattern !== null ? $pattern : '/^([A-Za-z0-9\+\/]{4})*([A-Za-z0-9\+\/]{4}|[A-Za-z0-9\+\/]{3}\=|[A-Za-z0-9\+\/]{2}\=\=)$/';

		if ( !preg_match( $pattern, $this->current ) ) {
			return false;
		}

		$this->current = base64_decode( $this->current );
		$this->values[] = $this->current;

		return true;

	}

	protected function isWWW() {

		if ( ( $first_four = substr( $this->current, 0, 4 ) ) === 'www.' || $first_four === 'WWW.' ) {
			$this->current = 'http://' . $this->current;
			return true;
		}

		if ( substr( $this->current, 0, 7 ) === 'http://' || substr( $this->current, 0, 8 ) === 'https://' ) {
			return true;
		}

		return false;

	}

	protected function isCyberLockerWWW() {

		static $needles;

		$needles = $needles !== null ? $needles : array(
			'http://www.uploadable.ch',
		);

		foreach ( $needles as $needle ) {
			if ( strpos( $this->current, $needle ) === 0 ) {
				return true;
			}
		}

		return false;

	}

	protected function getFormatSetError( $level ) {

		static $formats;

		$formats = $formats !== null ? $formats : array(
			1 => 'Coloque o arquivo "%s" na pasta "%s"',
			2 => 'O arquivo extraído "%s" não existe na pasta "%s"',
		);

		return isset( $formats[$level] ) ? $formats[$level] : '"%s" "%s"';

	}

	protected function isFileSetError( $file, $folder, $level ) {

		if ( !is_file( $folder . DIRECTORY_SEPARATOR . $file ) ) {
			$this->setError( sprintf( $this->getFormatSetError( $level ), basename( $file ), $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR )  );
			return false;
		}

		$this->current = realpath( $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . basename( $file ) );
		$this->values[] = $this->current;

		return true;

	}

	protected function isCompressedFile() {

		if ( !$this->currentIsFile() ) {
			return false;
		}

		static $signatures;

		$signatures = $signatures !== null ? $signatures : array(
			"\x52\x61\x72\x21\x1a\x07\x01\x00" => 'RAR',
			"\x52\x61\x72\x21\x1a\x07\x00" => 'RAR',
		);

		$read = file_get_contents( $this->current, true, null, 0, 8 );

		foreach ( $signatures as $value => $format ) {
			if ( substr( $read, 0, strlen( $value ) ) === $value ) {
				$this->compressed_format = $format;
				return true;
			}
		}

		return false;

	}

	protected function isImageFile() {

		if ( !$this->currentIsFile() ) {
			return false;
		}

		static $signatures;

		$signatures = $signatures !== null ? $signatures : array(
			"\x89\x50\x4e\x47\x0d\x0a\x1a\x0a" => 'PNG',
			"\xff\xd8\xff" => 'JPG',
		);

		$read = file_get_contents( $this->current, true, null, 0, 8 );

		foreach ( $signatures as $value => $format ) {
			if ( substr( $read, 0, strlen( $value ) ) === $value ) {
				$this->image_format = $format;
				return true;
			}
		}

		return false;

	}

	protected function extractRARFile() {

		if ( is_file( '/usr/bin/unrar' ) ) {
			return $this->unrarFile();
		}

	}

	protected function unrarFile() {

		if ( !$this->currentIsFile() ) {
			return false;
		}

		chdir( $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR );

		$command = '"/usr/bin/unrar" e -y "' . $this->current . '"';

		$exec = exec( $command, $lines, $errorlevel );

		$files = array();

		$lines = is_array( $lines ) ? $lines : array();

		foreach ( $lines as $line ) {
			if ( preg_match( '/^Extracting[\x20]+([^\x20]+).+\d+%.+OK/', $line, $match ) ) {
				$files[] = $match[1];
			}
		}

		if ( $files ) {
			$this->extracted_files[] = $files;
		}

		return true;

	}

}

?>
