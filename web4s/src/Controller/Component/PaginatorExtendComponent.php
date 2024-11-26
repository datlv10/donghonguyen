<?php
declare(strict_types=1);

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\Exception\PageOutOfBoundsException;
use Cake\Datasource\Paginator;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Exception\NotFoundException;
use InvalidArgumentException;
use Cake\Controller\Component\PaginatorComponent;

class PaginatorExtendComponent extends PaginatorComponent
{   

    /**
     * Kế thừa lại hàm paginate() của component Paginator nhưng thay đổi không cho hàm này đọc params từ request 
     */

	public function paginate(object $object, array $settings = []): ResultSetInterface
    {
        try {
            $results = $this->_paginator->paginate(
                $object,
                [],
                $settings
            );

            $this->_setPagingParams();
        } catch (PageOutOfBoundsException $e) {
            $this->_setPagingParams();

            throw new NotFoundException(null, null, $e);
        }

        return $results;
    }
}
