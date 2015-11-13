jQuery(function($){
  select();
  //フォームの内容が変更されたとき
  $('#file').change(function() {
    var preview = $('#preview');

  //現在表示されているものを消す。
//        preview.find("img").fadeOut(300);

  //アップロード
  $(this).upload(
    'upload.php',
    $("form").serialize()+"&proc=profile",
    function(html){
     //サムネイルの表示
      preview.html(html);
//     preview.html(html).animate({"height":preview.find("img").height()+"px"},300,function(){
//     preview.find("img").hide().fadeIn(300);
//     });
    },'html');
  });

  //離れるときに画像を削除
  $(window).bind("beforeunload",function(){
    var unlinkFile = $("#postPhotoName").val();
    if (unlinkFile) {
      $.ajax({
        async: false,
        cache: false,
        method:   "POST",
        url:    "upload.php",
        data:   "postPhotoName="+unlinkFile+"&proc=profile"+"&del=del"
      });
    }
  });

  $('.input').each(function() {
      var guideText = this.defaultValue;
      var element = $(this);
      var intro = $('#intro');
      var userName = $('#userName');

      var str = userName.val();

      element.focus(function() {
          element.css('color', 'black');
      });
      userName.blur(function() {
        if (userName.val() === '' || userName.val() === guideText) {
          userName.val(guideText);
          userName.css('color', 'gray');
        }
      });
      intro.blur(function() {
        if (intro.val() === guideText) {
          intro.css('color', 'gray');
        }
      })
      if (element.val() === guideText) {
        element.css('color', 'gray');
      }
    });
//  var flg = false;
//  var count = 0;
  $('#reg').click(function() {
//    count++;
//    alert(count);
    var reg = $(this);
    $.ajax({
      method: "POST",
      url: "profileEdit.php",
      data: $("form").serialize()+"&proc=edit",
      beforeSend: function () {
        if (jQuery.trim($('#userName').val()) !== '' && jQuery.trim($('#realName').val())) {
          var con = confirm('編集内容を登録します');
          if (con) {
            reg.attr('disabled', 'disabled');
            $('#realName').val(jQuery.trim($('#realName').val()));
            $('#userName').val(jQuery.trim($('#userName').val()));
            $('#intro').text(jQuery.trim($('#intro').text()));
          }
        } else {
          alert('入力内容に不備があります。ご確認ください。')
          con = false;
        }
            return con;
          },
      success: function (data) {
        if (data === 'OK') {
          window.location = 'profile.php';
        } else if (data === 'NG2'){
          alert('ユーザ名は既に使用されています。');
          reg.attr('disabled', false);
        } else {
          alert('登録失敗しました。');
            reg.attr('disabled', false);
        }
      }
    });
  });

  /**
   * 月が選択された場合
   */
  $('#month').change(function () {
    select();
  });
  /**
   * 年が選択された場合
   */
  $('#year').change(function () {
    var month = parseInt($('#month').val());
    if (month === 2) {
      var year = parseInt($(this).val());
      // 閏年判定
      if ((year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0)) {
        if (0 === $('option[value="29"]').size()) {
          var html = '<option value="29" >29</option>';
          $('#day').append(html);
        }
      } else {
        if (0 < $('option[value="29"]').size()) {
          $('option[value="29"]').remove();
        }
      }
    }
  });
  /**
   * 選択された年月に対応した日を割り出す
   */
  function select () {
    // 選択されている月の値を整数型にして取得する
    var month = parseInt($('#month').val());
    // html変数の初期化
    var html = '';
    // 月の判断
    if (month === 1 || month === 3 || month === 5 || month === 7 || month === 8 ||
        month === 10 || month === 12) {
      // 大の月の場合の日付を設定
      if (0 === $('option[value="31"]').size()) {
        if (0 === $('option[value="30"]').size()) {
          if (0 === $('option[value="29"]').size()) {
            html += '<option value="29" >29</option>';
          }
          html += '<option value="30" >30</option>';
        }
        html += '<option value="31" >31</option>';
      }
    } else {
      // 小の月の場合の日付を設定、2月の場合は閏年の検討
      if (month === 2) {
        // 年の値を整数型にして取得
        var year = parseInt($('#year').val());
        if ((year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0)) {
          if (0 === $('option[value="29"]').size()) {
            html = '<option value="29" >29</option>';
          }
        } else {
          if (0 < $('option[value="29"]').size()) {
            $('option[value="29"]').remove();
          }
        }
        if (0 < $('option[value="30"]').size()) {
          if (0 < $('option[value="31"]').size()) {
            $('option[value="31"]').remove();
          }
            $('option[value="30"]').remove();
        }
      } else {
        if (0 === $('option[value="30"]').size()) {
          if (0 === $('option[value="29"]').size()) {
            html += '<option value="29" >29</option>';
          }
          html += '<option value="30" >30</option>';
          $('#day').append(html);
        }
        if (0 < $('option[value="31"]').size()) {
          $('option[value="31"]').remove();
        }
      }
    }
    // 生成したhtmlを日付として追加する
    $('#day').append(html);
  }
});