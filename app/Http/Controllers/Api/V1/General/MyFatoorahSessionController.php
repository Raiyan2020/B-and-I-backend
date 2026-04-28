<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Http;

class MyFatoorahSessionController extends Controller
{
    use ResponseTrait;
    private array $credentials;
    private string $mode;
    public function __construct()
    {
        $this->mode = config('services.myfatoorah.mode');
        $this->credentials = config('services.myfatoorah.credentials.' . $this->mode);
    }

    public function __invoke()
    {
        $amount = GeneralSetting::getValueForKey('seat_price') ?? 0;
        $body = [
            'PaymentMode' => 'COMPLETE_PAYMENT',
            'Order' => [
                'Amount' => $amount,
            ]
        ];
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => 'Bearer ' . $this->credentials['token'],
            'content-type' => 'application/json',
        ])->post($this->credentials['base_url'] . '/sessions', $body);

        return $this->jsonResponse(
            key: $response->json('IsSuccess') ? 'success' : 'fail',
            msg: $response->json('Message'),
            data: [
            'session_id' => $response->json('Data.SessionId'),
            'amount' => $response->json('Data.Order.Amount'),
            'currency' => $response->json('Data.Order.Currency'),
        ]);
    }

    public function getStatus(string $paymentId)
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => 'Bearer ' . $this->credentials['token'],
            'content-type' => 'application/json',
        ])->get($this->credentials['base_url'] . '/payments/' . $paymentId);

        $jsonResponse = $response->json();
        return [
            'key' => $jsonResponse['IsSuccess'] ? 'success' : 'fail',
            'invoice_status' => $jsonResponse['Data']['Invoice']['Status'] ?? null,
            'transaction_status' => $jsonResponse['Data']['Transaction']['Status'] ?? null,
        ];
    }
}
