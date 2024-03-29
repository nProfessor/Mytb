;(function(){

if(window.BX.adminLogin)
	return;

BX.adminLogin = function(params)
{
	BX.adminLogin = this;

	this.current_form = null;
	this.start_form = params.start_form;
	this.post_data = params.post_data;
	this.url = params.url || window.location.href;

	this.arForms = {};

	this.error_block = null;
	this.animation_duration = params.animation_duration || 500;

	this.form = params.form;
	this.login_wrapper = params.login_wrapper;
	this.window_wrapper = params.window_wrapper;
	this.popup_alignment = params.popup_alignment;
	this.auth_form_wrapper = params.auth_form_wrapper;
	this.login_variants = params.login_variants;

	BX.AUTHAGENT = this;

	BX.ready(BX.proxy(this.Init, this));
}

BX.adminLogin.prototype.registerForm = function(obForm)
{
	this.arForms[obForm.name] = obForm;
}

BX.adminLogin.prototype.Init = function()
{
	this.form = document.forms[this.form];
	this.login_wrapper = BX(this.login_wrapper);
	this.window_wrapper = BX(this.window_wrapper);
	this.popup_alignment = BX(this.popup_alignment);
	this.auth_form_wrapper = BX(this.auth_form_wrapper);
	this.login_variants = BX(this.login_variants);

	for (var i in this.arForms)
		this.arForms[i].Init(this.form)

	var hash = window.location.hash;
	if (hash.substring(0, 1) == '#')
		hash = hash.substring(1, hash.length);

	hash = hash.replace(/_message/g, '');

	if (!this.arForms[hash])
		hash = this.start_form;

	BX.bindDelegate(this.form, 'keydown', {tagName: 'INPUT'}, BX.proxy(this.hideError, this));

	BX.bind(this.form, 'submit', BX.proxy(this.hideError, this));

	this.toggleAuthForm(this.arForms[hash]);
}

BX.adminLogin.prototype.toggleAuthForm = function(obForm)
{
	if (BX.type.isString(obForm))
		obForm = this.arForms[obForm];

	this.hideError();
	if (!!this.current_form)
	{
		this.removeAuthForm(this.current_form, BX.delegate(function(){
			this.addAuthForm(obForm);
		}, this));
	}
	else
	{
		this.current_form = obForm;
		this.addAuthForm(obForm);
	}
}

BX.adminLogin.prototype.showAuthForm = function(obForm)
{
	this.current_form = obForm;

	BX.removeClass(document.body, 'login-animate-popup2');
	BX.addClass(document.body, 'login-animate-popup');

	BX.defer(obForm.onshow, obForm)();
	BX.bind(this.form, 'submit', BX.proxy(obForm.validate, obForm));
}

BX.adminLogin.prototype.addAuthForm = function (obForm)
{
	window.location.hash = obForm.name;

	this.auth_form_wrapper.appendChild(obForm.container);

	BX.defer(this.showAuthForm, this)(obForm);
}

BX.adminLogin.prototype.removeAuthForm = function(obForm, cb)
{
	BX.unbind(this.form, 'submit', BX.proxy(obForm.validate, obForm));

	this.popup_alignment.style.display = 'table-cell';
	this.popup_alignment.style.textAlign = 'center';

	BX.removeClass(document.body, 'login-animate-popup');
	BX.addClass(document.body, 'login-animate-popup2');

	BX.defer(function() {
		this.login_variants.appendChild(obForm.container);

		if (cb)
			cb();

		BX.defer(function(){
			BX.defer(obForm.onclose, obForm)();
		})();

	}, this)();
}

BX.adminLogin.prototype._loadAdmin = function(admin_html)
{
	if (BX.util.trim(admin_html).length > 0)
	{
		var w = this.window_wrapper;
		w.innerHTML = admin_html;
		w.style.display = 'block';

		setTimeout(BX.delegate(function(){
			BX.removeClass(document.body, 'login-animate');
			BX.addClass(document.body, 'login-last-animate');

			BX.defer(BX.delegate(function(){


				var onTransitionEnd = function(){
					if(this.parentNode)
						this.parentNode.removeChild(this);

					BX.removeClass(document.body, 'login-animate-popup2');
					BX.removeClass(document.body, 'login-animate-popup');

					BX.removeClass(document.body, 'login-last-animate');

					if (BX.adminLogin)
						BX.adminLogin.Destroy();
				};

				BX.bind(document.body, 'transitionend', BX.proxy(onTransitionEnd, this.login_wrapper));
				setTimeout(BX.delegate(onTransitionEnd, this.login_wrapper), 700);

			}, this))();
		}, this), 30);
	}
}

BX.adminLogin.prototype.setAuthResult = function(result)
{
	if (this.form.USER_PASSWORD)
		this.form.USER_PASSWORD.disabled = false;
	if (this.form.USER_CONFIRM_PASSWORD)
		this.form.USER_CONFIRM_PASSWORD.disabled = false;

	if (!!result)
	{
		this.current_form.onerror(result);
	}
	else
	{
		if (!!this.post_data)
			BX.ajax.post(this.url, this.post_data, BX.delegate(this._loadAdmin, this));
		else
			BX.ajax.get(this.url, BX.delegate(this._loadAdmin, this));
	}
}

BX.adminLogin.prototype.showError = function(field, error, callback, bSkipCount)
{
	this.hideError();

	BX.addClass(this.current_form.container, 'login-popup-error');
	field = this.form[field];

	var pos = BX.pos(field);

	this.error_block = this.login_wrapper.appendChild(BX.create('DIV', {
		props: {className: 'login-error-message-block'},
		style: {
			top: pos.top + 'px',
			left: pos.right + 'px'
		},
		html: '<div class="login-error-message" id="error-message">'+BX.message('admin_authorize_error')+'<span class="login-error-red">'+error.MESSAGE+'</span></div>'
	}));

	this.error_block.style.display = 'block';
	this.error_block.style.opacity = '1';

	BX.defer(function(){
		this.style.width = BX.firstChild(this).offsetWidth + 'px';
	}, this.error_block)();
}

BX.adminLogin.prototype.hideError = function()
{
	if (!!this.current_form)
		BX.removeClass(this.current_form.container, 'login-popup-error');

	if (this.error_block && !!this.error_block.parentNode)
		this.error_block.parentNode.removeChild(this.error_block)

	BX.defer(this.enableFields, this)();
}

BX.adminLogin.prototype.enableFields = function()
{
	for (var i = 0; i < this.form.elements.length; i++)
	{
		if (this.form.elements[i].disabled)
			this.form.elements[i].disabled = false;
	}
}

BX.adminLogin.prototype.Destroy = function()
{
	this.arForms = null;
	BX.adminLogin = null;
}

/* interface class for admin forms */
BX.IAdminAuthForm = function(container, params){
	this.container = container;
	this.params = params;

	this.form = null;
}
BX.IAdminAuthForm.prototype.Init = function(form)
{
	this.form = form;
	this.container = BX(this.container);
}

BX.IAdminAuthForm.prototype.validate = function(e) {}
BX.IAdminAuthForm.prototype.onshow = function() {
	this.form.action = this.params.url;
}
BX.IAdminAuthForm.prototype.onclose = function() {}
BX.IAdminAuthForm.prototype.onerror = function(error) {alert(error.MESSAGE||error);}

BX.IAdminAuthForm.prototype.fix = function()
{
	var pos = BX.pos(this.container);

	BX.adjust(this.container, {style:{
		position: 'absolute',
		top: pos.top + 'px',
		left: pos.left + 'px'
	}});
}

/* all forms handlers */

BX.authFormAuthorize = function(container, params)
{
	this.name = 'authorize';
	this.error_count = -1;

	BX.authFormAuthorize.superclass.constructor.apply(this, arguments);
}
BX.extend(BX.authFormAuthorize, BX.IAdminAuthForm);

BX.authFormAuthorize.prototype.validate = function(e)
{
	if (BX.util.trim(this.form.USER_LOGIN.value == ''))
	{
		BX.defer(BX.focus)(this.form.USER_LOGIN);
		return BX.PreventDefault(e);
	}
	if (BX.util.trim(this.form.USER_PASSWORD.value == ''))
	{
		BX.defer(BX.focus)(this.form.USER_PASSWORD);
		return BX.PreventDefault(e);
	}
	if (BX.hasClass(this.container, 'login-captcha-popup-wrap')
		&& BX.util.trim(this.form.captcha_word.value == '')
	)
	{
		BX.defer(BX.focus)(this.form.captcha_word);
		return BX.PreventDefault(e);
	}

	BX.addClass(this.container, 'login-loading-active');

	return true;
}

BX.authFormAuthorize.prototype.onshow = function()
{
	this.error_count = 0;

	BX.authFormAuthorize.superclass.onshow.apply(this, arguments);

	if (this.form.USER_LOGIN.value.length <= 0)
		BX.defer(BX.focus)(this.form.USER_LOGIN);
	else
		BX.defer(BX.focus)(this.form.USER_PASSWORD);
}

BX.authFormAuthorize.prototype.onerror = function(error)
{
	if(this.error_count == -1)
	{
		this.error_count = 1;
	}

	BX.addClass(this.container, 'login-popup-error-shake');

	if (this.error_count == 0 && error.ERROR_TYPE && error.ERROR_TYPE == 'LOGIN')
	{
		setTimeout(BX.delegate(function(){
			BX.removeClass(this.container, 'login-loading-active');
			BX.removeClass(this.container, 'login-popup-error-shake');
			BX.addClass(this.container, 'login-popup-error');

			this._showCaptcha(error);
		}, this), 395);
	}
	else
	{
		setTimeout(BX.delegate(function(){
			BX.removeClass(this.container, 'login-loading-active');
			BX.removeClass(this.container, 'login-popup-error-shake');
			BX.adminLogin.showError('USER_PASSWORD', error)

			this._showCaptcha(error);
		}, this), 400);
	}

	this.error_count++;
}

BX.authFormAuthorize.prototype._showCaptcha = function(error)
{
	if (!!error.CAPTCHA)
	{
		this.fix();

		this.form.captcha_sid.value = error.CAPTCHA_CODE;
		BX('captcha_image').innerHTML = '<img src="/bitrix/tools/captcha.php?captcha_sid='+error.CAPTCHA_CODE+'" width="180" height="40" alt="CAPTCHA" />';

		BX.addClass(this.container, 'login-captcha-popup-wrap');
	}
}

BX.authFormForgotPassword = function(container, params)
{
	this.name = 'forgot_password';
	this.message = params.message;
	BX.authFormForgotPassword.superclass.constructor.apply(this, arguments);
}
BX.extend(BX.authFormForgotPassword, BX.IAdminAuthForm);


BX.authFormForgotPassword.prototype.validate = function(e)
{
	if (BX.util.trim(this.form.USER_LOGIN.value == '')
		&& BX.util.trim(this.form.USER_EMAIL.value == ''))
	{
		BX.defer(BX.focus)(this.form.USER_LOGIN);
		return BX.PreventDefault(e);
	}
	return true;
}

BX.authFormForgotPassword.prototype.onshow = function()
{
	BX.authFormForgotPassword.superclass.onshow.apply(this, arguments);
	BX.defer(BX.focus)(
		document.form_auth.USER_LOGIN
	);
}

BX.authFormForgotPassword.prototype.onerror = function(error)
{
	if (error.TYPE == 'OK')
	{
		this.message.setContent(error.MESSAGE);
		BX.adminLogin.toggleAuthForm('forgot_password_message');
	}
	else
	{
		BX.adminLogin.showError('USER_LOGIN', error);
	}
}

BX.authFormForgotPasswordMessage = function(container, params)
{
	this.name = 'forgot_password_message';
	BX.authFormForgotPasswordMessage.superclass.constructor.apply(this, arguments);
}
BX.extend(BX.authFormForgotPasswordMessage, BX.IAdminAuthForm);

BX.authFormForgotPasswordMessage.prototype.setContent = function(str)
{
	BX('forgot_password_message_inner', true).innerHTML = '<div class="adm-info-message-title">'+BX.message('admin_authorize_info')+'</div>' + str + '<div class="adm-info-message-icon"></div>';
}

BX.authFormChangePassword = function(container, params)
{
	this.name = 'change_password';
	this.message = params.message;
	BX.authFormChangePassword.superclass.constructor.apply(this, arguments);
}
BX.extend(BX.authFormChangePassword, BX.IAdminAuthForm);

BX.authFormChangePassword.prototype.validate = function(e)
{
	if (BX.util.trim(this.form.USER_LOGIN.value == ''))
	{
		BX.defer(BX.focus)(this.form.USER_LOGIN);
		return BX.PreventDefault(e);
	}
	if (BX.util.trim(this.form.USER_CHECKWORD.value == ''))
	{
		BX.defer(BX.focus)(this.form.USER_CHECKWORD);
		return BX.PreventDefault(e);
	}
	if (BX.util.trim(this.form.USER_PASSWORD.value == ''))
	{
		BX.defer(BX.focus)(this.form.USER_PASSWORD);
		return BX.PreventDefault(e);
	}
	if (BX.util.trim(this.form.USER_CONFIRM_PASSWORD.value == ''))
	{
		BX.defer(BX.focus)(this.form.USER_CONFIRM_PASSWORD);
		return BX.PreventDefault(e);
	}

	if (this.form.USER_PASSWORD.value !=this.form.USER_CONFIRM_PASSWORD.value)
	{
		BX.adminLogin.showError('USER_CONFIRM_PASSWORD', {MESSAGE: BX.message('AUTH_NEW_PASSWORD_CONFIRM_WRONG')});
		BX.defer(BX.focus)(this.form.USER_PASSWORD);
		return BX.PreventDefault(e);
	}

	return true;
}

BX.authFormChangePassword.prototype.onshow = function()
{
	BX.authFormChangePassword.superclass.onshow.apply(this, arguments);

	if (this.form.USER_LOGIN.value != '')
	{
		if (this.form.USER_CHECKWORD != '')
		{
			BX.defer(BX.focus)(this.form.USER_CHECKWORD);
		}
		else
		{
			BX.defer(BX.focus)(this.form.USER_PASSWORD);
		}
	}
	else
	{
		BX.defer(BX.focus)(this.form.USER_LOGIN);
	}
}

BX.authFormChangePassword.prototype.onerror = function(error)
{
	if (error.TYPE == 'OK')
	{
		this.message.setContent(error.MESSAGE);
		BX.adminLogin.toggleAuthForm('change_password_message');
	}
	else
	{
		switch(error.FIELD)
		{
			case 'LOGIN':
				BX.adminLogin.showError('USER_LOGIN', error);
				break;
			case 'CHECKWORD':
				BX.adminLogin.showError('USER_CHECKWORD', error);
				break;
			case 'CHECKWORD_EXPIRE':
				this.fix();
				BX('change_password_forgot_link').style.display = 'inline-block';
				BX.adminLogin.showError('USER_CHECKWORD', error);
				break;
			default:
				BX.adminLogin.showError('USER_CONFIRM_PASSWORD', error);
		}
	}
}

BX.authFormChangePasswordMessage = function(container, params)
{
	this.name = 'change_password_message';
	BX.authFormForgotPasswordMessage.superclass.constructor.apply(this, arguments);
}
BX.extend(BX.authFormChangePasswordMessage, BX.IAdminAuthForm);

BX.authFormChangePasswordMessage.prototype.setContent = function(str)
{
	BX('change_password_message_inner', true).innerHTML = '<div class="adm-info-message-title">'+BX.message('admin_authorize_info')+'</div>' + str + '<div class="adm-info-message-icon"></div>';
}


})();