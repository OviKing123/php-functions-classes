<?php

class DOMDocument2 extends DOMDocument {

	public $recover = true;
	public $strictErrorChecking = false;

	public function __construct( $version = null, $encoding = null ) {
		DOMDocument::__construct( $version, $encoding );
	}

	public function createAttribute( $name ) {
		return DOMDocument::createAttribute( $name );
	}

	public function createAttributeNS( $namespaceURI, $qualifiedName  ) {
		return DOMDocument::createAttributeNS( $namespaceURI, $qualifiedName  );
	}

	public function createCDATASection( $data ) {
		return DOMDocument::createCDATASection( $data );
	}

	public function createComment( $data ) {
		return DOMDocument::createComment( $data );
	}

	public function createDocumentFragment() {
		return DOMDocument::createDocumentFragment();
	}

	public function createElement( $name, $value = null ) {
		return ( $value === null ) ? DOMDocument::createElement( $name ) : DOMDocument::createElement( $name, $value );
	}

	public function createElementNS( $namespaceURI, $qualifiedName, $value = null ) {
		return ( $value === null ) ? DOMDocument::createElementNS( $namespaceURI, $qualifiedName ) : DOMDocument::createElementNS( $namespaceURI, $qualifiedName, $value );
	}

	public function createEntityReference( $name ){
		return DOMDocument::createEntityReference( $name );
	}

	public function createProcessingInstruction( $target, $data = null ) {
		return ( $data === null ) ? DOMDocument::createProcessingInstruction( $target ) : DOMDocument::createProcessingInstruction( $target, $data );
	}

	public function createTextNode( $content ) {
		return DOMDocument::createTextNode( $content );
	}

	public function getElementById( $elementId ) {
		return DOMDocument::getElementById( $elementId );
	}

	public function getElementsByTagName( $name ) {
		return DOMDocument::getElementsByTagName( $name );
	}

	public function getElementsByTagNameNS( $namespaceURI, $localName ) {
		return DOMDocument::getElementsByTagNameNS( $namespaceURI, $localName );
	}

	public function importNode( DOMNode $importedNode, $deep = null ) {
		return ( $deep === null ) ? DOMDocument::importNode( $importedNode ) : DOMDocument::importNode( $importedNode, $deep );
	}

	public function load( $filename, $options = 0 ) {
		return ( $options === null ) ? DOMDocument::load( $filename ) : DOMDocument::load( $filename, $options );
	}

	public function loadHTML( $source, $options = 0 ) {
		libxml_use_internal_errors( true );
		$this->recover = true;
		$this->strictErrorChecking = false;
		return ( $options === null ) ? DOMDocument::loadHTML( $source ) : DOMDocument::loadHTML( $source, $options );
	}

	public function loadHTMLFile( $filename, $options = 0 ) {
		return ( $options === null ) ? DOMDocument::loadHTMLFile( $filename ) : DOMDocument::loadHTMLFile( $filename, $options );
	}

	public function loadXML( $source, $options = 0 ) {
		return ( $options === null ) ? DOMDocument::loadXML( $source ) : DOMDocument::loadXML( $source, $options );
	}

	public function normalizeDocument() {
		DOMDocument::normalizeDocument();
	}

	public function registerNodeClass( $baseclass, $extendedclass ) {
		return DOMDocument::registerNodeClass( $baseclass, $extendedclass );
	}

	public function relaxNGValidate( $filename ) {
		return DOMDocument::relaxNGValidate( $filename );
	}

	public function relaxNGValidateSource( $source ) {
		return DOMDocument::relaxNGValidateSource( $source );
	}

	public function save( $filename, $options = null ) {
		return ( $options === null ) ? DOMDocument::save( $filename ) : DOMDocument::save( $filename, $options );
	}

	public function saveHTML( DOMNode $node = null ) {
		return ( $node === null ) ? DOMDocument::saveHTML() : DOMDocument::saveHTML( $node );
	}

	public function saveHTMLFile( $filename ) {
		return DOMDocument::saveHTMLFile( $filename );
	}

	public function saveXML( DOMNode $node = null, $options = null ) {
		return ( $options === null ) ? DOMDocument::saveXML( $node ) : DOMDocument::saveXML( $node, $options );
	}

	public function schemaValidate( $filename, $flags = null ) {
		return ( $flags === null ) ? DOMDocument::schemaValidate( $filename ) : DOMDocument::schemaValidate( $filename, $flags );
	}

	public function schemaValidateSource( $source, $flags = null ) {
		return ( $flags === null ) ? DOMDocument::schemaValidateSource( $source ) : DOMDocument::schemaValidateSource( $source, $flags );
	}

	public function validate() {
		return DOMDocument::validate();
	}

	public function xinclude( $options = null ) {
		return ( $options === null ) ? DOMDocument::xinclude() : DOMDocument::xinclude( $options );
	}

