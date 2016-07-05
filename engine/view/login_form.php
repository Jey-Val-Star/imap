<div class="row main">


<form method="post" action="" id="login_form" class="login_form content">
<?php if(isset($data['error']['not_login'])){ ?>
  <p class="error"><?=$data['error']['not_login']?></p>
  <?php } ?>
  <div class="label_field">
    <label for="login">Логин</label>
    <input type="text" class="form-control inp_field chek_data" id="login" name="login" value="<?=$this->checkData($data,'data','login')?>" />
    <p class="error login"><?=$this->checkData($data,'error','login')?></p>
  </div>
  <div class="label_field">
    <label for="password">Пароль</label>
    <input type="password" class="form-control inp_field chek_data" id="password" name="password" />
    <p class="error password"><?=$this->checkData($data,'error','password')?></p>
  </div>
  <div>
    <input type="submit" class="btn btn-primary btn-block" value="Войти" />
  </div>
  
  <a href="/registration" class="link_reg">Зарегистрироваться</a>
</form>

<div class="hide out_field">Заполните поле</div>

</div>