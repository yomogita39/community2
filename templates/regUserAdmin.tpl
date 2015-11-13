{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_settings">

				<div class="frame_adminMenu">

					<h3>管理項目</h3>

					<ul class="frame_adminMenu">
						<li><a href="./infoAdmin.php">インフォメーション</a></li>
						<li><a href="./userAdmin.php">ユーザ</a></li>
						<li>登録承認</li>
					</ul>

				</div>

				<div class="frame_adminValue">
					<h3>承認対象リスト</h3>

					<div>
						<table class="table_adminUserList">
							<tr>
								<td class="textWeightBold">該当人数：{$html|@count}人</td>
{if $html|@count != 0}
								<td class="input_userAdmin_caption right">
									<span>{$html|@count}件中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>件を表示</span>
								</td>
{else}
								<td class="input_userAdmin_caption right">&nbsp;</td>
{/if}
							</tr>
						</table>
					</div>
					
{if $html|@count != 0}
					<table class="table_adminUserList">
						<tbody id="admin">
{foreach from=$html item=var name=option}
							<tr>
								<td>
									<form>
										<input type="hidden" name="userId" value="{$var.id}">
										<input type="hidden" name="mail" value="{$var.mail}">
										<table class="table_adminUser">
											<tbody id="{$var.id}">
												<tr>
													<td rowspan="3" class="td_adminUserListImage">
														<img src="{$var.image}" class="image_m" alt="{$var.name}画像">
													</td>
													<td class="td_adminUserListLabel">名前</td>
													<td class="td_adminUserListMessage">{$var.name}</td>
													<td rowspan="5" class="center">
														<input type="button" value="承認" class="button_s textColorRed">
													</td>
												</tr>
												<tr>
													<td class="td_adminUserListLabel">メールアドレス</td>
													<td class="td_adminUserListMessage">{$var.mail}</td>
												</tr>
												<tr>
													<td class="td_adminUserListLabel">区画番号</td>
													<td class="td_adminUserListMessage">
														<input type="text" name="divisionNo1" class="input_divisionNo" maxlength="3" value="{$var.no1}">
														<span>&ndash;</span>
														<input type="text" name="divisionNo2" class="input_divisionNo" maxlength="3" value="{$var.no2}">
														<span>&ndash;</span>
														<input type="text" name="divisionNo3" class="input_divisionNo" maxlength="3" value="{$var.no3}">
														<span>&ndash;</span>
														<input type="text" name="divisionNo4" class="input_divisionNo" maxlength="3" value="{$var.no4}">
													</td>
												</tr>
											</tbody>
										</table>
									</form>
								</td>
							</tr>
{if $smarty.foreach.option.last}
						</tbody>
					</table>
					<input type="hidden" id="total" name="total" value="{$total}">
{/if}
{/foreach}

					<div class="right holder" id="holder">次へ</div>
{else}
					<div>&nbsp;</div>
{/if}

					<h3>承認済みリスト(7日以内)</h3>

					<div>
						<table class="table_adminUserList">
							<tr>
								<td class="textWeightBold">該当人数：{$appHtml|@count}人</td>
{if $appHtml|@count != 0}
								<td class="input_userAdmin_caption right">
									<span>{$appHtml|@count}件中&nbsp;</span><span id="appMin"></span><span>～</span><span id="appMax"></span><span>件を表示</span>
								</td>
{else}
								<td class="input_userAdmin_caption right">&nbsp;</td>
{/if}
							</tr>
						</table>
					</div>
					
{if $appHtml|@count != 0}
					<table class="table_adminUserList">
						<tbody id="app">
{foreach from=$appHtml item=var name=app}
							<tr>
								<td>
									<table class="table_adminUser">
										<tbody id="{$var.id}">
											<tr>
												<td rowspan="3" class="td_adminUserListImage">
													<img src="{$var.image}" class="image_m" alt="{$var.name}画像">
{if $var.flg == 0}
													<span class="textColorRed">本登録未完了</span>
{else}
													<span class="textColorRed">本登録済み</span>
{/if}
												</td>
												<td class="td_adminUserListLabel">名前</td>
												<td class="td_adminUserListMessage">{$var.name}</td>
											</tr>
											<tr>
												<td class="td_adminUserListLabel">メールアドレス</td>
												<td class="td_adminUserListMessage">{$var.mail}</td>
											</tr>
											<tr>
												<td class="td_adminUserListLabel">区画番号</td>
												<td class="td_adminUserListMessage">{$var.no}</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
{if $smarty.foreach.app.last}
						</tbody>
					</table>
					<input type="hidden" id="appTotal" name="appTotal" value="{$appTotal}">
{/if}
{/foreach}

					<div class="right holder" id="appHolder">次へ</div>
{/if}
				</div>
			</div>
		</div>

		<script src="js/regUserAdmin.js" type="text/javascript" language="javascript"></script>
		<input type="hidden" id="current" name="current" value=10>
		<input type="hidden" id="size" name="size" value=10>

{include file=#footerTsalPath#}