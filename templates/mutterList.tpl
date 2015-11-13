{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_home_left">

				<div class="frame_image">
					<img src="{$userImage}" class="image_l" alt="{$userName}画像">
					<div class="image_name">{$userName}</div>
				</div>

			</div>

			<div class="frame_groupHome">
				<form>
					<table class="table_mutterList_input">
						<tr>
							<td class="td_mutterList_input">
								<table class="table_mutterList_input2">
									<tr>
										<td class="balloon_input_mutterList">
											<textarea id="message" name="message" class="textarea_mutterList"></textarea>
											<div id="countSpan" class="right" style="margin-right:10px;">入力文字数：<span id="count"></span>/<span id="maxCount"></span></div>
											<span class="balloon_input_triangle_mutterList">&nbsp;</span>
											<div class="position_select_public">
												<span>公開範囲</span>
												<select id="public" name="public" class="select_public">
													<option value="0" selected="selected">全員に公開</option>
													<option value="1">友達まで公開</option>
													<option value="2">公開しない</option>
												</select>
											</div>
											<input type="button" id="mutterButton" value="投稿" class="button_s textColorRed position_button_mutter">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="right"></td>
						</tr>
					</table>
				</form>
				<div>&nbsp;</div>

{if $html|@count != 0}
				<h3>ひとり言リスト</h3>
				<p class="list_count_m">
					<span id="total"></span><span>件中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>件を表示</span>
				</p>

				<table class="table_mutterList2">
				<tbody id="itemContainer">
{foreach from=$html item=var name=mutter}
					<tr class="tr_mutterList2">
						<td class="td_mutterListImage2">
							<a href="./profile.php?userId={$var.userId}"><img src="{$var.userImage}" class="image_s" alt="{$var.userName}画像"></a>
						</td>
						<td class="td_mutterListMessage2">
							<div>{$var.time}&nbsp;<a href="./profile.php?userId={$var.userId}">{$var.userName}</a></div>
							<div>&nbsp;</div>
							<div>{$var.message}</div>
						</td>
					</tr>
{/foreach}
				<input type="hidden" id="total" name="total" value={$smarty.foreach.mutter.total}>
				</tbody>
				</table>

				<div class="list_count_m">
					<div class="holder"></div>
				</div>
{else}
				<div>&nbsp;</div>
{/if}
			</div>

		</div>
		<script src="js/jquery.stringCount.js"></script>
		<script src="js/friendMail.js"></script>
		
		<input type="hidden" id="current" name="current" value=10>
		<input type="hidden" id="size" name="size" value=10>
{include file=#footerTsalPath#}