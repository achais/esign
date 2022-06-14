<?php

namespace Lmh\ESign\Foundation\File;

use Lmh\ESign\Core\BaseClient;
use Lmh\ESign\Exceptions\HttpException;
use Lmh\ESign\Support\Collection;

class Client extends BaseClient
{
    /**
     * @param $templateId
     * @param $name
     * @param $simpleFormFields
     * @return Collection|null
     * @throws HttpException
     */
    public function createByTemplateId($templateId, $name, $simpleFormFields): ?Collection
    {
        $url = '/v1/files/createByTemplate';
        $params = [
            'name' => $name,
            'templateId' => $templateId,
            'simpleFormFields' => $simpleFormFields,
        ];
        return $this->request('json', [$url, $params]);
    }

}