<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    /**
     * Display a listing of all users (Read - All).
     */
    public function index(): JsonResponse
    {
        $users = User::all();

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ], 200);
    }

    /**
     * Store a newly created user in storage (Create).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    /**
     * Display the specified user (Read - One).
     * Uses Laravel Route Model Binding to automatically fetch the User by ID.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $user,
        ], 200);
    }

    /**
     * Update the specified user in storage (Update).
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
        ]);

        // If password is not provided in update, don't overwrite it
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user,
        ], 200);
    }

    /**
     * Remove the specified user from storage (Delete).
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ], 200);
    }

    /**
     * Demonstrate how to run complex queries in Laravel:
     * 1. Advanced Eloquent Query Builder (Chaining, subquery closures, conditions)
     * 2. Query Builder with Joins & Aggregates (DB::table)
     * 3. Raw SQL with Parameter Bindings (DB::select)
     */
    public function complexQuery(Request $request): JsonResponse
    {
        $search = $request->query('search', '');
        $status = $request->query('status');

        // Approach 1: ADVANCED ELOQUENT QUERY BUILDER
        $users = User::query()
            ->select('id', 'name', 'email', 'created_at')
            ->selectRaw('DATE(created_at) as registered_date')
            // Grouped WHERE condition: AND (name LIKE ... OR email LIKE ...)
            ->where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                }
            })
            // Conditional filtering: only runs when 'status' is present
            ->when($status === 'verified', function ($query) {
                $query->whereNotNull('email_verified_at');
            })
            ->when($status === 'unverified', function ($query) {
                $query->whereNull('email_verified_at');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Approach 2: QUERY BUILDER WITH JOINS & AGGREGATE (DB facade)
        // Join users with sessions table and count sessions per user
        $sessionStats = DB::table('users')
            ->leftJoin('sessions', 'users.id', '=', 'sessions.user_id')
            ->select('users.id', 'users.name', DB::raw('COUNT(sessions.id) as session_count'))
            ->groupBy('users.id', 'users.name')
            ->get();

        // Approach 3: RAW SQL WITH PARAMETER BINDINGS (DB::select)
        $rawUsers = DB::select("
            SELECT id, name, email, created_at
            FROM users
            WHERE created_at <= :now
            ORDER BY id DESC
            LIMIT 5
        ", [
            'now' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'status' => 'success',
            'approach_1_eloquent_builder' => $users,
            'approach_2_joins_aggregates' => $sessionStats,
            'approach_3_raw_sql' => $rawUsers,
        ], 200);
    }

    /* -------------------------------------------------------------
     * Previous playground methods
     * ------------------------------------------------------------- */

    public function dynamic($name1, $name2, $name3)
    {
        if (View::exists('about.index')) {
            return view('about.index', ['name1' => $name1, 'name2' => $name2, 'name3' => $name3]);
        }

        return 'View does not exist';
    }

    public function loop()
    {
        $users = ['Koushik', 'Mouli', 'Bhai'];

        return view('user', ['users' => $users]);
    }
}
