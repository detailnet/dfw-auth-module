<?php

namespace Detail\Auth\Identity;

interface ThreeScaleTransactionRepositoryInterface
{
    /**
     * @param array $data
     * @return ThreeScaleTransactionInterface
     */
    public function create(array $data);

    /**
     * @param mixed $id
     * @return ThreeScaleTransactionInterface
     */
    public function find($id);

    /**
     * @return ThreeScaleTransactionInterface[]
     */
    public function findAll();

    /**
     * @param ThreeScaleTransactionInterface|ThreeScaleTransactionInterface[] $transactions
     */
    public function add($transactions);

    /**
     * @param ThreeScaleTransactionInterface|ThreeScaleTransactionInterface[] $transactions
     */
    public function remove($transactions);
}
