<?php

/* TODO */

class DOMAttr2 extends DOMAttr {}
class DOMCharacterData2 extends DOMCharacterData {}
class DOMDocumentFragment2 extends DOMDocumentFragment {}
class DOMDocumentType2 extends DOMDocumentType {}
class DOMEntity2 extends DOMEntity {}
class DOMEntityReference2 extends DOMEntityReference {}
class DOMNotation2 extends DOMNotation {}
class DOMProcessingInstruction2 extends DOMProcessingInstruction {}
class DOMNode2 extends DOMNode {}

/* IMPOSSIBLE TO DATE - ONLY THEORETICAL CODE */

class DOMNodeList2 extends DOMNodeList {

	public function item( $index ) {
		return DOMNodeList::item( $index );
	}

	public function hasIndex( $index ) {
		return DOMNodeList::item( $index );
	}

	public function first() {
		return DOMNodeList::item(0);
	}

	public function second() {
		return DOMNodeList::item(1);
	}

	public function last() {
		return $this->length - 1 > 0 ? DOMNodeList::item( $this->length - 1 ) : null;
	}

	public function hasItem( $index ) {
		return self::hasIndex( $index );
	}

}

class DOMElement2 extends DOMElement {

	public function __construct( $name, $value = null, $namespaceURI = null ) {
		parent::__construct( $name, $value, $namespaceURI );
	}

	public function __destruct() {
	}

	public function __toString() {
		return $this->nodeValue;
	}

	public function __get( $name ) {

		if ( isset( $this->$name ) ) {
			return $this->$name;
		}

		switch ( strtolower( $name ) ) {
			case 'outerhtml':
			case 'htmlouter':
			case 'outer':
			case 'out':
				return self::outerHTML( $this );
				break;
			case 'innerhtml':
			case 'htmlinner':
			case 'inner':
			case 'in':
				return self::innerHTML( $this );
				break;
			default:
				trigger_error( 'Undefined property: ' . __CLASS__ . '::$' . $name, E_USER_NOTICE );
				break;
		}

	}

	public function innerElement( $element = null ) {
		$element = !is_null( $element ) ? $element : $this;
		$dom = new DOMDocument2();
		$result = $dom->innerElement( $element );
		return $result;
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

	public function outerElement( $element = null ) {
		$element = !is_null( $element ) ? $element : $this;
		$dom = new DOMDocument2();
		$result = $dom->outerElement( $element );
		return $result;
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

	/* Magic Methods */

	public function __construct( $version = null, $encoding = null ) {
		DOMDocument::__construct( $version, $encoding );
		libxml_use_internal_errors( true );
		$this->registerNodeClass( 'DOMElement', 'DOMElement2' );
		$this->recover = true;
		$this->strictErrorChecking = false;
	}

	public function serialize(){
		return static::isHTML( self::saveHTML() ) ? self::saveHTML() : self::saveXML();
	}

	public function unserialize( $serialized ) {
		return static::isHTML( self::saveHTML() ) ? self::loadHTML() : self::loadXML();
	}

	public function __toString() {
		return $this->outerHTML();
	}

	public function c14N( $exclusive = false, $with_comments = true, array $xpath = null, array $ns_prefixes = null ) {
		return str_replace( '<br></br>', '<br/>', DOMDocument::c14N( $exclusive, $with_comments, $xpath, $ns_prefixes ) );
	}

	public static function isHTML( $filename ) {
		return ( ( ( $DOCTYPE = substr( file_exists( $filename ) ? ltrim( file_get_contents( $filename ) ) : ltrim( $filename ), 0, 14 ) ) !== null && ( $doctype = strtolower( $DOCTYPE ) ) !== null ) && ( $doctype === '<!doctype html' || substr( $doctype, 0, 5 ) === '<html' ) ) ? true : false;
	}

	public static function isXML( $filename ) {
		return true;
	}

	public function createAttribute( $name ) {
		return DOMDocument::createAttribute( $name );
	}

	public function createAttributeNS( $namespaceURI, $qualifiedName ) {
		return DOMDocument::createAttributeNS( $namespaceURI, $qualifiedName );
	}

	public function createCDATASection( $data ) {
		return DOMDocument::createCDATASection( $data );
	}

	public function createComment( $data ) {
		return DOMDocument::createComment( $data );
	}

	public function createDocumentFragment() {
		return ( class_exists( 'DOMDocumentFragment2' ) ) ? new DOMDocumentFragment2() : DOMDocument::createDocumentFragment();
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

	public function loadHTML( $source, $options = 0 ) {
		if ( file_exists( $source ) ) {
			return ( $options === 0 ) ? self::loadHTMLFile( $source ) : self::loadHTMLFile( $source, $options );
		}
		return ( $options === 0 ) ? DOMDocument::loadHTML( $source ) : DOMDocument::loadHTML( $source, $options );
	}

	public function loadHTMLFile( $filename, $options = 0 ) {
		return ( ( libxml_use_internal_errors( true ) !== null ) && $options === 0 ) ? DOMDocument::loadHTMLFile( $filename ) : DOMDocument::loadHTMLFile( $filename, $options );
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
		if ( $options === null ) {
			return ( $node === null ) ? DOMDocument::saveXML() : DOMDocument::saveXML( $node );
		} else {
			return ( $node === null ) ? DOMDocument::saveXML() : DOMDocument::saveXML( $node, $options );
		}
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

	public static function selectorToGroups( $selector ) {
		return ( strpos( $selector, ',' ) !== false ) ? static::selectorToGroupsMultiple( $selector ) : static::selectorToGroupsSingle( $selector );
	}

	/* Todo */

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

	/* Dom List Tag Attribute Value */

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

	public static function domListTag( $dom, $tag ) {

		$list = $dom->getElementsByTagName( $tag );

		if ( $list->length === 0 ) {
			return $list;
		}

		return $list;

	}

	/* Node List Tag Attribute Value */

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

	/* New Methods */

	public function getElementsByClassName( $class ) {
	}

	public function getElementsByName( $name ) {
	}

	/* New Methods */

	public function querySelector( $selector ) {
		return ( ( $nodes = $this->querySelectorAll( $selector ) ) ) ? $nodes[0] : new DOMNodeList();
	}

	public function querySelectorAll( $selector ) {
		return $this->groupsToNodeList( static::selectorToGroups( $selector ) );
	}

	/* getElementsByTagName */

	public function getElementByTagName( $name ) {
		return self::getElementsByTagName( $name );
	}

	public function getElementsByTag( $name ) {
		return self::getElementsByTagName( $name );
	}

	public function getElementByTag( $name ) {
		return self::getElementsByTagName( $name );
	}

	/* loadHTMLFile */

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
		//self::registerNodeClass( "DOMElement", "DOMElement2" );
		return self::loadHTMLFile( $filename, $options );
	}

	public function fileLoad( $filename, $options = 0 ) {
		return self::loadHTMLFile( $filename, $options );
	}

	/* New Methods */

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
