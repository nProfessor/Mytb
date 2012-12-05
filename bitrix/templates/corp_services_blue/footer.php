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

                        <!-- Put this script tag to the <head> of your page -->
                        <script type="text/javascript" src="http://userapi.com/js/api/openapi.js?71"></script>
                        <!-- Put this div tag to the place, where the Poll block will be -->
                        <div id="vk_poll"></div>
                            <br/>
                        <script type="text/javascript">
                            VK.Widgets.Poll("vk_poll", {width: 200}, "62422981_cdce58296edb28f3cc");
                        </script>

                        <script type="text/javascript" src="//vk.com/js/api/openapi.js?63"></script>

                        <!-- VK Widget -->
                        <div id="vk_groups"></div>
                        <script type="text/javascript">
                            VK.Widgets.Group("vk_groups", {mode: 0, width: "200", height: "290"}, 45144570);
                        </script>
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