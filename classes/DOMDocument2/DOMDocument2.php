<?php

class DOMAttr2 extends DOMAttr {}
class DOMCharacterData2 extends DOMCharacterData {}
class DOMDocumentFragment2 extends DOMDocumentFragment {}
class DOMDocumentType2 extends DOMDocumentType {}
class DOMEntity2 extends DOMEntity {}
class DOMEntityReference2 extends DOMEntityReference {}
class DOMNotation2 extends DOMNotation {}
class DOMProcessingInstruction2 extends DOMProcessingInstruction {}
class DOMNode2 extends DOMNode {}

class DOMNodeList2 extends DOMNodeList {

	/**
	 * Retrieves a node specified by index within the DOMNodeList object.
	 * @param  int             $index
	 * @return DOMElement|null
	 */
	public function item( $index ) {
		return DOMNodeList::item( $index );
	}

	/**
	 * Checks if the given index exists in the DOMNodeList
	 * @param  int     $index
	 * @return boolean
	 */
	public function hasIndex( $index ) {
		return DOMNodeList::item( $index );
	}

	/**
	 * Retrieves the first node within the DOMNodeList object.
	 * @return DOMElement|null
	 */
	public function first() {
		return DOMNodeList::item(0);
	}

	/**
	 * Retrieves the second node within the DOMNodeList object.
	 * @return DOMElement|null
	 */
	public function second() {
		return DOMNodeList::item(1);
	}

	/**
	 * Retrieves the last node within the DOMNodeList object.
	 * @return DOMElement|null
	 */
	public function last() {
		return DOMNodeList::item( $this->length - 1 );
	}

	/**
	 * Aliases of New Methods
	 */

	public function hasItem( $index ) {
		return self::hasIndex( $index );
	}

}

class DOMElement2 extends DOMElement {

	/**
	 * Creates a new DOMElement object
	 * @param string $name         The tag name of the element. When also passing in namespaceURI, the element name may take a prefix to be associated with the URI.
	 * @param string $value        The value of the element.
	 * @param string $namespaceURI A namespace URI to create the element within a specific namespace.
	 */
	public function __construct( $name, $value = null, $namespaceURI = null ) {
		parent::__construct( $name, $value, $namespaceURI );
	}

	/**
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString() {
		return $this->outerElement();
	}

	/**
	 * [__get description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}
		switch ( strtolower( $name ) ) {
			case 'href':
				return $this->getAttribute( 'href' );
			break;
			case 'outerhtml':
			case 'htmlouter':
			case 'outer':
			case 'out':
				return self::outerElement( $this );
				break;
			case 'innerhtml':
			case 'htmlinner':
			case 'inner':
			case 'in':
				return self::innerElement( $this );
				break;
			default:
				if ( $this->hasAttribute( $name ) ) {
					return $this->getAttribute( $name );
				} else {
					trigger_error( 'Undefined property: ' . __CLASS__ . '::$' . $name, E_USER_NOTICE );
				}
				break;
		}
	}

	/**
	 * [innerElement description]
	 * @param  [type] $element [description]
	 * @return [type]          [description]
	 */
	public function innerElement( $element = null ) {
		$element = !is_null( $element ) ? $element : $this;
		$dom = new DOMDocument2();
		$result = $dom->innerElement( $element );
		return $result;
	}

	/**
	 * [outerElement description]
	 * @param  [type] $element [description]
	 * @return [type]          [description]
	 */
	public function outerElement( $element = null ) {
		$element = !is_null( $element ) ? $element : $this;
		$dom = new DOMDocument2();
		$result = $dom->outerElement( $element );
		return $result;
	}

	/**
	 * [find description]
	 * @param  [type] $selector [description]
	 * @return [type]           [description]
	 */
	public function find( $selector ) {
		$dom = new DOMDocument2();
		$dom->appendChild( $dom->importNode( $this, true ) );
		$result = $dom->find( $selector );
		return $result;
	}

	public function textarea( $element = null ) {
		$element = !is_null( $element ) ? $element : $this;
		$result = $element->outerHTML;
		$lines = substr_count( $result, "\x0a" );
		$height = ( $lines + 2 ) * 20;
		echo '<textarea style="font-family: Consolas; font-size: 14px; height: ' . $height . 'px; line-height: 20px; margin-top: 10px; tab-size: 4; width: 100%;">';
		if ( stripos( $result, 'textarea' ) ) {
			$result = str_ireplace( '<textarea', '<textarea', $element );
			$result = str_ireplace( 'textarea>', 'textarea>', $element );
		}
		echo $result;
		echo '</textarea>';
	}

