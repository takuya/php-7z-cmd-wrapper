## 7-Zip command wrapper for reading

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

`7z` command will do **nothing** to encoding. So we should take care of encoding in php world.

cp932(jp/windows/filename) example.
```php
$a = new Archive7zReader($f);
$file_name = $a->files()[1];
$file_name =   $name = mb_convert_encoding($file_name,"UTF8","UTF8,CP932");
```



