;(function(){

if (!!BX.adminPanel)
	return;

/*************************** admin panel **************************************/

BX.adminPanel = function()
{
	this.buttons = [];
	this.panel = null;

	this.modifyFormElements = BX.adminFormTools.modifyFormElements;
	this.modifyFormElement = BX.adminFormTools.modifyFormElement;

	this._showMenu = function(e)
	{
		if (this.CONFIG.MENU)
		{
			BX.adminShowMenu(this.BUTTON, this.CONFIG.MENU, {active_class: 'adm-header-language-active'});
		}

		return BX.PreventDefault(e);
	}

	BX.ready(BX.defer(this.Init, this));
}

BX.adminPanel.isFixed = BX.False;

BX.adminPanel.prototype.Init = function()
{
	this.panel = BX('bx-panel');

	if (!!this.panel)
	{
		for (var i = 0; i<this.buttons.length; i++)
		{
			this.buttons[i].BUTTON = BX(this.buttons[i].ID);
			if (this.buttons[i].BUTTON)
			{
				if (this.buttons[i].CONFIG.MENU)
				{
					this.setButtonMenu(this.buttons[i]);
				}
			}
		}

		(BX.defer(this._recountWrapHeight, this))();
	}
}

BX.adminPanel.prototype.registerButton = function(id, config)
{
	this.buttons.push({ID: id, CONFIG: config});
}

BX.adminPanel.prototype.setButtonMenu = function(button)
{
	BX.bind(button.BUTTON, 'click', BX.delegate(this._showMenu, button))
}

BX.adminPanel.prototype.isFixed = function()
{
	return BX.hasClass(document.documentElement, 'adm-header-fixed');
}

BX.adminPanel.prototype.Fix = function(el)
{
	var bFixed = this.isFixed();

	if (bFixed)
	{
		this.panel.parentNode.style.height = 'auto';
		BX.removeClass(document.documentElement, 'adm-header-fixed');
		el.title = BX.message('JSADM_PIN_ON');
	}
	else
	{
		BX.addClass(document.documentElement, 'adm-header-fixed');
		el.title = BX.message('JSADM_PIN_OFF');
		(BX.defer(this._recountWrapHeight, this))();
	}

	BX.userOptions.save('admin_panel', 'settings', 'fix', (bFixed ? 'off':'on'));
	BX.onCustomEvent('onAdminPanelFix', [!bFixed]);
}

BX.adminPanel.prototype.addDesktop = function()
{
	(new BX.CAdminDialog({
		'content_url': '/bitrix/components/bitrix/desktop/admin_settings.php?lang='+BX.message('LANGUAGE_ID')+'&bxpublic=Y',
		'content_post': 'sessid='+BX.bitrix_sessid()+'&type=desktop&desktop_page=0&action=new&desktop_backurl=/bitrix/admin/',
		'draggable': true,
		'resizable': true,
		'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
	})).Show();
}

BX.adminPanel.prototype.recalcDesktopSettingsDialog = function(e)
{
	if(!e)
		e = window.event;

	col_count = this.value;
	if (e.type == 'blur' && col_count.length <= 0)
	{
		col_count = current_col_count;
		BX('SETTINGS_COLUMNS').value = col_count;
	}
	else if (e.type == 'keyup' && (parseInt(col_count) <= 0	|| parseInt(col_count) >= 10))
	{
		current_col_count = col_count = 2;
		BX('SETTINGS_COLUMNS').value = col_count;
	}
	else if (e.type == 'keyup' && col_count.length > 0)
		current_col_count = col_count;

	var tableNode = BX.findParent(this, {'tag':'tbody'})

	var arItems = BX.findChildren(tableNode, {'tag':'tr', 'class':'bx-gd-admin-settings-col'}, true);
	if (!arItems)
		arItems = [];

	for (var i = 0; i < arItems.length; i++)
	{
		if (i >= col_count)
			arItems[i].parentNode.removeChild(arItems[i]);
	}

	var col_add = col_count - i;

	for (var i = 0; i < col_add; i++)
	{
		tableNode.appendChild(BX.create('tr', {
			props: {
				'className': 'bx-gd-admin-settings-col'
			},
			children: [
				BX.create('td', {
					attrs: {
						'width': '40%'
					},
					html: BX.message('langGDSettingsDialogRowTitle') + (parseInt(arItems.length) + parseInt(i) + 1)
				}),
				BX.create('td', {
					attrs: {
						'width': '60%'
					},
					children: [
						BX.create('input', {
							attrs: {
								'type': 'text',
								'size': '5',
								'maxlength': '6'
							},
							props: {
								'id': 'SETTINGS_COLUMN_WIDTH_' + (arItems.length + i),
								'name': 'SETTINGS_COLUMN_WIDTH_' + (arItems.length + i),
								'value': ''
							}
						})
					]
				})
			]
		}));
	}
}

BX.adminPanel.prototype.setTitle = function(title)
{
	document.title = BX.message('TITLE_PREFIX') + title;
	var p = BX('adm-title');
	if (p)
	{
		if (p.firstChild && p.firstChild.nodeType == 3)
		{
			p.replaceChild(document.createTextNode(title), p.firstChild);
		}
		else if (p.firstChild)
		{
			p.insertBefore(p.firstChild, document.createTextNode(title));
		}
		else
		{
			BX.adjust(p, {text: title});
		}
	}
}

BX.adminPanel.prototype._recountWrapHeight = function()
{
	if (this.isFixed())
		BX.adminPanel.panel.parentNode.style.height = BX.adminPanel.panel.offsetHeight + 'px';
	BX.onCustomEvent(this, 'onAdminPanelChange');
}

BX.adminPanel.prototype.Notify = function(str)
{
	if (!BX.isReady)
	{
		var _args = arguments;
		BX.ready(BX.defer(function() {BX.adminPanel.Notify.apply(this, _args);}));
		return;
	}

	if (null == BX.adminPanel.NOTIFY)
	{
		BX.adminPanel.NOTIFY = BX.adminPanel.panel.appendChild(BX.create('DIV', {
			props: {className: 'adm-warning-block'},
			html:
				'<span class="adm-warning-text">'+(str||'&nbsp;')+'</span><span onclick="BX.adminPanel.hideNotify(this.parentNode)" class="adm-warning-close"></span>'
		}));

	}
	else
	{
		BX.adminPanel.NOTIFY.firstChild.innerHTML = str||'&nbsp;';
	}

	BX.removeClass(BX.adminPanel.NOTIFY, 'adm-warning-animate');

	(BX.defer(this._recountWrapHeight, this))();
	setTimeout(BX.proxy(this._recountWrapHeight, this), 310);
};

BX.adminPanel.prototype.hideNotify = function(element)
{
	var element = BX.type.isDomNode(element)? element: this;

	if (!!element && !!element.parentNode && !!element.parentNode.parentNode)
	{
		BX.addClass(element, 'adm-warning-animate');
	}

	if (BX.type.isDomNode(element) && element.getAttribute('data-ajax') == "Y")
	{
		var notifyId = parseInt(element.getAttribute('data-id'));
		if (notifyId > 0)
		{
			BX.ajax({
				url: '/bitrix/admin/admin_notify.php',
				method: 'POST',
				dataType: 'json',
				data: {'ID' : notifyId, 'sessid': BX.bitrix_sessid()}
			});
		}
	}

	(BX.defer(this._recountWrapHeight, this))();
	setTimeout(BX.proxy(this._recountWrapHeight, this), 310);
}

BX.adminPanel.prototype.Redirect = function(args, url, e)
{
	var bShift = false;
	if(args && args.length > 0)
		e = args[0];
	if(!e)
		e = window.event;
	if(e)
		bShift = e.shiftKey;

	if(bShift)
		window.open(url);
	else
	{
		ShowWaitWindow();
		window.location.href=url;
	}
};

/**************************** admin forms *************************************/

BX.adminFormTools = {
	modifyFormElements: function(tbl, types)
	{
		var el = BX.findFormElements(tbl);

		if (el && el.length > 0)
		{
			for (var i = 0; i < el.length; i++)
			{
				BX.adminFormTools.modifyFormElement(el[i], types)
			}
		}
	},

	modifyFormElement: function(el, types)
	{
		if (typeof types == 'undefined' || !BX.type.isArray(types))
			types = ['checkbox', 'file'];

		if (el && BX.type.isElementNode(el) && !!el.type)
		{
			if (BX.util.in_array('*', types) || BX.util.in_array(el.type, types))
			{
				switch(el.type)
				{
					case 'checkbox': return BX.adminFormTools.modifyCheckbox(el);
					case 'file': return BX.adminFormTools.modifyFile(el);

					case 'select-one':
					case 'select-multiple':
						return BX.adminFormTools.modifySelect(el);

					case 'button':
					case 'submit':
					case 'reset':
						return BX.adminFormTools.modifyButton(el);

					default: return el;
				}
			}
			else
			{
				return el;
			}
		}
	},

	modifyCheckbox: function(el)
	{
		if ((!BX.browser.IsIE() || BX.browser.IsIE9()) && BX.type.isElementNode(el) && el.tagName.toUpperCase() == 'INPUT' && el.type.toUpperCase() == 'CHECKBOX')
		{
			if (!BX.hasClass(el, 'adm-designed-checkbox'))
			{
				if (!el.id)
					el.id = 'designed_checkbox_' + Math.random();

				var label = BX.create('LABEL', {
					props: {
						className: 'adm-designed-checkbox-label',
						htmlFor: el.id,
						title: el.title
					}
				});

				BX.addClass(label, el.className);
				BX.addClass(el, 'adm-designed-checkbox');

				if (!!el.nextSibling)
					el.parentNode.insertBefore(label, el.nextSibling);
				else
					el.parentNode.appendChild(label);
			}
		}
	},

	modifyFile: function(el)
	{
		if (!BX.hasClass(el, 'adm-designed-file'))
		{
			var wrap = BX.create('SPAN', {
				props: {className: 'adm-input-file'},
				html: '<span>' + (!!el.multiple ? BX.message('JSADM_FILES') : BX.message('JSADM_FILE')) + '</span>'
			});

			BX.bind(el, 'change', BX.adminFormTools._modified_file_onchange);

			BX.addClass(el, 'adm-designed-file');

			if (el.parentNode)
				el.parentNode.insertBefore(wrap, el);

			wrap.appendChild(el);

			return wrap;
		}
		else
		{
			return el;
		}
	},

	_modified_file_onchange: function()
	{
		var v = this.files || [this.value], s = '';
		if (!v || v.length <= 0)
		{
			s = (!!el.multiple ? BX.message('JSADM_FILES') : BX.message('JSADM_FILE'));
		}
		else
		{
			var s = '';
			for(var i = 0; i < v.length; i++)
			{
				var n = v[i].name || v[i];
				var p = Math.max(n.lastIndexOf('/'), n.lastIndexOf('\\'));
				if (p > 0)
					n = n.substring(p+1, n.length);
				s += (s == '' ? '' : ', ') + n;
			}
		}

		this.parentNode.firstChild.innerHTML = s;
	},

	// should not be called in modifyFormElements!
	modifySelect: function(el)
	{
		if (BX.type.isElementNode(el) && el.tagName.toUpperCase() == 'SELECT')
		{
			if (el.type == 'select-one')
			{
				if (!BX.hasClass(el, 'adm-select'))
				{
					var wrap = BX.create('SPAN', {
						props: {className: 'adm-select-wrap'}
					});

					BX.addClass(el, 'adm-select');

					if (el.parentNode)
						el.parentNode.insertBefore(wrap, el);

					wrap.appendChild(el);

					return wrap;
				}
			}
		}
	},

	modifyButton: function(el)
	{
		if (BX.type.isElementNode(el) && el.tagName.toUpperCase() == 'INPUT'
			&& (el.type == 'submit' || el.type == 'reset' || el.type == 'button')
			&& !BX.hasClass(el, 'adm-btn')
		)
		{
			var wrap = BX.create('SPAN', {props: {className: 'adm-btn-wrap ' + el.className}});

			el.className = 'adm-btn';

			if (el.parentNode)
				el.parentNode.insertBefore(wrap, el);

			wrap.appendChild(el);
			return wrap;
		}
		else
		{
			return el;
		}
	}
}

/*************************** admin menu ***************************************/

BX.adminMenu = function()
{
	this.activeSection = '';
	this.oSections = {},
	this.items = [];

	this.dest = {item: null, fav: null};

	var _ondestdragfinish = {
		item: BX.delegate(function(node)
		{
			if (typeof node.BXMENUITEM == 'undefined' || !this.items[node.BXMENUITEM])
				return;

			BX.adminFav.add(
				node.innerText||node.textContent,
				this.items[node.BXMENUITEM].CONFIG.URL,
				this.items[node.BXMENUITEM].CONFIG.ID,
				this.items[node.BXMENUITEM].CONFIG.MODULE_ID,
				BX.adminFav.refresh
			);
		}, this),
		fav: BX.delegate(function(node)
		{
			if (typeof node.BXMENUITEM == 'undefined' || !this.items[node.BXMENUITEM] || !this.items[node.BXMENUITEM].CONFIG.FAV_ID)
				return;

			BX.adminFav.del(this.items[node.BXMENUITEM].CONFIG.FAV_ID, BX.adminFav.refresh);
		}, this)
	};

	var __r = function(){jsDD.refreshDestArea(this)};
	var _ondestdragstart = BX.delegate(function(node)
	{
		if (typeof node.BXMENUITEM == 'undefined' || !this.items[node.BXMENUITEM])
			return;

		var key = !!this.items[node.BXMENUITEM].CONFIG.FAV_ID ? 'fav' : 'item';

		if (!this.dest[key])
		{
			var windowSize = BX.GetWindowInnerSize();

			this.dest[key] = document.body.appendChild(BX.create('DIV', {
				props: {BXTYPEKEY: key},
				style: {
					position: 'fixed',
					top: parseInt(windowSize.innerHeight / 2 - 100) + 'px',
					left: parseInt(windowSize.innerWidth / 2 - 200) + 'px',
					fontSize: '30px',
					textAlign: 'center',
					padding: '100px',
					width: '400px',
					backgroundColor: 'white',
					border: 'solid 2px ' + (key == 'fav' ? 'red' : 'orange')
				},
				html: (key == 'fav' ? BX.message('JSADM_FAV_DEL') : BX.message('JSADM_FAV_ADD'))
			}));

			this.dest[key].onbxdestdraghover = function() {this.style.backgroundColor = this.BXTYPEKEY == 'fav' ? '#FFEAEA' : '#FFFFEA'};
			this.dest[key].onbxdestdraghout = function() {this.style.backgroundColor = 'white'};
			this.dest[key].onbxdestdragfinish = _ondestdragfinish[key];

			jsDD.registerDest(this.dest[key]);
		}
		else
		{
			this.dest[key].style.display = 'block';
			jsDD.refreshDestArea(this.dest[key]);
		}

		BX.bind(window, 'scroll', BX.proxy(__r, this.dest[key]));
	}, this);

	var _ondestdragstop = BX.delegate(function()
	{
		if (this.dest.item)
		{
			this.dest.item.style.display = 'none';
			BX.unbind(window, 'scroll', BX.proxy(__r, this.dest.item));
		}
		if (this.dest.fav)
		{
			this.dest.fav.style.display = 'none';
			BX.unbind(window, 'scroll', BX.proxy(__r, this.dest.fav));
		}
	}, this);

	this._onitemdragstart = function()
	{
		_ondestdragstart(this.NODE);

		if (null == this.MIRROR)
		{
			this.MIRROR = document.body.appendChild(BX.create('DIV', {
				style: {
					position: 'absolute',
					border: 'solid 1px gray'
				},
				html: this.NODE.innerHTML
			}));
		}

		this.MIRROR.style.display = 'block';
	};

	this._onitemdrag = function(x, y)
	{
		this.MIRROR.style.left = parseInt(x - this.MIRROR.offsetWidth/2) + 'px';
		this.MIRROR.style.top = parseInt(y - this.MIRROR.offsetHeight/2) + 'px';
	};

	this._onitemdragstop = function()
	{
		_ondestdragstop();
		this.MIRROR.style.display = 'none';
	};

	BX.ready(BX.delegate(this.Init, this));
};

BX.adminMenu.prototype.Init = function()
{
	if (!!BX('bx_menu_panel', true))
	{
		new BX.adminMenuResizer(BX('bx_menu_panel', true));
		setTimeout(BX.delegate(this.InitDeferred, this), 200);
	}
};

BX.adminMenu.prototype.InitDeferred = function()
{
	for(var i=0; i<this.items.length; i++)
	{
		this._registerItem(i);
	}
};

BX.adminMenu.prototype.setActiveSection = function(section_id)
{
	this.activeSection = section_id;
};

BX.adminMenu.prototype.setOpenedSections = function(sSections)
{
	var aSect = sSections.split(',');
	for(var i in aSect)
	{
		this.oSections[aSect[i]] = true;
	}
};

BX.adminMenu.prototype.GlobalMenuClick = function(id)
{
	if (id == this.activeSection)
	{
		return;
	}

	if (!!this.activeSection)
	{
		BX.removeClass(BX('global_menu_' + this.activeSection, true), 'adm-main-menu-item-active');
		BX.hide(BX('global_submenu_' + this.activeSection, true));
	}

	this.activeSection = id;

	BX.addClass(BX('global_menu_' + this.activeSection, true), 'adm-main-menu-item-active');
	BX.show(BX('global_submenu_' + this.activeSection, true));

	BX.onCustomEvent(this, 'onMenuChange');
};

BX.adminMenu.prototype.toggleSection = function(cell, div_id, level)
{
	var res;
	if (BX.hasClass(cell, 'adm-sub-submenu-open'))
	{
		res = false;
		BX.removeClass(cell, 'adm-sub-submenu-open');
	}
	else
	{
		res = true;
		BX.addClass(cell, 'adm-sub-submenu-open');
	}

	if(level <= 2)
	{
		this.oSections[div_id] = res;

		var sect='';
		for(var i in this.oSections)
		{
			if(this.oSections[i] == true)
			{
				sect += (sect != ''? ',':'')+i;
			}
		}

		BX.userOptions.save('admin_menu', 'pos', 'sections', sect);
	}

	BX.onCustomEvent(this, 'onMenuChange');

	return res;
};

BX.adminMenu.prototype.toggleDynSection = function(padding, cell, module_id, div_id, level)
{
	if (this.toggleSection(cell, div_id, level) && !cell.BXLOAD)
	{
		cell.BXLOAD = true;

		var img = cell.appendChild(BX.create('SPAN', {
			props: {className: 'adm-submenu-loading adm-sub-submenu-block'},
			style: {marginLeft: parseInt(padding) + 'px'},
			text: BX.message('JS_CORE_LOADING')
		}));

		BX.defer(function(){
			BX.addClass(img, 'adm-submenu-loading-animate');
		})();

		BX.ajax.get(
			'/bitrix/admin/get_menu.php',
			{
				lang: BX.message('LANGUAGE_ID'),
				admin_mnu_module_id: module_id,
				admin_mnu_menu_id: div_id
			},
			function(result)
			{
				result = BX.util.trim(result);
				if (result != '')
				{
					cell.removeChild(img);
					cell.innerHTML += result;
				}
				else
				{
					img.innerHTML = BX.message('JS_CORE_NO_DATA');
				}

				BX.onCustomEvent(this, 'onMenuChange');
			}
		);
	}
};

BX.adminMenu.prototype._registerItem = function(i)
{
	this.items[i].NODE = BX(this.items[i].ID);
	this.items[i].NODE.BXMENUITEM = i;
	if (this.items[i].NODE)
	{
		this.items[i].NODE.onbxdragstart = BX.delegate(this._onitemdragstart, this.items[i]);
		this.items[i].NODE.onbxdrag = BX.delegate(this._onitemdrag, this.items[i]);
		this.items[i].NODE.onbxdragstop = BX.delegate(this._onitemdragstop, this.items[i]);
		jsDD.registerObject(this.items[i].NODE)
	}
}

BX.adminMenu.prototype.registerItem = function(id, config)
{
	this.items.push({ID: id, CONFIG: config});

	if (BX.isReady)
	{
		this._registerItem(this.items.length-1);
	}
};

/*************************** admin menu resizer *******************************/

BX.adminMenuResizer = function(node)
{
	this.min_width = 240;
	this.node = node;
	this.dragger = document.body.appendChild(BX.create('DIV', {
		style: {
			position: 'absolute',
			width: '5px',
			cursor: 'e-resize',
			zIndex: 10000
		},
		props: {
			onbxdragstart: BX.delegate(this.Start, this),
			onbxdrag: BX.delegate(this.Drag, this),
			onbxdragstop: BX.delegate(this.Save, this)
		}
	}));

	BX.addCustomEvent(BX.adminMenu, 'onMenuChange', BX.delegate(this.SetPos, this));
	BX.addCustomEvent('onAdminTabsChange', BX.delegate(this.SetPos, this));
	this.SetPos();

	jsDD.registerObject(this.dragger);
};

BX.adminMenuResizer.prototype.SetPos = function()
{
	var node_pos = BX.pos(this.node);
	this.pos = node_pos.width;

	BX.adjust(this.dragger, {
		style: {
			height: this.node.parentNode.offsetHeight + 'px',
			top: node_pos.top + 'px',
			left: (node_pos.width - 2) + 'px'
		}
	})
}

BX.adminMenuResizer.prototype.Start = function()
{
	BX.setUnselectable(document.body);
	document.body.style.cursor = 'e-resize';
};

BX.adminMenuResizer.prototype.Drag = function(x, y)
{
	this.pos = Math.max(x, this.min_width);

	this.node.style.width = this.pos + 'px'
	this.dragger.style.left = (this.pos - 2) + 'px';

	BX.onCustomEvent(BX.adminMenu, 'onAdminMenuResize', [this.pos]);
};

BX.adminMenuResizer.prototype.Save = function()
{
	BX.onCustomEvent(BX.adminMenu, 'onAdminMenuResize', [this.pos]);

	BX.setSelectable(document.body);
	document.body.style.cursor = '';

	// check BX.fireEvent()
	if(window.onresize)
		window.onresize();

	BX.userOptions.save('admin_menu', 'pos', 'width', this.pos);
};


/*************************** admin favorites **********************************/

BX.adminFav = {
	url: '/bitrix/admin/favorite_act.php',
	add: function(nameToSave,urlToSave,menu_id,module_id,callback)
	{
		var urlToSend = BX.adminFav.url + "?act=add",
			data = {
				sessid: BX.bitrix_sessid(),
				name: nameToSave
			};

		if(urlToSave)
			data.addurl = urlToSave;

		if(menu_id)
			data.menu_id = menu_id;

		if (BX.type.isFunction(module_id))
		{
			callback = module_id;
			module_id = '';
		}

		if(module_id)
		{
			data.module_id = module_id;
		}

		if(!callback)
		{
			callback = function(result)
			{
				if(result)
				{
					BX.adminFav.refresh(result);
					alert(BX.message('JSADM_FAV_ADD_SUC'));
				}
				else
				{
					alert(BX.message('JSADM_FAV_ADD_ERR'));
				}
			}
		}

		return BX.ajax.post(urlToSend,data,callback);
	},

	del: function(id, callback)
	{
		var urlToSend = BX.adminFav.url + "?act=delete&id="+id,
			data = {sessid: BX.bitrix_sessid()};

		if(!callback)
		{
			callback = function(result)
			{
				if(result)
				{
					BX.adminFav.refresh(result);
					alert(BX.message('JSADM_FAV_DEL_SUC'));
				}
				else
				{
					alert(BX.message('JSADM_FAV_DEL_ERR'));
				}
			}
		}

		return BX.ajax.post(urlToSend,data,callback);
	},

	get: function(callback)
	{
		var urlToSend = BX.adminFav.url + "?act=get_list",
			data = {sessid: phpVars.bitrix_sessid};

		if(!callback)
		{
			callback = function(result)
			{
				if(console)
					console.log(result);
			}
		}

		return BX.ajax.post(urlToSend,data,callback);
	},

	getMenuHtml: function(callback)
	{
		var urlToSend = BX.adminFav.url + "?act=get_menu_html";
		var data = {sessid: BX.bitrix_sessid()};

		if(!callback)
			callback = function(result)
				{
					if(console)
						console.log(result);
				}

		return BX.ajax.post(urlToSend,data,callback);
	},

	refresh: function(htmlMenu)
	{
		if(!htmlMenu)
			return;

		var menu = BX("_global_menu_desktop");
		menu.innerHTML = htmlMenu;

		BX.adminFav.setActiveItem();
	},

	setActiveItem: function()
	{
		var menu = BX("menucontainer");
		var activeItem = BX.findChild(menu, { className: "adm-submenu-item-active"}, true);

		if(!activeItem)
			return false;

		var itemNameLink = BX.findChild(activeItem, { className: "adm-submenu-item-name-link"}, true).href;
		var itemNameLinkText = BX.findChild(activeItem, { className: "adm-submenu-item-name-link-text"}, true);

		var itemText = itemNameLinkText.textContent || itemNameLinkText.innerText;
		itemText =  BX.util.trim(itemText);

		var favMenu = BX("_global_menu_desktop");

		var favMenuItems = BX.findChildren(favMenu, { className: "adm-sub-submenu-block"}, true);

		for(var idx in favMenuItems)
		{
			var favItemNameLink = BX.findChild(favMenuItems[idx], { className: "adm-submenu-item-name-link"},true).href;
			var favItemNameLinkText = BX.findChild(favMenuItems[idx], { className: "adm-submenu-item-name-link-text"}, true);
			var favItemText = favItemNameLinkText.textContent || favItemNameLinkText.innerText;
			favItemText = BX.util.trim(favItemText);

			if((favItemNameLink == itemNameLink) && itemNameLink != "javascript:void(0)")
			{
				BX.addClass(favMenuItems[idx],"adm-submenu-item-active");
				return true;
			}

			if(itemText && itemText == favItemText)
			{
				BX.addClass(favMenuItems[idx],"adm-submenu-item-active");
				return true;
			}
		}

		return false;
	},

	titleLinkClick: function(el, fav_id, items_id)
	{
		BX.adminFav.titleLink = el;
		BX.adminFav.titleNode = el.parentNode;

		if (!el.BXFAVSET)
		{
			el.BXFAVID = fav_id;
			el.BXITEMSID = items_id;

			if (!!el.BXFAVID)
				BX.adminFav._titleLinkClickDel()
			else
				BX.adminFav._titleLinkClickAdd()

			el.BXFAVSET = true;
		}
	},

	_titleLinkClickAdd: function(items_id)
	{
		BX.adminFav.add(
			BX.adminFav.titleNode.textContent||BX.adminFav.titleNode.innerText,
			BX.adminHistory.pushSupported ? window.location.href : BX('navchain-link').getAttribute('href'),
			BX.adminFav.titleLink.BXITEMSID,
			'',
			function(result) {
				if (result)
				{
					BX.adminFav.refresh(result);

					// we should somehow get fav_id here
					BX.adminFav.titleLink.BXFAVID = BX("last-fav-addition-id").textContent||BX("last-fav-addition-id").innerText;
					BX.addClass(BX.adminFav.titleLink, 'adm-fav-link-active');
					BX.adminFav.titleLink.title = BX.message('JSADM_FAV_DEL');

					BX.unbind(BX.adminFav.titleLink, 'click', BX.adminFav._titleLinkClickAdd);
					BX.bind(BX.adminFav.titleLink, 'click', BX.adminFav._titleLinkClickDel);
				}
				else
				{
					alert(BX.message('JSADM_FAV_ADD_ERR'));
				}
			}
		);
	},

	_titleLinkClickDel: function()
	{
		BX.adminFav.del(
			BX.adminFav.titleLink.BXFAVID,
			function(result) {
				if (result)
				{
					BX.adminFav.refresh(result);
					BX.removeClass(BX.adminFav.titleLink, 'adm-fav-link-active');
					BX.adminFav.titleLink.removeAttribute('data-fav-id');
					BX.adminFav.titleLink.title = BX.message('JSADM_FAV_ADD');

					BX.unbind(BX.adminFav.titleLink, 'click', BX.adminFav._titleLinkClickDel);
					BX.bind(BX.adminFav.titleLink, 'click', BX.adminFav._titleLinkClickAdd);
				}
				else
				{
					alert(BX.message('JSADM_FAV_DEL_ERR'));
				}
			}
		);
	},

	onMenuChange: function()
	{
		if(BX.adminMenu.activeSection =='desktop')
			BX.userOptions.save('favorite', 'favorite_menu', 'stick', "Y");
		else
			BX.userOptions.save('favorite', 'favorite_menu', 'stick', "N");
	}

}

/**************************** admin grid ********************************/

BX.adminList = function(table_id, params)
{
	this.table_id = table_id;
	this.params = {
		context_ctrl: !!(params||{}).context_ctrl
	};

	this.TABLE = null;
	this.CHECKBOX = [];
	this.CHECKBOX_COUNTER = null;

	this.num_checked = 0;
	this.bSelectAllChecked = false;
	this._last_row = null;

	BX.ready(BX.defer(this.Init, this));
	BX.garbage(BX.proxy(this.Destroy, this));
}

BX.adminList.prototype.Init = function()
{
	this.TABLE = BX(this.table_id);

	this.LAYOUT = BX(this.table_id + '_result_div');
	this.FOOTER = BX(this.table_id + '_footer');
	this.FOOTER_EDIT = BX(this.table_id + '_footer_edit');
	this.FORM = document.forms['form_' + this.table_id];

	this.CHECKBOX_COUNTER = BX(this.table_id + '_selected_count');

	this.ACTION_SELECTOR = this.FORM.action;
	this.ACTION_BUTTON = this.FORM.apply;
	this.ACTION_TARGET = this.FORM.action_target

	this.BUTTON_EDIT = BX('action_edit_button');
	this.BUTTON_DELETE = BX('action_delete_button');

	BX.bind(this.ACTION_SELECTOR, 'change', BX.proxy(this.UpdateCheckboxCounter, this));
	BX.bind(this.ACTION_TARGET, 'click', BX.proxy(this.UpdateCheckboxCounter, this));

	if (!!this.TABLE && this.TABLE.tBodies[0] && this.TABLE.tBodies[0].rows.length > 0)
	{
		for (var i = 0; i < this.TABLE.tBodies[0].rows.length; i++)
		{
			if (this.TABLE.tBodies[0].rows[i].oncontextmenu)
			{
				BX.bind(this.TABLE.tBodies[0].rows[i], 'contextmenu', BX.proxy(function(e)
				{
					if(!this.params.context_ctrl && e.ctrlKey || this.params.context_ctrl && !e.ctrlKey)
						return;

					BX.adminList.ShowMenu({x: e.pageX || (e.clientX + document.body.scrollLeft), y: e.pageY || (e.clientY + document.body.scrollTop)}, BX.proxy_context.oncontextmenu(), BX.proxy_context);

					return BX.PreventDefault(e);

				}, this))
			}

			BX.bind(this.TABLE.tBodies[0].rows[i], 'click', BX.proxy(this.RowClick, this));
		}
	}

	var checkboxList = BX.findChildren(this.LAYOUT || this.TABLE, {tagName: 'INPUT', property: {type: 'checkbox'}}, true);
	if (!!checkboxList)
	{
		for (var i = 0; i < checkboxList.length; i++)
		{
			BX.adminFormTools.modifyCheckbox(checkboxList[i]);
			if(checkboxList[i].name == 'ID[]')
			{
				if (!checkboxList[i].disabled)
				{
					BX.bind(checkboxList[i], 'click', BX.proxy(this._checkboxClick, this));
					BX.bind(checkboxList[i].parentNode, 'click', BX.proxy(this._checkboxCellClick, this));
					BX.bind(checkboxList[i].parentNode, 'dblclick', BX.PreventDefault);

					this.CHECKBOX.push(checkboxList[i]);
				}
			}
		}
	}

	var check = BX(this.table_id + '_check_all');
	if (this.TABLE && this.TABLE.tHead)
	{
		if (check)
		{
			var check_id = check.id;
			BX.addCustomEvent(this.TABLE.tHead, 'onFixedNodeChangeState', BX.delegate(function(state)
			{
				if (state)
				{
					check.setAttribute('id', '');
					setTimeout("BX('"+check_id+"').checked="+this.table_id+".bSelectAllChecked", 5);
				}
				else
				{
					check.checked = this.bSelectAllChecked;
					check.setAttribute('id', check_id);
				}

			}, this));
		}

		BX.Fix(this.TABLE.tHead, {type: 'top', limit_node: this.TABLE});
	}

	if (this.FOOTER || this.FOOTER_EDIT)
	{
		BX.adminFormTools.modifyFormElements(this.FOOTER || this.FOOTER_EDIT, ['*']);

		BX.addCustomEvent(this.FOOTER || this.FOOTER_EDIT, 'onFixedNodeChangeState', function(state) {
			if (state)
				BX.addClass(this, 'adm-list-table-footer-fixed');
			else
				BX.removeClass(this, 'adm-list-table-footer-fixed');
		});
	}

	if (this.FOOTER_EDIT)
	{
		BX.Fix(this.FOOTER_EDIT, {type: 'bottom', limit_node: this.TABLE});
	}

	if (!!this.TABLE)
	{
		var pos = BX.pos(this.TABLE), wndScroll = BX.GetWindowScrollPos();

		if (pos.top < wndScroll.scrollTop)
			window.scrollTo(wndScroll.scrollLeft, pos.top);
	}

	this.UpdateCheckboxCounter();
}

BX.adminList.prototype.ReInit = function()
{
	BX.defer(this.Init, this)();
}

BX.adminList.prototype.GetAdminList = function(url, callback)
{
	BX.showWait(this.LAYOUT)

	url = BX.util.remove_url_param(url, ['mode', 'table_id']);
	url += (url.indexOf('?') >= 0 ? '&' : '?') + 'mode=list&table_id='+BX.util.urlencode(this.table_id);

	BX.ajax({
		method: 'GET',
		dataType: 'html',
		url: url,
		onsuccess: BX.delegate(function(result) {
			if (result.length > 0)
			{
				BX.closeWait(this.LAYOUT);
				this._GetAdminList(result);

				if (callback && BX.type.isFunction(callback))
					callback();
			}
		}, this),
		onfailure: function() {console.info(arguments)}
	});
}

BX.adminList.prototype._GetAdminList = function(result)
{
	this.Destroy(false);
	this.LAYOUT.innerHTML = result;

	this.ReInit();

	BX.adminChain.addItems(this.table_id + "_navchain_div");
}

BX.adminList.prototype.PostAdminList = function(url)
{
	url = BX.util.remove_url_param(url, ['mode', 'table_id']);
	url += (url.indexOf('?') >= 0 ? '&' : '?') + 'mode=frame&table_id='+BX.util.urlencode(this.table_id);

	// i can only guess of the sacred meaning of this strange thing. but it had an error in previous version.
	try{this.FORM.action.parentNode.removeChild(this.FORM.action);}catch(e){}

	this.FORM.action = url;
	BX.submit(this.FORM);
}

BX.adminList.prototype.UpdateCheckboxCounter = function()
{
	if (!this.CHECKBOX_COUNTER)
		return;

	var bChecked = this.num_checked > 0 || this.ACTION_TARGET && this.ACTION_TARGET.checked;

	if (!bChecked)
	{
		if (!!this.FOOTER)
			BX.UnFix(this.FOOTER);

		BX.removeClass(this.CHECKBOX_COUNTER, 'adm-table-counter-visible');
		this.CHECKBOX_COUNTER.lastChild.innerHTML = '0';

		if (!!this.ACTION_BUTTON)
			this.ACTION_BUTTON.disabled = true;
	}
	else
	{
		if (!!this.FOOTER)
			BX.Fix(this.FOOTER, {type: 'bottom', limit_node: this.TABLE.tBodies[0]});

		BX.addClass(this.CHECKBOX_COUNTER, 'adm-table-counter-visible');
		this.CHECKBOX_COUNTER.lastChild.innerHTML = this.ACTION_TARGET && this.ACTION_TARGET.checked ? BX.message('JSADM_LIST_SELECTEDALL') : this.num_checked;

		if (!!this.ACTION_BUTTON)
			this.ACTION_BUTTON.disabled = this.ACTION_SELECTOR.selectedIndex <= 0;
	}
}

BX.adminList.prototype.Sort = function(url, bCheckCtrl, args)
{
	if(bCheckCtrl == true)
	{
		var e = null, bControl = false;

		if(args.length > 0)
			e = args[0];
		if(!e)
			e = window.event;
		if(e)
			bControl = e.ctrlKey;
		url += (bControl? 'desc':'asc');
	}

	this.GetAdminList(url);
}


BX.adminList.prototype.RowClick = function(e)
{
	e = e || window.event;

	if (e.button != 0)
		return true;

	if (e.ctrlKey || e.metaKey || e.shiftKey && !this._last_row)
	{
		var c = BX.proxy_context.cells[0].firstChild;
		c.checked = !c.checked

		this.SelectRow(c, c.checked);

		this.UpdateCheckboxCounter();
		this.EnableActions();

		return BX.PreventDefault(e);
	}

	if (e.shiftKey)
	{
		if (!this._last_row)
			this._last_row = BX.proxy_context.parentNode.rows[0];

		var tBody = this._last_row.parentNode,
			ixStart = Math.min(this._last_row.rowIndex, BX.proxy_context.rowIndex),
			ixFinish = Math.max(this._last_row.rowIndex, BX.proxy_context.rowIndex);

		for (var i = ixStart; i <= ixFinish; i++)
		{
			var c = tBody.rows[i-1].cells[0].firstChild;
			if (!c.checked)
			{
				c.checked = true;
				this.SelectRow(c, c.checked);
			}
		}

		this.UpdateCheckboxCounter();
		this.EnableActions();

		return BX.PreventDefault(e);
	}
}

BX.adminList.prototype._checkboxClick = function(e)
{
	if (e.shiftKey || e.ctrlKey || e.metaKey)
		return true;

	this.SelectRow(BX.proxy_context, BX.proxy_context.checked);

	this.UpdateCheckboxCounter();
	this.EnableActions();

	return BX.eventCancelBubble(e);
}

BX.adminList.prototype._checkboxCellClick = function(e)
{
	if (e.shiftKey || e.ctrlKey || e.metaKey)
		return true;

	var c = BX.proxy_context.firstChild;
	c.checked = !c.checked;

	this.SelectRow(c, c.checked);

	this.UpdateCheckboxCounter();
	this.EnableActions();

	return BX.PreventDefault(e);
}

BX.adminList.prototype.SelectRow = function(el, bSelect)
{
	if (el.tagName.toUpperCase() != 'TR')
	{
		if (!el.BXROW)
		{
			el.BXROW = BX.findParent(el, {tag: 'TR'});
		}

		if (!!el.BXROW)
		{
			this.SelectRow(el.BXROW, bSelect);
		}
	}
	else
	{
		if (bSelect)
			BX.addClass(el, 'adm-table-row-active');
		else
			BX.removeClass(el, 'adm-table-row-active');

		this._last_row = el;
		this.num_checked += bSelect ? 1 : -1;
	}
}

BX.adminList.prototype.SelectAllRows = function(node)
{
	this.bSelectAllChecked = !!node.checked;

	for (var i = 0; i < this.CHECKBOX.length; i++)
	{
		if(this.CHECKBOX[i].checked != this.bSelectAllChecked && !this.CHECKBOX[i].disabled)
		{
			this.CHECKBOX[i].checked = this.bSelectAllChecked;
			this.SelectRow(this.CHECKBOX[i], this.bSelectAllChecked);
		}
	}

	this.UpdateCheckboxCounter();
	this.EnableActions();
}

BX.adminList.prototype.IsActionEnabled = function(action)
{
	if(action == 'edit')
		return !(this.ACTION_TARGET && this.ACTION_TARGET.checked) && (this.num_checked > 0);
	else
		return (this.ACTION_TARGET && this.ACTION_TARGET.checked) || (this.num_checked > 0);
}

BX.adminList.prototype.EnableActions = function()
{
	if (!!this.BUTTON_EDIT)
	{
		if (this.IsActionEnabled('edit'))
			BX.removeClass(this.BUTTON_EDIT, 'adm-edit-disable');
		else
			BX.addClass(this.BUTTON_EDIT, 'adm-edit-disable');
	}

	if (!!this.BUTTON_DELETE)
	{
		if (this.IsActionEnabled('delete'))
			BX.removeClass(this.BUTTON_DELETE, 'adm-edit-disable');
		else
			BX.addClass(this.BUTTON_DELETE, 'adm-edit-disable');
	}
}

BX.adminList.prototype.Destroy = function()
{
	this.CHECKBOX = [];
	if (BX.PopupMenu.currentItem && BX.PopupMenu.currentItem.popupWindow.isShown())
		BX.PopupMenu.currentItem.popupWindow.close();

	if (this.TABLE && this.TABLE.tHead)
		BX.UnFix(this.TABLE.tHead);
	if (this.FOOTER)
	BX.UnFix(this.FOOTER);
	if (this.FOOTER_EDIT)
		BX.UnFix(this.FOOTER_EDIT);

	this._last_row = null;
	this.num_checked = 0;
}

BX.adminList.prototype.ShowSettings = function(url)
{
	(new BX.CDialog({
			content_url: url,
			resizable: true,
			height: 475,
			width: 560
		})).Show();
}

BX.adminList.prototype.SaveSettings =  function()
{
	BX.showWait();

	var sCols='', sBy='', sOrder='', sPageSize='';

	var oSelect = document.list_settings.selected_columns;
	var n = oSelect.length;
	for(var i=0; i<n; i++)
		sCols += (sCols != ''? ',':'')+oSelect[i].value;

	oSelect = document.list_settings.order_field;
	if(oSelect)
		sBy = oSelect[oSelect.selectedIndex].value;

	oSelect = document.list_settings.order_direction;
	if(oSelect)
		sOrder = oSelect[oSelect.selectedIndex].value;

	oSelect = document.list_settings.nav_page_size;
	sPageSize = oSelect[oSelect.selectedIndex].value;

	var bCommon = (document.list_settings.set_default && document.list_settings.set_default.checked);

	BX.userOptions.save('list', this.table_id, 'columns', sCols, bCommon);
	BX.userOptions.save('list', this.table_id, 'by', sBy, bCommon);
	BX.userOptions.save('list', this.table_id, 'order', sOrder, bCommon);
	BX.userOptions.save('list', this.table_id, 'page_size', sPageSize, bCommon);

	var url = window.location.href;
	BX.userOptions.send(BX.delegate(function(){
		BX.closeWait();
		this.GetAdminList(
			url,
			function(){BX.WindowManager.Get().Close();}
		);
	}, this));
}

BX.adminList.prototype.DeleteSettings = function(bCommon)
{
	BX.showWait();
	var url = window.location.href;
	BX.userOptions.del('list', this.table_id, bCommon, BX.delegate(function(){
		BX.closeWait();
		this.GetAdminList(
			url,
			function(){BX.WindowManager.Get().Close();}
		);
	}, this));
}

BX.adminList._onpopupmenushow = function(){BX.addClass(this, 'adm-list-row-active');}
BX.adminList._onpopupmenuclose = function(){BX.removeClass(this, 'adm-list-row-active');}

BX.adminList.ShowMenu = function(el, menu, el_row)
{
	if (!!menu && menu.length > 0)
	{
		if (!!el_row)
		{
			BX.addCustomEvent(el, 'onAdminMenuShow', BX.proxy(BX.adminList._onpopupmenushow, el_row));
			BX.addCustomEvent(el, 'onAdminMenuClose', BX.proxy(BX.adminList._onpopupmenuclose, el_row));
		}

		BX.adminShowMenu(el, menu);
	}
}

BX.adminTabControl = function (name, unique_name, aTabs)
{
	this.name = name;
	this.unique_name = unique_name;
	this.aTabs = aTabs;

	this.bInited = false;
	this.bFixed = {top: true, bottom: true};

	this.bExpandTabs = false;
	this.aTabsDisabled = {};

	this.bPublicMode = false;

	BX.ready(BX.defer(this.Init, this));
}

BX.adminTabControl.prototype.Init = function()
{
	if (this.aTabs && this.aTabs.length > 0)
	{
		var tabs_block = this.TABS_BLOCK = BX(this.name + '_tabs');
		if (!!tabs_block)
		{
			var settings_btn = BX(this.name + '_settings_btn');

			tabs_block.appendChild(BX.create('DIV', {
				props: {
					className: 'adm-detail-pin-btn-tabs',
					title: BX.message('JSADM_PIN_OFF')
				},
				attrs: {onclick: this.name + '.ToggleFix(\'top\')'}
			}));

			BX.addCustomEvent(tabs_block, 'onFixedNodeChangeState', function(state)
			{
				if (state)
				{
					BX.addClass(tabs_block, 'adm-detail-tabs-block-fixed');
				}
				else
				{
					BX.removeClass(tabs_block, 'adm-detail-tabs-block-fixed');
				}

				if (!!settings_btn && BX.hasClass(settings_btn, 'bx-settings-btn-active'))
				{
					BX.onCustomEvent(settings_btn, 'onChangeNodePosition');
				}
			});

			if (this.bFixed['top'])
			{
				BX.Fix(tabs_block, {type: 'top', limit_node: tabs_block.parentNode});
			}
			else
			{
				BX.addClass(tabs_block, 'adm-detail-tabs-block-pin');
				tabs_block.lastChild.title = BX.message('JSADM_PIN_ON');
			}
		}

		for (var tab = 0; tab < this.aTabs.length; tab++)
		{
			this.aTabs[tab].CONTENT = BX(this.aTabs[tab]["DIV"]);

			var tbl = BX(this.aTabs[tab]["DIV"]+'_edit_table');
			if (!!tbl)
			{
				var n = tbl.tBodies[0].rows.length;
				for(var i=0; i<n; i++)
				{
					if(tbl.tBodies[0].rows[i].cells.length > 1)
					{
						BX.addClass(tbl.rows[i].cells[0], 'adm-detail-content-cell-l');
						BX.addClass(tbl.rows[i].cells[1], 'adm-detail-content-cell-r');
					}
				}

				this.aTabs[tab].EDIT_TABLE = tbl;
				this.aTabs[tab].CONTENT_BLOCK = tbl.parentNode;
				var modifyFormElements = BX.adminFormTools.modifyFormElements(tbl);
			}
		}
	}

	var footer = BX(this.name + '_buttons_div');
	if (!!footer)
	{
		if (footer.firstChild)
		{
			if (BX.util.trim(footer.firstChild.innerHTML).length <= 0)
			{
				if (!BX.hasClass(footer.firstChild, 'adm-detail-content-btns-empty'))
					BX.addClass(footer.firstChild, 'adm-detail-content-btns-empty');
			}
			else
			{
				footer.firstChild.insertBefore(BX.create('DIV', {
					props: {
						className: 'adm-detail-pin-btn',
						title: BX.message('JSADM_PIN_OFF')
					},
					attrs: {onclick: this.name + '.ToggleFix(\'bottom\')'}
				}), footer.firstChild.firstChild);

				BX.addCustomEvent(footer, 'onFixedNodeChangeState', function(state)
					{
						if (state)
							BX.addClass(footer, 'adm-detail-content-btns-fixed');
						else
							BX.removeClass(footer, 'adm-detail-content-btns-fixed');
					});

				if (this.bFixed['bottom'])
				{
					BX.Fix(footer, {type: 'bottom', limit_node: footer.parentNode});
				}
				else
				{
					BX.addClass(footer, 'adm-detail-content-btns-pin');
					footer.firstChild.firstChild.title = BX.message('JSADM_PIN_ON')
				}
			}
		}
	}

	this.bInited = true;
}

BX.adminTabControl.prototype.setPublicMode = function(v)
{
	this.bPublicMode = !!v;
}

BX.adminTabControl.prototype.ToggleFix = function(type)
{
	if (!this.bInited)
	{
		this.bFixed[type] = !this.bFixed[type];
		return;
	}

	switch (type)
	{
		case 'bottom':
			var footer = BX(this.name + '_buttons_div');
			if (!!footer)
			{
				if (this.bFixed[type])
				{
					BX.addClass(footer, 'adm-detail-content-btns-pin');
					footer.firstChild.firstChild.title = BX.message('JSADM_PIN_ON');
					BX.UnFix(footer);
				}
				else
				{
					BX.removeClass(footer, 'adm-detail-content-btns-pin');
					footer.firstChild.firstChild.title = BX.message('JSADM_PIN_OFF');
					BX.Fix(footer, {type: 'bottom', limit_node: footer.parentNode});
				}

			}
		break;
		case 'top':
			if (!!this.TABS_BLOCK)
			{
				if (this.bFixed[type])
				{
					BX.addClass(this.TABS_BLOCK, 'adm-detail-tabs-block-pin');
					this.TABS_BLOCK.lastChild.title = BX.message('JSADM_PIN_ON');
					BX.UnFix(this.TABS_BLOCK);
				}
				else
				{
					BX.removeClass(this.TABS_BLOCK, 'adm-detail-tabs-block-pin');
					this.TABS_BLOCK.lastChild.title = BX.message('JSADM_PIN_OFF');
					BX.Fix(this.TABS_BLOCK, {type: 'top', limit_node: this.TABS_BLOCK.parentNode});
				}
			}
		break;
	}

	this.bFixed[type] = !this.bFixed[type];
	BX.userOptions.save('edit', 'admin_tabs', 'fix_'+type, (this.bFixed[type] ? 'on': 'off'));
}

BX.adminTabControl.prototype.SelectTab = function(tab_id)
{
	if (!this.bInited)
	{
		setTimeout("window."+this.name+".SelectTab('"+BX.util.jsencode(tab_id)+"')", 50);
	}
	else if (!this.aTabsDisabled[tab_id])
	{
		var div = BX(tab_id);
		if (div.style.display != 'none')
			return;

		var oldHeight = 0;
		var newHeight = 0;
		var contentBlockPaddings = 52;
		for (var i = 0, cnt = this.aTabs.length; i < cnt; i++)
		{
			var tab = BX(this.aTabs[i]["DIV"]);
			if(tab.style.display != 'none')
			{
				oldHeight = this.aTabs[i].CONTENT_BLOCK.offsetHeight - contentBlockPaddings;
				this.ShowTab(this.aTabs[i]["DIV"], false);
				tab.style.display = 'none';
				break;
			}
		}

		this.ShowTab(tab_id, true);
		div.style.display = 'block';

		BX(this.name+'_active_tab').value = tab_id;

		var currentTab = null;
		for (var i = 0, cnt = this.aTabs.length; i < cnt; i++)
		{
			if(this.aTabs[i]["DIV"] == tab_id)
			{
				this.aTabs[i]["_ACTIVE"] = true;

				if(this.aTabs[i]["ONSELECT"])
				{
					BX.evalGlobal(this.aTabs[i]["ONSELECT"]);
				}

				if (!this.bPublicMode)
				{
					currentTab = this.aTabs[i];
					var currentContentBlock = this.aTabs[i].CONTENT_BLOCK;
					newHeight = currentContentBlock.offsetHeight - contentBlockPaddings;
					if (oldHeight > 0)
					{
						currentContentBlock.style.height = oldHeight + "px";
						currentContentBlock.style.overflowY = "hidden";
						this.aTabs[i].EDIT_TABLE.style.opacity = 0;
					}
				}

				break;
			}
		}

		if (!!this.TABS_BLOCK)
		{
			if (BX.hasClass(this.TABS_BLOCK, 'adm-detail-tabs-block-fixed'))
			{
				var pos = BX.pos(div), wndScroll = BX.GetWindowScrollPos();
				window.scrollTo(wndScroll.scrollLeft, pos.top - this.TABS_BLOCK.offsetHeight - parseInt(this.TABS_BLOCK.style.top));
			}
		}

		if (!this.bPublicMode && oldHeight > 0 && newHeight > 0 && currentTab)
		{
			var easing = new BX.easing({
				duration : 500,
				start : { height: oldHeight, opacity : 0 },
				finish : { height: newHeight, opacity : 100 },
				transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),

				step : BX.proxy(function(state){
					this.CONTENT_BLOCK.style.height = state.height + 'px';
					this.EDIT_TABLE.style.opacity = state.opacity / 100;
					BX.onCustomEvent('onAdminTabsChange');
				}, currentTab),

				complete : BX.proxy(function(){
					this.CONTENT_BLOCK.style.height = "auto";
					this.CONTENT_BLOCK.style.overflowY = "visible";
					BX.onCustomEvent('onAdminTabsChange');

				}, currentTab)

			});
			easing.animate();
		}
		else
			BX.onCustomEvent('onAdminTabsChange');
	}
}

BX.adminTabControl.prototype.ShowTab = function(tab_id, bShow)
{
	if (bShow)
		BX.addClass(BX('tab_cont_' + tab_id), 'adm-detail-tab-active');
	else
		BX.removeClass(BX('tab_cont_' + tab_id), 'adm-detail-tab-active');
}

BX.adminTabControl.prototype.ShowDisabledTab = function(tab_id, disabled)
{
	var tab = BX('tab_cont_'+tab_id);
	if(disabled)
	{
		BX.addClass(tab, 'adm-detail-tab-disable');
	}
	else
	{
		BX.removeClass(tab, 'adm-detail-tab-disable');
	}
}

// TODO: rewrite
BX.adminTabControl.prototype.NextTab = function()
{
	var CurrentTab=BX(this.name+'_active_tab').value;
	var NextTab="";

	for(var i=0; i<this.aTabs.length; i++)
		{
			if(CurrentTab==this.aTabs[i]["DIV"])
			{
				if(i>=(this.aTabs.length-1))
					NextTab=this.aTabs[0];
				else
					NextTab=this.aTabs[i+1];
			}
		}

	if(NextTab["DIV"])
		this.SelectTab(NextTab["DIV"]);
}

BX.adminTabControl.prototype.ToggleTabs = function()
{
	this.bExpandTabs = !this.bExpandTabs;

	var a = BX(this.name+'_expand_link');
	a.title = (this.bExpandTabs? BX.message('JSADM_TABS_COLLAPSE') : BX.message('JSADM_TABS_EXPAND'));
	if (this.bExpandTabs)
	{
		BX.addClass(a, 'adm-detail-title-setting-active');
		BX.UnFix(this.TABS_BLOCK);
	}
	else
	{
		BX.removeClass(a, 'adm-detail-title-setting-active');
		BX.Fix(this.TABS_BLOCK, {type: 'top', limit_node: this.TABS_BLOCK.parentNode});
	}

	for(var i=0; i < this.aTabs.length; i++)
	{
		var tab_id = this.aTabs[i]["DIV"];
		this.ShowTab(tab_id, false);

		this.ShowDisabledTab(tab_id, (this.bExpandTabs || this.aTabsDisabled[tab_id]));

		var div = BX(tab_id);
		div.style.display = (this.bExpandTabs && !this.aTabsDisabled[tab_id]? 'block':'none');
	}

	if(!this.bExpandTabs)
	{
		this.ShowTab(this.aTabs[0]["DIV"], true);
		var div = document.getElementById(this.aTabs[0]["DIV"]);
		div.style.display = 'block';
	}

	BX.userOptions.save('edit', this.unique_name, 'expand', (this.bExpandTabs? 'on': 'off'));

	BX.onCustomEvent('OnToggleTabs');
	BX.onCustomEvent('onAdminTabsChange');
}


BX.adminTabControl.prototype.DisableTab = function(tab_id)
{
	this.aTabsDisabled[tab_id] = true;
	this.ShowDisabledTab(tab_id, true);
	if(this.bExpandTabs)
	{
		var div = BX(tab_id);
		div.style.display = 'none';
	}
}

BX.adminTabControl.prototype.EnableTab = function(tab_id)
{
	this.aTabsDisabled[tab_id] = false;
	this.ShowDisabledTab(tab_id, this.bExpandTabs);
	if(this.bExpandTabs)
	{
		var div = BX(tab_id);
		div.style.display = 'block';
	}
}

BX.adminTabControl.prototype.ShowWarnings = function(form_name, warnings)
{
	var form = document.forms[form_name];
	if(!form)
		return;

	for(var i in warnings)
	{
		var e = form.elements[warnings[i]['name']];

		if(!e)
			continue;

		var type = (e.type? e.type.toLowerCase():'');
		var bBefore = false;
		if(e.length > 1 && type != 'select-one' && type != 'select-multiple')
		{
			e = e[0];
			bBefore = true;
		}
		if(type == 'textarea' || type == 'select-multiple')
			bBefore = true;

		var td = e.parentNode;
		var img;
		if(bBefore)
		{
			img = td.insertBefore(new Image(), e);
			td.insertBefore(document.createElement("BR"), e);
		}
		else
		{
			img = td.insertBefore(new Image(), e.nextSibling);
			img.hspace = 2;
			img.vspace = 2;
			img.style.verticalAlign = 'bottom';
		}
		img.src = '/bitrix/panel/main/images_old/icon_warn.gif';
		img.title = warnings[i]['title'];
	}
}

BX.adminTabControl.prototype.ShowSettings = function(url)
{
	(new BX.CDialog({
			content_url: url,
			resizable: true,
			height: 605,
			width: 560
	})).Show();
}

BX.adminTabControl.prototype.CloseSettings =  function()
{
	BX.WindowManager.Get().Close();
}

BX.adminTabControl.prototype.SaveSettings =  function()
{
	var sTabs='', s='';

	var oFieldsSelect;
	var oSelect = BX('selected_tabs');
	if(oSelect)
	{
		var k = oSelect.length;
		for(var i=0; i<k; i++)
		{
			s = oSelect[i].value + '--#--' + oSelect[i].text;
			oFieldsSelect = BX('selected_fields[' + oSelect[i].value + ']');
			if(oFieldsSelect)
			{
				var n = oFieldsSelect.length;
				for(var j=0; j<n; j++)
				{
					s += '--,--' + oFieldsSelect[j].value + '--#--' + jsUtils.trim(oFieldsSelect[j].text);
				}
			}
			sTabs += s + '--;--';
		}
	}

	var bCommon = (document.form_settings.set_default && document.form_settings.set_default.checked);

	var sParam = '';
	sParam += '&p[0][c]=form';
	sParam += '&p[0][n]='+BX.util.urlencode(this.name);
	if(bCommon)
		sParam += '&p[0][d]=Y';
	sParam += '&p[0][v][tabs]=' + BX.util.urlencode(sTabs);

	var options_url = '/bitrix/admin/user_options.php?lang='+BX.message('LANGUAGE_ID')+'&sessid=' + BX.bitrix_sessid();
	options_url += '&action=delete&c=form&n='+this.name+'_disabled';

	BX.showWait();
	BX.ajax.post(options_url, sParam, function() {
		BX.WindowManager.Get().Close();
		BX.reload();
	});
}

BX.adminTabControl.prototype.DeleteSettings = function(bCommon)
{
	BX.showWait();
	BX.userOptions.del('form', this.name, bCommon, function () {BX.reload()});
}

BX.adminTabControl.prototype.DisableSettings = function()
{
	var request = new JCHttpRequest;
	request.Action = function () {BX.reload()};
	var sParam = '';
	sParam += '&p[0][c]=form';
	sParam += '&p[0][n]='+encodeURIComponent(this.name+'_disabled');
	sParam += '&p[0][v][disabled]=Y';
	request.Send('/bitrix/admin/user_options.php?lang=' + phpVars.LANGUAGE_ID + sParam + '&sessid='+phpVars.bitrix_sessid);
}

BX.adminTabControl.prototype.EnableSettings = function()
{
	var request = new JCHttpRequest;
	request.Action = function () {BX.reload()};
	var sParam = '';
	sParam += '&c=form';
	sParam += '&n='+encodeURIComponent(this.name)+'_disabled';
	sParam += '&action=delete';
	request.Send('/bitrix/admin/user_options.php?lang=' + phpVars.LANGUAGE_ID + sParam + '&sessid='+phpVars.bitrix_sessid);
}

BX.adminViewTabControl = function(aTabs)
{
	this.aTabs = aTabs;
	this.bPublicMode = false;
	BX.ready(BX.delegate(this.Init, this));
}

BX.adminViewTabControl.prototype.setPublicMode = function(v)
{
	this.bPublicMode = !!v;
}

BX.adminViewTabControl.prototype.SelectTab = function(tab_id)
{
	var div = BX(tab_id);
	if(div.style.display != 'none')
		return;

	var oldHeight = 0;
	var contentBlockPaddings = 41;
	for(var i in this.aTabs)
	{
		var tab_div = BX(this.aTabs[i]["DIV"]);
		if(tab_div.style.display != 'none')
		{
			var tab = BX('view_tab_'+this.aTabs[i]["DIV"]);
			BX.removeClass(tab, 'adm-detail-subtab-active');

			var oldContentBlock = BX.findChild(tab_div, { className : "adm-detail-content-item-block-view-tab"});
			if (oldContentBlock)
				oldHeight = oldContentBlock.offsetHeight - contentBlockPaddings;

			tab_div.style.display = 'none';
			break;
		}
	}

	var active_tab = BX('view_tab_'+tab_id);
	BX.addClass(active_tab, 'adm-detail-subtab-active');
	div.style.display = 'block';

	var newHeight = 0;
	var newContentBlock = BX.findChild(div, { className : "adm-detail-content-item-block-view-tab" });
	var newContentTable = null;
	if (newContentBlock)
	{
		newHeight = newContentBlock.offsetHeight - contentBlockPaddings;
		if (oldHeight > 0)
		{
			newContentBlock.style.height = oldHeight + "px";
			newContentBlock.style.overflowY = "hidden";
			newContentTable = BX.findChild(newContentBlock, { tagName : "table" });
			if (newContentTable)
				newContentTable.style.opacity = 0;
		}
	}

	for(var i in this.aTabs)
	{
		if(this.aTabs[i]["DIV"] == tab_id)
		{
			if(this.aTabs[i]["ONSELECT"])
			{
				BX.evalGlobal(this.aTabs[i]["ONSELECT"]);
			}
			break;
		}
	}

	if (oldHeight > 0 && newHeight > 0 && newContentBlock)
	{
		var easing = new BX.easing({
			duration : 500,
			start : { height: oldHeight, opacity : 0 },
			finish : { height: newHeight, opacity : 100 },
			transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),

			step : BX.proxy(function(state){
				this.style.height = state.height + 'px';
				if (newContentTable)
					newContentTable.style.opacity = state.opacity / 100;
				BX.onCustomEvent('onAdminTabsChange');
			}, newContentBlock),

			complete : BX.proxy(function(){
				this.style.height = "auto";
				this.style.overflowY = "visible";
				BX.onCustomEvent('onAdminTabsChange');

			}, newContentBlock)

		});
		easing.animate();
	}
	else
		BX.onCustomEvent('onAdminTabsChange');
}

