<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Worker Sign Up</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .register-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .register-content {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .register-content h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .register-content p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-row .form-group {
            margin-bottom: 0;
        }

        .register-btn {
            width: 100%;
            padding: 10px 16px;
            background: #674cbf;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-link {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .register-content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    @php
        $healthcareSkills = config('healthcare_skills.options', []);
        $oldSkills = old('skills', ['']);
        if (empty($oldSkills)) {
            $oldSkills = [''];
        }
    @endphp
    <div class="register-container">
        <div class="register-content">
            <a href="{{ url('/signup-option') }}" class="back-link">← Back</a>
            <h1>Healthcare Worker Registration</h1>
            <p>Create your professional account</p>

            @if($errors->any())
                <div style="color:red;margin-bottom:15px;">
                    <ul style="margin:0;padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="healthcareRegisterForm" method="POST" action="{{ url('/healthcare-register') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" value="{{ old('firstName') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" value="{{ old('lastName') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label for="profession">Healthcare Profession *</label>
                    <select id="profession" name="profession" required>
                        <option value="">Select Profession</option>
                        <option value="doctor" {{ old('profession')=='doctor'?'selected':'' }}>Doctor</option>
                        <option value="nurse" {{ old('profession')=='nurse'?'selected':'' }}>Nurse</option>
                        <option value="physiotherapist" {{ old('profession')=='physiotherapist'?'selected':'' }}>Physiotherapist</option>
                        <option value="psychologist" {{ old('profession')=='psychologist'?'selected':'' }}>Psychologist</option>
                        <option value="counselor" {{ old('profession')=='counselor'?'selected':'' }}>Counselor</option>
                        <option value="technician" {{ old('profession')=='technician'?'selected':'' }}>Medical Technician</option>
                        <option value="other" {{ old('profession')=='other'?'selected':'' }}>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="specialization">Specialization *</label>
                    <input type="text" id="specialization" name="specialization" placeholder="e.g., Cardiology, Orthopedics" value="{{ old('specialization') }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="licenseNumber">License Number *</label>
                        <input type="text" id="licenseNumber" name="licenseNumber" value="{{ old('licenseNumber') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="experience">Years of Experience *</label>
                        <input type="number" id="experience" name="experience" min="0" value="{{ old('experience') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="facility">Hospital/Clinic Name *</label>
                    <input type="text" id="facility" name="facility" value="{{ old('facility') }}" required>
                </div>

                <div class="form-group">
                    <label for="facilityAddress">Facility Address *</label>
                    <input type="text" id="facilityAddress" name="facilityAddress" value="{{ old('facilityAddress') }}" required>
                </div>

                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="healthcareLocationInput" name="location" value="{{ old('location') }}" placeholder="Enter your location" autocomplete="off" required>
                    @error('location')<div style="color:red; margin-top:6px; font-size:12px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="credentials">Professional Credentials *</label>
                    <textarea id="credentials" name="credentials" rows="3" placeholder="List your qualifications and certifications" required>{{ old('credentials') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Skills</label>
                    <div style="font-size:12px; color:#666; margin-bottom:8px;">Start typing to search the healthcare skills catalog, then add more as needed.</div>
                    <div id="skillFields">
                        @foreach($oldSkills as $index => $skill)
                            <div class="form-row skill-row" style="grid-template-columns: 1fr auto; gap: 10px; margin-bottom: 10px;">
                                <input type="text" name="skills[]" list="healthcareSkillsList" placeholder="Search healthcare skills" value="{{ $skill }}" autocomplete="off" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                <button type="button" class="{{ $index === 0 ? 'add-skill-btn' : 'remove-skill-btn' }}" style="padding:10px 14px; border:none; background:{{ $index === 0 ? '#674cbf' : '#dc3545' }}; color:white; border-radius:4px; cursor:pointer;">{{ $index === 0 ? '+' : '-' }}</button>
                            </div>
                        @endforeach
                    </div>
                    <datalist id="healthcareSkillsList">
                        @foreach($healthcareSkills as $skillOption)
                            <option value="{{ $skillOption }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group">
                    <label>Employment History</label>
                    <div id="employmentHistoryFields">
                        <div class="employment-row" style="border:1px solid #e9ecef; padding:10px; border-radius:6px; margin-bottom:10px; display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                            <input type="text" name="employment_history[0][company_name]" placeholder="Company Name" value="{{ old('employment_history.0.company_name') }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                            <input type="text" name="employment_history[0][job_position]" placeholder="Job Position" value="{{ old('employment_history.0.job_position') }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                            <textarea name="employment_history[0][summary]" placeholder="Summary" style="grid-column: 1 / -1; padding:10px; border:1px solid #ddd; border-radius:4px;">{{ old('employment_history.0.summary') }}</textarea>
                            <input type="text" name="employment_history[0][year_started]" placeholder="Year Started (YYYY)" value="{{ old('employment_history.0.year_started') }}" style="padding:10px;border:1px solid #ddd;border-radius:4px;">
                            <input type="text" name="employment_history[0][year_ended]" placeholder="Year Ended (YYYY or blank)" value="{{ old('employment_history.0.year_ended') }}" style="padding:10px;border:1px solid #ddd;border-radius:4px;">
                            <label style="display:flex; align-items:center; gap:8px; grid-column:1 / -1;"><input type="checkbox" name="employment_history[0][is_currently_employed]" value="1" {{ old('employment_history.0.is_currently_employed') ? 'checked' : '' }}> Currently employed</label>
                        </div>
                    </div>
                    <button type="button" id="addEmploymentBtn" style="padding:10px 14px; border:none; background:#28a745; color:white; border-radius:4px; cursor:pointer;">Add Another Job</button>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password *</label>
                    <input type="password" id="confirmPassword" name="password_confirmation" required>
                </div>

                <button type="submit" class="register-btn">Create Account</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="{{ url('/login') }}">Sign In</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const skillFields = document.getElementById('skillFields');
            const healthcareSkillsList = document.getElementById('healthcareSkillsList');
            const healthcareRegisterForm = document.getElementById('healthcareRegisterForm');
            const allowedSkills = new Set(
                Array.from(healthcareSkillsList.options).map(option => option.value.trim())
            );
            const healthcareLocationInput = document.getElementById('healthcareLocationInput');

            function validateSkillInput(input) {
                const value = input.value.trim();

                if (value === '' || allowedSkills.has(value)) {
                    input.setCustomValidity('');
                    return true;
                }

                input.setCustomValidity('Please select a skill from the healthcare skills list.');
                return false;
            }

            function clearInvalidSkillInput(input) {
                if (validateSkillInput(input)) {
                    return true;
                }

                input.value = '';
                input.setCustomValidity('');
                return false;
            }

            function attachSkillValidation(input) {
                input.addEventListener('input', function () {
                    validateSkillInput(input);
                });

                input.addEventListener('change', function () {
                    clearInvalidSkillInput(input);
                });

                input.addEventListener('blur', function () {
                    clearInvalidSkillInput(input);
                });
            }

            skillFields.querySelectorAll('input[name="skills[]"]').forEach(attachSkillValidation);
            
            function createSkillRow() {
                const row = document.createElement('div');
                row.className = 'form-row skill-row';
                row.style = 'grid-template-columns: 1fr auto; gap: 10px; margin-bottom: 10px;';
                row.innerHTML = `
                    <input type="text" name="skills[]" list="healthcareSkillsList" placeholder="Search healthcare skills" autocomplete="off" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <button type="button" class="remove-skill-btn" style="padding:10px 14px; border:none; background:#dc3545; color:white; border-radius:4px; cursor:pointer;">-</button>
                `;
                attachSkillValidation(row.querySelector('input[name="skills[]"]'));
                return row;
            }

            skillFields.addEventListener('click', function(e){
                if (e.target.classList.contains('add-skill-btn')) {
                    skillFields.appendChild(createSkillRow());
                    return;
                }

                if (e.target.classList.contains('remove-skill-btn')) {
                    e.target.closest('.form-row').remove();
                }
            });

            healthcareRegisterForm.addEventListener('submit', function (event) {
                const skillInputs = skillFields.querySelectorAll('input[name="skills[]"]');
                let firstInvalidInput = null;

                skillInputs.forEach(function (input) {
                    if (!clearInvalidSkillInput(input) && !firstInvalidInput) {
                        firstInvalidInput = input;
                    }
                });

                if (firstInvalidInput) {
                    event.preventDefault();
                    firstInvalidInput.reportValidity();
                    firstInvalidInput.focus();
                }
            });

            const employmentHistoryFields = document.getElementById('employmentHistoryFields');
            const addEmploymentBtn = document.getElementById('addEmploymentBtn');
            let historyIndex = employmentHistoryFields.querySelectorAll('.employment-row').length;

            addEmploymentBtn.addEventListener('click', function(){
                const row = document.createElement('div');
                row.className = 'employment-row';
                row.style = 'border:1px solid #e9ecef; padding:10px; border-radius:6px; margin-bottom:10px; display:grid; grid-template-columns: 1fr 1fr; gap:10px;';
                row.innerHTML = `
                    <input type="text" name="employment_history[${historyIndex}][company_name]" placeholder="Company Name" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <input type="text" name="employment_history[${historyIndex}][job_position]" placeholder="Job Position" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <textarea name="employment_history[${historyIndex}][summary]" placeholder="Summary" style="grid-column: 1 / -1; padding:10px; border:1px solid #ddd; border-radius:4px;"></textarea>
                    <input type="text" name="employment_history[${historyIndex}][year_started]" placeholder="Year Started (YYYY)" style="padding:10px;border:1px solid #ddd;border-radius:4px;">
                    <input type="text" name="employment_history[${historyIndex}][year_ended]" placeholder="Year Ended (YYYY or blank)" style="padding:10px;border:1px solid #ddd;border-radius:4px;">
                    <label style="display:flex; align-items:center; gap:8px; grid-column:1 / -1;"><input type="checkbox" name="employment_history[${historyIndex}][is_currently_employed]" value="1"> Currently employed</label>
                    <button type="button" class="remove-employment-btn" style="grid-column:1 / -1; padding:10px 14px; border:none; background:#dc3545; color:white; border-radius:4px; cursor:pointer;">Remove</button>
                `;
                employmentHistoryFields.appendChild(row);
                historyIndex++;

                row.querySelector('.remove-employment-btn').addEventListener('click', function () {
                    row.remove();
                });
            });

            employmentHistoryFields.addEventListener('click', function(e){
                if (e.target.classList.contains('remove-employment-btn')) {
                    e.target.closest('.employment-row').remove();
                }
            });
        });
    </script>
</body>
</html>