	public function innerHTML( $element = null ) {
		return self::innerElement( $element );
	}

	public function inner( $element = null ) {
		return self::innerElement( $element );
	}

	public function in( $element = null ) {
		return self::innerElement( $element );
	}

	public function elementInner( $element = null ) {
		return self::innerElement( $element );
	}

	public function HTMLInner( $element = null ) {
		return self::innerElement( $element );
	}

	public function outerHTML( $element = null ) {
		return self::outerElement( $element );
	}

	public function outer( $element = null ) {
		return self::outerElement( $element );
	}

	public function out( $element = null ) {
		return self::outerElement( $element );
	}

	public function elementOuter( $element = null ) {
		return self::outerElement( $element );
	}

	public function HTMLOuter( $element = null ) {
		return self::outerElement( $element );
	}

}

class DOMDocument2 extends DOMDocument implements Serializable {

	/**
	 * Creates a new DOMDocument object
	 * @param string $version  The version number of the document as part of the XML declaration.
	 * @param string $encoding The encoding of the document as part of the XML declaration.
	 */
	public function __construct( $version = null, $encoding = null ) {
		DOMDocument::__construct( $version, $encoding );
		libxml_use_internal_errors( true );
		$this->registerNodeClass( 'DOMElement', 'DOMElement2' );
		$this->recover = true;
		$this->strictErrorChecking = false;
	}

	/**
	 * [serialize description]
	 * @return [type] [description]
	 */
	public function serialize(){
		return static::isHTML( self::saveHTML() ) ? self::saveHTML() : self::saveXML();
	}

	/**
	 * [unserialize description]
	 * @param  [type] $serialized [description]
	 * @return [type]             [description]
	 */
	public function unserialize( $serialized ) {
		return static::isHTML( self::saveHTML() ) ? self::loadHTML() : self::loadXML();
	}

	/**
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString() {
		return $this->outerHTML();
	}

	/**
	 * [__get description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}
		switch ( strtolower( $name ) ) {
			case 'outerhtml':
			case 'htmlouter':
			case 'outer':
			case 'out':
				return self::outerDOM( $this );
				break;
			case 'innerhtml':
			case 'htmlinner':
			case 'inner':
			case 'in':
				return self::innerDOM( $this );
				break;
			default:
				trigger_error( 'Undefined property: ' . __CLASS__ . '::$' . $name, E_USER_NOTICE );
				break;
		}
	}

	/**
	 * [c14N description]
	 * @param  boolean $exclusive     [description]
	 * @param  boolean $with_comments [description]
	 * @param  [type]  $xpath         [description]
	 * @param  [type]  $ns_prefixes   [description]
	 * @return [type]                 [description]
	 */
	public function c14N( $exclusive = false, $with_comments = true, array $xpath = null, array $ns_prefixes = null ) {
		return str_replace( '<br></br>', '<br/>', DOMDocument::c14N( $exclusive, $with_comments, $xpath, $ns_prefixes ) );
	}

	/**
	 * [isHTML description]
	 * @param  [type]  $filename [description]
	 * @return boolean           [description]
	 */
	public static function isHTML( $filename ) {
		return ( ( ( $DOCTYPE = substr( file_exists( $filename ) ? ltrim( file_get_contents( $filename ) ) : ltrim( $filename ), 0, 14 ) ) !== null && ( $doctype = strtolower( $DOCTYPE ) ) !== null ) && ( $doctype === '<!doctype html' || substr( $doctype, 0, 5 ) === '<html' ) ) ? true : false;
	}

	/**
	 * [isXML description]
	 * @param  [type]  $filename [description]
	 * @return boolean           [description]
	 */
	public static function isXML( $filename ) {
		return true;
	}

	/**
	 * [createAttribute description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function createAttribute( $name ) {
		return DOMDocument::createAttribute( $name );
	}

	/**
	 * [createAttributeNS description]
	 * @param  [type] $namespaceURI  [description]
	 * @param  [type] $qualifiedName [description]
	 * @return [type]                [description]
	 */
	public function createAttributeNS( $namespaceURI, $qualifiedName ) {
		return DOMDocument::createAttributeNS( $namespaceURI, $qualifiedName );
	}

