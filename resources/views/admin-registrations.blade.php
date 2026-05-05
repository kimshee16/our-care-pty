@extends('layouts.dashboard')

@section('page-title', 'Registration Management')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Registration Management</h1>
    </div>

    <div class="stats-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <h3 style="color: #666; font-size: 14px; margin: 0 0 10px 0; text-transform: uppercase;">Total Pending</h3>
            <div class="number" id="statPending" style="color: var(--accent); font-size: 32px; font-weight: 700;">0</div>
        </div>
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <h3 style="color: #666; font-size: 14px; margin: 0 0 10px 0; text-transform: uppercase;">Total Approved</h3>
            <div class="number" id="statApproved" style="color: var(--accent); font-size: 32px; font-weight: 700;">0</div>
        </div>
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <h3 style="color: #666; font-size: 14px; margin: 0 0 10px 0; text-transform: uppercase;">Total Rejected</h3>
            <div class="number" id="statRejected" style="color: var(--accent); font-size: 32px; font-weight: 700;">0</div>
        </div>
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <h3 style="color: #666; font-size: 14px; margin: 0 0 10px 0; text-transform: uppercase;">Total Registrations</h3>
            <div class="number" id="statTotal" style="color: var(--accent); font-size: 32px; font-weight: 700;">0</div>
        </div>
    </div>

    <div class="admin-nav" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <button class="nav-btn active" onclick="switchTab('all')" style="padding: 10px 16px; background: white; border: 2px solid var(--accent); color: var(--accent); border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.3s ease;">All Registrations</button>
        <button class="nav-btn" onclick="switchTab('clients')" style="padding: 10px 16px; background: white; border: 2px solid var(--accent); color: var(--accent); border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.3s ease;">Clients</button>
        <button class="nav-btn" onclick="switchTab('healthcare')" style="padding: 10px 16px; background: white; border: 2px solid var(--accent); color: var(--accent); border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.3s ease;">Healthcare Workers</button>
    </div>

    <div class="search-filter" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" id="searchInput" placeholder="Search by name, email..." style="padding: 10px 12px; border: 1px solid #e6e6ee; border-radius: 8px; font-size: 14px; font-family: inherit;">
        <select id="statusFilter" style="padding: 10px 12px; border: 1px solid #e6e6ee; border-radius: 8px; font-size: 14px; font-family: inherit;">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        <button onclick="filterData()" style="padding: 10px 16px; background: var(--accent); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px;">Filter</button>
    </div>

    <div class="table-container" style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto;">
        <table id="registrationTable" style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead style="background: #f9f9fc; border-bottom: 2px solid #e6e6ee;">
                <tr>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">ID</th>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">Name</th>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">Type</th>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">Email</th>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">Phone</th>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">Status</th>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">Approved/Rej. By</th>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">Registered</th>
                    <th style="padding: 16px; text-align: left; color: var(--accent); font-weight: 700; font-size: 13px; text-transform: uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody" style="color: #333;">
            </tbody>
        </table>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); animation: fadeIn 0.3s ease;">
    <div class="modal-content" style="background-color: white; margin: 0; padding: 20px; border-radius: 0; width: 100%; height: 100%; overflow-y: auto; box-shadow: none; animation: slideIn 0.3s ease;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e6e6ee;">
            <h2 style="margin: 0; color: var(--accent);">Registration Details</h2>
            <button class="close-btn" onclick="closeModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
        </div>
        <div class="modal-body" id="modalBody" style="color: #333;">
        </div>
        <div class="modal-footer" style="display: flex; gap: 10px; margin-top: 25px; padding-top: 15px; border-top: 1px solid #e6e6ee; justify-content: flex-end;">
            <button class="modal-btn modal-btn-approve" onclick="approveRegistration()" style="padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.3s ease; background: #7a5cd8; color: white;">Approve</button>
            <button class="modal-btn modal-btn-reject" onclick="rejectRegistration()" style="padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.3s ease; background: #a77ae8; color: white;">Reject</button>
            <button class="modal-btn modal-btn-cancel" onclick="closeModal()" style="padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.3s ease; background: #6c757d; color: white;">Close</button>
        </div>
    </div>
</div>

