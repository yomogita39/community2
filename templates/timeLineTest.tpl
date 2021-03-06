{config_load file="common.conf"}

{include file=#headerScriptPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page_timeLine">

			<div class="frame_timeLine_input">

				<h3 class="h3_timeLine">{$groupName}</h3>

				<table class="table_timeLine_input">
					<tr>

						<td class="td_timeLine_input">
							<table class="table_timeLine_input2">
								<tr>
									<td class="balloon_input">
										<form id="msgform">
											<textarea input type="text" id="msginput" name="msg" class="textarea_timeLine">メッセージを入力してください</textarea>
											<div id="fallback">
												<br>
												<div id="preview">画像(2Mバイト以内のJPEG、PNG、GIF形式。)</div>

											</div>
											
											<div id="boxs" class="dropzone-custom">ここにドロップ</div>
											<div id="preview_area" class="dropzone-custom" style="display:none"></div>
											
											<br>
											<p>
												<input type="hidden" id="gid" name="gid" value="{$gid}">
												<input type="hidden" id="lastid" name="lastid" value="{$lastid}">
												<input type="hidden" id="firstid" name="firstid" value="{$firstid}">
												<input type="button" id="msgsend" name="msgsend" value="投稿" class="button_s position_button_contribute">
											</p>
										</form>
										<span class="balloon_input_triangle">&nbsp;</span>
										
									</td>
								</tr>
							</table>
						</td>

					</tr>
				</table>

				<div>&nbsp;</div>
				<div class="timeLine_userImage">
					<img src="{$userImage}" class="image_l" alt="{$userName}画像">
							<div class="image_name">{$userName}</div>
				</div>

			</div>

		</div>

		<div class="frame_timeLine">

			<div class="frame_tree">&nbsp;</div>

			<div class="position_timeLine">

				<div class="frame_timeLine_message" id="timeline">
					<div id="cline">
						{$html.html}
					</div>
					<div>&nbsp;</div>
				</div>

				<div class="position_button_timeLine">
					<form action="./groupHome.php" method="get">
						<input type="hidden" name="groupId" value="{$gid}">
						<input type="submit" value="メンバー" class="button_m">
					</form>

					<form id="msgform">
						<input type="button" id="msgupdate" name="msgupdate" value="更新" class="button_s position_button_update">
					</form>
				</div>

			</div>

		</div>
		

{include file=#scriptTagPath#}

{include file=#footerTsalPath#}