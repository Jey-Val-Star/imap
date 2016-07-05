<?php

/** 
 * Файл контроллер
 */
class Controller extends Engine{
  
  public $data = null; // для данных
  
  /** Метод индекс - авторизация или отображение данных */
  public function index()
  {
    
    if(isset($_SESSION['user_id']))
    {
      if(isset($_GET['param']))
      {
        $this->_imapgetmails('inbox');
      }
      else
      {
        $this->_getMails('inbox');
      }
    }
    else if(!empty($_POST))
    {
      $this->_checkPost();
      
      $this->_checkPassword(false, false);
      
      if(!isset($this->data['error']))
      {
        $model = new Model();
        
        $userData = $model->authUser($this->data['data']);
        
        if(!$userData)
        {
          $this->data['error']['not_login'] = 'Неправильный логин или пароль';
          $this->view('login_form',$this->data);
        }
        else
        {
          $this->_getMails();
        }
      }
      else
      {
        $this->view('login_form',$this->data);
      }
    }
    else
    {
      $this->view('login_form');
    }
  }
  
  /** метод разавторизации пользователя */
  public function logout()
  {
    unset($_SESSION['user_id']);
    session_destroy();
    
    header('Location: /');
  }
  
  /** Метод вывода формы регистрации, и регистрация пользователей */
  public function registration()
  {
    if(!empty($_POST))
    { 
      $this->_checkPost();
      
      if(!isset($this->data['error']))
      {
        $this->_checkPassword();
      }

      if(isset($this->data['error']))
      {
        $this->view('registration_form', $this->data);
      }
      else
      { 
        $model = new Model();
        
        if($model->chekEmail($this->data['data']['login']))
        {         
          $model->addUser($this->data['data']);
          
          $this->view('registration_confirm');
        }
        else
        {
          $this->data['error']['login'] = 'Такой логин уже есть';
          
          $this->view('registration_form', $this->data);
        }
      }
    }
    else
    {
      $this->view('registration_form');
    }
  }
  
  public function trash()
  { 
    if(isset($_SESSION['user_id']))
    {
      if(isset($_GET['param']))
      {
        $this->_imapgetmails('trash');
      }
      else
      {
        $this->_getMails('trash');
      }
    }
    else
    {
      header('Location: /');
    }
  }
  
  public function spam()
  { 
    if(isset($_SESSION['user_id']))
    {
      if(isset($_GET['param']))
      {
        $this->_imapgetmails('spam');
      }
      else
      {
        $this->_getMails('spam');
      }
    }
    else
    {
      header('Location: /');
    }
  }
  
  public function delete()
  {
    $mail_id = (isset($_GET['id']))? (int)$_GET['id'] : 0 ;
    
    if($mail_id == 0)
    {
      header('Location: /');
    }
    
    $model = new Model();
    $model->deleteMail($mail_id);
    
    header('Location: /');
  }
  
  private function _getMails($folder='inbox')
  {
    $mail_id = (isset($_GET['id']))? (int)$_GET['id'] : 0;
    
    $model = new Model();
 
    $mails = $model->getMails($folder,$_SESSION['user_id'],$mail_id);
      
    $this->view('mails',$mails);
  }
  
  private function _imapgetmails($folder)
  {   
    if(isset($_SESSION['user_id']))
    {
      $model = new Model();
 
      $userData = $model->getUser($_SESSION['user_id']);
      
      $port = '993';
      $host = 'imap.gmail.com';
      
      // корзина - [Gmail]/&BBoEPgRABDcEOAQ9BDA-
      // Входящие - INBOX
      // Спам - [Gmail]/&BCEEPwQwBDw-
      
      $folderArr = array(
        'inbox'=>'INBOX',
        'trash'=>'[Gmail]/&BBoEPgRABDcEOAQ9BDA-',
        'spam' =>'[Gmail]/&BCEEPwQwBDw-'
        );
      
      //$folder = '[Gmail]/&BBoEPgRABDcEOAQ9BDA-';
      
      $imapPath = '{'.$host.':'.$port.'/imap/ssl/novalidate-cert}'.$folderArr[$folder];
      
      $username = $userData['gmail_login'];
      $password = $userData['gmail_pass'];
      
      
      // try to connect
      error_reporting(E_ERROR);
      
      try
      {
        $inbox = imap_open($imapPath,$username,$password) or die($this->view('error','Cannot connect to Gmail: ' . imap_last_error()));
      } 
      catch(Exception $e)
      {
          echo $e->getMessage();
          $this->view('error','Cannot connect to Gmail: ' . imap_last_error());
          exit;
      }
      
      
      $emails = array_reverse(imap_search($inbox,'ALL'));

      //print_r($emails);
      //exit;
      $output = '';
      
      $i=1;
      
      $model->addMails($inbox,$emails,$folder);
      
      // colse the connection
      imap_expunge($inbox);
      imap_close($inbox);
      
      $folder = ($folder=='inbox') ? '' : $folder;
      
      header('Location: /'.$folder);
    }
    else
    {
      header('Location: /');
    }
  }
  
  /** Метод проверки данных пришедших из формы */ 
  private function _checkPost($password=false)
  {
    foreach($_POST as $key=>$val)
    {
      if(($val == ''))
      {
        $this->data['error'][$key] = 'Заполните поле';
      }
      else
      {
        if($key !== 'confirm_password' && $key !== 'password')
        {
          $this->data['data'][$key] = $this->_protectionData($val);
        }
      }
    }
  }
  

  
  /** Метод очистки удаления скобок html */
  private function _protectionData($data = '')
  {
    if($data != '') 
    {
      $data = str_replace(array('<','>'), '|', $data);
    }
    
    return $data;
  }
  
  /** Проверка ввода пароля */
  private function _checkPassword($hash=true, $confirm = true)
  {
    if($_POST['password'] !== '')
    {
      if(!$confirm)
      {
        $_POST['confirm_password'] = $_POST['password'];
      }
      
      if($_POST['password']==$_POST['confirm_password'])
      {
        $this->data['data']['password'] = ($hash) ? $this->_hashPassword() : $_POST['password'];
      }
      else
      {
        $this->data['error']['confirm_password'] = $this->fLang('_not_confirm_password_');
      }
    }
    else
    {
      $this->data['error']['password'] = $this->fLang('_not_password_');
    }
  }
  
  /** Хеширование пароля с солью */
  private function _hashPassword()
  {
    $salt = md5(uniqid('nhaw723#@', true));
    $salt = substr(strtr(base64_encode($salt), '+', '.'), 0, 22);
    
    $this->data['data']['salt'] = $salt;
    
    return crypt($_POST['password'], $salt);
  }
}