<?php

namespace App\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExceptionNormalizer implements NormalizerInterface
{

    public function __construct() {
        die('sd');
    }
    public function normalize($object, $format = null, array $context = array())
    {
        return array('code' => 300);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \My\Exception;
    }
}
