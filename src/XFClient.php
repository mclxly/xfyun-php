<?php 
/**
 * @author Colin <legoo8@qq.com>
 */
namespace mclxly\xfyun;

class XFClient
{
	protected $appid;
	protected $secret_key;

	public function __construct($appid, $secret_key) {
		$this->appid = $appid;
		$this->secret_key = $secret_key;
	}
}