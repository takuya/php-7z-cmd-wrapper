<?php

namespace Tests\Units;

use Tests\TestCase;
use SystemUtil\Archiver\Archive7zReader;

class ArchiveTarGzReadTest extends TestCase {
  
  public function test_tar_gz_list(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample.tar.gz';
    $a = new Archive7zReader($f);
    $name = $a->list()[0]['Name'];
    $bin = $a->at(0);
    $temp_file = mktempfile($name);
    file_put_contents($temp_file,$bin);
    $b = new Archive7zReader($temp_file);
    $list = $b->list();
    $this->assertCount(3,$list);
  }
  
}