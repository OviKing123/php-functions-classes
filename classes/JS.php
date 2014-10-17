<?php

class JS {

	public function __construct() {
	}

	public static function load() {
	}

	public static function loadHTML( $source, $options = 0 ) {
		libxml_use_internal_errors( true );
		$dom = new DOMDocument();
		$dom->recover = true;
		$dom->strictErrorChecking = false;
		$dom->loadHTML( $source );
		return $dom;
	}

	public static function loadXML() {
	}

	public static function querySelector() {
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

	public static function groupsToNodes( $dom, $groups ) {
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
					$list = $dom->getElementsByTagName( 'li' );
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

	public static function querySelectorAll( $dom, $selector ) {
		$groups = static::selectorToGroups( $selector );
		$nodes = static::groupsToNodes( $dom, $groups );
		return $nodes;
	}

	public static function getElementById() {
	}

	public static function getElementsByClassName() {
	}

	public static function getElementsByName() {
	}

	public static function getElementsByTagName( $dom, $tag ) {
		return $dom->getElementsByTagName( $tag );
	}

}

?>
