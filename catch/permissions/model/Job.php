<?php
namespace catchAdmin\permissions\model;

use catcher\base\CatchModel;

class Job extends CatchModel
{
    protected $name = 'job';
    
    protected $field = [
            'id', // 
			'job_name', // 岗位名称
			'coding', // 编码
			'creator_id', // 创建人ID
			'status', // 1 正常 2 停用
			'sort', // 排序字段
			'description', // 描述
			'created_at', // 创建时间
			'updated_at', // 更新时间
			'deleted_at', // 删除状态，null 未删除 timestamp 已删除
			   
    ];  
}