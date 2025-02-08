<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function create(array $data);
    public function findById(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findAll(array $data);
}
