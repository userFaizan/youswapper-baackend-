<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSwap;
use App\Models\Product;
use App\Models\User;
use App\Models\Message;


class UserSwapController extends Controller
{
    //
    public function store(Request $request)
    {
        // $request['sender_id'] = Auth()->id();

        $data = UserSwap::create([
            'sender_id'=> auth()->id(),
            'reciever_id'=> $request->reciever_id,
            'sender_product_id'=> $request->sender_product_id,
            'reciever_product_id'=> $request->reciever_product_id,

        ]);
  $chat; 
  $message= Message::orwhere('sender_id' , auth()->id())->orwhere('reciever_id' , $request->reciever_id )->orwhere('reciever_id' , auth()->id())->orwhere('sender_id' , $request->reciever_id )->first();
   if($message == Null)
   {
    $chat = Message::create(['user_swap_id'=> $data->id,'sender_id'=>auth()->id(), 'reciever_id' => $request->reciever_id, 'chat_id' => $this->unique_code(16)]);

   }else
   {
    $chat= "Chat already exist";
   }
    if ($data && $chat)
      {
          return response()->json([
              'data'    => $chat,
              'message' => 'Swap Added and Chat created successfully',
              'error'   =>FALSE
          ]);
      }
      else
      {
          return response()->json([
              'message' => 'Some Error Occur',
              'error'   =>TRUE
          ]);
      }
    }


    public function get_request(Request $request)
    {
        $param=null;
        // $request['sender_id'] = Auth()->id();
        if($request->type == 1)
        {
            $param = 'sender_id';
        }elseif($request->type == 0){
            $param = 'reciever_id';
        }
        $product = UserSwap::where($param,Auth()->id())->get();
             $response = [];
             $i = 0;
             foreach($product as $products)
             {
                $sender_product = Product::with('images','user')->where('id',$products->sender_product_id)->first();
                $receiver_product = Product::with('images','user')->where('id',$products->reciever_product_id)->first();
                $response[$i]['id'] = $products->id;
                
                $response[$i]['data_sender'] = $sender_product;
                $response[$i]['data_reciever'] = $receiver_product;
                $i++;
             }
            if (count($product)>0) {
                return response()->json([
                    'data'=>$response,
                    'message' => 'Product Found',
                    'error'=>FALSE
                ]);
            } else {
                return response()->json([
                    'data'=>$product,
                    'message' => 'No Product found',
                    'error'=>TRUE
                ]);
            }
      
    }
public function delete(Request $request)
{
    if ($request->accept == False )
    {
        $userswap = UserSwap::where('id', $request->id)->delete();
        if ($userswap)
        {
            return response()->json([
                'message' => 'UserSwap deleted successfully',
                'error'=>FALSE
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'No UserSwap found against this id',
                'error'=>TRUE
            ]);
        }
    }elseif($request->accept == True )
    {
    $userswap = UserSwap::where('id', $request->id)->delete();
    // $message = Message::create(['sender_id'=>auth()->id(), 'reciever_id' => $request->reciever_id, 'chat_id' => $this->unique_code(16)]);
    if ($userswap )
    {
        return response()->json([
            'message' => 'Request Accept successfully',
            'error'=>FALSE
        ]);
    }
    else
    {
        return response()->json([
            'message' => 'Request Rejected successfully ',
            'error'=>TRUE
        ]);
    }
}
    
}
function unique_code($limit)
{
    return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
}




}
