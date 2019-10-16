<?php 
/**
 * @author Colin <legoo8@qq.com>
 */
namespace mclxly\xfyun;

use GuzzleHttp\Client;

class XFLongFormAsrClient extends XFClient
{
  private $is_console = false;

  // host
  private $lfasr_host = 'http://raasr.xfyun.cn/api';

  // 请求的接口名
  private $api_prepare = '/prepare';
  private $api_upload = '/upload';
  private $api_merge = '/merge';
  private $api_get_progress = '/getProgress';
  private $api_get_result = '/getResult';

  // 请求头类型
  private $content_type_1 = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
  private $content_type_2 = 'Content-Type: multipart/form-data;';

  // 文件分片大小10M
  private $file_piece_sice = 10485760;
  // private $file_piece_sice = 100000;

  private $upload_file_path;
  private $audio_language;


  public function __construct($appid, $secret_key, $upload_file_path, $audio_language = 'cn') {
    parent::__construct($appid, $secret_key);
    $this->upload_file_path = $upload_file_path;
    $this->audio_language = $audio_language;
  }

  public function set_language($value = 'cn')
  {
    $this->audio_language = $value;
  }

  /**
   * gene_params
   * @desc 根据不同的apiname生成不同的参数,本示例中未使用全部参数您可在官网()查看后选择适合业务场景的进行更换
   * @link https://dhttps://www.xfyun.cn/doc/asr/lfasr/API.html#%E6%8E%A5%E5%8F%A3%E8%AF%B4%E6%98%8E
   * @param  mixed $apiname
   * @param  mixed $taskid
   * @param  mixed $slice_id
   *
   * @return array
   */
  public function gene_params($apiname, $taskid = null, $slice_id = null): ?array {
    $ts = time();
    // $temp = $this->appid . $ts;
    // echo $temp . PHP_EOL;
    // echo md5($temp) . PHP_EOL;

    $signa = $this->getSigna($this->appid, $this->secret_key, $ts);

    // // must be IrrzsJeOFk1NGfJHW6SkHUoN9CU=
    // $signa = $this->getSigna('595f23df', 'd9f4aa7ea6d94faca62cd88a28fd5234', '1512041814');
    // echo $signa . PHP_EOL;

    if (!file_exists($this->upload_file_path)) {
      // var_dump( file_exists($this->upload_file_path) );exit;
      return null;
    }

    $file_len = filesize($this->upload_file_path);
    if ($file_len < 100) {
      // var_dump( filesize($this->upload_file_path) );exit;
      return null;
    }

    $file_name = basename($this->upload_file_path);

    // build params
    $param_dict = [];
    $param_dict['language'] = $this->audio_language;

    switch ($apiname) {
      case $this->api_prepare:
        // slice_num是指分片数量，如果您使用的音频都是较短音频也可以不分片，直接将slice_num指定为1即可
        $slice_num = 1;
        if ($file_len % $this->file_piece_sice === 0) {
          $slice_num = 0;
        }

        $slice_num = floor($file_len / $this->file_piece_sice) + $slice_num;

        $param_dict['app_id'] = $this->appid;
        $param_dict['signa'] = $signa;
        $param_dict['ts'] = $ts;
        $param_dict['file_len'] = $file_len;
        $param_dict['file_name'] = $file_name;
        $param_dict['slice_num'] = $slice_num;
        break;

      case $this->api_upload:
        $param_dict['app_id'] = $this->appid;
        $param_dict['signa'] = $signa;
        $param_dict['ts'] = (string)$ts;
        $param_dict['task_id'] = $taskid;
        $param_dict['slice_id'] = $slice_id;
        break;

      case $this->api_merge:
        $param_dict['app_id'] = $this->appid;
        $param_dict['signa'] = $signa;
        $param_dict['ts'] = $ts;
        $param_dict['task_id'] = $taskid;
        // $param_dict['file_name'] = $file_name;
        break;
      
      case $this->api_get_progress:
      case $this->api_get_result:
        $param_dict['app_id'] = $this->appid;
        $param_dict['signa'] = $signa;
        $param_dict['ts'] = $ts;
        $param_dict['task_id'] = $taskid;
        break;

      default:
        # code...
        break;
    }

    return $param_dict;
  }

  # 请求和结果解析，结果中各个字段的含义可参考：https://doc.xfyun.cn/rest_api/%E8%AF%AD%E9%9F%B3%E8%BD%AC%E5%86%99.html
  public function gene_request($apiname, $data, $files = [], $headers = [])
  {
    // var_dump($data);
    if ($data === null) {
      return null;
    }

    $client = new Client();
    if ($apiname === $this->api_upload) {
      $content_type = [
        'Content-Type' => 'multipart/form-data;',
      ];

      if ($this->is_console) {
        echo $this->lfasr_host . $apiname . PHP_EOL;
        var_dump(array_merge($data, $files));
        var_dump(array_merge($headers, $content_type));
      }
 
      $data = static::curlPost($this->lfasr_host . $apiname, array_merge($data, $files), array_merge($headers, $content_type));

      // $response = $client->request('POST', $this->lfasr_host . $apiname, [
      // // $response = $client->request('POST', 'http://la6.test/xf', [
      //   'query' => $data,
      //   'headers' => array_merge($headers, $content_type),
      //   'multipart' => [
      //     // [
      //     //   'name'     => 'data',
      //     //   'contents' => json_encode($data),
      //     //   'headers'  => [ 'Content-Type' => 'application/json']
      //     // ],
      //     $files
      //   ],
      //   'debug' => true,
      // ]);

    } else {
      $content_type = [
        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
      ];

      if ($this->is_console) {
        echo $this->lfasr_host . $apiname . PHP_EOL;
        var_dump(array_merge($data, $files));
        var_dump(array_merge($headers, $content_type));
      }

      $response = $client->request('POST', $this->lfasr_host . $apiname, [
      // $response = $client->request('POST', 'http://la6.test/xf', [
        'form_params' => $data,
        'headers' => array_merge($headers, $content_type),
        'debug' => false,
      ]);

      $data = json_decode($response->getBody(), true);
    }
    
    // var_dump($data);
    if ($data['ok'] == '0') {
      return $data;
    } else {
      // print("{} error:".$data);
      // exit(0);
      throw new ApiException($data['failed'], $data['err_no']);
    }
  }

