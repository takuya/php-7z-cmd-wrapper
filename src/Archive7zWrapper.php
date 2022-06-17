<?php

namespace SystemUtil\Archiver;

use SystemUtil\Process;

class Archive7zWrapper {
  public static $command;
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
}