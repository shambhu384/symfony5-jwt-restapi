<?php

namespace App\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;


class MeetingConverter implements ParamConverterInterface
{

    public function apply(Request $request, ParamConverter $configuration)
    {
        die($request->attributes->get('name'));
        $request->attributes->set($name, $object);
        $postdata = json_decode($request->getContent());
        $meeting = new Meeting();
        $meeting->setName($postdata->name);
        $meeting->setDescription($postdata->description);
        $meeting->setDateTime(new \DateTime($postdata->date));

        return $meeting;
    }

    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getConverter()) {
            return false;
        }

        return 'meeting.post.paramconverter' == $configuration->getConverter();
    }
}
