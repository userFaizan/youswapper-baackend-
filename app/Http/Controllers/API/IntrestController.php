<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
Use Exception;
use App\Http\Controllers\Controller;
use App\Models\Intrest;
use App\Models\User;
use App\Models\UserIntrest_pivot;
use DB;
use Illuminate\Support\Facades\Auth;
class IntrestController extends Controller
{
    public function get_intrests()
    {
        try {
            $intrest = Intrest::all();
            
            if (count($intrest)>0)
             {
               
                return response()->json([
                    'data'=>$intrest,
                    'message' => 'Intrest Found',
                    'error'=>FALSE
                ]);
            } else {
                return response()->json([
                    'data'=>$intrest,
                    'message' => 'No Intrest found',
                    'error'=>TRUE
                ]);
            }
        } catch (\Throwable $th) {
            return withError($exception->getMessage());        
         }

    }

    public function store_intrest(Request $request){

    $data = User::with('intrests')->where('id',auth()->id())->first();

        if($data != "" || $data != NULL)
        {
            if($request->intrest_id !="" || $request->intrest_id != NULL)
            {
                 $intrest = User::where('id' , Auth()->id())->first();

                $intrest->intrests()->detach();

                $intrest->intrests()->attach($request->intrest_id);

            }
            return response()->json([
                'message' => 'Intrest Added Successfully',
                'error'=>FALSE
            ]);

        }
        else{
            return response()->json([
                'message'=>'No record found to update against given id ',
                'error'=>TRUE
                // 'code'=>202,
            ]);

        }
    }
}
