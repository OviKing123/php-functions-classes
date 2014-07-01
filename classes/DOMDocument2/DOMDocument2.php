<?php

class DOMDocument2 extends DOMDocument {

	public function __construct( $version = null, $encoding = null ) {
		DOMDocument::__construct( $version, $encoding );
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
		if ( file_exists( $filename ) ) {
			return ( $options === 0 ) ? DOMDocument::load( $filename ) : DOMDocument::load( $filename, $options );
		} elseif ( static::isHTML( $filename ) ) {
			return ( $options === 0 ) ? $this->loadHTML( $filename ) : $this->loadHTML( $filename, $options );
		} elseif ( static::isXML( $filename ) ) {
			return ( $options === 0 ) ? $this->loadXML( $filename ) : $this->loadXML( $filename, $options );
		} else {
			return ( $options === 0 ) ? DOMDocument::load( $filename ) : DOMDocument::load( $filename, $options );
		}
	}

	public function loadHTML( $source, $options = 0 ) {
		libxml_use_internal_errors( true );
		$this->recover = true;
		$this->strictErrorChecking = false;
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
				if ( $char === '.' ) {
					if ( !$is_tag && !$is_attr && !$is_value ) {
						$is_tag = false;
						$is_attr = false;
						$is_value = true;
						$groups[$counter]['tag'] = '*';
						$groups[$counter]['attr'] = 'class';
						continue;
					} else {
						die( 'Die Dot Class' );
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
					} else {
						die( 'Die Dot Id' );
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
				echo '<h1>ELSE</h1>';
				var_dump( $tag );
				var_dump( $groups );
				var_dump( $char );
				die();
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
				if ( $char === '.' ) {
					if ( !$is_tag && !$is_attr && !$is_value ) {
						$is_tag = false;
						$is_attr = false;
						$is_value = true;
						$groups[$counter]['tag'] = '*';
						$groups[$counter]['attr'] = 'class';
						continue;
					} else {
						die( 'Die Dot Class' );
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
					} else {
						die( 'Die Dot Id' );
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
				echo '<h1>ELSE</h1>';
				var_dump( $tag );
				var_dump( $groups );
				var_dump( $char );
				die();
			}
		}
		if ( !empty( $tag ) ) $groups[$counter]['tag'] = $tag;
		if ( !empty( $attr ) ) $groups[$counter]['attr'] = $attr;
		if ( !empty( $value ) ) $groups[$counter]['value'] = $value;
		return $groups;
	}

	/* Dom List Tag Attribute Value */

	protected static function domListTagAttrValue( $dom, $tag, $attr, $value ) {

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

	protected static function domListTagAttr( $dom, $tag, $attr ) {

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
					$nodeList = static::domListTagAttrValue( $this, $groupv['tag'], $groupv['attr'], $groupv['value'] );
					if ( $nodeList->length === 0 ) {
						break;
					}
					$nodes = $nodeList;
					continue;
				}
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) ) {
					$nodeList = static::domListTagAttr( $this, $groupv['tag'], $groupv['attr'] );
					if ( $nodeList->length === 0 ) {
						break;
					}
					$nodes = $nodeList;
					continue;
				}
				if ( isset( $groupv['tag'] ) ) {
					$nodeList = static::domListTag( $this, $groupv['tag'] );
					if ( $nodeList->length === 0 ) {
						break;
					}
					$nodes = $nodeList;
					continue;
				}
			} else {
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) && isset( $groupv['value'] ) ) {
					$nodes = static::nodeListTagAttrValue( $nodes, $groupv['tag'], $groupv['attr'], $groupv['value'] );
					continue;
				}
				if ( isset( $groupv['tag'] ) && isset( $groupv['attr'] ) ) {
					$nodes = static::nodeListTagAttr( $nodes, $groupv['tag'], $groupv['attr'] );
					continue;
				}
				if ( isset( $groupv['tag'] ) ) {
					$nodes = static::nodeListTag( $nodes, $groupv['tag'] );
					continue;
				}
			}
		}
		return $nodes;
	}

	public static function outerHTML( $node ) {
		if ( $node instanceof DOMElement ) {
			$tmp = new DOMDocument();
			$tmp->appendChild( $tmp->importNode( $node, true ) );
			$html = $tmp->saveHTML();
			$html = ( substr( $html, -1 ) === "\x0a" ) ? substr( $html, 0, -1 ) : $html;
			return $html;
		} else {
			die( 'HAHA' );
		}
	}

	/* New Methods */

	public function getElementsByClassName( $class ) {
	}

	public function getElementsByName( $name ) {
	}

	/* New Methods */

	public function querySelector( $selector ) {
		return ( ( $nodes = $this->querySelectorAll( $selector ) ) ) ? $nodes[0] : null;
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
		return self::loadHTMLFile( $filename, $options );
	}

	public function fileLoad( $filename, $options = 0 ) {
		return self::loadHTMLFile( $filename, $options );
	}

	/* New Methods */

	public function find( $selector ) {
		return self::querySelectorAll( $selector );
	}

}


?>
