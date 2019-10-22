# xfyun-php
讯飞开放平台语音识别接口之PHP实现

```
composer require mclxly/xfyun-php
```

# 语音识别模块

## 语音转写

### Usage
```PHP
use mclxly\xfyun\XFLongFormAsrClient;

$appid = 'xxx';
$secret_key = 'xxxx';
$upload_file_path = 'en_src.mp3';
$client = new XFLongFormAsrClient($appid, $secret_key, $upload_file_path);
var_dump($client);
$client->set_language('en'); // 'cn' | 'en
$result = $client->all_api_request();
var_dump($result);
```


