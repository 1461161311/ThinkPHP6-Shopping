<?php

namespace app\common\business;

use app\common\model\mysql\User as UserModel;
use app\common\lib\Str;
use app\common\lib\Time;

class User
{
    public $userObj = null;

    public function __construct()
    {
        $this->userObj = new UserModel();
    }

    /**
     * 用户登录功能
     * @param $data
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login($data)
    {
        // 查询缓存
        $redisCode = cache(config("redis.code_pre") . $data['phone_number']);
        // 验证数据
        if (empty($redisCode) || $redisCode != $data['code']) {
            throw new \think\Exception("不存在该验证码", config('status.code_not'));
        }

        // 根据用户手机号查询数据库用户信息
        $user = $this->userObj->getUserByPhoneNumber($data['phone_number']);

        // 如果没有查询到数据则添加数据
        if (empty($user)) {
            // 设置要保存的数据
            $username = "Saber_" . $data['phone_number'];
            $userData = [
                'username' => $username,
                'phone_number' => $data['phone_number'],
                'type' => $data['type'],
                'status' => config('status.mysql.table_normal'),
                'last_login_ip' => request()->ip(),
            ];

            // 保存数据至数据库
            try {
                $userId = $user->id;
                $this->userObj->save($userData);
            } catch (\Exception $exception) {
                throw new\think\Exception("数据库内部异常");
            }

            // 如果查询到数据则更新数据
        } else {
            // 设置要更新的数据
            $updateData = [
                "update_time" => time(),
                'last_login_ip' => request()->ip(),
            ];
            // 调用Model层方法进行更新操作
            try {
                $userId = $user->id;
                $username = $user->username;
                $this->userObj->updateById($userId, $updateData);
            } catch (\Exception $exception) {
                throw new\think\Exception("数据库内部异常");
            }

        }

        // 生成随机 Token
        $token = Str::getLoginToken($data['phone_number']);
        // 设置存放数据
        $redisData = [
            'id' => $userId,
            'username' => $username,
        ];
        // 将 Token 和数据以及生效时间存放 redis
        $result = cache(config("redis.token_pre") . $token, $redisData, Time::userLoginExpiresTime($data['type']));

        // 如果存放 redis 成功则返回包含 token 以及 username 值的 json 数据
        return $result ? ["token" => $token, "username" => $username] : false;

    }

    /**
     * 根据 id 查询数据库，返回用户数据
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalUserById($id)
    {
        // 调用模型层方法
        $user = $this->userObj->getUserById($id);
        // 验证数据
        if (!$user || $user->status != config("status.mysql.table_normal")) {
            return [];
        }
        return $user->toArray();
    }

    /**
     * 根据用户名查询数据库，返回用户数据
     * @param $username
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalUserByUsername($username)
    {
        // 调用模型层方法
        $user = $this->userObj->getUserByUsername($username);
        // 验证数据
        if (!$user || $user->status != config("status.mysql.table_normal")) {
            return [];
        }
        return $user->toArray();
    }

    /**
     * 更新用户个人信息
     * @param $id
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($id, $data)
    {
        // 验证用户 id 是否存在
        $user = $this->userObj->getUserById($id);
        if (!$user) {
            throw new \think\Exception("不存在该用户");
        }

        // 查询用户名是否重复
        $userResult = $this->getNormalUserByUsername($data['username']);
        if ($userResult && $userResult['id'] != $id) {
            throw new \think\Exception("该用户已存在");
        }

        // 调用 Model 层更新数据库
        return $this->userObj->updateById($id, $data);
    }


}

