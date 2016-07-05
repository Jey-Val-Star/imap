<?php
/**
 * Основной класс приложения 
 */
class Engine{
  
  public $title = ''; // Заголовок страницы
  
  public $action = '';
  
  /** Я использовал конструктор для дальнейшего выполнения приложения, 
    * но можно обойтись и без него - указав метод после создания объекта */
  public function __construct()
  {
    $this->rout();
  }
  
  /** Метод разбора адресной строки и определение методов и языков */
  private function rout()
  {
    $arr_uri =  explode('?',$_SERVER['REQUEST_URI']);
    $arr_uri = explode('/',trim(reset($arr_uri),'/'));
    
    $action = ($arr_uri[0]=='') ? 'index' : $arr_uri[0] ;

    unset($arr_uri);
    
    $this->getAction($action);
  }
  
  
  /** Проверка и обращение к методу или 404 */
  private function getAction($action)
  { 
    session_start();
    // А если метода нет - то 404
    if(method_exists($this, $action))
    {
      $this->$action();
    }
    else
    {
      $this->view('error_404');
    }
  }
  
  /** Метод вывода информации (рендеринг) */
  public function view($view, $data=null)
  {
    $path = __DIR__ . '/view/';
    
    include $path . 'header.php';
    
    include $path . $view.'.php';
    
    include $path . 'footer.php';
  }
  
  /** метод проверки данных */
  public function checkData($data=null,$key=null,$val=null)
  {
    return (isset($data[$key][$val])) ? $data[$key][$val] : null;
  }
  
  public function decodeMess($mess)
  {
    return quoted_printable_decode ($mess);
  } 
}