	public static function appendNodesToArray( $nodes, &$array ) {
		foreach ( $nodes as $nodek => $nodev ) {
			$array[] = $nodev;
		}
		return $array;
	}

	public static function selectorToGroups( $selector ) {
		$selector = trim( $selector );
		$is_tag = false;
		$is_attr = false;
		$is_value = false;
		$tag = '';
		$attr = '';
		$value = '';
		$groups = array();
		$counter = 0;
		for ( $i = 0; $i < strlen( $selector ); $i++ ) {
			$char = $selector[$i];
			if ( ctype_alpha( $char ) || $char === "\x5f" ) {
				if ( !$is_attr && !$is_value ) {
					$tag .= $char;
					continue;
				}
				if ( !$is_tag && !$is_value ) {
					$attr .= $char;
					continue;
				}
				if ( !$is_tag && !$is_attr ) {
					$value .= $char;
					continue;
				}
				echo '<h1>CTYPE_ALPHA</h1>';
				var_dump( $char );
				die();
			} elseif ( $char === "\x2d" ) {
				if ( !$is_tag && !$is_attr ) {
					$value .= $char;
					continue;
				}
				echo '<h1>\x2d</h1>';
				var_dump( $char );
				die();
			} else {
				if ( $char === '[' ) {
					$is_tag = false;
					$is_attr = true;
					$is_value = false;
					$groups[$counter]['tag'] = $tag;
					continue;
				}
				if ( $char === '=' ) {
					$is_tag = false;
					$is_attr = false;
					$is_value = true;
					$groups[$counter]['attr'] = $attr;
					continue;
				}
				if ( $char === ']' ) {
					$is_tag = false;
					$is_attr = false;
					$is_value = false;
					$groups[$counter]['value'] = $value;
					continue;
				}
				if ( $char === ' ' ) {
					$groups[$counter]['tag'] = $tag;
					$is_tag = false;
					$is_attr = false;
					$is_value = false;
					$tag = '';
					$attr = '';
					$value = '';
					$counter++;
					continue;
				}
				if ( !$is_attr && !$is_value ) {
					if ( ctype_digit( $char ) ) {
						if ( strtolower( $tag ) !== 'h' ) {
							continue;
						}
						$tag .= $char;
						continue;
					}
				}
				echo '<h1>ELSE</h1>';
				var_dump( $char );
				die();
			}
		}
		if ( !empty( $tag ) ) $groups[$counter]['tag'] = $tag;
		if ( !empty( $attr ) ) $groups[$counter]['attr'] = $attr;
		/*if ( !empty( $value ) ) $groups[$counter]['value'] = $value;*/
		return $groups;
	}

	protected function groupsToNodes( $groups ) {
		$nodes = array();
		foreach ( $groups as $groupk => $groupv ) {
			if ( !$nodes ) {
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) && isset( $groupv['value'] ) ) {
					echo '<h1>TAG_ATTR_VALUE_FALSE</h1>';
					die();
				}
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) ) {
					echo '<h1>TAG_ATTR_FALSE</h1>';
					die();
				}
				if ( isset( $groupv['tag'] ) ) {
					$list = $this->getElementsByTagName( $groupv['tag'] );
					if ( $list->length === 0 ) {
						$nodes = array();
						break;
					}
					static::appendNodesToArray( $list, $nodes );
					continue;
				}
				if ( isset( $groupv['attr'] ) ) {
					echo '<h1>ATTR_FALSE</h1>';
					die();
				}
			} else {
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) && isset( $groupv['value'] ) ) {
					echo '<h1>TAG_ATTR_VALUE_TRUE</h1>';
					die();
				}
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) ) {
					echo '<h1>TAG_ATTR_TRUE</h1>';
					die();
				}
				if ( isset( $groupv['tag'] ) ) {
					$nodes2 = array();
					foreach ( $nodes as $nodek => $nodev ) {
						if ( ( $list = $nodev->getElementsByTagName( $groupv['tag'] ) ) === null || $list === false || !isset( $list->length ) || $list->length === 0 ) {
							unset( $nodes[$nodek] );
							continue;
						}
						static::appendNodesToArray( $list, $nodes2 );
					}
					if ( !empty( $nodes2 ) ) {
						$nodes = $nodes2;
					}
					unset( $nodek );
					unset( $nodev );
					unset( $list );
					unset( $nodes2 );
				}
				if ( isset( $groupv['attr'] ) ) {
					echo '<h1>ATTR_TRUE</h1>';
					die();
				}
			}
		}
		return $nodes;
	}

	public function querySelector( $selector ) {
		$nodes = $this->querySelectorAll( $selector );
		return $nodes ? $nodes[0] : null;
	}

	public function querySelectorAll( $selector ) {
		$groups = static::selectorToGroups( $selector );
		$nodes = $this->groupsToNodes( $groups );
		return $nodes;
	}

}

?>
