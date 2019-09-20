<?php

namespace App\Http\Controllers;
use App\Saldo;
use App\User;
use Illuminate\Http\Request;
use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SaldoController extends Controller
{

    public function index(){
      $data = Saldo::all();
      return response ($data);
    }
    public function show($id){
      $data = Saldo::where('id', $id)->get();
      return response ($data);
    }

    public function store(Request $request){
      $akun = JWTAuth::parseToken()->authenticate();
      $data = new Saldo();
      $data->username=$akun->username;
      $data->jenis=$request->input('jenis');
      $data->jenis_transaksi=$request->input('jenis_transaksi');
      $data->jumlah=$request->input('jumlah');

      if ($request->input('jenis') == 'kredit') {
        $saldo = $akun->jml_saldo - $request->input('jumlah');
      } elseif ($request->input('jenis') == 'debit') {
        $saldo = $akun->jml_saldo + $request->input('jumlah');
      }else{
        return "jenis_transaksi salah";
      }

      $akun->jml_saldo=$saldo;
      $akun->save();
      $data->save();

      return $akun;
    }
}
