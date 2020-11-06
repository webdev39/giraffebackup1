<?php

namespace App\Services\Bill\Pdf;

use App\Models\Bill;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class BillPdfService
{
    use BillPdfSetting;

    /**
     * BillPdfService constructor.
     * @throws \Throwable
     */
    public function __construct()
    {
        if (Auth::id()) {
            $this->setCustomSetting();
        }
    }

    /**
     * @param int $billId
     *
     * @return Bill|null
     * @throws \Exception
     */
    public function getBillData(int $billId): ?Bill
    {
        $bill = app('BillSer')->getBillWithRelations($billId);
        $sum  = 0;

        foreach ($bill->billTimers as $billTimer) {
            if ($billTimer['timer_billing_id']) {
                app('BillSer')->makeBillTimer($timerBilling, $billTimer, $sum);
            } else {
                $timerBilling = ['id' => null];

                app('BillSer')->makeBillTimer($timerBilling, $billTimer, $sum);

                $bill->timerBillings->push($timerBilling);
            }
        }

        $bill['sum']    = $sum;
        $bill['mwst']   = round(($sum / 100) * $this->fee,2);
        $bill['total']  = $bill['sum'] + $bill['mwst'];

        return $bill;
    }

    /**
     * @param Bill $bill
     *
     * @return string
     * @throws \Throwable
     */
    public function getPdfContent(Bill $bill)
    {
        // set bill data
        $this->setBill($bill);
        $htmlContent = $this->handlerForContent($this->templates['content']);

        $htmlWrapper = view('bill.wrapper', [
            'content' => $htmlContent,
            'font'    => $this->fontFamily
        ])->render();

        $htmlWrapper = $this->renderTemplate($htmlWrapper);
        $htmlWrapper = $this->replaceLinks($htmlWrapper);

        return App::make('dompdf.wrapper')->loadHTML($htmlWrapper)->stream();
    }
}
