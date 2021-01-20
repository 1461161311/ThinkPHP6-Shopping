<?php

namespace app\common\model\mysql;

class Goods extends BaseModel
{

    /**
     * 查询条件表达式
     * 搜索器仅在调用 withSearch 方法时触发
     * @param $query
     * @param $value
     */
    public function searchTitleAttr($query, $value)
    {
        $query->where('title', 'like', '%' . $value . '%');
    }

    public function searchCreateTimeAttr($query, $value)
    {
        $query->whereBetweenTime('create_time', $value[0], $value[1]);
    }


    /**
     * 商品排序
     * @param int $num
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getLists($likeKeys, $data, $num = 10)
    {
        // 判断是否使用搜索功能
        if (!empty($likeKeys)) {
            // 调用搜索器
            $res = $this->withSearch($likeKeys, $data);
        } else {
            $res = $this;
        }

        $order = [
            "id" => 'by',
        ];

        return $res->where("status", "<>", config("status.mysql.table_delete"))
            ->order($order)
            ->paginate($num);
    }


}