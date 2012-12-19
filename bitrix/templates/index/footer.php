<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


				</div>

				<div id="sidebar">

                        <?$APPLICATION->IncludeComponent("mytb:auth", "soc", array(
                            "START_FROM" => "1",
                            "PATH"       => "",
                            "SITE_ID"    => SITE_ID
                        ),
                        FALSE
                    );?>

                    <?$APPLICATION->IncludeComponent("mytb:baner.home", "akcia", array(
                        "START_FROM" => "1",
                        "PATH"       => "",
                        "SITE_ID"    => SITE_ID
                    ),
                    FALSE
                );?>

			</div>
<div>
    <div class="home_block_news">
        <span class="block_title">Акции</span>
    </div><div class="home_block_news">
    <span class="block_title">События</span>
    </div><div class="home_block_news">
    <span class="block_title">Новости</span>
    </div>
    <div class="clear_both"></div>
</div>

<div>
    <div class="home_seo_text">
        <h3>MyTB.ru — акции, события, новости, общение.</h3>
    </div>

</div>

			<div id="space-for-footer"></div>

		</div>


		<div id="footer">

			<div id="copyright">
			<?$APPLICATION->IncludeFile(
									SITE_DIR."include/copyright.php",
									Array(),
									Array("MODE"=>"html")
								);?>
			</div>

		</div>
</body>
</html>