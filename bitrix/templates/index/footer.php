<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div>
    <div class="home_block_news">
        <span class="block_title">Акции</span>
    </div>
    <div class="home_block_news">
        <span class="block_title">События</span>
    </div>
    <div class="home_block_news">
        <span class="block_title">Новости</span>
    </div>
    <div class="clear_both"></div>
</div>

<div>
    <div class="home_seo_text">
        <h3>MyTB.ru — акции, события, новости.</h3>
    </div>

</div>

<div id="space-for-footer"></div>

</div>

<?$APPLICATION->IncludeFile(
    SITE_DIR . "include/footer.php",
    Array(),
    Array("MODE" => "html")
);?>

</body>
</html>