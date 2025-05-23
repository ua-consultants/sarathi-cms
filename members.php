<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
requireLogin();

$page = 'members';

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3">Members Management</h1>
            </div>
        </div>

        <ul class="nav nav-tabs" id="memberTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">
                    New Applications <span class="badge bg-warning pending-count"></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button" role="tab">
                    Active Members
                </button>
            </li>
        </ul>

        <div class="tab-content mt-4">
            <div class="tab-pane fade show active" id="applications">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped" id="applicationsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Applied On</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conn->query("SELECT * FROM members 
                                    WHERE status = 'pending' OR status = 'inactive'
                                    ORDER BY joined_date DESC");
                                while ($row = $stmt->fetch_assoc()) {
                                    $statusClass = match($row['status']) {
                                        'pending' => 'warning',
                                        'inactive' => 'warning',
                                        'active' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['joined_date'])); ?></td>
                                        <td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-application" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if($row['status'] == 'pending' || $row['status'] == 'inactive'): ?>
                                                <button class="btn btn-sm btn-success approve-application" data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-application" data-id="<?php echo $row['id']; ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="members">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Bulk Import Members</h5>
                        <form action="ajax/import-members.php" method="post" enctype="multipart/form-data" id="importForm">
                            <div class="mb-3">
                                <label for="excelFile" class="form-label">Upload Excel File</label>
                                <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xlsx,.xls" required>
                                <div class="form-text">Upload Excel file containing member details (Required columns: Name, Email)</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Import & Send Notifications</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped" id="membersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Joined On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conn->query("SELECT * FROM members WHERE status = 'active' ORDER BY joined_date DESC");
                                while ($row = $stmt->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <img src="/uploads/members/<?php echo $row['profile_image']; ?>" 
                                                 class="member-thumb rounded-circle" alt="Profile Photo" 
                                                 width="40" height="40">
                                        </td>
                                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><span class="badge bg-info"><?php echo ucfirst($row['member_type']); ?></span></td>
                                        <td><span class="badge bg-success"><?php echo ucfirst($row['status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($row['joined_date'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-member" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning suspend-member" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Application View Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Member Profile Modal -->
<div class="modal fade" id="memberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Load all required scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTables
    $('#applicationsTable').DataTable({
        "pageLength": 10,
        "order": [[0, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ]
    });

    $('#membersTable').DataTable({
        "pageLength": 10,
        "order": [[0, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [1, 7] }
        ]
    });

    // View Application
    $(document).on('click', '.view-application', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        $.ajax({
            url: '/ajax/view-application.php',
            method: 'GET',
            data: { id: id },
            success: function(response) {
                $('#applicationModal .modal-content').html(response);
                $('#applicationModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Failed to load application details. Error: ' + xhr.status);
            }
        });
    });

    // Approve Application
    $(document).on('click', '.approve-application', function() {
        if(confirm('Are you sure you want to approve this application?')) {
            const id = $(this).data('id');
            const button = $(this);
            button.prop('disabled', true);

            $.ajax({
                url: '/ajax/approve-application.php',
                method: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        alert('Application approved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr);
                    alert('Server error occurred. Check console for details.');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        }
    });

    // Reject Application
    $(document).on('click', '.reject-application', function() {
        const remarks = prompt('Please enter rejection remarks:');
        if(remarks) {
            const id = $(this).data('id');
            const button = $(this);
            button.prop('disabled', true);

            $.ajax({
                url: '/ajax/reject-application.php',
                method: 'POST',
                data: { id: id, remarks: remarks },
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        alert('Application rejected successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr);
                    alert('Server error occurred. Check console for details.');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        }
    });

    // View Member Profile
    $(document).on('click', '.view-member', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: '/ajax/view-member.php',
            method: 'GET',
            data: { id: id },
            success: function(response) {
                $('#memberModal .modal-content').html(response);
                $('#memberModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Failed to load member details. Error: ' + xhr.status);
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>