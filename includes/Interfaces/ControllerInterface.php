<?php

namespace Interfaces;
use Models\Model;

interface ControllerInterface
{
    /**
     * for Admin
     */
    public function create(array $data);

    public function read();

    public function update(array $data): bool;

    public function delete(int $id = null);

    /**
     * for user
     */
}