	/**
	 * [createCDATASection description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function createCDATASection( $data ) {
		return DOMDocument::createCDATASection( $data );
	}

	/**
	 * [createComment description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function createComment( $data ) {
		return DOMDocument::createComment( $data );
	}

	/**
	 * [createDocumentFragment description]
	 * @return [type] [description]
	 */
	public function createDocumentFragment() {
		return ( class_exists( 'DOMDocumentFragment2' ) ) ? new DOMDocumentFragment2() : DOMDocument::createDocumentFragment();
	}

	/**
	 * [createElement description]
	 * @param  [type] $name  [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function createElement( $name, $value = null ) {
		return ( $value === null ) ? DOMDocument::createElement( $name ) : DOMDocument::createElement( $name, $value );
	}

	/**
	 * [createElementNS description]
	 * @param  [type] $namespaceURI  [description]
	 * @param  [type] $qualifiedName [description]
	 * @param  [type] $value         [description]
	 * @return [type]                [description]
	 */
	public function createElementNS( $namespaceURI, $qualifiedName, $value = null ) {
		return ( $value === null ) ? DOMDocument::createElementNS( $namespaceURI, $qualifiedName ) : DOMDocument::createElementNS( $namespaceURI, $qualifiedName, $value );
	}

	/**
	 * [createEntityReference description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function createEntityReference( $name ){
		return DOMDocument::createEntityReference( $name );
	}

	/**
	 * [createProcessingInstruction description]
	 * @param  [type] $target [description]
	 * @param  [type] $data   [description]
	 * @return [type]         [description]
	 */
	public function createProcessingInstruction( $target, $data = null ) {
		return ( $data === null ) ? DOMDocument::createProcessingInstruction( $target ) : DOMDocument::createProcessingInstruction( $target, $data );
	}

	/**
	 * [createTextNode description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function createTextNode( $content ) {
		return DOMDocument::createTextNode( $content );
	}

	/**
	 * [getElementById description]
	 * @param  [type] $elementId [description]
	 * @return [type]            [description]
	 */
	public function getElementById( $elementId ) {
		return DOMDocument::getElementById( $elementId );
	}

	/**
	 * [getElementsByTagName description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function getElementsByTagName( $name ) {
		return DOMDocument::getElementsByTagName( $name );
	}

	/**
	 * [getElementsByTagNameNS description]
	 * @param  [type] $namespaceURI [description]
	 * @param  [type] $localName    [description]
	 * @return [type]               [description]
	 */
	public function getElementsByTagNameNS( $namespaceURI, $localName ) {
		return DOMDocument::getElementsByTagNameNS( $namespaceURI, $localName );
	}

	/**
	 * [importNode description]
	 * @param  DOMNode $importedNode [description]
	 * @param  [type]  $deep         [description]
	 * @return [type]                [description]
	 */
	public function importNode( DOMNode $importedNode, $deep = null ) {
		return ( $deep === null ) ? DOMDocument::importNode( $importedNode ) : DOMDocument::importNode( $importedNode, $deep );
	}

	/**
	 * [load description]
	 * @param  [type]  $filename [description]
	 * @param  integer $options  [description]
	 * @return [type]            [description]
	 */
	public function load( $filename, $options = 0 ) {

		if ( static::isHTML( $filename ) ) {
			if ( file_exists( $filename ) ) {
				return ( $options === 0 ) ? self::loadHTMLFile( $filename ) : self::loadHTMLFile( $filename, $options );
			} else {
				return ( $options === 0 ) ? self::loadHTML( $filename ) : self::loadHTML( $filename, $options );
			}
		} elseif ( static::isXML( $filename ) && is_string( $filename ) && !file_exists( $filename ) ) {
			return ( $options === 0 ) ? self::loadXML( $filename ) : self::loadXML( $filename, $options );
		} else {
			return ( $options === 0 ) ? DOMDocument::load( $filename ) : DOMDocument::load( $filename, $options );
		}

	}

	/**
	 * [loadHTML description]
	 * @param  [type]  $source  [description]
	 * @param  integer $options [description]
	 * @return [type]           [description]
	 */
	public function loadHTML( $source, $options = 0 ) {

		if ( file_exists( $source ) ) {
			return ( $options === 0 ) ? self::loadHTMLFile( $source ) : self::loadHTMLFile( $source, $options );
		}

		return ( $options === 0 ) ? DOMDocument::loadHTML( $source ) : DOMDocument::loadHTML( $source, $options );

	}

