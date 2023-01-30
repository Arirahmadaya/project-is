<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sale = Sale::all();

        if ($sale->count() > 0){
            return response()->json([
                'status' => true,
                'data' => $sale
            ]);
        }else{
        if ($sale->count() > 0){
            return response()->json([
                'tsatus' => false,
                'message' => 'Data sale belum ada'                ]);
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
            'user_id' => 'required|max:20',
            'amount' => 'required|max:20'
        ], [
            'product_id.required' => 'Nama product harus diisi.',
            'product_id.max' => 'Panjang nama product maksimum 60 karakter.',
            'product_id.exists' => 'Harap masukan id product.',
            'user_id.required' => 'user harus diisi.',
            'user_id.max' => 'Panjang keterangan user maksimal 20 karakter',
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
            $sales = new Sale();
            $sales->product_id = $request->product_id;
            $sales->user_id = $request->user_id;
            $sales->amount = $request->amount;
            $sales->save();
            $product = Product::findOrFail($request->product_id);
            $product->stock -= $request->amount;
            $product->save();
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
        $sale = Sale::find($id);
            if ($sale != null){
                return response() ->json([
                'ststus' =>true,
                'data' => $sale
                ]);
            }else{ 
                return response() ->json([
                    'ststus' =>false,
                    'message' => 'Data sale tidak ada.'
                    ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
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
            'user_id' => ['required','max:20'],
            'amount' => ['required','max:20']
        ],[
            'product_id.required' => 'Nama product harus diisi.',
            'product_id.max' => 'Panjang nama product maksimum 60 karakter.',
            'product_id.exists' => 'Harap masukan id dari tabel product.',
            'user_id.required' => 'user harus diisi.',
            'user_id.max' => 'Panjang keterangan user maksimal 20 karakter',
            'amount.required' => 'Jumlah amount harus diisi.',
            'amount.max' => 'Panjang keterangan amount maksimal 20 karakter'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            $sales = Sale::findOrFail($id);
            $sales->product_id = $request->product_id;
            $sales->user_id = $request->user_id;
            $sales->amount = $request->amount;
            $sales->save();
            $product = Product::findOrFail($request->product_id);
            $product->stock -= $request->amount;
            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Data product berhasil di update.'
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
        $sale = Sale::destroy($id);

        if ($sale) {
            return response()->json([
                'status' => true,
                'message' => 'Data sale berhasil dihapus.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data sale gagal dihapus/user tidak ditemukan.'
            ]);
        }
    }
}
