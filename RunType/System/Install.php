<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Installer\RunType\System;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Setup\Data\Setup as DataSetup;
use Weline\Installer\Helper\Data;

class Install
{
    protected Data $data;

    /**
     * @var Printing
     */
    private Printing $printing;

    /**
     * @var DataSetup
     */
    private DataSetup $setup;

    public function __construct(
        Data      $data,
        DataSetup $setup,
        Printing  $printing
    )
    {
        $this->data     = $data;
        $this->printing = $printing;
        $this->setup    = $setup;
    }

    public function run(): array
    {
        $db_conf = Env::getInstance()->reload()->getDbConfig();
        if ($db_conf) {
            return ['data' => [], 'hasErr' => false, 'msg' => '-------  系统安装...  -------'];
        } else {
            throw  new Exception('请先安装数据库配置！');
        }
    }
}