	/**
	 * [loadHTMLFile description]
	 * @param  [type]  $filename [description]
	 * @param  integer $options  [description]
	 * @return [type]            [description]
	 */
	public function loadHTMLFile( $filename, $options = 0 ) {
		return ( ( libxml_use_internal_errors( true ) !== null ) && $options === 0 ) ? DOMDocument::loadHTMLFile( $filename ) : DOMDocument::loadHTMLFile( $filename, $options );
	}

	/**
	 * [loadXML description]
	 * @param  [type]  $source  [description]
	 * @param  integer $options [description]
	 * @return [type]           [description]
	 */
	public function loadXML( $source, $options = 0 ) {
		return ( $options === null ) ? DOMDocument::loadXML( $source ) : DOMDocument::loadXML( $source, $options );
	}

	/**
	 * [normalizeDocument description]
	 * @return [type] [description]
	 */
	public function normalizeDocument() {
		DOMDocument::normalizeDocument();
	}

	/**
	 * [registerNodeClass description]
	 * @param  [type] $baseclass     [description]
	 * @param  [type] $extendedclass [description]
	 * @return [type]                [description]
	 */
	public function registerNodeClass( $baseclass, $extendedclass ) {
		return DOMDocument::registerNodeClass( $baseclass, $extendedclass );
	}

	/**
	 * [relaxNGValidate description]
	 * @param  [type] $filename [description]
	 * @return [type]           [description]
	 */
	public function relaxNGValidate( $filename ) {
		return DOMDocument::relaxNGValidate( $filename );
	}

	/**
	 * [relaxNGValidateSource description]
	 * @param  [type] $source [description]
	 * @return [type]         [description]
	 */
	public function relaxNGValidateSource( $source ) {
		return DOMDocument::relaxNGValidateSource( $source );
	}

	/**
	 * [save description]
	 * @param  [type] $filename [description]
	 * @param  [type] $options  [description]
	 * @return [type]           [description]
	 */
	public function save( $filename, $options = null ) {
		return ( $options === null ) ? DOMDocument::save( $filename ) : DOMDocument::save( $filename, $options );
	}

	/**
	 * [saveHTML description]
	 * @param  [type] $node [description]
	 * @return [type]       [description]
	 */
	public function saveHTML( DOMNode $node = null ) {
		return ( $node === null ) ? DOMDocument::saveHTML() : DOMDocument::saveHTML( $node );
	}

	/**
	 * [saveHTMLFile description]
	 * @param  [type] $filename [description]
	 * @return [type]           [description]
	 */
	public function saveHTMLFile( $filename ) {
		return DOMDocument::saveHTMLFile( $filename );
	}

	/**
	 * [saveXML description]
	 * @param  [type] $node    [description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public function saveXML( DOMNode $node = null, $options = null ) {
		if ( $options === null ) {
			return ( $node === null ) ? DOMDocument::saveXML() : DOMDocument::saveXML( $node );
		} else {
			return ( $node === null ) ? DOMDocument::saveXML() : DOMDocument::saveXML( $node, $options );
		}
	}

	/**
	 * [schemaValidate description]
	 * @param  [type] $filename [description]
	 * @param  [type] $flags    [description]
	 * @return [type]           [description]
	 */
	public function schemaValidate( $filename, $flags = null ) {
		return ( $flags === null ) ? DOMDocument::schemaValidate( $filename ) : DOMDocument::schemaValidate( $filename, $flags );
	}

	/**
	 * [schemaValidateSource description]
	 * @param  [type] $source [description]
	 * @param  [type] $flags  [description]
	 * @return [type]         [description]
	 */
	public function schemaValidateSource( $source, $flags = null ) {
		return ( $flags === null ) ? DOMDocument::schemaValidateSource( $source ) : DOMDocument::schemaValidateSource( $source, $flags );
	}

	/**
	 * [validate description]
	 * @return [type] [description]
	 */
	public function validate() {
		return DOMDocument::validate();
	}

	/**
	 * [xinclude description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public function xinclude( $options = null ) {
		return ( $options === null ) ? DOMDocument::xinclude() : DOMDocument::xinclude( $options );
	}

	/**
	 * [selectorToGroups description]
	 * @param  [type] $selector [description]
	 * @return [type]           [description]
	 */
	public static function selectorToGroups( $selector ) {
		return ( strpos( $selector, ',' ) !== false ) ? static::selectorToGroupsMultiple( $selector ) : static::selectorToGroupsSingle( $selector );
	}

