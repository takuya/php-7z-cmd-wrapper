<?php

namespace SystemUtil\Archiver;

use Takuya\ProcOpen\ProcOpen;

class Archive7zWrapper {
  public static $command = '7z';
  public static function help($env=[]){
    return static::run('--help',$env);
  }
  public static function run($opts,$env=[]){
    $opts = is_array($opts) ? $opts : [$opts];
    $cmd = array_merge([static::$command],$opts);
    $popen = new ProcOpen($cmd,null,$env);
    $popen->start();
    return stream_get_contents($popen->stdout());
  }
  public static function extract($file_path,$name, $env=null):string {
    return static::run(['-so','x', $file_path, $name],$env);
  }
  public static function list7z($file_path, $env=[]):string {
    return static::run(['l','-ba', $file_path],$env);
  }
  public static function extensions(){
    $ret = static::supported_type();
    array_walk_recursive($ret,function ($e)use(&$ext){$ext[]=$e; });
    $ext = array_map('trim',$ext);
    $ext = array_unique($ext);
    $ext = array_diff($ext,['apk','appx',"lzma86"]);
    return $ext;
  }
  public static function parse_supported_type($output){
    preg_match_all('/Formats:(.*)Codecs:/s',$output,$matches);
    $ret = preg_split('/\n/', $matches[1][0]);
    $ret = array_values(array_filter($ret));
    //
    $ret = array_map(function ($e){
      return preg_match('/m\+/',$e)? preg_replace('/ w.{5} /','  ',$e):$e;
      },$ret);
    $ret = array_map(function($e){ return str_replace('.',' ',$e);},$ret);
    $ret = array_map('trim',$ret);
    $ret = array_map(function($e){ return str_replace('m+','  ',$e ); },$ret);
    $pos = strpos($ret[0],'  7z',);
    $ret = array_map(function($e)use($pos){ return substr($e,$pos); },$ret);
    $ret = array_map(function ($str){
      $format = trim(substr($str, 0,9));
      $info = substr($str, 9);
      preg_match_all('/[a-z][a-z0-9]+\s|7z\s/', $info,$m);
      return [$format,$m[0]];}
      ,$ret);
    $ret = array_reduce($ret, function($s,$e){$s[$e[0]]=$e[1];return $s;},[]);
    $ret = array_map(function ($e){ return array_map('trim',$e); },$ret);
    return $ret;
    
  }
  public static function supported_type() {
    $output = static::run('i');
    return self::parse_supported_type($output);
  }
}
