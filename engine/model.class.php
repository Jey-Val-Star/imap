<?php
/** 
  Модель приложения
*/ 
Class Model
{
  private $host = 'localhost';
  private $db = 'task_imap';
  private $user = 'root';
  private $passw = '';
  private $mysqli = null;
  
  /** Подключение к бд */
  public function __construct()
  {
    $this->mysqli = new mysqli($this->host, $this->user, $this->passw, $this->db);
    
    if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
    }
    
    $this->mysqli->query('SET NAMES "utf8"');
  }
  
  /** Авторизация пользователя */
  public function authUser($data)
  {
     if (!($stmt = $this->mysqli->prepare("SELECT * FROM users WHERE login = (?)"))) {
        echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        exit();
    }
    
    if (!$stmt->bind_param("s", $data['login'])) {
        echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    if (!$stmt->execute()) {
        echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    $res = $stmt->get_result();
    
    if($res->num_rows == 0)
    {
      return false;
    }
    
    $row = $res->fetch_assoc();
    
    //print_r($row);exit;
    
    if($row['password'] != crypt($data['password'], $row['salt']))
    {
      return false;
    }
    
    //session_start();
    
    $_SESSION['user_id'] = $row['id'];
    
    return $row;
  }
  
  /** Получаем пользователя по id */
  public function getUser($id=0)
  {
    if (!($stmt = $this->mysqli->prepare("SELECT * FROM users WHERE id = (?)"))) {
        echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        exit();
    }
    
    if (!$stmt->bind_param("i", $id)) {
        echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    if (!$stmt->execute()) {
        echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    $res = $stmt->get_result();
    
    return $res->fetch_assoc();
  }
  
  /** Получаем список писем из папки */
  public function getMails($folder,$user_id, $id)
  {
    $where = ($id != 0) ? ' AND id = ? ' : 'AND id <> ?' ;
    if (!($stmt = $this->mysqli->prepare("SELECT * FROM mails WHERE folder = (?) AND user_id = ? {$where}"))) {
        echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        exit();
    }
    
    if (!$stmt->bind_param("sii", $folder,$user_id,$id)) {
        echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    
    if (!$stmt->execute()) {
        echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    $res = $stmt->get_result();
    
    if($res->num_rows == 0)
    {
      return false;
    }
    
    return $res->fetch_all(MYSQLI_ASSOC);
  }
  
  /** Добавление нового пользователя в бд */
  public function addUser($data)
  {
    $sql = 'INSERT INTO `users`
       (`login`,`password`,`salt`,`gmail_login`,`gmail_pass`)
       VALUE (?,?,?,?,?)';
    
    if (!($stmt = $this->mysqli->prepare($sql))) {
        echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        exit();
    }
    
    if (!$stmt->bind_param("sssss", $data['login'], $data['password'], $data['salt'], $data['gmail_login'], $data['gmail_pass'])) {
        echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    if (!$stmt->execute()) {
        echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    return $this->mysqli->insert_id;
  }
  
  /** Добавление данных в бд */
  public function addMails($inbox,$emails,$folder)
  {
    $sql = 'INSERT INTO `mails`
       (`user_id`,`subject`,`date_mail`,`message`,`from_mail`,`dmarc`,`folder`)
       VALUE (?,?,?,?,?,?,?)';
    
    if (!($stmt = $this->mysqli->prepare($sql))) {
        echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        exit();
    }
    
    $data['user_id'] = 1;
    $data['subject'] = 1;
    $data['date_mail'] = date('Y-m-d H:i:s');
    $data['message'] = 1;
    $data['from_mail'] = 1;
    $data['dmarc'] = 0;
    $data['folder'] = $folder;
    
    if (!$stmt->bind_param("issssis", $data['user_id'], $data['subject'], $data['date_mail'], $data['message'], $data['from_mail'], $data['dmarc'], $data['folder'])) {
        echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    $i=1;

    foreach($emails as $mail) 
    {
      
      $headerInfo = imap_headerinfo($inbox,$mail);
      
      $header = imap_fetchheader($inbox,$mail);
      
      
          
      //$body = imap_fetchbody($inbox, $mail, "1");
          
      $msg_structure = imap_fetchstructure($inbox, $mail);
      $msg_body 	   = imap_fetchbody($inbox, $mail, 1,FT_PEEK );

      //$msg_body2 = imap_body($inbox, $mail);
  
      $arr_subj = imap_mime_header_decode($headerInfo->subject);
      
      //print_r($header);
      

      $data['dmarc'] = (strripos($header, 'dmarc=pass')) ? 1 : 0;

      
      //echo '<hr>';
  
      $subject = '';
  
      foreach($arr_subj as $arr_subj_row)
      {
        if($arr_subj_row->charset != 'UTF-8' && $arr_subj_row->charset != 'default')
        {
          $subject .= iconv($arr_subj_row->charset,'UTF-8',$arr_subj_row->text);
          
          $body = imap_qprint($msg_body);
        }
        else
        {
          $subject .= $arr_subj_row->text;
          
          $body = $msg_body;
        }
      }
          
      $data['user_id'] = $_SESSION['user_id'];
      $data['subject'] = $subject;
      $data['date_mail'] = date('Y-m-d H:i:s', $headerInfo->udate);
      $data['message'] = $body;
      $data['from_mail'] = $headerInfo->from[0]->mailbox.'@'.$headerInfo->from[0]->host;
      $data['folder'] = $folder;

      if (!$stmt->execute()) {
        echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        exit();
      }
        
      $i++;
        
      if($i>20){break;}
    }
    
    //exit;   
    
  }
  
  /** Проверка существования login в бд */
  public function chekEmail($login)
  {
    if (!($stmt = $this->mysqli->prepare("SELECT login FROM users WHERE login = (?)"))) {
        echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        exit();
    }
    
    if (!$stmt->bind_param("s", $login)) {
        echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    if (!$stmt->execute()) {
        echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    $res = $stmt->get_result();
    
    return ($res->num_rows > 0) ? false : true;
  }
  
  /** Удаление Email */
  public function deleteMail($id)
  {
    if (!($stmt = $this->mysqli->prepare("DELETE FROM mails WHERE id = ?"))) {
        echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        exit();
    }
    
    if (!$stmt->bind_param("i", $id)) {
        echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
    
    if (!$stmt->execute()) {
        echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }
  }
}