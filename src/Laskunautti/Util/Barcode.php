<?php

namespace Laskunautti\Util;
use \DateTime;
use \DateInterval;
use \Date;

class Barcode
{
    public function generate($config, $responseValues, $convertDueDate = true)
    {
        $target          = $config['barcode_target'];
        $barcodeLocation = $config['barcode_location'];
        $convertLocation = $config['convert_location'];

        if ($convertDueDate) {
            $date = new DateTime();
            $date->add(new DateInterval('P' . $responseValues['billDueDate'] . 'D'));
            $responseValues['billDueDateForBarcode'] = $date->format('ymd');
        } else {
            $date = new DateTime($responseValues['billDueDate']);
            $responseValues['billDueDateForBarcode'] = $date->format('ymd');
        }

        $reference = $responseValues['billReference'];
        $reference .= '271500';
        $mod = 98 - ($reference % 97);
        $rf = 'RF' . $mod . $responseValues['billReference'];

        $iban = substr($responseValues['senderIban'], 2, strlen($responseValues['senderIban']));
        $iban = str_replace(' ', '', $iban);

        $euros = 0;
        $cents = 0;

        if (strpos($responseValues['billTotal'], '.')) {
            $parts = explode('.', $responseValues['billTotal']);
            $euros = $parts[0];
            $cents = $parts[1];
        } elseif (strpos($responseValues['billTotal'], ',')) {
            $parts = explode(',', $responseValues['billTotal']);
            $euros = $parts[0];
            $cents = $parts[1];
        } else {
            $euros = $responseValues['billTotal'];
        }

        $euros = str_pad($euros, 6, '0', STR_PAD_LEFT);
        $cents = str_pad($cents, 2, '0', STR_PAD_LEFT);

        $rfref = substr($rf, 4, strlen($rf));
        $rfref = str_pad($rfref, 23, '0', STR_PAD_LEFT);

        $code = '5' . $iban . $euros . $cents . $rfref . $responseValues['billDueDateForBarcode'];

        /*
        IBAN: 16
        EUROT: 6
        SENTIT: 2
        RF: 23
        DUE: 6

        echo '5 ' . $iban . ' ' . $euros . ' ' . $cents . ' ' . $rfref . ' ' . $responseValues['billDueDateForBarcode'];

        Generointi:
        echo $rf . '<br/>';
        $rf1 = substr($rf, 0, 4);
        $rf2 = substr($rf, 4, strlen($rf));
        $rf3 = $rf2 . $rf1;
        echo $rf3 . '<br/>';

        Validointi, pitää olla 1
        $rf3 = str_replace('R', '27', $rf3);
        $rf3 = str_replace('F', '15', $rf3);
        echo $rf3 . '<br/>';
        $rf3 = $rf3 % 97;
        echo $rf3 . '<br/>';
        */

        shell_exec($barcodeLocation . ' -u "mm" -g "180x30" -o ' .$target . '/' . $code . '.eps -b "' . $code . '" -e "128c"');
        shell_exec($convertLocation . ' ' . $target . '/' . $code . '.eps ' . $target . '/' . $code . '.png');

        $barcode = file_get_contents($target . '/' . $code . '.png');

        unlink($target . '/' . $code . '.eps');
        unlink($target . '/' . $code . '.png');

        return base64_encode($barcode);
    }
}