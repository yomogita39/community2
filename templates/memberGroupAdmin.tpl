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

				<h3 class="h3_list_m memberColor">{$userName}&nbsp;さん&nbsp;グループリスト({$html|@count})</h3>

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
								<img src="{$var.imagePath}" class="image_m" alt="{$var.groupName}画像">
								<div class="list_td_userName">{$var.groupName}</div>
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