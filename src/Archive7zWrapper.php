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
  public static function extensions(){
    $ret = static::supported_type();
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
    $ret = array_map(function ($str){
      $format = trim(substr($str, 19,9));
      $info = substr($str, 28);
      preg_match_all('/[a-z][a-z0-9]+\s|7z/', $info,$m);
      return [$format,$m[0]];}
      ,$ret);
    $ret = array_reduce($ret, function($s,$e){$s[$e[0]]=$e[1];return $s;},[]);
    foreach ($ret as $k=>$v) {
      unset($ret[$k]);
      if (preg_match('/zip|rar|7z|^lz|cpio|cab/i',$k)){
        $ret[strtolower($k)]=$v;
      }
    }
    return $ret;
  }
}