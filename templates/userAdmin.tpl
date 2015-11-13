{config_load file="common.conf"}
{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_settings">

				<div class="frame_adminMenu">

					<h3>管理項目</h3>

					<ul class="frame_adminMenu">
						<li><a href="./infoAdmin.php">インフォメーション</a></li>
						<li>ユーザ</li>
						<li><a href="./regUserAdmin.php">登録承認</a></li>
					</ul>

				</div>

				<div class="frame_adminValue">
					<h3>絞り込み検索</h3>

					<div class="input_userAdmin_caption">メールアドレス又はユーザ名、ニックネームを入力して下さい。</div>
					<form action="./userAdmin.php" method="post" id="searchForm">
						<table class="table_search">
							<tr>
								<td class="td_searchValue">
									<input type="text" name="search" id="search" class="input_searchValue" value="" maxlength="50">
									<input type="text" name="dummy" style="display:none;">
								</td>

								<td><input type="button" value="検索" class="button_s" id="userSearch"></td>
								<td><input type="button" value="クリア" class="button_s" id="clear"></td>
							</tr>
						</table>
					</form>

					<div>&nbsp;</div>
					<h3>ユーザリスト</h3>

					<div>
						<table class="table_adminUserList">
							<tr>
								<td class="textWeightBold">該当人数：<span class="total">{$html|@count}</span>人</td>
{if $html|@count != 0}
								<td class="input_userAdmin_caption right"><span class="total"></span><span>件中&nbsp;</span><span id="min"></span><span>～</span><span id="max"></span><span>件を表示</span></td>
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
										<table class="table_adminUser">
											<tbody id="{$var.id}">
												<tr>
													<td rowspan="6" class="td_adminUserListImage">
														<img src="{$var.image}" class="image_m" alt="{$var.name}画像">
													</td>
													<td class="td_adminUserListLabel">名前</td>
													<td class="td_adminUserListMessage">{$var.name}</td>
													<td rowspan="6" class="center">
														<input type="button" value="変更" class="button_s textColorRed">
													</td>
												</tr>
												<tr>
													<td class="td_adminUserListLabel">ニックネーム</td>
													<td class="td_adminUserListMessage">{$var.nickname}</td>
												</tr>
												<tr>
													<td class="td_adminUserListLabel">メールアドレス</td>
													<td class="td_adminUserListMessage">{$var.mail}</td>
												</tr>
												<tr>
													<td class="td_adminUserListLabel">パスワード</td>
													<td class="td_adminUserListMessage">
														<input type="text" name="pass" class="input_adminUserListPass" maxlength="20">
														<input type="text" name="dummy" style="display:none;">
													</td>
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
												<tr>
													<td class="td_adminUserListLabel">管理者権限</td>
													<td class="td_adminUserListMessage">
														<select name="auth" class="select_userAuth">
{if $var.auth == 0}
															<option value="0" selected="selected">なし</option>
															<option value="1">あり</option>
{else}
															<option value="0">なし</option>
															<option value="1" selected="selected">あり</option>
{/if}
														</select>
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

					<div class="right holder">次へ</div>
{/if}

				</div>
			</div>
		</div>

		<script src="js/userAdmin.js" type="text/javascript" language="javascript"></script>
		<input type="hidden" id="current" name="current" value=10>
		<input type="hidden" id="size" name="size" value=10>

{include file=#footerTsalPath#}