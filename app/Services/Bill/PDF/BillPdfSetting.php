<?php

namespace App\Services\Bill\Pdf;

use App\Models\Bill;
use App\Models\BillLayoutType;
use App\Models\Customer;
use App\Models\UserTenant;
use App\Services\Time\TimeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait BillPdfSetting
{
    /**
     * @var int
     */
    public $bill;

    /**
     * @var array
     */
    public $templates;

    /**
     * @var array
     */
    public $templateVariables = [];

    /**
     * Default setting and templates
     */
    public $fee;
    public $logo;
    public $filename;
    public $currency;
    public $dateFormat;
    public $moneyFormat;
    public $email;
    public $phone;
    public $web;
    public $postcode;
    public $street;
    public $city;
    public $bill_settings;

    public $fontFamily;

    public $customCreator;
    public $customAuthor;
    public $customTitle;
    public $customSubject;
    public $customKeywords;

    /**
     * Table
     */
    public $headerTask      = 'Projekt/Task';
    public $headerComment   = 'Kommentar';
    public $headerTime      = 'Dauer/Anzahl';
    public $headerRate      = 'Satz';
    public $headerSum       = 'Summe';

    public $labelProject    = 'Projekt';
    public $labelTask       = 'Aufgabe';

    public $labelVat        = 'zzgl. 19% MwSt.';
    public $labelSum        = 'Gesamt Netto';
    public $labelTotal      = 'Gesamt Brutto';

    /**
     * @throws \Throwable
     */
    public function setCustomSetting()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $settings               = app('TenantSer')->getCompanySettingsByUserTenant($userTenant);
        $templates              = app('TenantSer')->getCompanyTemplates($userTenant);
        $this->templates        = $templates["bill"];
        $this->currency         = in_array($settings->currency->symbol, ["€", "$"]) ? $settings->currency->symbol : $settings->currency->code;
        $this->logo             = public_path($settings->logo);
        $this->fee              = $settings->fee;
        $this->filename         = $settings->filename;
        $this->dateFormat       = TimeService::convertMomentToPHPFormat($settings->date_format ?? config('company.date_format'));
        $this->moneyFormat      = $settings->money_format;
        $this->customCreator    = $settings->creator;
        $this->customAuthor     = $settings->author;
        $this->customTitle      = $settings->title;
        $this->customSubject    = $settings->subject;
        $this->customKeywords   = $settings->keywords;
        $this->email            = $settings->email;
        $this->phone            = $settings->phone;
        $this->web              = $settings->web;
        $this->postcode         = $settings->postcode;
        $this->street           = $settings->street;
        $this->city             = $settings->city;
        $this->bill_settings    = (array)$settings->bill_settings;
        $this->fontFamily       = ucfirst($settings->font->key);
        $this->labelVat         = "zzgl. {$settings->fee}% MwSt.";
    }

    /**
     * @param Bill $bill
     */
    public function setBill(Bill &$bill)
    {
        $this->bill = $bill;
    }

    /**
     * @return int
     */
    public function getBillId(): int
    {
        return $this->bill->id;
    }

    /**
     * @return \App\Models\Customer
     */
    public function getBillCustomer(): Customer
    {
        return $this->bill->customer;
    }

    /**
     * @return array
     */
    public function getTemplateVariables(): array
    {
        if (empty($this->templateVariables)) {
            $country = optional($this->bill->customer->country)->name;

            $this->templateVariables = [
                '{author}'              => $this->customAuthor,
                '{currency}'            => $this->currency,
                '{logo}'                => '<img src="'.$this->logo.'" alt="" style="max-height:30px;">',
                '{fee}'                 => $this->fee,

                '{email}'               => $this->email,
                '{phone}'               => $this->phone,
                '{web}'                 => $this->web,
                '{postcode}'            => $this->postcode,
                '{street}'              => $this->street,
                '{city}'                => $this->city,

                '{bill_settings.logo_position}' => isset($this->bill_settings['logo_position']) ? $this->bill_settings['logo_position'] : '',
                '{bill_settings.logo_height}' => isset($this->bill_settings['logo_height']) ? $this->bill_settings['logo_height'] : '',

                '{bill.id}'             => $this->bill->id,
                '{bill.invoice_number}' => $this->bill->invoice_number,
                '{bill.created_at}'     => $this->getCreatedAt(),

                '{customer.id}'         => $this->bill->customer->custom_id,
                '{customer.name}'       => $this->bill->customer->name,
                '{customer.contact}'    => $this->bill->customer->contact,
                '{customer.street}'     => $this->bill->customer->street,
                '{customer.house}'      => $this->bill->customer->house,
                '{customer.postcode}'   => $this->bill->customer->postcode,
                '{customer.city}'       => $this->bill->customer->city,
                '{customer.country}'    => $country == "Germany" ? null : $country,

                '{table.header.task}'   => $this->headerTask,
                '{table.header.comment}'=> $this->headerComment,
                '{table.header.logged}' => $this->headerTime,
                '{table.header.rate}'   => $this->headerRate,
                '{table.header.sum}'    => $this->headerSum,

                '{table.label.project}' => $this->labelProject,
                '{table.label.task}'    => $this->labelTask,

                '{table.label.vat}'     => $this->labelVat,
                '{table.label.sum}'     => $this->labelSum,
                '{table.label.total}'   => $this->labelTotal,
            ];
        }

        return $this->templateVariables;
    }

    /**
     * @param string    $template
     * @param Bill|null $bill
     *
     * @return string
     * @throws \Throwable
     */
    public function renderTemplate(string $template, Bill $bill = null)
    {
        if ($bill) {
            $this->setBill($bill);
        }

        return strtr($template, $this->getTemplateVariables());
    }

    /**
     * @param string|null $createdAt
     *
     * @return \Carbon\Carbon|string|null
     */
    public function getCreatedAt(string $createdAt = null)
    {
        if (is_null($createdAt)) {
            $createdAt = $this->bill->created_at;
        }

        $createdAt = TimeService::toUserLocalTime($createdAt);

        return Carbon::parse($createdAt)->format('Y-m-d');
    }

    /**
     * @param Bill|null $bill
     *
     * @return string
     * @throws \Throwable
     */
    public function getPdfFilename(Bill $bill = null)
    {
        return $this->renderTemplate($this->filename, $bill);
    }

    /**
     * @return bool
     */
    public function isPDFLong(): bool
    {
        return $this->bill->billLayout->bill_layout_type_id === BillLayoutType::BILL_LAYOUT_TYPES['Long']['id'];
    }

    /**
     * @param $number
     *
     * @return string
     */
    public function numberToFormat($number): string
    {
        return number_format($number, 2, $this->moneyFormat[1], $this->moneyFormat[0]);
    }

    /**
     * @param string $template
     *
     * @return string
     * @throws \Throwable
     */
    public function handlerForContent(string $template): string
    {
        $bill   = $this->bill;
        $isLong = $this->isPDFLong();

        return strtr($template, [
            '{TABLE}' => view('bill.table', compact('bill', 'isLong'))->render()
        ]);
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function replaceLinks(string $template): string
    {
        return str_replace(config('app.url'), public_path(), $template);
    }
}
