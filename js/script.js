$(document).ready(function(){
  
    /** Проверка ввода двнных в форму авторизации (login_form) */
    $('#login_form').submit(function(){
        var error = 0;

        // Проверка логина пользователя
        if($(this).find('#login').val() == ''){
            $('.login').html($('.out_field').text());
            error = 1;
        }
        
        // проверка пароля
        if($(this).find('#password').val() == ''){
            $('.password').html($('.out_field').text());
            error = 1;
        }
        
        // проверка ошибок
        if(error == 1)
        {
            return false
        }
        
    });
    /** end login_form */
    
    /** Проверка ввода двнных в форму регистрации (registration_form) */
    $('#registration_form').submit(function(){
        var error = 0;

        // Проверка поля 
        if($(this).find('#gmail_login').val() == ''){
            $('.gmail_login').html($('.out_field').text());
            error = 1;
        }
        
        if($(this).find('#gmail_pass').val() == ''){
            $('.gmail_pass').html($('.out_field').text());
            error = 1;
        }
        
        if($(this).find('#login').val() == ''){
            $('.login').html($('.out_field').text());
            error = 1;
        }
        
        
        // проверка поля - "Пароль"
        if($(this).find('#password').val() == ''){
            $('.password').html($('.out_field').text());
            error = 1;
        }
        
        // проверка поля - "Подтверждения пароля"
        if($(this).find('#confirm_password').val() != $(this).find('#password').val()){
            $('.confirm_password').html($('.not_confirm_pass').text());
            error = 1;
        }
        
        // проверка ошибок
        if(error == 1)
        {
            return false
        }
        
    });
    /** end registration_form */
    
    /** Скрытие ошибок при изменении значения полей */
    $('.chek_data').focus(function(){
      var id = $(this).attr('id');
      if(id == 'day' || id == 'month' || id == 'year')
      {
        $('.date').text('');
      }
      else
      {
        $('.'+id).text('');
      }
    });
  
});
