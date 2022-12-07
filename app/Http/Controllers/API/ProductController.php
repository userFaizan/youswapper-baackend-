<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
Use Exception;
use App\Http\Controllers\Controller;
use Validator;
use App\Http\Requests\CreateProductRequestApi;
use App\Models\Product;
use App\Models\Product_Image;
use App\Models\UserSwap;
use App\Models\Category;
use App\Models\ProductCategories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function store_product(CreateProductRequestApi $request)
    {
    log::info($request);
      
        // $request['category_id']= ucfirst($request->category_id);
        $request['user_id']=auth()->id();
        $request['product_name']= ucfirst($request->product_name);
        $request['description']= ucfirst($request->description);
        $request['address']= ucfirst($request->address);
        $request['lat']= ucfirst($request->lat);
        $request['lng']= ucfirst($request->lng);
        $product = Product::create($request->except('files','category_id'));
        $product->category()->sync($request->category_id);
        if($request->hasFile('files'))
        {
            $files=$request->file('files');
            foreach($files as $file){
                $file_name = $file->store('public/product/images');
                Product_Image::create(['user_id'=>auth()->id(),'product_id'=>$product->id,'image'=>str_replace("public/","",$file_name)]);
            }
        }
        if ($product)
        {
            return response()->json([
                'message' => 'Product Added successfully',
                'error'=>FALSE
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'Product Not Added',
                'error'=>TRUE
            ]);
        }
    }
    public function get_product(Request $request)
    {


            $offset= 0;
            $limit = 15;
            if($request->offset != Null){
                $offset = $request->offset;
            }
            if($request->limit != Null){
                $limit = $request->limit;
            }
if( $request->category_id != 0){
    $categoryId  =  $request->category_id;
            $products    = Product::with('user','category','images')
                        ->where('user_id','!=',auth()->id())
                        ->WhereHas('category', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })->offset($offset)->limit($limit)->get();
            $count    = Product::with('user','category','images')
            ->where('user_id','!=',auth()->id())
            ->WhereHas('category', function ($query) use ($categoryId) {
    $query->where('category_id', $categoryId);
})->count();

if ($products) {   
    return response()->json([
        'data'=>$products,
        'total'=>$count,
        'message' => 'Product Found',
        'error'=>FALSE
    ]);
} else {
    return response()->json([
        'data'=>$products,
        'message' => 'No Product found',
        'error'=>TRUE
    ]);
}

}else{
    $products = Product::with('user','category','images')->get();
    if ($products) {   
        return response()->json([
            'data'=>$products,
            'message' => 'Product Found',
            'error'=>FALSE
        ]);
    } else {
        return response()->json([
            'data'=>$products,
            'message' => 'No Product found',
            'error'=>TRUE
        ]);
    }

}
        

    
       
    }
