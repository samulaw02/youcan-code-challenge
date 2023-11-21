<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;


class CategoryRepository
{
    protected $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function getAllWithProducts()
    {
        return $this->model->with('products')->all();
    }

    public function paginate($perPage, $page, $data=[])
    {
        $query = $this->model->with('products');
        $categories = $query->when(isset($data['name']) && $data['name'] != '', function (Builder $query) use ($data) {
            $query->where('name', 'like', '%' .$data['name']);
        })
        ->orderBy('created_at', 'DESC')->paginate($perPage, ['*'], 'page', $page) ?? [];
        return $categories;
    }

    public function find($id)
    {
        return $this->model->with('products')->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = $this->model->find($id);
        if ($category) {
            $category->products()->detach();
            $category->delete();
            return true;
        }
        return false;
    }

    public function insertToAllCategories($array)
    {
        return $this->model->insert($array);
    }
}