  /**
   * prepare_request:预处理
   *
   * @return void
   */
  public function prepare_request()
  {
    return $this->gene_request($this->api_prepare, $this->gene_params($this->api_prepare));
  }

  public function upload_request($taskid, $upload_file_path)
  {
    $file_object = fopen($upload_file_path, 'rb');

    try {
      $index = 1;
      $sig = new SliceIdGenerator();

      while (1) {
        $content = fread($file_object, $this->file_piece_sice);

        if ($content === false || strlen($content) === 0) {
          if ($this->is_console) {
            // echo $content;
            // echo strlen($content);
            echo 'upload done' . PHP_EOL;
          }
          
          break;
        }

        // build file info
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($upload_file_path);

        $curlFile = curl_file_create(
            $upload_file_path,
            $mime,
            pathinfo(
                $upload_file_path,
                PATHINFO_BASENAME
            )
        );

        $files = [
          // "name" => $this->gene_params($this->api_upload)["slice_id"],
          // "name" => 'content',
          // "contents" => $content,
          // 'headers'  => [ 'Content-Type' => 'application/octet-stream']
          "content" => $curlFile,
        ];

        $response = $this->gene_request($this->api_upload,
                      $this->gene_params($this->api_upload, $taskid, $sig->getNextSliceId()),
                      $files);

        if ($response['ok'] != 0) {
          # 上传分片失败
          if ($this->is_console) {
            print('upload slice fail, response: ' . $response);
          }

          return false;
        }

        if ($this->is_console) {
          print('upload slice ' . $index . ' success');
        }

        $index += 1;
      }
    } catch (\Throwable $th) {
      //throw $th;

      if ($this->is_console) {
        // var_dump($th);
        echo $th->getMessage();
      }
    } finally {
      if ($this->is_console) {
        echo 'file index:' . ftell($file_object);
      }

      fclose($file_object);
    }

    return true;
  }

  /**
   * merge_request: 合并
   *
   * @param  mixed $taskid
   *
   * @return void
   */
  public function merge_request($taskid)
  {
    return $this->gene_request($this->api_merge, $this->gene_params($this->api_merge, $taskid));
  }

  /**
   * get_progress_request: 获取进度
   *
   * @param  mixed $taskid
   *
   * @return void
   */
  public function get_progress_request($taskid)
  {
    return $this->gene_request($this->api_get_progress, $this->gene_params($this->api_get_progress, $taskid));
  }

  /**
   * get_result_request: 获取结果
   *
   * @param  mixed $taskid
   *
   * @return void
   */
  public function get_result_request($taskid)
  {
    return $this->gene_request($this->api_get_result, $this->gene_params($this->api_get_result, $taskid));
  }

  public function all_api_request()
  {
    # 1. 预处理
    $pre_result = $this->prepare_request();
    // var_dump($pre_result);
    $taskid = $pre_result["data"];
    // $taskid = '8f78633fc7724d6189152bb13727433d';

    # 2 . 分片上传
    $this->upload_request($taskid, $this->upload_file_path);

    # 3 . 文件合并
    $this->merge_request($taskid);

    # 4 . 获取任务进度
    while (1) {
      $progress = $this->get_progress_request($taskid);
      $progress_dic = $progress;

      if ($this->is_console) {
        var_dump($progress_dic);
      }

      if ($progress_dic['err_no'] != 0 && $progress_dic['err_no'] != 26605){
        return;
      }else{
        $data = $progress_dic['data'];
        $task_status = json_decode($data, true);
        if ($task_status['status'] == 9){
          if ($this->is_console) {
            echo('The task ' . $taskid . ' is in processing, task status: ' . $task_status['desc'] . PHP_EOL);
          }
          break;
        }

        if ($this->is_console) {
          echo('The task ' . $taskid . ' is in processing, task status: ' . $task_status['desc'] . PHP_EOL);
        }

        sleep(5);
      }
    }

    # 5 . 获取结果
    $result = $this->get_result_request($taskid);
    // var_dump($result);
    return $result;
  }

  /**
   * getSigna: 计算签名
   *
   * @param  mixed $appid
   * @param  mixed $secret_key
   * @param  mixed $ts
   *
   * @return string
   */
  public function getSigna($appid, $secret_key, $ts): string
  {
    $ret = $appid . $ts;
    $md5 = md5($ret);
    // echo $md5 . PHP_EOL;

    $ret = hash_hmac('sha1', $md5, $secret_key, true);
    // echo $ret . PHP_EOL;
    $ret = base64_encode($ret);
    // echo $ret . PHP_EOL;

    return $ret;
  }

  /**
   * curl
   *
   * @param $url
   * @param string $postData
   * @param string $header
   * @return bool|string
   */
  public static function curlPost($url, $postData = '', $header = '')
  {
      //初始化
      $curl = curl_init(); //用curl发送数据给api
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_URL, $url);

      if (!empty($header)) {
          curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
      }

      if (!empty($postData)) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
      }

      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
      $response = curl_exec($curl);
      //关闭URL请求
      curl_close($curl);

      if ($this->is_console) {
        var_dump($response);
      }

      $data = json_decode($response, true);

      //显示获得的数据
      return $data;
  }
}