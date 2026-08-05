<?php
namespace App\Services;

class PorterService
{
    private string $apiKey;
    private string $apiUrl;
    private float  $originLat;
    private float  $originLng;

    // Tabela oficial Porter Dubai (AED)
    private const RATES = [
        'bike'   => ['base' => 9,  'per_km' => 1.0, 'per_min' => 0.15, 'max_kg' => 20,   'label' => 'Bike / Scooter', 'icon' => '🛵', 'prazo' => '30 – 60 min'],
        'car'    => ['base' => 24, 'per_km' => 1.5, 'per_min' => 0.25, 'max_kg' => 100,  'label' => 'Car',             'icon' => '🚗', 'prazo' => '30 – 90 min'],
        'pickup' => ['base' => 30, 'per_km' => 2.2, 'per_min' => 0.35, 'max_kg' => 1050, 'label' => 'Pickup 1 Ton',   'icon' => '🛻', 'prazo' => '1 – 2 hrs'],
        '3ton'   => ['base' => 55, 'per_km' => 3.5, 'per_min' => 0.50, 'max_kg' => 3510, 'label' => 'Canter 3 Ton',  'icon' => '🚚', 'prazo' => '1 – 3 hrs'],
    ];

    // Peso por par (2 sapatos + caixa + embalagem) em kg, indexado por product_id
    private const PRODUCT_WEIGHTS = [
        4  => 2.30, // Balmain Unicorn Low-Top
        9  => 1.30, // Converse Chuck 70 Hi Distressed
        6  => 1.24, // Jordan Air Jordan 5 Retro OG
        8  => 1.22, // Nike Air Max 95 OG Neon
        3  => 1.18, // Nike Air Force 1 Low Triple Black
        5  => 1.18, // Nike NOCTA Hot Step 2
        2  => 1.12, // Nike Air Max 1/97 Sean Wotherspoon
        1  => 1.10, // Nike Calm Mule
        7  => 1.02, // Salomon XT-4 OG Aurora Borealis
        11 => 0.96, // Mizuno Wave Creation 21 Feminino
        13 => 0.94, // New Balance 740
        10 => 0.78, // Mizuno Wave Rider 27 Feminino
        12 => 0.78, // Nike Zoom Vomero 17 Feminino
    ];

    private const DEFAULT_WEIGHT_KG = 1.0;
    private const OSRM_URL          = 'https://router.project-osrm.org/route/v1/car';

    public function __construct()
    {
        $this->apiKey    = config('services.porter.key', '');
        $this->apiUrl    = config('services.porter.url', '');
        $this->originLat = (float) config('services.porter.origin_lat', 25.0762); // Dubai Marina Mall
        $this->originLng = (float) config('services.porter.origin_lng', 55.1403);
    }

    public static function pesoTotal(array $cart): float
    {
        $total = 0.0;
        foreach ($cart as $item) {
            $id    = $item['product_id'] ?? null;
            $peso  = isset($id, self::PRODUCT_WEIGHTS[$id])
                       ? self::PRODUCT_WEIGHTS[$id]
                       : self::DEFAULT_WEIGHT_KG;
            $total += $peso * $item['qty'];
        }
        return max(0.1, $total);
    }

    public function calcular(float $destLat, float $destLng, float $pesoKg): array
    {
        // 1. Tenta rota real via OSRM (distância por estrada + tempo real)
        $rota = $this->rotear($destLat, $destLng);

        if ($rota) {
            $distKm  = $rota['distancia_km'];
            $timeMin = $rota['tempo_min'];
        } else {
            // Fallback: Haversine + estimativa de tempo (25 km/h média Dubai)
            $distKm  = $this->haversine($this->originLat, $this->originLng, $destLat, $destLng);
            $timeMin = ($distKm / 25) * 60;
        }

        // 2. Se tiver API Porter configurada, usa ela
        if ($this->apiKey && $this->apiUrl) {
            $apiResult = $this->chamarApi($destLat, $destLng, $pesoKg);
            if ($apiResult) {
                return ['success' => true, 'opcoes' => $apiResult, 'distancia_km' => round($distKm, 1), 'tempo_min' => round($timeMin)];
            }
        }

        // 3. Calcula localmente com dados reais de rota
        return [
            'success'      => true,
            'opcoes'       => $this->calcularLocal($distKm, $timeMin, $pesoKg),
            'distancia_km' => round($distKm, 1),
            'tempo_min'    => round($timeMin),
        ];
    }

    public function geocodificar(string $endereco): ?array
    {
        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q'      => $endereco . ', Dubai, UAE',
            'format' => 'json',
            'limit'  => 1,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'DiasSneakers/1.0 (contact@diassneakers.com)',
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        if (!$body) return null;
        $data = json_decode($body, true);
        if (empty($data)) return null;

        return ['lat' => (float) $data[0]['lat'], 'lng' => (float) $data[0]['lon']];
    }

    // Chama OSRM para obter distância real por estrada e tempo de viagem
    private function rotear(float $destLat, float $destLng): ?array
    {
        // OSRM usa formato: lng,lat (invertido)
        $url = sprintf(
            '%s/%s,%s;%s,%s?overview=false',
            self::OSRM_URL,
            $this->originLng, $this->originLat,
            $destLng, $destLat
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'DiasSneakers/1.0',
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        if (!$body) return null;
        $data = json_decode($body, true);
        if (($data['code'] ?? '') !== 'Ok' || empty($data['routes'])) return null;

        $route = $data['routes'][0];
        return [
            'distancia_km' => round($route['distance'] / 1000, 2), // metros → km
            'tempo_min'    => round($route['duration'] / 60, 1),   // segundos → min
        ];
    }

    private function calcularLocal(float $distKm, float $timeMin, float $pesoKg): array
    {
        $opcoes = [];
        foreach (self::RATES as $tipo => $r) {
            if ($pesoKg > $r['max_kg']) continue;

            // Fórmula Porter: base + (distância × per_km) + (tempo × per_min)
            $valor = $r['base']
                   + ($distKm  * $r['per_km'])
                   + ($timeMin * $r['per_min']);

            $opcoes[] = [
                'nome'  => $tipo,
                'label' => $r['label'],
                'valor' => round(max($r['base'], $valor), 2),
                'prazo' => $r['prazo'],
                'icone' => $r['icon'],
            ];
        }
        return $opcoes;
    }

    private function chamarApi(float $destLat, float $destLng, float $pesoKg): ?array
    {
        $payload = json_encode([
            'pickup_details'  => ['lat' => $this->originLat, 'lng' => $this->originLng],
            'drop_details'    => ['lat' => $destLat,         'lng' => $destLng],
            'package_details' => ['weight' => $pesoKg],
        ]);

        $ch = curl_init(rtrim($this->apiUrl, '/') . '/fare-estimate');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $this->apiKey],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        if (!$body) return null;
        $data = json_decode($body, true);
        if (empty($data['vehicles'])) return null;

        $opcoes = [];
        foreach ($data['vehicles'] as $v) {
            $tipo     = $v['type'] ?? '';
            $rate     = self::RATES[$tipo] ?? null;
            $opcoes[] = [
                'nome'  => $tipo,
                'label' => $rate['label'] ?? ucfirst($tipo),
                'valor' => ($v['fare']['minor_amount'] ?? 0) / 100,
                'prazo' => $rate['prazo'] ?? 'Same Day',
                'icone' => $rate['icon']  ?? '📦',
            ];
        }
        return $opcoes;
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
