<?php

namespace Larapkg\Ejuess;

class EssClient
{
    /**
     * @var string
     */
    protected $accessKey;
    /**
     * @var string
     */
    protected $secretKey;
    /**
     * @var string
     */
    protected $bucket;
    /**
     * @var string
     */
    protected $domain;

    /**
     * @param $accessKey
     * @param $accessSecret
     * @param $bucket
     * @param $domain
     */
    public function __construct($accessKey, $secretKey, $bucket, $domain)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket = $bucket;
        $this->domain = $domain;
    }

    /**
     * 上传
     * 
     * @param  $localFile  file path
     * @param  $remoteFile  file path
     * @return array
     */
    public function upload($localFile, $remoteFile = '')
    {
        $fileId = $remoteFile ? : $this->createUuid();

        return $this->uploadContent($fileId, file_get_contents($localFile));
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function uploadContent($path, $contents)
    {
        $contentType = $this->getMimeType($contents);
        $contentMd5  = base64_encode(md5($contents, true));
        $filesize    = strlen($contents);
        $ctime       = date('D, d M Y H:i:s', strtotime('-8 hours')) . ' GMT';

        $hashHeaders = [
            'content-md5'  => $contentMd5,
            'content-type' => $contentType,
            'date'         => $ctime,
        ];

        $headers = [
            'authorization: ' . $this->getSign($hashHeaders, $path),
            'content-length: ' . $filesize,
            'content-md5: ' . $contentMd5,
            'content-type: ' . $contentType,
            'date: ' . $ctime,
        ];

        $url = $this->domain . '/' . $path;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $document = curl_exec($ch);
        $rinfo = curl_getinfo($ch);
        curl_close($ch);

        if ($rinfo['http_code'] != 200) {
            return [
                'http_code' => $rinfo['http_code'],
                'status'    => false,
                'message'   => $document
            ];
        } else {
            return [
                'http_code' => 200,
                'url'    => $url,
                'status' => true,
                'id'     => $path
            ];
        }
    }

    /**
     * 返回访问域名
     * NOTE： 此处暂时只认为图片，为防止图片过大，加了最大1200
     * 
     * @return string
     */
    public function getUrl($fileId)
    {
        return $this->domain . $fileId; //. '@imageView2/2/w/1200/h/1200';
    }

    /**
     * 生成uuid
     * 
     * @return string
     */
    private function createUuid()
    {
        //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));

        $uuid = substr($str, 0, 8) . '-';
        $uuid .= substr($str, 8, 4) . '-';
        $uuid .= substr($str, 12, 4) . '-';
        $uuid .= substr($str, 16, 4) . '-';
        $uuid .= substr($str, 20, 12);

        return $uuid;
    }

    /**
     * 生成authorize签名
     * 
     * @param  $hashHeaders
     * @param  $fileId
     * @return string
     */
    private function getSign($hashHeaders, $fileId)
    {
        if (empty($hashHeaders) || empty($fileId)) {
            return 'error';
        }

        $headerString = '';
        foreach ($hashHeaders as $key => $value) {
            if (strpos(strtolower($key), '-ess-') == false) {
                $headerString .= $value . "\n";
            } else {
                $headerString .= strtolower($key) . ':' . $value . "\n";
            }
        }

        $plainText  = "PUT\n" . $headerString . '/' . $this->bucket . '/' . $fileId;
        $hash       = hash_hmac('sha1', $plainText, $this->secretKey, true);
        $hashBase64 = base64_encode($hash);

        return 'ESS ' . $this->accessKey . ':' . $hashBase64;
    }

    /**
     * 检测文件 mime type
     * 
     * @param  file content
     * @return string mime type of given content
     */
    private function getMimeType($contents)
    {
        if (!function_exists('finfo_open')) {
            throw new \Exception('Not support fileinfo extension');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        return $finfo->buffer($contents);
    }
}