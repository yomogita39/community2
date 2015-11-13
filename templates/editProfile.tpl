{config_load file="common.conf"}

{include file=#headerTsalPath# title='T-SAL農園&nbsp;SNS'}

		<div class="frame_page">

			<div class="frame_profile">

				<h3 class="profile">マイプロフィール&nbsp;編集</h3>

				<div class="input_editProfile_caption">
					<span>○プロフィールを入力して下さい。</span>
					<span class="textColorRed">&nbsp;(※)</span>
					<span>は必須入力になります。</span>
				</div>

				<div class="frame_editProfile">

					<div class="frame_image_profile">
						<img src="{$userImage}" class="image_l" alt="{$userName}画像">
						<div class="image_name">{$userName}</div>
					</div>

					<form id='editForm' enctype="multipart/form-data">

						<div class="frame_editProfileList">

							<table class="table_editProfile">
								<tr class="tr_editProfile">
									<td class="td_editProfile_label">画像</td>
									<td class="td_editProfile_label">
										<div id="preview">2Mバイト以内のJEPG、PNG、GIF形式</div>
										<input type="file" size="11" id="file" name="file" class="input_profileImage" enctype="multipart/form-data">
									</td>
								</tr>
								<tr class="tr_editProfile">
									<td class="td_editProfile_label">名前<span class="textColorRed">&nbsp;(※)</span></td>
									<td class="td_editProfile_label"><input type="text" name="realName" class="input_editProfile" id="realName" value="{$realName}" maxlength="20"></td>
								</tr>
								<tr class="tr_editProfile">
									<td class="td_editProfile_label">ニックネーム<span class="textColorRed">&nbsp;(※)</span></td>
									<td class="td_editProfile_label"><input type="text" name="userName" class="input_editProfile" id="userName" value="{$userName}" maxlength="20"></td>
								</tr>

								<tr class="tr_editProfile">
									<td class="td_editProfile_label">性別<span class="textColorRed">&nbsp;(※)</span></td>
									<td class="td_editProfile_label">
										<select name="gender" id="gender" class="select_gender">
											<option value="男性" {$man}>男性</option>
											<option value="女性" {$woman}>女性</option>
										</select>
									</td>
								</tr>

								<tr class="tr_editProfile">
									<td class="td_editProfile_label">誕生日<span class="textColorRed">&nbsp;(※)</span></td>
									<td class="td_editProfile_label">
										<select name="year" id="year" class="select_gender">
												{$year}
										</select>
										<select name="month" id="month" class="select_date">
											<option value="01" {$month1}>01</option>
											<option value="02" {$month2}>02</option>
											<option value="03" {$month3}>03</option>
											<option value="04" {$month4}>04</option>
											<option value="05" {$month5}>05</option>
											<option value="06" {$month6}>06</option>
											<option value="07" {$month7}>07</option>
											<option value="08" {$month8}>08</option>
											<option value="09" {$month9}>09</option>
											<option value="10" {$month10}>10</option>
											<option value="11" {$month11}>11</option>
											<option value="12" {$month12}>12</option>
										</select>
										<select name="day" id="day" class="select_date">
											<option value="01" {$day1}>01</option>
											<option value="02" {$day2}>02</option>
											<option value="03" {$day3}>03</option>
											<option value="04" {$day4}>04</option>
											<option value="05" {$day5}>05</option>
											<option value="06" {$day6}>06</option>
											<option value="07" {$day7}>07</option>
											<option value="08" {$day8}>08</option>
											<option value="09" {$day9}>09</option>
											<option value="10" {$day10}>10</option>
											<option value="11" {$day11}>11</option>
											<option value="12" {$day12}>12</option>
											<option value="13" {$day13}>13</option>
											<option value="14" {$day14}>14</option>
											<option value="15" {$day15}>15</option>
											<option value="16" {$day16}>16</option>
											<option value="17" {$day17}>17</option>
											<option value="18" {$day18}>18</option>
											<option value="19" {$day19}>19</option>
											<option value="20" {$day20}>20</option>
											<option value="21" {$day21}>21</option>
											<option value="22" {$day22}>22</option>
											<option value="23" {$day23}>23</option>
											<option value="24" {$day24}>24</option>
											<option value="25" {$day25}>25</option>
											<option value="26" {$day26}>26</option>
											<option value="27" {$day27}>27</option>
											<option value="28" {$day28}>28</option>
											<option value="29" {$day29}>29</option>
											<option value="30" {$day30}>30</option>
											<option value="31" {$day31}>31</option>
										</select>
									</td>
								</tr>

								<tr class="tr_editProfile">
									<td class="td_editProfile_label">都道府県<span class="textColorRed">&nbsp;(※)</span></td>
									<td class="td_editProfile_label">
										<select name="place" id="place" class="select_gender">
											<option value="北海道" {$hokkaido}>北海道</option>
											<option value="青森県" {$aomori}>青森県</option>
											<option value="岩手県" {$iwate}>岩手県</option>
											<option value="宮城県" {$miyagi}>宮城県</option>
											<option value="秋田県" {$akita}>秋田県</option>
											<option value="山形県" {$yamagata}>山形県</option>
											<option value="福島県" {$fukushima}>福島県</option>
											<option value="茨城県" {$ibaraki}>茨城県</option>
											<option value="栃木県" {$totigi}>栃木県</option>
											<option value="群馬県" {$gunma}>群馬県</option>
											<option value="埼玉県" {$saitama}>埼玉県</option>
											<option value="千葉県" {$chiba}>千葉県</option>
											<option value="東京都" {$toukyo}>東京都</option>
											<option value="神奈川県" {$kanagawa}>神奈川県</option>
											<option value="新潟県" {$niigata}>新潟県</option>
											<option value="富山県" {$toyama}>富山県</option>
											<option value="石川県" {$ishikawa}>石川県</option>
											<option value="福井県" {$fukui}>福井県</option>
											<option value="山梨県" {$yamanashi}>山梨県</option>
											<option value="長野県" {$nagano}>長野県</option>
											<option value="岐阜県" {$gifu}>岐阜県</option>
											<option value="静岡県" {$shizuoka}>静岡県</option>
											<option value="愛知県" {$aichi}>愛知県</option>
											<option value="三重県" {$mie}>三重県</option>
											<option value="滋賀県" {$shiga}>滋賀県</option>
											<option value="京都府" {$kyouto}>京都府</option>
											<option value="大阪府" {$osaka}>大阪府</option>
											<option value="兵庫県" {$hyougo}>兵庫県</option>
											<option value="奈良県" {$nara}>奈良県</option>
											<option value="和歌山県" {$wakayama}>和歌山県</option>
											<option value="鳥取県" {$tottori}>鳥取県</option>
											<option value="島根県" {$shimane}>島根県</option>
											<option value="岡山県" {$okayama}>岡山県</option>
											<option value="広島県" {$hiroshima}>広島県</option>
											<option value="山口県" {$yamaguchi}>山口県</option>
											<option value="徳島県" {$tokushima}>徳島県</option>
											<option value="香川県" {$kagawa}>香川県</option>
											<option value="愛媛県" {$ehime}>愛媛県</option>
											<option value="高知県" {$kochi}>高知県</option>
											<option value="福岡県" {$fukuoka}>福岡県</option>
											<option value="佐賀県" {$saga}>佐賀県</option>
											<option value="長崎県" {$nagasaki}>長崎県</option>
											<option value="熊本県" {$kumamoto}>熊本県</option>
											<option value="大分県" {$oita}>大分県</option>
											<option value="宮崎県" {$miyazaki}>宮崎県</option>
											<option value="鹿児島県" {$kagoshima}>鹿児島県</option>
											<option value="沖縄県" {$okinawa}>沖縄県</option>
										</select>
									</td>
								</tr>

								<tr class="tr_editProfile">
									<td class="td_editProfile_label">自己紹介</td>
									<td class="td_editProfile_label"><textarea name="intro" id="intro" class="textarea_introduce">{$intro}</textarea></td>
								</tr>

								<tr>
									<td colspan="2" class="td_button_editProfile">
										<input type="button" id="reg" value="登録" class="button_s textColorRed">
										<input type="hidden" id="proc" name="proc" value="">
	                                                                                
									</td>
								</tr>
							</table>

						</div>

					</form>

				</div>

			</div>

		</div>

		<script language="javascript" type="text/javascript" src="js/profileEdit.js"></script>
		<script language="javascript" type="text/javascript" src="js/jquery.upload-1.0.2.js"></script>
{include file=#footerTsalPath#}