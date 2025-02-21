<?php

namespace Tests\Units;

use Tests\TestCase;
use SystemUtil\Archiver\Archive7zWrapper;

class Archive7zListSupportedTest extends TestCase {

  public function test_list_supported(){
    $ret = Archive7zWrapper::extensions();
    dump($ret);
    $this->assertContains('zip',$ret);
    $this->assertContains('tar',$ret);
    $this->assertContains('7z',$ret);
  }
}
