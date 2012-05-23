/** Autocomplete control by Koh Zi Chun **/
function getCaretEnd(obj){
	if(typeof obj.selectionEnd != "undefined"){
		return obj.selectionEnd;
	}else if(document.selection&&document.selection.createRange){
		var M=document.selection.createRange();
		var Lp=obj.createTextRange();
		Lp.setEndPoint("EndToEnd",M);
		var rb=Lp.text.length;
		if(rb>obj.value.length){
			return -1;
		}
		return rb;
	}
}
function getCaretStart(obj){
	if(typeof obj.selectionStart != "undefined"){
		return obj.selectionStart;
	}else if(document.selection&&document.selection.createRange){
		var M=document.selection.createRange();
		var Lp=obj.createTextRange();
		try{
			Lp.setEndPoint("EndToStart",M);
			var rb=Lp.text.length;
			if(rb>obj.value.length){
				return -1;
			}
			return rb;
		}catch(e){
			
		}
	}
}
function setCaret(obj,l){
	obj.focus();
	if (obj.setSelectionRange){
		obj.setSelectionRange(l,l);
	}else if(obj.createTextRange){
		m = obj.createTextRange();		
		m.moveStart('character',l);
		m.collapse();
		m.select();
	}
}
function setSelection(obj,s,e){
	try{
		obj.focus();
		if (obj.setSelectionRange){
			obj.setSelectionRange(s,e);
		}else if(obj.createTextRange){
			m = obj.createTextRange();		
			m.moveStart('character',s);
			m.moveEnd('character',e);
			m.select();
		}
	}catch(e){
	}
}
/* Offset position from top of the screen */
function curTop(obj){
	toreturn = 0;
	while(obj){
		toreturn += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return toreturn;
}
function curLeft(obj){
	toreturn = 0;
	while(obj){
		toreturn += obj.offsetLeft;
		obj = obj.offsetParent;
	}
	return toreturn;
}
function getScrollHeight(){
	var $ruler = $('<div style="position:fixed;bottom:0px;top:0px;display:none;">&nbsp;</div>');
	$ruler.appendTo(document.body);
	var tr = $ruler.height()
	$ruler.remove();
	return tr;
	
}
/* ------ End of Offset function ------- */

/* Types Function */

// is a given input a number?
function isNumber(a) {
    return typeof a == 'number' && isFinite(a);
}

/* Object Functions */

function actb(obj,ca){
	/* ---- Public Variables ---- */
	this.actb_timeOut = -1; // Autocomplete Timeout in ms (-1: autocomplete never time out)
	this.actb_lim = 6;    // Number of elements autocomplete can show (-1: no limit)
	this.actb_firstText = false; // should the auto complete be limited to the beginning of keyword?
	this.actb_mouse = true; // Enable Mouse Support
	this.actb_delimiter = [];  // Delimiter for multiple autocomplete. Set it to empty array for single autocomplete
	this.actb_skipSearch = true; // if true, allow "F D Roosevelt" to match "Franklin D Roosevelt"
	this.actb_downShow = true;	// if true, user press down triggers autocomplete
	this.actb_startcheck = 1; // Show widget only after this number of characters is typed in.
	this.actb_onFocusShow = false; // show immediately when focused
	this.actb_multiWord = false;   // multi-word search: might be slow
	this.actb_category = false; // add category support
	/* ---- Public Variables ---- */

	/* ---- Private Variables ---- */
	var actb_delimwords = [];
	var actb_cdelimword = 0;
	var actb_delimchar = [];
	var actb_focus = false;
	var actb_display = false;
	var actb_pos = 0;
	var actb_total = 0;
	var actb_curr = null;
	var actb_rangeu = 0;
	var actb_ranged = 0;
	var actb_bool = [];
	var actb_pre = 0;
	var actb_toid;
	var actb_tomake = false;
	var actb_getpre = "";
	var actb_mouse_on_list = 1;
	var actb_kwcount = 0;
	var actb_caretmove = false;
	var actb_pretext = '';
	this.actb_keywords = [];
	/* ---- Private Variables---- */
	
	this.actb_keywords = ca;
	var actb_self = this;

	actb_curr = obj;

	this.actb_select_array = function(element){
		return element;
	}
	this.actb_display_array = function(element){
		return element;
	}
	this.actb_after_parse = function(after_parse, element) {
		return after_parse;
	}
	this.actb_insert_after = function(obj, pos, element, word){
	}
	this.actb_get_category = function(element){
		return element[0];
	}
	this.actb_get_display = function(){
		return actb_display;
	}

	function actb_clear(evt){
		if (!evt) evt = event;
		$(actb_curr).unbind('blur', actb_clear);
		$(document).unbind('keypress', actb_keypress);
		actb_focus = false;
		actb_removedisp(1);
	}
	function actb_setup(){
		actb_focus = true;
		actb_curr = this;
		$(actb_curr).blur(actb_clear);
		$(document).keypress(actb_keypress);
		if (actb_self.actb_onFocusShow){
			if (actb_curr.value.length > actb_self.actb_startcheck){
				actb_tocomplete(-1);
			}
		}
	}
	function actb_makeRegExp(t){
		if (actb_self.actb_firstText){
			if (actb_self.actb_skipSearch){
				var rt = (t.length ? '()^' : '(.*)');
				for (i=0;i<t.length;++i){
					rt += '('+ RegExp.escape(t.substr(i,1))+')';
					if (i == t.length - 1){
						rt += '(.*)';
					}else{
						rt += '(.*?)';
					}
				}
				return new RegExp(rt,'i');
			}else{
				return new RegExp("()^(" + RegExp.escape(t)+")(.*)", "i");
			}
		}else{
			if (actb_self.actb_skipSearch){
				var rt = (t.length ? '(.*?)' : '(.*)');
				for (i=0;i<t.length;++i){
					rt += '('+ RegExp.escape(t.substr(i,1)) +')';
					if (i == t.length - 1){
						rt += '(.*)';
					}else{
						rt += '(.*?)';
					}
				}
				return new RegExp(rt,'i');
			}else{
				return new RegExp('(.*?)('+RegExp.escape(t)+')(.*)', "i");
			}
		}
	}
	function actb_parse(n){
		if (actb_self.actb_delimiter.length > 0){
			var t = actb_delimwords[actb_cdelimword].trim();
			var plen = actb_delimwords[actb_cdelimword].trim().length;
		}else{
			var t = actb_curr.value;
			var plen = actb_curr.value.length;
		}
		var i;
		var tobuild = '';
		
		if (actb_self.actb_multiWord){
		    var re = t.split(' ');
		    var words = n.split(' ');
		    for (var i=0;i<re.length;++i){
		    	re[i] = actb_makeRegExp(re[i]); 
		    }
		    var p;
		    for (var i=0;i<words.length;++i){
		    	var match = false;
			for (var j=0;j<re.length;++j){
			    if ((p = words[i].match(re[j]))){
				for (k=1;k<p.length;++k){
				    if (k%2==0){
						tobuild += '<font class="actb_match">'+p[k]+'</font>';
				    }else{
						tobuild += p[k];
				    }
				}
				match = true;
				break;
			    }
			}
			if (!match){
			    tobuild += words[i];
			}
			tobuild += ' ';
		    }
		}else{
		    var re = actb_makeRegExp(t);
		    var p = n.match(re);
		    for (i=1;i<p.length;++i){
			    if (i%2==0){
				    tobuild += '<font class="actb_match">'+p[i]+'</font>';
			    }else{
				    tobuild += p[i];
			    }
		    }
		}
		t = t.addslashes();
		return tobuild;
	}
	function create_category(a,cat){
		r = a.insertRow(-1);
		r.className = 'actb_row_category';

		c = r.insertCell(-1);
		c.className = "actb_cell_category";
		c.innerHTML = cat;
	}
	function actb_generate(){
		if (document.getElementById('actb_tat_table')){
			actb_display = false;
			document.body.removeChild(document.getElementById('actb_tat_table'));
		}
		if (actb_kwcount == 0){
			actb_display = false;
			return;
		}
		a = document.createElement('table');
		a.cellSpacing='1px';
		a.cellPadding='2px';
		a.style.position='absolute';
		a.style.top = eval(curTop(actb_curr) + actb_curr.offsetHeight) + "px";
		a.style.left = curLeft(actb_curr) + "px";
		a.id = 'actb_tat_table';
		document.body.appendChild(a);
		var i;
		var first = true;
		var j = 1;
		if (actb_self.actb_mouse){
			a.onmouseout = actb_table_unfocus;
			a.onmouseover = actb_table_focus;
		}
		var counter = 0;
		var pre_cat = false;
		for (i=0;i<actb_self.actb_keywords.length;i++){
			if (actb_bool[i]){
				counter++;
				if (actb_self.actb_category){
					var cur_cat = actb_self.actb_get_category(actb_self.actb_keywords[i]);
					if (cur_cat != pre_cat){
						create_category(a,cur_cat);
						pre_cat = cur_cat;
					}
				}
				r = a.insertRow(-1);
				if ( (first && !actb_tomake) || actb_pre == i ){
					r.className = 'actb_row_selected';
					first = false;
					actb_pos = counter;
				}else{
					r.className = 'actb_row';
				}
				
				r.id = 'tat_tr'+(j);
				c = r.insertCell(-1);
				c.className = "actb_cell";
				c.innerHTML = actb_self.actb_after_parse( actb_parse( actb_self.actb_display_array( actb_self.actb_keywords[i] )), actb_self.actb_keywords[i] );
				c.id = 'tat_td'+(j);
				c.setAttribute('pos',j);
				if (actb_self.actb_mouse){
					c.style.cursor = 'pointer';
					$(c).mousedown(actb_mouseclick);
					c.onmouseover = actb_table_highlight;
				}
				j++;
			}
			if (j - 1 == actb_self.actb_lim && j < actb_total){
				r = a.insertRow(-1);
				r.className="actb_row";
				c = r.insertCell(-1);
				c.className = 'actb_cell_arrow';
				$(c).html('V');
				if (actb_self.actb_mouse){
					c.style.cursor = 'pointer';
					$(c).click(actb_mouse_down);
				}
				break;
			}
		}

			
  			var targetOffset = $(actb_curr).offset().top + $(a).height() -  getScrollHeight() + 50;
			//$('html, body').animate({scrollTop: targetOffset}, 1000, function() {});
			    			
		actb_rangeu = 1;
		actb_ranged = j-1;
		actb_display = true;
		if (actb_pos <= 0) actb_pos = 1;
	}
	function actb_remake(){
		document.body.removeChild(document.getElementById('actb_tat_table'));
		a = document.createElement('table');
		a.cellSpacing='1px';
		a.cellPadding='2px';
		a.style.position='absolute';
		a.style.top = eval(curTop(actb_curr) + actb_curr.offsetHeight) + "px";
		a.style.left = curLeft(actb_curr) + "px";
		a.id = 'actb_tat_table';
		if (actb_self.actb_mouse){
			a.onmouseout= actb_table_unfocus;
			a.onmouseover=actb_table_focus;
		}
		document.body.appendChild(a);
		var i;
		var first = true;
		var j = 1;
		if (actb_rangeu > 1){
			r = a.insertRow(-1);
			r.className = 'actb_row';
			c = r.insertCell(-1);
			c.className = 'actb_cell_arrow';
			$(c).html('/\\');
			if (actb_self.actb_mouse){
				c.style.cursor = 'pointer';
				$(c).click(actb_mouse_up);
			}
		}
		var pre_cat = false;
		for (i=0;i<actb_self.actb_keywords.length;i++){
			if (actb_bool[i]){
				if (j >= actb_rangeu && j <= actb_ranged){
					if (actb_self.actb_category){
						var cur_cat = actb_self.actb_get_category(actb_self.actb_keywords[i]);
						if (cur_cat != pre_cat){
							create_category(a,cur_cat);
							pre_cat = cur_cat;
						}
					}
					r = a.insertRow(-1);
					r.className = 'actb_row';
					r.id = 'tat_tr'+(j);
					c = r.insertCell(-1);
					c.className = "actb_cell";
					c.innerHTML = actb_self.actb_after_parse( actb_parse(actb_self.actb_display_array(actb_self.actb_keywords[i])), actb_self.actb_keywords[i]);
					c.id = 'tat_td'+(j);
					c.setAttribute('pos',j);
					if (actb_self.actb_mouse){
						c.style.cursor = 'pointer';
						$(c).mousedown(actb_mouseclick);
						c.onmouseover = actb_table_highlight;
					}
					j++;
				}else{
					j++;
				}
			}
			if (j > actb_ranged) break;
		}
		if (j-1 < actb_total){
			r = a.insertRow(-1);
			r.className = 'actb_row';
			c = r.insertCell(-1);
			c.className = 'actb_cell_arrow';
			$(c).html('V');
			if (actb_self.actb_mouse){
				c.style.cursor = 'pointer';
				$(c).click(actb_mouse_down);
			}
		}
		
		var targetOffset = $(actb_curr).offset().top + $(a).height() -  getScrollHeight() + 50;
		//$('html, body').animate({scrollTop: targetOffset}, 400, function() {});
	}
	function actb_goup(){
		if (!actb_display) return;
		if (actb_pos == 1) return;
		document.getElementById('tat_tr'+actb_pos).className="actb_row";
		actb_pos--;
		if (actb_pos < actb_rangeu) actb_moveup();
		document.getElementById('tat_tr'+actb_pos).className="actb_row_selected";
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_godown(){
		if (!actb_display){
			if (actb_self.actb_downShow) actb_tocomplete(-1);
			return;
		}
		if (actb_pos == actb_total) return;
		document.getElementById('tat_tr'+actb_pos).className="actb_row";
		actb_pos++;
		if (actb_pos > actb_ranged) actb_movedown();
		document.getElementById('tat_tr'+actb_pos).className="actb_row_selected";
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_movedown(){
		actb_rangeu++;
		actb_ranged++;
		actb_remake();
	}
	function actb_moveup(){
		actb_rangeu--;
		actb_ranged--;
		actb_remake();
	}

	/* Mouse */
	function actb_mouse_down(){
		document.getElementById('tat_tr'+actb_pos).className="actb_row";
		actb_pos++;
		actb_movedown();
		document.getElementById('tat_tr'+actb_pos).className="actb_row_selected";
		actb_curr.focus();
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_mouse_up(evt){
		if (!evt) evt = event;
		if (evt.stopPropagation){
			evt.stopPropagation();
		}else{
			evt.cancelBubble = true;
		}
		document.getElementById('tat_tr'+actb_pos).className="actb_row";
		actb_pos--;
		actb_moveup();
		document.getElementById('tat_tr'+actb_pos).className="actb_row_selected";
		actb_curr.focus();
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list=0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_mouseclick(evt){
		if (!evt) evt = event;
		if (!actb_display) return;
		actb_mouse_on_list = 0;
		actb_pos = this.getAttribute('pos');
		actb_penter();
	}
	function actb_table_focus(){
		actb_mouse_on_list = 1;
	}
	function actb_table_unfocus(){
		actb_mouse_on_list = 0;
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
	}
	function actb_table_highlight(){
		actb_mouse_on_list = 1;
		document.getElementById('tat_tr'+actb_pos).className="actb_row";
		actb_pos = this.getAttribute('pos');
		while (actb_pos < actb_rangeu) actb_moveup();
		while (actb_pos > actb_ranged) actb_movedown();
		document.getElementById('tat_tr'+actb_pos).className="actb_row_selected";
		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
	}
	/* ---- */

	function actb_insertword(a){
		if (actb_self.actb_delimiter.length > 0){
			str = '';
			l=0;
			for (i=0;i<actb_delimwords.length;i++){
				if (actb_cdelimword == i){
					prespace = postspace = '';
					gotbreak = false;
					for (j=0;j<actb_delimwords[i].length;++j){
						if (actb_delimwords[i].charAt(j) != ' '){
							gotbreak = true;
							break;
						}
						prespace += ' ';
					}
					for (j=actb_delimwords[i].length-1;j>=0;--j){
						if (actb_delimwords[i].charAt(j) != ' ') break;
						postspace += ' ';
					}
					str += prespace;
					str += a;
					l = str.length;
					if (gotbreak) str += postspace;
				}else{
					str += actb_delimwords[i];
				}
				if (i != actb_delimwords.length - 1){
					str += actb_delimchar[i];
				}
			}
			actb_curr.value = str;
			setCaret(actb_curr,l);
		}else{
			actb_curr.value = a;
		}
		actb_mouse_on_list = 0;
		actb_removedisp();
	}
	function actb_penter(){
		if (!actb_display) return;
		actb_display = false;
		var word = '';
		var c = 0;
		for (var i=0;i<=actb_self.actb_keywords.length;i++){
			if (actb_bool[i]) c++;
			if (c == actb_pos){
				word = actb_self.actb_select_array(actb_self.actb_keywords[i]);
				break;
			}
		}
		actb_insertword(word);
		actb_self.actb_insert_after(actb_curr, i, actb_self.actb_keywords[i], actb_self.actb_select_array(actb_self.actb_keywords[i]) );
		l = getCaretStart(actb_curr);
	}
	function actb_removedisp(force){
		if (actb_mouse_on_list==0 || force){
			actb_display = 0;
			if (document.getElementById('actb_tat_table')){ document.body.removeChild(document.getElementById('actb_tat_table')); }
			if (actb_toid) clearTimeout(actb_toid);
		}
	}
	function actb_keypress(e){
		if (!e) e = event;
		if (actb_caretmove){
			e.stopPropagation();
		}
		return !actb_caretmove;
	}
	function actb_checkkey(evt){
		if (!evt) evt = event;
		a = evt.keyCode || evt.which;
		evt.stopPropagation();
		
		caret_pos_start = getCaretStart(actb_curr);
		actb_caretmove = 0;
		switch (a){
			case 38:
				actb_goup();
				actb_caretmove = 1;
				return false;
				break;
			case 40:
				actb_godown();
				actb_caretmove = 1;
				return false;
				break;
			case 13: case 9:
				if (actb_display){
					actb_caretmove = 1;
					actb_penter();
					return false;
				}else{
					return true;
				}
				break;
			case 27:
				if (actb_display){
					actb_removedisp(true);
					return false;
				}
			default:
				setTimeout(function(){actb_tocomplete(a)},50);
				break;
		}
	}
	function actb_test(re, word){
	    if (actb_self.actb_multiWord){
		re = re.split(' ');
		word = word.split(' ');
		if (re.length > word.length) return false;
		var used = [];
		for (var j=0;j<word.length;++j){ used[j] = false; }
		for (var i=0;i<re.length;++i){
		    var reg = actb_makeRegExp(re[i]);
		    var match = false;
		    for (var j=0;j<word.length;++j){
			if (!used[j] && reg.test(word[j])){
			    match = true;
			    used[j] = true;
			    break;
			}
		    }
		    if (!match) return false;
		}
		return true;
	    }else{
		return re.test(word);
	    }
	}
	function actb_tocomplete(kc){
		if (kc == 38 || kc == 40 || kc == 13) return;
		var i;
		if (actb_display){ 
			var word = 0;
			var c = 0;
			for (var i=0;i<=actb_self.actb_keywords.length;i++){
				if (actb_bool[i]) c++;
				if (c == actb_pos){
					word = i;
					break;
				}
			}
			actb_pre = word;
		}else{ actb_pre = -1};
		if (actb_curr.value == '' && kc != -1){
			actb_mouse_on_list = 0;
			actb_removedisp();
			return;
		}
		if (actb_self.actb_delimiter.length > 0){
			caret_pos_start = getCaretStart(actb_curr);
			caret_pos_end = getCaretEnd(actb_curr);
			
			delim_split = '';
			for (i=0;i<actb_self.actb_delimiter.length;i++){
				delim_split += actb_self.actb_delimiter[i];
			}
			delim_split = delim_split.addslashes();
			delim_split_rx = new RegExp("(["+delim_split+"])");
			c = 0;
			actb_delimwords = new Array();
			actb_delimwords[0] = '';
			for (i=0,j=actb_curr.value.length;i<actb_curr.value.length;i++,j--){
				if (actb_curr.value.substr(i,j).search(delim_split_rx) == 0){
					ma = actb_curr.value.substr(i,j).match(delim_split_rx);
					actb_delimchar[c] = ma[1];
					c++;
					actb_delimwords[c] = '';
				}else{
					actb_delimwords[c] += actb_curr.value.charAt(i);
				}
			}

			var l = 0;
			actb_cdelimword = -1;
			for (i=0;i<actb_delimwords.length;i++){
				if (caret_pos_end >= l && caret_pos_end <= l + actb_delimwords[i].length){
					actb_cdelimword = i;
				}
				l+=actb_delimwords[i].length + 1;
			}
			var ot = actb_delimwords[actb_cdelimword].trim(); 
			var t = actb_delimwords[actb_cdelimword].addslashes().trim();
		}else{
			var ot = actb_curr.value;
			var t = actb_curr.value;
		}
		if (ot.length == 0 && kc != -1){
			actb_mouse_on_list = 0;
			actb_removedisp();
		}
		if (ot.length < actb_self.actb_startcheck && kc != -1) return this;
		var subset = false;
		if (actb_pretext != '' && t.indexOf(actb_pretext) != -1){
		    subset = true;
		}

		actb_pretext = t;
		if (actb_self.actb_multiWord){
		    var re = t;
		}else{
		    var re = actb_makeRegExp(t);
		}
		t = t.addslashes();
		
		actb_total = 0;
		actb_tomake = false;
		actb_kwcount = 0;
		for (i=0;i<actb_self.actb_keywords.length;i++){
			if (!subset || actb_bool[i]){
			    actb_bool[i] = actb_test(re, actb_self.actb_select_array(actb_self.actb_keywords[i]));
			}
			if (actb_bool[i]){
			    actb_total++;
			    if (actb_pre == i) actb_tomake = true;
			}
		}
		actb_kwcount = actb_total;

		if (actb_toid) clearTimeout(actb_toid);
		if (actb_self.actb_timeOut > 0) actb_toid = setTimeout(function(){actb_mouse_on_list = 0;actb_removedisp();},actb_self.actb_timeOut);
		actb_generate();
	}

	$(actb_curr).focus(actb_setup);
	$(actb_curr).keydown(actb_checkkey);

	this.actb_tocomplete_inspect = function(kc){
		if (actb_focus) {
			actb_pretext = '';
			actb_tocomplete(kc);
		}
	}
	this.actb_getCaret = function(){
		return actb_caretmove;
	}
	
	return this;
}