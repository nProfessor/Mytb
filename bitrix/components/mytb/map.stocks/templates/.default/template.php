<script src="http://api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
<p class="text-info"><strong>Внимание!</strong> На карте отображены только те заведения, в которых на данный момент есть действующие акции.</p>
  <div class="map_filter pull-right">
    <?foreach($arResult["KIND_CLUB_LIST"] as $var):?>
      <label><input type="checkbox" class="type_club" value="<?=$var['ID']?>" checked="checked"><?=$var['VALUE']?></label>
    <?endforeach;?>
      <input type="button" class="button" value="Убрать" id="clear">
  </div>

<div id="map" style="width:850px; height:500px"></div>
