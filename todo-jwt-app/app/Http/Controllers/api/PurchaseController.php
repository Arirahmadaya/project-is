<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchase = Purchase::all();

        if ($purchase->count() > 0){
            return response()->json([
                'status' => true,
                'data' => $purchase
            ]);
        }else{
        if ($purchase->count() > 0){
            return response()->json([
                'tsatus' => false,
                'message' => 'Data purchase belum ada'                ]);
            }
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
        $status = false;
        $message = '';

        //validasi data
        $validator = Validator::make($request->all(), [
            'product_id' =>  'required|max:60|exists:products,id',
            'amount' => 'required|max:20'
        ], [
            'product_id.required' => 'Nama product harus diisi.',
            'product_id.max' => 'Panjang nama product maksimum 60 karakter.',
            'product_id.exists' => 'Harap masukan id product.',
            'amount.required' => 'Jumlah amount harus diisi.',
            'amount.max' => 'Panjang keterangan amount maksimal 20 karakter'
        ]);

        if ($validator->fails()){
            $status = false;
            $message = $validator->errors();
        }else {
            $status = true;
            $message = "tambah product berhasil dilakukan";
            //simpan data
            $purchase = new Purchase();
            $purchase->product_id = $request->product_id;
            $purchase->amount = $request->amount;
            $purchase->save();
            $purchase = Product::findOrFail($request->product_id);
            $purchase->stock += $request->amount;
            $purchase->save();
        }


        //kirim respon json
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchase = Purchase::find($id);
            if ($purchase != null){
                return response() ->json([
                'ststus' =>true,
                'data' => $purchase
                ]);
            }else{ 
                return response() ->json([
                    'ststus' =>false,
                    'message' => 'Data purchase tidak ada.'
                ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'product_id' =>  [
                'required',
                'max:60',
                'exists:products,id',              
            ],
            'amount' => ['required','max:20']
        ],[
            'product_id.required' => 'Nama product harus diisi.',
            'product_id.max' => 'Panjang nama product maksimum 60 karakter.',
            'product_id.exists' => 'Harap masukan id dari tabel product.',
            'product_id.unique' => 'Id sudah ada harap masukan id yang lain',
           'amount.required' => 'Jumlah amount harus disi.',
            'amount.max' => 'Panjang keterangan amount maksimal 20 karakter'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            $purchases = Purchase::findOrFail($id);
            $purchases->product_id = $request->product_id;
            $purchases->amount = $request->amount;
            $purchases->save();
            $product = Product::findOrFail($request->product_id);
            $product->stock += $request->amount;
            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Data purchase berhasil di update.'
            ], 200);
        }
    }

  

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        $purchase = Purchase::destroy($id);

        if ($purchase) {
            return response()->json([
                'status' => true,
                'message' => 'Data purchase berhasil dihapus.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data purchase gagal dihapus/user tidak ditemukan.'
            ]);
        }
    }
}
