<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;

class ProductService
{
    public function getProductsData()
    {
        try {
            $allProducts = Product::with('category')->get();

            return ['success' => true, 'data' => $allProducts];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao criar usuário: ' . $e->getMessage()];
        }
    }

    public function getProductData($id)
    {
        try {
            $productData = Product::with('category')
                ->where('id', $id)
                ->first();
            return ['success' => true, 'data' => $productData];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao criar usuário: ' . $e->getMessage()];
        }
    }

    public function createProduct($request)
    {
        try {
             
            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $imageName);
                
                $productData['img'] = 'images/products/' . $imageName;
            }
    
            $product = Product::create($productData);
            
            return ['success' => true, 'data' =>  "Produto Criado com sucesso"];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao criar produto: ' . $e->getMessage()];
        }
    }

    public function deleteProduct($id)
    {
        try {

            $post = Product::find($id);
            $post->delete();

            return ['success' => true, 'data' =>  "Produto deletado com sucesso"];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao criar produto: ' . $e->getMessage()];
        }
    }

    public function editProduct($id,$request)
    {
        try {

            $product = Product::find($id);

            if (!$product) {
                return ['success' => false, 'message' => 'Produto não encontrado'];
            }else{
                $product->update($request->all());
                return ['success' => true, 'data' =>  "Produto editado com sucesso"];
            }
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao criar produto: ' . $e->getMessage()];
        }
    }
}
