/**********************************************************************/
/*********** Bitrix JS Core library ver 0.9.0 beta ********************/
/**********************************************************************/

;(function(window){

if (!!window.BX && !!window.BX.extend)
	return;

window.BXDEBUG = false;

var _bxtmp;
if (!!window.BX)
{
	_bxtmp = window.BX;
}

window.BX = function(node, bCache)
{
	if (BX.type.isNotEmptyString(node))
	{
		var ob;

		if (!!bCache && null != NODECACHE[node])
			ob = NODECACHE[node];
		ob = ob || document.getElementById(node);
		if (!!bCache)
			NODECACHE[node] = ob;

		return ob;
	}
	else if (BX.type.isDomNode(node))
		return node;
	else if (BX.type.isFunction(node))
		return BX.ready(node);

	return null;
};

// language utility
BX.message = function(mess)
{
	if (BX.type.isString(mess))
	{
		if (typeof BX.message[mess] == 'undefined')
			BX.debug('message undefined: ' + mess);
		return BX.message[mess];
	}
	else
	{
		for (var i in mess)
		{
			BX.message[i]=mess[i];
		}
		return true;
	}
};

if(!!_bxtmp)
{
	for(var i in _bxtmp)
	{
		if(!BX[i])
		{
			BX[i]=_bxtmp[i];
		}
		else if(i=='message')
		{
			for(var j in _bxtmp[i])
			{
				BX.message[j]=_bxtmp[i][j];
			}
		}
		_bxtmp = null;
	}
}

var

/* ready */
__readyHandler = null,
readyBound = false,
readyList = [],

/* list of registered proxy functions */
proxySalt = Math.random(),
proxyId = 1,
proxyList = [],
deferList = [],

/* getElementById cache */
NODECACHE = {},

/* List of denied event handlers */
deniedEvents = [],

/* list of registered event handlers */
eventsList = [],

/* list of registered custom events */
customEvents = {},

/* list of external garbage collectors */
garbageCollectors = [],

/* list of loaded CSS files */
cssList = [],

/* list of loaded JS kernel files */
arKernelJS = [],

/* browser detection */
bSafari = navigator.userAgent.toLowerCase().indexOf('webkit') != -1,
bOpera = navigator.userAgent.toLowerCase().indexOf('opera') != -1,
bFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') != -1,
bChrome = navigator.userAgent.toLowerCase().indexOf('chrome') != -1,
bIE = document.attachEvent && !bOpera,

/* regexps */
r = {
	script: /<script([^>]*)>/i,
	script_src: /src=["\']([^"\']+)["\']/i,
	space: /\s+/,
	ltrim: /^[\s\r\n]+/g,
	rtrim: /[\s\r\n]+$/g,
	style: /<link.*?(rel="stylesheet"|type="text\/css")[^>]*>/i,
	style_href: /href=["\']([^"\']+)["\']/i
},

eventTypes = {
	click: 'MouseEvent',
	dblclick: 'MouseEvent',
	mousedown: 'MouseEvent',
	mousemove: 'MouseEvent',
	mouseout: 'MouseEvent',
	mouseover: 'MouseEvent',
	mouseup: 'MouseEvent',
	focus: 'MouseEvent',
	blur: 'MouseEvent'
},

lastWait = [],

CHECK_FORM_ELEMENTS = {tagName: /^INPUT|SELECT|TEXTAREA|BUTTON$/i};

BX.MSLEFT = 1;
BX.MSMIDDLE = 2;
BX.MSRIGHT = 4;

BX.ext = function(ob) {for (var i in ob) this[i] = ob[i];}

/* OO emulation utility */
BX.extend = function(child, parent)
{
	var f = function() {};
	f.prototype = parent.prototype;

	child.prototype = new f();
	child.prototype.constructor = child;

	child.superclass = parent.prototype;
	if(parent.prototype.constructor == Object.prototype.constructor)
	{
		parent.prototype.constructor = parent;
	}
}

BX.debug = function()
{
	if (window.BXDEBUG)
	{
		if (window.console && window.console.log)
			console.log('BX.debug: ', arguments.length > 0 ? arguments : arguments[0]);
	}
}

BX.is_subclass_of = function(ob, parent_class)
{
	if (ob instanceof parent_class)
		return true;

	if (parent_class.superclass)
		return BX.is_subclass_of(ob, parent_class.superclass);

	return false;
}

BX.bitrix_sessid = function() {return BX.message.bitrix_sessid;}

/* DOM manipulation */
BX.create = function(tag, data, context)
{
	context = context || document;

	if (null == data && typeof tag == 'object' && tag.constructor !== String)
	{
		data = tag; tag = tag.tag;
	}

	var elem;
	if (BX.browser.IsIE() && !BX.browser.IsIE9() && null != data && null != data.props && (data.props.name || data.props.id))
	{
		elem = context.createElement('<' + tag + (data.props.name ? ' name="' + data.props.name + '"' : '') + (data.props.id ? ' id="' + data.props.id + '"' : '') + '>');
	}
	else
	{
		elem = context.createElement(tag);
	}

	return data ? BX.adjust(elem, data) : elem;
}

BX.adjust = function(elem, data)
{
	var j,len;

	if (!elem.nodeType)
		return null;

	if (elem.nodeType == 9)
		elem = elem.body;

	if (data.attrs)
	{
		for (j in data.attrs)
		{
			if (j == 'class' || j == 'className')
				elem.className = data.attrs[j];
			else if (j == 'for')
				elem.htmlFor = data.attrs[j];
			else if(data.attrs[j] == "")
				elem.removeAttribute(j);
			else
				elem.setAttribute(j, data.attrs[j]);
		}
	}

	if (data.style)
	{
		for (j in data.style)
			elem.style[j] = data.style[j];
	}

	if (data.props)
	{
		for (j in data.props)
			elem[j] = data.props[j];
	}

	if (data.events)
	{
		for (j in data.events)
			BX.bind(elem, j, data.events[j]);
	}

	if (data.children && data.children.length > 0)
	{
		for (j=0,len=data.children.length; j<len; j++)
		{
			if (BX.type.isNotEmptyString(data.children[j]))
				elem.innerHTML += data.children[j];
			else if (BX.type.isElementNode(data.children[j]))
				elem.appendChild(data.children[j]);
		}
	}
	else if (data.text)
	{
		BX.cleanNode(elem);
		elem.appendChild((elem.ownerDocument || document).createTextNode(data.text));
	}
	else if (data.html)
	{
		elem.innerHTML = data.html;
	}

	return elem;
}

BX.remove = function(ob)
{
	if (ob && null != ob.parentNode)
		ob.parentNode.removeChild(ob);
	ob = null;
	return null;
}

BX.cleanNode = function(node, bSuicide)
{
	node = BX(node);
	bSuicide = !!bSuicide;

	if (node && node.childNodes)
	{
		while(node.childNodes.length > 0)
			node.removeChild(node.firstChild);
	}

	if (node && bSuicide)
	{
		node = BX.remove(node);
	}

	return node;
}

BX.addClass = function(ob, value)
{
	var classNames;
	ob = BX(ob)

	value = BX.util.trim(value);
	if (value == '')
		return ob;

	if (ob)
	{
		if (!ob.className)
		{
			ob.className = value
		}
		else if (!!ob.classList && value.indexOf(' ') < 0)
		{
			ob.classList.add(value);
		}
		else
		{
			classNames = (value || "").split(r.space);

			var className = " " + ob.className + " ";
			for (var j = 0, cl = classNames.length; j < cl; j++)
			{
				if (className.indexOf(" " + classNames[j] + " ") < 0)
				{
					ob.className += " " + classNames[j];
				}
			}
		}
	}

	return ob;
}

BX.removeClass = function(ob, value)
{
	ob = BX(ob);
	if (ob)
	{
		if (ob.className && !!value)
		{
			if (BX.type.isString(value))
			{
				if (!!ob.classList && value.indexOf(' ') < 0)
				{
					ob.classList.remove(value);
				}
				else
				{
					var classNames = value.split(r.space), className = " " + ob.className + " ";
					for (var j = 0, cl = classNames.length; j < cl; j++)
					{
						className = className.replace(" " + classNames[j] + " ", " ");
					}

					ob.className = BX.util.trim(className);
				}
			}
			else
			{
				ob.className = "";
			}
		}
	}

	return ob;
}

BX.toggleClass = function(ob, value)
{
	var className;
	if (BX.type.isArray(value))
	{
		className = ' ' + ob.className + ' ';
		for (var j = 0, len = value.length; j < len; j++)
		{
			if (BX.hasClass(ob, value[j]))
			{
				className = (' ' + className + ' ').replace(' ' + value[j] + ' ', ' ');
				className += ' ' + value[j >= len-1 ? 0 : j+1];

				j--;
				break;
			}
		}

		if (j == len)
			ob.className += ' ' + value[0];
		else
			ob.className = className;

		ob.className = BX.util.trim(ob.className);
	}
	else if (BX.type.isNotEmptyString(value))
	{
		if (!!ob.classList)
		{
			ob.classList.toggle(value);
		}
		else
		{
			className = ob.className;
			if (BX.hasClass(ob, value))
			{
				className = (' ' + className + ' ').replace(' ' + value + ' ', ' ');
			}
			else
			{
				className += ' ' + value;
			}

			ob.className = BX.util.trim(className);
		}
	}

	return ob;
}

BX.hasClass = function(el, className)
{
	if (!el || !BX.type.isDomNode(el))
	{
		BX.debug(el);
		return false;
	}

	if (!el.className || !className)
	{
		return false;
	}

	if (!!el.classList && !!className && className.indexOf(' ') < 0)
	{
		return el.classList.contains(BX.util.trim(className));
	}
	else
		return ((" " + el.className + " ").indexOf(" " + className + " ")) >= 0;
}

BX.hoverEvents = function(el)
{
	if (el)
		return BX.adjust(el, {events: BX.hoverEvents()});
	else
		return {mouseover: BX.hoverEventsHover, mouseout: BX.hoverEventsHout};
}

BX.hoverEventsHover = function(){BX.addClass(this,'bx-hover');this.BXHOVER=true;}
BX.hoverEventsHout = function(){BX.removeClass(this,'bx-hover');this.BXHOVER=false;}

BX.focusEvents = function(el)
{
	if (el)
		return BX.adjust(el, {events: BX.focusEvents()});
	else
		return {mouseover: BX.focusEventsFocus, mouseout: BX.focusEventsBlur};
}

BX.focusEventsFocus = function(){BX.addClass(this,'bx-focus');this.BXFOCUS=true;}
BX.focusEventsBlur = function(){BX.removeClass(this,'bx-focus');this.BXFOCUS=false;}

BX.setUnselectable = function(node)
{
	BX.addClass(node, 'bx-unselectable');
	node.setAttribute('unSelectable', 'on');
}

BX.setSelectable = function(node)
{
	BX.removeClass(node, 'bx-unselectable');
	node.removeAttribute('unSelectable');
}

BX.styleIEPropertyName = function(name)
{
	if (name == 'float')
		name = BX.browser.IsIE() ? 'styleFloat' : 'cssFloat';
	else
	{
		var res = BX.browser.isPropertySupported(name);
		if (res)
		{
			name = res;
		}
		else
		{
			var reg = /(\-([a-z]){1})/g;
			if (reg.test(name))
			{
				name = name.replace(reg, function () {return arguments[2].toUpperCase();});
			}
		}
	}
	return name;
}

/* CSS-notation should be used here */
BX.style = function(el, property, value)
{
	if (!BX.type.isElementNode(el))
		return null;

	if (value == null)
	{
		var res;

		if(el.currentStyle)
			res = el.currentStyle[BX.styleIEPropertyName(property)];
		else if(window.getComputedStyle)
		{
			var q = BX.browser.isPropertySupported(property, true);
			if (!!q)
				property = q;

			res = BX.GetContext(el).getComputedStyle(el, null).getPropertyValue(property);
		}

		if(!res)
			res = '';
		return res;
	}
	else
	{
		el.style[BX.styleIEPropertyName(property)] = value;
		return el;
	}
}

BX.focus = function(el)
{
	try
	{
		el.focus();
		return true;
	}
	catch (e)
	{
		return false;
	}
}

BX.firstChild = function(el)
{
	var e = el.firstChild;
	while (e && !BX.type.isElementNode(e))
	{
		e = e.nextSibling;
	}

	return e;
}

BX.lastChild = function(el)
{
	var e = el.lastChild;
	while (e && !BX.type.isElementNode(e))
	{
		e = e.previousSibling;
	}

	return e;
}

BX.previousSibling = function(el)
{
	var e = el.previousSibling;
	while (e && !BX.type.isElementNode(e))
	{
		var e = e.previousSibling;
	}

	return e;
}

BX.nextSibling = function(el)
{
	var e = el.nextSibling;
	while (e && !BX.type.isElementNode(e))
	{
		var e = e.nextSibling;
	}

	return e;
}

/*
	params: {
		tagName|tag : 'tagName',
		className|class : 'className',
		attribute : {attribute : value, attribute : value} | attribute | [attribute, attribute....],
		property : {prop: value, prop: value} | prop | [prop, prop]
	}

	all values can be RegExps or strings
*/
BX.findChildren = function(obj, params, recursive)
{
	return BX.findChild(obj, params, recursive, true);
}

BX.findChild = function(obj, params, recursive, get_all)
{
	if(!obj || !obj.childNodes) return null;

	recursive = !!recursive; get_all = !!get_all;

	var n = obj.childNodes.length, result = [];

	for (var j=0; j<n; j++)
	{
		var child = obj.childNodes[j];

		if (_checkNode(child, params))
		{
			if (get_all)
				result.push(child)
			else
				return child;
		}

		if(recursive == true)
		{
			var res = BX.findChild(child, params, recursive, get_all);
			if (res)
			{
				if (get_all)
					result = BX.util.array_merge(result, res);
				else
					return res;
			}
		}
	}

	if (get_all || result.length > 0)
		return result;
	else
		return null;
}

BX.findParent = function(obj, params, maxParent)
{
	if(!obj)
		return null;

	var o = obj;
	while(o.parentNode)
	{
		var parent = o.parentNode;

		if (_checkNode(parent, params))
			return parent;

		o = parent;

		if (!!maxParent &&
			(BX.type.isFunction(maxParent)
				|| typeof maxParent == 'object'))
		{
			if (BX.type.isElementNode(maxParent))
			{
				if (o == maxParent)
					break;
			}
			else
			{
				if (_checkNode(o, maxParent))
					break;
			}
		}
	}
	return null;
}

BX.findNextSibling = function(obj, params)
{
	if(!obj)
		return null;
	var o = obj;
	while(o.nextSibling)
	{
		var sibling = o.nextSibling;
		if (_checkNode(sibling, params))
			return sibling;
		o = sibling;
	}
	return null;
}

BX.findPreviousSibling = function(obj, params)
{
	if(!obj)
		return null;

	var o = obj;
	while(o.previousSibling)
	{
		var sibling = o.previousSibling;
		if(_checkNode(sibling, params))
			return sibling;
		o = sibling;
	}
	return null;
}

BX.findFormElements = function(form)
{
	if (BX.type.isString(form))
		form = document.forms[form]||BX(form);

	var res = [];

	if (BX.type.isElementNode(form))
	{
		if (form.tagName.toUpperCase() == 'FORM')
		{
			res = form.elements;
		}
		else
		{
			res = BX.findChildren(form, CHECK_FORM_ELEMENTS, true);
		}
	}

	return res;
}

BX.clone = function(obj, bCopyObj)
{
	var _obj, i, l;
	if (bCopyObj !== false)
		bCopyObj = true;

	if (obj === null)
		return null;

	if (BX.type.isDomNode(obj))
	{
		_obj = obj.cloneNode(bCopyObj);
	}
	else if (typeof obj == 'object')
	{
		if (BX.type.isArray(obj))
		{
			_obj = [];
			for (i=0,l=obj.length;i<l;i++)
			{
				if (typeof obj[i] == "object" && bCopyObj)
					_obj[i] = BX.clone(obj[i], bCopyObj);
				else
					_obj[i] = obj[i];
			}
		}
		else
		{
			_obj =  {};
			if (obj.constructor)
			{
				if (obj.constructor === Date)
					_obj = new Date(obj);
				else
					_obj = new obj.constructor();
			}

			for (i in obj)
			{
				if (typeof obj[i] == "object" && bCopyObj)
					_obj[i] = BX.clone(obj[i], bCopyObj);
				else
					_obj[i] = obj[i];
			}
		}

	}
	else
	{
		_obj = obj;
	}

	return _obj;
}

/* events */
BX.bind = function(el, evname, func)
{
	if (!el)
		return;

	if (evname === 'mousewheel')
		BX.bind(el, 'DOMMouseScroll', func);
	else if (evname === 'transitionend')
	{
		BX.bind(el, 'webkitTransitionEnd', func);
		BX.bind(el, 'msTransitionEnd', func);
		BX.bind(el, 'oTransitionEnd', func);
		// IE8-9 doesn't support this feature!
	}

	if (el.addEventListener)
		el.addEventListener(evname, func, false);
	else if(el.attachEvent) // IE
		el.attachEvent("on" + evname, BX.proxy(func, el));
	else
		el["on" + evname] = func;

	eventsList[eventsList.length] = {'element': el, 'event': evname, 'fn': func};
}

BX.unbind = function(el, evname, func)
{
	if (!el)
		return;

	if (evname === 'mousewheel')
		BX.unbind(el, 'DOMMouseScroll', func);

	if(el.removeEventListener) // Gecko / W3C
		el.removeEventListener(evname, func, false);
	else if(el.detachEvent) // IE
		el.detachEvent("on" + evname, BX.proxy(func, el));
	else
		el["on" + evname] = null;
}

BX.getEventButton = function(e)
{
	e = e || window.event;

	var flags = 0;

	if (typeof e.which != 'undefined')
	{
		switch (e.which)
		{
			case 1: flags = flags|BX.MSLEFT; break;
			case 2: flags = flags|BX.MSMIDDLE; break;
			case 3: flags = flags|BX.MSRIGHT; break;
		}
	}
	else if (typeof e.button != 'undefined')
	{
		flags = event.button;
	}

	return flags || BX.MSLEFT;
}

BX.unbindAll = function(el)
{
	if (!el)
		return;

	for (var i=0,len=eventsList.length; i<len; i++)
	{
		try
		{
			if (eventsList[i] && (null==el || el==eventsList[i].element))
			{
				BX.unbind(eventsList[i].element, eventsList[i].event, eventsList[i].fn);
				eventsList[i] = null;
			}
		}
		catch(e){}
	}

	if (null==el)
	{
		eventsList = [];
	}
}

var captured_events = null, _bind = null;
BX.CaptureEvents = function(el_c, evname_c)
{
	if (_bind)
		return false;

	_bind = BX.bind; captured_events = [];

	BX.bind = function(el, evname, func)
	{
		if (el === el_c && evname === evname_c)
			captured_events.push(func);

		_bind.apply(this, arguments);
	}
}

BX.CaptureEventsGet = function()
{
	if (_bind)
	{
		BX.bind = _bind;

		var captured = captured_events;

		_bind = null; captured_events = null;
		return captured;
	}
}

// Don't even try to use it for submit event!
BX.fireEvent = function(ob,ev)
{
	var result = false;
	if (BX.type.isDomNode(ob))
	{
		result = true;
		if (document.createEventObject)
		{
			// IE
			if (eventTypes[ev] != 'MouseEvent')
			{
				var e = document.createEventObject();
				e.type = ev;
				result = ob.fireEvent('on' + ev, e);
			}

			if (ob[ev])
			{
				ob[ev]();
			}
		}
		else
		{
			// non-IE
			var e = null;

			switch (eventTypes[ev])
			{
				case 'MouseEvent':
					e = document.createEvent('MouseEvent');
					e.initMouseEvent(ev, true, true, top, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, null);
				break;
				default:
					e = document.createEvent('Event');
					e.initEvent(ev, true, true);
			}

			result = ob.dispatchEvent(e);
		}
	}

	return result;
}

BX.getWheelData = function(e)
{
	e = e || window.event;
	return wheelData = e.detail ? e.detail * -1 : e.wheelDelta / 40;
}

BX.proxy_context = null;

BX.delegate = function (func, thisObject)
{
	if (!func || !thisObject)
		return func;

	return function() {
		var cur = BX.proxy_context;
		BX.proxy_context = this;
		var res = func.apply(thisObject, arguments);
		BX.proxy_context = cur;
		return res;
	}
}

BX.delegateLater = function (func_name, thisObject, contextObject)
{
	return function()
	{
		if (thisObject[func_name])
		{
			var cur = BX.proxy_context;
			BX.proxy_context = this;
			var res = thisObject[func_name].apply(contextObject||thisObject, arguments);
			BX.proxy_context = cur;
			return res;
		}
	}
}

BX._initObjectProxy = function(thisObject)
{
	if (typeof thisObject['__proxy_id_' + proxySalt] == 'undefined')
	{
		thisObject['__proxy_id_' + proxySalt] = proxyList.length;
		proxyList[thisObject['__proxy_id_' + proxySalt]] = {};
	}
}

BX.proxy = function(func, thisObject)
{
	if (!func || !thisObject)
		return func;

	BX._initObjectProxy(thisObject)

	if (typeof func['__proxy_id_' + proxySalt] == 'undefined')
		func['__proxy_id_' + proxySalt] = proxyId++;

	if (!proxyList[thisObject['__proxy_id_' + proxySalt]][func['__proxy_id_' + proxySalt]])
		proxyList[thisObject['__proxy_id_' + proxySalt]][func['__proxy_id_' + proxySalt]] = BX.delegate(func, thisObject);

	return proxyList[thisObject['__proxy_id_' + proxySalt]][func['__proxy_id_' + proxySalt]];
}

BX.defer = function(func, thisObject)
{
	if (!!thisObject)
		return BX.defer_proxy(func, thisObject);
	else
		return function() {
			var arg = arguments;
			setTimeout(function(){func.apply(this,arg)}, 10);
		};
}

BX.defer_proxy = function(func, thisObject)
{
	if (!func || !thisObject)
		return func;

	var f = BX.proxy(func, thisObject);

	this._initObjectProxy(thisObject);

	if (typeof func['__defer_id_' + proxySalt] == 'undefined')
		func['__defer_id_' + proxySalt] = proxyId++;

	if (!proxyList[thisObject['__proxy_id_' + proxySalt]][func['__defer_id_' + proxySalt]])
	{
		proxyList[thisObject['__proxy_id_' + proxySalt]][func['__defer_id_' + proxySalt]] = BX.defer(BX.delegate(func, thisObject));
	}

	return proxyList[thisObject['__proxy_id_' + proxySalt]][func['__defer_id_' + proxySalt]];
}

BX.bindDelegate = function (elem, eventName, isTarget, handler)
{
	var h = BX.delegateEvent(isTarget, handler);
	BX.bind(elem, eventName, h);
	return h;
}

BX.delegateEvent = function(isTarget, handler)
{
	return function(e)
	{
		e = e || window.event;
		var target = e.target || e.srcElement;

		while (target != this)
		{
			if (_checkNode(target, isTarget))
			{
				return handler.call(target, e);
			}
			if (target && target.parentNode)
				target = target.parentNode;
			else
				break;
		}
	}
}

BX.False = function() {return false;}
BX.DoNothing = function() {}

// TODO: also check event handlers set via BX.bind()
BX.denyEvent = function(el, ev)
{
	deniedEvents.push([el, ev, el['on' + ev]]);
	el['on' + ev] = BX.DoNothing;
}

BX.allowEvent = function(el, ev)
{
	for(var i=0, len=deniedEvents.length; i<len; i++)
	{
		if (deniedEvents[i][0] == el && deniedEvents[i][1] == ev)
		{
			el['on' + ev] = deniedEvents[i][2];
			BX.util.deleteFromArray(deniedEvents, i);
			return;
		}
	}
}

BX.fixEventPageXY = function(event)
{
	BX.fixEventPageX(event);
	BX.fixEventPageY(event);
	return event;
};

BX.fixEventPageX = function(event)
{
	if (event.pageX == null && event.clientX != null)
	{
		event.pageX =
			event.clientX +
			(document.documentElement && document.documentElement.scrollLeft || document.body && document.body.scrollLeft || 0) -
			(document.documentElement.clientLeft || 0);
	}

	return event;
};

BX.fixEventPageY = function(event)
{
	if (event.pageY == null && event.clientY != null)
	{
		event.pageY =
			event.clientY +
			(document.documentElement && document.documentElement.scrollTop || document.body && document.body.scrollTop || 0) -
			(document.documentElement.clientTop || 0);
	}

	return event;
};

BX.PreventDefault = function(e)
{
	if(!e) e = window.event;
	if(e.stopPropagation)
	{
		e.preventDefault();
		e.stopPropagation();
	}
	else
	{
		e.cancelBubble = true;
		e.returnValue = false;
	}
	return false;
}

BX.eventReturnFalse = function(e)
{
	e=e||window.event;
	if (e && e.preventDefault) e.preventDefault();
	else e.returnValue = false;
	return false;
}

BX.eventCancelBubble = function(e)
{
	e=e||window.event;
	if(e && e.stopPropagation)
		e.stopPropagation();
	else
		e.cancelBubble = true;
}

/* custom events */
/*
	BX.addCustomEvent(eventObject, eventName, eventHandler) - set custom event handler for particular object
	BX.addCustomEvent(eventName, eventHandler) - set custom event handler for all objects
*/
BX.addCustomEvent = function(eventObject, eventName, eventHandler)
{
	/* shift parameters for short version */
	if (BX.type.isString(eventObject))
	{
		eventHandler = eventName;
		eventName = eventObject;
		eventObject = window;
	}

	eventName = eventName.toUpperCase();

	if (!customEvents[eventName])
		customEvents[eventName] = [];

	customEvents[eventName].push(
		{
			handler: eventHandler,
			obj: eventObject
		}
	);
}

BX.removeCustomEvent = function(eventObject, eventName, eventHandler)
{
	/* shift parameters for short version */
	if (BX.type.isString(eventObject))
	{
		eventHandler = eventName;
		eventName = eventObject;
		eventObject = window;
	}

	eventName = eventName.toUpperCase();

	if (!customEvents[eventName])
		return;

	for (var i = 0, l = customEvents[eventName].length; i < l; i++)
	{
		if (!customEvents[eventName][i])
			continue;
		if (customEvents[eventName][i].handler == eventHandler && customEvents[eventName][i].obj == eventObject)
		{
			delete customEvents[eventName][i];
			return;
		}
	}
}

BX.onCustomEvent = function(eventObject, eventName, arEventParams)
{
	/* shift parameters for short version */
	if (BX.type.isString(eventObject))
	{
		arEventParams = eventName;
		eventName = eventObject;
		eventObject = window;
	}

	eventName = eventName.toUpperCase();

	if (!customEvents[eventName])
		return;

	if (!arEventParams)
		arEventParams = [];

	var h;
	for (var i = 0, l = customEvents[eventName].length; i < l; i++)
	{
		h = customEvents[eventName][i];
		if (!h || !h.handler)
			continue;

		if (h.obj == window || /*eventObject == window || */h.obj == eventObject) //- only global event handlers will be called
		{
			h.handler.apply(eventObject, arEventParams);
		}
	}
}

BX.parseJSON = function(data, context)
{
	var result = null;
	if (BX.type.isString(data))
	{
		try {
			if (data.indexOf("\n") >= 0)
				eval('result = ' + data);
			else
				result = (new Function("return " + data))();
		} catch(e) {
			BX.onCustomEvent(context, 'onParseJSONFailure', [data, context])
		}
	}

	return result;
}

/* ready */
BX.isReady = false;
BX.ready = function(handler)
{
	bindReady();

	if (!BX.type.isFunction(handler))
	{
		BX.debug('READY: not a function! ', handler);
	}
	else
	{
		if (BX.isReady)
			handler.call(document);
		else if (readyList)
			readyList.push(handler);
	}
}

BX.submit = function(obForm, action_name, action_value, onAfterSubmit)
{
	action_name = action_name || 'save';
	if (!obForm['BXFormSubmit_' + action_name])
	{
		obForm['BXFormSubmit_' + action_name] = obForm.appendChild(BX.create('INPUT', {
			'props': {
				'type': 'submit',
				'name': action_name,
				'value': action_value || 'Y'
			},
			'style': {
				'display': 'none'
			}
		}));
	}

	if (obForm.sessid)
		obForm.sessid.value = BX.bitrix_sessid();

	setTimeout(BX.delegate(function() {BX.fireEvent(this, 'click'); if (onAfterSubmit) onAfterSubmit();}, obForm['BXFormSubmit_' + action_name]), 10);
}


/* browser detection */
BX.browser = {

	IsIE: function()
	{
		return bIE;
	},

	IsIE6: function()
	{
		return (/MSIE 6/i.test(navigator.userAgent));
	},

	IsIE9: function()
	{
		return !!document.documentMode && document.documentMode >= 9;
	},

	IsIE10: function()
	{
		return !!document.documentMode && document.documentMode >= 10;
	},

	IsOpera: function()
	{
		return bOpera;
	},

	IsSafari: function()
	{
		return bSafari;
	},

	IsFirefox: function()
	{
		return bFirefox;
	},

	IsChrome: function()
	{
		return bChrome;
	},

	IsMac: function()
	{
		return (/Macintosh/i.test(navigator.userAgent));
	},

	IsAndroid: function()
	{
		return (/Android/i.test(navigator.userAgent));
	},

	IsIOS: function()
	{
		return (/(iPad;)|(iPhone;)/i.test(navigator.userAgent));
	},

	IsDoctype: function(pDoc)
	{
		pDoc = pDoc || document;

		if (pDoc.compatMode)
			return (pDoc.compatMode == "CSS1Compat");

		if (pDoc.documentElement && pDoc.documentElement.clientHeight)
			return true;

		return false;
	},

	SupportLocalStorage: function()
	{
		return !!BX.localStorage && !!BX.localStorage.checkBrowser()
	},

	addGlobalClass: function() {
		if(BX.browser.IsIOS())
		{
			BX.addClass(document.documentElement, 'bx-ios');
		}
		else if(BX.browser.IsMac())
		{
			BX.addClass(document.documentElement, 'bx-mac');
		}

		if(/AppleWebKit/.test(navigator.userAgent))
		{
			BX.addClass(document.documentElement, 'bx-chrome');
		}
		else if(/MSIE 8/.test(navigator.userAgent))
		{
			BX.addClass(document.documentElement, 'bx-ie bx-ie8'
				 + (!BX.browser.IsDoctype() ? ' bx-quirks' : ''));
		}
		else if(/MSIE 9/.test(navigator.userAgent))
		{
			BX.addClass(document.documentElement, 'bx-ie bx-ie9'
				 + (!BX.browser.IsDoctype() ? ' bx-quirks' : ''));
		}
		else if(/MSIE 10/.test(navigator.userAgent))
		{
			// it seems IE10 doesn't have any specific bugs like others event in quirks mode
			BX.addClass(document.documentElement, 'bx-ie bx-ie10');
		}
		else if(/Opera/.test(navigator.userAgent))
		{
			BX.addClass(document.documentElement, 'bx-opera');
		}
		else if(/Gecko/.test(navigator.userAgent))
		{
			BX.addClass(document.documentElement, 'bx-firefox');
		}

		BX.browser.addGlobalClass = BX.DoNothing;
	},

	isPropertySupported: function(jsProperty, bReturnCSSName)
	{
		if (!BX.type.isNotEmptyString(jsProperty))
			return false;

		var property = jsProperty.indexOf("-") > -1 ? getJsName(jsProperty) : jsProperty;
		bReturnCSSName = !!bReturnCSSName;

		var ucProperty = property.charAt(0).toUpperCase() + property.slice(1);
		var properties = (property + ' ' + ["Webkit", "Moz", "O", "ms"].join(ucProperty + " ") + ucProperty).split(" ");
		var obj = document.body || document.documentElement;

		for (var i = 0; i < properties.length; i++)
		{
			var prop = properties[i];
			if (obj.style[prop] !== undefined)
			{
				var prefix = prop == property
							? ""
							: "-" + prop.substr(0, prop.length - property.length).toLowerCase() + "-";
				return bReturnCSSName ? prefix + getCssName(property) : prop;
			}
		}

		function getCssName(propertyName)
		{
			return propertyName.replace(/([A-Z])/g, function() { return "-" + arguments[1].toLowerCase(); } )
		}

		function getJsName(cssName)
		{
			var reg = /(\-([a-z]){1})/g;
			if (reg.test(cssName))
				return cssName.replace(reg, function () {return arguments[2].toUpperCase();});
			else
				return cssName;
		}

		return false;
	},

	addGlobalFeatures : function(features, prefix)
	{
		if (!BX.type.isArray(features))
			return;

		var classNames = [];
		for (var i = 0; i < features.length; i++)
		{
			var support = !!BX.browser.isPropertySupported(features[i]);
			classNames.push( "bx-" + (support ? "" : "no-") + features[i].toLowerCase());
		}
		BX.addClass(document.documentElement, classNames.join(" "));
	}
}

/* low-level fx funcitons*/
BX.show = function(ob, displayType)
{
	if (ob.BXDISPLAY || !_checkDisplay(ob, displayType))
	{
		ob.style.display = ob.BXDISPLAY;
	}
}

BX.hide = function(ob, displayType)
{
	if (!ob.BXDISPLAY)
		_checkDisplay(ob, displayType);

	ob.style.display = 'none';
}

BX.toggle = function(ob, values)
{
	if (!values && BX.type.isElementNode(ob))
	{
		var bShow = true;
		if (ob.BXDISPLAY)
			bShow = !_checkDisplay(ob);
		else
			bShow = ob.style.display == 'none';

		if (bShow)
			BX.show(ob);
		else
			BX.hide(ob);
	}
	else if (BX.type.isArray(values))
	{
		for (var i=0,len=values.length; i<len; i++)
		{
			if (ob == values[i])
			{
				ob = values[i==len-1 ? 0 : i+1]
				break;
			}
		}
		if (i==len)
			ob = values[0];
	}

	return ob;
}

/* some useful util functions */

BX.util = {
	array_values: function(ar)
	{
		if (!BX.type.isArray(ar))
			return BX.util._array_values_ob(ar);
		var arv = [];
		for(var i=0,l=ar.length;i<l;i++)
			if (ar[i] !== null && typeof ar[i] != 'undefined')
				arv.push(ar[i]);
		return arv;
	},

	_array_values_ob: function(ar)
	{
		var arv = [];
		for(var i in ar)
			if (ar[i] !== null && typeof ar[i] != 'undefined')
				arv.push(ar[i]);
		return arv;
	},

	array_keys: function(ar)
	{
		if (!BX.type.isArray(ar))
			return BX.util._array_keys_ob(ar);
		var arv = [];
		for(var i=0,l=ar.length;i<l;i++)
			if (ar[i] !== null && typeof ar[i] != 'undefined')
				arv.push(i);
		return arv;
	},

	_array_keys_ob: function(ar)
	{
		var arv = [];
		for(var i in ar)
			if (ar[i] !== null && typeof ar[i] != 'undefined')
				arv.push(i);
		return arv;
	},

	array_merge: function(first, second)
	{
		if (!BX.type.isArray(first)) first = [];
		if (!BX.type.isArray(second)) second = [];

		var i = first.length, j = 0;

		if (typeof second.length === "number")
		{
			for (var l = second.length; j < l; j++)
			{
				first[i++] = second[j];
			}
		}
		else
		{
			while (second[j] !== undefined)
			{
				first[i++] = second[j++];
			}
		}

		first.length = i;

		return first;
	},

	array_unique: function(ar)
	{
		var i=0,j,len=ar.length;
		if(len<2) return ar;

		for (; i<len-1;i++)
		{
			for (j=i+1; j<len;j++)
			{
				if (ar[i]==ar[j])
				{
					ar.splice(j--,1); len--;
				}
			}
		}

		return ar;
	},

	in_array: function(needle, haystack)
	{
		for(var i=0; i<haystack.length; i++)
		{
			if(haystack[i] == needle)
				return true;
		}
		return false;
	},

	array_search: function(needle, haystack)
	{
		for(var i=0; i<haystack.length; i++)
		{
			if(haystack[i] == needle)
				return i;
		}
		return -1;
	},

	object_search_key: function(needle, haystack)
	{
		if (haystack[needle])
			return haystack[needle];

		for(var i in haystack)
		{
			if (typeof haystack[i] == "object")
			{
				var result = BX.util.object_search_key(needle, haystack[i]);
				if (result !== false)
					return result;
			}
		}
		return false;
	},

	trim: function(s)
	{
		if (BX.type.isString(s))
			return s.replace(r.ltrim, '').replace(r.rtrim, '');
		else
			return s;
	},

	urlencode: function(s){return encodeURIComponent(s);},

	// it may also be useful. via sVD.
	deleteFromArray: function(ar, ind) {return ar.slice(0, ind).concat(ar.slice(ind + 1));},
	insertIntoArray: function(ar, ind, el) {return ar.slice(0, ind).concat([el]).concat(ar.slice(ind));},

	htmlspecialchars: function(str)
	{
		if(!str.replace) return str;

		return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	},

	htmlspecialcharsback: function(str)
	{
		if(!str.replace) return str;

		return str.replace(/\&quot;/g, '"').replace(/&#39;/g, "'").replace(/\&lt;/g, '<').replace(/\&gt;/g, '>').replace(/\&amp;/g, '&');
	},

	// Quote regular expression characters plus an optional character
	preg_quote: function(str, delimiter)
	{
		if(!str.replace)
			return str;
		return str.replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
	},

	jsencode: function(str)
	{
		if (!str || !str.replace)
			return str;

		var escapes =
		[
			{ c: "\\\\", r: "\\\\" }, // should be first
			{ c: "\\t", r: "\\t" },
			{ c: "\\n", r: "\\n" },
			{ c: "\\r", r: "\\r" },
			{ c: "\"", r: "\\\"" },
			{ c: "'", r: "\\'" },
			{ c: "<", r: "\\x3C" },
			{ c: ">", r: "\\x3E" },
			{ c: "\\u2028", r: "\\u2028" },
			{ c: "\\u2029", r: "\\u2029" }
		];
		for (var i = 0; i < escapes.length; i++)
			str = str.replace(new RegExp(escapes[i].c, 'g'), escapes[i].r);
		return str;
	},

	str_pad: function(input, pad_length, pad_string, pad_type)
	{
		pad_string = pad_string || ' ';
		pad_type = pad_type || 'right';
		input = input.toString();

		if (pad_type == 'left')
			return BX.util.str_pad_left(input, pad_length, pad_string);
		else
			return BX.util.str_pad_right(input, pad_length, pad_string);

	},

	str_pad_left: function(input, pad_length, pad_string)
	{
		var i = input.length, q=pad_string.length;
		if (i >= pad_length) return input;

		for(;i<pad_length;i+=q)
			input = pad_string + input;

		return input;
	},

	str_pad_right: function(input, pad_length, pad_string)
	{
		var i = input.length, q=pad_string.length;
		if (i >= pad_length) return input;

		for(;i<pad_length;i+=q)
			input += pad_string;

		return input;
	},

	strip_tags: function(str)
	{
		return str.split(/<[^>]+>/g).join('')
	},

	popup: function(url, width, height)
	{
		var w, h;
		if(BX.browser.IsOpera())
		{
			w = document.body.offsetWidth;
			h = document.body.offsetHeight;
		}
		else
		{
			w = screen.width;
			h = screen.height;
		}
		return window.open(url, '', 'status=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+',top='+Math.floor((h - height)/2-14)+',left='+Math.floor((w - width)/2-5));
	},

	// BX.util.objectSort(object, sortBy, sortDir) - Sort object by property
	// function params: 1 - object for sort, 2 - sort by property, 3 - sort direction (asc/desc)
	// return: sort array [[objectElement], [objectElement]] in sortDir direction

	// example: BX.util.objectSortBy({'L1': {'name': 'Last'}, 'F1': {'name': 'First'}}, 'name', 'asc');
	// return: [{'name' : 'First'}, {'name' : 'Last'}]
	objectSort: function(object, sortBy, sortDir)
	{
		sortDir = sortDir == 'asc'? 'asc': 'desc';

		var arItems = Array();
		for (var i in object)
			if (object[i][sortBy])
				arItems.push([i, object[i][sortBy]]);

		if (sortDir == 'asc')
		{
			arItems.sort(function(i, ii) {
				if (!isNaN(i[1]) && !isNaN(ii[1]))
				{
					var s1 = parseInt(i[1]); var s2 = parseInt(ii[1]);
				}
				else
				{
					var s1 = i[1].toString().toLowerCase(); var s2 = ii[1].toString().toLowerCase();
				}
				if (s1 > s2) return 1; else if (s1 < s2) return -1; else return 0;
			});
		}
		else
		{
			arItems.sort(function(i, ii) {
				if (!isNaN(i[1]) && !isNaN(ii[1]))
				{
					var s1 = parseInt(i[1]);
					var s2 = parseInt(ii[1]);
				}
				else
				{
					var s1 = i[1].toString().toLowerCase();
					var s2 = ii[1].toString().toLowerCase();
				}
				if (s1 < s2) return 1; else if (s1 > s2) return -1; else return 0;
			});
		}

		var arReturnArray = Array();
		for (var i = 0; i < arItems.length; i++)
			arReturnArray.push(object[arItems[i][0]]);

		return arReturnArray;
	},

	// #fdf9e5 => {r=253, g=249, b=229}
	hex2rgb: function(color)
	{
		var rgb = color.replace(/[# ]/g,"").replace(/^(.)(.)(.)$/,'$1$1$2$2$3$3').match(/.{2}/g);
		for (var i=0;  i<3; i++)
		{
			rgb[i] = parseInt(rgb[i], 16);
		}
		return {'r':rgb[0],'g':rgb[1],'b':rgb[2]};
	},

	remove_url_param: function(url, param)
	{
		if (BX.type.isArray(param))
		{
			for (var i=0; i<param.length; i++)
				url = BX.util.remove_url_param(url, param[i])
		}
		else
		{
			url = url.replace(new RegExp('([?&])'+param+'=[^&]*[&]*', 'i'), '$1');
		}

		return url;
	},

	even: function(digit)
	{
		return (parseInt(digit) % 2 == 0)? true: false;
	}
}

BX.type = {
	isString: function(item) {
		return item === '' ? true : (item ? (typeof (item) == "string" || item instanceof String) : false);
	},
	isNotEmptyString: function(item) {
		return BX.type.isString(item) ? item.length > 0 : false;
	},
	isBoolean: function(item) {
		return item === true || item === false;
	},
	isNumber: function(item) {
		return item === 0 ? true : (item ? (typeof (item) == "number" || item instanceof Number) : false);
	},
	isFunction: function(item) {
		return item === null ? false : (typeof (item) == "function" || item instanceof Function);
	},
	isElementNode: function(item) {
		//document.body.ELEMENT_NODE;
		return item && typeof (item) == "object" && "nodeType" in item && item.nodeType == 1 && item.tagName && item.tagName.toUpperCase() != 'SCRIPT' && item.tagName.toUpperCase() != 'STYLE' && item.tagName.toUpperCase() != 'LINK';
	},
	isDomNode: function(item) {
		return item && typeof (item) == "object" && "nodeType" in item;
	},
	isArray: function(item) {
		return item && Object.prototype.toString.call(item) == "[object Array]";
	},
	isDate : function(item) {
		return item && Object.prototype.toString.call(item) == "[object Date]";
	}
}

BX.isNodeInDom = function(node)
{
	return node === document ? true :
		(node.parentNode ? BX.isNodeInDom(node.parentNode) : false);
}

BX.isNodeHidden = function(node)
{
	if (node === document)
		return false;
	else if (BX.style(node, 'display') == 'none')
		return true;
	else
		return (node.parentNode ? BX.isNodeHidden(node.parentNode) : true);
}

BX.evalPack = function(code)
{
	while (code.length > 0)
	{
		var c = code.shift();

		if (c.TYPE == 'SCRIPT_EXT' || c.TYPE == 'SCRIPT_SRC')
		{
			BX.loadScript(c.DATA, function() {BX.evalPack(code)});
		}
		else if (c.TYPE == 'SCRIPT')
			BX.evalGlobal(c.DATA);
	}
}

BX.evalGlobal = function(data)
{
	if (data)
	{
		var head = document.getElementsByTagName("head")[0] || document.documentElement,
			script = document.createElement("script");

		script.type = "text/javascript";

		if (!BX.browser.IsIE())
		{
			script.appendChild(document.createTextNode(data));
		}
		else
		{
			script.text = data;
		}

		head.insertBefore(script, head.firstChild);
		head.removeChild(script);
	}
}

BX.processHTML = function(HTML, scriptsRunFirst)
{
	var matchScript, scripts = [], styles = [], data = HTML;

	while ((matchScript = data.match(r.script)) !== null)
	{
		var end = data.search(/<\/script>/i);
		if (end == -1)
			break;

		var bRunFirst = scriptsRunFirst || (matchScript[1].indexOf('bxrunfirst') != '-1');

		var matchSrc;
		if ((matchSrc = matchScript[1].match(r.script_src)) !== null)
			scripts.push({"bRunFirst": bRunFirst, "isInternal": false, "JS": matchSrc[1]});
		else
		{
			var start = matchScript.index + matchScript[0].length;
			var js = data.substr(start, end-start);

			scripts.push({"bRunFirst": bRunFirst, "isInternal": true, "JS": js});
		}

		data = data.substr(0, matchScript.index) + data.substr(end+9);
	}

	while ((matchStyle = data.match(r.style)) !== null)
	{
		var matchHref;
		if ((matchHref = matchStyle[0].match(r.style_href)) !== null && matchStyle[0].indexOf('media="') < 0)
		{
			styles.push(matchHref[1]);
		}
		data = data.replace(matchStyle[0], '');
	}

	return {'HTML': data, 'SCRIPT': scripts, 'STYLE': styles};
}

BX.garbage = function(call, thisObject)
{
	garbageCollectors.push({callback: call, context: thisObject});
}

/* window pos functions */

BX.GetDocElement = function (pDoc)
{
	pDoc = pDoc || document;
	return (BX.browser.IsDoctype(pDoc) ? pDoc.documentElement : pDoc.body);
}

BX.GetContext = function(node)
{
	if (BX.type.isElementNode(node))
		return node.ownerDocument.parentWindow || node.ownerDocument.defaultView || window;
	else if (BX.type.isDomNode(node))
		return node.parentWindow || node.defaultView || window;
	else
		return window;
}

BX.GetWindowInnerSize = function(pDoc)
{
	var width, height;

	pDoc = pDoc || document;

	if (self.innerHeight) // all except Explorer
	{
		width = BX.GetContext(pDoc).innerWidth;
		height = BX.GetContext(pDoc).innerHeight;
	}
	else if (pDoc.documentElement && (pDoc.documentElement.clientHeight || pDoc.documentElement.clientWidth)) // Explorer 6 Strict Mode
	{
		width = pDoc.documentElement.clientWidth;
		height = pDoc.documentElement.clientHeight;
	}
	else if (pDoc.body) // other Explorers
	{
		width = pDoc.body.clientWidth;
		height = pDoc.body.clientHeight;
	}
	return {innerWidth : width, innerHeight : height};
}

BX.GetWindowScrollPos = function(pDoc)
{
	var left, top;

	pDoc = pDoc || document;

	if (self.pageYOffset) // all except Explorer
	{
		left = BX.GetContext(pDoc).pageXOffset;
		top = BX.GetContext(pDoc).pageYOffset;
	}
	else if (pDoc.documentElement && (pDoc.documentElement.scrollTop || pDoc.documentElement.scrollLeft)) // Explorer 6 Strict
	{
		left = pDoc.documentElement.scrollLeft;
		top = pDoc.documentElement.scrollTop;
	}
	else if (pDoc.body) // all other Explorers
	{
		left = pDoc.body.scrollLeft;
		top = pDoc.body.scrollTop;
	}
	return {scrollLeft : left, scrollTop : top};
}

BX.GetWindowScrollSize = function(pDoc)
{
	var width, height;
	if (!pDoc)
		pDoc = document;

	if ( (pDoc.compatMode && pDoc.compatMode == "CSS1Compat"))
	{
		width = pDoc.documentElement.scrollWidth;
		height = pDoc.documentElement.scrollHeight;
	}
	else
	{
		if (pDoc.body.scrollHeight > pDoc.body.offsetHeight)
			height = pDoc.body.scrollHeight;
		else
			height = pDoc.body.offsetHeight;

		if (pDoc.body.scrollWidth > pDoc.body.offsetWidth ||
			(pDoc.compatMode && pDoc.compatMode == "BackCompat") ||
			(pDoc.documentElement && !pDoc.documentElement.clientWidth)
		)
			width = pDoc.body.scrollWidth;
		else
			width = pDoc.body.offsetWidth;
	}
	return {scrollWidth : width, scrollHeight : height};
}

BX.GetWindowSize = function(pDoc)
{
	var innerSize = this.GetWindowInnerSize(pDoc);
	var scrollPos = this.GetWindowScrollPos(pDoc);
	var scrollSize = this.GetWindowScrollSize(pDoc);

	return  {
		innerWidth : innerSize.innerWidth, innerHeight : innerSize.innerHeight,
		scrollLeft : scrollPos.scrollLeft, scrollTop : scrollPos.scrollTop,
		scrollWidth : scrollSize.scrollWidth, scrollHeight : scrollSize.scrollHeight
	};
}

BX.hide_object = function(ob)
{
	ob = BX(ob);
	ob.style.position = 'absolute';
	ob.style.top = '-1000px';
	ob.style.left = '-1000px';
	ob.style.height = '10px';
	ob.style.width = '10px';
};

BX.is_relative = function(el)
{
	var p = BX.style(el, 'position');
	return p == 'relative' || p == 'absolute';
}

BX.is_float = function(el)
{
	var p = BX.style(el, 'float');
	return p == 'right' || p == 'left';
}

BX.is_fixed = function(el)
{
	var p = BX.style(el, 'position');
	return p == 'fixed';
}

BX.pos = function(el, bRelative)
{
	var r = { top: 0, right: 0, bottom: 0, left: 0, width: 0, height: 0 };
	bRelative = !!bRelative;
	if (!el)
		return r;
	if (typeof (el.getBoundingClientRect) != "undefined" && el.ownerDocument == document && !bRelative)
	{
		var clientRect = el.getBoundingClientRect();
		var root = document.documentElement;
		var body = document.body;

		r.top = clientRect.top + (root.scrollTop || body.scrollTop);
		r.left = clientRect.left + (root.scrollLeft || body.scrollLeft);
		r.width = clientRect.right - clientRect.left;
		r.height = clientRect.bottom - clientRect.top;
		r.right = clientRect.right + (root.scrollLeft || body.scrollLeft);
		r.bottom = clientRect.bottom + (root.scrollTop || body.scrollTop);
	}
	else
	{
		var x = 0, y = 0, w = el.offsetWidth, h = el.offsetHeight;
		var first = true;
		for (; el != null; el = el.offsetParent)
		{
			if (!first && bRelative && BX.is_relative(el))
				break;

			x += el.offsetLeft;
			y += el.offsetTop;
			if (first)
			{
				first = false;
				continue;
			}

			var elBorderLeftWidth = parseInt(BX.style(el, 'border-left-width')),
				elBorderTopWidth = parseInt(BX.style(el, 'border-top-width'));

			if (!isNaN(elBorderLeftWidth) && elBorderLeftWidth > 0)
				x += elBorderLeftWidth;
			if (!isNaN(elBorderTopWidth) && elBorderTopWidth > 0)
				y += elBorderTopWidth;
		}

		r.top = y;
		r.left = x;
		r.width = w;
		r.height = h;
		r.right = r.left + w;
		r.bottom = r.top + h;
	}

	for (var i in r) r[i] = parseInt(r[i]);

	return r;
}


BX.align = function(pos, w, h, type)
{
	if (type)
		type = type.toLowerCase();
	else
		type = '';

	var pDoc = document;
	if (BX.type.isElementNode(pos))
	{
		pDoc = pos.ownerDocument;
		pos = BX.pos(pos);
	}

	var x = pos["left"], y = pos["bottom"];

	var scroll = BX.GetWindowScrollPos(pDoc);
	var size = BX.GetWindowInnerSize(pDoc);

	if((size.innerWidth + scroll.scrollLeft) - (pos["left"] + w) < 0)
	{
		if(pos["right"] - w >= 0 )
			x = pos["right"] - w;
		else
			x = scroll.scrollLeft;
	}

	if(((size.innerHeight + scroll.scrollTop) - (pos["bottom"] + h) < 0) || ~type.indexOf('top'))
	{
		if(pos["top"] - h >= 0 || ~type.indexOf('top'))
			y = pos["top"] - h;
		else
			y = scroll.scrollTop;
	}

	return {'left':x, 'top':y};
}

BX.scrollToNode = function(node)
{
	var obNode = BX(node);

	if (obNode.scrollIntoView)
		obNode.scrollIntoView(true);
	else
	{
		var arNodePos = BX.pos(obNode);
		window.scrollTo(arNodePos.left, arNodePos.top);
	}
}

/* non-xhr loadings */
BX.showWait = function(node, msg)
{
	node = BX(node) || document.body || document.documentElement;
	msg = msg || BX.message('JS_CORE_LOADING');

	var container_id = node.id || Math.random();

	var obMsg = node.bxmsg = document.body.appendChild(BX.create('DIV', {
		props: {
			id: 'wait_' + container_id,
			className: 'bx-core-waitwindow'
		},
		text: msg
	}));

	setTimeout(BX.delegate(_adjustWait, node), 10);

	lastWait[lastWait.length] = obMsg;
	return obMsg;
}

BX.closeWait = function(node, obMsg)
{
	if(node && !obMsg)
		obMsg = node.bxmsg;
	if(node && !obMsg && BX.hasClass(node, 'bx-core-waitwindow'))
		obMsg = node;
	if(node && !obMsg)
		obMsg = BX('wait_' + node.id);
	if(!obMsg)
		obMsg = lastWait.pop();

	if (obMsg && obMsg.parentNode)
	{
		for (var i=0,len=lastWait.length;i<len;i++)
		{
			if (obMsg == lastWait[i])
			{
				lastWait = BX.util.deleteFromArray(lastWait, i);
				break;
			}
		}

		obMsg.parentNode.removeChild(obMsg);
		if (node) node.bxmsg = null;
		BX.cleanNode(obMsg, true);
	}
}

BX.setKernelJS = function(scripts)
{
	if (BX.type.isArray(scripts))
		arKernelJS = scripts;
}

BX.getKernelJS = function()
{
	return arKernelJS;
}

BX.loadScript = function(script, callback, doc)
{
	if (!BX.isReady)
	{
		var _args = arguments;
		BX.ready(function() {
			BX.loadScript.apply(this, _args);
		});
		return;
	}

	doc = doc || document;

	if (BX.type.isString(script))
		script = [script];
	var _callback = function()
	{
		return (callback && BX.type.isFunction(callback)) ? callback() : null
	}
	var load_js = function(ind)
	{
		if(ind >= script.length)
			return _callback();

		if(!!script[ind])
		{
			var oHead = doc.getElementsByTagName("head")[0] || doc.documentElement;
			var oScript = doc.createElement('script');
			oScript.src = script[ind];

			var verInd = script[ind].indexOf('.js?');
			if(verInd>0)
				fileSrc = script[ind].substr(0, verInd + 3);
			else
				fileSrc = script[ind];

			if(_isScriptLoaded(fileSrc))
			{
				load_js(++ind);
			}
			else
			{
				var bLoaded = false;
				oScript.onload = oScript.onreadystatechange = function()
				{
					if (!bLoaded && (!oScript.readyState || oScript.readyState == "loaded" || oScript.readyState == "complete"))
					{
						bLoaded = true;
						setTimeout(function (){load_js(++ind);}, 50);

						oScript.onload = oScript.onreadystatechange = null;
						if (oHead && oScript.parentNode)
						{
							oHead.removeChild(oScript);
						}
					}
				}

				return oHead.insertBefore(oScript, oHead.firstChild);
			}
		}
		else
		{
			load_js(++ind);
		}
	}

	load_js(0);
}

BX.loadCSS = function(arCSS, doc, win)
{
	if (!BX.isReady)
	{
		var _args = arguments;
		BX.ready(function() {
			BX.loadCSS.apply(this, _args);
		});
		return null;
	}

	if (BX.type.isString(arCSS))
	{
		var bSingle = true;
		arCSS = [arCSS];
	}
	var i = 0,
		l = arCSS.length,
		lnk = null,
		pLnk = [];

	if (l == 0)
		return null;

	doc = doc || document;
	win = win || window;

	_checkCssList();

	if (!win.bxhead)
	{
		var heads = doc.getElementsByTagName('HEAD');
		win.bxhead = heads[0];

		if (!win.bxhead)
		{
			return null;
		}
	}

	for (i = 0; i < l; i++)
	{
		var _check = arCSS[i]
				.replace(/\.css(\?\d*)/, '.css')
				.replace(/^(http[s]*:)*\/\/[^\/]+/i, '');

		if (BX.util.in_array(_check, cssList))
			continue;

		lnk = document.createElement('LINK');
		lnk.href = arCSS[i];
		lnk.rel = 'stylesheet';
		lnk.type = 'text/css';
		win.bxhead.appendChild(lnk);

		pLnk.push(lnk);
		cssList.push(_check);
	}

	if (bSingle)
		return lnk;

	return pLnk;
}

BX.reload = function(back_url, bAddClearCache)
{
	if (back_url === true)
	{
		bAddClearCache = true;
		back_url = null;
	}

	var new_href = back_url || top.location.href;

	var hashpos = new_href.indexOf('#'), hash = '';

	if (hashpos != -1)
	{
		hash = new_href.substr(hashpos);
		new_href = new_href.substr(0, hashpos);
	}

	if (bAddClearCache && new_href.indexOf('clear_cache=Y') < 0)
		new_href += (new_href.indexOf('?') == -1 ? '?' : '&') + 'clear_cache=Y';

	if (hash)
	{
		// hack for clearing cache in ajax mode components with history emulation
		if (bAddClearCache && (hash.substr(0, 5) == 'view/' || hash.substr(0, 6) == '#view/') && hash.indexOf('clear_cache%3DY') < 0)
			hash += (hash.indexOf('%3F') == -1 ? '%3F' : '%26') + 'clear_cache%3DY'

		new_href = new_href.replace(/(\?|\&)_r=[\d]*/, '');
		new_href += (new_href.indexOf('?') == -1 ? '?' : '&') + '_r='+Math.round(Math.random()*10000) + hash;
	}

	top.location.href = new_href;
}

BX.clearCache = function()
{
	BX.showWait();
	BX.reload(true);
}

BX.template = function(tpl, callback, bKillTpl)
{
	BX.ready(function() {
		_processTpl(BX(tpl), callback, bKillTpl);
	});
}

BX.isAmPmMode = function()
{
	return BX.message('FORMAT_DATETIME').match('T') == null ? false : true;
}

BX.formatDate = function(date, format)
{
	date = date || new Date();

	var bTime = date.getHours() || date.getMinutes() || date.getSeconds(),
		str = !!format
			? format :
			(bTime ? BX.message('FORMAT_DATETIME') : BX.message('FORMAT_DATE')
		);

	return str.replace(/YYYY/ig, date.getFullYear())
		.replace(/MMMM/ig, BX.util.str_pad_left((date.getMonth()+1).toString(), 2, '0'))
		.replace(/MM/ig, BX.util.str_pad_left((date.getMonth()+1).toString(), 2, '0'))
		.replace(/DD/ig, BX.util.str_pad_left(date.getDate().toString(), 2, '0'))
		.replace(/HH/ig, BX.util.str_pad_left(date.getHours().toString(), 2, '0'))
		.replace(/MI/ig, BX.util.str_pad_left(date.getMinutes().toString(), 2, '0'))
		.replace(/SS/ig, BX.util.str_pad_left(date.getSeconds().toString(), 2, '0'));
}

BX.getNumMonth = function(month)
{
	var wordMonthCut = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
	var wordMonth = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];

	var q = month.toUpperCase();
	for (i = 1; i <= 12; i++)
	{
		if (q == BX.message('MON_'+i).toUpperCase() || q == BX.message('MONTH_'+i).toUpperCase() || q == wordMonthCut[i-1].toUpperCase() || q == wordMonth[i-1].toUpperCase())
		{
			return i;
		}
	}
	return month;
}

BX.parseDate = function(str)
{
	if (BX.type.isNotEmptyString(str))
	{
		var regMonths = '';
		for (i = 1; i <= 12; i++)
		{
			regMonths = regMonths + '|' + BX.message('MON_'+i);
		}

		var expr = new RegExp('([0-9]+|[a-z]+' + regMonths + ')', 'ig');
		var aDate = str.match(expr),
			aFormat = BX.message('FORMAT_DATE').match(/(DD|MI|MMMM|MM|M|YYYY)/ig),
			i, cnt,
			aDateArgs=[], aFormatArgs=[],
			aResult={};

		if (!aDate)
			return null;

		if(aDate.length > aFormat.length)
		{
			aFormat = BX.message('FORMAT_DATETIME').match(/(DD|MI|MMMM|MM|M|YYYY|HH|H|SS|TT|T|GG|G)/ig);
		}

		for(i = 0, cnt = aDate.length; i < cnt; i++)
		{
			if(BX.util.trim(aDate[i]) != '')
			{
				aDateArgs[aDateArgs.length] = aDate[i];
			}
		}

		for(i = 0, cnt = aFormat.length; i < cnt; i++)
		{
			if(BX.util.trim(aFormat[i]) != '')
			{
				aFormatArgs[aFormatArgs.length] = aFormat[i];
			}
		}


		var m = BX.util.array_search('MMMM', aFormatArgs)
		if (m > 0)
		{
			aDateArgs[m] = BX.getNumMonth(aDateArgs[m]);
			aFormatArgs[m] = "MM";
		}
		else
		{
			m = BX.util.array_search('M', aFormatArgs)
			if (m > 0)
			{
				aDateArgs[m] = BX.getNumMonth(aDateArgs[m]);
				aFormatArgs[m] = "MM";
			}
		}

		for(i = 0, cnt = aFormatArgs.length; i < cnt; i++)
		{
			var k = aFormatArgs[i].toUpperCase();
			aResult[k] = k == 'T' || k == 'TT' ? aDateArgs[i] : parseInt(aDateArgs[i], 10);
		}

		if(aResult['DD'] > 0 && aResult['MM'] > 0 && aResult['YYYY'] > 0)
		{
			var d = new Date();
			d.setDate(1);
			d.setFullYear(aResult['YYYY']);
			d.setMonth(aResult['MM']-1);
			d.setDate(aResult['DD']);
			d.setHours(0, 0, 0);

			if(
				(!isNaN(aResult['HH']) || !isNaN(aResult['GG']) || !isNaN(aResult['H']) || !isNaN(aResult['G']))
					&& !isNaN(aResult['MI'])
			)
			{
				if (!isNaN(aResult['H']) || !isNaN(aResult['G']))
				{
					var bPM = (aResult['T']||aResult['TT']||'am').toUpperCase()=='PM';
					aResult['HH'] = parseInt(aResult['H']||aResult['G']||0, 10) + (bPM ? 12 : 0);
				}
				else
				{
					aResult['HH'] = parseInt(aResult['HH']||aResult['GG']||0, 10);
				}

				if (isNaN(aResult['SS']))
					aResult['SS'] = 00;

				d.setHours(aResult['HH'], aResult['MI'], aResult['SS']);
			}

			return d;
		}
	}

	return null;
}

BX.selectUtils =
{
	addNewOption: function(oSelect, opt_value, opt_name, do_sort, check_unique)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			var n = oSelect.length;
			if(check_unique !== false)
			{
				for(var i=0;i<n;i++)
				{
					if(oSelect[i].value==opt_value)
					{
						return;
					}
				}
			}

			var newoption = new Option(opt_name, opt_value, false, false);
			oSelect.options[n]=newoption;
		}

		if(do_sort === true)
		{
			this.sortSelect(select_id);
		}
	},

	deleteOption: function(oSelect, opt_value)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			for(var i=0;i<oSelect.length;i++)
			{
				if(oSelect[i].value==opt_value)
				{
					oSelect.remove(i);
					break;
				}
			}
		}
	},

	deleteSelectedOptions: function(select_id)
	{
		var oSelect = BX(select_id);
		if(oSelect)
		{
			var i=0;
			while(i<oSelect.length)
			{
				if(oSelect[i].selected)
				{
					oSelect[i].selected=false;
					oSelect.remove(i);
				}
				else
				{
					i++;
				}
			}
		}
	},

	deleteAllOptions: function(oSelect)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			for(var i=oSelect.length-1; i>=0; i--)
			{
				oSelect.remove(i);
			}
		}
	},

	optionCompare: function(record1, record2)
	{
		var value1 = record1.optText.toLowerCase();
		var value2 = record2.optText.toLowerCase();
		if (value1 > value2) return(1);
		if (value1 < value2) return(-1);
		return(0);
	},

	sortSelect: function(oSelect)
	{
		oSelect = BX(select_id);
		if(oSelect)
		{
			var myOptions = [];
			var n = oSelect.options.length;
			for (var i=0;i<n;i++)
			{
				myOptions[i] = {
					optText:oSelect[i].text,
					optValue:oSelect[i].value
				};
			}
			myOptions.sort(this.optionCompare);
			oSelect.length=0;
			n = myOptions.length;
			for(var i=0;i<n;i++)
			{
				var newoption = new Option(myOptions[i].optText, myOptions[i].optValue, false, false);
				oSelect[i]=newoption;
			}
		}
	},

	selectAllOptions: function(oSelect)
	{
		oSelect = BX(select_id);
		if(oSelect)
		{
			var n = oSelect.length;
			for(var i=0;i<n;i++)
			{
				oSelect[i].selected=true;
			}
		}
	},

	selectOption: function(oSelect, opt_value)
	{
		oSelect = BX(select_id);
		if(oSelect)
		{
			var n = oSelect.length;
			for(var i=0;i<n;i++)
			{
				oSelect[i].selected = (oSelect[i].value == opt_value);
			}
		}
	},

	addSelectedOptions: function(oSelect, to_select_id, check_unique, do_sort)
	{
		oSelect = BX(oSelect);
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
			if(oSelect[i].selected)
				this.addNewOption(to_select_id, oSelect[i].value, oSelect[i].text, do_sort, check_unique);
	},

	moveOptionsUp: function(oSelect)
	{
		oSelect = BX(oSelect)
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
		{
			if(oSelect[i].selected && i>0 && oSelect[i-1].selected == false)
			{
				var option1 = new Option(oSelect[i].text, oSelect[i].value);
				var option2 = new Option(oSelect[i-1].text, oSelect[i-1].value);
				oSelect[i] = option2;
				oSelect[i].selected = false;
				oSelect[i-1] = option1;
				oSelect[i-1].selected = true;
			}
		}
	},

	moveOptionsDown: function(oSelect)
	{
		oSelect = BX(oSelect);
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=n-1; i>=0; i--)
		{
			if(oSelect[i].selected && i<n-1 && oSelect[i+1].selected == false)
			{
				var option1 = new Option(oSelect[i].text, oSelect[i].value);
				var option2 = new Option(oSelect[i+1].text, oSelect[i+1].value);
				oSelect[i] = option2;
				oSelect[i].selected = false;
				oSelect[i+1] = option1;
				oSelect[i+1].selected = true;
			}
		}
	}
}



