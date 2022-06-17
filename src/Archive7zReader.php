<?php

namespace SystemUtil\Archiver;

class Archive7zReader  {
  
  protected $list;
  protected $_LANG;
  /**
   * @var string
   */
  protected $f_name;
  
  public function setLang($lang){
    $this->_LANG =$lang;
  }
  
  public function __construct (string $filename) {
    $this->f_name =  realpath($filename) ?: null;
    Archive7zWrapper::$command = Archive7zWrapper::$command ?? '7z';
  }
  public function at($index):string {
    $files = $this->files();
    if ( !array_key_exists($index,$files)){
      throw new \InvalidArgumentException('$index is not found or $index points to a directory');
    }
    $name = $files[$index];
    return $this->content($name);
  }
  public function content($name):string{
    $ret = Archive7zWrapper::extract($this->f_name, $name, $this->_LANG?['LANG' =>$this->_LANG]:[]);
    return $ret;
  }
  public function hasFolder():bool {
    $list = $this->list();
    return sizeof( array_filter($list,function($item){
      return preg_match('/D/', $item['Attr']);
    })) > 0;
  }
  public function files():array {
    $list = $this->list();
    return array_map(function($e){
      return $e['Name'];
    }, array_filter($list,function($item){
      return !preg_match('/D/', $item['Attr']);
    }))
    ;
  }
  
  public function list():array {
    if( $this->list ){
      return $this->list;
    }
    $ret = Archive7zWrapper::list7z($this->f_name,$this->_LANG?['LANG'=>$this->_LANG]:[]);
    $string_io = new \SplFileObject('php://memory','w+');
    $string_io->fwrite($ret);
    $list = $this->parseList($string_io);
    return $this->list= $list;
  }
  protected function parseList( \SplFileObject $sio):array {
    $list = [];
    foreach ( $sio as $line ) {
      if (empty($line)){
        continue;
      }
      preg_match( '/^(?<Date>.+?)\s+(?<Time>.+?)\s+(?<Attr>.+?)\s+(?<Size>.+?)\s+(?<Compressed>.+?)\s+(?<Name>.+?)\s+$/', $line, $m );
      $list[]= array_intersect_key( $m,
        [
          'Date' => null,
          'Time' => null,
          'Attr' => null,
          'Size' => null,
          'Compressed' => null,
          'Name' => null,
        ]
      );
    }
    return $list;
  }
}