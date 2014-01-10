<?php

use Symfony\Component\HttpFoundation\Request;

$app = require __DIR__.'/bootstrap.php';

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
})
->bind('homepage');

$app->post('/preview', function (Request $request) use ($app) {

    $formFields = array(
        'senderIban',
        'senderSwift',
        'senderName',
        'senderEmail',
        'senderWww',
        'senderAddress',
        'senderZip',
        'senderCity',
        'senderYt',

        'payerName',
        'payerAddress',
        'payerZip',
        'payerCity',

        'payerDescription',
        'payerDueDate',
        'payerTotal',
        'payerBillNumber',
        'payerBillReference',
    );

    $responseValues = array();

    foreach($formFields as $field) {
        if ($request->get($field) !== '') {
            $responseValues[$field] = $request->get($field);
        }

    }

    return $app['twig']->render('preview.twig', $responseValues);
})
->bind('preview');

return $app;