jQuery(function($) {
  $(document).on('click', '#loginButton', function() {
    var mail = $('input[name="userId"]');
    var pass = $('input[name="password"]');
    var form = $('#loginForm');
    $.ajax({
          method: "POST",
          url: "login.php",
          data: form.serialize()+"&proc=login",
          dataType: 'json',
          beforeSend: function () {
            if (jQuery.trim(mail.val()) === '') {
              alert('メールアドレスを入力してください。');
              return false;
            } else if (jQuery.trim(pass.val()) ==='') {
              alert('パスワードを入力してください。');
              return false;
            }
            mail.val(jQuery.trim(mail.val()));
            pass.val(jQuery.trim(pass.val()));
            return true;
          },
          success: function(data) {
            if (data.flg === 'OK') {
              window.location = 'home.php';
            } else {
              alert(data.message);
            }
          }
        });
  });

  $('.input_login').keypress(function (event) {
    if (event.which === 13 || event.keyCode === 13) {
      $('#loginButton').trigger('click');
    }
  })
});