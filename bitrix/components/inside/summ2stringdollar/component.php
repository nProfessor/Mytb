<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!IsModuleInstalled("inside.summ2string")||!(CModule::IncludeModule("inside.summ2string")))
{
	ShowError(GetMessage("summ2string_MODULE_NOT_INSTALLED"));
	return;
}

$_1_2[1]=GetMessage("summ2string_1_2_1");
$_1_2[2]=GetMessage("summ2string_1_2_2");
$_1_19[1]=GetMessage("summ2string_1_19_1");
$_1_19[2]=GetMessage("summ2string_1_19_2");
$_1_19[3]=GetMessage("summ2string_1_19_3");
$_1_19[4]=GetMessage("summ2string_1_19_4");
$_1_19[5]=GetMessage("summ2string_1_19_5");
$_1_19[6]=GetMessage("summ2string_1_19_6");
$_1_19[7]=GetMessage("summ2string_1_19_7");
$_1_19[8]=GetMessage("summ2string_1_19_8");
$_1_19[9]=GetMessage("summ2string_1_19_9");
$_1_19[10]=GetMessage("summ2string_1_19_10");
$_1_19[11]=GetMessage("summ2string_1_19_11");
$_1_19[12]=GetMessage("summ2string_1_19_12");
$_1_19[13]=GetMessage("summ2string_1_19_13");
$_1_19[14]=GetMessage("summ2string_1_19_14");
$_1_19[15]=GetMessage("summ2string_1_19_15");
$_1_19[16]=GetMessage("summ2string_1_19_16");
$_1_19[17]=GetMessage("summ2string_1_19_17");
$_1_19[18]=GetMessage("summ2string_1_19_18");
$_1_19[19]=GetMessage("summ2string_1_19_19");
$des[2]=GetMessage("summ2string_des_2");
$des[3]=GetMessage("summ2string_des_3");
$des[4]=GetMessage("summ2string_des_4");
$des[5]=GetMessage("summ2string_des_5");
$des[6]=GetMessage("summ2string_des_6");
$des[7]=GetMessage("summ2string_des_7");
$des[8]=GetMessage("summ2string_des_8");
$des[9]=GetMessage("summ2string_des_9");
$hang[1]=GetMessage("summ2string_hang_1");
$hang[2]=GetMessage("summ2string_hang_2");
$hang[3]=GetMessage("summ2string_hang_3");
$hang[4]=GetMessage("summ2string_hang_4");
$hang[5]=GetMessage("summ2string_hang_5");
$hang[6]=GetMessage("summ2string_hang_6");
$hang[7]=GetMessage("summ2string_hang_7");
$hang[8]=GetMessage("summ2string_hang_8");
$hang[9]=GetMessage("summ2string_hang_9");
$namerub[1]=GetMessage("summ2string_namerub_1");
$namerub[2]=GetMessage("summ2string_namerub_2");
$namerub[3]=GetMessage("summ2string_namerub_3");
$nametho[1]=GetMessage("summ2string_nametho_1");
$nametho[2]=GetMessage("summ2string_nametho_2");
$nametho[3]=GetMessage("summ2string_nametho_3");
$namemil[1]=GetMessage("summ2string_namemil_1");
$namemil[2]=GetMessage("summ2string_namemil_2");
$namemil[3]=GetMessage("summ2string_namemil_3");
$namemrd[1]=GetMessage("summ2string_namemrd_1");
$namemrd[2]=GetMessage("summ2string_namemrd_2");
$namemrd[3]=GetMessage("summ2string_namemrd_3");
$kopeek[1]=GetMessage("summ2string_kopeek_1");
$kopeek[2]=GetMessage("summ2string_kopeek_2");
$kopeek[3]=GetMessage("summ2string_kopeek_3");

$arParams["SUMM_VALUE"]=str_replace(",",".",$arParams["SUMM_VALUE"]);
if(is_numeric($arParams["SUMM_VALUE"])){
$obj_summ=new CInsideSummStringFunctional($arParams["SUMM_VALUE"], true,$_1_2, $_1_19, $des, $hang, $namerub, $nametho, $namemil, $namemrd, $kopeek);
$arResult["SUMM_FOR_PRINTING"]=$obj_summ->summ2string();

if($arParams["SUMM_CAPITALIZE"]=="Y"){
    $arResult["SUMM_FOR_PRINTING"]=strtoupper(substr($arResult["SUMM_FOR_PRINTING"],0,1)).substr($arResult["SUMM_FOR_PRINTING"],1);
}

}
else $arResult["SUMM_FOR_PRINTING"]=GetMessage("summ2string_NO_SUMM");
$this->IncludeComponentTemplate();
?>