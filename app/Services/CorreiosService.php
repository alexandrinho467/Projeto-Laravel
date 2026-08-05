<?php
namespace App\Services;

/**
 * Cálculo de frete por tabela pública dos Correios (2025).
 * Usa ViaCEP para identificar o estado destino, depois aplica
 * a tabela de zonas/preços vigente para PAC e SEDEX sem contrato.
 */
class CorreiosService
{
    // UF de origem (configurável via CORREIOS_UF_ORIGEM)
    private string $ufOrigem;

    // Zonas de entrega saindo do PR (Paraná)
    // Baseado na tabela de zonas dos Correios por UF
    private const ZONAS_PR = [
        'PR' => 1,                                          // mesma UF
        'SP' => 2, 'SC' => 2, 'RS' => 2,                   // sul + SP
        'MG' => 3, 'RJ' => 3, 'ES' => 3,                   // sudeste
        'MS' => 3, 'GO' => 3, 'DF' => 3, 'MT' => 3,        // centro-oeste
        'BA' => 4, 'SE' => 4, 'AL' => 4, 'PE' => 4,        // nordeste baixo
        'PB' => 4, 'RN' => 4, 'CE' => 4, 'PI' => 4,
        'MA' => 4, 'TO' => 4,
        'AM' => 5, 'PA' => 5, 'AP' => 5,                   // norte
        'RR' => 5, 'AC' => 5, 'RO' => 5,
    ];

    // Mapeamento genérico por UF (fallback para outras origens)
    private const ZONAS_PADRAO = [
        'AC' => 5, 'AL' => 4, 'AM' => 5, 'AP' => 5, 'BA' => 3,
        'CE' => 4, 'DF' => 3, 'ES' => 3, 'GO' => 3, 'MA' => 4,
        'MG' => 3, 'MS' => 3, 'MT' => 3, 'PA' => 4, 'PB' => 4,
        'PE' => 4, 'PI' => 4, 'PR' => 2, 'RJ' => 3, 'RN' => 4,
        'RO' => 4, 'RR' => 5, 'RS' => 2, 'SC' => 2, 'SE' => 4,
        'SP' => 1, 'TO' => 4,
    ];

    // PAC – Tabela de preços por zona [preço_base_300g, acréscimo_por_100g, prazo_dias]
    // Valores aproximados da tabela pública dos Correios jan/2025 (sem contrato)
    private const PAC = [
        1 => ['base' => 20.30, 'add100g' => 1.30, 'prazo' => 5],
        2 => ['base' => 27.80, 'add100g' => 1.75, 'prazo' => 7],
        3 => ['base' => 35.90, 'add100g' => 2.20, 'prazo' => 9],
        4 => ['base' => 45.20, 'add100g' => 2.80, 'prazo' => 12],
        5 => ['base' => 58.40, 'add100g' => 3.50, 'prazo' => 15],
    ];

    // SEDEX – Tabela de preços por zona
    private const SEDEX = [
        1 => ['base' => 36.80, 'add100g' => 2.30, 'prazo' => 1],
        2 => ['base' => 54.20, 'add100g' => 3.30, 'prazo' => 2],
        3 => ['base' => 71.50, 'add100g' => 4.30, 'prazo' => 3],
        4 => ['base' => 93.00, 'add100g' => 5.50, 'prazo' => 4],
        5 => ['base' => 122.00,'add100g' => 7.00, 'prazo' => 5],
    ];

    public function __construct()
    {
        $this->ufOrigem = strtoupper(config('services.correios.uf_origem', 'PR'));
    }

    /**
     * Calcula PAC e SEDEX para o destino.
     *
     * @param  string $cepDestino 8 dígitos
     * @param  float  $pesoKg     peso total do pedido
     * @return array  ['success' => bool, 'opcoes' => [...], 'message' => '...']
     */
    public function calcular(string $cepDestino, float $pesoKg): array
    {
        $cep = preg_replace('/\D/', '', $cepDestino);

        // 1. Valida CEP e obtém UF via ViaCEP
        $uf = $this->getUfViaCep($cep);
        if (!$uf) {
            return ['success' => false, 'message' => 'CEP não encontrado. Verifique e tente novamente.'];
        }

        // 2. Determina zona
        $zona = $this->getZona($uf);

        // 3. Calcula preços para PAC e SEDEX
        $opcoes = [];
        foreach (['PAC' => self::PAC, 'SEDEX' => self::SEDEX] as $nome => $tabela) {
            $t     = $tabela[$zona];
            $valor = $this->calcularValor($t, $pesoKg);
            $opcoes[] = [
                'nome'  => $nome,
                'valor' => $valor,
                'prazo' => $t['prazo'],
            ];
        }

        return ['success' => true, 'opcoes' => $opcoes, 'uf' => $uf, 'zona' => $zona];
    }

    private function calcularValor(array $tabela, float $pesoKg): float
    {
        $gramas   = (int) ceil($pesoKg * 1000);
        $base300g = 300;

        $valor = $tabela['base'];

        if ($gramas > $base300g) {
            $extra100g  = (int) ceil(($gramas - $base300g) / 100);
            $valor     += $extra100g * $tabela['add100g'];
        }

        return round($valor, 2);
    }

    private function getZona(string $uf): int
    {
        $mapa = $this->ufOrigem === 'PR' ? self::ZONAS_PR : self::ZONAS_PADRAO;
        return $mapa[$uf] ?? 3;
    }

    private function getUfViaCep(string $cep): ?string
    {
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        if (!$body) return null;
        $data = json_decode($body, true);
        if (isset($data['erro'])) return null;

        return isset($data['uf']) ? strtoupper($data['uf']) : null;
    }
}