BX.adminViewTabControl.prototype.DisableTab = function(tab_id)
{
}

BX.adminViewTabControl.prototype.EnableTab = function(tab_id)
{
}

BX.adminViewTabControl.prototype.ReplaceAnchor = function(tab)
{
}

BX.adminViewTabControl.prototype.RebuildTabs = function()
{

}

BX.adminViewTabControl.prototype.Init = function()
{
	if(this.aTabs.length == 0)
		return;
}

function wwww(){
	var _this = this;
	this.name = name;
	this.unique_name = unique_name;
	this.aTabs = aTabs;
	this.aTabsDisabled = {};
	this.bExpandTabs = false;

	this.AUTOSAVE = null;

	var auto_lnk = BX(this.name + '_autosave_link');
	if (auto_lnk)
	{
		auto_lnk.title = BX.message('AUTOSAVE_T');
		BX.addCustomEvent('onAutoSavePrepare', function (ob, h) {
			BX.bind(auto_lnk, 'click', BX.proxy(ob.Save, ob));
		});
		BX.addCustomEvent('onAutoSave', function() {
			auto_lnk.className = 'context-button bx-core-autosave bx-core-autosave-saving';
		});
		BX.addCustomEvent('onAutoSaveFinished', function(ob, t) {
			t = parseInt(t);
			if (!isNaN(t))
			{
				setTimeout(function() {
					auto_lnk.className = 'context-button bx-core-autosave bx-core-autosave-ready';
				}, 1000);
				auto_lnk.title = BX.message('AUTOSAVE_L').replace('#DATE#', BX.formatDate(new Date(t * 1000)));
			}
		});
		BX.addCustomEvent('onAutoSaveInit', function() {
			auto_lnk.className = 'context-button bx-core-autosave bx-core-autosave-edited';
		});
	}



	this.InitEditTables = function()
	{
		for(var tab = 0, cnt = this.aTabs.length; tab < cnt; tab++)
		{
			var div = document.getElementById(this.aTabs[tab]["DIV"]);
			var tbl = jsUtils.FindChildObject(div.firstChild, 'table', 'edit-table');
			if(!tbl)
			{
				var tbl = jsUtils.FindChildObject(div, 'table', 'edit-table');
				if (!tbl)
					continue;
			}

			var n = tbl.rows.length;
			for(var i=0; i<n; i++)
				if(tbl.rows[i].cells.length > 1)
					tbl.rows[i].cells[0].className = 'field-name';
		}
	}


	this.Destroy = function()
	{
		//for(var i in this.aTabs)
		for(var i = 0, cnt = this.aTabs.length; i < cnt; i++)
		{
			var tab = document.getElementById('tab_cont_'+this.aTabs[i]["DIV"]);
			if (!tab)
				continue;
			tab.onclick = null;
			tab.onmouseover = null;
			tab.onmouseout = null;
		}
		_this = null;
	}

}


