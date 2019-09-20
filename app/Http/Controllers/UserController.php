<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class UserController extends Controller
{
    //
    public function login (Request $request){
      $credentials = $request->only('username', 'password');
      try{
        if (!$token = JWTAuth::attempt($credentials)){
          return response()->json(['error' => 'invalid_credentials'], 400);
        }
        } catch (JWTException $e){
          return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));
      }

      public function register (Request $request){
        $validator = Validator::make($request->all(), [
          'username' => 'required|string|max:300',
          'password' => 'required|string|min:6|confirmed',
          'jml_saldo' => 'required|integer|max:999999999999999999',
        ]);
        if($validator->fails()){
          return response()->json($validator->errors()->tojson(), 400);
        }
        $user = User::create([
          'username' => $request->get('username'),
          'email' => $request->get('email'),
          'jml_saldo' => $request->get('jml_saldo'),
          'password' => Hash::make($request->get('password')),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user', 'token'), 201);
      }

      public function updateSaldo(Request $request){
        try{
          if (!$akun = JWTAuth::parseToken()->authenticate()){
            return response()->json(['user_not_found'], 404);
          }
          } catch(Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
            return response()->json(['token_expired'], $e->getStatusCode());
          } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            return response()->json(['token_invalid'], $e->getStatusCode());
          } catch (Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json(['token_absent'], $e->getStatusCode());
          }
          $id = $akun['id'];
          $edit = User::where('id', $id)->first();
          $edit->jml_saldo = $request->input('jml_saldo');
          $edit->save();
          return $edit;
          // return response()->json(compact('user'));
        }
      }
