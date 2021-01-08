<?php

namespace app\common\lib;

class Arr
{
    /**
     * 无限极分类
     * @param $data
     * @return array
     */
    public static function getTree($data)
    {
        $arr = [];
        foreach ($data as $value) {
            $arr[$value['category_id']] = $value;
        }
        $result = [];
        foreach ($arr as $key => $value) {
            if (isset($arr[$value['pid']])) {
                $arr[$value['pid']]['list'][] = &$arr[$key];
            } else {
                $result[] = &$arr[$key];
            }
        }
        return $result;
    }


    /**
     * 在首页显示多少分类数量
     * @param $data //传入的数据
     * @param int $firsCount    // 一级分类数量
     * @param int $secondCount  // 二级分类数量
     * @param int $threeCount   // 三级分类数量
     * @return array
     */
    public static function sliceTreeArr($data, $firsCount = 5, $secondCount = 3, $threeCount = 5)
    {
        // array_slice()：取出数组指定的元素。第一个参数：传入数组  第二个参数：从哪里开始  第三个参数：到哪里结束
        $data = array_slice($data, 0, $firsCount);
        foreach ($data as $key => $value) {
            if (!empty($value['pid'])) {
                $data[$key]['list'] = array_slice($value['list'], 0, $secondCount);
                foreach ($value['list'] as $key2 => $value2) {
                    if (!empty($value2['list'])) {
                        $data[$key]['list'][$key2]['list'] = array_slice($value2['list'], 0, $threeCount);
                    }
                }
            }
        }
        return $data;
    }

}