<?php

namespace App\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;


class MeetingConverter implements ParamConverterInterface
{

    public function apply(Request $request, ParamConverter $configuration)
    {

    }

    public function supports(ParamConverter $configuration)
    {

    }
}
