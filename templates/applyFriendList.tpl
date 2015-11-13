{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_friendList">

				<h3 class="h3_list_l">友達申請リスト({$html|@count})</h3>

				<p class="list_count_l">
					<span id="total"></span><span>人中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>人を表示</span>
				</p>
	                                
				<table class="list_table_l" id="itemContainer">
					
{foreach from=$html item=var name=option}
{if $smarty.foreach.option.iteration % 5 == 1}
					<tr>
{/if}
						<td class="list_td_l">
							<a href="./profile.php?userId={$var.friendId}">
								<img src="{$var.friendImage}" alt="{$var.friendName}画像" class="image_m">
							</a>
							<div class="image_userName"><a href="./profile.php?userId={$var.friendId}">{$var.friendName}({$var.friendCnt})</a></div>
						</td>

{if $smarty.foreach.option.last}
{section name=count start=0 loop=5}
{if $smarty.foreach.option.total % 5 != 0 && $smarty.section.count.index < (5-($smarty.foreach.option.total % 5))}
						<td class="list_td_l">&nbsp;</td>
{/if}
{/section}
{/if}
{if ($smarty.foreach.option.iteration is div by 5) || $smarty.foreach.option.last}
					</tr>
					<tr>
{foreach from=$html item=value name=apply}
{if $smarty.foreach.option.iteration is div by 5}
{if ($smarty.foreach.option.iteration - 5) <= $smarty.foreach.apply.iteration && $smarty.foreach.apply.iteration <= $smarty.foreach.option.iteration}
						<td class="list_td_l">
							<form action="./applyFriendList.php" method="post">
								<input type="hidden" name="friendId" value={$value.friendId}>
								<input type="submit" value="承認" class="button_s textColorRed">
							</form>
						</td>
{/if}
{else}
{if (($smarty.foreach.option.iteration + 1) - ($smarty.foreach.option.iteration % 5)) <= $smarty.foreach.apply.iteration && $smarty.foreach.apply.iteration <= $smarty.foreach.option.iteration}
						<td class="list_td_l">
							<form action="./applyFriendList.php" method="post">
								<input type="hidden" name="friendId" value={$value.friendId}>
								<input type="submit" value="承認" class="button_s textColorRed">
							</form>
						</td>
{/if}
{/if}
{/foreach}
{if $smarty.foreach.option.last}
{section name=count start=0 loop=5}
{if $smarty.foreach.option.total % 5 != 0 && $smarty.section.count.index < (5-($smarty.foreach.option.total % 5))}
						<td class="list_td_l">&nbsp;</td>
{/if}
{/section}
{/if}
					<tr>
{/if}
{/foreach}
				</table>
				<input type="hidden" id="total" name="total" value={$smarty.foreach.option.total}>

				<div class="list_count_l">
					<div class="holder"></div>
				</div>

			</div>

		</div>
		<input type="hidden" id="current" name="current" value=2>
		<input type="hidden" id="size" name="size" value=10>
{include file=#footerTsalPath#}