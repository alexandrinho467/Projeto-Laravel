<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\ProductFeature;
use App\Models\Coupon;
use App\Models\SiteSetting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@diasneakers.com.br',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // Coupon
        Coupon::create([
            'code'   => 'BEMVINDO10',
            'type'   => 'percent',
            'value'  => 10,
            'active' => true,
        ]);

        // Site settings
        $settings = [
            'site_name'        => 'Dias Sneakers',
            'site_email'       => 'suporte@alexandredias.com.br',
            'banner_title'     => 'Nova Coleção 2025',
            'banner_subtitle'  => 'Até 50% Off',
            'banner_img'       => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1800&auto=format&fit=crop',
            'free_shipping_min'=> '1500',
        ];
        foreach ($settings as $k => $v) SiteSetting::set($k, $v);

        // Products
        $products = [
            [
                'brand' => 'Nike', 'name' => 'Calm Mule', 'badge' => 'Campeã de Vendas',
                'old_price' => 1299, 'price' => 999, 'ref' => 'NK-CM-001-2025',
                'description' => 'A inovadora Nike Calm Mule combina conforto máximo com design arrojado. Desenvolvida para uso casual e lifestyle, a sola altamente amortecida em espuma proporciona uma sensação de leveza incomparável.',
                'tech' => 'Solado em espuma de alta densidade com pods de tração laranja. Upper em malha sintética respirável. Design mule sem cadarços.',
                'colors' => [['color_name' => 'Anthracite / Orange', 'img1' => 'assets/images/tenis 1.png', 'img2' => 'assets/images/Tenis 11.png']],
                'sizes' => [['38',true],['39',true],['40',false],['41',true],['42',true],['43',false],['44',true]],
                'features' => ['Solado ultra-macio com amortecimento superior','Design mule slip-on','Pods de tração laranja','Upper em malha sintética respirável','Ideal para uso diário'],
            ],
            [
                'brand' => 'Nike', 'name' => 'Air Max 1/97 Sean Wotherspoon', 'badge' => 'Edição Limitada',
                'old_price' => 5499, 'price' => 4299, 'ref' => 'NK-AM197-SW-2025',
                'description' => 'A colaboração mais aguardada entre Nike e Sean Wotherspoon une as silhuetas icônicas Air Max 1 e Air Max 97 em um único modelo exclusivo.',
                'tech' => 'Câmaras Air Max 1 (forefoot) e Air Max 97 (heel) visíveis. Upper em veludo artesanal.',
                'colors' => [['color_name' => 'Multi-Color / Volt', 'img1' => 'assets/images/tenis 2.png', 'img2' => 'assets/images/tenis 22.png']],
                'sizes' => [['38',true],['39',false],['40',true],['41',true],['42',true],['43',true],['44',false]],
                'features' => ['Colaboração exclusiva Nike x Sean Wotherspoon','Câmaras Air Max 1 e Air Max 97 combinadas','Upper em veludo artesanal','Paleta de cores anos 80','Edição limitada'],
            ],
            [
                'brand' => 'Nike', 'name' => 'Air Force 1 Low \'07 Triple Black', 'badge' => 'Preço Exclusivo Site',
                'old_price' => 1199, 'price' => 899, 'ref' => 'NK-AF1-TB-2025',
                'description' => 'O clássico absoluto do streetwear em sua versão mais elegante e minimalista: o Triple Black.',
                'tech' => 'Upper em couro plena flor premium todo preto. Câmara Air na entressola.',
                'colors' => [['color_name' => 'Triple Black', 'img1' => 'assets/images/tenis 3.png', 'img2' => 'assets/images/tenis 33.png']],
                'sizes' => [['38',false],['39',true],['40',true],['41',true],['42',false],['43',true],['44',true]],
                'features' => ['Couro plena flor premium all-black','Câmara Air para amortecimento','Visual atemporal e versátil','Ícone do streetwear mundial','Combina com qualquer estilo'],
            ],
            [
                'brand' => 'Balmain', 'name' => 'Unicorn Low-Top', 'badge' => 'Luxo Importado',
                'old_price' => 5299, 'price' => 4199, 'ref' => 'BLM-UC-LT-2025',
                'description' => 'O Unicorn Low-Top é a expressão máxima do luxo parisiense em forma de tênis.',
                'tech' => 'Upper em couro e material sintético multicamadas. Fivela metálica exclusiva Balmain.',
                'colors' => [['color_name' => 'All Black', 'img1' => 'assets/images/tenis 4.png', 'img2' => 'assets/images/tenis 44.png']],
                'sizes' => [['38',true],['39',true],['40',true],['41',false],['42',true],['43',true],['44',true]],
                'features' => ['Design futurista Balmain','Fivela metálica exclusiva','Couro e materiais premium','Solado chunky de alta densidade','Símbolo de luxo e status'],
            ],
            [
                'brand' => 'Nike', 'name' => 'NOCTA Hot Step 2', 'badge' => 'Collab Exclusiva',
                'old_price' => 2499, 'price' => 1899, 'ref' => 'NK-NOCTA-HS2-2025',
                'description' => 'Desenvolvido em colaboração com Drake e seu label NOCTA, o Hot Step 2 redefine o conceito de tênis de lifestyle de luxo.',
                'tech' => 'Upper em malha sintética de alta resistência. Unidade Air Strobel integrada.',
                'colors' => [['color_name' => 'Fire Red / Black', 'img1' => 'assets/images/tenis 5.png', 'img2' => 'assets/images/tenis 55.png']],
                'sizes' => [['38',true],['39',true],['40',false],['41',true],['42',false],['43',true],['44',true]],
                'features' => ['Colaboração Nike x Drake / NOCTA','Colorway Fire Red','Unidade Air Strobel','Design aerodinâmico','Símbolo da cultura pop'],
            ],
            [
                'brand' => 'Jordan', 'name' => 'Air Jordan 5 Retro OG Black Metallic', 'badge' => 'Retro OG',
                'old_price' => 3999, 'price' => 3199, 'ref' => 'JD-AJ5-OG-BM-2025',
                'description' => 'O lendário Air Jordan 5 retorna em sua versão OG original, fiel ao modelo de 1990.',
                'tech' => 'Upper em camurça de alta qualidade. Câmara Air visível no calcanhar.',
                'colors' => [['color_name' => 'Black / Metallic Silver', 'img1' => 'assets/images/tenis 6.png', 'img2' => 'assets/images/tenis 66.png']],
                'sizes' => [['38',true],['39',false],['40',true],['41',true],['42',true],['43',false],['44',true]],
                'features' => ['Fiel ao modelo OG de 1990','Câmara Air no calcanhar','Upper em camurça premium','Rede lateral icônica','Card de autenticidade Jordan Brand'],
            ],
            [
                'brand' => 'Salomon', 'name' => 'XT-4 OG Aurora Borealis', 'badge' => 'Collab Especial',
                'old_price' => 1999, 'price' => 1599, 'ref' => 'SLM-XT4-AB-2025',
                'description' => 'O XT-4 OG Aurora Borealis é uma ode à natureza em forma de tênis.',
                'tech' => 'Upper Sensifit. Solado Contagrip TA. Sistema Quick Lace.',
                'colors' => [['color_name' => 'Aurora Borealis', 'img1' => 'assets/images/tenis 7.png', 'img2' => 'assets/images/tenis 77.png']],
                'sizes' => [['38',true],['39',true],['40',true],['41',true],['42',false],['43',true],['44',false]],
                'features' => ['Colorway Aurora Borealis','Tecnologia Contagrip','Sistema Quick Lace','Upper Sensifit','Favorito global do streetwear'],
            ],
            [
                'brand' => 'Nike', 'name' => 'Air Max 95 OG Neon', 'badge' => 'Retro OG',
                'old_price' => 2899, 'price' => 2299, 'ref' => 'NK-AM95-OG-NN-2025',
                'description' => 'O Air Max 95 OG Neon é um dos tênis mais icônicos já criados pela Nike.',
                'tech' => 'Câmaras Air duplas forefoot e heel. Solado Max Waffle.',
                'colors' => [['color_name' => 'Neon Yellow / Anthracite', 'img1' => 'assets/images/tenis 8.png', 'img2' => 'assets/images/tenis 8.png']],
                'sizes' => [['38',false],['39',true],['40',true],['41',true],['42',true],['43',true],['44',true]],
                'features' => ['Colorway OG original de 1995','Câmaras Air duplas','Design inspirado na anatomia','Criado por Sergio Lozano','Modelo mais cobiçado da Nike'],
            ],
            [
                'brand' => 'Converse', 'name' => 'Chuck 70 Hi Distressed', 'badge' => 'Edição Especial',
                'old_price' => 1399, 'price' => 1099, 'ref' => 'CVS-CK70-DST-2025',
                'description' => 'Uma releitura artística do ícone Chuck Taylor com acabamento distressed único.',
                'tech' => 'Upper em lona de algodão pesado com tratamento distressed. Palmilha OrthoLite.',
                'colors' => [['color_name' => 'Dark Olive / Red', 'img1' => 'assets/images/tenis 9.png', 'img2' => 'assets/images/tenis 99.png']],
                'sizes' => [['38',true],['39',true],['40',false],['41',true],['42',true],['43',true],['44',true]],
                'features' => ['Acabamento distressed único','Lona de algodão premium','Palmilha OrthoLite','Cada par é único','Autenticidade e originalidade'],
            ],
        ];

        foreach ($products as $i => $data) {
            $product = Product::create([
                'brand'       => $data['brand'],
                'name'        => $data['name'],
                'slug'        => \Illuminate\Support\Str::slug($data['name']),
                'badge'       => $data['badge'],
                'old_price'   => $data['old_price'],
                'price'       => $data['price'],
                'ref'         => $data['ref'],
                'description' => $data['description'],
                'tech'        => $data['tech'],
                'sort_order'  => $i,
                'active'      => true,
            ]);

            foreach ($data['colors'] as $c) {
                ProductImage::create([
                    'product_id'  => $product->id,
                    'color_name'  => $c['color_name'],
                    'img1'        => $c['img1'],
                    'img2'        => $c['img2'],
                    'sort_order'  => 0,
                ]);
            }

            foreach ($data['sizes'] as $s) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size'       => $s[0],
                    'available'  => $s[1],
                ]);
            }

            foreach ($data['features'] as $f => $feat) {
                ProductFeature::create([
                    'product_id' => $product->id,
                    'feature'    => $feat,
                    'sort_order' => $f,
                ]);
            }
        }
    }
}
