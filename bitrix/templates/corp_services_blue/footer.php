<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
							</div>
						</div>
					</div>

				</div>

				<div id="sidebar">
					<div id="sidebar-inner">

				<!--		<div id="email"><nobr><?/*$APPLICATION->IncludeFile(
									SITE_DIR."include/email.php",
									Array(),
									Array("MODE"=>"html")
								);*/?></nobr></div>-->

						<div id="schedule"><div class="schedule">
						<?$APPLICATION->IncludeFile(
									SITE_DIR."include/shedule.php",
									Array(),
									Array("MODE"=>"html")
								);?>
						</div></div>






                        <?if(isset($_GET["CLUB"])&&$_GET["CLUB"]=="true"):?>
                        <?$APPLICATION->IncludeComponent(
						"mytb:club.list.stock",
                        "small",
						Array(
                            "CLUB_ID" => intval($_GET["ID"]),
                              ));?>
                                <?else:?>

                        <a href="/bitrix/rss.php" class="rss big">
                            <span class="label label-warning large">Новости</span>

                        </a>

                        <?$APPLICATION->IncludeComponent(
						"bitrix:news.line",
                            ".default",
						Array(
                            "AREA_FILE_SHOW" => "page",
                            "AREA_FILE_SUFFIX" => "inc",
                            "AREA_FILE_RECURSIVE" => "N",
                            "EDIT_MODE" => "html",
							"EDIT_TEMPLATE" => "page_inc.php"
                        )
                        );?>
                                <?endif;?>
					</div>
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