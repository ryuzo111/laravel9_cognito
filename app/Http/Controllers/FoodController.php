<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function favoriteFood(Request $request)
    {

            $result = "マイナスです";
        $x = $request->x;
        if ($x < 0) {
            $result = "マイナスです";
        }
            return $result;
        // return "テスト完了";


        // $food1 = $request->food1 ?? "トマト";
        // $food2 = $request->food2 ?? "なす";
        // if ($food1 == "お寿司" || $food2 =="焼肉") {
        //     return "私の好きな食べ物です";
        // } else {
        //     return "私のまあまあ好きな食べ物です";
        // }
    }
}