/***************************** simple history listener **********************/

BX.adminHistory = function()
{
	BX.bind(window, 'popstate', BX.proxy(this._get, this));

	this.bStart = true;

	this.pushSupported = true;
	this.state = {};
}

BX.adminHistory.pushSupported = false;

/* callback is useless here but should be here for compatibility reasons */
BX.adminHistory.put = function(url, callback, arIgnoreParams)
{
	var link = BX('navchain-link');
	if(link)
	{
		if (url.indexOf('&amp;') > 0)
			url = BX.util.htmlspecialcharsback(url);
		if (BX.type.isArray(arIgnoreParams))
			url = BX.util.remove_url_param(url, arIgnoreParams);

		link.href = url;
		if (url != window.location.href)
			BX.addClass(link, 'navchain-link-visible');
		else
			BX.removeClass(link, 'navchain-link-visible');
	}
}

BX.adminHistory.prototype.put = function(url, callback, arIgnoreParams)
{
	if (url.indexOf('&amp;') > 0)
		url = BX.util.htmlspecialcharsback(url);
	if (BX.type.isArray(arIgnoreParams))
		url = BX.util.remove_url_param(url, arIgnoreParams);

	url = BX.util.remove_url_param(url, 'admin_history');

	var state = {url: url, callback: callback};

	var k = Math.random()
	this.state[k] = state;

	if (this.bStart)
	{
		history.pushState(k, '', window.location.href);
		this.bStart = false;
	}
	else
	{
		history.pushState(k, '', url);
	}
}

