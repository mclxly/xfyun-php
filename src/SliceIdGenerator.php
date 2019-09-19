<?php 
/**
 * @author Colin <legoo8@qq.com>
 */
namespace mclxly\xfyun;

class SliceIdGenerator
{
	private $__ch;

    public function __construct() {
        $this->__ch = 'aaaaaaaaa`';
    }

    public function getNextSliceId(){
        $ch = $this->__ch;
        $j = strlen($ch) - 1;

        while ($j >= 0) {
        	$cj = $ch[$j];
        	if ($cj != 'z') {
        		$new_cj = chr((ord($cj) + 1)); 
        		$ch = substr($ch, 0, $j) . $new_cj . substr($ch, $j + 1);
                break;
        	} else {
        		$ch = substr($ch, 0, $j) . 'a' . substr($ch, $j + 1);
                $j = $j - 1;
        	}
        }

        $this->__ch = $ch;
        return $this->__ch;
    }
}