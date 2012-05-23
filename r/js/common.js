Date.prototype.friendlyDate = function() {
	return this.toString('yyyy/dd');
}

var timeChunk = [
	[60 * 60 * 24 * 365 , 'y'],
	[60 * 60 * 24 * 30 , 'm'],
	[60 * 60 * 24 * 7, 'w'],
	[60 * 60 * 24 , 'd'],
	[60 * 60 , 'h'],
	[60 , 'm'],
	[1, 's'],
];
var monthString = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

Date.prototype.timeSince = function() {
	var since = Math.floor( ((new Date()).getTime() - this.getTime()) / 1000);
	var count, secs, name,i;
	for (i=0;i<timeChunk.length;++i) {
		secs = timeChunk[i][0];
		name = timeChunk[i][1];
		if ((count = Math.floor(since / secs)) != 0) {
			break;
		}
	}
	var print = count + name;
	//if (count > 1) print += 's';

	if (i + 1 < timeChunk.length) {
		var secs2 = timeChunk[i+1][0];
		var name2 = timeChunk[i+1][1];
		var count2;
		if ( (count2 = Math.floor((since - (secs * count)) / secs2)) != 0) {
			print += ' ' + count2 + name2;
			//if (count2 > 1) print += 's';
		}
	}
	if (i < 4) {
		return (this.getDay() + ' ' + monthString[this.getMonth()]) + ' ('+print + ' ago)';
	} else {
		return print + ' ago';
	}
}


