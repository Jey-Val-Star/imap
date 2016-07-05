<div class="row main">

<a href="/" class="link_reg">Назад</a>

<div class="login_form content">
  <?php if(isset($data['error'])){ ?>
  <p class="error">Есть ошибки в форме</p>
  <?php } ?>
  <form action="" method="post" id="registration_form">
  
    <div class="inform_block">
      
      <p class="inform_form">Данные доступа</p>
      <div class="label_field">
        <label for="gmail_login">Логин Gmail *</label>
        <input type="text" class="form-control inp_field chek_data" id="gmail_login" name="gmail_login" value="<?=$this->checkData($data,'data','gmail_login')?>" />
        <p class="error gmail_login"><?=$this->checkData($data,'error','gmail_login')?></p>
      </div>
      
      <div class="label_field">
        <label for="gmail_pass">Пароль Gmail *</label>
        <input type="text" class="form-control inp_field chek_data" id="gmail_pass" name="gmail_pass" value="<?=$this->checkData($data,'data','gmail_pass')?>" />
        <p class="error gmail_pass"><?=$this->checkData($data,'error','gmail_pass')?></p>
      </div>
    
    </div>
    
    <div class="inform_block">
      <div class="label_field">
        <label for="login">Логин *</label>
        <input type="text" class="form-control inp_field chek_data" id="login" name="login" value="<?=$this->checkData($data,'data','login')?>" />
        <p class="error login"><?=$this->checkData($data,'error','login')?></p>
      </div>
      
      <div class="label_field">
        <label for="password">Пароль *</label>
        <input type="password" class="form-control inp_field chek_data" id="password" name="password" />
        <p class="error password"><?=$this->checkData($data,'error','password')?></p>
      </div>
      
      <div class="label_field">
        <label for="confirm_password">Повтор пароля *</label>
        <input type="password" class="form-control inp_field chek_data" id="confirm_password" name="confirm_password" />
        <p class="error confirm_password"><?=$this->checkData($data,'error','confirm_password')?></p>
      </div>
    </div>
    
    <div>
      <input type="submit" class="btn btn-primary btn-block" value="Зарегистрироваться" />
    </div>
    
  </form>
</div>

</div>
<div class="hide out_field">Заполните поле</div>
<div class="hide not_confirm_pass">Пароли не совпадают</div>