/******* HINT ***************/
// if function has 2 params - the 2nd one is hint html. otherwise hint_html is third and hint_title - 2nd;
// '<div onmouseover="BX.hint(this, 'This is &lt;b&gt;Hint&lt;/b&gt;')"'>;
// BX.hint(el, 'This is <b>Hint</b>') - this won't work, use constructor
BX.hint = function(el, hint_title, hint_html, hint_id)
{
	if (null == hint_html)
	{
		hint_html = hint_title;
		hint_title = '';
	}

	if (null == el.BXHINT)
	{
		el.BXHINT = new BX.CHint({
			parent: el, hint: hint_html, title: hint_title, id: hint_id
		});
		el.BXHINT.Show();
	}
}

BX.hint_replace = function(el, hint_title, hint_html)
{
	if (null == hint_html)
	{
		hint_html = hint_title;
		hint_title = '';
	}

	if (!el || !el.parentNode || !hint_html)
			return null;

	var obHint = new BX.CHint({
		hint: hint_html,
		title: hint_title
	});

	obHint.CreateParent();

	el.parentNode.insertBefore(obHint.PARENT, el);
	el.parentNode.removeChild(el);

	obHint.PARENT.style.marginLeft = '5px';

	return el;
}

BX.CHint = function(params)
{
	this.PARENT = BX(params.parent);

	this.HINT = params.hint;
	this.HINT_TITLE = params.title;

	this.PARAMS = {}
	for (var i in this.defaultSettings)
	{
		if (null == params[i])
			this.PARAMS[i] = this.defaultSettings[i];
		else
			this.PARAMS[i] = params[i];
	}

	if (null != params.id)
		this.ID = params.id;

	this.timer = null;
	this.bInited = false;
	this.msover = true;

	if (this.PARAMS.showOnce)
	{
		this.__show();
		this.msover = false;
		this.timer = setTimeout(BX.proxy(this.__hide, this), this.PARAMS.hide_timeout);
	}
	else if (this.PARENT)
	{
		BX.bind(this.PARENT, 'mouseover', BX.proxy(this.Show, this));
		BX.bind(this.PARENT, 'mouseout', BX.proxy(this.Hide, this));
	}

	BX.addCustomEvent('onMenuOpen', BX.delegate(this.disable, this));
	BX.addCustomEvent('onMenuClose', BX.delegate(this.enable, this));
}

