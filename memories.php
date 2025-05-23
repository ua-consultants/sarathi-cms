<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
include 'includes/head.php';
requireLogin();

$page = 'memories';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Memories Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemoryModal">
                <i class="fas fa-plus"></i> Add New Memory
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="memoriesTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Event Date</th>
                                <th>Venue</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $memories = $conn->query("SELECT * FROM memories ORDER BY event_date DESC");
                            while($memory = $memories->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <img src="/sarathi/uploads/<?php echo $memory['image_path']; ?>" 
                                         class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($memory['title']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($memory['event_date'])); ?></td>
                                <td><?php echo htmlspecialchars($memory['venue']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $memory['status'] == 1 ? 'success' : 'danger'; ?>">
                                        <?php echo $memory['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-memory" 
                                            data-id="<?php echo $memory['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-memory" 
                                            data-id="<?php echo $memory['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Memory Modal -->
<div class="modal fade" id="addMemoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Memory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMemoryForm" action="javascript:void(0);" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Event Date</label>
                            <input type="date" class="form-control" name="event_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Venue</label>
                            <input type="text" class="form-control" name="venue" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Memory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Memory Modal -->
<div class="modal fade" id="editMemoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#memoriesTable').DataTable({
        order: [[2, 'desc']]
    });

    // Add Memory Form Submission
    $('#addMemoryForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: '/sarathi/admin/ajax/add-memory.php', // Fixed absolute path
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#addMemoryForm')[0].reset();
                    $('#addMemoryModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                alert('An error occurred while saving the memory. Check console for details.');
            }
        });
    });

    // Edit Memory
    $('.edit-memory').click(function() {
        var id = $(this).data('id');
        
        $.get('/sarathi/admin/ajax/get-memory.php', {id: id}, function(response) {
            $('#editMemoryModal .modal-content').html(response);
            $('#editMemoryModal').modal('show');
        });
    });

    // Delete Memory
    $('.delete-memory').click(function() {
        if(confirm('Are you sure you want to delete this memory?')) {
            var id = $(this).data('id');
            
            $.post('/sarathi/admin/ajax/delete-memory.php', {id: id}, function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to delete memory');
                }
            }, 'json');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>