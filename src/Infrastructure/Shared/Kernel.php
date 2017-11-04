<?php

namespace Leos\Infrastructure\Shared;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir(): string
    {
        return dirname(__DIR__) . '/../../../var/cache/' .$this->environment;
    }

    public function getLogDir(): string
    {
        return dirname(__DIR__) . '/../../var/log';
    }

    public function registerBundles()
    {
        $contents = require dirname(__DIR__) . '/../../etc/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->setParameter('container.autowiring.strict_mode', true);
        $confDir = dirname(__DIR__) . '/../../etc';
        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir.'/packages/'.$this->environment)) {
            $loader->load($confDir.'/packages/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = dirname(__DIR__) . '/../UI/API/Resources';
        if (is_dir($confDir.'/routing/')) {
            $routes->import($confDir.'/routing/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        if (is_dir($confDir.'/routing/'.$this->environment)) {
            $routes->import($confDir.'/routing/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        $routes->import($confDir.'/routing'.self::CONFIG_EXTS, '/', 'glob');
    }
}