BX.CHint.prototype.defaultSettings = {
	show_timeout: 1000,
	hide_timeout: 500,
	dx: 2,
	showOnce: false,
	preventHide: true,
	min_width: 250
}

BX.CHint.prototype.CreateParent = function(element, params)
{
	if (this.PARENT)
	{
		BX.unbind(this.PARENT, 'mouseover', BX.proxy(this.Show, this));
		BX.unbind(this.PARENT, 'mouseout', BX.proxy(this.Hide, this));
	}

	if (!params) params = {}
	var type = 'icon';

	if (params.type && (params.type == "link" || params.type == "icon"))
		type = params.type;

	if (element)
		type = "element";

	if (type == "icon")
	{
		element = BX.create('IMG', {
			props: {
				src: params.iconSrc
					? params.iconSrc
					: "/bitrix/js/main/core/images/hint.gif"
			}
		});
	}
	else if (type == "link")
	{
		element = BX.create("A", {
			props: {href: 'javascript:void(0)'},
			html: '[?]'
		});
	}

	this.PARENT = element;

	BX.bind(this.PARENT, 'mouseover', BX.proxy(this.Show, this));
	BX.bind(this.PARENT, 'mouseout', BX.proxy(this.Hide, this));

	return this.PARENT;
}

BX.CHint.prototype.Show = function()
{
	this.msover = true;

	if (null != this.timer)
		clearTimeout(this.timer);

	this.timer = setTimeout(BX.proxy(this.__show, this), this.PARAMS.show_timeout);
}