	/**
	 * [selectorToGroupsMultiple description]
	 * @param  [type] $selector [description]
	 * @return [type]           [description]
	 */
	public static function selectorToGroupsMultiple( $selector ) {
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
			} elseif ( $char === "\x2d" ) {
				if ( !$is_tag && !$is_attr ) {
					$value .= $char;
					continue;
				}
			} else {
				if ( $char === '.' ) {
					if ( !$is_tag && !$is_attr && !$is_value ) {
						$is_tag = false;
						$is_attr = false;
						$is_value = true;
						$groups[$counter]['tag'] = '*';
						$groups[$counter]['attr'] = 'class';
						continue;
					}
				}
				if ( $char === '#' ) {
					if ( !$is_tag && !$is_attr && !$is_value ) {
						$is_tag = false;
						$is_attr = false;
						$is_value = true;
						$groups[$counter]['tag'] = '*';
						$groups[$counter]['attr'] = 'id';
						continue;
					}
				}
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
					if ( !empty( $value ) ) {
						$groups[$counter]['value'] = $value;
					}
					continue;
				}
				if ( $char === ' ' ) {
					if ( !empty( $tag ) ) {
						$groups[$counter]['tag'] = $tag;
					}
					if ( !empty( $value ) ) {
						$groups[$counter]['value'] = $value;
					}
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
				if ( $char === "\x22" || $char === "\x27" ) {
					continue;
				}
				if ( !$is_tag && !$is_attr ) {
					$value .= $char;
					continue;
				}
			}
		}
		if ( !empty( $tag ) ) $groups[$counter]['tag'] = $tag;
		if ( !empty( $attr ) ) $groups[$counter]['attr'] = $attr;
		if ( !empty( $value ) ) $groups[$counter]['value'] = $value;
		return $groups;
	}

	/**
	 * [selectorToGroupsSingle description]
	 * @param  [type] $selector [description]
	 * @return [type]           [description]
	 */
	public static function selectorToGroupsSingle( $selector ) {
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
			} elseif ( $char === "\x2d" ) {
				if ( !$is_tag && !$is_attr ) {
					$value .= $char;
					continue;
				}
			} else {
				if ( $char === '.' ) {
					if ( !$is_tag && !$is_attr && !$is_value ) {
						$is_tag = false;
						$is_attr = false;
						$is_value = true;
						$groups[$counter]['tag'] = '*';
						$groups[$counter]['attr'] = 'class';
						continue;
					}
				}
				if ( $char === '#' ) {
					if ( !$is_tag && !$is_attr && !$is_value ) {
						$is_tag = false;
						$is_attr = false;
						$is_value = true;
						$groups[$counter]['tag'] = '*';
						$groups[$counter]['attr'] = 'id';
						continue;
					}
				}
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
					if ( !empty( $value ) ) {
						$groups[$counter]['value'] = $value;
					}
					continue;
				}
				if ( $char === ' ' ) {
					if ( !empty( $tag ) ) {
						$groups[$counter]['tag'] = $tag;
					}
					if ( !empty( $value ) ) {
						$groups[$counter]['value'] = $value;
					}
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
				if ( $char === "\x22" || $char === "\x27" ) {
					continue;
				}
				if ( !$is_tag && !$is_attr ) {
					$value .= $char;
					continue;
				}
			}
		}
		if ( !empty( $tag ) ) $groups[$counter]['tag'] = $tag;
		if ( !empty( $attr ) ) $groups[$counter]['attr'] = $attr;
		if ( !empty( $value ) ) $groups[$counter]['value'] = $value;
		return $groups;
	}

	/**
	 * [domListTagAttrValue description]
	 * @param  [type] $dom   [description]
	 * @param  [type] $tag   [description]
	 * @param  [type] $attr  [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public static function domListTagAttrValue( $dom, $tag, $attr, $value ) {
		$list = $dom->getElementsByTagName( $tag );
		if ( $list->length === 0 ) {
			return $list;
		}
		$tmp = new DOMDocument2();
		$i = 0;
		$j = 0;
		for (;;) {
			$el = $list->item( $i );
			if ( $el === null ) {
				break;
			}
			if ( $el->hasAttribute( $attr ) === false ) {
				$i++;
				continue;
			}
			$val = trim( $el->getAttribute( $attr ) );
			if ( $val === '' ) {
				$i++;
				continue;
			}
			if ( $val === $value || in_array( $value, explode( "\x20", $val ) ) ) {
				$tmp->appendChild( $tmp->importNode( $el, true ) );
				$i++;
				continue;
			}
			$i++;
		}
		return $tmp->childNodes;
	}

	/**
	 * [domListTagAttr description]
	 * @param  [type] $dom  [description]
	 * @param  [type] $tag  [description]
	 * @param  [type] $attr [description]
	 * @return [type]       [description]
	 */
	public static function domListTagAttr( $dom, $tag, $attr ) {
		$list = $dom->getElementsByTagName( $tag );
		if ( $list->length === 0 ) {
			return $list;
		}
		$tmp = new DOMDocument2();
		$i = 0;
		for (;;) {
			$el = $list->item( $i );
			if ( $el === null ) {
				break;
			}
			if ( $el->hasAttribute( $attr ) === false ) {
				$i++;
				continue;
			}
			$tmp->appendChild( $tmp->importNode( $el, true ) );
			$i++;
		}
		return $tmp->childNodes;
	}

	/**
	 * [domListTag description]
	 * @param  [type] $dom [description]
	 * @param  [type] $tag [description]
	 * @return [type]      [description]
	 */
	public static function domListTag( $dom, $tag ) {
		$list = $dom->getElementsByTagName( $tag );
		if ( $list->length === 0 ) {
			return $list;
		}
		return $list;
	}

	/**
	 * [nodeListTagAttrValue description]
	 * @param  [type] $list  [description]
	 * @param  [type] $tag   [description]
	 * @param  [type] $attr  [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public static function nodeListTagAttrValue( $list, $tag, $attr, $value ) {
		$dom = new DOMDocument2();
		$i = 0;
		for (;;) {
			$el = $list->item( $i );
			if ( $el === null ) {
				break;
			}
			$els = $el->getElementsByTagName( $tag );
			if ( $els->length === 0 ) {
				$i++;
				continue;
			}
			$j = 0;
			for (;;) {
				$node = $els->item( $j );
				if ( $node === null ) {
					break;
				}
				if ( $node->hasAttribute( $attr ) === false ) {
					$j++;
					continue;
				}
				$val = trim( $node->getAttribute( $attr ) );
				if ( $val === '' ) {
					$j++;
					continue;
				}
				if ( $val === $value || in_array( $value, explode( "\x20", $val ) ) ) {
					$dom->appendChild( $dom->importNode( $node, true ) );
					$j++;
					continue;
				}
				$j++;
			}
			$i++;
		}
		return $dom->childNodes;
	}

	/**
	 * [nodeListTagAttr description]
	 * @param  [type] $list [description]
	 * @param  [type] $tag  [description]
	 * @param  [type] $attr [description]
	 * @return [type]       [description]
	 */
	public static function nodeListTagAttr( $list, $tag, $attr ) {
		$dom = new DOMDocument2();
		$i = 0;
		for (;;) {
			$el = $list->item( $i );
			if ( $el === null ) {
				break;
			}
			$els = $el->getElementsByTagName( $tag );
			if ( $els->length === 0 ) {
				$i++;
				continue;
			}
			$j = 0;
			for (;;) {
				$node = $els->item( $j );
				if ( $node === null ) {
					break;
				}
				if ( $node->hasAttribute( $attr ) === false ) {
					$j++;
					continue;
				}
				$dom->appendChild( $dom->importNode( $node, true ) );
				$j++;
			}
			$i++;
		}
		return $dom->childNodes;
	}

	/**
	 * [nodeListTag description]
	 * @param  [type] $list [description]
	 * @param  [type] $tag  [description]
	 * @return [type]       [description]
	 */
	public static function nodeListTag( $list, $tag ) {
		$dom = new DOMDocument2();
		$i = 0;
		for (;;) {
			$el = $list->item( $i );
			if ( $el === null ) {
				break;
			}
			$els = $el->getElementsByTagName( $tag );
			if ( $els->length === 0 ) {
				$i++;
				continue;
			}
			$j = 0;
			for (;;) {
				$node = $els->item( $j );
				if ( $node === null ) {
					break;
				}
				$dom->appendChild( $dom->importNode( $node, true ) );
				$j++;
			}
			$i++;
		}
		return $dom->childNodes;
	}

	/**
	 * [groupsToNodeList description]
	 * @param  [type] $groups [description]
	 * @return [type]         [description]
	 */
	protected function groupsToNodeList( $groups ) {
		$nodes = new DOMNodeList();
		foreach ( $groups as $groupk => $groupv ) {
			if ( $nodes->length === 0 ) {
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) && isset( $groupv['value'] ) ) {
					$nodes = static::domListTagAttrValue( $this, $groupv['tag'], $groupv['attr'], $groupv['value'] );
					if ( $nodes->length === 0 ) {
						break;
					}
					continue;
				}
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) ) {
					$nodes = static::domListTagAttr( $this, $groupv['tag'], $groupv['attr'] );
					if ( $nodes->length === 0 ) {
						break;
					}
					continue;
				}
				if ( isset( $groupv['tag'] ) ) {
					$nodes = static::domListTag( $this, $groupv['tag'] );
					if ( $nodes->length === 0 ) {
						break;
					}
					continue;
				}
			} else {
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) && isset( $groupv['value'] ) ) {
					$nodes = static::nodeListTagAttrValue( $nodes, $groupv['tag'], $groupv['attr'], $groupv['value'] );
					if ( $nodes->length === 0 ){
						break;
					}
					continue;
				}
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) ) {
					$nodes = static::nodeListTagAttr( $nodes, $groupv['tag'], $groupv['attr'] );
					if ( $nodes->length === 0 ){
						break;
					}
					continue;
				}
				if ( isset( $groupv['tag'] ) ) {
					$nodes = static::nodeListTag( $nodes, $groupv['tag'] );
					if ( $nodes->length === 0 ){
						break;
					}
					continue;
				}
			}
		}
		return $nodes;
	}

	/**
	 * [outerString description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function outerString( $value = null ) {
		$value = !is_null( $value ) ? $value : $this;
		if ( is_string( $value ) ) {
			return trim( $value );
		} elseif ( $value instanceof DOMElement ) {
			return self::outerElement( $value );
		} elseif ( $value instanceof DOMDocument ) {
			return self::outerDOM( $value );
		}
		return $value;
	}

	/**
	 * [outerDOM description]
	 * @param  [type] $dom [description]
	 * @return [type]      [description]
	 */
	public function outerDOM( $dom = null ) {
		$dom = !is_null( $dom ) ? $dom : $this;
		if ( $dom instanceof DOMDocument ) {
			$html = $dom->saveHTML();
			if ( strtolower( substr( ltrim( $html ), 0, 10 ) ) === '<!doctype ' ) {
				if ( ( $pos = strpos( $html, '>' ) ) !== false ) {
					$html = ltrim( substr( $html, $pos + 1 ) );
				}
			}
			return $html;
		} elseif ( $dom instanceof DOMElement ) {
			return self::outerElement( $dom );
		} elseif ( is_string( $dom ) ) {
			return self::outerString( $dom );
		}
		return $dom;
	}

	/**
	 * [outerElement description]
	 * @param  [type] $node [description]
	 * @return [type]       [description]
	 */
	public function outerElement( $node = null ) {
		$node = !is_null( $node ) ? $node : $this;
		if ( $node instanceof DOMElement ) {
			$dom = new DOMDocument2();
			$dom->appendChild( $dom->importNode( $node, true ) );
			$html = $dom->saveHTML();
			$html = ( substr( $html, -1 ) === "\x0a" ) ? substr( $html, 0, -1 ) : $html;
			return $html;
		} elseif ( $node instanceof DOMDocument ) {
			return self::outerDOM( $node );
		} elseif ( is_string( $node ) ) {
			return self::outerString( $node );
		}
		return $node;
	}

	/**
	 * [outerHTML description]
	 * @param  [type] $node [description]
	 * @return [type]       [description]
	 */
	public function outerHTML( $node = null ) {
		$node = !is_null( $node ) ? $node : $this;
		if ( $node instanceof DOMElement ) {
			return self::outerElement( $node );
		} elseif ( $node instanceof DOMDocument ) {
			return self::outerDOM( $node );
		} elseif ( is_string( $node ) ) {
			return self::outerString( $node );
		}
		return $node;
	}

	/**
	 * [innerString description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function innerString( $value = null ) {
		$value = !is_null( $value ) ? $value : $this;
		if ( is_string( $value ) ) {
			$left = strpos( $value, '>' );
			$right = strpos( strrev( $value ), '<' );
			if ( $left !== false && $right !== false ) {
				$value = substr( $value, $left + 1 );
				$value = substr( $value, 0, -( $right + 1 ) );
				$value = trim( $value );
			}
			return $value;
		} elseif ( $value instanceof DOMElement ) {
			return self::innerElement( $value );
		} elseif ( $value instanceof DOMDocument ) {
			return self::innerDOM( $value );
		}
		return $value;
	}

	/**
	 * [innerDOM description]
	 * @param  [type] $dom [description]
	 * @return [type]      [description]
	 */
	public function innerDOM( $dom = null ) {
		$dom = !is_null( $dom ) ? $dom : $this;
		if ( $dom instanceof DOMDocument ) {
			$html = $dom->saveHTML();
			if ( strtolower( substr( ltrim( $html ), 0, 10 ) ) === '<!doctype ' ) {
				if ( ( $pos = strpos( $html, '>' ) ) !== false ) {
					$html = substr( $html, $pos + 1 );
					$html = self::innerString( $html );
				}
			}
			return $html;
		} elseif ( $dom instanceof DOMElement ) {
			return self::innerElement( $dom );
		} elseif ( is_string( $dom ) ) {
			return self::innerString( $dom );
		}
		return $dom;
	}

	/**
	 * [innerElement description]
	 * @param  [type] $node [description]
	 * @return [type]       [description]
	 */
	public function innerElement( $node = null ) {
		$node = !is_null( $node ) ? $node : $this;
		if ( $node instanceof DOMElement ) {
			$html = self::outerElement( $node );
			$html = self::innerString( $html );
			return $html;
		} elseif ( $node instanceof DOMDocument ) {
			return self::innerDOM( $node );
		} elseif ( is_string( $node ) ) {
			return self::innerString( $node );
		}
		return $node;
	}

	/**
	 * [innerHTML description]
	 * @param  [type] $node [description]
	 * @return [type]       [description]
	 */
	public function innerHTML( $node = null ) {
		$node = !is_null( $node ) ? $node : $this;
		if ( $node instanceof DOMElement ) {
			return self::innerElement( $node );
		} elseif ( $node instanceof DOMDocument ) {
			return self::innerDOM( $node );
		} elseif ( is_string( $node ) ) {
			return self::innerString( $node );
		}
		return $node;
	}

	/**
	 * [getElementsByClassName description]
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function getElementsByClassName( $class ) {
		/**
		 * First = *
		 * Second = has attribute class
		 * Third = have value of class
		 */
	}

	/**
	 * [getElementsByName description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function getElementsByName( $name ) {
		/**
		 * First = *
		 * Second = has attribute name
		 * Third = have value of name
		 */
	}

	/**
	 * [querySelector description]
	 * @param  [type] $selector [description]
	 * @return [type]           [description]
	 */
	public function querySelector( $selector ) {
		return ( ( $nodes = $this->querySelectorAll( $selector ) ) ) ? $nodes[0] : new DOMNodeList();
	}

	/**
	 * [querySelectorAll description]
	 * @param  [type] $selector [description]
	 * @return [type]           [description]
	 */
	public function querySelectorAll( $selector ) {
		return $this->groupsToNodeList( static::selectorToGroups( $selector ) );
	}

	/**
	 * Aliases of getElementsByTagName
	 */

	public function getElementByTagName( $name ) {
		return self::getElementsByTagName( $name );
	}

	public function getElementsByTag( $name ) {
		return self::getElementsByTagName( $name );
	}

	public function getElementByTag( $name ) {
		return self::getElementsByTagName( $name );
	}

	public function getByTag( $name ) {
		return self::getElementsByTagName( $name );
	}

	public function getTag( $name ) {
		return self::getElementsByTagName( $name );
	}

	/**
	 * Aliases of loadHTMLFile
	 */

	public function loadFileHTML( $filename, $options = 0 ) {
		return self::loadHTMLFile( $filename, $options );
	}

	public function HTMLLoadFile( $filename, $options = 0 ) {
		return self::loadHTMLFile( $filename, $options );
	}

	public function HTMLFileLoad( $filename, $options = 0 ) {
		return self::loadHTMLFile( $filename, $options );
	}

	public function loadFile( $filename, $options = 0 ) {
		return self::loadHTMLFile( $filename, $options );
	}

	public function fileLoad( $filename, $options = 0 ) {
		return self::loadHTMLFile( $filename, $options );
	}

	/**
	 * Aliases of New Methods
	 */

	public function outer( $value = null ) {
		return self::outerHTML( $value );
	}

	public function out( $value = null ) {
		return self::outerHTML( $value );
	}

	public function inner( $value = null ) {
		return self::innerHTML( $value );
	}

	public function in( $value = null ) {
		return self::innerHTML( $value );
	}

	public function find( $selector ) {
		return self::querySelectorAll( $selector );
	}

}

?>
