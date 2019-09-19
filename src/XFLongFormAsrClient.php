<?php 
/**
 * @author Colin <legoo8@qq.com>
 */
namespace mclxly\xfyun;

class XFLongFormAsrClient extends XFClient
{
	private $upload_file_path;
	private $audio_language;

	public function __construct($appid, $secret_key, $upload_file_path, $audio_language = 'cn') {
	    parent::__construct($appid, $secret_key);
	    $this->upload_file_path = $upload_file_path;
	    $this->audio_language = $audio_language;
	}

	/**
	 * gene_params
   	 * @desc 根据不同的apiname生成不同的参数,本示例中未使用全部参数您可在官网()查看后选择适合业务场景的进行更换
	 * @link https://doc.xfyun.cn/rest_api/%E8%AF%AD%E9%9F%B3%E8%BD%AC%E5%86%99.html
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

		// $signa = $this->getSigna($this->appid, $this->secret_key, $ts);

		// must be IrrzsJeOFk1NGfJHW6SkHUoN9CU=
		$signa = $this->getSigna('595f23df', 'd9f4aa7ea6d94faca62cd88a28fd5234', '1512041814');
		echo $signa . PHP_EOL;

		return null;

		// ts = str(int(time.time()))
  //       m2 = hashlib.md5()
  //       m2.update((appid + ts).encode('utf-8'))
  //       md5 = m2.hexdigest()
  //       md5 = bytes(md5, encoding='utf-8')
  //       # 以secret_key为key, 上面的md5为msg， 使用hashlib.sha1加密结果为signa
  //       signa = hmac.new(secret_key.encode('utf-8'), md5, hashlib.sha1).digest()
  //       signa = base64.b64encode(signa)
  //       signa = str(signa, 'utf-8')
  //       file_len = os.path.getsize(upload_file_path)
  //       file_name = os.path.basename(upload_file_path)
  //       param_dict = {}
  //       # param_dict['language'] = 'cn'
  //       param_dict['language'] = 'en'
	}

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
}