BX.CHint.prototype.Hide = function()
{
	this.msover = false;

	if (null != this.timer)
		clearTimeout(this.timer);

	this.timer = setTimeout(BX.proxy(this.__hide, this), this.PARAMS.hide_timeout);
}

BX.CHint.prototype.__show = function()
{
	if (!this.msover || this.disabled) return;
	if (!this.bInited) this.Init();

	if (this.prepareAdjustPos())
	{
		this.DIV.style.display = 'block';
		this.adjustPos();

		BX.bind(window, 'scroll', BX.proxy(this.__onscroll, this));

		if (this.PARAMS.showOnce)
		{
			this.timer = setTimeout(BX.proxy(this.__hide, this), this.PARAMS.hide_timeout);
		}
	}
}

BX.CHint.prototype.__onscroll = function()
{
	if (!BX.admin || !BX.admin.panel || !BX.admin.panel.isFixed()) return;

	if (this.scrollTimer) clearTimeout(this.scrollTimer);

	this.DIV.style.display = 'none';
	this.scrollTimer = setTimeout(BX.proxy(this.Reopen, this), this.PARAMS.show_timeout);
}

BX.CHint.prototype.Reopen = function()
{
	if (null != this.timer) clearTimeout(this.timer);
	this.timer = setTimeout(BX.proxy(this.__show, this), 50);
}

