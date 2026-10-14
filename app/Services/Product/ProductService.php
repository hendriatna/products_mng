<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Product::query()
            ->when(
                $filters['search'] ?? null,
                fn (Builder $query, string $search) => $query
                    ->where('title', 'ilike', "%{$search}%")
            )
            ->when(
                $filters['category'] ?? null,
                fn (Builder $query, string $category) => $query
                    ->where('category', $category)
            )
            ->orderByDesc('id')
            ->paginate(
                min((int) ($filters['limit'] ?? 10), 100)
            )
            ->withQueryString();
    }

    public function findById(int $id): ?Product
    {
        return Product::query()->find($id);
    }

    public function create(array $data, User $user): Product
    {
        return Product::query()->create([
            ...$data,
            'created_by' => $user->name,
            'created_by_id' => $user->id,
        ]);
    }

    public function update(
        Product $product,
        array $data,
        User $user
    ): Product {
        $product->fill([
            ...$data,
            'updated_by' => $user->name,
            'updated_by_id' => $user->id,
        ]);

        $product->save();

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}