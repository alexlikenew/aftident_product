<?php

namespace Interfaces;
use Models\Model;

interface FormInterface
{
    /**
     * for Admin
     */
    public function create(array $data);

    public function read();

    public function update(array $data): bool;

    public function delete(int $id = null);

    public function createInput(FormInputInterface $input);

    public function updateInput(array $data);

    public function deleteInput(int $id, int $type);

    /**
     * for user
     */
}
