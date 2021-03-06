<?php

namespace SystemUtil\Archiver;

use SystemUtil\Process;

class Archive7zWrapper {
  public static $command = '7z';
  public static function extract($file_path,$name, $env=null):string {
    $proc = new Process([static::$command,'-so','x', $file_path, $name],$env);
    $proc->run();
    $ret = $proc->getOutput();
    return $ret;
  }
  public static function list7z($file_path, $env=[]):string {
    $proc = new Process([Archive7zWrapper::$command,'l','-ba', $file_path], $env);
    $proc->run();
    $ret = $proc->getOutput();
    return $ret;
  }
  public static function extensions($pattern="tar|zip|rar|7z|^lz|cpio|cab"){
    $ret = static::supported_type();
    foreach ($ret as $k=>$v) {
      unset($ret[$k]);
      if (preg_match("/{$pattern}/i",$k)){
        $ret[strtolower($k)]=$v;
      }
    }
    $ext = [];
    array_walk_recursive($ret,function ($e)use(&$ext){$ext[]=$e; });
    $ext = array_map('trim',$ext);
    $ext = array_unique($ext);
    $ext = array_diff($ext,['apk','appx',"lzma86"]);
    return $ext;
  }
  public static function supported_type() {
    $proc = new Process([Archive7zWrapper::$command,'i']);
    $proc->run();
    $output = $proc->getOutput();
    preg_match_all('/Formats:(.*)Codecs:/s',$output,$matches);
    $ret = preg_split('/\n/', $matches[1][0]);
    $ret = array_filter($ret);
    // 
    $bin = array_map(function($e){ return preg_split('//',$e);},$ret);
    $bin = array_map(function($e){ return array_map('trim',$e);},$bin);
    $bin = array_map(function($e){ return array_map(function($f){return empty($f)?'0':'1';},$e);},$bin);
    $bin = array_map(function($e){ return join('',$e);},$bin);
    $bin = array_map(function($e){ return substr($e,0,24);},$bin);
    $bin = array_map(function($e){ return bindec($e);},$bin);
    $str_true = str_replace(',','',"1111,1111,1111,1111,1111,1111");
    $bin = array_reduce( $bin, function($ret, $e ){return $ret & $e; },bindec($str_true));
    $start = strpos(sprintf('%024s', decbin($bin)),'1');
    //
    $ret = array_map(function($str)use($start){
      return substr($str,$start-1);
    },$ret);


    $ret = array_map(function ($str){
      $format = trim(substr($str, 0,9));
      $info = substr($str, 9);
      preg_match_all('/[a-z][a-z0-9]+\s|7z\s/', $info,$m);
      return [$format,$m[0]];}
      ,$ret);
    $ret = array_reduce($ret, function($s,$e){$s[$e[0]]=$e[1];return $s;},[]);
    return $ret;
  }
}
