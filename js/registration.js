jQuery(function($){
  $("#regform").validationEngine();
  function hoge(elm, userId, userName, pass, repass, guideText){

    var v, old = elm.value;
    return function(){
      if(old != (v=elm.value)){
        old = v;
        if (userId.val() !== guideText && userName.val() !== guideText &&
          pass.val() !== '' && repass.val() !== '') {
            $('#regsubmit').removeAttr('disabled');
        } else {
          $('#regsubmit').attr('disabled', true);
        }
      }
    }
  }
  $('.input_userReg').each(function() {
    var guideText = this.defaultValue;
    var element = $(this);
    var userId = $('#userId');
    var userName = $('#userName');
    var pass = $('#password');
    var repass = $('#repassword');

    var str = element.val();

    if (str === '') {
      $('#regsubmit').attr('disabled', true);
    }
    element.bind('keyup', hoge(this, userId, userName, pass, repass, guideText));
    element.focus(function() {
      if (element.val() === guideText) {
        element.val('');
        element.css('color', 'black');
      }
    });
    element.blur(function() {
      if (element.val() === '') {
        element.val(guideText);
        element.css('color', 'gray');
        $('#regsubmit').attr('disabled', true);
      }
//      if (userId.val() !== guideText && userName.val() !== guideText &&
//      pass.val() !== '' && repass.val() !== '') {
//        $('#regsubmit').removeAttr('disabled');
//      }

    });
    if (element.val() === guideText) {
      element.css('color', 'gray');
    }
  });

  $('#reset').click(function() {
    var userId = $('#userId');
    var userValue = document.getElementById("userId").defaultValue;
    var userName = $('#userName');
    var nameValue = document.getElementById("userName").defaultValue;
    $('.input').val('');
    userId.val(userValue);
    userName.val(nameValue);
    userId.css('color', 'gray');
    userName.css('color', 'gray');
  })

  $('#regsubmit').click(function() {
    var flg = false;
    $.ajax({
      async: false,
      type: "POST",
      url: "registration.php",
      data: $("#regform").serialize()+"&regcomp=comp",
      success: function(data){
        flg = data;
        switch(data){
        case 'mail':
          alert('ご指定のメールアドレスは既に登録されています');
          break;
        case 'success':
          window.location = "registrationCheck.php";
          break;
        case 'name':
          alert('ご指定のユーザ名は既に登録されています');
          break;
        default:
          alert('入力内容に不備があります。今一度ご確認ください');
        break;
        }
      }
    });
  });
});