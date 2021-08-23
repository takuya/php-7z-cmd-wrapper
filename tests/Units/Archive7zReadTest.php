<?php

namespace Tests\Units;

use Tests\TestCase;
use SystemUtil\Archiver\Archive7zReader;

class Archive7zReadTest extends TestCase {
  
  public function test_7z_list() {
    
    $glob = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/*.{zip,rar,7z}';
    foreach ( glob($glob,GLOB_BRACE ) as $f ) {
      $a = new Archive7zReader($f);
      foreach ( $a->list() as $item ) {
        $this->assertArrayHasKey('Date', $item);
        $this->assertArrayHasKey('Time', $item);
        $this->assertArrayHasKey('Attr', $item);
        $this->assertArrayHasKey('Size', $item);
        $this->assertArrayHasKey('Compressed', $item);
        $this->assertArrayHasKey('Name', $item);
      }
    }
  }
  
  public function test_cp932_zip(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-japanese-cp932-utf8.zip';
    $a = new Archive7zReader($f);
    foreach ( $a->files() as $name ) {
      $content = $a->content($name);
      $this->assertIsString($content);
    }
  }
  public function test_7z_extract_directory_to_stdout(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-has-dir.zip';
    $a = new Archive7zReader($f);
    $this->expectException(\InvalidArgumentException::class);
    $a->at(1);
  }
  public function test_7z_list_files(){
    $glob = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/*.{zip,rar,7z}';
    foreach ( glob($glob,GLOB_BRACE ) as $f ) {
      $a = new Archive7zReader($f);
      foreach ( $a->files() as $name ) {
        $content = $a->content($name);
        $this->assertIsString($content);
      }
    }
  }
  public function test_7z_list_only_files(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-has-dir.zip';
    $a = new Archive7zReader($f);
    $ret = $a->files();
    $this->assertEquals(2, sizeof($ret));
  }
  public function test_7z_check_has_directory_in_archive(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-has-dir.zip';
    $a = new Archive7zReader($f);
    $ret = $a->hasFolder();
    $this->assertTrue($ret);
  }
  public function test_7z_get_entry_md5_consistency(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-with-image.zip';
    $a = new Archive7zReader($f);
    $content = $a->content('DW41z4RUQAE9D8d.jpg');
    $this->assertEquals("cedefa2658efc2ac86d539f82895a123", md5($content));
  }
  public function test_7z_get_entry_with_name(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample.zip';
    $a = new Archive7zReader($f);
    $content = $a->content('a.txt');
    $this->assertIsString($content);
  }
  
  public function test_zip_cp932_to_text(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-japanese-cp932-utf8.zip';
    $a = new Archive7zReader($f);
    $a->setLang('ja_JP.SJIS');
    $file_name = $a->files()[1];
    $file_name =   $name = mb_convert_encoding($file_name,"UTF8","CP932");
    $content = $a->at(1);
    $content =   $name = mb_convert_encoding($content,"UTF8","CP932");
    $this->assertStringContainsString('あああああ',$content);
    $this->assertStringContainsString('日本語',$file_name);
  }
  
  public function test_zip_cp932_with_environment_variable_C_to_text(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-japanese-cp932-utf8.zip';
    $a = new Archive7zReader($f);
    $a->setLang('ja_JP.utf8');
    $file_name = $a->files()[1];
    $file_name =   $name = mb_convert_encoding($file_name,"UTF8","UTF8,CP932");
    $content = $a->at(1);
    $content =   $name = mb_convert_encoding($content,"UTF8","UTF8,CP932");
    $this->assertStringContainsString('あああああ',$content);
    $this->assertStringContainsString('日本語',$file_name);
    
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-japanese-cp932-utf8.zip';
    $a = new Archive7zReader($f);
    $a->setLang('ja_JP.utf8');
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-with-image.zip';
    $a = new Archive7zReader($f);
    $content = $a->content('DW41z4RUQAE9D8d.jpg');
    $this->assertEquals("cedefa2658efc2ac86d539f82895a123", md5($content));
    
  }
  
  public function test_zip_cp932_with_environment_variable_lang_c_to_binary(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-with-image.zip';
    $a = new Archive7zReader($f);
    $a->setLang('C');
    $content = $a->content('DW41z4RUQAE9D8d.jpg');
    $this->assertEquals("cedefa2658efc2ac86d539f82895a123", md5($content));
  }
  
  public function test_zip_cp932_with_environment_variable_lang_jp_to_binary(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-with-image.zip';
    $a = new Archive7zReader($f);
    $a->setLang('ja_JP.utf8');
    $content = $a->content('DW41z4RUQAE9D8d.jpg');
    $this->assertEquals("cedefa2658efc2ac86d539f82895a123", md5($content));
  }
  
  public function test_zip_cp932_with_environment_variable_lang_jp_to_text(){
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-japanese-cp932-utf8.zip';
    $a = new Archive7zReader($f);
    $a->setLang('ja_JP.utf8');
    $file_name = $a->files()[1];
    $file_name =   $name = mb_convert_encoding($file_name,"UTF8","UTF8,CP932");
    $content = $a->at(1);
    $content =   $name = mb_convert_encoding($content,"UTF8","UTF8,CP932");
    $this->assertStringContainsString('あああああ',$content);
    $this->assertStringContainsString('日本語',$file_name);
    
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-japanese-cp932-utf8.zip';
    $a = new Archive7zReader($f);
    $a->setLang('ja_JP.utf8');
    $f = __DIR__.DIRECTORY_SEPARATOR.'../sample-data/sample-with-image.zip';
    $a = new Archive7zReader($f);
    $content = $a->content('DW41z4RUQAE9D8d.jpg');
    $this->assertEquals("cedefa2658efc2ac86d539f82895a123", md5($content));
    
  }
}