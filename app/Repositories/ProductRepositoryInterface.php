<?php

namespace App\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function getAllProducts();

    public function findProductById($id);

    public function createProduct(array $data);

    public function updateProduct(Product $product, array $data);

    public function deleteProduct(Product $product);
}
