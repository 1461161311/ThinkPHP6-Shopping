<?php

namespace app\common\model\mysql;

use think\Model;

class BaseModel extends Model
{
    /**
     * 自动写入时间，要求数据库字段必须为 create_time 和 update_time
     * @var bool
     */
    protected $autoWriteTimestamp = true;



    /**
     * 更新数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateById($id, $data)
    {
        $where = [
            "id" => $id,
        ];
        // 将当前时间写入数据库
        $data['update_time'] = time();
        return $this->where($where)->save($data);
    }

}