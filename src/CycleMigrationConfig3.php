<?php

namespace App;

use Yiisoft\Aliases\Aliases;
use Yiisoft\Yii\Cycle\Config\BaseConfig;

/**
 * @property-read string $directory
 * @property-read string $namespace
 * @property-read string $table
 * @property-read bool   $safe
 *
 * @method string getNamespace()
 * @method string getTable()
 * @method bool getSafe()
 */
class CycleMigrationConfig3 extends BaseConfig
{
    protected $directory = '@root/migrations';
    protected $namespace = 'App\\Migration';
    protected $table = 'migration';
    protected $safe = false;

    /** @var Aliases */
    private $objAliases;

    public function __construct(Aliases $aliases)
    {
        $this->objAliases = $aliases;
    }

    protected function getDirectory(): string
    {
        return $this->convertAlias($this->directory);
    }

    protected function convertAlias(string $alias): string
    {
        return $this->objAliases->get($alias, true);
    }
}
