<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Client;
use App\Models\HealthcareWorker;
use App\Models\NdisRequirementParameter;

class AdminController extends Controller
{
    public function registrations()
    {
        // Fetch all users with their related data, excluding admin users
        $users = User::with([
                'healthcareWorker.skills',
                'healthcareWorker.employmentHistory',
                'healthcareWorker.ndisRequirementsCompleted.parameter',
                'client',
            ])
            ->where('accounttype', '!=', 'admin')
            ->get();
        $ndisParameters = NdisRequirementParameter::orderBy('requirements')->get();
        
        // Build registrations array combining User and their related profile data
        $registrations = [];
        
        foreach ($users as $user) {
            $status = $user->approved == 1 ? 'approved' : ($user->approved == -1 ? 'rejected' : 'pending');
            $approvedByName = optional($user->approvedBy)->fullname;
            if (!$approvedByName && in_array($status, ['approved', 'rejected'], true)) {
                $approvedByName = 'Unknown';
            }

            $registration = [
                'id' => $user->id,
                'name' => $user->fullname,
                'type' => $user->accounttype === 'healthcare_worker' ? 'Healthcare Worker' : 'Client',
                'email' => $user->email,
                'phone' => $user->phone ?? 'N/A',
                'status' => $status,
                'approved_by' => $user->approved_by,
                'approved_by_name' => $approvedByName,
                'registered' => $user->created_at->format('Y-m-d'),
            ];
            
            // Add type-specific fields
            if ($user->accounttype === 'healthcare_worker' && $user->healthcareWorker) {
                $hw = $user->healthcareWorker;
                $completedRequirements = $hw->ndisRequirementsCompleted->keyBy('parameter_id');

                $registration = array_merge($registration, [
                    'profession' => $hw->profession ?? 'N/A',
                    'specialization' => $hw->specialization ?? 'N/A',
                    'licenseNumber' => $hw->license_number ?? 'N/A',
                    'experience' => $hw->experience_years ?? 'N/A',
                    'facility' => $hw->facility_name ?? 'N/A',
                    'facilityAddress' => $hw->facility_address ?? 'N/A',
                    'location' => $hw->location ?? 'N/A',
                    'credentials' => $hw->credentials ?? 'N/A',
                    'skills' => $hw->skills->pluck('skill')->toArray(),
                    'employment_history' => $hw->employmentHistory->map(function ($eh) {
                        return [
                            'company_name' => $eh->company_name,
                            'position' => $eh->job_position,
                            'summary' => $eh->summary,
                            'year_started' => $eh->year_started,
                            'year_ended' => $eh->year_ended,
                            'is_current' => (bool)$eh->is_currently_employed,
                        ];
                    })->toArray(),
                    'ndis_requirements' => $ndisParameters->map(function ($parameter) use ($completedRequirements) {
                        $completed = $completedRequirements->get($parameter->id);

                        return [
                            'id' => $parameter->id,
                            'requirements' => $parameter->requirements,
                            'document_link' => $completed?->document_link,
                            'completed' => (bool) $completed,
                        ];
                    })->toArray(),
                ]);
            } else if ($user->accounttype === 'client' && $user->client) {
                // Client fields - fetch from the client relationship
                $client = $user->client;
                $dob = $client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('F j, Y') : 'N/A';
                $registration = array_merge($registration, [
                    'dob' => $dob,
                    'address' => $client->address ?? 'N/A',
                    'city' => $client->city ?? 'N/A',
                    'state' => $client->state ?? 'N/A',
                    'zipcode' => $client->zip_code ?? 'N/A',
                    'country' => $client->country ?? 'N/A',
                ]);
            } else {
                // Fallback if no client record found
                $registration = array_merge($registration, [
                    'dob' => 'N/A',
                    'address' => 'N/A',
                    'city' => 'N/A',
                    'state' => 'N/A',
                    'zipcode' => 'N/A',
                    'country' => 'N/A',
                ]);
            }
            
            $registrations[] = $registration;
        }
        
        return view('admin-registrations', ['registrations' => $registrations]);
    }

    public function settings()
    {
        $requirements = NdisRequirementParameter::orderBy('requirements')->get();

        return view('admin-settings', compact('requirements'));
    }

    public function storeRequirement(Request $request)
    {
        $data = $request->validate([
            'requirements' => 'required|string|max:255|unique:ndis_requirements_parameters,requirements',
        ]);

        NdisRequirementParameter::create([
            'requirements' => trim($data['requirements']),
        ]);

        return redirect('/admin/settings')->with('status', 'NDIS requirement added successfully.');
    }

    public function destroyRequirement($id)
    {
        $requirement = NdisRequirementParameter::findOrFail($id);
        $requirement->delete();

        return redirect('/admin/settings')->with('status', 'NDIS requirement removed successfully.');
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->approved = 1; // 1 for approved

        $adminSession = Session::get('user');
        $approvedByName = null;

        if (is_array($adminSession) && !empty($adminSession['id'])) {
            $user->approved_by = $adminSession['id'];
            $approvedByName = optional(User::find($adminSession['id']))->fullname;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User approved successfully',
            'approved_by' => $user->approved_by,
            'approved_by_name' => $approvedByName,
        ]);
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->approved = -1; // -1 for rejected

        $adminSession = Session::get('user');
        $approvedByName = null;

        if (is_array($adminSession) && !empty($adminSession['id'])) {
            $user->approved_by = $adminSession['id'];
            $approvedByName = optional(User::find($adminSession['id']))->fullname;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User rejected successfully',
            'approved_by' => $user->approved_by,
            'approved_by_name' => $approvedByName,
        ]);
    }
}