BX.adminHistory.prototype._get = function(e)
{
	e = e || window.event || {state: null};
	if (e.state && e.state && this.state[e.state])
	{
		if (this.state[e.state].callback)
		{
			this.state[e.state].callback(this.state[e.state].url + (this.state[e.state].url.indexOf('?')>0?'&':'?') + 'admin_history=Y')
		}
		else
		{
			window.location.href = this.state[e.state].url;
		}
	}
}

/*************************** fixed elements *********************************/

BX.Fix = function(el, params)
{
	if (!el.BXFIXER)
	{
		if (el.tagName.toUpperCase() == 'THEAD')
			el.BXFIXER = new BX.CFixerTHead(el, params);
		else
			el.BXFIXER = new BX.CFixer(el, params);
	}

	el.BXFIXER.Start()
}

BX.UnFix = function(el)
{
	if (!!el && !!el.BXFIXER)
		el.BXFIXER.Stop()
}

BX.CFixer = function(node, params)
{
	this.node = node;
	this.params = params || {type: 'top'};

	this.pos = {};
	this.limit = -1;

	this.position_top = null;
	this.position_bottom = null;
	this.position_right = null;

	this.bStarted = false;
	this.bFixed = false;

	this.gutter = null;
}

BX.CFixer.prototype.Start = function()
{
	if (this.bStarted)
		return;

	this.pos = BX.pos(this.node);

	BX.bind(window, 'scroll', BX.proxy(this._scroll_listener, this));
	BX.bind(window, 'resize', BX.proxy(this._scroll_listener, this));
	BX.bind(window, 'resize', BX.proxy(this._ReFix, this));

	BX.addCustomEvent('onAdminFilterToggleRow', BX.proxy(this._scroll_listener, this));
	BX.addCustomEvent('onAdminPanelFix', BX.defer(this._scroll_listener, this));
	BX.addCustomEvent('onAdminPanelChange', BX.defer(this._scroll_listener, this));
	BX.addCustomEvent('onAdminTabsChange', BX.defer(this._recalc_pos, this));
	BX.addCustomEvent(BX.adminMenu, 'onAdminMenuResize', BX.proxy(this._scroll_listener, this));
	BX.addCustomEvent(BX.adminMenu, 'onAdminMenuResize', BX.proxy(this._ReFix, this));

	this._scroll_listener();

	this.bStarted = true;
}

