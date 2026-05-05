<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // if already logged in, redirect based on type
        if (Session::has('user')) {
            $user = Session::get('user');
            switch ($user['accounttype']) {
                case 'admin':
                    return redirect('/admin-registrations');
                case 'client':
                    return redirect('/client-dashboard');
                case 'healthcare_worker':
                    return redirect('/healthcare-jobs');
            }
        }
        return view('login');
    }

    public function login(Request $request)
    {
        // validation removed: only credentials check will determine result
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        // Check if email is verified for client and healthcare worker accounts
        if (($user->accounttype === 'client' || $user->accounttype === 'healthcare_worker') && 
            !$user->verified && !$user->email_verified_at) {
            return redirect('/email-verification-pending')
                ->with('status', 'Your account has been created successfully. Please verify your email address to proceed.')
                ->with('email', $user->email);
        }
                    
        // store essential data in session
        Session::put('user', [
            'id' => $user->id,
            'fullname' => $user->fullname,
            'accounttype' => $user->accounttype,
            'approved' => $user->approved,
        ]);

        // redirect based on type
        switch ($user->accounttype) {
            case 'admin':
                return redirect('/admin-registrations');
            case 'client':
                return redirect('/client-dashboard');
            case 'healthcare_worker':
                return redirect('/healthcare-jobs');
        }

        // fallback
        return redirect('/');
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }

}