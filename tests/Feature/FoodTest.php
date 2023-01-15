<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FoodTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        // $query1 = [
        //     "food" => "お寿司"
        // ];
        // $response1 = $this->get(route('favorite_food', $query1));
        // $response1->assertSeeText("私の好きな食べ物です");
        // $query2 = [
        //     "food" => "トマト"
        // ];
        // $response2 = $this->get(route('favorite_food', $query2));
        // $response2->assertSeeText("私のまあまあ好きな食べ物です");

        $query2 = [
            "x" => -1
        ];
        $response2 = $this->get(route('favorite_food', $query2));
        $response2->assertSeeText("マイナスです");
    }
}
