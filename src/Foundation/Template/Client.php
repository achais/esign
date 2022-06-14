<?php

namespace Lmh\ESign\Foundation\Template;

use Lmh\ESign\Core\BaseClient;
use Lmh\ESign\Exceptions\HttpException;
use Lmh\ESign\Support\Collection;

class Client extends BaseClient
{
    /**
     * @param $pageNum
     * @param $pageSize
     * @return Collection|null
     * @throws HttpException
     * @author lmh
     */
    public function getFlowTemplates($pageNum, $pageSize): ?Collection
    {
        $url = "/v3/flow-templates/basic-info";
        $params = [
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
        ];
        return $this->request('get', [$url, $params]);
    }
}