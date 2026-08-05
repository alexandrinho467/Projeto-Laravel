<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;

class FemininosSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = ['34','35','36','37','38','39'];

        $products = [
            [
                'product' => [
                    'brand'       => 'Mizuno',
                    'name'        => 'Wave Rider 27 Feminino',
                    'slug'        => 'mizuno-wave-rider-27-feminino',
                    'badge'       => 'Feminino',
                    'gender'      => 'feminino',
                    'featured'    => true,
                    'price'       => 699.90,
                    'old_price'   => 799.90,
                    'description' => 'Tênis feminino Mizuno Wave Rider 27 com tecnologia Wave e amortecimento superior para corrida de longa distância. Cabedal em mesh respirável com detalhes em rosa e turquesa.',
                    'active'      => true,
                    'sort_order'  => 10,
                ],
                'img1' => 'assets/images/mizuno-wave-rider-27-fem-1.jpg',
                'img2' => 'assets/images/mizuno-wave-rider-27-fem-2.jpg',
            ],
            [
                'product' => [
                    'brand'       => 'Mizuno',
                    'name'        => 'Wave Creation 21 Feminino',
                    'slug'        => 'mizuno-wave-creation-21-feminino',
                    'badge'       => 'Feminino',
                    'gender'      => 'feminino',
                    'featured'    => true,
                    'price'       => 849.90,
                    'old_price'   => 999.90,
                    'description' => 'Tênis feminino Mizuno Wave Creation 21 com máximo amortecimento e tecnologia Wave exclusiva. Design preto com detalhes em prata e rosa.',
                    'active'      => true,
                    'sort_order'  => 11,
                ],
                'img1' => 'assets/images/mizuno-wave-creation-21-fem-1.jpg',
                'img2' => 'assets/images/mizuno-wave-creation-21-fem-2.jpg',
            ],
            [
                'product' => [
                    'brand'       => 'Nike',
                    'name'        => 'Zoom Vomero 17 Feminino',
                    'slug'        => 'nike-zoom-vomero-17-feminino',
                    'badge'       => 'Feminino',
                    'gender'      => 'feminino',
                    'featured'    => true,
                    'price'       => 899.90,
                    'old_price'   => 1099.90,
                    'description' => 'Tênis feminino Nike Zoom Vomero 17 com tecnologia Air Zoom e entressola ZoomX. Azul lavanda com swoosh branco e solado para máximo retorno de energia.',
                    'active'      => true,
                    'sort_order'  => 12,
                ],
                'img1' => 'assets/images/nike-zoom-vomero-17-fem-1.jpg',
                'img2' => 'assets/images/nike-zoom-vomero-17-fem-2.jpg',
            ],
        ];

        foreach ($products as $data) {
            $p = Product::create($data['product']);
            ProductImage::create([
                'product_id' => $p->id,
                'img1'       => $data['img1'],
                'img2'       => $data['img2'],
                'sort_order' => 1,
            ]);
            foreach ($sizes as $size) {
                ProductSize::create([
                    'product_id' => $p->id,
                    'size'       => $size,
                    'available'  => true,
                ]);
            }
        }
    }
}
