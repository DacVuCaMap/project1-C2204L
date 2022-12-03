<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    private $cat;
    private $pro;
    private $htmlSelect;
    public function __construct(){
        $this->pro = new Product();
        $this->cat = new Category();
        $this->htmlSelect = '';
    }


    function RecusiveCat($cat_id, $id=0, $text = ''){
        $category = $this->cat->getAll();
            foreach($category as $item){
                if($item->parent_id == $id){
                    if(!empty($cat_id) && $cat_id==$item->id){
                        $this->htmlSelect .= "<option value = '$item->id' selected>".$text.$item->name."</option>";
                    }else{
                        $this->htmlSelect .= "<option value = '$item->id'>".$text.$item->name."</option>";
                    }
                    $this->RecusiveCat($cat_id, $item->id, $text."-");
                }
            }
        return $this->htmlSelect;
    }

    public function add(){
        $htmlSelect = $this->RecusiveCat(null);
        return view('product.add', compact('htmlSelect'));
    }

    public function postadd(Request $req){

        $rules = [
            'pro_id'       => 'required|unique:product,id|max:8'
            ,'pro_name'    => 'required|max:100'
            ,'cat_id'      => 'required'
            ,'pro_price'   => 'required|regex:/^\d*(\.\d+)?$/'
            ,'pro_quantity'=>'required|regex:/^[0-9]*$/'
            ,'img_1'       => 'nullable|file|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120'
            ,'img_2'       => 'nullable|file|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120'
            ,'img_3'       => 'nullable|file|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120'
            ,'size'        => 'nullable|max:100'
            ,'brand'       => 'nullable|max:100'
            ,'origin'      => 'nullable|max:50'
            ,'type'        => 'nullable|max:50'
            ,'dimention'   => 'nullable|max:50'
            ,'description' => 'nullable|max:500'
       ];
       $message = [
            'pro_id.required'   => 'Product ID must not be left blank!'
            ,'pro_id.unique'    => 'Product ID already exists!'
            ,'pro_id.max'       => 'Product ID is no larger than 8 characters!'
            ,'pro_name.required'=> 'Product name must not be left blank!'
            ,'pro_name.max'     => 'Product ID is no larger than 100 characters!'
            ,'cat_id.required'  => 'Please select Category name!'
            ,'pro_price.required'   => 'Product price must not be left blank!'
            ,'pro_price.regex'      => 'Product price must be float!'
            ,'pro_quantity.regex'   => 'Product quantity must be integer!'
            ,'pro_quantity.required'=> 'Product quantity must not be left blank!'
            ,'img_1.image'          => 'Product image must be image file!'
            ,'img_2.image'          => 'Product image must be image file!'
            ,'img_3.image'          => 'Product image must be image file!'
            ,'img_1.max'            => 'Product image file no lagger than 5MB!'
            ,'img_2.max'            => 'Product image file no lagger than 5MB!'
            ,'img_3.max'            => 'Product image file no lagger than 5MB!'
            ,'max'                  => ':attribute is no larger than :max characters!'
       ];
       $req->validate($rules, $message);
       $pro_id      = $req->pro_id;
       $pro_name    = $req->pro_name;
       $pro_price   = $req->pro_price;
       $cat_id      = $req->cat_id;
       $pro_quantity= $req->pro_quantity;
       $size        = $req->size;
       $brand       = $req->brand;
       $origin      = $req->origin;
       $type        = $req->type;
       $dimention   = $req->dimention;
       $description = $req->description;

       $path_1 = '';
       $path_2 = '';
       $path_3 = '';


       if($req->hasFile('img_1')){
        $file_1        = $req->file('img_1');
        $fileName_1    = time().'.'.$file_1->getClientOriginalName();
        $path_1        = $file_1->storeAs('public/fileUpload', $fileName_1);
        $path_1        = $path = Storage::url($path_1);
       };

       if($req->hasFile('img_2')){
        $file_2        = $req->file('img_2');
        $fileName_2    = time().'.'.$file_2->getClientOriginalName();
        $path_2        = $file_2->storeAs('public/fileUpload', $fileName_2);
        $path_2        = $path = Storage::url($path_2);
       };

       if($req->hasFile('img_3')){
        $file_3        = $req->file('img_3');
        $fileName_3    = time().'.'.$file_3->getClientOriginalName();
        $path_3        = $file_3->storeAs('public/fileUpload', $fileName_3);
        $path_3        = $path = Storage::url($path_3);
       };

       $dataproduct   = [$pro_id, $pro_name, $pro_price, $cat_id, $pro_quantity];
       $dataprodesc   = [$pro_id, $size, $brand, $origin, $type, $dimention, $description ];
       $dataproimage  = [$pro_id, $path_1, $path_2, $path_3];

       if(($this->pro->addproduct($dataproduct))==null
            && ($this->pro->addprodesc($dataprodesc))==null
            && ($this->pro->addproimage($dataproimage))==null){
            return redirect()->route('product.list')->with('msg', 'Add successfully Product!');
       }else{
            return redirect()->route('product.list')->with('msg', 'Add fail Product!');
       }
    }

    public function list(){
        $product = $this->pro->getlistpro();
        return view('product.list', compact('product'));
    }

    public function edit($id){
        $pro = $this->pro->getpro($id);
        $htmlOption = $this->RecusiveCat($pro[0]->cat_id);
        return view('product.edit', compact('pro', 'htmlOption'));
    }

    public function postedit(Request $req){
        $id   = $req->id;
        $path = $this->pro->getimgpro($id);
        $path_1 = $path[0]->img_first;
        $path_2 = $path[0]->img_second;
        $path_3 = $path[0]->img_third;

        $rules = [
            'pro_id'       => 'required|unique:product,id|max:8'
            ,'pro_name'    => 'required|max:100'
            ,'cat_id'      => 'required'
            ,'pro_price'   => 'required|regex:/^\d*(\.\d+)?$/'
            ,'pro_quantity'=>'required|regex:/^[0-9]*$/'
            ,'img_1'       => 'nullable|file|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120'
            ,'img_2'       => 'nullable|file|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120'
            ,'img_3'       => 'nullable|file|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120'
            ,'size'        => 'nullable|max:100'
            ,'brand'       => 'nullable|max:100'
            ,'origin'      => 'nullable|max:50'
            ,'type'        => 'nullable|max:50'
            ,'dimention'   => 'nullable|max:50'
            ,'description' => 'nullable|max:500'
       ];
       $message = [
            'pro_id.required'   => 'Product ID must not be left blank!'
            ,'pro_id.unique'    => 'Product ID already exists!'
            ,'pro_id.max'       => 'Product ID is no larger than 8 characters!'
            ,'pro_name.required'=> 'Product name must not be left blank!'
            ,'pro_name.max'     => 'Product ID is no larger than 100 characters!'
            ,'cat_id.required'  => 'Please select Category name!'
            ,'pro_price.required'   => 'Product price must not be left blank!'
            ,'pro_price.regex'      => 'Product price must be float!'
            ,'pro_quantity.regex'   => 'Product quantity must be integer!'
            ,'pro_quantity.required'=> 'Product quantity must not be left blank!'
            ,'img_1.image'          => 'Product image must be image file!'
            ,'img_2.image'          => 'Product image must be image file!'
            ,'img_3.image'          => 'Product image must be image file!'
            ,'img_1.max'            => 'Product image file no lagger than 5MB!'
            ,'img_2.max'            => 'Product image file no lagger than 5MB!'
            ,'img_3.max'            => 'Product image file no lagger than 5MB!'
            ,'max'                  => ':attribute is no larger than :max characters!'
       ];
       $req->validate($rules, $message);
       $pro_id      = $req->pro_id;
       $pro_name    = $req->pro_name;
       $pro_price   = $req->pro_price;
       $cat_id      = $req->cat_id;
       $pro_quantity= $req->pro_quantity;
       $size        = $req->size;
       $brand       = $req->brand;
       $origin      = $req->origin;
       $type        = $req->type;
       $dimention   = $req->dimention;
       $description = $req->description;
       $update_at   = now();

       if($req->hasFile('img_1')){
        $file_1        = $req->file('img_1');
        $fileName_1    = time().'.'.$file_1->getClientOriginalName();
        $path_1        = $file_1->storeAs('public/fileUpload', $fileName_1);
        $path_1        = $path = Storage::url($path_1);
       };

       if($req->hasFile('img_2')){
        $file_2        = $req->file('img_2');
        $fileName_2    = time().'.'.$file_2->getClientOriginalName();
        $path_2        = $file_2->storeAs('public/fileUpload', $fileName_2);
        $path_2        = $path = Storage::url($path_2);
       };

       if($req->hasFile('img_3')){
        $file_3        = $req->file('img_3');
        $fileName_3    = time().'.'.$file_3->getClientOriginalName();
        $path_3        = $file_3->storeAs('public/fileUpload', $fileName_3);
        $path_3        = $path = Storage::url($path_3);
       };

       $dataproduct   = [$pro_id, $pro_name, $pro_price, $cat_id, $pro_quantity, $update_at, $id];
       $dataprodesc   = [$pro_id, $size, $brand, $origin, $type, $dimention, $description, $id];
       $dataproimage  = [$pro_id, $path_1, $path_2, $path_3, $id];

        if(($this->pro->upproduct($dataproduct))==null
           && ($this->pro->upprodesc($dataprodesc))==null
           && ($this->pro->upproimage($dataproimage))==null){
            return redirect()->route('product.list')->with('msg', 'Edit successfully Product!');
        }else{
            return redirect()->route('product.list')->with('msg', 'Edit fail Product!');
        }
    }

    public function delete($id){
        if(($this->pro->delproduct($id))==null
           && ($this->pro->delprodesc($id))==null
           && ($this->pro->delproimage($id))==null){
            return redirect()->route('product.list')->with('msg', 'Edit successfully Product!');
        }else{
            return redirect()->route('product.list')->with('msg', 'Edit fail Product!');
        }
    }

}
