<?php
/**
 * Repository
 *
 * @author     Esben Petersen
 * @link       https://github.com/esbenp/genie/blob/master/src/Repository.php
 */

namespace App\V1;

use App\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Repository
{
    use QueryBuilderTrait;

    protected $model;

    protected $sortProperty = null;

    // 0 = ASC, 1 = DESC
    protected $sortDirection = 0;

    final public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return BaseModel
     */
    public function getModel(): BaseModel
    {
        return $this->model;
    }

    /**
     * @param array $options
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Builder[]|Collection
     */
    public function get(array $options = [])
    {
        $paginate = $options['paginate'] ?? false;
        $as_tree = $options['as_tree'] ?? false;
        unset($options['paginate']);
        unset($options['as_tree']);
        $query = $this->createBaseBuilder($options);
        if ($as_tree) {
            return $paginate ? $query->with('descendants')->paginate($options['limit']) : $query->get()->toTree();
        }

        return $paginate ? $query->paginate($options['limit']) : $query->get();
    }

    /**
     * Get a resource by its primary key
     * @param  mixed $id
     * @param  array $options
     * @return \App\BaseModel|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function getById($id, array $options = [])
    {
        $as_tree = $options['as_tree'] ?? false;
        if ($as_tree) {
            return ($this->getModel())::descendantsAndSelf($id)->toTree();
        }

        $options['filter_groups'][] = ['filters' => [
            ['key' => 'id', 'value' => $id, 'operator' => 'eq', 'not' => false], ], 'or' => false,
        ];
        $query = $this->createBaseBuilder($options);

        return $query->first();
    }

    /**
     * Get all resources ordered by recentness
     * @param  array $options
     * @return Collection
     */
    public function getRecent(array $options = []): Collection
    {
        $query = $this->createBaseBuilder($options);

        $query->orderBy($this->getCreatedAtColumn(), 'DESC');

        return $query->get();
    }

    /**
     * Get all resources by a where clause ordered by recentness
     * @param  array  $options
     * @return Collection
     */
    public function getRecentWhere(array $options = []): Collection
    {
        $query = $this->createBaseBuilder($options);

        $query->orderBy($this->getCreatedAtColumn(), 'DESC');

        return $query->get();
    }

    /**
     * Get latest resource
     * @param  array $options
     * @return BaseModel
     */
    public function getLatest(array $options = []): BaseModel
    {
        $query = $this->createBaseBuilder($options);

        $query->orderBy($this->getCreatedAtColumn(), 'DESC');

        return $query->first();
    }

    /**
     * Get latest resource by a where clause
     * @param  array  $options
     * @return BaseModel
     */
    public function getLatestWhere(array $options = []): BaseModel
    {
        $query = $this->createBaseBuilder($options);

        $query->orderBy($this->getCreatedAtColumn(), 'DESC');

        return $query->first();
    }

    /**
     * Get resources by a where clause
     * @param string $column
     * @param  mixed $value
     * @param  array $options
     * @return Collection
     */
    public function getWhere(string $column, $value, array $options = []): Collection
    {
        $query = $this->createBaseBuilder($options);

        $query->where($column, $value);

        return $query->get();
    }

    /**
     * Get resources by multiple where clauses
     * @param  array  $clauses
     * @param  array $options
     * @deprecated
     * @return Collection
     */
    public function getWhereArray(array $clauses, array $options = []): Collection
    {
        $query = $this->createBaseBuilder($options);

        $query->where($clauses);

        return $query->get();
    }

    /**
     * @param $column
     * @param array $values
     * @param array $options
     * @return Builder[]|Collection
     */
    public function getWhereIn($column, array $values, array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        $query->whereIn($column, $values);

        return $query->get();
    }

    /**
     * @param array $data
     * @return BaseModel
     */
    public function create(array $data): BaseModel
    {
        $model = $this->getModel();
        $model->fill($data);
        $model->save();

        return $model;
    }

    /**
     * @param BaseModel $model
     * @param array $data
     * @return BaseModel
     */
    public function update(BaseModel $model, array $data): BaseModel
    {
        $model->fill($data);
        $model->save();

        return $model;
    }

    /**
     * Delete a resource by its primary key
     * @param  mixed $id
     * @return void
     */
    public function delete($id)
    {
        $query = $this->createQueryBuilder();

        $query->where($this->getPrimaryKey($query), $id);
        $query->delete();
    }

    /**
     * Delete resources by a where clause
     * @param string $column
     * @param  mixed $value
     * @return void
     */
    public function deleteWhere(string $column, $value)
    {
        $query = $this->createQueryBuilder();

        $query->where($column, $value);
        $query->delete();
    }

    /**
     * Delete resources by multiple where clauses
     * @param  array  $clauses
     * @return void
     */
    public function deleteWhereArray(array $clauses)
    {
        $query = $this->createQueryBuilder();

        $query->whereArray($clauses);
        $query->delete();
    }

    /**
     * @param  array $options
     * @return Builder
     */
    protected function createBaseBuilder(array $options = []): Builder
    {
        $query = $this->createQueryBuilder();

        $this->applyResourceOptions($query, $options);

        if (empty($options['sort'])) {
            $this->defaultSort($query, $options);
        }

        return $query;
    }

    /**
     * Creates a new query builder
     * @return Builder
     */
    protected function createQueryBuilder(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Get primary key name of the underlying model
     * @param  Builder $query
     * @return string
     */
    protected function getPrimaryKey(Builder $query): string
    {
        return $query->getModel()->getKeyName();
    }

    /**
     * Order query by the specified sorting property
     * @param  Builder $query
     * @param  array  $options
     * @return void
     */
    protected function defaultSort(Builder $query, array $options = [])
    {
        if (isset($this->sortProperty)) {
            $direction = $this->sortDirection === 1 ? 'DESC' : 'ASC';
            $query->orderBy($this->sortProperty, $direction);
        }
    }

    protected function getCreatedAtColumn()
    {
        $model = $this->model;

        return ($model::CREATED_AT) ? $model::CREATED_AT : 'created_at';
    }
}
