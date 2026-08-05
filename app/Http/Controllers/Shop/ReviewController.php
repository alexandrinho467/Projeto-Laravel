<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'name'   => 'required|string|max:80',
            'rating' => 'required|integer|min:1|max:5',
            'body'   => ['nullable','string', function ($attr, $val, $fail) {
                if ($val && str_word_count($val) > 100) $fail('O texto deve ter no máximo 100 palavras.');
            }],
            'images.*' => 'nullable|image|max:5120',
            'video'    => 'nullable|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
        ]);

        $dir = public_path('assets/reviews');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $imgs  = [];
        if ($request->hasFile('images')) {
            foreach (array_slice($request->file('images'), 0, 3) as $file) {
                $name = uniqid('rev_') . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $name);
                $imgs[] = 'assets/reviews/' . $name;
            }
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $vfile = $request->file('video');
            $vname = uniqid('rev_vid_') . '.' . $vfile->getClientOriginalExtension();
            $vfile->move($dir, $vname);
            $videoPath = 'assets/reviews/' . $vname;
        }

        Review::create([
            'product_id' => $product->id,
            'user_id'    => auth()->id(),
            'name'       => $request->name,
            'rating'     => $request->rating,
            'body'       => $request->body,
            'img1'       => $imgs[0] ?? null,
            'img2'       => $imgs[1] ?? null,
            'img3'       => $imgs[2] ?? null,
            'video'      => $videoPath,
            'approved'   => true,
        ]);

        return back()->with('review_success', 'Avaliação enviada com sucesso!');
    }

    public function update(Request $request, Review $review)
    {
        if (auth()->id() !== $review->user_id && !auth()->user()->isAdmin()) abort(403);

        $request->validate([
            'name'   => 'required|string|max:80',
            'rating' => 'required|integer|min:1|max:5',
            'body'   => ['nullable', 'string', function ($attr, $val, $fail) {
                if ($val && str_word_count($val) > 100) $fail('O texto deve ter no máximo 100 palavras.');
            }],
        ]);

        $review->update([
            'name'   => $request->name,
            'rating' => $request->rating,
            'body'   => $request->body,
        ]);

        return back()->with('review_success', 'Avaliação atualizada com sucesso!');
    }

    public function destroy(Review $review)
    {
        if (auth()->id() !== $review->user_id && !auth()->user()->isAdmin()) abort(403);

        foreach ($review->images() as $img) {
            $path = public_path($img);
            if (file_exists($path)) unlink($path);
        }
        if ($review->video) {
            $path = public_path($review->video);
            if (file_exists($path)) unlink($path);
        }

        $review->delete();
        return back()->with('review_success', 'Avaliação apagada com sucesso.');
    }
}
