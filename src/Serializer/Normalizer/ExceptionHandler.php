<?php


declare(strict_types=1);

namespace App\Serializer\Normalizer;

use FOS\RestBundle\Serializer\Normalizer\ExceptionHandler as BaseExceptionHandler;
use JMS\Serializer\Context;

class ExceptionHandler extends BaseExceptionHandler
{

    /**
     * @param \Exception $exception
     * @param Context    $context
     *
     * @return array
     */
    protected function convertToArray(\Exception $exception, Context $context)
    {
        $data = [];

        $templateData = $context->attributes->get('template_data');
        if ($templateData->isDefined()) {
            $templateData = $templateData->get();
            if (array_key_exists('status_code', $templateData)) {
                $data['code'] = $statusCode = $templateData['status_code'];
            }
        }
        $data['message'] = $this->getExceptionMessage($exception, isset($statusCode) ? $statusCode : null);

        return $data;
    }
}
