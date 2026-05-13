<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirstController extends Controller
{
    //
    public function sum(){
        $a = 10;
        $b = 20;
        $c = $a + $b;
        return "Jumlah: $c";
    }

    public function multiply(Request $request){
        $a = $request->param1;
        if(isset($request->param2)){
            $b = $request->param2;
        } else {
            $b = 1;
        }
        $c = $a * $b;
        return "Hasil perkalian: $c";
    }
}
