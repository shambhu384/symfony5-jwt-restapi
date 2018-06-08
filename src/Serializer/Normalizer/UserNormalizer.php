<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * User normalizer
 */
class UserNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id'     => $object->getId(),
            'name'   => $object->getName(),
            'groups' => array_map(
                function (Group $group) {
                    return $group->getId();
                },
                $object->getGroups()
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }
}
