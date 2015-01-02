<?php

class Domain {

	public static function get_domain( &$domain, &$host ) {

		static $ccTLDs;
		static $gTLDs;
		static $TLDs;

		$domain = strtolower( $domain );

		$ccTLDs = $ccTLDs !== null ? $ccTLDs : array( 'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'as', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bw', 'by', 'bz', 'ca', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mk', 'ml', 'mm', 'mn', 'mo', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nc', 'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pn', 'pr', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sk', 'sl', 'sm', 'sn', 'sr', 'ss', 'st', 'sv', 'sy', 'sz', 'tc', 'td', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tr', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'za', 'zm', 'zw' );
		$gTLDs = $gTLDs !== null ? $gTLDs : array( 'aero', 'arpa', 'biz', 'com', 'coop', 'edu', 'gov', 'info', 'int', 'jus', 'mil', 'museum', 'name', 'nom', 'net', 'org', 'pro' );
		$TLDs = $TLDs !== null ? $TLDs : array( 'ac', 'academy', 'accountants', 'actor', 'ad', 'ae', 'aero', 'af', 'ag', 'agency', 'ai', 'airforce', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'archi', 'army', 'arpa', 'as', 'asia', 'associates', 'at', 'attorney', 'au', 'audio', 'autos', 'aw', 'ax', 'axa', 'az', 'ba', 'bar', 'bargains', 'bayern', 'bb', 'bd', 'be', 'beer', 'berlin', 'best', 'bf', 'bg', 'bh', 'bi', 'bid', 'bike', 'bio', 'biz', 'bj', 'black', 'blackfriday', 'blue', 'bm', 'bn', 'bo', 'boutique', 'br', 'brussels', 'bs', 'bt', 'build', 'builders', 'buzz', 'bv', 'bw', 'by', 'bz', 'bzh', 'ca', 'cab', 'camera', 'camp', 'capital', 'cards', 'care', 'career', 'careers', 'cash', 'cat', 'catering', 'cc', 'cd', 'center', 'ceo', 'cf', 'cg', 'ch', 'cheap', 'christmas', 'church', 'ci', 'citic', 'ck', 'cl', 'claims', 'cleaning', 'clinic', 'clothing', 'club', 'cm', 'cn', 'co', 'codes', 'coffee', 'college', 'cologne', 'com', 'community', 'company', 'computer', 'condos', 'construction', 'consulting', 'contractors', 'cooking', 'cool', 'coop', 'country', 'cr', 'credit', 'creditcard', 'cruises', 'cu', 'cv', 'cw', 'cx', 'cy', 'cz', 'dance', 'dating', 'de', 'degree', 'democrat', 'dental', 'dentist', 'desi', 'diamonds', 'digital', 'directory', 'discount', 'dj', 'dk', 'dm', 'dnp', 'do', 'domains', 'dz', 'ec', 'edu', 'education', 'ee', 'eg', 'email', 'engineer', 'engineering', 'enterprises', 'equipment', 'er', 'es', 'estate', 'et', 'eu', 'eus', 'events', 'exchange', 'expert', 'exposed', 'fail', 'farm', 'feedback', 'fi', 'finance', 'financial', 'fish', 'fishing', 'fitness', 'fj', 'fk', 'flights', 'florist', 'fm', 'fo', 'foo', 'foundation', 'fr', 'frogans', 'fund', 'furniture', 'futbol', 'ga', 'gal', 'gallery', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gift', 'gives', 'gl', 'glass', 'global', 'globo', 'gm', 'gmo', 'gn', 'gop', 'gov', 'gp', 'gq', 'gr', 'graphics', 'gratis', 'gripe', 'gs', 'gt', 'gu', 'guide', 'guitars', 'guru', 'gw', 'gy', 'hamburg', 'haus', 'hiphop', 'hiv', 'hk', 'hm', 'hn', 'holdings', 'holiday', 'homes', 'horse', 'host', 'house', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'immobilien', 'in', 'industries', 'info', 'ink', 'institute', 'insure', 'int', 'international', 'investments', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jetzt', 'jm', 'jo', 'jobs', 'jp', 'juegos', 'kaufen', 'ke', 'kg', 'kh', 'ki', 'kim', 'kitchen', 'kiwi', 'km', 'kn', 'koeln', 'kp', 'kr', 'kred', 'kw', 'ky', 'kz', 'la', 'land', 'lawyer', 'lb', 'lc', 'lease', 'li', 'life', 'lighting', 'limited', 'limo', 'link', 'lk', 'loans', 'london', 'lr', 'ls', 'lt', 'lu', 'luxe', 'luxury', 'lv', 'ly', 'ma', 'maison', 'management', 'mango', 'market', 'marketing', 'mc', 'md', 'me', 'media', 'meet', 'menu', 'mg', 'mh', 'miami', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'moda', 'moe', 'monash', 'mortgage', 'moscow', 'motorcycles', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nagoya', 'name', 'navy', 'nc', 'ne', 'net', 'neustar', 'nf', 'ng', 'nhk', 'ni', 'ninja', 'nl', 'no', 'np', 'nr', 'nu', 'nyc', 'nz', 'okinawa', 'om', 'onl', 'org', 'organic', 'pa', 'paris', 'partners', 'parts', 'pe', 'pf', 'pg', 'ph', 'photo', 'photography', 'photos', 'pics', 'pictures', 'pink', 'pk', 'pl', 'plumbing', 'pm', 'pn', 'post', 'pr', 'press', 'pro', 'productions', 'properties', 'ps', 'pt', 'pub', 'pw', 'py', 'qa', 'qpon', 'quebec', 're', 'recipes', 'red', 'rehab', 'reise', 'reisen', 'ren', 'rentals', 'repair', 'report', 'republican', 'rest', 'reviews', 'rich', 'rio', 'ro', 'rocks', 'rodeo', 'rs', 'ru', 'ruhr', 'rw', 'ryukyu', 'sa', 'saarland', 'sb', 'sc', 'schule', 'scot', 'sd', 'se', 'services', 'sexy', 'sg', 'sh', 'shiksha', 'shoes', 'si', 'singles', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'social', 'software', 'sohu', 'solar', 'solutions', 'soy', 'space', 'sr', 'st', 'su', 'supplies', 'supply', 'support', 'surf', 'surgery', 'sv', 'sx', 'sy', 'systems', 'sz', 'tattoo', 'tax', 'tc', 'td', 'technology', 'tel', 'tf', 'tg', 'th', 'tienda', 'tips', 'tirol', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'today', 'tokyo', 'tools', 'town', 'toys', 'tp', 'tr', 'trade', 'training', 'travel', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'university', 'uno', 'us', 'uy', 'uz', 'va', 'vacations', 'vc', 've', 'vegas', 'ventures', 'versicherung', 'vet', 'vg', 'vi', 'viajes', 'villas', 'vision', 'vlaanderen', 'vn', 'vodka', 'vote', 'voting', 'voto', 'voyage', 'vu', 'wang', 'watch', 'webcam', 'website', 'wed', 'wf', 'wien', 'wiki', 'works', 'ws', 'wtc', 'wtf', 'xn', 'xxx', 'xyz', 'yachts', 'ye', 'yokohama', 'yt', 'za', 'zm', 'zone', 'zw' );

		if ( !$domain ) {
			return false;
		}

		$dots = explode( '.', $domain );
		$count = count( $dots );

		if ( $count > 2 ) {
			if ( in_array( $dots[$count-2], $gTLDs ) && in_array( $dots[$count-1], $ccTLDs ) ) {
				$host = $dots[$count-3] . '.' . $dots[$count-2] . '.' . $dots[$count-1];
			} elseif ( in_array( $dots[$count-1], $gTLDs ) || in_array( $dots[$count-1], $ccTLDs ) ) {
				$host = $dots[$count-2] . '.' . $dots[$count-1];
			} else {
				$host = null;
				return false;
			}
		} elseif ( $count > 1 ) {
			if ( in_array( $dots[$count-1], $TLDs ) ) {
				$host = $dots[$count-2] . '.' . $dots[$count-1];
			} else {
				$host = null;
				return false;
			}
		} else {
			$host = null;
			return false;
		}

		return true;

	}

}

?>