String.prototype.addslashes = function(){
	return this.replace(/(["\\\.\|\[\]\^\*\+\?\$\(\)])/g, '\\$1');
}
String.prototype.trim = function () {
    return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
};
String.prototype.nl2br = function() {
	return (this + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
}
String.prototype.money = function() {
	return (parseInt(this)).money();
}
RegExp.escape = function(text) {
  if (!arguments.callee.sRE) {
    var specials = [
      '/', '.', '*', '+', '?', '|',
      '(', ')', '[', ']', '{', '}', '\\'
    ];
    arguments.callee.sRE = new RegExp(
      '(\\' + specials.join('|\\') + ')', 'g'
    );
  }
  return text.replace(arguments.callee.sRE, '\\$1');
}
Number.prototype.money = function() {
	var tr = this / 100;
	if (tr < 0){
		tr = '-$' + (-tr);
	}else{
		tr = '$' + tr;
	}
	if (this % 100 == 0){
		tr += '.00';
	}else if(this % 10 == 0){
		tr += '0';
	}
	return tr;
}
Array.prototype.has=function(v) {
	var i;
	for (i=0;i<this.length;i++) {
		if (this[i]==v) return i;
	}
	return false;
}
Array.prototype.remove = function(from, to) {
	if (!(this instanceof Array)) return;
	var rest = this.slice((to || from) + 1 || this.length);
	this.length = from < 0 ? this.length + from : from;
	return this.push.apply(this, rest);
};
Array.prototype.copy = function () {var a = new Array(); for (var property in this) {a[property] = typeof (this[property]) == 'object' ? this[property] : this[property]} return a; }

function mapReduce(array, map, reduce) {
	var tr = [];
	for(var key in array){
		mapReduce_store = [];
		map.call(array[key],key);
		tr = tr.concat(mapReduce_store );
	}
	var comp = mapReduce_compile(tr);
	tr = {};
	for (var key in comp){
		tr[key] = reduce(key,comp[key]);
	}
	mapReduce_store = false;
	return tr;
}
var mapReduce_store = false;
function mapReduce_emit(key, value) {
	mapReduce_store.push({key:key, value:value});
}
function mapReduce_compile(list) {
	var tr = {};
 	for (var i=0; i<list.length; i++) {
		var key = list[i].key;
		var value = list[i].value;
  		if (!tr[key]) {
			tr[key] = [];
		}

		tr[key].push(value);
	}
	return tr;
}
var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
var StringMaker = function () {
	this.parts = [];
	this.length = 0;
	this.append = function (s) {
		this.parts.push(s);
		this.length += s.length;
	}
	this.prepend = function (s) {
		this.parts.unshift(s);
		this.length += s.length;
	}
	this.toString = function () {
		return this.parts.join('');
	}
}
function encode64(input) {
	var output = new StringMaker();
	var chr1, chr2, chr3;
	var enc1, enc2, enc3, enc4;
	var i = 0;
 
	while (i < input.length) {
		chr1 = input.charCodeAt(i++);
		chr2 = input.charCodeAt(i++);
		chr3 = input.charCodeAt(i++);
 
		enc1 = chr1 >> 2;
		enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		enc4 = chr3 & 63;
 
		if (isNaN(chr2)) {
			enc3 = enc4 = 64;
		} else if (isNaN(chr3)) {
			enc4 = 64;
		}
 
		output.append(keyStr.charAt(enc1) + keyStr.charAt(enc2) + keyStr.charAt(enc3) + keyStr.charAt(enc4));
   }
   
   return output.toString();
}


function get_html_translation_table(c,d){var a={},i={},e,g={},f={},b={},h={};g[0]="HTML_SPECIALCHARS";g[1]="HTML_ENTITIES";f[0]="ENT_NOQUOTES";f[2]="ENT_COMPAT";f[3]="ENT_QUOTES";b=!isNaN(c)?g[c]:c?c.toUpperCase():"HTML_SPECIALCHARS";h=!isNaN(d)?f[d]:d?d.toUpperCase():"ENT_COMPAT";if(b!=="HTML_SPECIALCHARS"&&b!=="HTML_ENTITIES")throw Error("Table: "+b+" not supported");a["38"]="&amp;";b==="HTML_ENTITIES"&&(a["160"]="&nbsp;",a["161"]="&iexcl;",a["162"]="&cent;",a["163"]="&pound;",a["164"]="&curren;",
a["165"]="&yen;",a["166"]="&brvbar;",a["167"]="&sect;",a["168"]="&uml;",a["169"]="&copy;",a["170"]="&ordf;",a["171"]="&laquo;",a["172"]="&not;",a["173"]="&shy;",a["174"]="&reg;",a["175"]="&macr;",a["176"]="&deg;",a["177"]="&plusmn;",a["178"]="&sup2;",a["179"]="&sup3;",a["180"]="&acute;",a["181"]="&micro;",a["182"]="&para;",a["183"]="&middot;",a["184"]="&cedil;",a["185"]="&sup1;",a["186"]="&ordm;",a["187"]="&raquo;",a["188"]="&frac14;",a["189"]="&frac12;",a["190"]="&frac34;",a["191"]="&iquest;",a["192"]=
"&Agrave;",a["193"]="&Aacute;",a["194"]="&Acirc;",a["195"]="&Atilde;",a["196"]="&Auml;",a["197"]="&Aring;",a["198"]="&AElig;",a["199"]="&Ccedil;",a["200"]="&Egrave;",a["201"]="&Eacute;",a["202"]="&Ecirc;",a["203"]="&Euml;",a["204"]="&Igrave;",a["205"]="&Iacute;",a["206"]="&Icirc;",a["207"]="&Iuml;",a["208"]="&ETH;",a["209"]="&Ntilde;",a["210"]="&Ograve;",a["211"]="&Oacute;",a["212"]="&Ocirc;",a["213"]="&Otilde;",a["214"]="&Ouml;",a["215"]="&times;",a["216"]="&Oslash;",a["217"]="&Ugrave;",a["218"]=
"&Uacute;",a["219"]="&Ucirc;",a["220"]="&Uuml;",a["221"]="&Yacute;",a["222"]="&THORN;",a["223"]="&szlig;",a["224"]="&agrave;",a["225"]="&aacute;",a["226"]="&acirc;",a["227"]="&atilde;",a["228"]="&auml;",a["229"]="&aring;",a["230"]="&aelig;",a["231"]="&ccedil;",a["232"]="&egrave;",a["233"]="&eacute;",a["234"]="&ecirc;",a["235"]="&euml;",a["236"]="&igrave;",a["237"]="&iacute;",a["238"]="&icirc;",a["239"]="&iuml;",a["240"]="&eth;",a["241"]="&ntilde;",a["242"]="&ograve;",a["243"]="&oacute;",a["244"]=
"&ocirc;",a["245"]="&otilde;",a["246"]="&ouml;",a["247"]="&divide;",a["248"]="&oslash;",a["249"]="&ugrave;",a["250"]="&uacute;",a["251"]="&ucirc;",a["252"]="&uuml;",a["253"]="&yacute;",a["254"]="&thorn;",a["255"]="&yuml;");h!=="ENT_NOQUOTES"&&(a["34"]="&quot;");h==="ENT_QUOTES"&&(a["39"]="&#39;");a["60"]="&lt;";a["62"]="&gt;";for(e in a)a.hasOwnProperty(e)&&(i[String.fromCharCode(e)]=a[e]);return i};


String.prototype.html_entity_decode = function(quote_style) {
	var string = this;
    var hash_map = {},
        symbol = '',
        tmp_str = '',
        entity = '';
    tmp_str = string.toString();
 
    if (false === (hash_map = get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }
 
    // fix &amp; problem
    // http://phpjs.org/functions/get_html_translation_table:416#comment_97660
    delete(hash_map['&']);
    hash_map['&'] = '&amp;';
 
    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(entity).join(symbol);
    }
    tmp_str = tmp_str.split('&#039;').join("'");
 
    return tmp_str;
}
String.prototype.htmlentities = function(quote_style, charset, double_encode) {
	var string = this;
    var hash_map = get_html_translation_table('HTML_ENTITIES', quote_style),
        symbol = '';
    string = string == null ? '' : string + '';
 
    if (!hash_map) {
        return false;
    }
    
    if (quote_style && quote_style === 'ENT_QUOTES') {
        hash_map["'"] = '&#039;';
    }
    
    if (!!double_encode || double_encode == null) {
        for (symbol in hash_map) {
            if (hash_map.hasOwnProperty(symbol)) {
                string = string.split(symbol).join(hash_map[symbol]);
            }
        }
    } else {
        string = string.replace(/([\s\S]*?)(&(?:#\d+|#x[\da-f]+|[a-zA-Z][\da-z]*);|$)/g, function (ignore, text, entity) {
            for (symbol in hash_map) {
                if (hash_map.hasOwnProperty(symbol)) {
                    text = text.split(symbol).join(hash_map[symbol]);
                }
            }
            
            return text + entity;
        });
    }
 
    return string;
};

if (typeof jQuery != 'undefined') {
	(function($) {
		$.fn.sorted = function(customOptions) {
			var options = {
				reversed: false,
				by: function(a) {
					return a.text();
				}
			};
			$.extend(options, customOptions);
		
			$data = $(this);
			arr = $data.get();
			arr.sort(function(a, b) {
				
			   	var valA = options.by($(a));
			   	var valB = options.by($(b));
				if (options.reversed) {
					return (valA < valB) ? 1 : (valA > valB) ? -1 : 0;				
				} else {		
					return (valA < valB) ? -1 : (valA > valB) ? 1 : 0;	
				}
			});
			return $(arr);
		};
		$.fn.select = function(){
			if ($(this).size() == 0 || !$(this).val().length) return $(this);
			setSelection($(this).get(0), 0, $(this).val().length);
			
			return $(this);
		}
	})(jQuery);
	(function($) {
	  var cache = [];
	  // Arguments are image paths relative to the current page.
	  $.preLoadImages = function(x) {
			if (typeof x == 'undefined') x = arguments;
	    var args_len = x.length;
	    for (var i = args_len; i--;) {
	      var cacheImage = document.createElement('img');
	      cacheImage.src = x[i];
	      cache.push(cacheImage);
	    }
	  }
	})(jQuery);
}