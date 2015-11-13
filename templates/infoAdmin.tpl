{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_settings">

				<div class="frame_adminMenu">

					<h3>管理項目</h3>

					<ul class="frame_adminMenu">
						<li>インフォメーション</li>
						<li><a href="./userAdmin.php">ユーザ</a></li>
						<li><a href="./regUserAdmin.php">登録承認</a></li>
					</ul>

				</div>

				<div class="frame_infoAdminValue">
					<h3>インフォメーション登録</h3>

					<div class="input_userAdmin_caption">
						<div>インフォメーションに表示する内容を入力して下さい。</div>
						<form>
							<textarea name="newMessage" id="info" class="textarea_infoAdmin"></textarea>
							<input id="new" type="button" value="登録" class="button_s textColorRed">
						</form>
					</div>
					<div class="input_userAdmin_caption" id="dialog" title="インフォメーション編集" style="display:none">
						<div>編集完了後、変更ボタンを押してください</div>
						<form id="dForm">
							<textarea id="dInfo" name="editMessage" class="textarea_infoAdmin"></textarea>
							<input type="hidden" id="dId" name="infoId" value="">
						</form>
					</div>

					<div>&nbsp;</div>
					<h3>インフォメーションリスト</h3>

					<div>
						<div class="input_userAdmin_caption right"><span id="total"></span><span>件中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>件を表示</span></div>

{foreach from=$html item=var name=option}
{if $smarty.foreach.option.first}
						<table class="table_infoAdminList">
							<tr>
								<th class="th_infoAdminList_no">NO</th>
								<th class="th_infoAdminList_userName">登録者</th>
								<th class="th_infoAdminList_message">登録内容</th>
								<th class="th_infoAdminList_date">登録日</th>
								<th class="th_infoAdminList_action"></th>
							</tr>
							<tbody id="infoList">
{/if}
							<tr>
								<td colspan="5">&nbsp;</td>
							</tr>
							<tr id="{$var.infoId}">
								<td class="td_infoAdminList_no">{$smarty.foreach.option.iteration}</td>
								<td class="td_infoAdminList_userName">{$var.userName}</td>
								<td class="td_infoAdminList_message">{$var.message}</td>
								<td class="td_infoAdminList_date">{$var.regTime}</td>
								<td class="td_infoAdminList_action">
									<form>
										<input type="hidden" name="infoId" value="{$var.infoId}">
										<div><input type="button" value="編集" class="button_s edit"></div>
										<div><input type="button" value="削除" class="button_s textColorRed del"></div>
									</form>
								</td>
							</tr>
{if $smarty.foreach.option.last}
						<input type="hidden" id="total" name="total" value="{$total}">
						</tbody>
						</table>
{/if}
{/foreach}
					</div>

				<div class="right holder">次へ</div>

				</div>
			</div>
		</div>

		<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/start/jquery-ui.css" rel="stylesheet">
		<script src="js/infoAdmin.js" type="text/javascript" language="javascript"></script>
		<input type="hidden" id="current" name="current" value=10>
		<input type="hidden" id="size" name="size" value=10>
{include file=#footerTsalPath#}