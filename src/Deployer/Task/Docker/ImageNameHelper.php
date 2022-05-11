<?php

/**
 * @author Hypernode
 * @copyright Copyright (c) Hypernode
 */

declare(strict_types=1);

namespace Hypernode\Deploy\Deployer\Task\Docker;

use Hypernode\DeployConfiguration\Configuration;
use Hypernode\DeployConfiguration\Exception\EnvironmentVariableNotDefinedException;
use function Hypernode\Deploy\Deployer\getenvFallback;
use function Hypernode\DeployConfiguration\getenv;

class ImageNameHelper
{
    /**
     * @param Configuration $config
     * @param string        $suffix
     * @return string
     * @throws EnvironmentVariableNotDefinedException
     */
    public function getDockerImage(Configuration $config, string $suffix): string
    {
        try {
            $image = $config->getDockerImage();
            if (!$image) {
                $image = getenvFallback(['CI_PROJECT_PATH', 'BITBUCKET_REPO_SLUG']);
            }
        } catch (EnvironmentVariableNotDefinedException $e) {
            throw new EnvironmentVariableNotDefinedException(
                'Could not find image name, use `Hypernode\DeployConfiguration\Configuration::setDockerImage` ' .
                    'method, `CI_PROJECT_PATH` or `BITBUCKET_REPO_SLUG` env variable.'
            );
        }

        $tag = $this->getVersion();
        $image = rtrim($image, '/');
        $registry = $this->getImageRegistry($config);
        $registry = rtrim($registry, '/');
        $suffix = trim($suffix, '/');

        return  "$registry/$image/$suffix:$tag";
    }

    /**
     * @param Configuration $config
     * @return string
     * @throws EnvironmentVariableNotDefinedException
     */
    public function getImageRegistry(Configuration $config): string
    {
        try {
            $registry = $config->getDockerRegistry() ?? getenv('CI_REGISTRY');
            return rtrim($registry, '/');
        } catch (EnvironmentVariableNotDefinedException $e) {
            throw new EnvironmentVariableNotDefinedException(
                'Could not find image registry, use ' .
                    '`\Hypernode\DeployConfiguration\Configuration::setDockerRegistry` method or `CI_REGISTRY` env variable.'
            );
        }
    }

    /**
     * @return string
     * @throws EnvironmentVariableNotDefinedException
     */
    public function getVersion(): string
    {
        $commitSha = getenvFallback(['CI_COMMIT_SHA', 'BITBUCKET_COMMIT', 'GITHUB_SHA', 'CIRCLE_SHA1', 'COMMIT_SHA']);
        return substr($commitSha, 0, 8);
    }
}