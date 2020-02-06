<?php
namespace catcher\base;

use catcher\CatchQuery;
use catcher\traits\db\BaseOptionsTrait;
use catcher\traits\db\TransTrait;
use think\model\concern\SoftDelete;

/**
 *
 * @mixin CatchQuery
 * Class CatchModel
 * @package catcher\base
 */
abstract class CatchModel extends \think\Model
{
    use SoftDelete;
    use TransTrait;
    use BaseOptionsTrait;

    protected $createTime = 'created_at';

    protected $updateTime = 'updated_at';

    protected $deleteTime = 'deleted_at';

    protected $defaultSoftDelete = 0;

    protected $autoWriteTimestamp = true;

    public const LIMIT = 10;

    // 开启
    public const ENABLE = 1;
    // 禁用
    public const DISABLE = 2;
}
