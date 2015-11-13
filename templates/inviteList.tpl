{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_friendList">

				<h3 class="h3_list_l">招待リスト({$html|@count})</h3>

				<p class="list_count_l">
					<span id="total"></span><span>件中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>件を表示</span>
				</p>
	                                
				<table class="list_table_l">
					<tbody id="itemContainer">
					
{foreach from=$html item=var name=option}
{if $smarty.foreach.option.iteration % 5 == 1}
						<tr>
{/if}
							<td class="list_td_l">
								<a href="./inviteGroupHome.php?groupId={$var.groupId}">
									<img src="{$var.imagePath}" alt="{$var.groupName}画像" class="image_m">
								</a>
								<p><a href="./inviteGroupHome.php?groupId={$var.groupId}">{$var.groupName}({$var.count})</a></p>
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
{/if}
{/foreach}
					<tbody>
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