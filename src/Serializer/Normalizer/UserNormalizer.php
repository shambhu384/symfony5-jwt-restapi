<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\JsonSerializationVisitor;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
/**
 * User normalizer
 */
//class UserNormalizer implements NormalizerInterface
class UserNormalizer implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id'     => $object->getId(),
            'name'   => $object->getName(),
            'groups' => array_map(
                function (User $group) {
                    return $group->getId();
                },
                $object->getGroups()
            )
        ];
    }
     */

    /**
     * {@inheritdoc}
    public function supportsNormalization($data, $format = null)
    {
        die(get_class($data));
        return $data instanceof User;
    }
     */
   public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'method' => 'onPreSerialize',
                'class' => 'App\\Entity\\User', // if no class, subscribe to every serialization
                'format' => 'json', // optional format
                'priority' => 100, // optional priority
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
die('sdfsd');
    }

public static function getSubscribingMethods()
    {
        return [
            [
               'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
               'type' => User::class,
               'format' => 'json',
               'method' => 'serializeUserToJson',
            ]
        ];
    }

    public function serializeUserToJson(JsonSerializationVisitor $visitor, User $user)
    {
        // custom user serialization
        $data = [
            'username' => $user->getUsername()
        ];

        // extra root check
        if ($visitor->getRoot() === null) {
            $visitor->setRoot($data);
        }
        return $data;
    }
}