BX.CFixer.prototype.Stop = function()
{
	if (!this.bStarted)
		return;

	this._UnFix();

	BX.unbind(window, 'scroll', BX.proxy(this._scroll_listener, this));
	BX.unbind(window, 'resize', BX.proxy(this._scroll_listener, this));
	BX.unbind(window, 'resize', BX.proxy(this._ReFix, this));

	BX.removeCustomEvent('onAdminFilterToggleRow', BX.proxy(this._scroll_listener, this));
	BX.removeCustomEvent('onAdminPanelFix', BX.defer(this._scroll_listener, this));
	BX.removeCustomEvent('onAdminTabsChange', BX.defer(this._recalc_pos, this));
	BX.removeCustomEvent('onAdminPanelChange', BX.defer(this._scroll_listener, this));
	BX.removeCustomEvent(BX.adminMenu, 'onAdminMenuResize', BX.proxy(this._scroll_listener, this));
	BX.removeCustomEvent(BX.adminMenu, 'onAdminMenuResize', BX.proxy(this._ReFix, this));

	this.bStarted = false;
}

BX.CFixer.prototype._recalc_pos = function()
{
	this.pos = BX.pos(this.gutter || this.node);
	this._scroll_listener();
}

BX.CFixer.prototype._Fix = function()
{
	if (!this.bFixed)
	{
		this.pos = BX.pos(this.gutter || this.node);

		if (!this.gutter)
			this.gutter = this.node.parentNode.insertBefore(BX.create(
				this.node.tagName, {
					style: {height: this.pos.height + 'px', width: this.pos.width + 'px'},
					props: {className: this.node.className}
				}), this.node);

		this._w = this.node.style.width;
		this.node.style.width = this.pos.width + 'px';

		BX.addClass(this.node, 'bx-fixed-' + this.params.type);

		if (this['position_' + this.params.type] !== null)
			this.node.style[this.params.type] = this['position_' + this.params.type] + 'px';

		this.bFixed = true;
	}
}

BX.CFixer.prototype._UnFix = function(bRefix)
{
	if (this.bFixed)
	{
		this.node.style.width = this._w
		BX.removeClass(this.node, 'bx-fixed-' + this.params.type);

		this.node.style[this.params.type] = null;

		this.bFixed = false;

		if (!bRefix)
		{
			if (this.gutter && this.gutter.parentNode)
				this.gutter.parentNode.removeChild(this.gutter);

			this.gutter = null;

			this._check_scroll(this.pos.left, this.pos.top);
		}
	}
}

