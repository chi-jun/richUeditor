<?php
/**
 * 上传附件和上传视频
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";
if (is_file('../../../../vendor/autoload.php')) {
require_once '../../../../thinkphp/library/think/Console.php';
require_once '../../../../vendor/autoload.php';
}
use OSS\OssClient;
use OSS\Core\OssException;
$ossClient = new OssClient('LTAI4FtQzBrj9YxDnF11MsE5', 'suyjnUpDjeTaseu8i95xt8FmtfEH7F', 'oss-cn-shenzhen.aliyuncs.com');
/* 上传配置 */
$base64 = "upload";
switch (htmlspecialchars($_GET['action'])) {
    case 'uploadimage':
        $config = array(
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadscrawl':
        $config = array(
            "pathFormat" => $CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
        $config = array(
            "pathFormat" => $CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'uploadfile':
    default:
        $config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}
$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
$len = strlen($str)-1;
$rand_num = mt_rand(0, $len);
$type = pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION);
$object = 'uediter/'.time().$rand_num.'.'.$type;
$filePath = $_FILES[$fieldName]['tmp_name'];

$res = $ossClient->uploadFile('grade-file', $object, $filePath);
return json_encode(array(
    "state" => 'SUCCESS',
    "url" => $res['info']['url'],
    "title" => $res['info']['url'],
    "original" => $_FILES[$fieldName]['name'],
    "type" => $type,
    "size" => $_FILES[$fieldName]['size'],
));

/* 生成上传实例对象并完成上传 */
// $up = new Uploader($fieldName, $config, $base64);

/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */

/* 返回数据 */
// return json_encode($up->getFileInfo());
