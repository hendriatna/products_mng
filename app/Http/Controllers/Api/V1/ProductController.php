<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    public function index(
        IndexProductRequest $request
    ): AnonymousResourceCollection {
        $products = $this->productService->paginate(
            $request->validated()
        );

        return ProductResource::collection($products);
    }

    public function show(int $id): ProductResource|JsonResponse
    {
        $product = $this->productService->findById($id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        return new ProductResource($product);
    }

    public function store(
        StoreProductRequest $request
    ): JsonResponse {
        $product = $this->productService->create(
            $request->validated(),
            $request->user('api'),
        );

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        UpdateProductRequest $request,
        int $id
    ): ProductResource|JsonResponse {
        $product = $this->productService->findById($id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        $updatedProduct = $this->productService->update(
            $product,
            $request->validated(),
            $request->user('api'),
        );

        return new ProductResource($updatedProduct);
    }

    public function destroy(int $id): Response|JsonResponse
    {
        $product = $this->productService->findById($id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        $this->productService->delete($product);

        return response()->noContent();
    }
}