<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users(Request $request){
        $query = User::query();
        $query->where('role', '!=', 'Super Admin');

        return UserResource::collection($this->paginated($query, $request));
    }
    
}