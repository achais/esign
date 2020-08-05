<?php

namespace Achais\ESign\SignFlow;

use Achais\ESign\Core\AbstractAPI;
use Achais\ESign\Exceptions\HttpException;
use Achais\ESign\Support\Collection;

class SignFlow extends AbstractAPI
{
    const NOTICE_TYPE_SMS = '1';
    const NOTICE_TYPE_EMAIL = '2';
    const NOTICE_TYPE_NULL = '';

    /**
     * 签署流程创建
     *
     * @param $businessScene
     * @param $noticeDeveloperUrl
     * @param $autoArchive
     * @return Collection|null
     * @throws HttpException
     */
    public function createSignFlow($businessScene, $noticeDeveloperUrl = null, $autoArchive = true)
    {
        $url = '/v1/signflows';
        $params = [
            'autoArchive' => $autoArchive,
            'businessScene' => $businessScene,
            'configInfo' => [
                'noticeDeveloperUrl' => $noticeDeveloperUrl
            ]
        ];
        return $this->parseJSON('json', [$url, $params]);
    }

    /**
     * 流程文档添加
     *
     * @param $flowId
     * @param $fileId
     * @param int $encryption
     * @param null $fileName
     * @param null $filePassword
     * @return Collection|null
     * @throws HttpException
     */
    public function addDocuments($flowId, $fileId, $encryption = 0, $fileName = null, $filePassword = null)
    {
        $url = "/v1/signflows/{$flowId}/documents";
        $params = [
            'docs' => [
                ['fileId' => $fileId, 'encryption' => $encryption, 'fileName' => $fileName, 'filePassword' => $filePassword],
            ]
        ];
        return $this->parseJSON('json', [$url, $params]);
    }

    /**
     * 添加平台自动盖章签署区
     *
     * @param $flowId
     * @param $fileId
     * @param $sealId
     * @param $posPage
     * @param $posX
     * @param $posY
     * @param $signDateBeanType
     * @param $signDateBean
     * @param $signType
     * @return Collection|null
     * @throws HttpException
     */
    public function addPlatformSign($flowId, $fileId, $sealId, $posPage, $posX, $posY, $signDateBeanType = 0, $signDateBean = null, $signType = null)
    {
        $url = "/v1/signflows/{$flowId}/signfields/platformSign";
        $signFieldOne = [
            'fileId' => $fileId,
            'sealId' => $sealId,
            'posBean' => [
                'posPage' => $posPage,
                'posX' => $posX,
                'posY' => $posY,
            ],
            'signDateBeanType' => $signDateBeanType,
            'signDateBean' => $signDateBean,
            'signType' => $signType,
        ];

        $params = [
            'signfields' => [
                $signFieldOne,
            ]
        ];
        return $this->parseJSON('json', [$url, $params]);
    }

    /**
     * 添加签署方自动盖章签署区
     *
     * @param $flowId
     * @param $fileId
     * @param $authorizedAccountId
     * @param $sealId
     * @param $posPage
     * @param $posX
     * @param $posY
     * @param int $signDateBeanType
     * @param null $signDateBean
     * @param null $signType
     * @return Collection|null
     * @throws HttpException
     */
    public function addAutoSign($flowId, $fileId, $authorizedAccountId, $sealId, $posPage, $posX, $posY, $signDateBeanType = 0, $signDateBean = null, $signType = null)
    {
        $url = "/v1/signflows/{$flowId}/signfields/autoSign";
        $signFieldOne = [
            'fileId' => $fileId,
            'authorizedAccountId' => $authorizedAccountId,
            'sealId' => $sealId,
            'posBean' => [
                'posPage' => $posPage,
                'posX' => $posX,
                'posY' => $posY,
            ],
            'signDateBeanType' => $signDateBeanType,
            'signDateBean' => $signDateBean,
            'signType' => $signType,
        ];

        $params = [
            'signfields' => [
                $signFieldOne,
            ]
        ];
        return $this->parseJSON('json', [$url, $params]);
    }

    /**
     * 添加手动盖章签署区
     *
     * @param $flowId
     * @param $fileId
     * @param $signerAccountId
     * @param $posPage
     * @param $posX
     * @param $posY
     * @param $signDateBeanType
     * @param $signDateBean
     * @return Collection|null
     * @throws HttpException
     */
    public function addHandSign($flowId, $fileId, $signerAccountId, $posPage, $posX, $posY, $signDateBeanType = 0, $signDateBean = null)
    {
        $url = "/v1/signflows/{$flowId}/signfields/handSign";
        $signFieldOne = [
            'fileId' => $fileId,
            'signerAccountId' => $signerAccountId,
            'posBean' => [
                'posPage' => $posPage,
                'posX' => $posX,
                'posY' => $posY,
            ],
            'signDateBeanType' => $signDateBeanType,
            'signDateBean' => $signDateBean,
        ];

        $params = [
            'signfields' => [
                $signFieldOne,
            ]
        ];
        return $this->parseJSON('json', [$url, $params]);
    }

    /**
     * 流程开始
     *
     * @param $flowId
     * @return Collection|null
     * @throws HttpException
     */
    public function startSignFlow($flowId)
    {
        $url = "/v1/signflows/{$flowId}/start";
        return $this->parseJSON('put', [$url]);
    }

    /**
     * 获取签署地址
     * @param $flowId
     * @param $accountId
     * @param null $orgId
     * @param int $urlType
     * @param null $appScheme
     * @return Collection|null
     * @throws HttpException
     */
    public function getExecuteUrl($flowId, $accountId, $orgId = null, $urlType = 0, $appScheme = null)
    {
        $url = "/v1/signflows/{$flowId}/executeUrl";
        $params = [
            'accountId' => $accountId,
            'organizeId' => $orgId,
            'urlType' => $urlType,
            'appScheme' => $appScheme
        ];
        return $this->parseJSON('get', [$url, $params]);
    }
}