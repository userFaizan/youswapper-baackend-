<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\UserSwap;
class MessageController extends Controller
{
    //

    public function chats(){
        $chats = Message::where('sender_id', auth()->id())->orWhere('reciever_id', auth()->id() ,'is_accepted', 1)->get()
        ->map(function($obj){
                $user_id = '';
                if(auth()->id() != $obj->sender_id){
                    $user_id = $obj->sender_id;
                }else{
                    $user_id = $obj->reciever_id;
                }
                return [
                    'user' => User::where(['id' => $user_id])->first(),
                    'id'=> $obj->id,
                    'chat_id' => $obj->chat_id
                ];
        });
        return response()->json(['chats'=>$chats]);
    }

    public function update_chat_status(Request $request)
    {
    
        $useswap = UserSwap::findOrFail($request->id);
        $useswap->status = $request->status;
        $useswap->save();
        $chat = Message::where('user_swap_id',$request->id)->first();
        if($request->status  == 1){
            $useswap->message->update([
                'is_accepted' => 1,
            ]);
        $useswap = UserSwap::where('id',$request->id)->delete(); 

        }
        elseif($request->status  == 3){
        $useswap = UserSwap::where('id',$request->id)->delete();
        }
        elseif($request->status  == 5)
        {
            $update_user = User::where('id', $useswap->sender_id)->update([
                'status'=> $status_code
            ]);
        }
        if ($useswap && $chat)
        {
            return response()->json([
                'chat' => $chat,
                'message' => 'UserSwap Status Update successfully',
                'error'=>FALSE
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'UserSwap Status Not Updated',
                'error'=>TRUE
            ]);
        }
    }

}
