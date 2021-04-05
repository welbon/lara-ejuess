### 配置
在 `config/filesystems.php` 的 `disks` 下添加

`
	'ess' => [
        'driver' => 'ess',
        'key'    => env('ESS_ACCESS_KEY'),
        'secret' => env('ESS_SECRET_KEY'),
        'bucket' => env('ESS_BUCKET'),
        'url'    => env('ESS_URL'),
    ],
`

