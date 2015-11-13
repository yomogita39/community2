jQuery(function($){
var timer;
var confTimer;
var preview = $('#preview');
var vFlg = true;
  //フォームの内容が変更されたとき
  $(document).on("change", '#file', function() {
    var value = 0;
    var preview = $('#preview');
    // fileが選択されているとき
    if ($(this).val() !== '') {
      // preview内のテキストを空に
      preview.text('');
      // プログレスバーのセット
      timer = setInterval(function() {
        // プログレスバーの値を最大80まで1msごとに上昇
        if (value < 80) {
          value += 2;
        } else {
          // 80になったらタイマーを停止
          clearInterval(timer);
        }
        // プログレスバーに値をセット
        preview.progressbar({
          value: value
        });
      }, 1);
      // form送信ボタンの非押下処理
      $('#msgsend').attr('disabled', 'disabled');
      // 画像アップロード
      $(this).upload(
        'upload.php',
        $("form").serialize(),
        function(data){
          // 通信成功時タイマーを停止し、プログレスバーの値を100に
          clearInterval(timer);
              value = 100;
              preview.progressbar({
                value: value
              });
              // プログレスバーの値を視覚的にとらえられるよう100ms後処理を実行
              confTimer = setInterval(function() {
                preview.html(data);
                preview.progressbar("destroy");
                $('#msgsend').attr('disabled', false);
                clearInterval(confTimer);
              }, 100);
        },'html');
    } else {
      $('#postPhotoName').val('');
      $('#preview').html('画像(2Mバイト以内のJPEG、PNG、GIF形式。)');
    }
  });
  $("#boxs").dropzone({
    url: "upload.php",
    paramName: "file",
    method: "post",
    previewsContainer:'#preview_area',
    parallelUploads: 1,
    maxThumbnailFilesize: 1,
    maxFilesize: 2,
    uploadMultiple: false,
    addRemoveLinks: true,
    maxFiles: 1,
    thumbnailWidth:100, //px
    thumbnailHeight:100, //px
    uploadprogress:function(_file, _progress, _size){
      _file.previewElement.querySelector("[data-dz-uploadprogress]").style.width = "" + _progress + "%";
    },
    fallback:function(){
      $('#fallback').append('<input type="file" size="11" id ="file" name="file" class="input_timeLine_imageFile" enctype="multipart/form-data">');
      $('#boxs').hide();
      vFlg = false;
    },
    sending:function(){
      $('#boxs').hide();
      $('#preview_area').show();
    },
    success:function(_file, _return, _xml){
      //引数の _return には サーバ側 で出力(echo or print)された値が格納される。
      //サーバ側のエラーを検知するのに使いといい。
      _file.previewElement.classList.add("dz-success");
      if (_return === '投稿できる画像はjpg, png, gifです。') {
        var ref;
          (ref = _file.previewElement) != null ? ref.parentNode.removeChild(_file.previewElement) : void 0;
        $("#boxs").html('投稿できる画像はjpg, png, gifです。');
        $('#preview_area').html('').hide();
        $('#boxs').show();
      } else {
        preview.html(_return);
      }
    },
    error:function(_file, _error_msg){
      var ref;
      (ref = _file.previewElement) != null ? ref.parentNode.removeChild(_file.previewElement) : void 0;
    },
    removedfile:function(_file){
      var ref;
      (ref = _file.previewElement) != null ? ref.parentNode.removeChild(_file.previewElement) : void 0;
      var unlinkFile = $("#postPhotoName").val();
      $.ajax({
        async: true,
        cache: true,
        type:   "POST",
        url:    "upload.php",
        data:   "postPhotoName="+unlinkFile,
        success: function(data) {
          $('#postPhotoName').val('');
          $('#preview').html('画像(2Mバイト以内のJPEG、PNG、GIF形式。)');
          $('#preview_area').html('').hide();
          $('#boxs').html('ここにドロップ').show();
        }
      });
    },
    previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-details\">\n    <div class=\"dz-filename\"><span data-dz-name></span></div>\n    <div class=\"dz-size\" data-dz-size></div>\n    <img data-dz-thumbnail />\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-success-mark\"><span>&#10004;</span></div>\n  <div class=\"dz-error-mark\"><span>&#10008;</span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n</div>",
    dictRemoveFile:'削除',
    dictCancelUpload:'キャンセル'
  });
  $('#msgsend').click(function() {
    $('#preview').html('画像(2Mバイト以内のJPEG、PNG、GIF形式。)');
    $('#preview_area').html('').hide();
    if (vFlg) {
      $('#boxs').html('ここにドロップ').show();
    }
  });
  // 画面を離れるときに画像を削除
  $(window).bind("beforeunload",function(){
    var unlinkFile = $("#postPhotoName").val();
    $.ajax({
      async: true,
      cache: true,
      type:   "POST",
      url:    "upload.php",
      data:   "postPhotoName="+unlinkFile
    });
  });
});