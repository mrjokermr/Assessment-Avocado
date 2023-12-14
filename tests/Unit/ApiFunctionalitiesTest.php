<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ApiFunctionalitiesTest extends TestCase
{
    /**
     * Test if the validation works correct
     */
    public function test_new_product_validator_is_correct(): void
    {
        $aCorrectInput = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20.05
        ];

        //price should be decimal like 20.00 for example
        $aIncorrectInput = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20
        ];

        $this->post(route('product.store'),$aCorrectInput)->assertStatus(201);
        $this->post(route('product.store'),$aIncorrectInput)->assertStatus(400);
    }

    public function test_index_with_optional_filters_is_correct() {
        $aCorrectFilters = [
            'pagination' => 12,
            'page' => 1,
            'sortPrice' => 'desc',
        ];

        $aGetAllResults = [
            'pagination' => 9999999,
            'page' => 1,
        ];

        $aIncorrectPaginationFilter = ['pagination' => -1];
        $aIncorrectPageFilter = ['pagination' => -1];
        $aIncorrectSortPrice = ['sortPrice' => 'descc'];

        $allProductsResponse = $this->get(route('product.index',$aGetAllResults));

        $this->assertEquals(count(json_decode($allProductsResponse->getContent())), Product::all()->count());
        $this->get(route('product.index',$aCorrectFilters))->assertStatus(200);
        $this->get(route('product.index',$aIncorrectPaginationFilter))->assertStatus(400);
        $this->get(route('product.index',$aIncorrectPageFilter))->assertStatus(400);
        $this->get(route('product.index',$aIncorrectSortPrice))->assertStatus(400);
    }

    public function test_search_calls() {
        $aEmptyKeyword = [
            'keyword' => null
        ];

        $searchResponseFailed = $this->get(route('product.search',$aEmptyKeyword));
        $searchResponseCorrect = $this->get(route('product.searchByUri',['keyword' => 'vind']));
        $this->assertTrue($searchResponseFailed->status() === 400 || $searchResponseFailed->status() === 404);
        $this->assertTrue($searchResponseCorrect->status() === 200 || $searchResponseCorrect->status() === 204);
    }

    public function test_delete() {
        $newObject = Product::create([
            'name' => 'test',
            'description' => 'test',
            'price' => 20.00,
        ]);

        $this->delete(route('product.destroy',$newObject->id))->assertStatus(200);
    }
}
