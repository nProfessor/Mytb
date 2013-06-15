<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 12.06.13 16:17
 * File name: Article.php
 */
class Article
{
    private $articleID = array();

    function __construct($id=array())
    {
        if (!is_array($id)) {
            $this->articleID = array(intval($id));
        } else {
            foreach ($id as $articleID) {
                $this->articleID[] = intval($articleID);
            }
        }
    }

    function getItem()
    {
           if (!count($this->articleID))
            return false;

        $ob = CIBlockElement::GetList(Array(), array("ID" => $this->articleID,"IBLOCK_ID" => IB_ARTICLE), FALSE, FALSE, array());


        $result=array();
        while($row=$ob->Fetch()){
                 $db_list = CIBlockSection::GetList(Array(),  Array('IBLOCK_ID'=>IB_ARTICLE,"ID"=>$row['IBLOCK_SECTION_ID']), true);
            $row['SECTION']=$db_list->Fetch();
            $result[$row["ID"]]=$row;
        }
        return $result;
    }

    function getList()
    {

        $ob = CIBlockElement::GetList(Array(), array("IBLOCK_ID" => IB_ARTICLE), FALSE, FALSE, array());
        $result=array();
        $section=array();

        $db_list = CIBlockSection::GetList(Array(),  Array('IBLOCK_ID'=>IB_ARTICLE), true);
        while($row=$db_list->Fetch()){
            $section[$row["ID"]]=$row;
        }


        while($row=$ob->Fetch()){
            $row['SECTION']=$section[$row['IBLOCK_SECTION_ID']];
            $result[$row["ID"]]=$row;
        }
        return $result;
    }




}
