<?php

$c = new AMQPConnection();
$c->setPort(6181);
//$c->setCaCert('/home/sasa/code/HumusAmqp/tests/test_certs/cacert.pem');
//$c->setCert('/home/sasa/code/HumusAmqp/tests/test_certs/public.pem');
//$c->setKey('/home/sasa/code/HumusAmqp/tests/test_certs/private.pem');
$c->setVerify(false);

$c->connect();
