<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepository->getAllProducts();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255|unique:products',
            'description' => 'required',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants.*.size' => 'required',
            'variants.*.color' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = $this->productRepository->createProduct($request->all());

        return response()->json(['message' => 'Product created successfully!'], 200);
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validator = \Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:products,title,'.$product->id,
            'description' => 'required',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants.*.size' => 'required',
            'variants.*.color' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = $this->productRepository->updateProduct($product, $request->all());

        return response()->json(['message' => 'Product updated successfully!'], 200);
    }

    public function destroy(Product $product)
    {
        $this->productRepository->deleteProduct($product);

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }
}
