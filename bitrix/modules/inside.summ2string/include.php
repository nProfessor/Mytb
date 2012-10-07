<?
Class CInsideSummString
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			//"parent_menu" => "global_menu_services",
			"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => $MODULE_ID,
			"title" => '',
			"url" => "partner_modules.php?module=".$MODULE_ID,
			"icon" => "",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);

		if (file_exists($path = dirname(__FILE__).'/admin'))
		{
			if ($dir = opendir($path))
			{
				$arFiles = array();

				while($item = readdir($dir))
				{
					if (in_array($item,array('.','..','menu.php')))
						continue;

					$arFiles[] = $item;
				}

				sort($arFiles);

				foreach($arFiles as $item)
					$aMenu['items'][] = array(
						'text' => $item,
						'url' => $MODULE_ID.'_'.$item,
						'module_id' => $MODULE_ID,
						"title" => "",
					);
			}
		}
		$aModuleMenu[] = $aMenu;
	}
}

Class CInsideSummStringFunctional{
    var $L, $first_upper = false,$_1_2, $_1_19, $des, $hang, $namerub, $nametho, $namemil, $namemrd, $kopeek;
function __construct($L, $first_upper = false,$_1_2, $_1_19, $des, $hang, $namerub, $nametho, $namemil, $namemrd, $kopeek)
    {
if(!strpos($L,".")){$L=$L.".00";}
$this->L=$L;
$this->first_upper=$first_upper;
$this->_1_2=$_1_2;
$this->_1_19=$_1_19;
$this->des=$des;
$this->hang=$hang;
$this->namerub=$namerub;
$this->nametho= $nametho;
$this->namemil=$namemil;
$this->namemrd=$namemrd;
$this->kopeek=$kopeek;
    }

function __destruct()
    {
unset($this->L);
unset($this->first_upper);
unset($this->_1_2);
unset($this->_1_19);
unset($this->des);
unset($this->hang);
unset($this->namerub);
unset($this->nametho);
unset($this->namemil);
unset($this->namemrd);
unset($this->kopeek);
    }
function semantic($i,$f)
{
$words="";
$fl=0;

if($i >= 100)
{
$jkl = intval($i / 100);
$words.=$this->hang[$jkl];
$i%=100;
}

if($i >= 20)
{
$jkl = intval($i / 10);
$words.=$this->des[$jkl];
$i%=10;
$fl=1;
}

switch($i)
{
case 1: $many=1; break;
case 2:
case 3:
case 4: $many=2; break;
default: $many=3; break;
}

if($i)
{
if($i < 3 && $f == 1)
$words.=$this->_1_2[$i];
else
$words.=$this->_1_19[$i];
}
    return array($words,$many);
}

function summ2string()
{
$s=" ";
$s1=" ";
//считаем количество копеек, т.е. дробной части числа
//$kop=intval(( $this->L*100 - intval($this->L)*100 ));
//$kop=intval(( $this->L*100 - intval($this->L)*100 ));
$this->L=str_replace(",",".",$this->L);
$kop=substr($this->L,strpos($this->L,".")+1,2);
    if(strlen($kop)<2)$kop.="0";
//отбрасываем дробную часть
$this->L=intval($this->L);

if($this->L>=1000000000)
{
$tmp_arrx=$this->semantic(intval($this->L / 1000000000),3);
$s1=$tmp_arrx[0];
$many=$tmp_arrx[1];
$s.=$s1.$this->namemrd[$many];
$this->L%=1000000000;
//если ровно сколько-то миллиардов, то хватит считать
if($this->L==0)
{
$s.=$this->namerub[3];
}
}

if($this->L >= 1000000)
{
$tmp_arrx=$this->semantic(intval($this->L / 1000000),2);
$s1=$tmp_arrx[0];
$many=$tmp_arrx[1];
$s.=$s1.$this->namemil[$many];
$this->L%=1000000;
//аналогично если ровно сколько-то миллионов, то хватит считать
if($this->L==0)
{
$s.=$this->namerub[3];
}
}

if($this->L >= 1000)
{
$tmp_arrx=$this->semantic(intval($this->L / 1000),1);
$s1=$tmp_arrx[0];
$many=$tmp_arrx[1];
$s.=$s1.$this->nametho[$many];
$this->L%=1000;
if($this->L==0)
{
$s.=$this->namerub[3];
}
}


if($this->L != 0)
{
$tmp_arrx=$this->semantic($this->L,0);
$s1=$tmp_arrx[0];
$many=$tmp_arrx[1];
$s.=$s1.$this->namerub[$many];
}

$tmp_arrx=$this->semantic($kop,1);
$s1=$tmp_arrx[0];
$many=$tmp_arrx[1];
$s .= $kop." ".$this->kopeek[$many];
return trim($s);
}
}
?>
