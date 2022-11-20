<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
Use Exception;
use App\Http\Controllers\Controller;
use App\Models\Faq;
class FAQController extends Controller
{

     public function get_faqs()
     {
       
            $faqs = Faq::where('id', 1)->first();
            if ($faqs) {
                return response()->json([
                    'data'=>[
                        'id'=>  $faqs->id,
                        'description'=>  $faqs->description,
                    ],
                    'message' => 'faqs Found',
                    'error'=>FALSE
                ]);
            } else {
                return response()->json([
                    'data'=>$faqs,
                    'message' => 'No faqs found',
                    'error'=>TRUE
                ]);
            }
     
     }
}