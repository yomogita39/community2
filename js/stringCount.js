//(function($) {
jQuery(function($) {
  var stringCount = function(element, options) {
    var defaults = {
      max : 500, // 最大入力文字数
      maxText : '#maxCount', // 最大入力文字数表示要素
      count : '#count', // カウントアップした値を表示する要素
      countArea : 'textarea', // 文字カウントするテキストエリア
      button : '#msgsend', // 投稿用ボタン
      style : '#countSpan', // 色を変える要素
      changeStyle : true // テキストカウントをするかどうか
    };
    // 設定内容を入力値と比較して設定する
    var setting = $.extend(defaults, options);
    // 対象となるテキストエリア
    var target = $(element);

    // 初期値をセット
    if (setting.changeStyle) {
      $(setting.maxText).text(setting.max);
      $(setting.count).text(target.val().length);
    }
    // 対象でのキーイベントを確認したらスタート
    target.bind('keydown keyup keypress change', function() {
      // 入力された文字列の文字数を取得
      var thisLength = target.val().length;
      // 最大入力文字数を超過したらカウントアップを停止し送信ボタンをdisabledに
      if (setting.max >= thisLength) {
        $(setting.button).attr('disabled', false);
        countChange(thisLength);
        $(setting.style).css({color:'#000000',fontWeight:'normal'});
      } else {
        $(setting.button).attr('disabled', 'disabled');
        countChange(thisLength);
        $(setting.style).css({color:'#ff0000',fontWeight:'normal'});
      }
    });

    // カウントアップした値を引数の値に
    var countChange = function (count) {
        $(setting.count).text(count);
    }


    // メソッドチェイン対応のためthisを返却する
    return (this);
  }
});
