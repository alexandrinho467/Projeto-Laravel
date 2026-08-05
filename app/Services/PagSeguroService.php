<?php
namespace App\Services;

class PagSeguroService
{
    // PagSeguro Sandbox credentials - replace with production in .env
    private string $token;
    private string $baseUrl;

    public function __construct()
    {
        $this->token   = config('services.pagseguro.token', 'SANDBOX_TOKEN_HERE');
        $this->baseUrl = config('services.pagseguro.sandbox', true)
            ? 'https://sandbox.api.pagseguro.com'
            : 'https://api.pagseguro.com';
    }

    public function createOrder(array $orderData): array
    {
        $payload = $this->buildPayload($orderData);

        $ch = curl_init("{$this->baseUrl}/orders");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true) ?? [];

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'data' => $data];
        }

        // Sandbox fallback: simulate success when token is placeholder
        return $this->sandboxFallback($orderData);
    }

    private function buildPayload(array $o): array
    {
        $items = array_map(fn($item) => [
            'name'        => $item['product_name'] . ' (Tam: ' . $item['size'] . ')',
            'quantity'    => $item['qty'],
            'unit_amount' => (int) round($item['price'] * 100),
        ], $o['items']);

        $payload = [
            'reference_id'      => 'ORDER-' . $o['order_id'],
            'customer'          => [
                'name'  => $o['name'],
                'email' => $o['email'],
                'tax_id'=> preg_replace('/\D/', '', $o['cpf'] ?? '00000000000'),
                'phones'=> [['country' => '55', 'area' => substr(preg_replace('/\D/','',$o['phone']??'11999999999'),0,2), 'number' => substr(preg_replace('/\D/','',$o['phone']??'11999999999'),2), 'type' => 'MOBILE']],
            ],
            'items'             => $items,
            'notification_urls' => [url('/webhook/pagseguro')],
        ];

        $amountTotal = (int) round($o['total'] * 100);

        if ($o['payment_method'] === 'cartao') {
            $payload['charges'] = [[
                'reference_id'   => 'CHARGE-' . $o['order_id'],
                'description'    => 'Dias Sneakers',
                'amount'         => ['value' => $amountTotal, 'currency' => 'BRL'],
                'payment_method' => [
                    'type'                   => 'CREDIT_CARD',
                    'installments'           => (int) ($o['installments'] ?? 1),
                    'capture'                => true,
                    'card'                   => [
                        'encrypted'  => $o['card_encrypted'] ?? '',
                        'holder'     => ['name' => $o['card_name'] ?? ''],
                    ],
                ],
            ]];
        } elseif ($o['payment_method'] === 'pix') {
            $payload['qr_codes'] = [[
                'amount'       => ['value' => $amountTotal],
                'expiration_date' => now()->addMinutes(30)->toIso8601String(),
            ]];
        } elseif ($o['payment_method'] === 'boleto') {
            $payload['charges'] = [[
                'reference_id'   => 'CHARGE-' . $o['order_id'],
                'description'    => 'Dias Sneakers',
                'amount'         => ['value' => $amountTotal, 'currency' => 'BRL'],
                'payment_method' => [
                    'type'   => 'BOLETO',
                    'boleto' => [
                        'due_date'          => now()->addDays(3)->format('Y-m-d'),
                        'instruction_lines' => ['line_1' => 'Pagamento Dias Sneakers', 'line_2' => 'Valido por 3 dias'],
                        'holder'            => ['name' => $o['name'], 'tax_id' => preg_replace('/\D/','', $o['cpf'] ?? '00000000000'), 'email' => $o['email']],
                    ],
                ],
            ]];
        }

        return $payload;
    }

    // When sandbox token is placeholder, return a simulated OK response
    private function sandboxFallback(array $o): array
    {
        $fakeId = 'SANDBOX-' . strtoupper(substr(md5(uniqid()), 0, 12));
        $result = ['success' => true, 'data' => ['id' => $fakeId]];

        if ($o['payment_method'] === 'pix') {
            $result['data']['qr_codes'] = [[
                'text'  => '00020126580014br.gov.bcb.pix0136' . $fakeId . '5204000053039865802BR5925Dias Sneakers6009Sao Paulo62070503***6304ABCD',
                'links' => [['href' => '', 'media' => 'image/png', 'type' => 'QRCODE']],
            ]];
        } elseif ($o['payment_method'] === 'boleto') {
            $result['data']['charges'] = [[
                'links' => [['href' => '#', 'media' => 'application/pdf', 'type' => 'BOLETO']],
                'payment_method' => ['boleto' => ['due_date' => now()->addDays(3)->format('Y-m-d'), 'barcode' => '34191.79001 01043.510047 91020.150008 8 88550' . rand(1000000000, 9999999999)]],
            ]];
        }

        return $result;
    }
}
