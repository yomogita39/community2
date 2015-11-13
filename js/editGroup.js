jQuery(function($){
  /**
   * グループの編集
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
      width: 700,
      buttons: {
        '変更': function() {
          var groupName = $('#groupName');
          if (groupName !== '') {
            dialog.dialog('close');
            $.ajax({
              method:   "POST",
              url:    "editGroup.php",
              data:   $("form").serialize()+'&proc=group&groupId='+$('input[name="groupId"]').val(),
              success: function (data) {
                if (data === 'OK') {
                  alert('変更しました。');
                  window.location = 'groupHome.php?groupId='+$('input[name="groupId"]').val();
                } else {
                  alert('失敗しました。');
                  $(this).attr('disabled', false);
                }
              }
            });
          }
        },
        'キャンセル': function() {
          dialog.dialog('close');
          $(this).attr('disabled', false);
        }
      }
  });
  // テキストボックスをクリックした場合全選択
  $('#groupName').click(function() {
    $(this).select();
  });
  // 入力フォーム内のメールアドレスを転載し、ダイアログをオープン
  $('#groupEdit').click(function() {
    $('#groupName').val($('#imageName').html());
     dialog.dialog('open');
  });
  $('#file').change(function() {
      var preview = $('#preview');

    //現在表示されているものを消す。
//	        preview.find("img").fadeOut(300);

    //アップロード
    $(this).upload(
      'upload.php',
      $("form").serialize()+"&proc=group",
      function(html){
       //サムネイルの表示
        preview.html(html);
//	     preview.html(html).animate({"height":preview.find("img").height()+"px"},300,function(){
//	     preview.find("img").hide().fadeIn(300);
//	     });
      },'html');
    });

    //離れるときに画像を削除
    $(window).bind("beforeunload",function(){
      var unlinkFile = $("#postPhotoName").val();
      $.ajax({
        async: false,
        cache: false,
        method:   "POST",
        url:    "upload.php",
        data:   "postPhotoName="+unlinkFile+"&proc=profile"+"&del=del"
      });
    });
});