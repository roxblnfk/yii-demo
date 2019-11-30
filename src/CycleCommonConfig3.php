<?php

namespace App;

use Yiisoft\Yii\Cycle\Config\BaseConfig;

/**
 * @property-read array $entityPaths
 * @property-read string $cacheKey
 *
 * @method array getEntityPaths()
 * @method string getCacheKey()
 */
class CycleCommonConfig3 extends BaseConfig
{
    protected $entityPaths = [];
    protected $cacheKey = 'Cycle-ORM-Schema';
}
