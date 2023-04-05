<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Installer\RunType\Db;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Database\Setup\DataInterface;
use Weline\Framework\Output\Cli\Printing;
use Weline\Installer\Helper\Data;
use PDO;
use PDOException;

class InstallConfig
{
    protected Data $helper;

    protected Printing $printer;

    public function __construct()
    {
        $this->helper  = new Data();
        $this->printer = new Printing();
    }

    public function run(array $params): array
    {
        $tmp    = [];
        $msg    = '-------  数据库配置安装...  -------';
        $hasErr = false;
        if (empty($params)) {
            $hasErr = true;
            $msg    = '异常的$params参数';
            if (CLI) {
                $this->printer->error($msg, 'ERROR');
            }
            $tmp['-------  数据库配置安装...  -------'] = $msg . '【✖】';
        }
        unset($params['action']);
        $params['type'] = 'mysql';
        // 参数检测
        if (CLI) {
            $this->printer->note('数据库：1、参数检测...', '系统');
        }
        $tmp['数据库：1、参数检测...'] = '系统';
        $db_keys                     = DataInterface::db_keys;
        $paramsCheck                 = array_intersect_key($params, $db_keys);
        foreach ($db_keys as $db_key => $v) {
            if (!isset($paramsCheck[$db_key])) {
                $hasErr = true;
                $msg    = '数据库' . $db_key . '配置不能为空！示例：bin/m system:install --db-' . $db_key . '=demo';
                if (CLI) {
                    $this->printer->error($msg, '系统');
                    exit();
                }
                $tmp['缺少参数'] = $msg . '【✖】';
            }
        }
        // 数据库链接检测
        $db_conf = [
            'default' => $params['type'],
            'master'  => $params,
            'slaves'  => []
        ];
        if (CLI) {
            $this->printer->note('数据库：2、数据库链接检测...', '系统');
        }
        $tmp['数据库：2、数据库链接检测...'] = '系统';
        try {
            //初始化一个PDO对象
            $dbh = new PDO($params['type'] . ':host=' . $params['hostname'] . ';dbname=' . $params['database'], $params['username'], $params['password']);
            if (CLI) {
                $this->printer->success('PDO数据库链接检测通过', 'OK');
            }
            $tmp['PDO数据库链接检测通过'] = '【✔】';
            $dbh                          = null;
        } catch (PDOException $e) {
            if (CLI) {
                $this->printer->error('PDO数据库链接检测失败!' . 'Error: ' . $e->getMessage(), 'ERROR');
                exit();
            };
            $hasErr                        = true;
            $msg                           = 'PDO数据库链接检测失败!' . 'Error: ' . $e->getMessage();
            $tmp['PDO数据库链接检测失败!'] = $msg . '【✖】';
            return ['data' => $tmp, 'hasErr' => $hasErr, 'msg' => $msg . '【✖】'];
        }
        // 数据库信息安装
        if (CLI) {
            $this->printer->note('数据库：3、数据库信息安装...', '系统');
        }
        $tmp['数据库：3、数据库信息安装...'] = '系统';
        try {
            Env::getInstance()->setConfig('db', $db_conf);
            $msg = '数据库安装初始化成功【✔】';
            if (CLI) {
                $this->printer->success($msg, 'OK');
            }
            $tmp['初始化保存'] = $msg;
        } catch (Exception $exception) {
            $hasErr = true;
            $msg    = '数据库安装初始化失败' . '【✖】';
            if (CLI) {
                $this->printer->error($msg, 'ERROR');
                exit();
            }
            $tmp['初始化保存'] = $msg;
            return ['data' => $db_conf, 'hasErr' => $hasErr, 'msg' => $msg];
        }

        return ['data' => $tmp, 'hasErr' => $hasErr, 'msg' => '数据库配置...'];
    }
}
