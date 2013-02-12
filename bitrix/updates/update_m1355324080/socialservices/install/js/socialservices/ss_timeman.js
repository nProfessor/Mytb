;(function(){

if (BX.SocservTimeman)
	return;

    var SSPoint = '/bitrix/tools/oauth/socserv.ajax.php',
        intervals = {
            OPENED: 60000,
            CLOSED: 30000,
            EXPIRED: 30000,
            START: 30000
        },
        selectedTimestamp = 0,
        errorReport = '',
        SITE_ID = BX.message('SITE_ID'),
        calendarLastParams = null,

        waitDiv = null,
        waitTime = 1000,
        waitPopup = null,
        waitTimeout = null;

BX.SocservTimeman = function()
{
	this.DIV = null;
};

BX.SocservTimeman.prototype.Init = function(obTM, TMLayout, DATA)
{
    if(!!obTM.TABCONTROL)
    {
        TMLayout.insertBefore(this.Create(DATA), obTM.TABCONTROL.DIV);
    }
    else
    {
        TMLayout.appendChild(this.Create(DATA));
    }
	BX.addCustomEvent(obTM.PARENT, 'onTimeManDataRecieved', BX.delegate(this.Create, this));
    var query_data = {
        'method': 'POST',
        'dataType': 'json',
        'timeout': 90,
        'url': '/bitrix/tools/oauth/socserv.ajax.php?action=getuserdata&site_id=' + SITE_ID + '&sessid=' + BX.bitrix_sessid(),
        'onsuccess': BX.delegate(function(data) {
            window.SOCSERV_DATA = data;
            BX.SocservTimeman.prototype.setValue(data, false);

        }),
        'onfailure': BX.delegate(function(data) {
            BX.SocservTimeman.prototype.closeWnd();
        })
    };

    return BX.ajax(query_data);
};

BX.SocservTimeman.prototype.Create = function(DATA)
{
    window.DATA = DATA;
    window.SOC_ARRAY = null;
	if (!this.DIV)
	{
		this.DIV = BX.create('DIV', {
            props: {className: 'bx-taimen-socserv-div'},
            children: [
                BX.create('SPAN', {
                    html: '<input  type="checkbox" value="Y" id="ss-send-to-socserv"><label for="ss-send-to-socserv">'+BX.message('JS_CORE_SS_SEND_TO_SOCSERV')+'</label>',
                    events: {
                        click: BX.proxy(this.saveCheckBox, this)
                    }

                }),
                BX.create('SPAN', {
                    html: '<a class="ss-socserv-setup-link" href="javascript:void(0)">'+BX.message('JS_CORE_SS_EVENT_SETUP')+'</a>',
                    events: {
                        click: BX.proxy(this.showWnd, this)
                    }
                })
            ]
		});
	}
	else
	{

	}

	return this.DIV;
};

BX.addCustomEvent('onTimeManWindowBuild', function(){
	var obST = new BX.SocservTimeman();
	obST.Init.apply(obST, arguments);
});
    BX.SocservTimeman.prototype.saveCheckBox = function()
    {
        myDataObj = new Object();
        myDataObj.ENABLED = "N";
        if(chkbxSendMyActivity = document.getElementById("ss-send-to-socserv"))
            if(chkbxSendMyActivity.checked == true)
                myDataObj.ENABLED = "Y";
        BX.SocservTimeman_query(myDataObj);
    }

    BX.SocservTimeman.prototype.closeWnd = function(e)
    {
        if(window.myPopup)
            window.myPopup.close();
        else if(this.popup)
            this.popup.close();
        return (e || window.event) ? BX.PreventDefault(e) : true;
    }

    BX.SocservTimeman.prototype.showWnd = function()
    {
        this.popup_id = 'ss-popup-send-message';
        var defaultMessageStart = BX.message('JS_CORE_SS_WORKDAY_START');
        var defaultMessageEnd = BX.message('JS_CORE_SS_WORKDAY_END');
        if(document.getElementById("ss-textarea-message-start") != null)
            defaultMessageStart = document.getElementById("ss-textarea-message-start").value;
        if(document.getElementById("ss-textarea-message-end") != null)
            defaultMessageEnd = document.getElementById("ss-textarea-message-end").value;

        if(this.popup)
        {
            this.popup.show();
            return;
        }
        var userAccounts = '';
        this.popup_buttons = this.popup_buttons || [
            new BX.PopupWindowButton({
                text : BX.message('JS_CORE_SS_EVENT_SEND'),
                className : "popup-window-button-accept",
                events : {click : BX.proxy(this.saveValue, this)}
            })
        ];
        for(var key in  window.SOCSERV_DATA["SOCSERVARRAYALL"]) {
            userAccounts = userAccounts +
                '<tr><td class="bx-ss-soc-serv"><input type="checkbox" id="provider_id_'+key+
                '" value="'+key+
                '"><i style="cursor: default;" class="bx-ss-icon '+
                window.SOCSERV_DATA["SOCSERVARRAYALL"][key].toLowerCase()+'"></i>'+ window.SOCSERV_DATA["SOCSERVARRAYALL"][key]+
                '</td></tr>';
        }

        this.popup = new BX.PopupWindow(this.popup_id, this.DIV,  {
            draggable: false,
            closeIcon:true,
            autoHide: true,
            offsetLeft:50,
            offsetTop:-35,
            zIndex:1000,
            closeByEsc: true,
            bindOptions: {forceBindPosition: true},
            content:
                '<div class="bx-tm-popup-clock-wnd-report"><div class="bx-tm-popup-clock-wnd-subtitle">'+BX.message('JS_CORE_SS_SEND_TO_SOCSERV')+'</div>' +
                    '<input type="checkbox" class="checkbox-class" value="Y" id="ss-day-start-checkbox">' +
                    '<label for="ss-day-start-checkbox">' + BX.message('JS_CORE_SS_SEND_TO_START') + '</label>' +
                    '<span class="bx-spacer-vert"></span><br>' +
                    '<textarea class="ss-text-for-message" id="ss-textarea-message-start">'+defaultMessageStart+'</textarea><br>' +
                    '<span class="bx-spacer-vert25"></span>' +
                    '<input type="checkbox" class="checkbox-class" value="Y" id="ss-day-end-checkbox">' +
                    '<label for="ss-day-end-checkbox">' + BX.message('JS_CORE_SS_SEND_TO_END') + '</label>' +
                    '<span class="bx-spacer-vert"></span><br>' +
                    '<textarea class="ss-text-for-message" id="ss-textarea-message-end">'+defaultMessageEnd+'</textarea><br>' +
                    '</div>' +
                    '<span class="bx-spacer-vert"></span>' +
                    '<div class="bx-auth-serv-icons"><table>'
                    +userAccounts+'</table></div>'
        });

        this.popup.setButtons(this.popup_buttons);
        this.popup.show();
        window.myPopup = this.popup;
        this.setValue(window.SOCSERV_DATA, true);
    }

    BX.SocservTimeman.prototype.saveValue = function(e)
    {
        var startSend = document.getElementById("ss-day-start-checkbox");
        var endSend = document.getElementById("ss-day-end-checkbox");
        myDataObj = new Object();
        var socServArray = [];
        myDataObj.STARTTEXT = document.getElementById("ss-textarea-message-start").value;
        myDataObj.ENDTEXT = document.getElementById("ss-textarea-message-end").value;
        for(var key in  window.SOCSERV_DATA["SOCSERVARRAYALL"]) {
            checkBox = document.getElementById("provider_id_"+key);
            if(checkBox.checked == true)
            {
                socServArray[key] = (window.SOCSERV_DATA["SOCSERVARRAYALL"][key]);
            }
        }
        myDataObj.SOCSERVARRAY = socServArray;
        if(startSend.checked == true)
            myDataObj.STARTSEND = "Y";
        if(endSend.checked == true)
            myDataObj.ENDSEND = "Y";

        BX.SocservTimeman_query(myDataObj);
    }

    BX.SocservTimeman.prototype.setValue = function(data, check)
    {
        var sendToSocServ = document.getElementById("ss-send-to-socserv");
        var TASKS = DATA.TASKS.length;
        var EVENTS = DATA.EVENTS.length;
        if(startSend = document.getElementById("ss-day-start-checkbox"))
            if(data.STARTSEND == 'Y')
                startSend.checked = true;
        if(endSend = document.getElementById("ss-day-end-checkbox"))
            if(data.ENDSEND == 'Y')
                endSend.checked = true;
        if(startText = document.getElementById("ss-textarea-message-start"))
            startText.value = data.STARTTEXT;
        if(endText = document.getElementById("ss-textarea-message-end"))
        {
            firstReplace = data.ENDTEXT.replace("#task#", TASKS);
            endText.value = firstReplace.replace("#event#", EVENTS);
        }
        if(data.ENABLED == 'Y')
            sendToSocServ.checked = true;
        if(check === true)
        {
            for(var key in window.SOCSERV_DATA["SOCSERVARRAYALL"]) {
                checkBox = document.getElementById("provider_id_"+key);
                for(var key2 in window.SOCSERV_DATA["SOCSERVARRAY"]) {
                    if(window.SOCSERV_DATA["SOCSERVARRAYALL"][key] == window.SOCSERV_DATA["SOCSERVARRAY"][key2])
                        checkBox.checked = true;
                }
                if(data.SOCSERVARRAY == true)
                {
                    socServArray.push(window.SOCSERV_DATA["SOCSERVARRAYALL"][key]);
                }
            }
        }
    }

    BX.SocservTimeman_query = function(myDataObj)
    {
        var query_data = {
            'method': 'POST',
            'dataType': 'json',
            'timeout': 90,
            'url': '/bitrix/tools/oauth/socserv.ajax.php?action=saveuserdata&site_id=' + SITE_ID + '&sessid=' + BX.bitrix_sessid(),
            'data':  BX.ajax.prepareData(myDataObj),
            'onsuccess': BX.delegate(function(data) {
                BX.SocservTimeman.prototype.closeWnd(this);
            }),
            'onfailure': BX.delegate(function(data) {
                BX.SocservTimeman.prototype.closeWnd(this);
            })
        };
        return BX.ajax(query_data);
    }

})();