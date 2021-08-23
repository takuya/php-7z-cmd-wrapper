## 7-Zip command wrapper for reading
![<CircleciTest>](https://circleci.com/gh/takuya/php-7z-cmd-wrapper.svg?style=svg)


7z command supports several type of archive. 

## requirements

```
sudo apt list p7zip-full
```

rar will be installed as non-free
```
sudo apt list p7zip-rar # debian non-free
```
## usage 
Access with file name.
```php
$a7z = new Archive7zReader("sample.zip");
foreach( $a7z->files() as $name) {
    $bin = $a7z->conent($name);
}
```
Access with index.
```php
$a7z = new Archive7zReader("sample.zip");
$bin = $a7z->at(0);
```

### limitations 

currently, this project aimed at READING archive. write/append not yet.


### character encodings.

`7z` command will encode filenames by shell ENV[LANG].
So we should take care of encoding using shell env LANG='' and php string.

cp932(jp/windows/filename) example. #01 using utf8 
```php
$a = new Archive7zReader($f);
$a->setLang('ja_JP.UTF8');
$file_name = $a->files()[1];
$file_name =   $name = mb_convert_encoding($file_name,"UTF8","UTF8,CP932");
```
cp932(jp/windows/filename) example. #01 using cp932
```php
$a = new Archive7zReader($f);
$a->setLang('ja_JP.SJIS');
$file_name = $a->files()[1];
$file_name =   $name = mb_convert_encoding($file_name,"UTF8","CP932");
```





