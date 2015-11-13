jQuery(function($){
  $('#inquiry').click(function() {
    $(this).attr('disabled', true);
    $.ajax({
      method: 'POST',
      async: false,
      url: "./regmail/inquiry.php",
      data: "userMail="+$('#userMail').val(),
      success: function(data) {
        if (data === '1') {
          alert('問い合わせメールを送信しました。確認後、ご連絡まで最長3営業日ほどお待ちください');
          window.location = "login.php";
        } else {
          alert('お問い合わせ失敗');
          $(this).attr('disabled', false);
        }
      }
    });
  });
});