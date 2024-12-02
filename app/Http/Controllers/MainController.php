<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Position;
use App\Models\User;



class MainController extends Controller
{
    public function positions()
    {

        $postitons = Position::select(['id', 'name'])->get();

        return response()->json([
            "success" => true,
            "positions" => $postitons,
        ], 200); 

    }

    public function get_user_by_id($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "The user with the requestedid does not exist",
                "errors" => $validator->errors(),
            ], 400);
        }

        $user = User::where('id', $id)->select('id', 'name', 'email', 'phone','position_id', 'photo')->with('positions')->first();

        if(!$user)
        {
            return response()->json([
                "success" => false,
                "message" => "User not found",
            ], 404); 
        }

        return response()->json([
            "success" => true,
            "user" => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'position' => $user->positions->name,
                'position_id' => $user->position_id,
                'photo' => $user->photo,
            ]
        ], 404); 
    }

    public function get_users_list(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'count' => 'required|integer|min:1',
            'page' => 'required|integer|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Validation failed",
                "errors" => $validator->errors(),
            ], 400);
        }

        $count = $request->count;
        $page = $request->page;
        
        $paginator = User::select(['id', 'name', 'photo', 'position_id', 'phone', 'email'])->with('positions')->paginate($count);

        $users = $paginator->map(function ($user){
            return [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "phone" => $user->phone,
                "position" => $user->positions->name,
                "position_id" => $user->position_id,
                "photo" => $user->photo,
            ];
        });

        return response()->json([
            "success" => true,
            'page' => $paginator->currentPage(),
            'total_pages' => $paginator->lastPage(),
            'total_users' => $paginator->total(),
            'count' => $paginator->count(),
            'links' => [
                'next_url' => $paginator->nextPageUrl(),
                'prev_url' => $paginator->previousPageUrl(),
            ],
            "users" => $users,
        ], 200); 
    }
}
