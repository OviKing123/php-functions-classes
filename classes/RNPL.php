<?php

/**
 * Response Now Process Later
 */
class RNPL {

	/**
	 * Callbacks (Uso Futuro)
	 * @var array
	 */
	private $callbacks = array();

	/**
	 * Adiciona 1 callback na lista de callbacks
	 * @param function $callback Uma função ou método de uma classe
	 */
	public function add( $callback = null ) {

		if ( !isset( $callback ) || !is_callable( $callback ) ) {
			return;
		}

		array_push( $this->callbacks, $callback );

	}

	/**
	 * JSON + Callback
	 * @param  array $json Array ou objeto para ser transformado em uma string em formato JSON
	 * @param  function $callback Função ou método de uma classe para ser executada
	 * @param  array $args Argumentos para serem passados para o $callback
	 * @return mixed Retorna NULL se $callback não foi definido ou não pode ser executado, caso contrário retorna o resultado de $callback
	 */
	public static function json_callback( $json = array(), $callback = null, $args = array() ) {

		ob_end_clean();
		ignore_user_abort();
		ob_start();
		header( 'Connection: close' );
		$json && print( json_encode( $json ) );
		header( 'Content-Length: ' . ob_get_length() );
		ob_end_flush();
		flush();

		if ( !isset( $callback ) || !is_callable( $callback ) ) {
			return;
		}

		return call_user_func_array( $callback, $args );

	}

	/**
	 * JSON
	 * @param  array $json Array ou objeto para ser transformado em uma string em formato JSON
	 * @return null Sempre retorna NULL
	 */
	public static function json( $json = array() ) {

		ob_end_clean();
		ignore_user_abort();
		ob_start();
		header( 'Connection: close' );
		$json && print( json_encode( $json ) );
		header( 'Content-Length: ' . ob_get_length() );
		ob_end_flush();
		flush();

		return;

	}

	/**
	 * Callback
	 * @param  function $callback Função ou método de uma classe para ser executada
	 * @param  array $args Argumentos para serem passados para o $callback
	 * @return mixed Retorna NULL se $callback não foi definido ou não pode ser executado, caso contrário retorna o resultado de $callback
	 */
	public static function callback( $callback = null, $args = array() ) {

		ob_end_clean();
		ignore_user_abort();
		ob_start();
		header( 'Connection: close' );
		header( 'Content-Length: ' . ob_get_length() );
		ob_end_flush();
		flush();

		if ( !isset( $callback ) || !is_callable( $callback ) ) {
			return;
		}

		return call_user_func_array( $callback, $args );

	}

	/**
	 * Inline
	 * @return null Sempre retorna NULL
	 */
	public static function inline() {

		ob_end_clean();
		ignore_user_abort();
		ob_start();
		header( 'Connection: close' );
		header( 'Content-Length: ' . ob_get_length() );
		ob_end_flush();
		flush();

		return;

	}

	protected static function after() {}

}

?>