BX.CHint.prototype.__hide = function()
{
	if (this.msover) return;
	if (!this.bInited) return;

	BX.unbind(window, 'scroll', BX.proxy(this.Reopen, this));

	if (this.PARAMS.showOnce)
	{
		this.Destroy();
	}
	else
	{
		this.DIV.style.display = 'none';
	}
}

BX.CHint.prototype.__hide_immediately = function()
{
	this.msover = false;
	this.__hide();
}

BX.CHint.prototype.Init = function()
{
	this.DIV = document.body.appendChild(BX.create('DIV', {
		props: {className: 'bx-panel-tooltip'},
		style: {display: 'none'},
		children: [
			BX.create('DIV', {
				props: {className: 'bx-panel-tooltip-top-border'},
				html: '<div class="bx-panel-tooltip-corner bx-panel-tooltip-left-corner"></div><div class="bx-panel-tooltip-border"></div><div class="bx-panel-tooltip-corner bx-panel-tooltip-right-corner"></div>'
			}),
			(this.CONTENT = BX.create('DIV', {
				props: {className: 'bx-panel-tooltip-content'},
				children: [
					BX.create('DIV', {
						props: {className: 'bx-panel-tooltip-underlay'},
						children: [
							BX.create('DIV', {props: {className: 'bx-panel-tooltip-underlay-bg'}})
						]
					})
				]
			})),

			BX.create('DIV', {
				props: {className: 'bx-panel-tooltip-bottom-border'},
				html: '<div class="bx-panel-tooltip-corner bx-panel-tooltip-left-corner"></div><div class="bx-panel-tooltip-border"></div><div class="bx-panel-tooltip-corner bx-panel-tooltip-right-corner"></div>'
			})
		]
	}));

	if (this.ID)
	{
		this.CONTENT.insertBefore(BX.create('A', {
			attrs: {href: 'javascript:void(0)'},
			props: {className: 'bx-panel-tooltip-close'},
			events: {click: BX.delegate(this.Close, this)}
		}), this.CONTENT.firstChild)
	}

	if (this.HINT_TITLE)
	{
		this.CONTENT.appendChild(
			BX.create('DIV', {
				props: {className: 'bx-panel-tooltip-title'},
				text: this.HINT_TITLE
			})
		)
	}

	if (this.HINT)
	{
		this.CONTENT_TEXT = this.CONTENT.appendChild(BX.create('DIV', {props: {className: 'bx-panel-tooltip-text'}})).appendChild(BX.create('SPAN', {html: this.HINT}));
	}

	if (this.PARAMS.preventHide)
	{
		BX.bind(this.DIV, 'mouseout', BX.proxy(this.Hide, this));
		BX.bind(this.DIV, 'mouseover', BX.proxy(this.Show, this));
	}

	this.bInited = true;
}

