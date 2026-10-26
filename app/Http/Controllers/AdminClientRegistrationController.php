<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminClientRegistrationController extends Controller
{
    public function create()
    {
        $this->requireAdmin();

        return view('admin-client-register');
    }

    public function store(Request $request)
    {
        $admin = $this->requireAdmin();
        $data = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email|unique:clients,email',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date|before_or_equal:today',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'privacy_rules_disclaimer' => 'accepted',
        ], [
            'privacy_rules_disclaimer.accepted' => 'Please acknowledge the Privacy Rules Disclaimer for this client registration.',
        ]);

        DB::transaction(function () use ($data, $admin) {
            $client = Client::create([
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'alias' => $data['alias'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'date_of_birth' => $data['dob'],
                'address' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'],
                'zip_code' => $data['zipcode'],
                'country' => $data['country'],
            ]);

            User::create([
                'fullname' => $data['firstName'] . ' ' . $data['lastName'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'accounttype' => 'client',
                'record_id' => $client->id,
                'approved' => 1,
                'approved_by' => $admin->id,
            ]);
        });

        return redirect('/admin-registrations')
            ->with('status', 'Client account created and approved successfully.');
    }

    protected function requireAdmin(): User
    {
        $user = User::find(session('user.id'));
        abort_unless($user && $user->accounttype === 'admin', 403);

        return $user;
    }
}
