<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public static function generate($number, $payer, $country, $service_name, $amount, $date = null) {

        if(is_null($date)) {
            $date = date('d-m-Y', strtotime('now'));
        }

        $due_date = date('d-m-Y', strtotime($date. ' +3 days'));

        $currency = $country == 'kz' ? 'тг.':'руб.';
        $offset = $country == 'kz' ? 3.7:6.3;

        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pdf->setSourceFile('static/partner/docs/'.$country.'/new.pdf');
        $pdf->AddPage();
        $tplIdx = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tplIdx);

        $pdf->useTemplate($tplIdx, null, null, $size['width'], $size['height'], true);

        $pdf->SetFont('DejaVuSans','B',18);
        $pdf->SetXY(35, 90);
        $pdf->Write(0, 'СЧЕТ НА ОПЛАТУ № '.$number.' от '.$date);

        $pdf->SetFont('DejaVuSans',null ,10);
        $pdf->SetXY(36, 99);
        $pdf->Write(0, 'Срок оплаты '.$due_date);

        $pdf->SetXY(47.5, 40.5);
        $pdf->Write(0, $payer);

        $pdf->SetXY(26, 128+$offset);
        $pdf->Write(0, $service_name);

        $pdf->SetXY(154.5, 128+$offset);
        $pdf->Write(0, $amount);

        $pdf->SetXY(172.5, 128+$offset);
        $pdf->Write(0, $amount);

        $pdf->SetXY(172.5, 138.5+$offset);
        $pdf->Write(0, $amount);

        // $pdf->SetXY(174.5, 144+$offset);
        // $pdf->Write(0, $amount);

        $pdf->SetXY(77, 152+$offset);
        $pdf->Write(0, $amount.''.$currency);

        $pdf->SetFont('DejaVuSans','B' ,10);
        $pdf->SetXY(18, 157+$offset);
        $pdf->Write(0, $amount.''.$currency);

        return $pdf;
    }
}
