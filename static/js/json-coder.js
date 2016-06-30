fwShortcodesJSONCoder = (function ($) {
	return {
		encode: encode,
		decode: decode
	};

	/**
	 * An implementation of encode from the attr coder json.
	 * https://github.com/ThemeFuse/Unyson-Shortcodes-Extension/blob/v1.3.16/includes/coder/class-fw-ext-shortcodes-attr-coder-json.php#L17
	 *
	 * Mainly used for dynamic encoding of values from fw.OptionsModal().
	 */
	function encode (atts) {
		var encoded = {};
		var array_keys = {};

		_.each(
			atts,
			function (value, key) {
				key = str_replace('-', '_', key);

				if (_.isObject(value)) {
					value = JSON.stringify(value);
					array_keys[key] = key;
				}

				encoded[key] = encode_value(value);
			}
		)

		if (! _.isEmpty(array_keys)) {
			encoded['_array_keys'] = encode_value(
				JSON.stringify(array_keys)
			);
		}

		encoded['_made_with_builder'] = 'true';

		return encoded;
	}

	function encode_value (value) {
		return str_replace(
			['[', ']', "\r\n", '\\'],
			['&#91;', '&#93;', '&#010;', '&#92;'],
			htmlentities(value, 'ENT_QUOTES', 'UTF-8')
		);
	}

	function decode () {
		throw 'decode operation is not implemented';
	}

	/////////////

	function str_replace (search, replace, subject, countObj) { // eslint-disable-line camelcase
		//  discuss at: http://locutus.io/php/str_replace/
		// original by: Kevin van Zonneveld (http://kvz.io)
		// improved by: Gabriel Paderni
		// improved by: Philip Peterson
		// improved by: Simon Willison (http://simonwillison.net)
		// improved by: Kevin van Zonneveld (http://kvz.io)
		// improved by: Onno Marsman (https://twitter.com/onnomarsman)
		// improved by: Brett Zamir (http://brett-zamir.me)
		//  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
		// bugfixed by: Anton Ongson
		// bugfixed by: Kevin van Zonneveld (http://kvz.io)
		// bugfixed by: Oleg Eremeev
		// bugfixed by: Glen Arason (http://CanadianDomainRegistry.ca)
		// bugfixed by: Glen Arason (http://CanadianDomainRegistry.ca)
		//    input by: Onno Marsman (https://twitter.com/onnomarsman)
		//    input by: Brett Zamir (http://brett-zamir.me)
		//    input by: Oleg Eremeev
		//      note 1: The countObj parameter (optional) if used must be passed in as a
		//      note 1: object. The count will then be written by reference into it's `value` property
		//   example 1: str_replace(' ', '.', 'Kevin van Zonneveld')
		//   returns 1: 'Kevin.van.Zonneveld'
		//   example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars')
		//   returns 2: 'hemmo, mars'
		//   example 3: str_replace(Array('S','F'),'x','ASDFASDF')
		//   returns 3: 'AxDxAxDx'
		//   example 4: var countObj = {}
		//   example 4: str_replace(['A','D'], ['x','y'] , 'ASDFASDF' , countObj)
		//   example 4: var $result = countObj.value
		//   returns 4: 4

		var i = 0
		var j = 0
		var temp = ''
		var repl = ''
		var sl = 0
		var fl = 0
		var f = [].concat(search)
		var r = [].concat(replace)
		var s = subject
		var ra = Object.prototype.toString.call(r) === '[object Array]'
		var sa = Object.prototype.toString.call(s) === '[object Array]'
		s = [].concat(s)

		/*
		var $global = (typeof window !== 'undefined' ? window : GLOBAL)
		$global.$locutus = $global.$locutus || {}
		var $locutus = $global.$locutus
		$locutus.php = $locutus.php || {}
		*/

		if (typeof (search) === 'object' && typeof (replace) === 'string') {
			temp = replace
			replace = []

			for (i = 0; i < search.length; i += 1) {
				replace[i] = temp
			}

			temp = ''
			r = [].concat(replace)
			ra = Object.prototype.toString.call(r) === '[object Array]'
		}

		if (typeof countObj !== 'undefined') {
			countObj.value = 0
		}

		for (i = 0, sl = s.length; i < sl; i++) {
			if (s[i] === '') { continue }

			for (j = 0, fl = f.length; j < fl; j++) {
				temp = s[i] + ''
				repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0]
				s[i] = (temp).split(f[j]).join(repl)

				if (typeof countObj !== 'undefined') {
					countObj.value += ((temp.split(f[j])).length - 1)
				}
			}
		}

		return sa ? s : s[0]
	}

	function htmlentities (string, quoteStyle, charset, doubleEncode) {
		//  discuss at: http://locutus.io/php/htmlentities/
		// original by: Kevin van Zonneveld (http://kvz.io)
		//  revised by: Kevin van Zonneveld (http://kvz.io)
		//  revised by: Kevin van Zonneveld (http://kvz.io)
		// improved by: nobbler
		// improved by: Jack
		// improved by: Rafał Kukawski (http://blog.kukawski.pl)
		// improved by: Dj (http://locutus.io/php/htmlentities:425#comment_134018)
		// bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
		// bugfixed by: Brett Zamir (http://brett-zamir.me)
		//    input by: Ratheous
		//      note 1: function is compatible with PHP 5.2 and older
		//   example 1: htmlentities('Kevin & van Zonneveld')
		//   returns 1: 'Kevin &amp; van Zonneveld'
		//   example 2: htmlentities("foo'bar","ENT_QUOTES")
		//   returns 2: 'foo&#039;bar'

		var hashMap = get_html_translation_table('HTML_ENTITIES', quoteStyle)

		string = string === null ? '' : string + ''

		if (!hashMap) {
			return false
		}

		if (quoteStyle && quoteStyle === 'ENT_QUOTES') {
			hashMap["'"] = '&#039;'
		}

		doubleEncode = doubleEncode === null || !!doubleEncode

		var regex = new RegExp('&(?:#\\d+|#x[\\da-f]+|[a-zA-Z][\\da-z]*);|[' +
			Object.keys(hashMap)
			.join('')
			// replace regexp special chars
			.replace(/([()[\]{}\-.*+?^$|\/\\])/g, '\\$1') + ']',
			'g')

		return string.replace(regex, function (ent) {
			if (ent.length > 1) {
			return doubleEncode ? hashMap['&'] + ent.substr(1) : ent
			}

			return hashMap[ent]
		})
	}

	function get_html_translation_table (table, quoteStyle) { // eslint-disable-line camelcase
		//  discuss at: http://locutus.io/php/get_html_translation_table/
		// original by: Philip Peterson
		//  revised by: Kevin van Zonneveld (http://kvz.io)
		// bugfixed by: noname
		// bugfixed by: Alex
		// bugfixed by: Marco
		// bugfixed by: madipta
		// bugfixed by: Brett Zamir (http://brett-zamir.me)
		// bugfixed by: T.Wild
		// improved by: KELAN
		// improved by: Brett Zamir (http://brett-zamir.me)
		//    input by: Frank Forte
		//    input by: Ratheous
		//      note 1: It has been decided that we're not going to add global
		//      note 1: dependencies to Locutus, meaning the constants are not
		//      note 1: real constants, but strings instead. Integers are also supported if someone
		//      note 1: chooses to create the constants themselves.
		//   example 1: get_html_translation_table('HTML_SPECIALCHARS')
		//   returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}

		var entities = {}
		var hashMap = {}
		var decimal
		var constMappingTable = {}
		var constMappingQuoteStyle = {}
		var useTable = {}
		var useQuoteStyle = {}

		// Translate arguments
		constMappingTable[0] = 'HTML_SPECIALCHARS'
		constMappingTable[1] = 'HTML_ENTITIES'
		constMappingQuoteStyle[0] = 'ENT_NOQUOTES'
		constMappingQuoteStyle[2] = 'ENT_COMPAT'
		constMappingQuoteStyle[3] = 'ENT_QUOTES'

		useTable = !isNaN(table)
			? constMappingTable[table]
			: table
			? table.toUpperCase()
			: 'HTML_SPECIALCHARS'

		useQuoteStyle = !isNaN(quoteStyle)
			? constMappingQuoteStyle[quoteStyle]
			: quoteStyle
			? quoteStyle.toUpperCase()
			: 'ENT_COMPAT'

		if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
			throw new Error('Table: ' + useTable + ' not supported')
		}

		entities['38'] = '&amp;'
		if (useTable === 'HTML_ENTITIES') {
			entities['160'] = '&nbsp;'
			entities['161'] = '&iexcl;'
			entities['162'] = '&cent;'
			entities['163'] = '&pound;'
			entities['164'] = '&curren;'
			entities['165'] = '&yen;'
			entities['166'] = '&brvbar;'
			entities['167'] = '&sect;'
			entities['168'] = '&uml;'
			entities['169'] = '&copy;'
			entities['170'] = '&ordf;'
			entities['171'] = '&laquo;'
			entities['172'] = '&not;'
			entities['173'] = '&shy;'
			entities['174'] = '&reg;'
			entities['175'] = '&macr;'
			entities['176'] = '&deg;'
			entities['177'] = '&plusmn;'
			entities['178'] = '&sup2;'
			entities['179'] = '&sup3;'
			entities['180'] = '&acute;'
			entities['181'] = '&micro;'
			entities['182'] = '&para;'
			entities['183'] = '&middot;'
			entities['184'] = '&cedil;'
			entities['185'] = '&sup1;'
			entities['186'] = '&ordm;'
			entities['187'] = '&raquo;'
			entities['188'] = '&frac14;'
			entities['189'] = '&frac12;'
			entities['190'] = '&frac34;'
			entities['191'] = '&iquest;'
			entities['192'] = '&Agrave;'
			entities['193'] = '&Aacute;'
			entities['194'] = '&Acirc;'
			entities['195'] = '&Atilde;'
			entities['196'] = '&Auml;'
			entities['197'] = '&Aring;'
			entities['198'] = '&AElig;'
			entities['199'] = '&Ccedil;'
			entities['200'] = '&Egrave;'
			entities['201'] = '&Eacute;'
			entities['202'] = '&Ecirc;'
			entities['203'] = '&Euml;'
			entities['204'] = '&Igrave;'
			entities['205'] = '&Iacute;'
			entities['206'] = '&Icirc;'
			entities['207'] = '&Iuml;'
			entities['208'] = '&ETH;'
			entities['209'] = '&Ntilde;'
			entities['210'] = '&Ograve;'
			entities['211'] = '&Oacute;'
			entities['212'] = '&Ocirc;'
			entities['213'] = '&Otilde;'
			entities['214'] = '&Ouml;'
			entities['215'] = '&times;'
			entities['216'] = '&Oslash;'
			entities['217'] = '&Ugrave;'
			entities['218'] = '&Uacute;'
			entities['219'] = '&Ucirc;'
			entities['220'] = '&Uuml;'
			entities['221'] = '&Yacute;'
			entities['222'] = '&THORN;'
			entities['223'] = '&szlig;'
			entities['224'] = '&agrave;'
			entities['225'] = '&aacute;'
			entities['226'] = '&acirc;'
			entities['227'] = '&atilde;'
			entities['228'] = '&auml;'
			entities['229'] = '&aring;'
			entities['230'] = '&aelig;'
			entities['231'] = '&ccedil;'
			entities['232'] = '&egrave;'
			entities['233'] = '&eacute;'
			entities['234'] = '&ecirc;'
			entities['235'] = '&euml;'
			entities['236'] = '&igrave;'
			entities['237'] = '&iacute;'
			entities['238'] = '&icirc;'
			entities['239'] = '&iuml;'
			entities['240'] = '&eth;'
			entities['241'] = '&ntilde;'
			entities['242'] = '&ograve;'
			entities['243'] = '&oacute;'
			entities['244'] = '&ocirc;'
			entities['245'] = '&otilde;'
			entities['246'] = '&ouml;'
			entities['247'] = '&divide;'
			entities['248'] = '&oslash;'
			entities['249'] = '&ugrave;'
			entities['250'] = '&uacute;'
			entities['251'] = '&ucirc;'
			entities['252'] = '&uuml;'
			entities['253'] = '&yacute;'
			entities['254'] = '&thorn;'
			entities['255'] = '&yuml;'
		}

		if (useQuoteStyle !== 'ENT_NOQUOTES') {
			entities['34'] = '&quot;'
		}
		if (useQuoteStyle === 'ENT_QUOTES') {
			entities['39'] = '&#39;'
		}
		entities['60'] = '&lt;'
		entities['62'] = '&gt;'

		// ascii decimals to real symbols
		for (decimal in entities) {
			if (entities.hasOwnProperty(decimal)) {
			hashMap[String.fromCharCode(decimal)] = entities[decimal]
			}
		}

		return hashMap
	}
})();
