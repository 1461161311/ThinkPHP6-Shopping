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
     * @param $likeKeys
     * @param $data
     * @param int $num
     * @param bool $field
     * @param $order
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getLists($likeKeys, $data, $order, $num = 10, $field = true)
    {
        // 判断是否使用搜索功能
        if (!empty($likeKeys)) {
            // 调用搜索器
            $res = $this->withSearch($likeKeys, $data);
        } else {
            $res = $this;
        }

        return $res->where("status", "<>", config("status.mysql.table_delete"))
            ->order($order)
            ->field($field)
            ->paginate($num);
    }


    /**
     * 查询商品
     * @param $where //查询条件
     * @param bool $field // 需要查询字段
     * @param int $limit // 分页数量
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalGoodsByCondition($where, $field = true, $limit = 5)
    {
        $order = [
            "id" => 'desc',
        ];

        $where["status"] = config("status.success");

        return $this->where($where)
            ->order($order)
            ->field($field)
            ->limit($limit)
            ->select();
    }


    /**
     * 自动运行方法
     * 前端首页运行轮播图时,调用该方法修改图片路径
     * @param $value
     * @return string
     */
    public function getImageAttr($value)
    {
        return "http://localhost:81" . $value;
    }

    /**
     * 自动运行方法
     * 商品页处理轮播图路径
     * @param $value
     * @return string[]
     */
    public function getCarouselImageAttr($value)
    {
        if (!empty($value)) {
            $value = explode(",", $value);  // explode():按照指定方法分割字符串
            $value = array_map(function ($v) {   //array_map():将函数作用到数组中每一个值上
                return "http://localhost:81" . $v;
            }, $value);
        }
        return $value;
    }


    /**
     * 根据分类 id 查询商品
     * @param $categoryId
     * @param bool $field
     * @param int $limit
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalGoodsFindInSetCategoryId($categoryId, $field = true, $limit = 10)
    {
        $order = [
            "id" => 'desc',
        ];

        return $this->whereFindInSet("category_path_id", $categoryId)
            ->where("status", "=", config("status.success"))
            ->order($order)
            ->field($field)
            ->limit($limit)
            ->select();

    }


    /**
     * 根据条件查询商品
     * @param $field // 查询字段
     * @param $where // 查询条件
     * @param $limit // 分页显示数量
     * @param $order // 排序方式
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getByCategoryId($field, $where, $limit, $order)
    {
        return $this->where($where)
            ->order($order)
            ->field($field)
            ->paginate($limit);
    }


}