BX.CFixer.prototype._ReFix = function()
{
	if (this.bFixed)
	{
		this._UnFix(true); this._Fix();
	}
}

BX.CFixer.prototype._scroll_listener = function()
{
	var wndScroll = BX.GetWindowScrollPos(), bFixed = this.bFixed;

	if (!BX.isNodeInDom(this.node))
		return this.Stop();

	var pos = bFixed ? this.pos : BX.pos(this.node);

	if (this.params.limit_node)
	{
		var pos1 = BX.pos(this.params.limit_node);

		switch(this.params.type)
		{
			case 'top':
				this.limit = pos1.bottom - this.pos.height;
			break;
			case 'bottom':
				this.limit = pos1.top + this.pos.height;
			break;
			case 'right':
				this.limit = pos1.right + this.pos.width;
			break;
		}
	}

	if (!BX.isNodeHidden(this.node))
	{
		switch(this.params.type)
		{
			case 'top':
				this.position_top = BX.adminPanel.isFixed() ? BX.adminPanel.panel.offsetHeight : 0;

				if (this.limit > 0 && wndScroll.scrollTop + this.position_top > this.limit)
					this._UnFix();
				else if (!this.bFixed && wndScroll.scrollTop + this.position_top >= pos.top)
					this._Fix();
				else if (this.bFixed && wndScroll.scrollTop + this.position_top < pos.top)
					this._UnFix();

			break;
			case 'bottom':
				var wndSize = BX.GetWindowInnerSize();

				wndScroll.scrollBottom = wndScroll.scrollTop + wndSize.innerHeight;

				if (this.limit > 0 && wndScroll.scrollBottom < this.limit)
					this._UnFix();
				else if (!this.bFixed && wndScroll.scrollBottom < pos.bottom)
					this._Fix();
				else if (this.bFixed && wndScroll.scrollBottom >= pos.bottom)
					this._UnFix();
			break;
			case 'right':
				var wndSize = BX.GetWindowInnerSize();

				// 15 is a browser scrollbar fix
				wndScroll.scrollRight = wndScroll.scrollLeft + wndSize.innerWidth - 15;

				if (this.limit > 0 && wndScroll.scrollRight < this.limit)
					this._UnFix();
				else if (!this.bFixed && wndScroll.scrollRight < pos.right)
					this._Fix();
				else if (this.bFixed && wndScroll.scrollRight >= pos.right)
					this._UnFix();

			break;
		}
	}
	else if (this.bFixed)
	{
		this._UnFix();
	}

	if (this.bFixed)
	{
		this._check_scroll(wndScroll.scrollLeft, wndScroll.scrollTop);
	}
	else
	{
		this._check_scroll(this.pos.left, this.pos.top);
	}

	if (bFixed != this.bFixed)
	{
		BX.onCustomEvent(this.node, 'onFixedNodeChangeState', [this.bFixed]);
	}
}

BX.CFixer.prototype._check_scroll = function(scrollLeft, scrollTop)
{
	if (this.params.type == 'top' || this.params.type == 'bottom')
		this.node.style.left = (this.pos.left - scrollLeft) + 'px';
	else
		this.node.style.top = (this.pos.top - scrollTop) + 'px'

	if (this.bFixed && this['position_' + this.params.type] !== null)
	{
		this.node.style[this.params.type] = this['position_' + this.params.type] + 'px';
	}
}

BX.CFixerTHead = function()
{
	BX.CFixerTHead.superclass.constructor.apply(this, arguments);

	this.mirror = null;
	this.mirror_thead = null;
}
BX.extend(BX.CFixerTHead, BX.CFixer);

BX.CFixerTHead.prototype._Fix = function()
{
	if (!this.bFixed)
	{
		if (!this.mirror)
		{
			this.pos = BX.pos(this.node);

			var wndScroll = BX.GetWindowScrollPos()

			this.mirror_thead = BX.clone(this.node);

			this.mirror = document.body.appendChild(
				BX.create('DIV', {
					style: {
						left: (this.pos.left-wndScroll.scrollLeft) + 'px'
					},
					props: {className: 'bx-fixed-' + this.params.type + ' adm-list-table-fixed'},
					children:[
						BX.create('TABLE', {
							props: {className: this.node.parentNode.className},
							style: {width: this.node.parentNode.offsetWidth + 'px'},
							children: [this.mirror_thead]
						})
					]
				})
			);

			for (var i = 0; i < this.node.rows[0].cells.length; i++)
			{
				this.mirror_thead.rows[0].cells[i].style.width = this.node.rows[0].cells[i].offsetWidth + 'px';
			}
		}

		this.mirror.style.display = 'block';
		this.mirror.style.top = (this.position_top !== null ? this.position_top : 0) + 'px';
		this.bFixed = true;
	}
}

BX.CFixerTHead.prototype._UnFix = function()
{
	if (this.bFixed)
	{
		if (!!this.mirror)
		{
			this._clear_mirror();
		}

		this.bFixed = false;
	}
}

BX.CFixerTHead.prototype._clear_mirror = function()
{
	if (!!this.mirror)
		this.mirror.parentNode.removeChild(this.mirror);

	this.mirror = null;
	this.mirror_thead = null;
}

BX.CFixerTHead.prototype._check_scroll = function(scrollLeft)
{
	if (!!this.mirror)
	{
		this.mirror.style.left = (this.pos.left - scrollLeft) + 'px';
		if (this.bFixed && this['position_' + this.params.type] !== null)
			this.mirror.style[this.params.type] = this['position_' + this.params.type] + 'px'
	}
}

/******************************** admin menu unification ********************/

BX.adminShowMenu = function(el, menu, params)
{
	if (el.OPENER)
		return true;

	var bindElement = el,
		pseudo_el = null;

	if (typeof el == 'object' && !BX.type.isElementNode(el) && typeof el.x != 'undefined')
	{
		pseudo_el = document.body.appendChild(BX.create('DIV', {
			style: {
				position: 'absolute',
				left: el.x + 'px',
				top: el.y + 'px',
				height: 0,
				width: 0
			}
		}));

		bindElement = pseudo_el;
	}

	params = params || {};

	bindElement.OPENER = new BX.COpener({
		DIV: bindElement,
		MENU: menu,
		TYPE: 'click',
		ACTIVE_CLASS: (typeof params.active_class != 'undefined') ? params.active_class : 'adm-btn-active',
		CLOSE_ON_CLICK: (typeof params.close_on_click != 'undefined') ? !!params.close_on_click : true
	});

	var f = function()
	{
		BX.onCustomEvent(el, 'onAdminMenuClose');

		if (!!pseudo_el)
		{
			pseudo_el.parentNode.removeChild(pseudo_el);
			pseudo_el = null;
		}

		bindElement = null;
	}

	BX.addCustomEvent(bindElement.OPENER, 'onOpenerMenuClose', f);
	BX.addCustomEvent(bindElement.OPENER, 'onOpenerMenuOpen', function() {
		BX.onCustomEvent(el, 'onAdminMenuShow');
	});

	bindElement.OPENER.Toggle();
}

/****************Admin Filter********************************/

