<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // BUG: Missing authorization check - any authenticated user can see all users
        // Should be admin only
        
        // BUG: No pagination - could return thousands of users
        $users = User::all();

        return response()->json($users);
    }

    public function show($id)
    {
        // BUG: SQL injection vulnerability - direct use of $id without validation
        // BUG: No authorization check - users can see other users' data
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        // BUG: Missing authorization - users can update other users' data
        // BUG: No validation rules
        // BUG: Allows role change - regular users can make themselves admin
        
        $user = User::findOrFail($id);
        
        $user->update($request->all());

        return response()->json($user);
    }

    public function destroy($id)
    {
        // BUG: Missing authorization check
        // BUG: No check if user has orders - will cause foreign key constraint issues
        // BUG: Admin can delete themselves
        
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}

