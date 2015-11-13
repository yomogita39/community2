document.open();
document.write('<script src="./js/jquery.stringCount.js"></script>');
document.close();
jQuery(function($){
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
});