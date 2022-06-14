<?php

namespace Lmh\ESign\Foundation\SignFlow;

use Lmh\ESign\Core\BaseClient;
use Lmh\ESign\Exceptions\HttpException;
use Lmh\ESign\Support\Collection;

class Client extends BaseClient
{

    /**
     * 一步发起签署
     *
     * @param array $params
     * @return Collection|null
     * @throws HttpException
     */
    public function createFlowOneStep(array $params): ?Collection
    {
        $url = '/api/v2/signflows/createFlowOneStep';
        return $this->request('json', [$url, $params]);
    }
    /**
     * 签署流程创建
     *
     * @param $businessScene
     * @param $noticeDeveloperUrl
     * @param $autoArchive
     * @return Collection|null
     * @throws HttpException
     */
    public function createSignFlow($businessScene, $noticeDeveloperUrl = null, $autoArchive = true): ?Collection
    {
        $url = '/v1/signflows';
        $params = [
            'autoArchive' => $autoArchive,
            'businessScene' => $businessScene,
            'configInfo' => [
                'noticeDeveloperUrl' => $noticeDeveloperUrl
            ]
        ];
        return $this->request('json', [$url, $params]);
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
    public function addDocuments($flowId, $fileId, $encryption = 0, $fileName = null, $filePassword = null): ?Collection
    {
        $url = '/v1/signflows/' . $flowId . '/documents';
        $params = [
            'docs' => [
                ['fileId' => $fileId, 'encryption' => $encryption, 'fileName' => $fileName, 'filePassword' => $filePassword],
            ]
        ];
        return $this->request('json', [$url, $params]);
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
    public function addPlatformSign(string $flowId, $fileId, $sealId, $posPage, $posX, $posY, $signDateBeanType = 0, $signDateBean = null, $signType = null): ?Collection
    {
        $url = '/v1/signflows/' . $flowId . '/signfields/platformSign';
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
        return $this->request('json', [$url, $params]);
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
    public function addAutoSign($flowId, $fileId, $authorizedAccountId, $sealId, $posPage, $posX, $posY, $signDateBeanType = 0, $signDateBean = null, $signType = null): ?Collection
    {
        $url = '/v1/signflows/' . $flowId . '/signfields/autoSign';
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
        return $this->request('json', [$url, $params]);
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
    public function addHandSign($flowId, $fileId, $signerAccountId, $posPage, $posX, $posY, $signDateBeanType = 0, $signDateBean = null): ?Collection
    {
        $url = '/v1/signflows/' . $flowId . '/signfields/handSign';
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
        return $this->request('json', [$url, $params]);
    }

    /**
     * 流程开始
     *
     * @param $flowId
     * @return Collection|null
     * @throws HttpException
     */
    public function startSignFlow(string $flowId): ?Collection
    {
        $url = '/v1/signflows/' . $flowId . '/start';
        return $this->request('put', [$url]);
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
    public function getExecuteUrl(string $flowId, $accountId, $orgId = null, $urlType = 0, $appScheme = null): ?Collection
    {
        $url = '/v1/signflows/' . $flowId . '/executeUrl';
        $params = [
            'accountId' => $accountId,
            'organizeId' => $orgId,
            'urlType' => $urlType,
            'appScheme' => $appScheme
        ];
        return $this->request('get', [$url, $params]);
    }
}