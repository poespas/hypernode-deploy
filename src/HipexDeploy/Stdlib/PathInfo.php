<?php

namespace HipexDeploy\Stdlib;

use function Deployer\get;
use function Deployer\has;
use function Deployer\run;
use function Deployer\set;

class PathInfo
{
    /**
     * @return string
     */
    public static function getAbsoluteDomainPath(): string
    {
        if (!has('domain_path/realpath')) {
            $realpath = run('realpath {{domain_path}}');
            set('domain_path/realpath', $realpath);
        }
        return get('domain_path/realpath');
    }
}