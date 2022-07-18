<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\BusinessTypeRequest;
// use App\FoodCustomCat;
use App\FoodCustomCat;
use App\NinjaProduct;
use App\NinjaProductCat;
use App\NinjaProductBridge;
use App\Product;
use App\ProductImages;    
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Sentinel;
use Response;
use Image;
use File;
use App\User;
use App\Banner;
use Datatables;
use Illuminate\Http\Request;
use Validator;
use Redirect;

class FoodCustomCatController extends AWController {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
//        $products =  NinjaProductBridge::select(['ninja_products.id', 'ninja_products.title','ninja_products.created_at'])->where('is_show_on_food_customize',1)
//            ->join('ninja_products','ninja_products_bridge.ninja_product_id','=','ninja_products.id')->where('deleted_at', Null)->get();
        $products = NinjaProductCat::where('is_customized_cat', 1)->get();
        return view('admin.food_custom_cat.index',compact('products'));
    }

    public function data()
    {
//        $slider = FoodCustomCat::select(['food_custom_cat.id', 'food_custom_cat.title','food_custom_cat.created_at']);
//        $products =  NinjaProductBridge::select(['ninja_products.id', 'ninja_products.title','ninja_products.created_at'])->where('is_show_on_food_customize',1)
//            ->join('ninja_products','ninja_products_bridge.ninja_product_id','=','ninja_products.id')->get();

//         dd($products);
//        return Datatables::of($slider)
//            ->editColumn('created_at','{{ $created_at->diffForHumans() }}')
//            ->add_column('actions','<a href="{{ route(\'update/food_custom_cat\', $id) }}"><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="Edit Slider"></i></a>
//                                    <a href="{{ route(\'confirm-delete/food_custom_cat\', $id) }}" data-toggle="modal" data-target="#delete_confirm"><i class="livicon" data-name="remove-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="Delete Slider"></i></a>
//                                   ')
//            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getCreate()
    {
        $templates = getTemplates();
        $image_fields = ['image'];

        return view('admin.food_custom_cat.create', compact('image_fields', 'templates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postCreate(Request $request)
    {
        $rules = array(
            'cat_title'                 => 'required|min:3',
            'product_image'          => 'required',
        );
        // Create a new validator instance from our validation rules
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, we'll exit the operation now.
        if ($validator->fails()) {
            // Ooops.. something went wrong
            return Redirect::back()->withInput()->withErrors($validator);
        }
        $product = new NinjaProductCat($request->all());
        $product->save();
        $productBridge = NinjaProductBridge::create([
            'ninja_product_id' => $product->id,
            'branch' => $request->location,
            'is_show_on_food_customize' => 1,
        ]);

        return redirect('admin/food_custom_cat');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getEdit(NinjaProductCat $food_custom_cat)
    {

        $image_fields = ['image'];

        return view('admin.food_custom_cat.edit', compact('food_custom_cat', 'image_fields'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function postEdit(Request $request, NinjaProductCat $slider)
    {
        $rules = array(
            'cat_title'                 => 'required|min:3',
            'product_image'          => 'required',
        );
        // Create a new validator instance from our validation rules
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, we'll exit the operation now.
        if ($validator->fails()) {
            // Ooops.. something went wrong
            return Redirect::back()->withInput()->withErrors($validator);
        }
        $slider->update($request->except('_method'));

        $productBridge = NinjaProductBridge::where('ninja_product_id', $slider->id)->update([
            'ninja_product_id' => $slider->id,
            'branch' => $request->location,
        ]);

        return redirect('admin/food_custom_cat');
    }

    /**
     * Remove page.
     *
     * @param $website
     * @return Response
     */
    public function getModalDelete(NinjaProductCat $food_custom_cat)
    {
        $model = 'business_type';
        $confirm_route = $error = null;
        try {
            $confirm_route =  route('delete/food_custom_cat',['id'=>$food_custom_cat->id]);
            return View('admin/layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));
        } catch (GroupNotFoundException $e) {

            $error = trans('business_type/message.error.delete', compact('id'));
            return View('admin/layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDelete(NinjaProductCat $slider)
    {
        $slider->delete();
        return redirect('admin/food_custom_cat');
    }



    public function showBusinessTypes($slug)
    {
        $food_custom_cat = FoodCustomCat::where('slug',$slug)->first();

        // echo $food_custom_cat->id;

        // $servicedatas = Banner::get();  

       
        $serviceproviders = User::where('business_type', $food_custom_cat->id)->where('user_type', 'vendor')
        ->leftjoin('banners','users.id', '=', 'banners.user_id')
        ->select('*', 'users.id AS uid')
        ->get();

        // $servicedata = Banner::where('id', $food_custom_cat->id)->get();
        // echo '<pre>';
        // print_r($serviceproviders);
        // echo $food_custom_cat->id;

        return View('site.restaurant.business-type', compact('food_custom_cat', 'serviceproviders', 'slug'));

    }

    public function showserviceprovider($slug, $spid)
    {
            // echo $slug;
            // echo $spid;
        if(Sentinel::check())
        {
            // $user = Sentinel::getUser();
            $servic_providers = Product::where('user_id',$spid)->where('type','service')
            ->leftjoin('product_images','products.id', '=', 'product_images.product_id')
            ->get();

            // $service_image = ProductImages::where('user_id',$spid)->where('type','service')->get()
            $serviceproviderprofile = User::where('users.id', $spid)->where('user_type', 'vendor')
                ->leftjoin('banners','users.id', '=', 'banners.user_id')
                ->select('*', 'users.id AS uid')
                ->first();

            // print_r($serviceproviderprofile);
            // echo $user->id;
        return View('site.restaurant.service-provider', compact('servic_providers', 'serviceproviderprofile'));
        }
        
    }

}