<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    
    public function getAllProducts()
    {
        $result = $this->productService->getProductsData();

        return response()->json($result);
    }

    public function createProduct(Request $request)
    {
        try {
            $productData = $request->all();

            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imagePath = $this->uploadImage($image);
                $productData['img'] = $imagePath;
            }
            
            $product = Product::create($productData);

            return response()->json(['success' => true, 'data' => "Produto Criado com sucesso"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao criar produto: ' . $e->getMessage()], 500);
        }
    }

    private function uploadImage($image)
    {
        try {
            $imageFolderPath = base_path('../frontend/src/assets/uploads');
            if (!File::exists($imageFolderPath)) {
                File::makeDirectory($imageFolderPath, 0777, true, true);
            }

            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move($imageFolderPath, $imageName);

            return 'uploads/' . $imageName;
        } catch (\Exception $e) {
            throw new \Exception('Erro ao fazer upload da imagem: ' . $e->getMessage());
        }
    }

    
    public function getProductData(string $id)
    {
        $result = $this->productService->getProductData($id);

        return response()->json($result);
    }

    
    public function deleteProduct(string $id)
    {
        $result = $this->productService->deleteProduct($id);

        return response()->json($result);
    }

    public function editProduct(string $id, Request $request)
    {
        $result = $this->productService->editProduct($id, $request);

        return response()->json($result);
    }
}
