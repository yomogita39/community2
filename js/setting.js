jQuery(function($){
  /**
   * 設定画面内の非同期処理と結果のアラート表示
   */
  $('.button_s').click(function() {
    var button = $(this);
    // 設定変更画面のフラグ
    var flg = $('#flg').val();

    // 入力されたフォームと設定画面のフラグをポスト送信
    $.ajax({
      method: 'POST',
      async: false,
      url: "./setting.php",
      data: $("form").serialize() + '&proc=' + flg,
      beforeSend: function () {
        // 実行前の最終確認
        var con = confirm('変更します。よろしいですか？');
        if (con) {
          button.attr('disabled', 'disabled');
        }
        return con;
      },
      success: function(data) {
        // 返り値によりアラートを出す
        switch (data) {
        case 'mobileOK':
            alert('登録されたアドレスに投稿用メールアドレスを記載したメールを送信しました。ご確認ください。');
                window.location = "setting.php?setting=3";
                break;
        case 'mailOK':
          alert('新しいアドレスにメールを送信しました。メール記載のアドレスにアクセスし、登録を完了してください。');
              window.location = "setting.php?setting=1";
              break;
        case 'passOK':
          alert('パスワードを変更しました。');
              window.location = "setting.php?setting=2";
              break;
        case 'mailNG':
          alert('入力内容に不備があります。もう一度ご確認ください。');
          button.attr('disabled', false);
          break;
        case 'mobileNG':
            alert('入力内容に不備があります。もう一度ご確認ください。');
                window.location = "setting.php?setting=3";
                break;
        case 'addNG':
          alert('アドレスが正しくありません。');
          button.attr('disabled', false);
          break;
        case 'readdNG':
          alert('アドレスが一致しません。');
          button.attr('disabled', false);
          break;
        default :
          alert('入力内容に不備があります。ご確認ください。');
          button.attr('disabled', false);
          break;
        }
      }
    });
  });

  // メール投稿時公開範囲の設定
  $(document).on('change', '#publicState', function() {
    $.ajax({
        method: 'POST',
        async: false,
        url: "./setting.php",
        data: 'proc=4&publicState='+$(this).val(),
        success: function(data) {
          var succesMessage = $('#changePublicStateSuccess');
          // 結果のメッセージを受け取り1秒かけて表示
          succesMessage.hide().text(data).fadeIn(1000);
          // 3秒たったら、1秒かけて非表示
          setTimeout(function() {
            succesMessage.fadeOut(1000);
          }, 3000);
        }
    });
  });

  // 更新ボタンクリック時の画像入れ替え
  $('#refresh').click(function() {
    $('#siimage').attr('src', './securimage/securimage_show.php?sid='+ Math.random());
  });
});