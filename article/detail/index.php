<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
?>

<?
if (empty($_GET['URL'])) {
    $Article = new Article(intval($_GET['ARTICLE_ID']));
    $article_contant=$Article->getItem();




    $url = formUrl($article_contant[$_GET['ARTICLE_ID']]['ID'], $article_contant[$_GET['ARTICLE_ID']]['NAME']);

    header('HTTP/1.1 301 Moved Permanently');
    header("Location: /article/{$article_contant[$_GET['ARTICLE_ID']]['SECTION']['CODE']}/{$url}/");
    die();

}
?>

<?$APPLICATION->IncludeComponent("mytb:article", "", array(
    "ARTICLE_ID" => $_GET['ARTICLE_ID']
), false);?>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>