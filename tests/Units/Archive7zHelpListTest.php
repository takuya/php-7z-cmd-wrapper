<?php

namespace Tests\Units;

use Tests\TestCase;
use SystemUtil\Archiver\Archive7zWrapper;

class Archive7zHelpListTest extends TestCase {

  public function test_show_7z_help(){
    $ret = Archive7zWrapper::help();
    $this->assertStringContainsString("<Switches>\n",$ret);
    $this->assertStringContainsString("<Commands>\n",$ret);
  }
}
