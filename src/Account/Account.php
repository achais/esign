<?php

namespace Achais\ESign\Account;

use Achais\ESign\Core\AbstractAPI;
use Achais\ESign\Exceptions\HttpException;
use Achais\ESign\Support\Collection;

class Account extends AbstractAPI
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
    public function createPersonAccount($thirdPartyUserId, $name, $idType, $idNumber, $mobile = null, $email = null)
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

        return $this->parseJSON('json', [$url, $params]);
    }

    /**
     * 查询个人信息 By 账户id
     *
     * @param $accountId
     * @return Collection|null
     *
     * @throws HttpException
     */
    public function queryPersonByAccountId($accountId)
    {
        $url = '/v1/accounts/' . $accountId;

        return $this->parseJSON('get', [$url]);
    }

    /**
     * 查询个人信息 By 第三方id
     *
     * @param $thirdId
     * @return Collection|null
     * @throws HttpException
     */
    public function queryPersonByThirdId($thirdId)
    {
        $url = '/v1/accounts/getByThirdId';
        $params = [
            'thirdPartyUserId' => $thirdId
        ];

        return $this->parseJSON('get', [$url, $params]);
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
    public function updatePersonByAccountId($accountId, $mobile = null, $email = null, $name = null, $idType = null, $idNumber = null)
    {
        $url = "/v1/accounts/{$accountId}";
        $params = [
            'mobile' => $mobile,
            'email' => $email,
            'name' => $name,
            'idType' => $idType,
            'idNumber' => $idNumber,
        ];

        return $this->parseJSON('put', [$url, $params]);
    }

    /**
     * 创建机构账号
     *
     * @param $thirdPartyUserId string 第三方平台标识, 如: 统一信用代码
     * @param $creatorAccountId string 创建者 accountId
     * @param $name string 机构名称
     * @param $idType string 证件类型, 默认: CRED_ORG_USCC
     * @param $idNumber string 证件号
     * @param null $orgLegalIdNumber string 企业法人证件号
     * @param null $orgLegalName string 企业法人名称
     * @return Collection|null
     * @throws HttpException
     */
    public function createOrganizeAccount($thirdPartyUserId, $creatorAccountId, $name, $idType, $idNumber, $orgLegalIdNumber = null, $orgLegalName = null)
    {
        $url = '/v1/organizations/createByThirdPartyUserId';
        $params = [
            'thirdPartyUserId' => $thirdPartyUserId,
            'creator' => $creatorAccountId,
            'name' => $name,
            'idType' => $idType,
            'idNumber' => $idNumber,
            'orgLegalIdNumber' => $orgLegalIdNumber,
            'orgLegalName' => $orgLegalName,
        ];

        return $this->parseJSON('json', [$url, $params]);
    }

    /**
     * 查询机构信息 by 账户id
     *
     * @param $orgId
     * @return Collection|null
     * @throws HttpException
     */
    public function queryOrganizeByOrgId($orgId)
    {
        $url = '/v1/organizations/' . $orgId;

        return $this->parseJSON('get', [$url]);
    }

    /**
     * 查询机构信息 by 第三方id
     *
     * @param $thirdId
     * @return Collection|null
     * @throws HttpException
     */
    public function queryOrganizeByThirdId($thirdId)
    {
        $url = '/v1/organizations/getByThirdId';
        $params = [
            'thirdPartyUserId' => $thirdId
        ];

        return $this->parseJSON('get', [$url, $params]);
    }

    /**
     * 更新机构信息
     *
     * @param $orgId
     * @param string|null $name
     * @param string|null $idType
     * @param string|null $idNumber
     * @param string|null $orgLegalIdNumber
     * @param string|null $orgLegalName
     * @return Collection|null
     * @throws HttpException
     */
    public function updateOrganizeByAccountId($orgId, $name = null, $idType = null, $idNumber = null, $orgLegalIdNumber = null, $orgLegalName = null)
    {
        $url = "/v1/organizations/{$orgId}";
        $params = [
            'name' => $name,
            'idType' => $idType,
            'idNumber' => $idNumber,
            'orgLegalIdNumber' => $orgLegalIdNumber,
            'orgLegalName' => $orgLegalName,
        ];

        return $this->parseJSON('put', [$url, $params]);
    }

    /**
     * 静默签署授权
     *
     * @param $accountId
     * @param string|null $deadline 授权截止时间, 格式为yyyy-MM-dd HH:mm:ss，默认无限期
     * @return Collection|null
     * @throws HttpException
     */
    public function signAuth($accountId, $deadline = null)
    {
        $url = "/v1/signAuth/{$accountId}";
        $params = [
            'deadline' => $deadline,
        ];

        return $this->parseJSON('json', [$url, $params]);
    }

    /**
     * 取消静默签署授权
     *
     * @param $accountId
     * @return Collection|null
     * @throws HttpException
     */
    public function cancelSignAuth($accountId)
    {
        $url = "/v1/signAuth/{$accountId}";

        return $this->parseJSON('delete', [$url]);
    }
}