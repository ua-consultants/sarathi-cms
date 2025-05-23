<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
include 'includes/head.php';
requireLogin();

$page = 'achievements';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Achievements Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAchievementModal">
                <i class="fas fa-plus"></i> Add New Achievement
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="achievementsTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Achievement Date</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $achievements = $conn->query("SELECT * FROM achievements ORDER BY achievement_date DESC");
                            while($achievement = $achievements->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <img src="/sarathi/uploads/<?php echo $achievement['image_path']; ?>" 
                                         class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($achievement['title']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($achievement['achievement_date'])); ?></td>
                                <td><?php echo htmlspecialchars($achievement['category']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $achievement['status'] == 1 ? 'success' : 'danger'; ?>">
                                        <?php echo $achievement['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-achievement" 
                                            data-id="<?php echo $achievement['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-achievement" 
                                            data-id="<?php echo $achievement['id']; ?>">
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

<!-- Add Achievement Modal -->
<div class="modal fade" id="addAchievementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Achievement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAchievementForm" action="javascript:void(0);" enctype="multipart/form-data">
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
                            <label class="form-label">Achievement Date</label>
                            <input type="date" class="form-control" name="achievement_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-control" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Award">Award</option>
                                <option value="Recognition">Recognition</option>
                                <option value="Milestone">Milestone</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Achievement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Achievement Modal -->
<div class="modal fade" id="editAchievementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#achievementsTable').DataTable({
        order: [[2, 'desc']]
    });

    $('#addAchievementForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: '/sarathi/admin/ajax/add-achievement.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#addAchievementForm')[0].reset();
                    $('#addAchievementModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                alert('An error occurred while saving the achievement.');
            }
        });
    });

    $('.edit-achievement').click(function() {
        var id = $(this).data('id');
        $.get('/sarathi/admin/ajax/get-achievement.php', {id: id}, function(response) {
            $('#editAchievementModal .modal-content').html(response);
            $('#editAchievementModal').modal('show');
        });
    });

    $('.delete-achievement').click(function() {
        if(confirm('Are you sure you want to delete this achievement?')) {
            var id = $(this).data('id');
            $.post('/sarathi/admin/ajax/delete-achievement.php', {id: id}, function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to delete achievement');
                }
            }, 'json');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>