jQuery(function($){
  /**
   * ログインフォーム内パスワード忘れようダイアログの生成と実行処理
   */
  // ダイアログの生成
  var dialog = $( "#dialog" );
  // ダイアログの設定
  dialog.dialog({
      autoOpen: false,
      modal: true,
      show: 'fade',
      hide: 'fade',
      position: 'center center',
      width: 350,
      buttons: {
        '送信': function() {
          var mAddress = $('#mAddress').val();
          if (mAddress !== '') {
            dialog.dialog('close');
            $.ajax({
              method:   "POST",
              url:    "forgetsPass.php",
              data:   "mail=" + mAddress,
              success: function (data) {
                if (data === 'OK') {
                  alert('入力されたアドレスにメールを送信いたしました。ご確認ください。')
                } else {
                  switch (parseInt(data)) {
                    case 0: alert('メールアドレスが登録されていません。');break;
                    case 1: alert('パスワードの発行に失敗しました。もう一度実行してください。');break;
                    case 2: alert('メールの送信に失敗しました。');break;
                  }
                }
              }
            });
          }
        },
        'キャンセル': function() {
          dialog.dialog('close');
        }
      }
  });
  // テキストボックスをクリックした場合全選択
  $('#mAddress').click(function() {
    $(this).select();
  });
  // 入力フォーム内のメールアドレスを転載し、ダイアログをオープン
  $('#forgetPass').click(function() {
    $('#mAddress').val($('input[name="userId"]').val());
     dialog.dialog('open');
  });
//  $('#cDialog').click(function() {
//    dialog.dialog('close');
//  });
//  $('#mailSend').click(function() {
//    var mAddress = $('#mAddress').val();
//    if (mAddress !== '') {
//      dialog.dialog('close');
//      $.ajax({
//        method:   "POST",
//        url:    "forgetsPass.php",
//        data:   "mail=" + mAddress,
//        success: function (data) {
//          if (data === 'OK') {
//            alert('入力されたアドレスにメールを送信いたしました。ご確認ください。')
//          } else {
//            switch (parseInt(data)) {
//              case 0: alert('メールアドレスが登録されていません。');break;
//              case 1: alert('パスワードの発行に失敗しました。もう一度実行してください。');break;
//              case 2: alert('メールの送信に失敗しました。');break;
//            }
//          }
//        }
//      });
//    }
//  });
});