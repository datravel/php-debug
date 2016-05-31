<?php

namespace Debug\Monolog\Processor;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Post\PostBody;

class Guzzle
{
    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        if ($record['message'] instanceof RequestException || $record['message'] instanceof ConnectException) {
            if (!array_key_exists('extra', $record)) {
                $record['extra'] = [];
            }
            $request = $record['message']->getRequest();
            $body = $request->getBody();
            if ($body instanceof PostBody) {
                $postFields = $body->getFields();
                // find bad provider
                if (!empty($postFields['providers'][0])) {
                    $context['extra']['guzzleRequest']['providerId'] = (int)$postFields['providers'][0];
                }
            }
        }
    }
}