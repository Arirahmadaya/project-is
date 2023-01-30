<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //tampil semua data user
        $user = User::all();
        
        if($user->count()>0){
            //data user ada
            return response()->json([
                'status'=> true,
                'data' => $user
            ]);
        }else{
          //data user kosong
          return response()->json([
            'status'=> false,
            'message' => 'data user belum ada.'
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
        //
    }

  
    public function show($id)
    {
        //menampilkan data Kelompok2 berdasarkan id tertentu
        $user = User :: find ($id);

        if($user != null){
        //jika user ditemukan 
        return response()->json([
            'status'=> true,
            'data' => $user
        ]);      
    } else {
        //jika user tidak ditemukan 
        return response()->json([
            'status'=> false,
            'message' => 'data user belum ada.'
        ]);
    }
    }



    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'name' => ['required',
            Rule::unique('users')->ignore($id)
        ],
            'email' => ['required','email','unique:users'],
            'password' => ['required','confirmed']
        ],[
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.confirmed' => 'password tidak sama.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            //update data
            $user = user::find($id);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil di update.'
            ], 200);
        }
    }


    public function destroy($id)
    {
        $user = User::destroy($id);

        if($user){
            return response()->json([
                'status' => true,
                'message' => 'Data user Berhasil dihapus.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Data user gagal dihapus.'
            ]);
        }
    }

//register
public function register(Request $request)
{
    //validasi 
    $validator = Validator::make($request->all(), [
        'name' => ['required'],
        'email' => ['required','email','unique:users'],
        'password' => ['required','min:6','confirmed']

    ]);

    //jika gagal validasi
    if ($validator->fails()){
        return response()->json([
            'status' => false,
            'message' => $validator->errors()
        ],400);
    }

    //simpan user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

    //login
    $token = Auth::login($user);

    //send response to clients
    return response()->json([
        'status' => true,
        'message' => 'anda berhasil register',
        'user' => $user,
        'authorisation' => [
            'token' => $token,
            'type' => 'Bearer'
        ]
    ],200);
}

public function login(Request $request)
{
    $validator = Validator::make($request->all(),[
        'email' => ['required','email'],
        'password' => ['required'],
    ]);

    //jika gagal validasi
    if ($validator->fails()){
        return response()->json([
            'status' => false,
            'message' => $validator->errors()
        ],400);
    }

    //validasi email dan password disini
    $loginValue = $request->only('email', 'password');

    $token = Auth::attempt($loginValue);

    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Email atau password invalid.'
        ], 401);
    }

    $user = Auth::user();
    return response()->json([
        'status' => true,
        'user' => $user,
        'authorization' => [
            'token' => $token,
            'type' => 'bearer',
        ]
        ], 200);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            'status' => true,
            'message' => 'anda berhasil logout',
            ]);
        }

    public function refresh()
    {
       $token = Auth::refresh();
       $user = Auth::user();
       
       return response()->json([
            'status' => true,
            'messege' => 'Anda Berhasil refresh token.',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200);
    }
}

