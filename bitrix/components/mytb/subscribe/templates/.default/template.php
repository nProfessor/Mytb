<div class="modal hide fade" id="modal_subs">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6>Подписка на акции в «<?=$clubInfo["NAME"]?>»</h6>
    </div>
    <div class="modal-body">

        Хотите узнавать о новых акциях в <b>«<?=$clubInfo["NAME"]?>»</b>?<br/> Можно получать уведомления по СМС, Email, или просматривать их в личном кабинете.
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="no_subs">Не сейчас</a>
        <a href="#" class="btn btn-primary"  id="subs_ok_modal" data-auth="<?=$USER->IsAuthorized()?"yes":"no";?>">Да, хочу</a>
    </div>
</div>

<?$url=formUrl($clubInfo["ID"],implode("-",$clubInfo["PROPERTY_TYPE_FACILITY_VALUE"])." ".$clubInfo['NAME']);?>
<input id="redirect" type="hidden" value="/club/<?=$url?>//stock/?subscribe=ok">
<? $APPLICATION->IncludeComponent("mytb:auth", "",  array("AUTH_URL"=>"/stock/?subscribe=ok&login=yes"),false); ?>

