<?php

declare(strict_types=1);

namespace App\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\User;

class ExceptionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id'     => $object->getId(),
            'name'   => $object->getEmail(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }
}
