<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $token = Str::random(150);
        $password = $request->password;
        $password_confirm = $request->password_confirm;

        if($password != $password_confirm) {
            return response()->json([
                "status" => false,
                "message" => "Passwords isn't equal"
            ]);
        }

        $user = User::create([
            "username" => $request->username,
            "password" => Hash::make($request->password),
            "token" => $token,
            "email" => $request->email
        ]);

        return response()->json([
            "status" => true,
            "token" => $token,
            "user" => new UserResource($user)
        ]);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                "status" => false,
                "errors" => [
                    "email" => 'Не найден пользователь'
                ],
                "message" => 'Не найден пользователь'
            ]);
        }

        if (Hash::check($request->password, $user->password)) {
            // auth ok
            $token = Str::random(150);
            $user->token = $token;
            $user->save();

            return response()->json([
                "status" => true,
                "token" => $token,
                "user" => new UserResource($user)
            ]);

        } else {
            return response()->json([
                "status" => false,
                "errors" => [
                    "password" => 'Пароль не совпадает'
                ],
                "message" => 'Пароль не совпадает'
            ]);
        }



    }

    public function refresh(Request $request) 
    {
        $token = $request->bearerToken();
        $user = User::where('token', $token)->first();

        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "user not found"
            ], 401);
        }

        return response()->json([
            "status" => true,
            "user" => new UserResource($user),
            "token" => $token
        ]);
    }

    public function edit(Request $request, $id) {
        $request_data = $request->only(['username', 'avatar']);
        $user = User::find($id);
        $fuck = '';

        if(!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ])->setStatusCode(404, 'User not found');
        }

        foreach($request_data as $key => $data) {
            if ($key === 'avatar' && $data != 'null') {
                if($user->avatar) {
                    $this->removeImageInCatalog($user);
                }
                $dataUploadedImage = $this->uploadImageInCatalog($request);
        
                if ($dataUploadedImage['status']) {
                    $user->avatar = $dataUploadedImage['file_path'];
                }
            }
            elseif($key === 'avatar' && $data === 'null') {
                if($user->avatar) {
                    $this->removeImageInCatalog($user);
                }
                $user->$key = Null;
            }
            else {
                $user->$key = $data;
            }
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User is updated',
            'user' => new UserResource($user),
            'request' => $request_data,
            'fuck' => $fuck
        ])->setStatusCode(200);
    }
    
    public function removeImageInCatalog($user) {
        try {
            if(($user->avatar) && file_exists(public_path($user->avatar))) {
                if(unlink(public_path(($user->avatar)))) {
                    return [
                        'status' => true,
                        'message' => 'File remove seccessfully'
                    ];
                }
                else {
                    return [
                        'status' => false,
                        'message' => 'File not remove seccessfully'
                    ];
                }
            }

            return [
                'status' => true,
                'message' => 'File not found'
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function uploadImageInCatalog(Request $request) {
        try {
            if($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $file_name = time() . '.' . $file->getClientOriginalName();
                $catalog = 'avatars';
                $file->move(public_path($catalog), $file_name);
                $file_path = '/' . $catalog . '/' . $file_name;

                return [
                    'status' => true,
                    'message' => 'File uploaded seccessfully',
                    'file_path' => $file_path
                ];
            }

            return [
                'status' => false,
                'message' => 'File not uploaded seccessfully'
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
