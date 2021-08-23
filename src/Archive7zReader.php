<?php

namespace SystemUtil\Archiver;

use SystemUtil\Process;

class Archive7zReader {
  
  public static $command;
  protected $list;
  protected $_LANG;
  
  public function setLang($lang){
    $this->_LANG =$lang;
  }
  
  public function __construct ($f) {
    $this->f_name =  realpath($f) ?: null;
    static::$command = static::$command ?? '7z';
  }
  public function at($index){
    $files = $this->files();
    if ( !array_key_exists($index,$files)){
      throw new \InvalidArgumentException('$index is not found or $index points a directory');
    }
    $name = $files[$index];
    return $this->content($name);
  }
  public function content($name){
    $ret = static::extract($this->f_name,$name,$this->_LANG?['LANG'=>$this->_LANG]:[]);
    return $ret;
  }
  public function hasFolder(){
    $list = $this->list();
    return sizeof( array_filter($list,function($item){
      return preg_match('/D/', $item['Attr']);
    })) > 0;
  }
  protected static function list7z($file_path, $env=[]){
    // TODO:: move to static class
    $proc = new Process([static::$command,'l','-ba', $file_path], $env);
    $proc->run();
    $ret = $proc->getOutput();
    return $ret;
  }
  public static function extract($file_path,$name, $env=null){
    $proc = new Process([static::$command,'-so','x', $file_path, $name],$env);
    $proc->run();
    $ret = $proc->getOutput();
    return $ret;
  }
  public function files(){
    $list = $this->list();
    return array_map(function($e){
      return $e['Name'];
    }, array_filter($list,function($item){
      return !preg_match('/D/', $item['Attr']);
    }))
    ;
  }
  
  public function list() {
    if( $this->list ){
      return $this->list;
    }
    $ret = static::list7z($this->f_name,$this->_LANG?['LANG'=>$this->_LANG]:[]);
    //dd($proc->getCommandLine());
    $string_io = new \SplFileObject('php://memory','w+');
    $string_io->fwrite($ret);
    $list = $this->parseList($string_io);
    return $this->list= $list;
  }
  protected function parseList( \SplFileObject $sio){
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