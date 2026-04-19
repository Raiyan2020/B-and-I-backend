<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Enums\DeviceType;
use App\Models\Admin;
use App\Services\Devices\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function __construct(private readonly DeviceService $deviceService) {}

    public function index(){
        return view('dashboard.auth.login');
    }

    public function login(Request $request){

        $rules=[
            'email' => ['required','email',Rule::exists('admins','email')],
            'password' => ['required', 'min:6' ,'max:100'],
            'device_token' => ['nullable', 'string', 'max:2048'],
            'device_type' => ['nullable', Rule::in(DeviceType::values())],
        ];

        $messages_ar = [
            'required' => 'هذا الحقل لا يجب ان يكون فارغ',
            'email' => 'يجب ان يكون الحقل من نوع بريد الكتروني',
            'email.exists' => 'هذا البريد غير موجود',
            'min' => 'لا يجب ان يقل  الرقم السري عن 6 احرف',
            'max' => 'لا يجب ان يزيد  الرقم السري عن 100 حرف'
        ];

        $messages = (app()->getLocale() == 'ar' )? $messages_ar : [];

        $validator = Validator::make($request->all(),$rules, $messages);

        if($validator->fails()){
            return back()->withErrors($validator);
        }
        $credentials = $validator->safe()->only(['email', 'password']);
        $admin = Admin::query()->where('email', $credentials['email'])->first();

        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            if ($admin->is_blocked) {
                return back()->with(['error' => __('auth.admin_blocked')]);
            }

            auth('admin')->login($admin);
            // if(!auth('admin')->user()->hasRole('super_admin')){
            //     auth('admin')->logout();
            //     abort(403);
            // }
            session()->regenerate();

            if ($request->filled('device_token')) {
                $this->deviceService->syncAdminDevice(
                    auth('admin')->user(),
                    $request->string('device_token')->toString(),
                    $request->input('device_type', DeviceType::Web->value),
                    app()->getLocale(),
                );
            }

            return redirect()->route('admin.home')->with('alert','hello');
        }
        return back()->with(['error'=>__('dashboard.email or password or both are wrong')]);
    }

    public function logout(Request $request){
        if (auth('admin')->check() && $request->filled('device_token')) {
            $this->deviceService->forgetAdminDevice(
                auth('admin')->user(),
                $request->string('device_token')->toString(),
            );
        }

        Auth::logout();
        return redirect()-> route('admin.login');
    }

    public function profile(){
        $admin = Admin::with('roles')->find(\auth('admin')->user()->id);
        $roles = \App\Models\Role::where('guard_name', 'admin')->get();
        return view('dashboard.auth.profile', ['admin' => $admin, 'roles' => $roles]);
    }
    public function update_profile(Request $request){
        $admin = Admin::find(\auth('admin')->user()->id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('admins')->ignore($admin->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
            'role' => 'nullable|exists:roles,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ];

        $messages_ar = [
            'required' => 'هذا الحقل لا يجب ان يكون فارغ',
            'email' => 'يجب ان يكون الحقل من نوع بريد الكتروني',
            'email.unique' => 'هذا البريد مستخدم بالفعل',
            'password.min' => 'لا يجب ان يقل الرقم السري عن 6 احرف',
            'password.confirmed' => 'تأكيد الرقم السري غير متطابق',
            'image.image' => 'يجب ان يكون الملف صورة',
            'image.max' => 'حجم الصورة يجب ان لا يتجاوز 2 ميجابايت',
            'role.exists' => 'الرول المحدد غير موجود'
        ];

        $messages = (app()->getLocale() == 'ar') ? $messages_ar : [];
        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['_token', '_method', 'password', 'password_confirmation', 'role', 'image']);

        if($request->hasFile('image')){
            $data['image'] = $request->file('image');
        }

        if($request->password){
            $data['password'] = $request->password;
        }

        $admin->update($data);

        // Update role if provided
        if($request->role){
            $role = \App\Models\Role::find($request->role);
            if($role && $role->guard_name === 'admin'){
                $admin->syncRoles([$role->name]);
            }
        }

        return back()->with(['success'=>__('dashboard.item updated successfully')]);
    }


}
