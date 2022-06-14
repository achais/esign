<?php

namespace Lmh\ESign\Foundation\Account;

use Lmh\ESign\Core\BaseClient;
use Lmh\ESign\Exceptions\HttpException;
use Lmh\ESign\Support\Collection;

class Client extends BaseClient
{
    /**
     * 创建个人账号
     *
     * @param $thirdPartyUserId
     * @param $name
     * @param $idType string 证件类型, 默认: CRED_PSN_CH_IDCARD
     * @param $idNumber
     * @param string $mobile
     * @param string $email
     * @return Collection|null
     *
     * @throws HttpException
     */
    public function createPersonAccount($thirdPartyUserId, $name, $idType, $idNumber, $mobile = null, $email = null): ?Collection
    {
        $url = '/v1/accounts/createByThirdPartyUserId';
        $params = [
            'thirdPartyUserId' => $thirdPartyUserId,
            'name' => $name,
            'idType' => $idType,
            'idNumber' => $idNumber,
            'mobile' => $mobile,
            'email' => $email,
        ];

        return $this->request('json', [$url, $params]);
    }

    /**
     * 查询个人信息 By 账户id
     *
     * @param $accountId
     * @return Collection|null
     *
     * @throws HttpException
     */
    public function queryPersonByAccountId($accountId): ?Collection
    {
        $url = '/v1/accounts/' . $accountId;

        return $this->request('get', [$url]);
    }

    /**
     * 查询个人信息 By 第三方id
     *
     * @param $thirdId
     * @return Collection|null
     * @throws HttpException
     */
    public function queryPersonByThirdId($thirdId): ?Collection
    {
        $url = '/v1/accounts/getByThirdId';
        $params = [
            'thirdPartyUserId' => $thirdId
        ];

        return $this->request('get', [$url, $params]);
    }

    /**
     * 更新个人信息
     *
     * @param $accountId
     * @param null $mobile
     * @param null $email
     * @param null $name
     * @param null $idType
     * @param null $idNumber
     * @return Collection|null
     * @throws HttpException
     */
    public function updatePersonByAccountId($accountId, $mobile = null, $email = null, $name = null, $idType = null, $idNumber = null): ?Collection
    {
        $url = '/v1/accounts/' . $accountId;
        $params = [
            'mobile' => $mobile,
            'email' => $email,
            'name' => $name,
            'idType' => $idType,
            'idNumber' => $idNumber,
        ];

        return $this->request('put', [$url, $params]);
    }

    /**
     * 静默签署授权
     *
     * @param $accountId
     * @param string|null $deadline 授权截止时间, 格式为yyyy-MM-dd HH:mm:ss，默认无限期
     * @return Collection|null
     * @throws HttpException
     */
    public function signAuth($accountId, $deadline = null): ?Collection
    {
        $url = '/v1/signAuth/' . $accountId;
        $params = [
            'deadline' => $deadline,
        ];

        return $this->request('json', [$url, $params]);
    }

    /**
     * 取消静默签署授权
     *
     * @param $accountId
     * @return Collection|null
     * @throws HttpException
     */
    public function cancelSignAuth($accountId): ?Collection
    {
        $url = "/v1/signAuth/{$accountId}";

        return $this->request('delete', [$url]);
    }
}