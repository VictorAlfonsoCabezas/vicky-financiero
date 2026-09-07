<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Support\CustomerPortal;

class LoginController extends Controller {

    use AuthenticatesUsers;

    protected $redirectTo = '/';
    protected $customerLogin = false;

    public function showLoginForm() {
        return view('auth.login', ['customerLogin' => $this->customerLogin]);
    }

    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user) {
        $roles = $user->roles()->where('rol.status', true)->get();
        if ($roles->isNotEmpty()) {
            $customer = CustomerPortal::customer($user);
            if (($this->customerLogin && !$customer) || ($customer && !$customer->status)) {
                return $this->rejectLogin($request, 'Este usuario no tiene un cliente activo asociado a su empresa.');
            }
            $user->setSession($roles->toArray());
            $request->session()->put('auth.portal', $customer ? 'customer' : 'staff');
            $request->session()->put('last_activity', time());
            if ($customer) {
                $request->session()->forget('url.intended');
                return redirect()->route('portal.index');
            }
            if ($request->session()->get('url.intended') === route('portal.index')) {
                $request->session()->forget('url.intended');
            }
        } else {
            return $this->rejectLogin($request, 'Este usuario no tiene un rol activo');
        }
    }

    protected function rejectLogin(Request $request, $message) {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route($this->customerLogin ? 'login2' : 'login')
            ->withErrors(['error' => $message])->withInput($request->only('username'));
    }

    public function logout(Request $request) {
        $customer = $this->customerLogin || $request->session()->get('auth.portal') === 'customer';
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $request->expectsJson() ? response()->json([], 204) : redirect()->route($customer ? 'login2' : 'login');
    }

    public function username() {
        return 'username';
    }

    protected function credentials(Request $request) {
        return array_merge($request->only($this->username(), 'password'), ['status' => true]);
    }

}
