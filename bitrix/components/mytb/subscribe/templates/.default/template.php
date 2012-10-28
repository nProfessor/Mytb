<div class="modal hide fade" id="modal_subs">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6>Подписка на акции, события и новости клуба «<?=$clubInfo["NAME"]?>»</h6>
    </div>
    <div class="modal-body">
        <b>Хотите узнавать об акциях, событиях и новостях клуба «<?=$clubInfo["NAME"]?>»?</b>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="no_subs">Не сейчас</a>
        <a href="#" class="btn btn-primary"  id="subs_ok_modal" data-auth="<?=$USER->IsAuthorized()?"yes":"no";?>">Да, хочу</a>
    </div>
</div>


<input id="redirect" type="hidden" value="/club/<?=$clubInfo["ID"]?>/stock/?subscribe=ok">
<? $APPLICATION->IncludeComponent("mytb:auth", "",  array("AUTH_URL"=>"/stock/?subscribe=ok&login=yes"),false); ?>