BX.CHint.prototype.setContent = function(content)
{
	this.HINT = content;

	if (this.CONTENT_TEXT)
		this.CONTENT_TEXT.innerHTML = this.HINT;
	else
		this.CONTENT_TEXT = this.CONTENT.appendChild(BX.create('DIV', {props: {className: 'bx-panel-tooltip-text'}})).appendChild(BX.create('SPAN', {html: this.HINT}));
}

BX.CHint.prototype.prepareAdjustPos = function()
{
	this._wnd = {scrollPos: BX.GetWindowScrollPos(),scrollSize:BX.GetWindowScrollSize()};
	return BX.style(this.PARENT, 'display') != 'none';
}

BX.CHint.prototype.getAdjustPos = function()
{
	var res = {}, pos = BX.pos(this.PARENT);

	res.top = pos.bottom + this.PARAMS.dx;

	if (BX.admin && BX.admin.panel.DIV)
	{
		var min_top = BX.admin.panel.DIV.offsetHeight + this.PARAMS.dx;

		if (BX.admin.panel.isFixed())
		{
			min_top += this._wnd.scrollPos.scrollTop;
		}
	}

	if (res.top < min_top)
		res.top = min_top;
	else
	{
		if (res.top + this.DIV.offsetHeight > this._wnd.scrollSize.scrollHeight)
			res.top = pos.top - this.PARAMS.dx - this.DIV.offsetHeight;
	}

	res.left = pos.left;
	if (pos.left < this.PARAMS.dx) pos.left = this.PARAMS.dx;
	else
	{
		floatWidth = this.DIV.offsetWidth;

		var max_left = this._wnd.scrollSize.scrollWidth - floatWidth - this.PARAMS.dx;

		if (res.left > max_left)
			res.left = max_left;
	}

	return res;
}

