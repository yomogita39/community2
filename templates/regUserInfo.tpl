{config_load file="common.conf"}

{include file=#headerPath# title='T-SAL農園&nbsp;SNS'}

	<div class="display">
		<div><img src="./templates/image/top_title.png" alt="タイトル画像"></div>

		<div class="frame_userReg">
			<div class="frame_userReg_title">入会-アカウント登録</div>

			<div class="frame_input_userReg">

				<div class="input_userReg_caption">
					<span>○ユーザ情報を入力して下さい。</span>
					<span class="textColorRed">※全て必須入力になります。</span>
				</div>

				<form id="regform">
					<table class="input_userReg_table">
						<tr>
							<td class="input_userReg_label">メールアドレス</td>
							<td><input type="text" id="userId" name="userId" class="input_userReg validate[required,custom[email],maxSize[50]]" value="メールアドレスを入力して下さい"></td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">名前(本名)</td>
							<td><input type="text" id="userName" name="realName" class="input_userReg validate[required,maxSize[20]]" value="ご本名を入力して下さい"></td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">ニックネーム</td>
							<td><input type="text" id="userName" name="userName" class="input_userReg validate[required,maxSize[20]]" value="サイト内でのお名前を入力して下さい"></td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">性別</td>
							<td>
								<select name="gender" class="select_gender">
									<option value="" selected="selected"></option>
									<option value="男性">男性</option>
									<option value="女性">女性</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">誕生日</td>
							<td>
								<select name="year" id="year" class="select_gender">
									<option value=""></option>
										{$year}
								</select>
								<select name="month" id="month" class="select_date">
									<option value="" selected="selected"></option>
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
								</select>
								<select name="day" id="day" class="select_date">
									<option value="" selected="selected"></option>
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
									<option value="24">24</option>
									<option value="25">25</option>
									<option value="26">26</option>
									<option value="27">27</option>
									<option value="28">28</option>
									<option value="29">29</option>
									<option value="30">30</option>
									<option value="31">31</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td class="input_userReg_label">都道府県</td>
							<td>
								<select name="place" class="select_gender">
									<option value="" selected="selected"></option>
									<option value="北海道">北海道</option>
									<option value="青森県">青森県</option>
									<option value="岩手県">岩手県</option>
									<option value="宮城県">宮城県</option>
									<option value="秋田県">秋田県</option>
									<option value="山形県">山形県</option>
									<option value="福島県">福島県</option>
									<option value="茨城県">茨城県</option>
									<option value="栃木県">栃木県</option>
									<option value="群馬県">群馬県</option>
									<option value="埼玉県">埼玉県</option>
									<option value="千葉県">千葉県</option>
									<option value="東京都">東京都</option>
									<option value="神奈川県">神奈川県</option>
									<option value="新潟県">新潟県</option>
									<option value="富山県">富山県</option>
									<option value="石川県">石川県</option>
									<option value="福井県">福井県</option>
									<option value="山梨県">山梨県</option>
									<option value="長野県">長野県</option>
									<option value="岐阜県">岐阜県</option>
									<option value="静岡県">静岡県</option>
									<option value="愛知県">愛知県</option>
									<option value="三重県">三重県</option>
									<option value="滋賀県">滋賀県</option>
									<option value="京都府">京都府</option>
									<option value="大阪府">大阪府</option>
									<option value="兵庫県">兵庫県</option>
									<option value="奈良県">奈良県</option>
									<option value="和歌山県">和歌山県</option>
									<option value="鳥取県">鳥取県</option>
									<option value="島根県">島根県</option>
									<option value="岡山県">岡山県</option>
									<option value="広島県">広島県</option>
									<option value="山口県">山口県</option>
									<option value="徳島県">徳島県</option>
									<option value="香川県">香川県</option>
									<option value="愛媛県">愛媛県</option>
									<option value="高知県">高知県</option>
									<option value="福岡県">福岡県</option>
									<option value="佐賀県">佐賀県</option>
									<option value="長崎県">長崎県</option>
									<option value="熊本県">熊本県</option>
									<option value="大分県">大分県</option>
									<option value="宮崎県">宮崎県</option>
									<option value="鹿児島県">鹿児島県</option>
									<option value="沖縄県">沖縄県</option>
								</select>
							</td>
						</tr>

						<tr>
							<td class="input_userReg_label">パスワード</td>
							<td><input type="password" id="password" name="password" class="input_userReg validate[required,custom[onlyLetterNumber],maxSize[20],minSize[6]]"></td>
						</tr>

						<tr>
							<td class="input_userReg_label">パスワード(再確認)</td>
							<td><input type="password" id="repassword" name="repassword" class="input_userReg validate[required,custom[onlyLetterNumber],maxSize[20],minSize[6],equals[password]]"></td>
						</tr>
					</table>

				</form>
				<input type="submit" id="regsubmit" value="次へ" class="button_right_s">
                                
				<form action="./login.php">
					<input type="submit" value="戻る" class="button_left_s">
				</form>

				<div class="triangle">&nbsp;</div>
				<div class="triangle2">&nbsp;</div>
				<div class="triangle3">&nbsp;</div>

			</div>
			
			<div class="height_50">&nbsp;</div>

		</div>

		<div class="footer">&nbsp;</div>
	</div>
	
	<script language="javascript" type="text/javascript" src="js/profileEdit.js"></script>

{include file=#footerPath#}