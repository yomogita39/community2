{config_load file="common.conf"}

{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

<!--			<div class="frame_home_left">

				<div class="frame_home_left_bottom">
					<h3>グループ作成</h3>
				</div>

				<div class="position_imageArrow">
					<img src="./templates/image/arrow.png" alt="矢印画像">

					<form action="./createGroup.php">
						<input type="submit" value="グループ作成" class="button_big">
					</form>
				</div>
			</div>
-->

			<div class="frame_friendList">
				<div style="margin-bottom:15px;margin-left:10px;"><a href="./createGroup.php">グループ新規作成</a></div>
				<h3 class="h3_list_m">グループリスト({$html|@count})</h3>

				<p class="list_count_m">
					<span id="total"></span><span>件中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>件を表示</span>
				</p>

				<table class="list_table_m">
				<tbody id="itemContainer">
					
{foreach from=$html item=var name=option}
{if $smarty.foreach.option.iteration % 5 == 1}
					<tr>
{/if}
						<td class="list_td_m">
							<a href="./timeLine.php?groupId={$var.groupId}" class="position_count">
							<img src="{$var.imagePath}" alt="{$var.groupName}画像" class="image_m">
{if 1 <= $var.groupUnread && $var.groupUnread <= 9}
							<span class="unread_count1">{$var.groupUnread}</span>
{elseif 10 <= $var.groupUnread && $var.groupUnread <= 99}
							<span class="unread_count2">{$var.groupUnread}</span>
{elseif 100 <= $var.groupUnread && $var.groupUnread <= 999}
							<span class="unread_count3">{$var.groupUnread}</span>
{/if}
							</a>
							<div class="list_td_userName"><a href="./timeLine.php?groupId={$var.groupId}">{$var.groupName}({$var.count})</a></div>
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

				<div class="list_count_m">
					<div class="holder"></div>
				</div>

			</div>

		</div>

		<input type="hidden" id="total" name="total" value={$smarty.foreach.option.total}>
		<input type="hidden" id="current" name="current" value=2>
		<input type="hidden" id="size" name="size" value=10>

{include file=#footerTsalPath#}