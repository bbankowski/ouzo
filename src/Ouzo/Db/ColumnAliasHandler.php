<?php
namespace Ouzo\Db;

use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;

class ColumnAliasHandler
{
    public static function createSelectColumnsWithAliases($prefix, $columns, $alias)
    {
        return FluentArray::from($columns)->toMap(
            function ($field) use ($prefix) {
                return "{$prefix}{$field}";
            },
            function ($field) use ($alias) {
                return "$alias.$field";
            }
        )->toArray();
    }

    public static function extractAttributesForPrefix($result, $prefix)
    {
        return FluentArray::from($result)
            ->filterByKeys(Functions::startsWith($prefix))
            ->mapKeys(Functions::removePrefix($prefix))
            ->toArray();
    }
}