$(document).ready(function(){
  // アクセス権限追加ダイアログの生成
  var aDialog = $( "#accessDialog" );
  // ダイアログの設定
  aDialog.dialog({
    autoOpen: false, // ページを開いた際に勝手にオープンするかどうか
    modal: true,     // ダイアログを開いた際背景を暗くして操作を受け付けなくする
    show: 'fade',    // 表示時のアニメーション
    hide: 'fade',    // 閉じる際のアニメーション
    position: 'center center', // 画面のどこに表示させるか w h
    width: 400,      // ダイアログのサイズ設定
    // ダイアログで生成するボタンの設定
    buttons: {
      '追加': function() {
        var friendSelect = $('#friendSelect option:selected');
        if (friendSelect.val() !== '') {
          aDialog.dialog('close');
          $.ajax({
            method:   "POST",
            url:    "workEdit.php",
            data:   $("#accessForm").serialize()+'&proc=accessAdd',
            dataType: 'json',
            beforeSend: function () {
              if (friendSelect.val() !== '') {
                return confirm(friendSelect.text() + 'さんに日誌への\n' +
                    'アクセス権限を与えます。よろしいですか？');
              } else {
                return false;
              }
            },
            success: function (data) {
              if (data.flg === 'OK') {
                $('#accessMember').html(data.html);
                $('#friendSelect').html(data.option);
                alert('追加しました。');
              } else {
                alert('失敗しました。');
              }
            }
          });
        }
      },
      '削除': function() {
        aDialog.dialog('close');
        var delCheck = $("[name='accessCheck[]']:checked");
          $.ajax({
            method:   "POST",
            url:    "workEdit.php",
            data:   $("#accessForm").serialize()+'&proc=accessDel',
            dataType: 'json',
            beforeSend: function () {
              if (delCheck.length > 0) {
                return confirm(delCheck.parent().text() + 'さんの日誌への\n' +
                    'アクセス権限を削除します。よろしいですか？');
              } else {
                return false;
              }
            },
            success: function (data) {
              if (data.flg === 'OK') {
                $('#accessMember').html(data.html);
                $('#friendSelect').html(data.option);
                alert('削除しました。');
              } else {
                alert('失敗しました。');
              }
            }
          });
      },
      'キャンセル': function() {
        aDialog.dialog('close');
      }
    }
  });
  $(document).on('click','#accessButton', function() {
    aDialog.dialog('open');
  });

  // 選択したユーザの現在開いている日誌にアクセスする
  $(document).on('change','#perDiary', function() {
      if ($('#perDiary').val() !== '') {
        window.location = location.pathname+ '?perDiary=' + $('#perDiary').val();
      } else {
        window.location = location.pathname;
      }
    });
});
jQuery(function($){

});