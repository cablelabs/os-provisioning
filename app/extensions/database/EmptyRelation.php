<?php

namespace App\Extensions\Database;

/**
 * Class to provide an empty relation.
 * This can be returned if the model we want to get the relations to does not exist
 *	especially in case a module is not installed.
 *
 * Only the abstract methods from \Illuminate\Database\Eloquent\Relations\Relation are defined.
 *
 * @author Patrick Reichel
 */
class EmptyRelation extends \Illuminate\Database\Eloquent\Relations\Relation
{
    public function __construct()
    {
    }

    public function addConstraints()
    {
    }

    public function addEagerConstraints(array $models)
    {
    }

    public function initRelation(array $models, $relation)
    {
    }

    public function match(array $models, \Illuminate\Database\Eloquent\Collection $results, $relation)
    {
    }

    public function getResults()
    {
    }
}
