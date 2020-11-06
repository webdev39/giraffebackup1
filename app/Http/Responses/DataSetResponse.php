<?php

namespace App\Http\Responses;

use App\Http\Resources\CountryResource;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\FieldResource;
use App\Http\Resources\LanguageResource;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Cache;

class DataSetResponse implements Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function toResponse($request)
    {
        return response()
            ->view('dataset', ['data' => $this->transform()])
            ->header('Content-Type', 'application/javascript');
    }

    /**
     * A object transformer.
     *
     * @return array
     */
    protected function transform() : array
    {
        return Cache::remember('users', 360, function () {
            return [
                'config'            => $this->config(),
                'countries'         => $this->getCountries(),
                'currencies'        => $this->getCurrencies(),
                'fonts'             => $this->getFonts(),
                'languages'         => $this->getLanguages(),
                'view_types'        => $this->getViewTypes(),
                'budget_types'      => $this->getBudgetTypes(),
                'notify_types'      => $this->getNotifyTypes(),
                'bill_layout_types' => $this->getBillLayoutTypes(),
                'billing_statuses'  => $this->getBillingStatuses(),
                'customer_statuses' => $this->getCustomerStatuses(),
                'user_statuses'     => $this->getUserStatuses(),
                'reports'           => $this->getReports(),
                'time_zones'        => $this->getTimeZones(),
                'audio_sounds'      => $this->getAudioSounds()
            ];
        });
    }

    /**
     * Include config
     *
     * @return array
     */
    private function config()
    {
        $default = [
            'url'               => config('app.url'),
            'env'               => config('app.env'),
            'debug'             => config('app.debug'),
            'api' => [
                'pusher' => [
                    'broadcaster'   => 'pusher',
                    'key'           => config('broadcasting.connections.pusher.key'),
                    'auth_url'      => config('broadcasting.connections.pusher.options.auth'),
                    'cluster'       => config('broadcasting.connections.pusher.options.cluster'),
                ],
                'socket_io' => [
                    'broadcaster'   => 'socket.io',
                    'key'           => config('broadcasting.connections.pusher.key'),
                    'auth_url'      => config('broadcasting.connections.pusher.options.auth'),
                    'port'          => config('broadcasting.connections.pusher.options.port'),
                ],
                'sentry' => [
                    'dns'       => config('sentry.frontend.dns'),
                ],
                'vapid_public_key' => config('webpush.vapid.public_key')
            ]
        ];

        return $default;
    }

    /**
     * @return AnonymousResourceCollection
     */
    private function getFonts() : AnonymousResourceCollection
    {
        return FieldResource::collection(app('FieldRepo')->getFonts());
    }

    /**
     * @return AnonymousResourceCollection
     */
    private function getViewTypes(): AnonymousResourceCollection
    {
        return FieldResource::collection(app('FieldRepo')->getViewTypes());
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getCountries(): AnonymousResourceCollection
    {
        return CountryResource::collection(Country::all());
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getCurrencies(): AnonymousResourceCollection
    {
        return CurrencyResource::collection(Currency::all());
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getLanguages(): AnonymousResourceCollection
    {
        return LanguageResource::collection(Language::all());
    }

    /**
     * @return AnonymousResourceCollection
     */
    private function getBudgetTypes() : AnonymousResourceCollection
    {
        return FieldResource::collection(app('FieldRepo')->getBudgetTypes());
    }

    /**
     * @return AnonymousResourceCollection
     */
    private function getNotifyTypes() : AnonymousResourceCollection
    {
        return FieldResource::collection(app('FieldRepo')->getNotificationTypes());
    }

    /**
     * @return AnonymousResourceCollection
     */
    private function getBillLayoutTypes() : AnonymousResourceCollection
    {
        return FieldResource::collection(app('FieldRepo')->getBillLayoutTypes());
    }

    /**
     * @return AnonymousResourceCollection
     */
    private function getBillingStatuses(): AnonymousResourceCollection
    {
        return FieldResource::collection(app('FieldRepo')->getBillingStatuses());
    }

    /**
     * @return array
     */
    public function getCustomerStatuses(): array
    {
        return app('FieldRepo')->getCustomerStatuses();
    }

    /**
     * @return array
     */
    public function getUserStatuses() : array
    {
        return app('FieldRepo')->getUserStatuses();
    }

    /**
     * @return array
     */
    public function getReports()
    {
        return [
            'criteria'          => config('reports.options'),
            'defaultSelected'   => config('reports.default'),
        ];
    }

    /**
     * Get all time zones
     */
    private function getTimeZones()
    {
        $allTimeZone = timezone_identifiers_list();
        // For remove UTC time zone
        array_pop($allTimeZone);
        return $allTimeZone;
    }

    /**
     * List sounds for profile
     */
    private function getAudioSounds()
    {
        return [
            'finish_task' => [
                [
                    'name' => 'Sound 1',
                    'file' =>  "/audio/finish_task_1.wav"
                ], [
                    'name' => 'Sound 2',
                    'file' =>  "/audio/finish_task_2.wav"
                ], [
                    'name' => 'Sound 3',
                    'file' =>  "/audio/finish_task_3.wav"
                ]
            ]
        ];
    }
}
