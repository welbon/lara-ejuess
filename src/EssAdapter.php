<?php

namespace Larapkg\Ejuess;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;
use App\Admin\Utils\FileUploader;

class EssAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    /**
     * @var EssClient
     */
    protected $essClient;

    /**
     * EssAdapter constructor.
     *
     * @param string $accessKey
     * @param string $secretKey
     * @param string $bucket
     * @param string $domain
     */
    public function __construct(EssClient $client)
    {
        $this->essClient = $client;
    }

    /**
     * 把 string 内容写入到$path
     * 
     * @param  string $path
     * @param  resource $resource
     * @param  Config
     * @return mixed
     */
    public function write($path, $contents, Config $config)
    {
        // $file = array_values($_FILES)[0];
        // $response = FileUploader::uploadImage($file['tmp_name'], $file['name']);

        // if(!$response['status']) {
        //     return [
        //         'http_code' => 200,
        //         'url'    => $this->essClient->getUrl($response['data']),
        //         'status' => true,
        //         'id'     => $response['data'],
        //     ];

        // }

        $response = $this->essClient->uploadContent($path, $contents);

        if ($response['status'] == false) {
            // throw new \Exception('Upload to remote ess error, msg: ' . $response['message']);
        }

        return $response;
    }

    /**
     * 把资源内容写入到$path
     * 
     * @param  string $path
     * @param  resource $resource
     * @param  Config
     * @return mixed
     */
    public function writeStream($path, $resource, Config $config)
    {
        
        // $file = array_values($_FILES)[0];
        // $response = FileUploader::uploadImage($file['tmp_name'], $file['name']);
        
        // if($response['status']) {

        //     return [
        //         'http_code' => 200,
        //         'url'    => $this->essClient->getUrl($response['data']),
        //         'status' => true,
        //         'id'     => $response['data'],
        //     ];

        // }

        $response = $this->essClient->uploadContent($path, stream_get_contents($resource));

        if ($response['status'] == false) {
            // throw new \Exception('Upload to remote ess error, msg: ' . $response['message']);
        }

        return $response;
    }

    public function update($path, $contents, Config $config)
    {

        //@todo
    }

    public function updateStream($path, $resource, Config $config)
    {
        //@todo
    }

    public function rename($path, $newpath)
    {
        //@todo
    }

    public function copy($path, $newpath)
    {
        //@todo
    }

    public function delete($path)
    {
        //@todo
    }

    public function deleteDir($dirname)
    {
        //@todo
    }

    public function createDir($dirname, Config $config)
    {
        //@todo
    }

    public function has($path)
    {
        //@todo
        return false;
    }

    public function read($path)
    {

    }

    public function readStream($path)
    {
        
        //@todo
    }

    public function listContents($directory = '', $recursive = false)
    {
        //@todo
    }

    public function getMetadata($path)
    {
        //@todo
    }

    public function getSize($path)
    {
        //@todo
    }

    public function getMimetype($path)
    {
        //@todo
    }

    public function getTimestamp($path)
    {
        //@todo
    }

    // return the file url
    public function getUrl($path)
    {
        return $this->essClient->getUrl($path);
    }
}