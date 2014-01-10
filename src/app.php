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

    return $app['twig']->render('preview.twig',
        array(
            'senderIban' => $request->get('senderIban'),
            'senderSwift' => $request->get('senderSwift'),
            'senderName' => $request->get('senderName'),
            'senderEmail' => $request->get('senderEmail'),
            'senderWww' => $request->get('senderWww'),
            'senderAddress' => $request->get('senderAddress'),
            'senderZip' => $request->get('senderZip'),
            'senderCity' => $request->get('senderCity'),
            'senderYt' => $request->get('senderYt'),

            'payerName' => $request->get('payerName'),
            'payerAddress' => $request->get('payerAddress'),
            'payerZip' => $request->get('payerZip'),
            'payerCity' => $request->get('payerCity'),

            'payerDescription' => $request->get('payerDescription'),
            'payerDueDate' => $request->get('payerDueDate'),
            'payerTotal' => $request->get('payerTotal'),
        )
    );
})
->bind('preview');

return $app;