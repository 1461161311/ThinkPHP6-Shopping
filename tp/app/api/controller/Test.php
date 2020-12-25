<?php

namespace app\api\controller;

use app\BaseController;

class Test extends BaseController
{

    public function redis()
    {
        $roll = [];
        $va = [];
        // 将数组拿回来
        $arr = config("sms");
        // 遍历
        foreach ($arr as $key => $value) {
            if ($key == 'roll') {
                $roll = $value;
            } else if ($key == 'value') {
                $va = $value;
            }
        }

        dump("遍历结果");
        dump($roll);
        dump($va);

        for ($i = 0; $i <= 1; $i++) {
            $a = self::Choose($va, $roll, 1);

            // 随机获得的字符串
            dump($a);

            // 根据值获取键
            $key = array_search($a['0'], $va);

            dump($key);

            // 根据key删除value
            unset($roll[$key]);
            unset($va[$key]);

            $roll = array_values($roll);
            $va = array_values($va);


            dump($roll);
            dump($va);
        }


    }

    /**
     * 获取浮点随机数
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function randomFloat($min = 0, $max = 1)
    {

        // 0 + 随机数 / 最大随机数 * 1
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);


    }

    /**
     * 根据概率提取数组中元素
     * @param array $seq 待取数组
     * @param array $prob 对应每个元素概率（浮点数，和等于1）
     * @param int $k 重复次数
     * @return array
     */
    public static function Choose($seq, $prob, $k = 1)
    {
        $list = [];
        for ($i = 0; $i < $k; $i++) {
            // 生成随机数 0.45744
            $q = self::randomFloat();
            // 循环传入的数组长度次数
            for ($j = 0; $j <= count($seq); $j++) {
                // 所传入的随机数需要在每次循环的次数所对应的概率
                if (array_sum(array_slice($prob, 0, $j)) < $q && $q <= array_sum(array_slice($prob, 0, $j + 1))) {
                    // 如果在
                    $list[$i] = $seq[$j];
                    break;
                }
            }
        }
        $list['q'] = $q;
        return $list;
    }

}
