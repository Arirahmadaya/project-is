<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //tampil semua data Kelompok2
        $product = Product::all();
        
        if($product->count()>0){
            //data product ada
            return response()->json([
                'status'=> true,
                'data' => $product
            ]);
        }else{
          //data product kosong
          return response()->json([
            'status'=> false,
            'message' => 'data product belum ada.'
        ]);
    }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //initial value
        $status = false;
        $message = '';
        
        //validasi data
        $validator = Validator::make($request->all(),[
            'product_name'=>'required|max:100|unique:products',
            'price'=>'required|max:60',
            'stock'=>'required|max:50'
        ], [
            'product_name.required' => 'nama produk product harus diisi.',
            'product_name.max' => 'panjang nama produk product maksimum 100 karakter.',
            'product_name.unique' => 'nama produk product sudah ada.',
            'price.required' => 'harga product harus diisi.',
            'price.max' => 'panjang harga product maksimum 60 karakter.',
            'stock.required' => 'stok product harus diisi.',
            'stock.max' => 'panjang stok product maksimum 50 karakter.'
        ]);

        //creating
        if ($validator->fails()) {
            $status = false;
            $message =$validator->errors();
        }else{
            $status = true;
            $message = "Tambah data products berhasil dilakukan!";
        

        //simpan data
        $product = new Product();
        $product->product_name = $request->product_name;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->save();
        }

        //kirim response json
        return response()-> json([
        'status' => $status,
        'message' => $message
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kelompok2  $kelompok2
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //menampilkan data Kelompok2 berdasarkan id tertentu
        $product = Product :: find ($id);

        if($product != null){
        //jika product ditemukan 
        return response()->json([
            'status'=> true,
            'data' => $product
        ]);      
    } else {
        //jika product tidak ditemukan 
        return response()->json([
            'status'=> false,
            'message' => 'data product belum ada.'
        ]);
    }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kelompok2  $kelompok2
     * @return \Illuminate\Http\Response
     */
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kelompok2  $kelompok2
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         //validasi data
         $validator = Validator::make($request->all(), [
            'product_name' => ['required','max:100',Rule::unique('products')->ignore($id)],
            'price' => ['required','max:60'],
            'stock'=>'required|max:50'
        ],[
            'product_name.required' => 'nama produk product harus diisi.',
            'product_name.max' => 'panjang nama produk product maksimum 100 karakter.',
            'product_name.unique' => 'nama produk product sudah ada.',
            'price.required' => 'harga product harus diisi.',
            'price.max' => 'panjang harga product maksimum 60 karakter.',
            'stock.required' => 'stok product harus diisi.',
            'stock.max' => 'panjang stok product maksimum 50 karakter.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ],400);
        } else {
            //update data
            $product = Product::find($id);
            $product->product_name = $request->product_name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->is_done = $request->is_done;
            $product->save();

                return response()->json([
                    'status' =>true,
                    'message' => 'Data Product Berhasil di-update.'
                ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kelompok2  $kelompok2
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::destroy($id);

        if($product){
            return response()->json([
                'status' => true,
                'message' => 'Data product Berhasil dihapus.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Data product gagal dihapus.'
            ]);
        }
    }
}
