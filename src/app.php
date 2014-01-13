<?php

use Symfony\Component\HttpFoundation\Request;

$app = require __DIR__.'/bootstrap.php';

$app->register(new DerAlex\Silex\YamlConfigServiceProvider(
    __DIR__ . '/../config/settings.yml'
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../logs/laskunautti.' . date("Y-m-d") . '.log',
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['config']['database']
));

/**
 * Fields that are fetched from the form.
 */
$formFields = array(
    'senderIban' => 'sender_iban',
    'senderSwift' => 'sender_swift',
    'senderName' => 'sender_name',
    'senderEmail' => 'sender_email',
    'senderWww' => 'sender_www',
    'senderAddress' => 'sender_address',
    'senderZip' => 'sender_zip',
    'senderCity' => 'sender_city',
    'senderYt' => 'sender_yt',

    'payerName' => 'payer_name',
    'payerAddress' => 'payer_address',
    'payerZip' => 'payer_zip',
    'payerCity' => 'payer_city',
    'payerYt' => 'payer_yt',

    'billDescription' => 'bill_description',
    'billDueDate' => 'bill_duedate',
    'billTotal' => 'bill_total',
    'billIncludesVat' => 'bill_includes_vat',
    'billVat' => 'bill_vat',
    'billNumber' => 'bill_number',
    'billReference' => 'bill_reference',
);

/**
 * IndexAction
 */
$app->get('/', function () use ($app) {

    return $app['twig']->render('index.twig');
})
->bind('homepage');

/**
 * PreviewAction
 */
$app->post('/esikatsele', function (Request $request) use ($app, $formFields) {

    $app['monolog']->addInfo('Preview loaded.');

    $responseValues = array();

    foreach($formFields as $field => $dbColumn) {
        if ($request->get($field) !== '') {
            $responseValues[$field] = $request->get($field);
        }

    }

    $date = new DateTime();
    $date->add(new DateInterval('P' . $request->get('billDueDate') . 'D'));
    $responseValues['billDueDate'] = $date->format('Y-m-d');

    $total = $responseValues['billTotal'];
    $vat = $responseValues['billVat'];

    if ($vat !== '0' && $vat !== '-1') {
        if (!$request->get('billIncludesVat')) {
            $responseValues['billVatAmount'] = ($total / 100) * $vat;
            $responseValues['billTotalWithVat'] = $total + ($total / 100) * $vat;
        } else {
            $responseValues['billVatAmount'] = $total - ($total * $vat) / ($total + $vat);
            $responseValues['billTotal'] = $total - $responseValues['billVatAmount'];
            $responseValues['billTotalWithVat'] = $total;
        }
    } else {
        $responseValues['billVatAmount'] = 0;
        $responseValues['billTotalWithVat'] = $total;
    }

    $responseValues['preview'] = true;

    return $app['twig']->render('invoice.twig', $responseValues);
})
->bind('preview');

/**
 * SaveAction
 */
$app->post('/tallenna', function (Request $request) use ($app, $formFields) {

    foreach($formFields as $field => $dbColumn) {
        if ($request->get($field) !== '') {
            $databaseValues[$dbColumn] = $request->get($field);
        }
    }

    $databaseValues['bill_includes_vat'] = $request->get('billIncludesVat') ? true : false;

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $hash = '';
    for ($i = 0; $i < 6; $i++) {
        $hash .= $characters[rand(0, strlen($characters) - 1)];
    }

    $date = new DateTime();
    $date->add(new DateInterval('P' . $request->get('billDueDate') . 'D'));
    $databaseValues['bill_duedate'] = $date->format('Y-m-d');

    $databaseValues['hash'] = $hash;

    $success = $app['db']->insert('invoice', $databaseValues);

    $id = $app['db']->lastInsertId();

    $app['session']->getFlashBag()->add(
        'info',
        array(
            'success' => $success,
            'id'      => $id,
            'hash'    => $hash,
        )
    );

    $app['monolog']->addInfo('Invoice saved: ' . $id . '/' . $hash);

    return $app->redirect('/', 301);
})
->bind('save');

/**
 * ViewAction
 */
$app->get('/lasku/{id}/{hash}', function ($id, $hash, Request $request) use ($app, $formFields) {

    $sql = "SELECT * FROM invoice WHERE id = ? AND hash = ?";
    $data = $app['db']->fetchAssoc($sql, array((int) $id, $hash));

    if (!$data) {
        return $app['twig']->render('notfound.twig');
    }

    foreach($formFields as $field => $dbColumn) {
        $responseValues[$field] = $data[$dbColumn];
    }

    $total = $responseValues['billTotal'];
    $vat = $responseValues['billVat'];

    if ($vat !== '0' && $vat !== '-1') {
        if (!$responseValues['billIncludesVat']) {
            $responseValues['billVatAmount'] = ($total / 100) * $vat;
            $responseValues['billTotalWithVat'] = $total + ($total / 100) * $vat;
        } else {
            $responseValues['billVatAmount'] = $total - ($total * $vat) / ($total + $vat);
            $responseValues['billTotal'] = $total - $responseValues['billVatAmount'];
            $responseValues['billTotalWithVat'] = $total;
        }
    } else {
        $responseValues['billVatAmount'] = 0;
        $responseValues['billTotalWithVat'] = $total;
    }

    $app['monolog']->addInfo('Invoice viewed: ' . $id . '/' . $hash);

    return $app['twig']->render('invoice.twig', $responseValues);
})
->bind('view');

/**
 * Finally, return the app.
 */
return $app;