BX.CHint.prototype.adjustWidth = function()
{
	if (this.bWidthAdjusted) return;

	var w = this.DIV.offsetWidth, h = this.DIV.offsetHeight;

	if (w > this.PARAMS.min_width)
		w = Math.round(Math.sqrt(1.618*w*h));

	if (w < this.PARAMS.min_width)
		w = this.PARAMS.min_width;

	this.DIV.style.width = w + "px";

	if (this._adjustWidthInt)
		clearInterval(this._adjustWidthInt);
	this._adjustWidthInt = setInterval(BX.delegate(this._adjustWidthInterval, this), 5);

	this.bWidthAdjusted = true;
}

BX.CHint.prototype._adjustWidthInterval = function()
{
	if (!this.DIV || this.DIV.style.display == 'none')
		clearInterval(this._adjustWidthInt);

	var
		dW = 20,
		maxWidth = 1500,
		w = this.DIV.offsetWidth,
		w1 = this.CONTENT_TEXT.offsetWidth;

	if (w > 0 && w1 > 0 && w - w1 < dW && w < maxWidth)
	{
		this.DIV.style.width = (w + dW) + "px";
		return;
	}

	clearInterval(this._adjustWidthInt);
}

BX.CHint.prototype.adjustPos = function()
{
	this.adjustWidth();

	var pos = this.getAdjustPos();

	this.DIV.style.top = pos.top + 'px';
	this.DIV.style.left = pos.left + 'px';
}

