<?php
namespace Interfaces;

use Models\Model;

interface ModelInterface{

    public function create(array $data):int;
    public function read(int $id):Model;
    public function update(array $data, int $id): bool;
    public function delete(int $id):bool;
}