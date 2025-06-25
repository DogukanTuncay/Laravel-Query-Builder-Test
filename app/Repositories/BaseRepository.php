<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class BaseRepository
{
    protected Model $model;
    protected Request $request;

    public function __construct(Model $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    public function query()
    {
        $query = QueryBuilder::for(get_class($this->model), $this->request)
            ->allowedFilters($this->getAllowed('Filters'))
            ->allowedSorts($this->getAllowed('Sorts'))
            ->allowedFields($this->getAllowed('Fields'))
            ->allowedIncludes($this->getAllowed('Includes'));

        return $query;
    }

    public function get()
    {
        return $this->query()->get();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->query()->paginate($perPage)->appends($this->request->query());
    }

    protected function getAllowed(string $type): array
    {
        $property = 'allowed' . $type;
        return property_exists($this->model, $property) ? $this->model::${$property} : [];
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
        }
        return $record;
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }
}
