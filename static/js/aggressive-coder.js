/**
 * @since 1.3.18
 */
fw.shortcodesAggressiveCoder = (function ($) {
	var SYMBOL_TABLE = { // @see php class FW_Ext_Shortcodes_Attr_Coder_Aggressive
		first: [
			'‹', '[', ']', '"', "'", '&', '=', '\\', '<', '>'
		],
		second: [
			'ˆ', 'º', '¹', '²', '³', '¯', '´',  'ª', '¨', '˜'
		].map(function(val){ return '‹'+ val +'›'; })
	};

	return {
		encode: encode,
		encodeValue: encode_value,
		decode: decode,
		decodeValue: decode_value,
		canDecode: can_decode
	};

	/**
	 * An implementation of encode from the attr coder json.
	 * https://github.com/ThemeFuse/Unyson-Shortcodes-Extension/blob/v1.3.16/includes/coder/class-fw-ext-shortcodes-attr-coder-json.php#L17
	 *
	 * Mainly used for dynamic encoding of values from fw.OptionsModal().
	 */
	function encode (atts) {
		var encoded = {},
			array_keys = {};

		_.each(atts, function (value, key) {
			key = str_replace('-', '_', key);

			if (_.isObject(value)) {
				value = JSON.stringify(value);
				array_keys[key] = key;
			}

			encoded[key] = encode_value(value);
		});

		if (! _.isEmpty(array_keys)) {
			encoded['_array_keys'] = encode_value(
				JSON.stringify(array_keys)
			);
		}

		encoded['_fw_coder'] = 'aggressive';

		return encoded;
	}

	function encode_value (value) {
		return str_replace(
			SYMBOL_TABLE.first,
			SYMBOL_TABLE.second,
			value
		);
	}

	function decode (atts) {
		if (! can_decode(atts)) {
			return atts;
		}

		atts = _.omit(atts, '_fw_coder');

		var array_keys = {};

		if (atts._array_keys) {
			try {
				array_keys = JSON.parse(decode_value(atts._array_keys));
			} catch (e) {
				console.error('Shortcode attribute decode failed', decode_value(atts._array_keys), e);
				return {};
			}

			atts = _.omit(atts, '_array_keys');
		}

		var decoded = {};

		_.each(atts, function (value, key) {
			try {
				decoded[key] = array_keys[key]
					? JSON.parse(decode_value(value))
					: decode_value(value);
			} catch (e) {
				console.error('Shortcode attribute decode failed', decode_value(value), e);
				return {};
			}
		});

		return decoded;
	}

	function decode_value (encoded_value) {
		return str_replace(
			SYMBOL_TABLE.second.slice().reverse(), // note: .slice() used to prevent reverse the original array
			SYMBOL_TABLE.first.slice().reverse(),
			encoded_value
		);
	}

    function can_decode (atts) {
		return atts._fw_coder && atts._fw_coder == 'aggressive';
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
})();