<style>
    .nav-btn.active {
        background: var(--accent) !important;
        color: white !important;
    }

    .nav-btn:hover {
        background: var(--accent) !important;
        color: white !important;
    }

    tbody tr:hover {
        background: #f9f9fc;
    }

    table th,
    table td {
        padding: 16px 14px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-small {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .btn-view {
        background: #674cbf;
        color: white;
    }

    .btn-view:hover {
        background: #5639a8;
    }

    .btn-approve {
        background: #7a5cd8;
        color: white;
    }

    .btn-approve:hover {
        background: #674cbf;
    }

    .btn-reject {
        background: #a77ae8;
        color: white;
    }

    .btn-reject:hover {
        background: #7a5cd8;
    }

    .no-data {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        font-size: 16px;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 15px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f5;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 700;
        color: var(--accent);
    }

    .detail-value {
        color: #555;
    }

    .modal-btn-approve:hover {
        background: #674cbf !important;
    }

    .modal-btn-reject:hover {
        background: #7a5cd8 !important;
    }

    .modal-btn-cancel:hover {
        background: #5a6268 !important;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .admin-nav {
            flex-direction: column;
        }

        .nav-btn {
            width: 100%;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 12px 8px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .modal-content {
            width: 95%;
        }

        .detail-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    const registrations = @json($registrations);

    let currentFilter = 'all';
    let selectedRegistration = null;

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function updateStats() {
        const pending = registrations.filter(r => r.status === 'pending').length;
        const approved = registrations.filter(r => r.status === 'approved').length;
        const rejected = registrations.filter(r => r.status === 'rejected').length;
        const total = registrations.length;

        document.getElementById('statPending').textContent = pending;
        document.getElementById('statApproved').textContent = approved;
        document.getElementById('statRejected').textContent = rejected;
        document.getElementById('statTotal').textContent = total;
    }

    function renderTable(data) {
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="9" class="no-data">No registrations found</td></tr>';
            return;
        }

        data.forEach(reg => {
            const row = document.createElement('tr');
            const statusClass = `status-${reg.status}`;
            row.innerHTML = `
                <td>#${reg.id}</td>
                <td>${reg.name}</td>
                <td>${reg.type}</td>
                <td>${reg.email}</td>
                <td>${reg.phone}</td>
                <td><span class="status-badge ${statusClass}">${reg.status}</span></td>
                <td>${reg.approved_by_name || (reg.approved_by ? `ID ${reg.approved_by}` : (reg.status === 'pending' ? '-' : 'Unknown'))} </td>
                <td>${reg.registered}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-small btn-view" onclick="viewDetails(${reg.id})">View</button>
                        ${reg.status === 'pending' ? `
                            <button class="btn-small btn-approve" onclick="approveDirectly(${reg.id})">Approve</button>
                            <button class="btn-small btn-reject" onclick="rejectDirectly(${reg.id})">Reject</button>
                        ` : ''}
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    function filterData() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;

        let filtered = registrations;

        if (currentFilter === 'clients') {
            filtered = filtered.filter(r => r.type === 'Client');
        } else if (currentFilter === 'healthcare') {
            filtered = filtered.filter(r => r.type === 'Healthcare Worker');
        }

        if (searchInput) {
            filtered = filtered.filter(r =>
                r.name.toLowerCase().includes(searchInput) ||
                r.email.toLowerCase().includes(searchInput)
            );
        }

        if (statusFilter) {
            filtered = filtered.filter(r => r.status === statusFilter);
        }

        renderTable(filtered);
    }

    function switchTab(tab) {
        currentFilter = tab;
        document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        filterData();
    }

    function viewDetails(id) {
        selectedRegistration = registrations.find(r => r.id === id);
        const modal = document.getElementById('detailModal');
        const modalBody = document.getElementById('modalBody');

        let html = '';

        if (selectedRegistration.type === 'Client') {
            html = `
                <div class="detail-row">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value">${selectedRegistration.name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">${selectedRegistration.email}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">${selectedRegistration.phone}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Date of Birth</div>
                    <div class="detail-value">${selectedRegistration.dob}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Address</div>
                    <div class="detail-value">${selectedRegistration.address}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">City</div>
                    <div class="detail-value">${selectedRegistration.city}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">State</div>
                    <div class="detail-value">${selectedRegistration.state}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Zip Code</div>
                    <div class="detail-value">${selectedRegistration.zipcode}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value"><span class="status-badge status-${selectedRegistration.status}">${selectedRegistration.status}</span></div>
                </div>
            `;
        } else {
            html = `
                <div class="detail-row">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value">${selectedRegistration.name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">${selectedRegistration.email}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">${selectedRegistration.phone}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Profession</div>
                    <div class="detail-value">${selectedRegistration.profession}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Specialization</div>
                    <div class="detail-value">${selectedRegistration.specialization}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">License Number</div>
                    <div class="detail-value">${selectedRegistration.licenseNumber}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Years of Experience</div>
                    <div class="detail-value">${selectedRegistration.experience}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Facility</div>
                    <div class="detail-value">${selectedRegistration.facility}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Facility Address</div>
                    <div class="detail-value">${selectedRegistration.facilityAddress}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Location</div>
                    <div class="detail-value">${selectedRegistration.location}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Credentials</div>
                    <div class="detail-value">${selectedRegistration.credentials}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value"><span class="status-badge status-${selectedRegistration.status}">${selectedRegistration.status}</span></div>
                </div>
            `;
        }

        if (selectedRegistration.type === 'Healthcare Worker') {
            const skills = selectedRegistration.skills && selectedRegistration.skills.length > 0
                ? selectedRegistration.skills.join(', ')
                : 'N/A';
            html += `
                <div class="detail-row">
                    <div class="detail-label">Skills</div>
                    <div class="detail-value">${skills}</div>
                </div>
            `;

            const history = selectedRegistration.employment_history && selectedRegistration.employment_history.length > 0
                ? selectedRegistration.employment_history.map(item => `
                    <div style="margin-bottom: 8px;">
                        <strong>${item.company_name}</strong> (${item.year_started} - ${item.is_current ? 'Present' : item.year_ended})<br>
                        ${item.position}<br>
                        ${item.summary}
                    </div>
                `).join('')
                : '<div>N/A</div>';

            html += `
                <div class="detail-row" style="grid-template-columns: 100%;">
                    <div class="detail-label">Employment History</div>
                    <div class="detail-value" style="display: block;">${history}</div>
                </div>
            `;

            const ndisRequirements = selectedRegistration.ndis_requirements || [];
            const ndisHtml = ndisRequirements.length > 0
                ? ndisRequirements.map(item => {
                    const statusStyles = item.completed
                        ? 'background: #d4edda; color: #155724;'
                        : 'background: #fff3cd; color: #856404;';
                    const documentLink = item.document_link
                        ? `<a href="${escapeHtml(item.document_link)}" target="_blank" rel="noopener" style="color: #674cbf; font-weight: 700;">View document</a>`
                        : '<span style="color: #6b7280;">No document link</span>';

                    return `
                        <div style="display: grid; gap: 6px; padding: 12px 0; border-bottom: 1px solid #f0f0f5;">
                            <div style="display: flex; justify-content: space-between; gap: 12px; align-items: center;">
                                <strong>${escapeHtml(item.requirements)}</strong>
                                <span style="padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; ${statusStyles}">${item.completed ? 'Completed' : 'Pending'}</span>
                            </div>
                            <div>${documentLink}</div>
                        </div>
                    `;
                }).join('')
                : '<div>No NDIS requirements configured.</div>';

            html += `
                <div class="detail-row" style="grid-template-columns: 100%;">
                    <div class="detail-label">NDIS Requirements</div>
                    <div class="detail-value" style="display: block;">${ndisHtml}</div>
                </div>
            `;
        }

        html += `
            <div class="detail-row">
                <div class="detail-label">Approved/Rejected By</div>
                <div class="detail-value">${selectedRegistration.approved_by_name || (selectedRegistration.approved_by ? 'ID ' + selectedRegistration.approved_by : (selectedRegistration.status === 'pending' ? '-' : 'Unknown'))}</div>
            </div>
        `;
        modalBody.innerHTML = html;
        modal.style.display = 'block';
    }

    function closeModal() {
        document.getElementById('detailModal').style.display = 'none';
        selectedRegistration = null;
    }

    function approveRegistration() {
        if (selectedRegistration) {
            fetch(`/admin-registrations/${selectedRegistration.id}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selectedRegistration.status = 'approved';
                    selectedRegistration.approved_by = data.approved_by || selectedRegistration.approved_by || 'Admin';
                    selectedRegistration.approved_by_name = data.approved_by_name || selectedRegistration.approved_by_name || 'Unknown';
                    alert(`${selectedRegistration.name} has been approved!`);
                    closeModal();
                    filterData();
                    updateStats();
                } else {
                    alert('Error approving user: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error approving user: ' + error.message);
            });
        }
    }

    function rejectRegistration() {
        if (selectedRegistration) {
            fetch(`/admin-registrations/${selectedRegistration.id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selectedRegistration.status = 'rejected';
                    selectedRegistration.approved_by = data.approved_by || selectedRegistration.approved_by || 'Admin';
                    selectedRegistration.approved_by_name = data.approved_by_name || selectedRegistration.approved_by_name || 'Unknown';
                    alert(`${selectedRegistration.name} has been rejected!`);
                    closeModal();
                    filterData();
                    updateStats();
                } else {
                    alert('Error rejecting user: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error rejecting user: ' + error.message);
            });
        }
    }

    function approveDirectly(id) {
        fetch(`/admin-registrations/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const reg = registrations.find(r => r.id === id);
                reg.status = 'approved';
                reg.approved_by = data.approved_by || reg.approved_by || 'Admin';
                reg.approved_by_name = data.approved_by_name || reg.approved_by_name || 'Unknown';
                filterData();
                updateStats();
                alert(`${reg.name} has been approved!`);
            } else {
                alert('Error approving user: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving user: ' + error.message);
        });
    }

    function rejectDirectly(id) {
        fetch(`/admin-registrations/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const reg = registrations.find(r => r.id === id);
                reg.status = 'rejected';
                reg.approved_by = data.approved_by || reg.approved_by || 'Admin';
                reg.approved_by_name = data.approved_by_name || reg.approved_by_name || 'Unknown';
                filterData();
                updateStats();
                alert(`${reg.name} has been rejected!`);
            } else {
                alert('Error rejecting user: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting user: ' + error.message);
        });
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('detailModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Initialize
    updateStats();
    renderTable(registrations);
</script>
@endsection



