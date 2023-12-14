<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    /**
     * Create a new Product by $request inputs
     *
     * @param string $targetId object id
     * @param Request $request
     *
     * @returns mixed
     */
    private function newProduct(Request $request): mixed {
        $validator = Validator::make($request->all(), Product::$rules);
        if(!$validator->fails()) {
            $oProduct = Product::create($request->all());
            if(isset($request->image)) {
                $oNewProductImage = $oProduct->setImage($request->image);
            }

            return $oProduct;
        }
        else return $validator->errors();
    }

    /**
     * Update product by $request inputs for $targetId
     *
     * @param string $targetId object id
     *
     * @return mixed
     */
    private function updateProduct(Request $request, string $targetId): mixed {
        $oProduct = Product::find($targetId);
        $validator = Validator::make($request->all(), Product::$updateRules);
        if(isset($oProduct)) {
            if(!$validator->fails()) {
                if($oProduct->update($request->all())) {
                    return $oProduct;
                }
                else return null;
            }
            else return $validator->errors();
        }
        else return null;
    }

    /**
     * Delete product by its $targetId
     *
     * @param string $targetId object id
     *
     * @return bool
     */
    private function deleteProduct(string $targetId): bool {
        $oProduct = Product::find($targetId);
        if(isset($oProduct)) {
            $oProduct->delete();
            return true;
        }
        else return false;
    }

    /**
     * get the products by a certain keyword or obtain the query
     *
     * @param string $keyword
     * @param bool $queryOnly if the function returns the query instead of the collection
     *
     * @return mixed
     */
    public static function searchByKeyword(string $keyword, $queryOnly = false): mixed {
        if($queryOnly) return Product::where('name', 'like', '%' . $keyword . '%')->orWhere('description', 'like', '%' . $keyword . '%');
        else return Product::where('name', 'like', '%' . $keyword . '%')->orWhere('description', 'like', '%' . $keyword . '%')->get();
    }

    /**
     * search product function
     *
     * @param string $keyword that will be searched for
     *
     * @returns Response
     */
    private function searchProduct(string $keyword): Response {
        $ooProductsByKeyword = self::searchByKeyword($keyword);
        if($ooProductsByKeyword->count() == 0) return response($ooProductsByKeyword,204);
        else {
            return response(ProductResource::collection($ooProductsByKeyword),200);
        }
    }

    /**
     * search via the /product/keyword URL where the params contains the keyword value
     *
     * @param Request $request should contain the parameter 'keyword'
     *
     * @returns Response
     */
    public function search(Request $request): Response {
        if($request->keyword !== null) {
            return $this->searchProduct($request->keyword);
        }
        else return response('No keyword given',400);
    }

    /**
     * search via the /product/keyword/{keyword} URL
     *
     * @param Request $request
     *  should contain the parameter 'keyword'
     */
    public function searchViaPath(Request $request) {
        if($request->keyword !== null) {
            return $this->searchProduct($request->keyword);
        }
        else return response('No keyword given',400);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pagination' => 'nullable|numeric|min:1',
            'page' => 'nullable|numeric|min:1',
            'keyword' => 'nullable|string',
            'sortPrice' => 'nullable|string',
        ]);

        $page = $request->page !== null ? $request->page : 1;

        if(!$validator->fails()) {
            //set keyword filter in combination with pagination (if the filters are set)
            if(isset($request->keyword)) {
                //combine keyword with pagination
                if(isset($request->pagination)) {
                    $ooProducts = self::searchByKeyword($request->keyword,true)->paginate($request->pagination,['*'],'page',$page);
                }
                else $ooProducts = self::searchByKeyword($request->keyword,true)->paginate(Product::PAGINATION_DEFAULT,['*'],'page',$page);
            }
            else {
                if(isset($request->pagination)) {
                    $ooProducts = Product::paginate($request->pagination,['*'],'page',$page);
                }
                else $ooProducts = Product::paginate(Product::PAGINATION_DEFAULT,['*'],'page',$page);
            }

            //set sortPrice filter
            if(isset($request->sortPrice)) {
                if(strtolower($request->sortPrice) == 'desc') {
                    $ooProducts = $ooProducts->sortByDesc('price');
                }
                elseif(strtolower($request->sortPrice) == 'asc') {
                    $ooProducts = $ooProducts->sortBy('price');
                }
                else return response(['sortPrice' => "Should be string value: 'desc' or 'asc'"],400);

            }

            return response(ProductResource::collection($ooProducts),200);
        }
        else return response($validator->errors(),400);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //oNewProduct contains the validation error or the new product as an Object
        $oNewProduct = $this->newProduct($request);
        if($oNewProduct instanceof Product) return response(new ProductResource($oNewProduct),201);
        else return response($oNewProduct,400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //oNewProduct contains the validation error or the new product as an Object
        $oNewProduct = $this->newProduct($request);
        if($oNewProduct instanceof Product) return response(new ProductResource($oNewProduct),201);
        else return response($oNewProduct,400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $oProduct = Product::find($id);
        if(isset($oProduct)) return response(new ProductResource($oProduct),200);
        else return response([],404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $oProduct = Product::find($id);
        if(isset($oProduct)) return response(new ProductResource($oProduct),200);
        else return response([],404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updatedProduct = $this->updateProduct($request,$id);
        if(isset($updatedProduct) && $updatedProduct instanceof Product) return response(new ProductResource($updatedProduct),200);
        else return response($updatedProduct,400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if($this->deleteProduct($id)) return response('Successful',200);
        else return response('Error',404);
    }
}