BX.AdminFilter = function(filter_id, aRows)
{
	var _this = this;
	this.filter_id = filter_id;
	this.aRows = aRows;
	this.oVisRows = {};
	this.oOptions = {};
	this.curID = "0";
	this.filteredId = false;
	this.form = jsUtils.FindParentObject(BX(this.filter_id), "form");
	this.popupItems = {};
	this.missingRows = 0;
	this.tableWrap = null;
	this.easing = null;
	this.startContentHeight = 0;


	this.InitFilter = function(oVisRows)
	{
		var vREmpty = this.isObjectEmpty(oVisRows);

		this.oVisRows = oVisRows;

		var tbl = BX(this.filter_id);

		if(!tbl)
			return;

		var n=tbl.rows.length;
		this.missingRows = tbl.rows.length - this.aRows.length;
		var diff = this.missingRows;

		for(var i=n-1; i>=0; i--)
		{
			var row = tbl.rows[i];
			var td = row.insertCell(-1);
			var tail = "";
			this.WrapRow(row);

			if( i-diff >=0 )
			{
				tail = this.aRows[i-diff];
			}
			else
			{
				tail = "miss-"+i;
				this.aRows.unshift(tail);

				if(vREmpty)
					this.oVisRows[tail] = true;
			}

			row.id = this.filter_id+'_row_'+tail;

			if(this.oVisRows[tail] != true)
				row.style.display = 'none';

			td.innerHTML = '<span class="adm-filter-item-delete" onclick="this.blur(); '+this.filter_id+'.DeleteFilterRow(\''+row.id+'\');" hidefocus="true" title="'+phpVars.messFilterLess+'" style="display: none;"></span>';
		}

		for(i=0; i<n; i++)
		{
			var tr = tbl.insertRow(i*2+1);

			if(this.oVisRows[this.aRows[i]] != true)
				tr.style.display = 'none';
			tr.id = this.filter_id+'_row_'+this.aRows[i]+'_delim';

			var td = tr.insertCell(-1);
			td.colSpan = 3;
			td.className = 'delimiter';
			td.innerHTML = '<div class="empty"></div>';
		}

		try{
			tbl.style.display = 'table';}
		catch(e){
			tbl.style.display = 'block';}

		this.tableWrap = tbl.parentNode;

		this.DisplayNonEmptyRows();
		this.ChangeViewDependVisible();
	}

	this.InitFirst = function()
	{
		this.oOptions["0"] = {
						FIELDS: {},
						EDITABLE: false
					};
	}

	this.isObjectEmpty = function( obj )
	{
		for ( var key in obj )
			return false;

		return true;
	}

	this.ChangeViewDependVisible = function()
	{
		var countVR = this.CountVisibleRows();

		if(countVR < 1)
			this.ToggleFilterRow(this.filter_id+'_row_'+this.aRows[0], true);

		if(countVR <= 1)
			this.ToggleButtonsHideAll();

		if(countVR >= 2)
			this.ToggleButtonsShowAll();

		this.SetBottomStyle();
	}

	this.WrapCalendarBlock = function(calendarBlock)
	{
		var wrap = document.createElement("DIV");
		wrap.className = "adm-filter-box-sizing";
		cbChildren = BX.findChildren(calendarBlock);

		for(var i in cbChildren)
			wrap.appendChild(cbChildren[i]);

		calendarBlock.appendChild(wrap);
	}

	this.WrapRow = function(row)
	{
		row.cells[0].className = "adm-filter-item-left";
		row.cells[1].className = "adm-filter-item-center";
		row.cells[2].className = 'adm-filter-item-right';

		row.cells[0].innerHTML = row.cells[0].textContent || row.cells[0].innerText;

		var calendarBlock = BX.findChild(row.cells[1], {'className': 'adm-calendar-block'}, true);

		if(calendarBlock)
		{
			BX.addClass(calendarBlock,"adm-filter-alignment");
			this.WrapCalendarBlock(calendarBlock);
			return;
		}

		if (row.cells[1].children[0] && !BX.hasClass(row.cells[1].children[0], 'adm-filter-alignment'))
		{
			var boxSizing = BX.create('div', {props: {className: 'adm-filter-box-sizing'}});
			var alingment = BX.create('div', {props: {className: 'adm-filter-alignment'}});

			row.cells[1].innerHTML = this.WrapCell(row.cells[1]).innerHTML;

			while(row.cells[1].children.length>0)
				boxSizing.appendChild(row.cells[1].children[0]);

			alingment.appendChild(boxSizing);
			row.cells[1].appendChild(alingment);
		}
		return row;
	}

	this.WrapCell = function(cell)
	{
		var newCell = cell.cloneNode(true);
		newCell.innerHTML = "";

		while(cell.childNodes.length)
		{
			switch(cell.childNodes[0].nodeName.toLowerCase())
			{
				case 'small':
					this.WrapElement(cell.childNodes[0], "", "span", "adm-filter-text-wrap");
					break;

				case '#text':

					cell.childNodes[0].nodeValue = jsUtils.trim(cell.childNodes[0].nodeValue);

					if(cell.childNodes[0].nodeValue == '')
					{
						cell.removeChild(cell.childNodes[0]);
						continue;
					}

					this.WrapElement(cell.childNodes[0], "", "span", "adm-filter-text-wrap");

					break;

				case 'label':

					if(cell.childNodes[0].className == "adm-designed-checkbox-label")
						break;


					var input = BX.findChild(cell.childNodes[0],{tag: "input"});

					if(input)
						var wrap = this.WrapInputElement(input);

					break;

				case 'input':

					var helpIcon = false;

					var nextInput = BX.findNextSibling(cell.childNodes[0], {tagName: "INPUT"});

					if(cell.childNodes[0].type == "text" && ( !nextInput || nextInput.type != "text"))
						helpIcon = BX.findChild(cell.childNodes[0].parentNode, {className: "adm-input-help-icon"});

					var wrap = this.WrapInputElement(cell.childNodes[0]);

					if(helpIcon)
					{
						BX.addClass (wrap, "adm-input-help-icon-wrap");
						wrap.appendChild(helpIcon);
					}
					break;

				case 'select':
					this.WrapInputElement(cell.childNodes[0]);
					break;

				case 'iframe':
					cell.childNodes[0].style.display = 'none';
					break;

				case 'span':
					if(cell.childNodes[0].style.display != 'none')
						cell.childNodes[0].style.display = 'inline-block';
					break;

				default:
					break;
			}

			newCell.appendChild(cell.childNodes[0]);
		}

		return newCell;
	}

	this.WrapInputElement = function(el)
	{
		var wrap = false;
		switch (el.type)
		{
			case "select-one":
				wrap = this.WrapElement(el,"adm-select","span","adm-select-wrap");
				break;

			case "select-multiple":
				wrap = this.WrapElement(el,"adm-select-multiple","span","adm-select-wrap-multiple");
				break;

			case "text": // input
				wrap = this.WrapElement(el,"adm-input","div","adm-input-wrap");
				break;

			case "checkbox":

				var label = BX.findChild(el.parentNode, {tagName: "label", htmlFor: el.id});
				if(label)
				{
					var wraplabel = this.WrapElement(el, "", "label", "");

					if(label && label.childNodes[0])
					{
						wraplabel.appendChild(label.childNodes[0]);
						label.parentNode.removeChild(label);
					}
				}

				BX.adminFormTools.modifyCheckbox(el);
				break;

			case 'submit':
			case 'button':
			case 'reset':
			case "hidden":
			default:
				break;
		}

		return wrap;
	}

	this.WrapElement = function(el, elClass, wrapType, wrapClass)
	{
		var wrap = document.createElement(wrapType);

		if(wrapClass)
			wrap.className = wrapClass;

		if(elClass)
			el.className = elClass;

		el.parentNode.insertBefore(wrap, el);
		wrap.appendChild(el);

		return wrap;
	}

	this.FilteredTabMark = function(tabId)
	{
		for(var key in this.oOptions)
		{
			var tab = BX("adm-filter-tab-"+this.filter_id+"-"+key);

			if(BX.hasClass(tab,"adm-current-filter-tab"))
					BX.removeClass(tab,"adm-current-filter-tab")

			if(tabId !== false && key == tabId)
				BX.addClass(tab,"adm-current-filter-tab");
		}


		this.SaveFilteredId(tabId);
		this.filteredId = tabId;
		this.SetFilteredBG(tabId);
	}

	this.OnSet = function(table_id, url)
	{
		BX.onCustomEvent(window, 'onBeforeAdminFilterSet');
		window[table_id].GetAdminList(url+'set_filter=Y'+this.GetParameters());

		if(this.curID != "0")
			this.Save();

		this.FilteredTabMark(this.curID);
	}

	this.OnClear = function(table_id, url)
	{
		BX.onCustomEvent(window, 'onBeforeAdminFilterClear');

		this.ClearParameters();
		window[table_id].GetAdminList(url+'del_filter=Y'+this.GetParameters());

		this.FilteredTabMark(false);
	}

	this.ApplyFilter = function(id)
	{
		this.StartAnimation();

		if(this.curID == "0")
			this.oOptions["0"]["FIELDS"] = this.GetFilterFields(true);

		this.curID = id;
		this.SetFilterFields(this.oOptions[id]["FIELDS"]);
		this.SaveOpenTab(id);

		this.EndAnimation();
	}

	this.Save = function(saveAs)
	{
		var fields = this.GetFilterFields();

		if((!this.oOptions[this.curID]["EDITABLE"] && !saveAs))
		{
			this.SaveInsteadPreset();
			return;
		}

		if(saveAs || this.curID == "0")
			this.ShowSaveOptsWnd(fields, false);
		else
			this.SaveToBase(this.oOptions[this.curID]["NAME"], this.oOptions[this.curID]["COMMON"], fields, false, false);

	}

	this.SaveAs = function()
	{
		this.Save(true);
	}

	this.Delete = function()
	{
		this.DeleteFromBase(this.curID);
	}

	this.GetClearFields = function()
	{
		var fields = this.GetFilterFields();

		for(var key in fields)
			fields[key]["value"] = "";

		return fields;
	}

	this.CreateNewFilter = function()
	{
		var fields = this.GetClearFields();
		this.ShowSaveOptsWnd(fields, true);
	}

	this.AddFilterTab = function(id, name)
	{
		var tabsBlock = BX("filter-tabs-"+this.filter_id);
		var newTab = document.createElement('span');

		newTab.className = "adm-filter-tab";
		newTab.id = "adm-filter-tab-"+this.filter_id+"-"+id;
		newTab.onclick = function(){ _this.SetActiveTab(this); _this.ApplyFilter(id); };
		newTab.innerHTML = BX.util.htmlspecialchars(name);
		tabsBlock.insertBefore(newTab, BX("adm-filter-add-tab-"+this.filter_id));
		this.SetActiveTab(newTab);
		this.ApplyFilter(id);
	}

	this.ReplaceFilterTab = function(oldId, newId)
	{
		tab = BX("adm-filter-tab-"+this.filter_id+"-"+oldId);

		if(!tab)
			return false;

		tab.id = "adm-filter-tab-"+this.filter_id+"-"+newId;

		tab.onclick = function(){ _this.SetActiveTab(this); _this.ApplyFilter(newId); };
	}

	this.DelFilterTab = function(id)
	{
		var delTab = BX("adm-filter-tab-"+this.filter_id+"-"+id);
		delTab.parentNode.removeChild(delTab);
		BX("adm-filter-tab-"+this.filter_id+"-"+"0").click();
	}

	this.SetFilteredBG = function(id)
	{
		if(!this.filteredId && id !== false)
			return;

		if(id == this.filteredId && id !== false)
			BX.addClass(BX("adm-filter-tab-wrap-"+this.filter_id),"adm-current-filter");
		else
			BX.removeClass(BX("adm-filter-tab-wrap-"+this.filter_id),"adm-current-filter");
	}

	this.SetActiveTab = function(tabObj)
	{
		var arPrevSelTabs = BX.findChildren(tabObj.parentNode, {tag: "span"} ,false);

		for (i=arPrevSelTabs.length-1; i>=0; i--)
			if(BX.hasClass(arPrevSelTabs[i] ,"adm-filter-tab-active"))
				BX.removeClass(arPrevSelTabs[i] ,"adm-filter-tab-active");

		var tabIdBegin = "adm-filter-tab-"+this.filter_id+"-";

		var tabId = tabObj.id.substr(tabIdBegin.length,tabObj.id.length);

		this.SetFilteredBG(tabId);

		BX.addClass(tabObj,"adm-filter-tab-active");

	}

	this.ShowSaveOptsWnd = function(fields, empty)
	{
		var bCreated = false;
		if(!window['filterSaveOptsDialog'+this.filter_id])
		{
			window['filterSaveOptsDialog'+this.filter_id] = new BX.CDialog({
				'content':'<form name="flt_save_opts_'+this.filter_id+'" onkeypress=" return '+this.filter_id+'.SaveOptsWndKeyPress(event);"></form>',
				'title': BX.message('JSADM_FLT_SAVE_TITLE'),
				'width': 450,
				'height': 100,
				'resizable': false
			});
			bCreated = true;
		}

		var formOpts = document['flt_save_opts_'+this.filter_id];

		formOpts.onKeyPress = this.onKeyPress;
		var fsTable= BX('filter_save_opts_'+this.filter_id).children[0];

		window['filterSaveOptsDialog'+this.filter_id].ClearButtons();

		window['filterSaveOptsDialog'+this.filter_id].SetButtons([
			{
				'id': this.filter_id+"_btn_save",
				'className':'adm-btn-save',
				'title': BX.message('JSADM_FLT_SAVE'),
				'action': function(){

					if(formOpts.common)
						common = formOpts.common.checked;
					else
						common = false;

					_this.SaveToBase(formOpts.filter_name.value, common, fields, true, empty);

					this.parentWindow.Close();
				}
			},
			BX.CDialog.prototype.btnCancel
		]);

		window['filterSaveOptsDialog'+this.filter_id].adjustSizeEx();
		window['filterSaveOptsDialog'+this.filter_id].Show();

		if(bCreated)
			formOpts.appendChild(fsTable);

		if(this.curID != "0" && !empty)
		{
			formOpts.filter_name.value = (this.oOptions[this.curID]["NAME"] ? this.oOptions[this.curID]["NAME"] : '');

			if(formOpts.common)
				formOpts.common.checked = (this.oOptions[this.curID]["COMMON"] =='Y' ? true : false);
		}
		else
		{
			formOpts.filter_name.value = BX.message('JSADM_FLT_NEW_NAME');

			if(formOpts.common)
				formOpts.common.checked = false;
		}

		formOpts.filter_name.focus();
	}

	this.SaveOptsWndKeyPress = function(event)
	{
		if(!event)
			event = window.event;

		if(!event)
			return true;

		if(event.keyCode == 13)
		{
			BX(this.filter_id+"_btn_save").click();
			return false;
		}

		if(event.keyCode == 27)
		{
			window['filterSaveOptsDialog'+this.filter_id].Close();
			return false;
		}

		return true;
	}

	this.DeleteFromBase = function(id)
	{
		if(!confirm(BX.message('JSADM_FLT_DEL_CONFIRM')))
			return;

		var data = {
			'id': id,
			'action': 'del_filter',
			'sessid': phpVars.bitrix_sessid
		};

		var callback = function(result)
		{
			if(result)
			{
				_this.DelFilterTab(id);
				delete _this.oOptions[id];
			}
			else
				alert(BX.message('JSADM_FLT_DEL_ERROR'));
		}

		BX.ajax.post('/bitrix/admin/filter_act.php', data, callback);

	}

	this.SaveInsteadPreset = function()
	{
		var data = {
			'filter_id': this.filter_id,
			'preset_id': this.curID,
			'action': 'save_filter',
			'sessid': phpVars.bitrix_sessid,
			'name': this.oOptions[this.curID]["NAME"],
			'fields': _this.GetFilterFields()
		};

		var callback = function(resultId)
		{
			if(resultId)
			{
				_this.oOptions[resultId] =
				{
					NAME: _this.oOptions[_this.curID]["NAME"],
					FIELDS: _this.GetFilterFields(),
					EDITABLE: true,
					PRESET_ID: _this.curID
				};

				_this.ReplaceFilterTab(_this.curID, resultId);
				delete(_this.oOptions[_this.curID]);
				_this.curID = resultId;
			}
			else
				alert(BX.message('JSADM_FLT_SAVE_ERROR'));
		}

		BX.ajax.post('/bitrix/admin/filter_act.php', data, callback);
	}


	this.SaveToBase = function(name, common, fields, saveAs, empty)
	{
		if(name=="")
			name = BX.message('JSADM_FLT_NO_NAME');

		var data = {
			'filter_id': this.filter_id,
			'action': 'save_filter',
			'sessid': phpVars.bitrix_sessid,
			'name': name,
			'common': common ? 'Y' : 'N',
			'fields': fields
		};

		if(!saveAs && this.curID != "0")
			data['id']=this.curID;

		if(!saveAs && this.oOptions[this.curID]["PRESET_ID"])
			data['preset_id']=this.oOptions[this.curID]["PRESET_ID"];

		var callback = function(resultId)
		{
			if(resultId)
			{
				_this.oOptions[resultId] =
				{
					NAME: name,
					COMMON: common,
					FIELDS: fields,
					EDITABLE: true
				};

				if(saveAs || _this.curID == "0")
					_this.AddFilterTab(resultId, name);

				if(empty)
					_this.ClearParameters();
			}
			else
				alert(BX.message('JSADM_FLT_SAVE_ERROR'));
		}

		BX.ajax.post('/bitrix/admin/filter_act.php', data, callback);

		return data;
	}

	this.ClearParameters = function()
	{
		if(!this.form)
			return;

		var i;
		var n = this.form.elements.length;
		for(i=0; i<n; i++)
		{
			var el = this.form.elements[i];
			switch(el.type.toLowerCase())
			{
				case 'text':
				case 'textarea':
					el.value = '';
					break;

				case 'select-one':
					el.selectedIndex = 0;
					if(el.onchange)
						el.onchange();
					break;

				case 'select-multiple':
					var j;
					var l = el.options.length;
					for(j=0; j<l; j++)
						el.options[j].selected = false;
					break;

				default:
					break;
			}
		}
	}

	this.GetRowByElement = function(element)
	{
		return jsUtils.FindParentObject(element, "tr");
	}

	this.SetFilterFields = function(fields)
	{
		this.ClearParameters();
		var checkboxesIdx = [];

		for(var i=0, n = this.form.elements.length; i<n; i++)
		{
			var el = this.form.elements[i];

			if(el.type == 'select-multiple')
				var elName = el.name.substr(0, el.name.length - 2);
			else if(el.type == 'checkbox' && el.name.search(/[\[\]]/))
				{
					var elName = el.name.substr(0, el.name.length - 2);

					if(checkboxesIdx[elName] == undefined)
						checkboxesIdx[elName] = 0;
					else
						checkboxesIdx[elName]++;

					elName +="_cbxIdx_"+checkboxesIdx[elName];

					el.checked = false;
				}
			else
				var elName = el.name;

			if(!fields[elName])
			{
				var row = this.GetRowByElement(el);

				if(!row)
					continue;

				if(this.IsAllRowElementsHidden(row.id, fields))
					this.ToggleFilterRow(row.id, false, false, true);

				continue;
			}


			switch(el.type.toLowerCase())
			{
				case 'select-one':
					el.value = fields[elName]["value"];

					if(el.value == "")
						el.selectedIndex = 0;

					break;

				case 'text':
				case 'textarea':
					el.value = fields[elName]["value"];
					break;

				case 'radio':
				case 'checkbox':
					el.checked = (el.value == fields[elName]["value"]);
					break;

				case 'select-multiple':
					var bWasSelected = false;
					el.value = null;
					el.options[0].selected = false;
					for(var j=0, l=el.options.length; j<l; j++)
					{
						for(var option in fields[elName]['value'])
						{
							if(el.options[j].value == fields[elName]['value'][option])
							{
								el.options[j].selected = true
								bWasSelected = true;
							}
						}
					}

					if(!bWasSelected && el.options.length > 0 && el.options[0].value == '')
						el.options[0].selected = true;

					break;

				default:
					break;
			}

			if(el.onchange)
				el.onchange();

			if(fields[elName]['hidden'] ==  'true' && this.IsAllRowElementsHidden(this.GetRowByElement(el).id, fields))
				this.ToggleFilterRow(this.GetRowByElement(el).id, false, false, true);
			else
				this.ToggleFilterRow(this.GetRowByElement(el).id, true, false);
		}

		if(this.CountVisibleRows() < 1)
			this.ToggleFilterRow(this.filter_id+'_row_'+this.aRows[0], true, false);

		//this.SaveRowsOption();
	}

	this.IsFormElementHidden = function (el)
	{
		return !el.offsetWidth && !el.offsetHeight;
	}

	this.IsAllRowElementsHidden = function (rowId, fields)
	{
		var bAllHidden = true;

		for(var i=0, n = this.form.elements.length; i<n; i++)
		{
			var el = this.form.elements[i];

			if(!fields[el.name])
				continue;

			if(jsUtils.FindParentObject(el, "tr").id != rowId)
				continue;

			if(fields[el.name]['hidden'] == 'false')
			{
					bAllHidden = false;
					break;
			}
		}

		return bAllHidden;
	}

	this.GetFilterFields = function(bSetVisibilityByRow)
	{
		var fields = {};
		var checkboxesIdx = [];

		for(var i=0, n = this.form.elements.length; i<n; i++)
		{
			var el = this.form.elements[i];

			if(el.type == 'select-multiple')
				var elName = el.name.substr(0, el.name.length - 2);
			else if(el.type == 'checkbox' && el.name.search(/[\[\]]/))
				{
					var elName = el.name.substr(0, el.name.length - 2);

					if(checkboxesIdx[elName] == undefined)
						checkboxesIdx[elName] = 0;
					else
						checkboxesIdx[elName]++;

					elName +="_cbxIdx_"+checkboxesIdx[elName];
				}
			else
				var elName = el.name;

			switch(el.type.toLowerCase())
			{
				case 'select-one':
				case 'text':
				case 'textarea':
					fields[elName] = { value: el.value };
					break;

				case 'radio':
					if(el.checked)
						fields[elName] = { value: el.value };
					break;

				case 'checkbox':
					if(el.checked)
						fields[elName] = { value: el.value };
					else
						fields[elName] = { value: false };
					break;

				case 'select-multiple':
					fields[elName] = {value:[]};

					for(var j=0, l = el.options.length; j<l; j++)
						if(el.options[j].selected && el.options[j].value)
							fields[elName]['value']['sel_'+el.options[j].value] = el.options[j].value;

					//fields[elName]['hidden'] = this.IsFormElementHidden(el);

					break;
				default:
					break;
			}

			if(!fields[elName])
				continue;

			if(bSetVisibilityByRow)
				fields[elName]['hidden'] = (this.GetRowByElement(el).style.display == 'none' ? 'true' : 'false');
			else
				fields[elName]['hidden'] = this.IsFormElementHidden(el) ? 'true' : 'false';

		}

		return fields;
	}

	this.CheckActive = function()
	{
		if(!this.form)
			return;

		var i;
		var n = this.form.elements.length;
		for(i=0; i<n; i++)
		{
			var el = this.form.elements[i];
			if(el.disabled)
				continue;
			var tr = this.GetRowByElement(el);
			if(tr && tr.style && tr.style.display == 'none')
				continue;

			switch(el.type.toLowerCase())
			{
				case 'select-one':
					if(el.options.length > 0)
						if(el.options[0].value.length != 0 && (el.options[0].value.toUpperCase() != 'NOT_REF' || el.value.toUpperCase() == 'NOT_REF'))
							break;
				case 'text':
				case 'textarea':
					if(el.value.length > 0)
						return true;
					break;
				case 'checkbox':
					if(el.checked)
						return true;
					break;
				case 'select-multiple':
					var j;
					var l = el.options.length;
					for(j=0; j<l; j++)
						if(el.options[j].selected && el.options[j].value != '')
							return true;
					break;
				default:
					break;
			}
		}
		return false;
	}

	this.GetParameters = function()
	{
		if(!this.form)
			return;

		var i, s = "";
		var n = this.form.elements.length;
		for(i=0; i<n; i++)
		{
			var el = this.form.elements[i];

			if(el.disabled)
				continue;

			var tr = jsUtils.FindParentObject(el, 'tr');

			if(tr && tr.style && tr.style.display == 'none')
				continue;

			if(el.className == "adm-select adm-calendar-period" && el.value != '')
			{
				var selPParent = el.parentNode.parentNode;
				var inputFrom = BX.findChild(selPParent, {'className':'adm-input adm-calendar-from'},true);
				var inputTo = BX.findChild(selPParent, {'className':'adm-input adm-calendar-to'},true);

				var dateFrom = false;
				var dateTo = false;
				var today = new Date();
				var year = today.getFullYear();
				var month = today.getMonth();
				var day = today.getDate();
				var dayW = today.getDay();

				if (dayW == 0)
					dayW = 7;

				switch(el.value)
				{
					case 'exact':
						dateFrom = new Date(inputFrom.value.replace(/(\d+).(\d+).(\d+)/, '$3/$2/$1'));
						dateTo = dateFrom;
						break;

					case 'after':
						inputTo.value = "";
						break;

					case 'before':
						inputFrom.value = "";
						break;

					default:
						break;
				}

				var format = window[inputFrom.name+"_bTime"] ? BX.message('FORMAT_DATETIME') : BX.message('FORMAT_DATE');

				if(dateFrom)
					inputFrom.value = BX.formatDate(dateFrom, format);

				if(dateTo)
					inputTo.value = BX.formatDate(dateTo, format);
			}

			var val = "";
			switch(el.type.toLowerCase())
			{
				case 'select-one':
				case 'text':
				case 'textarea':
				case 'hidden':
					val = el.value;
					break;
				case 'radio':
				case 'checkbox':
					if(el.checked)
						val = el.value;
					break;
				case 'select-multiple':
					var j;
					var l = el.options.length;
					for(j=0; j<l; j++)
						if(el.options[j].selected)
							s += '&' + el.name + '=' + encodeURIComponent(el.options[j].value);
					break;
				default:
					break;
			}
			if(val != "")
				s += '&' + el.name + '=' + encodeURIComponent(val);

		}
		return s;
	}

	this.DisplayNonEmptyRows = function()
	{
		if(!this.form)
			return;

		var i;
		var n = this.form.elements.length;
		for(i=0; i<n; i++)
		{
			var el = this.form.elements[i];
			if(el.disabled)
				continue;

			var bVisible = false;
			switch(el.type.toLowerCase())
			{
				case 'select-one':
					if(el.value.length>0 && (el.options[0].value.length == 0 || (el.options[0].value != el.value)))
						bVisible = true;
					break;

				case 'text':
				case 'textarea':
					if(el.value.length>0)
						bVisible = true;
					break;

				case 'checkbox':
					if(el.checked)
						bVisible = true;
					break;

				case 'select-multiple':
					var j;
					var l = el.options.length;
					for(j=0; j<l; j++)
						if(el.options[j].selected && el.options[j].value != '')
						{
							bVisible = true;
							break;
						}
					break;

				default:
					break;
			}
			if(bVisible)
			{
				var tr = jsUtils.FindParentObject(el, 'tr');
				if(tr.id)
					this.ToggleFilterRow(tr.id, true, false);
			}
		}
	}

	this.CountVisibleRows = function()
	{
		var counter = 0;
		for (var i=this.aRows.length-1; i>=0; i--)
			if(this.oVisRows[this.aRows[i]])
				counter++;

		return counter;
	}

	this.SetBottomStyle = function()
	{
		var bottomSeparator = BX(this.filter_id+"_bottom_separator");
		var contentDiv = BX(this.filter_id+"_content");

		if(this.CountVisibleRows() > 1)
		{
			contentDiv.className = "adm-filter-content";
			bottomSeparator.style.display = "block";
		}
		else
		{
			contentDiv.className = "adm-filter-content adm-filter-content-first";
			bottomSeparator.style.display = "none";
		}
	}

	this.ToggleButtonShow = function(rowId)
	{
		BX(rowId).cells[2].children[0].style.display = 'block';
	}

	this.ToggleButtonHide = function(rowId)
	{
		BX(rowId).cells[2].children[0].style.display = 'none';
	}

	this.ToggleButtonsShowAll = function()
	{
		for(var key in this.aRows)
			this.ToggleButtonShow(this.filter_id+'_row_'+this.aRows[key]);
	}

	this.ToggleButtonsHideAll = function()
	{
		for(var key in this.aRows)
			this.ToggleButtonHide(this.filter_id+'_row_'+this.aRows[key]);
	}

	this.ToggleFilterRow = function(rowId, on, bSave, skipControl)
	{
		var row = BX(rowId),
			delimiter = BX(rowId+'_delim'),
			ret = 0;


		if(!row)
			return ret;

		var short_id = rowId.substr((this.filter_id+'_row_').length);

		if(on != true && on != false)
			on = (row.style.display == 'none');

		if(on == true)
		{
			try{
				row.style.display = 'table-row';
				delimiter.style.display = 'table-row';
			}
			catch(e){
				row.style.display = 'block';
				delimiter.style.display = 'block';
			}
			this.oVisRows[short_id] = true;

			ret = row.offsetHeight + delimiter.offsetHeight;
		}
		else
		{
			if( skipControl || this.CountVisibleRows() > 1)
			{
				ret = -(row.offsetHeight + delimiter.offsetHeight);

				row.style.display = 'none';
				delimiter.style.display = 'none';
				this.oVisRows[short_id] = false;

			}
		}

		this.SetBottomStyle();

		var countVR = this.CountVisibleRows();

		if(countVR == 1)
			this.ToggleButtonsHideAll();

		if(countVR == 2)
			this.ToggleButtonsShowAll();


		if(bSave != false)
			this.SaveRowsOption();

		return ret;
	}

	this.DeleteFilterRow = function(rowId)
	{
		this.StartAnimation();
		this.ToggleFilterRow(rowId);
		this.EndAnimation();
	}

	this.StartAnimation = function()
	{
		if (this.easing)
			this.easing.stop();

		this.startContentHeight = this.tableWrap.offsetHeight;
	}

	this.EndAnimation = function()
	{
		var newHeight = this.tableWrap.offsetHeight;
		if (newHeight == 0)
			return;

		if (this.startContentHeight == newHeight)
		{
			this.tableWrap.style.height = "auto";
			newHeight = this.tableWrap.offsetHeight;
		}

		this.tableWrap.style.height = this.startContentHeight + "px";
		this.tableWrap.style.overflowY = "hidden";

		this.easing = new BX.easing({
			duration : 500,
			start : { height: this.startContentHeight, opacity : 0 },
			finish : { height: newHeight, opacity : 100 },
			transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),

			step : BX.proxy(function(state){
				this.tableWrap.style.height = state.height + "px";
			}, this),

			complete : BX.proxy(function(){
				this.tableWrap.style.height = "auto";
				this.tableWrap.style.overflowY = "visible";
				this.easing = null;
			}, this)

		});
		this.easing.animate();
	}

	this.SaveRowsOption = function()
	{
		if(this.curID != "0")
		{
			this.Save(false);
			return true;
		}


		var sRows = '';

		for(var key in this.oVisRows)
			if(this.oVisRows[key] == true)
				sRows += (sRows != ''? ',':'')+key;

		jsUserOptions.SaveOption('filter', this.filter_id, 'rows', sRows);
	}

	this.SaveOpenTab = function(id)
	{
		var data = {
			'id': id,
			'filter_id': this.filter_id,
			'action': 'open_tab_save',
			'sessid': phpVars.bitrix_sessid
		};

		BX.ajax.post('/bitrix/admin/filter_act.php', data);
	}

	this.SaveFilteredId = function(id)
	{
		var data = {
			'id': id,
			'filter_id': this.filter_id,
			'action': 'filtered_tab_save',
			'sessid': phpVars.bitrix_sessid
		};

		BX.ajax.post('/bitrix/admin/filter_act.php', data);
	}

	this.ToggleAllFilterRows = function(on)
	{
		var tbl = document.getElementById(this.filter_id);
		if(!tbl)
			return;

		this.StartAnimation();

		var n = tbl.rows.length;
		for(var i=n-1; i>=0; i--)
		{
			var row = tbl.rows[i];
			if(row.id && row.cells[0].className != 'delimiter')
				this.ToggleFilterRow(row.id, on, false);
		}

		if(on)
			this.ToggleButtonsShowAll();
		else
			this.ToggleButtonsHideAll();

		this.SaveRowsOption();

		this.EndAnimation();
	}

	this.SaveMenuShow = function(el)
	{
		var menuItems =[];

		if(this.curID != "0")
			menuItems.push({TEXT: BX.message('JSADM_FLT_SAVE'), ONCLICK: filter_id+".Save();"});

		menuItems.push({TEXT: BX.message('JSADM_FLT_SAVE_AS'), ONCLICK: filter_id+".SaveAs();"});

		if(this.curID != "0" && this.oOptions[this.curID].EDITABLE)
			menuItems.push({TEXT: BX.message('JSADM_FLT_DELETE'), ONCLICK: filter_id+".Delete();"});

		if (!el.OPENER)
			BX.adminShowMenu(el,menuItems);
		else
			el.OPENER.SetMenu(menuItems);

	}

	this.SettMenuItemClick = function(rowId,objItem)
	{
		this.StartAnimation();

		var scrollOffset = this.ToggleFilterRow(rowId);

		this.EndAnimation();

		BX.onCustomEvent(objItem, 'onAdminFilterToggleRow');
	}

	this.SettMenuShow = function(el)
	{
		var tbl = BX(this.filter_id);

		if(!tbl)
			return;

		var menuItems =[];
		var diff = this.missingRows;
		var itemsIdx = this.aRows.length-1;

		for(var i = tbl.rows.length-1; i >=0; i--)
		{
			var row = tbl.rows[i];

			if(!row.id || row.cells[0].className == 'delimiter')
				continue;

			var text ="";
			if(itemsIdx-diff >= 0)
				text = this.popupItems[this.aRows[itemsIdx]];
			else
				text = (row.cells[0].textContent || row.cells[0].innerText).replace(/:$/,"");

			menuItems.unshift({
				TEXT: text,
				ONCLICK: filter_id+".SettMenuItemClick('"+row.id+"',this);",
				CLOSE_ON_CLICK: false,
				CHECKED: (row.style.display == 'none') ? false : true
			});

			itemsIdx--;
		}

		menuItems.push({SEPARATOR: true});
		menuItems.push({
			TEXT: BX.message('JSADM_FLT_SHOW_ALL'),
			ONCLICK: filter_id+".ToggleAllFilterRows(true); BX.onCustomEvent(this, 'onAdminFilterToggleRow');"
		});

		menuItems.push({
			TEXT: BX.message('JSADM_FLT_HIDE_ALL'),
			ONCLICK: filter_id+".ToggleAllFilterRows(false); BX.onCustomEvent(this, 'onAdminFilterToggleRow');"
		});

		if (!el.OPENER)
			BX.adminShowMenu(el,menuItems);
		else
			el.OPENER.SetMenu(menuItems);
	}
}

BX.adminChain = {
	_chain: '',

	addItems: function(divId)
	{
		BX.ready(function(){BX.adminChain._addItems(divId)});
	},

	_addItems: function(divId)
	{
		var main_chain = BX("main_navchain");
		if(!main_chain)
			return;

		if(this._chain == '')
			this._chain = main_chain.innerHTML;
		else
			main_chain.innerHTML = this._chain;

		var div = BX(divId);
		if(!div)
			return;

		main_chain.innerHTML += '<span class="adm-navchain-delimiter"></span>';
		main_chain.innerHTML += div.innerHTML;
	}
}

/************************* singletons construction **************************/

BX.InitializeAdmin = function()
{
	BX.adminPanel = new BX.adminPanel();
	BX.adminMenu = new BX.adminMenu();

	if (!!(history.pushState && BX.type.isFunction(history.pushState)))
	{
		BX.adminHistory = new BX.adminHistory();
	}
}

BX.adminPanel.modifyFormElements = BX.adminFormTools.modifyFormElements;
BX.adminPanel.modifyFormElement = BX.adminFormTools.modifyFormElement;

BX.browser.addGlobalClass();
})();