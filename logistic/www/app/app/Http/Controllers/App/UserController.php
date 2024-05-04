<?php

namespace App\Http\Controllers\App;

use App\Avatar;
use App\Location;
use App\Services\Uploader;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends BaseController
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with([
                'customers.orders.packs',
                'customers.orders.payments',
            ]);

        $q = $request->get('q');
        $role = $request->get('role');

        if ($q) {
            $query = $query
                ->where('name', 'LIKE', "%$q%")
                ->orWhere('code', 'LIKE', "%$q%")
                ->orWhere('phone', 'LIKE', "%$q%");
        }

        if (!auth()->user()->is_admin && !auth()->user()->is_accountant) {
            $query = $query
                ->where('id', auth()->id());
        }

        if (!is_null($role)) {
            $query = $query
                ->where('role', $role);
        }

        $users =  $query
            ->orderBy('role')
            ->orderBy('updated_at', 'DESC')
            ->paginate();

        $locationsCN = Location::with([
            'trucks'
        ])
            ->where('type', Location::IN_CHINA)
            ->get();

        $locationsVN = Location::with([
            'trucks'
        ])
            ->where('type', Location::IN_VIETNAM)
            ->get();

        return view('app.user.index', [
            'users' => $users,
            'locationsCN' => $locationsCN,
            'locationsVN' => $locationsVN,
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('only-admin');

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')
            ],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
            // 'role' => [
            //     Rule::in([
            //         User::ROLE_ADMIN,
            //         User::ROLE_SELLER,
            //         User::ROLE_VN_INVENTORY,
            //         User::ROLE_CN_INVENTORY,
            //         User::ROLE_ACCOUNTANT,
            //     ])
            // ]
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('users#create-user')
                ->withErrors($validator)
                ->withInput();
        }

        if (strpos($data['role'], 'location_vn_') !== false) {
            $data['location_id'] = str_replace(
                'location_vn_',
                '',
                $data['role']
            );
            $data['role'] = User::ROLE_VN_INVENTORY;
        }

        if (strpos($data['role'], 'location_cn_') !== false) {
            $data['location_id'] = str_replace(
                'location_cn_',
                '',
                $data['role']
            );
            $data['role'] = User::ROLE_CN_INVENTORY;
        }

        $data['password'] = Hash::make($data['password']);
        $data['permissions'] = [];

        $user = User::create($data);

        return redirect()->to("/users?role={$user->role}");
    }

    public function show(Request $request, User $user)
    {
        Gate::authorize('only-admin');

        $query = User::orderBy('id', 'DESC');

        $q = $request->get('q');
        $role = $request->get('role');

        if ($q) {
            $query = $query
                ->where('name', 'LIKE', "%$q%")
                ->orWhere('code', 'LIKE', "%$q%")
                ->orWhere('phone', 'LIKE', "%$q%");
        }

        if (!is_null($role)) {
            $query = $query
                ->where('role', $role);
        }

        $users =  $query->paginate();

        $user->loadMissing([]);

        $locationsCN = Location::with([
            'trucks'
        ])
            ->where('type', Location::IN_CHINA)
            ->get();

        $locationsVN = Location::with([
            'trucks'
        ])
            ->where('type', Location::IN_VIETNAM)
            ->get();

        return view('app.user.index', [
            'users' => $users,
            'user' => $user,
            'locationsCN' => $locationsCN,
            'locationsVN' => $locationsVN,
        ]);
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('only-admin');

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id, 'id')
            ],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
            // 'role' => [
            //     Rule::in([
            //         User::ROLE_ADMIN,
            //         User::ROLE_SELLER,
            //         User::ROLE_VN_INVENTORY,
            //         User::ROLE_CN_INVENTORY,
            //         User::ROLE_ACCOUNTANT,
            //     ])
            // ]
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('users/' .  $user->id . '#update-user')
                ->withErrors($validator)
                ->withInput();
        }

        $user->location_id = null;
        $user->save();

        if (strpos($data['role'], 'location_vn_') !== false) {
            $data['location_id'] = str_replace(
                'location_vn_',
                '',
                $data['role']
            );
            $data['role'] = User::ROLE_VN_INVENTORY;
        }

        if (strpos($data['role'], 'location_cn_') !== false) {
            $data['location_id'] = str_replace(
                'location_cn_',
                '',
                $data['role']
            );
            $data['role'] = User::ROLE_CN_INVENTORY;
        }

        $data['password'] = Hash::make($data['password']);
        $user->fill($data);
        $user->save();

        return redirect()->to("/users?role={$user->role}");
    }

    public function destroy(User $user)
    {
        Gate::authorize('only-admin');

        if (!$user->is_admin) {
            $user->delete();
        }

        return redirect()->back();
    }

    public function changePassword(Request $request, Uploader $uploader)
    {
        $user = auth()->user();
        if (auth('customer')->check()) {
            $user = auth('customer')->user();
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8'
            ],
        ]);

        if ($validator->fails()) {
            if (auth('customer')->check()) {
                return redirect()
                    ->to("/customers/{$user->id}#change-password")
                    ->withErrors($validator)
                    ->withInput();
            }

            return redirect()
                ->to('/home/#change-password')
                ->withErrors($validator)
                ->withInput();
        }

        $avatar = null;
        if ($data['avatar']) {
            $avatar = $uploader->uploadAvatar($data['avatar']);
        }

        $hash = Hash::make($data['password']);

        if (auth()->check()) {
            DB::table('users')->where('id', auth()->id())->update([
                'password' => $hash
            ]);
        } elseif (auth('customer')->check()) {
            DB::table('customers')->where('id', auth('customer')->id())->update([
                'password' => $hash
            ]);
        }

        if ($avatar) {
            if (auth()->check()) {
                Avatar::query()->updateOrCreate([
                    'user_id' => auth()->id()
                ], [
                    'image' => $avatar
                ]);
            } elseif (auth('customer')->check()) {
                Avatar::query()->updateOrCreate([
                    'user_id' => auth('customer')->id()
                ], [
                    'image' => $avatar
                ]);
            }
        }

        return redirect()->route('user.index');
    }
}
