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
						"mytb:club.left.block",
                        "",
						Array(
                            "CLUB_ID" => intval($_GET["ID"]),
                              ));?>
                                <?else:?>

                        <?$APPLICATION->IncludeFile(
                            SITE_DIR."include/vk.php",
                            Array(),
                            Array("MODE"=>"html")
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