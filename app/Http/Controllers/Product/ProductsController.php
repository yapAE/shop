<?php

namespace App\Http\Controllers\Product;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\ApiController;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends ApiController
{
    //

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $builder = Product::query()->where('on_sale',true);

        //判断请求中search参数，存在即赋值
        if ($search = $request->input('search','')){
            $like = '%'.$search.'%';
            //模糊搜索Product标题、描述；sku标题、描述
            $builder->where(function ($query) use($like){
                $query->where('title','like',$like)
                      ->orWhere('description','like',$like)
                      ->orWhereHas('skus',function ($query) use($like){
                          $query->where('title','like',$like)
                              ->orWhere('description','like',$like);
                      });
            });
        }

        //判断请求中的order参数，存在则赋值，eg：price_desc
        if ($order = $request->input('order','')){
            //order参数是否以_asc 或 _desc 结尾
            if (preg_match('',$order,$matches)){
                // 判断是否是合法的排序值
                if (in_array($matches[1],['price','sold_count','rating'])){
                    //根据排序值构造排序参数
                    $builder->orderBy($matches[1],$matches[2]);
                }
            }
        }

        $products = $builder->paginate();

        $data = [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ]
        ];

        return  $this->success($data);
    }


    /**
     * @param Product $product
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function show(Product $product,Request $request)
    {
        //判断Product是否上架，否则报异常
        if (!$product->on_sale){

            throw new InvalidRequestException('商品未上架');
        }
        $product->skus;

        return  $this->success($product);
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function favor(Product $product,Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)){

            $user->favoriteProducts()->detach($product);

            return $this->message('收藏过了');
        }
            $user->favoriteProducts()->attach($product);

            return  $this->message('已收藏');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function favorites(Request $request)
    {

        $products = $request->user()->favoriteProducts()->paginate();

        return $this->success($products);
    }

}
