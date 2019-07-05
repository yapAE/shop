<?php


namespace App\Http\Service;


use App\Exceptions\InvalidRequestException;
use App\Models\ProductSku;

class ProductService
{
    protected  $product;

    protected  $productSku;

    public function __construct(ProductSku $productSku)
    {
        $this->productSku = $productSku;
    }

}
