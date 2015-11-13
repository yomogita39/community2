{config_load file="common.conf"}

{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_home_left">

				<div class="frame_image">
					<img src="{$groupImage}" class="image_l" alt="{$groupName}画像">
					<div id="imageName" class="image_name">{$groupName}</div>
					<div class="image_name">
{if $flg == 1}
						<a href="#" onclick="return false" id="groupEdit">編集</a>&nbsp;
{/if}
						<a href="./timeLine.php?groupId={$groupId}" id="groupTimeLine">入室</a>
					</div>
				</div>
				
				<div id="dialog" title="グループ編集" style="display:none">
					<form enctype="multipart/form-data">
						<table class="table_createGroup">
							<tr>
								<td class="td_createGroup_label">グループ名</td>
								<td class="td_createGroup_value">
									<input type="text" name="groupName" id="groupName" class="input_groupInfo" maxlength="20">
									<input type="text" name="dummy" style="display:none;">
								</td>
							</tr>

							<tr>
								<td class="td_createGroup_label">グループ画像</td>
								<td class="td_createGroup_value">
									<div id="preview">2Mバイト以内のJEPG、PNG、GIF形式</div>
									<input type="file" id="file" name="file" class="input_groupInfo">
								</td>
							</tr>
						</table>
						
					</form>
				</div>
				

<!--				<div class="position_imageArrow">
					<img src="./templates/image/arrow.png" alt="矢印画像">

					<form action="./timeLine.php" method="get">
						<input type="hidden" name="groupId" value={$groupId}>
						<input type="submit" value="入室" class="button_big">
					</form>
				</div>
				-->
			</div>

			<div class="frame_groupHome">

				<h3 class="h3_list_m">メンバーリスト({$html|@count})</h3>

				<table class="list_couont_table_m">
					<tr>
						<td>
							<form action="./groupInvitation.php">
								<input type="submit" value="友達を招待する" class="button_long">
							</form>
						</td>
						<td class="right"><span id="total"></span><span>人中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>人を表示</span></td>
					</tr>
				</table>
	                                
				<table class="list_table_m">
				<tbody id="itemContainer">
{foreach from=$html item=var name=option}
{if $smarty.foreach.option.iteration % 5 == 1}
					<tr>
{/if}
						<td class="list_td_m">
							<a href="./profile.php?userId={$var.userId}"><img src="{$var.userImage}" alt="{$var.userName}画像" class="image_m"></a>
							<div class="list_td_userName"><a href="./profile.php?userId={$var.userId}">{$var.userName}</a></div>
						</td>

{if $smarty.foreach.option.last}
{section name=count start=0 loop=5}
{if $smarty.foreach.option.total % 5 != 0 && $smarty.section.count.index < (5-($smarty.foreach.option.total % 5))}
						<td class="list_td_m">&nbsp;</td>
{/if}
{/section}
{/if}
{if ($smarty.foreach.option.iteration is div by 5) || $smarty.foreach.option.last}
					</tr>
{/if}
{/foreach}
				</tbody>
				</table>
				<input type="hidden" id="total" name="total" value={$smarty.foreach.option.total}>

				<div class="list_count_m">
					<div class="holder"></div>
				</div>

			</div>

		</div>

		<input type="hidden" id="current" name="current" value=2>
		<input type="hidden" id="size" name="size" value=10>
		<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/start/jquery-ui.css" rel="stylesheet">
		<script language="javascript" type="text/javascript" src="js/editGroup.js"></script>
		<script language="javascript" type="text/javascript" src="js/jquery.upload-1.0.2.js"></script>
{include file=#footerTsalPath#}