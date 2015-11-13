jQuery(function($){
var timer;
var confTimer;
var preview = $('#preview');
var maxUpload = 1;
var uploadCount = 0;
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
        'dropUpload.php',
        $("form").serialize()+'&proc='+$('#proc').val(),
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
                $('#file').val('');
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
  myDropzone = new Dropzone("#boxs", {
      url: "dropUpload.php", // アップロード処理が記述されたファイル
      paramName: "file", // name
      method: "post", // ポスト送信
      previewsContainer:'#preview_area', // サムネイルや結果表示に使用するエリアの設定
      parallelUploads: 2, // 一度にアップロードできるファイルサイズMB
      maxThumbnailFilesize: 2, // 最大サムネイルサイズMB
      maxFilesize: 2, // 1ファイルあたりの最大ファイルサイズMB
      maxUpload: 1,
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
        if (maxUpload > uploadCount) {
          uploadCount++;
          this.totalCount++;
          var proc = $('#proc').val();
          formData.append("proc", proc);
          $('#boxs').hide();
          $('#preview_area').show();
        }
      },
      // dropzoneがサポートしていないブラウザだった場合の処理
      fallback:function(){
        $('#fallback').append('<input type="file" size="11" id ="file" name="file" class="input_timeLine_imageFile" enctype="multipart/form-data">');
        $('#boxs').hide();
        vFlg = false;
      },
      // 通信成功時の処理
      success:function(_file, _return, _xml){
        //引数の _return には サーバ側 で出力(echo or print)された値が格納される。
        //サーバ側のエラーを検知するのに使う
        _file.previewElement.classList.add("dz-success");
        if (_return === '投稿できる画像はjpg, png, gifです。') {
          var ref;
            (ref = _file.previewElement) != null ? ref.parentNode.removeChild(_file.previewElement) : void 0;
          $("#boxs").html('投稿できる画像はjpg, png, gifです。');
          $('#preview_area').html('').hide();
          $('#boxs').show();
        } else {
          $('#preview').html(_return);
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
        var unlinkFile = $("#postPhotoName").val();
        $.ajax({
          async: true,
          cache: true,
          type:   "POST",
          url:    "dropUpload.php",
          data:   "postPhotoName="+unlinkFile+'&proc='+$('#proc').val(),
          success: function(data) {
            $('#postPhotoName').val('');
            $('#preview').html('画像(2Mバイト以内のJPEG、PNG、GIF形式。)');
            $('#preview_area').html('').hide();
            $('#boxs').html('ここにドロップ').show();
            uploadCount--;
            myDropzone.totalCount--;
          }
        });
      },
      // テンプレートの書き換え
      previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-details\">\n    <div class=\"dz-filename\"><span data-dz-name></span></div>\n    <div class=\"dz-size\" data-dz-size></div>\n    <img data-dz-thumbnail />\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-success-mark\"><span>&#10004;</span></div>\n  <div class=\"dz-error-mark\"><span>&#10008;</span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n</div>",
      dictRemoveFile:'削除', // ボタン設定
      dictCancelUpload:'キャンセル' // ボタン設定
    });

  $('#msgsend').click(function() {
    $('#preview_area').html('').hide();
    uploadCount = 0;
    myDropzone.totalCount = 0;
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
      url:    "dropUpload.php",
      data:   "postPhotoName="+unlinkFile+'&proc='+$('#proc').val()
    });
  });
});