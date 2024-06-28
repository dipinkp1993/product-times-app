<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Variant;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts()
    {
        return Product::with('variants')->latest()->paginate(10);
    }

    public function findProductById($id)
    {
        return Product::with('variants')->findOrFail($id);
    }

    public function createProduct(array $data)
    {
        $product = new Product;
        $product->title = $data['title'];
        $product->description = $data['description'];

        if (isset($data['main_image'])) {
            $path = $data['main_image']->store('images', 'public');
            $product->main_image = $path;
        }

        $product->save();

        if (isset($data['variants'])) {
            foreach ($data['variants'] as $variant) {
                $product->variants()->create($variant);
            }
        }

        return $product;
    }

    public function updateProduct(Product $product, array $data)
    {
        $product->title = $data['title'];
        $product->description = $data['description'];

        if (isset($data['main_image'])) {
            $path = $data['main_image']->store('images', 'public');
            $product->main_image = $path;
        }

        $variantIdsToDelete = collect($product->variants()->pluck('id'))->diff(collect($data['variants'])->pluck('id'));
        Variant::whereIn('id', $variantIdsToDelete)->delete();

        foreach ($data['variants'] as $variantData) {
            if (isset($variantData['id'])) {
                $variant = Variant::find($variantData['id']);
                if ($variant) {
                    $variant->update($variantData);
                }
            } else {
                $variant = new Variant();
                $variant->product_id = $product->id;
                $variant->size = $variantData['size'];
                $variant->color = $variantData['color'];
                $variant->save();
            }
        }

        $product->save();

        return $product;
    }

    public function deleteProduct(Product $product)
    {
        $product->variants()->delete();
        $product->delete();
    }
}