BX.CHint.prototype.Close = function()
{
	if (this.ID && BX.WindowManager)
		BX.WindowManager.saveWindowOptions(this.ID, {display: 'off'});
	this.__hide_immediately();
	this.Destroy();
}

BX.CHint.prototype.Destroy = function()
{
	if (this.PARENT)
	{
		BX.unbind(this.PARENT, 'mouseover', BX.proxy(this.Show, this));
		BX.unbind(this.PARENT, 'mouseout', BX.proxy(this.Hide, this));
	}

	if (this.DIV)
	{
		BX.unbind(this.DIV, 'mouseover', BX.proxy(this.Show, this));
		BX.unbind(this.DIV, 'mouseout', BX.proxy(this.Hide, this));

		BX.cleanNode(this.DIV, true);
	}
}

BX.CHint.prototype.enable = function(){this.disabled = false;}
BX.CHint.prototype.disable = function(){this.__hide_immediately(); this.disabled = true;}

/* ready */
if (document.addEventListener)
{
	__readyHandler = function()
	{
		document.removeEventListener("DOMContentLoaded", __readyHandler, false);
		runReady();
	}
}
else if (document.attachEvent)
{
	__readyHandler = function()
	{
		if (document.readyState === "complete")
		{
			document.detachEvent("onreadystatechange", __readyHandler);
			runReady();
		}
	}
}

function bindReady()
{
	if (!readyBound)
	{
		readyBound = true;

		if (document.readyState === "complete")
		{
			return runReady();
		}

		if (document.addEventListener)
		{
			document.addEventListener("DOMContentLoaded", __readyHandler, false);
			window.addEventListener("load", runReady, false);
		}
		else if (document.attachEvent) // IE
		{
			document.attachEvent("onreadystatechange", __readyHandler);
			window.attachEvent("onload", runReady);

			var toplevel = false;
			try {toplevel = (window.frameElement == null);} catch(e) {}

			if (document.documentElement.doScroll && toplevel)
				doScrollCheck();
		}
	}

	return null;
}


function runReady()
{
	if (!BX.isReady)
	{
		if (!document.body)
			return setTimeout(runReady, 15);

		BX.isReady = true;


		if (readyList && readyList.length > 0)
		{
			var fn, i = 0;
			while (readyList && (fn = readyList[i++]))
			{
				try{
					fn.call(document);
				}
				catch(e){
					BX.debug('BX.ready error: ', e);
				}
			}

			readyList = null;
		}
		// TODO: check ready handlers binded some other way;
	}
	return null;
}

// hack for IE
function doScrollCheck()
{
	if (BX.isReady)
		return;

	try {document.documentElement.doScroll("left");} catch( error ) {setTimeout(doScrollCheck, 1); return;}

	runReady();
}
/* \ready */

function _adjustWait()
{
	if (!this.bxmsg) return;

	var arContainerPos = BX.pos(this),
		div_top = arContainerPos.top;

	if (div_top < BX.GetDocElement().scrollTop)
		div_top = BX.GetDocElement().scrollTop + 5;

	this.bxmsg.style.top = (div_top + 5) + 'px';

	if (this == BX.GetDocElement())
	{
		this.bxmsg.style.right = '5px';
	}
	else
	{
		this.bxmsg.style.left = (arContainerPos.right - this.bxmsg.offsetWidth - 5) + 'px';
	}
}

function _checkDisplay(ob, displayType)
{
	if (typeof displayType != 'undefined')
		ob.BXDISPLAY = displayType;

	var d = ob.style.display || BX.style(ob, 'display');
	if (d != 'none')
	{
		ob.BXDISPLAY = ob.BXDISPLAY || d;
		return true;
	}
	else
	{
		ob.BXDISPLAY = ob.BXDISPLAY || 'block';
		return false;
	}
}

function _processTpl(tplNode, cb, bKillTpl)
{
	if (tplNode)
	{
		if (bKillTpl)
			tplNode.parentNode.removeChild(tplNode);

		var res = {}, nodes = BX.findChildren(tplNode, {attribute: 'data-role'}, true);

		for (var i = 0, l = nodes.length; i < l; i++)
		{
			res[nodes[i].getAttribute('data-role')] = nodes[i];
		}

		cb.apply(tplNode, [res]);
	}
}

function _checkNode(obj, params)
{
	params = params || {};

	if (BX.type.isFunction(params))
		return params.call(window, obj);

	if (!params.allowTextNodes && !BX.type.isElementNode(obj))
		return false;
	var i,j,len;
	for (i in params)
	{
		switch(i)
		{
			case 'tag':
			case 'tagName':
				if (BX.type.isString(params[i]))
				{
					if (obj.tagName.toUpperCase() != params[i].toUpperCase())
						return false;
				}
				else if (params[i] instanceof RegExp)
				{
					if (!params[i].test(obj.tagName))
						return false;
				}
			break;

			case 'class':
			case 'className':
				if (BX.type.isString(params[i]))
				{
					if (!BX.hasClass(obj, params[i]))
						return false;
				}
				else if (params[i] instanceof RegExp)
				{
					if (!BX.type.isString(obj.className) || !params[i].test(obj.className))
						return false;
				}
			break;

			case 'attr':
			case 'attribute':
				if (BX.type.isString(params[i]))
				{
					if (!obj.getAttribute(params[i]))
						return false;
				}
				else if (BX.type.isArray(params[i]))
				{
					for (j = 0, len = params[i].length; j < len; j++)
					{
						if (params[i] && !obj.getAttribute(params[i]))
							return false;
					}
				}
				else
				{
					for (j in params[i])
					{
						var q = obj.getAttribute(j);
						if (params[i][j] instanceof RegExp)
						{
							if (!BX.type.isString(q) || !params[i][j].test(q))
								return false;
						}
						else
						{
							if (q != '' + params[i][j])
								return false;
						}
					}
				}
			break;

			case 'property':
				if (BX.type.isString(params[i]))
				{
					if (!obj[params[i]])
						return false;
				}
				else if (BX.type.isArray(params[i]))
				{
					for (j = 0, len = params[i].length; j < len; j++)
					{
						if (params[i] && !obj[params[i]])
							return false;
					}
				}
				else
				{
					for (j in params[i])
					{
						if (BX.type.isString(params[i][j]))
						{
							if (obj[j] != params[i][j])
								return false;
						}
						else if (params[i][j] instanceof RegExp)
						{
							if (!BX.type.isString(obj[j]) || !params[i][j].test(obj[j]))
								return false;
						}
					}
				}
			break;

			case 'callback':
				return params[i](obj);
			break;
		}
	}

	return true;
}

function _checkCssList()
{
	var linksCol = document.getElementsByTagName('LINK'), links = [];

	if(!!linksCol && linksCol.length > 0)
	{
		for(var i=0;i<linksCol.length;i++)
		{
			links.push(linksCol[i]);
		}
	}

	if(!!window.arKernelCSS && BX.type.isArray(arKernelCSS))
	{
		links = BX.util.array_merge(links, arKernelCSS);
	}

	for (var i = 0; i < links.length; i++)
	{
		var href = BX.type.isDomNode(links[i]) ? links[i].getAttribute('href') : links[i];
		if (!!href && href.replace)
		{
			cssList.push(href
				.replace(/\.css(\?\d*)/, '.css')
				.replace(/^(http[s]*:)*\/\/[^\/]+/i, '')
			);
		}
	}
	_checkCssList = BX.DoNothing;
}

/********* Check for currently loaded core scripts ***********/
function _isScriptLoaded(fileSrc)
{
	return (
		BX.util.in_array(fileSrc, arKernelJS)
		||fileSrc.indexOf('/core/core.js') > 0
		||fileSrc.indexOf('/core_access.js') >= 0 && !!BX.Access
		||fileSrc.indexOf('/core_admin.js') >= 0 && !!BX.admin
		||fileSrc.indexOf('/core_admin_interface.js') >= 0 && !!BX.adminPanel
		||fileSrc.indexOf('/core_admin_login.js') >= 0 && !!BX.adminLogin
		||fileSrc.indexOf('/core_ajax.js') >= 0 && !!BX.ajax
		||fileSrc.indexOf('/core_autosave.js') >= 0 && !!BX.CAutoSave
		||fileSrc.indexOf('/core_date.js') >= 0 && !!BX.date
		||fileSrc.indexOf('/core_finder.js') >= 0 && !!BX.Finder
		||fileSrc.indexOf('/core_fx.js') >= 0 && !!BX.easing
		||fileSrc.indexOf('/core_image.js') >= 0 && !!BX.CImageView
		||fileSrc.indexOf('/core_ls.js') >= 0 && !!BX.localStorage
		||fileSrc.indexOf('/core_popup.js') >= 0 && !!BX.PopupWindowManager
		||fileSrc.indexOf('/core_tags.js') >= 0 && !!BX.TagsWindowArea
		||fileSrc.indexOf('/core_timer.js') >= 0 && !!BX.timer
		||fileSrc.indexOf('/core_tooltip.js') >= 0 && !!BX.tooltip
		||fileSrc.indexOf('/core_translit.js') >= 0 && !!BX.translit
		||fileSrc.indexOf('/core_window.js') >= 0 && !!BX.WindowManager
		||fileSrc.indexOf('/jquery-') >= 0 && !!window.jQuery
	);
}

/* garbage collector */
function Trash()
{
	var i,len;

	for (i = 0, len = garbageCollectors.length; i<len; i++)
	{
		try {
			garbageCollectors[i].callback.apply(garbageCollectors[i].context || window);
			delete garbageCollectors[i];
			garbageCollectors[i] = null;
		} catch (e) {}
	}

	try {BX.unbindAll();} catch(e) {}
/*
	for (i = 0, len = proxyList.length; i < len; i++)
	{
		try {
			delete proxyList[i];
			proxyList[i] = null;
		} catch (e) {}
	}
*/
}

if(window.attachEvent) // IE
	window.attachEvent("onunload", Trash);
else if(window.addEventListener) // Gecko / W3C
	window.addEventListener('unload', Trash, false);
else
	window.onunload = Trash;
/* \garbage collector */

// set empty ready handler
BX(BX.DoNothing);
window.BX = BX;
BX.browser.addGlobalClass();
BX.browser.addGlobalFeatures(["boxShadow", "borderRadius", "flexWrap", "boxDirection", "transition", "transform"])
})(window);
