<?php

if ( !function_exists('get_query_from_builder') )
{
    function get_query_from_builder($builder)
    {
        $addSlashes = str_replace('?', "'?'", $builder->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
    }
}