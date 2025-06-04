<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\ResetPassword;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Mail\Verification;
use App\Traits\BaseResponse;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    use BaseResponse;
    /**
     * All Users.
     */
    public function index(IndexRequest $request)
    {

        $validated = $request->validated();

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $is_verified = $request->input('is_verified');
        $search = $request->input('search', '');
        $role = $request->input('role', '');
        $orderBy = $request->input('orderBy', 'id');
        $orderDirection = $request->input('orderDirection', 'desc');

        $user = User::query()->select('id', 'name', 'sales', 'role', 'telephone', 'is_verified')
            ->withCount('invoice')
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($is_verified !== null && $is_verified == "0", function ($query, $is_verified) {
                $query->where('is_verified', false);
            })
            ->when($is_verified !== null && $is_verified == "1", function ($query, $is_verified) {
                $query->where('is_verified', true);
            })
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sales', 'like', "%{$search}%");
            })
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage = $perPage, $page = $page);

        $user->appends($validated);

        if ($user->count() > 0) {
            return $this->dataFound($user, 'Pengguna');
        }
        return $this->dataNotFound('Pengguna');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store User
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $tokenVerified = Str::random(60);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'token_verified' => Hash::make($tokenVerified),
                'token_verified_before_at' => now()->addMinutes(30),
            ]);

            Mail::to($validated['email'])->send(new Verification($user, $tokenVerified, $validated['password']));

            DB::commit();

            return $this->createSuccess($user);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Show User
     */
    public function show($id)
    {
        $user = User::find($id);

        if ($user) {

            $userLogin = Auth::user();
            if ($userLogin->role != 'admin' && $user->id != $userLogin->id) {
                return $this->unauthorizedResponse();
            }

            return $this->dataFound($user, 'Pengguna');
        }

        return $this->dataNotFound('Pengguna');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update User
     */
    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();

        $user = User::find($id);

        if (!$user) {
            return $this->dataNotFound('Pengguna');
        }

        $userLogin = Auth::user();
        if ($userLogin->role != 'admin' && $user->id != $userLogin->id) {
            return $this->unauthorizedResponse();
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '-' . Str::random(5) . '-' . Str::slug($request->name)  . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/company'), $filename);

            $oldImage = public_path($user->logo['path']);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            $validated['logo'] = "img/company/$filename";
        }

        DB::beginTransaction();
        try {
            if ($user->email != $validated['email']) {

                $tokenTime = Carbon::parse($user->token_verified_before_at);
                if ($user->token_verified_before_at && !$tokenTime->isPast()) {
                    return $this->limitTime('verifikasi email', $tokenTime->format('H:i:s'));
                }

                $tokenVerified = Str::random(60);

                $validated['id'] = $id;
                $validated['token_verified'] = Hash::make($tokenVerified);
                $validated['token_verified_before_at'] = now()->addMinutes(30);
                $validated['is_verified'] = false;
                $validated['email_verified_at'] = NULL;

                Mail::to($validated['email'])->send(new Verification($validated, $tokenVerified));
            }
            $user->update($validated);

            DB::commit();

            return $this->editSuccess($user);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * Reset Password User
     */
    public function resetPassword(ResetPassword $request)
    {
        
        $validated = $request->validated();

        $user = User::where('id', Auth::id())->first();
        if (!$user) {
            return $this->dataNotFound('Token / Pengguna');
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->success('Reset password berhasil.');
    }

    /**
     * Destroy User
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if ($id == Auth::id() || $id == 1) {
            return $this->unauthorizedResponse();
        }

        if (!$user) {
            return $this->dataNotFound('Pengguna');
        }

        $user->delete();

        return $this->deleteSuccess();
    }
}
