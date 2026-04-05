<?php

namespace App\Support;
class QueryOptions
{
    public int $paginateNum = 10;
    public array $conditions = [];
    public array $with = [];
    public array $withCount = [];
    public string|array|null $scopes = 'search';
    public array $anyOfRelationsMustExist = [];
    public bool $latest = true;
    public ?\Closure $customQuery = null;

    public function paginateNum($num): static
    {
        $this->paginateNum = $num;
        return $this;
    }
    public function conditions($arr): static
    {
        $this->conditions = $arr;
        return $this;
    }
    public function with($arr): static
    {
        $this->with = $arr;
        return $this;
    }
    public function withCount($arr): static
    {
        $this->withCount = $arr;
        return $this;
    }
    public function scopes($scopes): static
    {
        $this->scopes = $scopes;
        return $this;
    }
    public function latest($bool = true): static
    {
        $this->latest = $bool;
        return $this;
    }
    public function custom(\Closure $cb): static
    {
        $this->customQuery = $cb;
        return $this;
    }
    public function anyOfRelationsMustExist($arr): static
    {
        $this->anyOfRelationsMustExist = $arr;
        return $this;
    }
}