//get user product
    public function product(Request $request)
    {

        try
        {
           
             $product = Product::with('images','user','category')->where('id',$request->id)->first();
            if ($product) {
                return response()->json([
                    'data'=>$product,
                    // 'total'=>$count,
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
        catch(\Throwable $th)
        {
            return withError($exception->getMessage());
        }
    }


    public function update_product(Request $request)

    {
        $product = Product::where('id', $request->id)->first();
        $product->category()->sync($request->category_id); 
       
        if(isset($request->description)){
            if($request->description != $product->description){
                $product->update(['description'=> $request->description]);
            }
        }
        if(isset($request->product_name)){
            if($request->product_name != $product->product_name){
                $product->update(['product_name'=> $request->product_name]);
            }
        }
        if(isset($request->address)){
            if($request->address != $product->address){
                $product->update(['address'=> $request->address]);
            }
        }
        if(isset($request->lat)){
            if($request->lat != $product->lat){
                $product->update(['lat'=> $request->lat]);
            }
        }
        if(isset($request->lng)){
            if($request->lng != $product->lng){
                $product->update(['lng'=> $request->lng]);
            }
        }
        if($request->hasFile('files'))
        {
            // Product_Image::where("product_id",$product->id);
            $files=$request->file('files');
            foreach($files as $file){
                $file_name = $file->store('public/product/images');
                $file_name = str_replace('public/','', $file_name);
                Product_Image::create(['user_id'=>auth()->id(),'product_id'=>$product->id,'image'=>$file_name]);

            }
            foreach($request->image_delete_ids as $data)
            {
                $delete = Product_Image::where('id', $data)->delete();         
            
            }
        }
        if ($product)
        {
            return response()->json([
                'message' => 'Product Update successfully',
                'error'=>FALSE
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'Product Not Updated',
                'error'=>TRUE
            ]);
        }

    }

    //update favourite

    public function update_favourite(Request $request)
    {
        $product = Product::where('id' , $request->id)->first();
        
        $product->favourite = $request->favourite;
        $product->save();
        if ($product)
        {
            return response()->json([
                'message' => 'Product Update successfully',
                'error'=>FALSE
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'Product Not Updated',
                'error'=>TRUE
            ]);
        }
 
    }
    //get favourite product
    public function get_favourite(Request $request)
    {
        $offset= 0;
        $limit = 10;
        if($request->offset !=null){
            $offset = $request->offset;
        }
        if($request->limit != null){
            $limit = $request->limit;
        }
        
        $product = Product::with('images','category','user')->where('favourite' ,$request->favourite )->offset($offset)->limit($limit)->get();
        $count = count(Product::where('favourite' ,$request->favourite )->get());
        if (count($product)>0) {
            return response()->json([
                'data'=>$product,
                'total'=>$count,
                'message' => 'favourite Found',
                'error'=>FALSE
            ]);
        } else {
            return response()->json([
                'data'=>$product,
                'message' => 'No favourite found',
                'error'=>TRUE
            ]);
        }
    }
    public function destroy_product($id)
    {
        $product = Product::find($id);
        $product->images()->delete();
        $product->delete();
        $useswap= UserSwap::where('sender_product_id',$id)->orWhere('reciever_product_id',$id)->delete();

        if ($product)
        {
            return response()->json([
                'message' => 'Product deleted with related data successfully',
                'error'=>FALSE
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'No Product found against this id',
                'error'=>TRUE
            ]);
        }
    }
    public function get_myproduct(Request $request)
    {
        try
        {
            $product = Product::with('images','category','user')->where('user_id',auth()->id())->get();

            if (count($product)>0) {
                return response()->json([
                    'data'=>$product,
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
        catch(\Throwable $th)
        {
            return withError($exception->getMessage());
        }
    }

// get_discoverproduct

    public function get_discoverproduct(Request $request)
    {

        try
        {
            $offset= 0;
            $limit = 10;
            if($request->offset !=null){
                $offset = $request->offset;
            }
            if($request->limit != null){
                $limit = $request->limit;
            }
             $product = Product::with('images','category','user')->where('user_id','!=',auth()->id())->where('favourite','!=','1')->offset($offset)->limit($limit)->get();
             $count =Product::with('images')->where('user_id','!=',auth()->id())->where('favourite','!=','1')->get()->count();
            if (count($product)>0) {
                return response()->json([
                    'data'=>$product,
                    'total'=>$count,
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
        catch(\Throwable $th)
        {
            return withError($exception->getMessage());
        }
    }





//seach myproduct
    public function search_myproduct(Request $request)
    {
        try
        {
            $product = Product::with('images','category')->where('product_name', 'LIKE', '%'. $request->product_name.'%')->where('user_id',auth()->id())->get();

            if (count($product)>0) {
                return response()->json([
                    'data'=>$product,
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
        catch(\Throwable $th)
        {
            return withError($exception->getMessage());
        }
    }

    //search All product
    public function search_allproduct(Request $request)
    {

            $offset= 0;
            $limit = 10;
            $count=0;
            if($request->offset !=null){
                $offset = $request->offset;
            }
            if($request->limit != null){
                $limit = $request->limit;
            }
            if($request->category_id != "")
            {
                $product = Product::with('images','category','user')->where('category_id',$request->category_id)->where('product_name', 'LIKE', '%'. $request->product_name.'%')->where('user_id','!=',auth()->id())->offset($offset)->limit($limit)->get();
                $count = Product::with('images','category','user')->where('category_id',$request->category_id)->where('product_name', 'LIKE', '%'. $request->product_name.'%')->where('user_id','!=',auth()->id())->get()->count();

            }
            elseif($request->favourite != ""){
                $product = Product::with('images','category','user')->where('favourite',$request->favourite)->where('product_name', 'LIKE', '%'. $request->product_name.'%')->where('user_id','!=',auth()->id())->offset($offset)->limit($limit)->get();
                $count = Product::with('images','category','user')->where('favourite',$request->favourite)->where('product_name', 'LIKE', '%'. $request->product_name.'%')->where('user_id','!=',auth()->id())->get()->count();

            }
               elseif($request->category_id == "")
            {
                $product = Product::with('images','category','user')->where('product_name', 'LIKE', '%'. $request->product_name.'%')->where('user_id','!=',auth()->id())->offset($offset)->limit($limit)->get();
                $count = Product::with('images','category','user')->where('product_name', 'LIKE', '%'. $request->product_name.'%')->where('user_id','!=',auth()->id())->get()->count();

            }
            if (count($product)>0) {
                return response()->json([
                    'data'=>$product,
                    'count'=> $count,
                    'message' => 'Product Found',
                    'error'=>FALSE
                ]);
            } else {
                return response()->json([
                    'data'=>$product,
                    'count'=> $count,
                    'message' => 'No Product found',
                    'error'=>TRUE
                ]);
            }
    }
    
}

