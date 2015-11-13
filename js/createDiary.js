jQuery(function($){
  var count = 0;
  var vFlg = true;
  myDropzone = new Dropzone("#boxs", {
    url: "dropUpload.php", // アップロード処理が記述されたファイル
    paramName: "file", // name
    method: "post", // ポスト送信
    previewsContainer:'#boxs', // サムネイルや結果表示に使用するエリアの設定
    parallelUploads: 2, // 一度にアップロードできるファイルサイズMB
    maxThumbnailFilesize: 2, // 最大サムネイルサイズMB
    maxFilesize: 2, // 1ファイルあたりの最大ファイルサイズMB
    maxUpload: 3,  // 最大アップロードファイル数
    uploadMultiple: false,
    addRemoveLinks: true, // 削除可能かどうか
    maxFiles: 1, // MB
    thumbnailWidth:100, //px
    thumbnailHeight:100, //px
    // プログレスバーの設定
    uploadprogress:function(_file, _progress, _size){
      _file.previewElement.querySelector("[data-dz-uploadprogress]").style.width = "" + _progress + "%";
    },
    // ファイルと一緒にPOST送信する値の設定と送信前の処理
    sending:function(file, xhr, formData){
      count++;
      myDropzone.totalCount++;
      var proc = 'diary';
      formData.append("proc", proc);
      formData.append("diaryCount", count);
    },
    // dropzoneがサポートしていないブラウザだった場合の処理
    fallback:function(){
      // テンプレートファイルを非対応ように切り替え
      $('.fallback').each(function() {
        $(this).toggle();
      });
      $('#create').val('falseCreate');
      $('#reg').attr('type', 'submit');

      vFlg = false;
    },
    // 通信成功時の処理
    success:function(_file, _return, _xml){
      //引数の _return には サーバ側 で出力(echo or print)された値が格納される。
      //サーバ側のエラーを検知するのに使う
      _file.previewElement.classList.add("dz-success");
      if (_return === '投稿できる画像はjpg, png, gifです。') {
        var ref;
//          (ref = _file.previewElement) != null ? ref.parentNode.removeChild(_file.previewElement) : void 0;
        $("#boxs").children('span').html('投稿できる画像はjpg, png, gifです。');
      } else {
        $('#boxs').children('span').html('');
        $(_file.previewElement).append(_return);
      }
    },
    // 何らかのエラーを感知した場合の処理
    error:function(_file, _error_msg){
      var ref;
      (ref = _file.previewElement) != null ? ref.parentNode.removeChild(_file.previewElement) : void 0;
    },
    // サムネイルの削除を選択した場合
    removedfile:function(_file){
      var ref;
      (ref = _file.previewElement) != null ? ref.parentNode.removeChild(_file.previewElement) : void 0;
      var unlinkFile = $(_file.previewElement).find('#postPhotoName').val();
      $.ajax({
        async: true,
        cache: true,
        type:   "POST",
        url:    "dropUpload.php",
        data:   "postPhotoName="+unlinkFile+'&proc=diary',
        success: function(data) {
//          $('#postPhotoName').val('');
//          $('#preview').html('画像(2Mバイト以内のJPEG、PNG、GIF形式。)');
//          $('#preview_area').html('').hide();
          if ($('#boxs').text() == '') {
            $('#boxs').children('span').html('ここにドロップ');
          }
          count--;
          myDropzone.totalCount--;
        }
      });
    },
    // テンプレートの書き換え
    previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-details\">\n    <div class=\"dz-filename\"><span data-dz-name></span></div>\n    <div class=\"dz-size\" data-dz-size></div>\n    <img data-dz-thumbnail />\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-success-mark\"><span>&#10004;</span></div>\n  <div class=\"dz-error-mark\"><span>&#10008;</span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n</div>",
    dictRemoveFile:'削除', // ボタン設定
    dictCancelUpload:'キャンセル' // ボタン設定
  });

//文字カウント
  var options = {
    max : 500, // 最大入力文字数
    maxText : '#maxCount', // 最大入力文字数表示要素
    count : '#count', // カウントアップした値を表示する要素
    countArea : 'textarea', // 文字カウントするテキストエリア
    button : '#reg', // 投稿用ボタン
    style : '#countSpan', // 色を変える要素
    changeStyle : true // テキストカウントをするかどうか
  };
  $('textarea').stringCount(options);


  $('#reg').click(function() {
    if (vFlg) {
      $(this).attr('disabled', 'disabled');
      $.ajax({
            async: true,
            cache: true,
            type:   "POST",
            url:    "./createDiary.php",
            data:   $('form').serialize(),
            dataType: 'json',
            success: function(data) {
              if (data.flg != 'OK') {
                alert(data.message);
                $(this).attr('disabled', false);
              } else {
                count = 0;
                myDropzone.totalCount = 0;
                $('input[name="postPhotoName[]"]').each(function() {
                  $(this).val('');
                });
                window.location = './myDiaryList.php';
              }
            }
          });
    }
  });


  // 画面を離れるときに画像を削除
  $(window).bind("beforeunload",function(event){
    $('input[name="postPhotoName[]"]').each(function() {
      $.ajax({
            async: true,
            cache: true,
            type:   "POST",
            url:    "dropUpload.php",
            data:   'postPhotoName='+$(this).val()+'&proc=diary',
            success: function(data) {

            }
          });
    });
  });
});