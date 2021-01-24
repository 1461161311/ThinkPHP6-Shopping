<?php

namespace app\common\business;

use app\common\model\mysql\Category as CategoryModel;
use think\Exception;

class Category extends BaseBusiness
{
    public $model = null;

    /**
     * 构造函数，自动 new Model 类对象
     * CategoryBus constructor.
     */
    public function __construct()
    {
        $this->model = new CategoryModel();
    }

    /**
     * 添加分类
     * @param $data
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add($data)
    {
        // 设置分类状态码为 1 正常
        $data['status'] = config("status.mysql.table_normal");

        // 调用 Model 层方法查询分类名是否重复
        if ($this->model->getCategoryByname($data['name'])) {
            throw new \think\Exception("此分类名已存在");
        }

        // 保存数据
        try {
            $this->model->save($data);
        } catch (\Exception $exception) {
            return false;
        }
        // 返回 id
        return $this->model->id;
    }


    /**
     * 添加分类时，获取父类信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalCategorys($type = "admin")
    {
        // 需要获取的字段
        // 后端调用该方法时
        if ($type == "admin") {
            $field = "id,name,pid";
            // 前端api调用该方法时
        } elseif ($type == "api") {
            $field = "id as category_id,name,pid";
        }
        // 调用 model 层方法
        try {
            $result = $this->model->getNormalCategorys($field);
        } catch (\Exception $exception) {
            throw new \think\Exception(config("status.error"), $exception->getMessage());
        }
        if (!$result) {
            return [];
        }
        // 将对象转换成数组
        $result = $result->toArray();
        return $result;
    }


    /**
     * 分页查询分类
     * @param $data
     * @param $num
     * @return array
     */
    public function getLists($data, $num)
    {
        // 调用 model 层方法
        try {
            $list = $this->model->getLists($data, $num);
        } catch (\Exception $exception) {
            return \app\common\lib\Arr::getPaginateDefaultData(3);
        }

        // 将对象转换成数组
        $result = $list->toArray();
        // 将分页接口数据存入数组
        $result['render'] = $list->render();

        // 获取列表的数据，取出 id 字段
        if (!$result['data']) {
            throw new \think\Exception("数据获取错误");
        }
        $pids = array_column($result['data'], "id");

        // 当获取到数据时
        if ($pids) {
            // 调用 model 层方法,查询父分类有多少子分类.
            try {
                $idCountResult = $this->model->getChildCountInPids(['pid' => $pids]);
            } catch (\Exception $exception) {
                throw new \think\Exception("status.error", $exception->getMessage());
            }
            $idCountResult = $idCountResult->toArray(); // 目前是二维数组

            // 组装数据,转换成一维数组,   父分类id  => 父分类所拥有的子分类数量
            $idCounts = [];
            foreach ($idCountResult as $countResult) {
                $idCounts[$countResult['pid']] = $countResult['count'];
            }
        }

        // 在 $result['data'] 中添加一条字段 childCount , 存储该分类所拥有的子分类数量
        if ($result['data']) {
            foreach ($result['data'] as $key => $value) {
                $result['data'][$key]['childCount'] = $idCounts[$value['id']] ?? 0;
            }
        }
        return $result;
    }


    /**
     * 更新排序字段
     * @param $id
     * @param $listorder
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function listorder($id, $listorder)
    {
        // 查询 id 是否存在
        $result = $this->getById($id);
        if (!$result) {
            throw new \think\Exception("不存在该条记录");
        }

        // 调用 model 层方法查询数据库
        $data = [
            "listorder" => $listorder,
        ];
        try {
            $result = $this->model->updateById($id, $data);
        } catch (\Exception $exception) {
            return false;
        }

        // 返回查询的数据
        return $result;
    }


    /**
     * 编辑分类
     * @param $id   // 要修改的分类 id
     * @param $data // 修改内容
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateCategory($id, $data)
    {
        $result = $this->getById($id);
        if (!$result) {
            throw new \think\Exception("不存在该条记录");
        }

        if ($data['pid'] == $result['pid'] && $data['name'] == $result['name'] && $data['path'] == $result['path']) {
            throw new \think\Exception("状态修改前和修改后一致");
        }

        $data = [
            'pid' => intval($data['pid']),
            'name' => $data['name'],
            'path' => $data['path'],
        ];

        try {
            $this->model->updateById($id, $data);
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * 递归查询分类父类
     * @param $id   // 传入要查询的分类 id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTree($id)
    {
        // 调用 model 方法查询数据库
        try {
            $res = $this->model->getTree($id);
        } catch (\Exception $exception) {
            throw new Exception("status.error", $exception->getMessage());
        }

        // 将获取的数据进行转换
        $result = [];
        foreach ($res as $key => $value) {
            if ($value != null) {
                $value = $value->toArray();
            }
            $result['data'][$key] = $value;
        }

        // 对数据进行倒序处理( array_reverse() 将数组中的值倒序排序 )
        $result['data'] = array_reverse($result['data']);
        return $result;
    }


    /**
     * 根据 pid 查询分类数据库，默认返回 id，name，pid 三个字段
     * @param int $pid // 可选参数。默认为0
     * @param string $field
     * @return array
     */
    public function getNormalByPid($pid = 0, $field = "id,name,pid")
    {
        try {
            $result = $this->model->getNormalByPid($pid, $field);
        } catch (\Exception $exception) {
            return [];
        }

        $result = $result->toArray();
        return $result;
    }

}
