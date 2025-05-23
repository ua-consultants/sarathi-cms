<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
include 'includes/head.php';
requireLogin();

$page = 'ebooks';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">E-Books Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEbookModal">
                <i class="fas fa-plus"></i> Add New E-Book
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="ebooksTable">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Downloads</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ebooks = $conn->query("SELECT * FROM ebooks ORDER BY created_at DESC");
                            while($ebook = $ebooks->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <img src="/sarathi/uploads/<?php echo $ebook['cover_image']; ?>" 
                                         class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($ebook['title']); ?></td>
                                <td><?php echo htmlspecialchars($ebook['author']); ?></td>
                                <td><?php echo htmlspecialchars($ebook['category_id']); ?></td>
                                <td><?php echo $ebook['downloads']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $ebook['status'] == 1 ? 'success' : 'danger'; ?>">
                                        <?php echo $ebook['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-ebook" 
                                            data-id="<?php echo $ebook['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-ebook" 
                                            data-id="<?php echo $ebook['id']; ?>">
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

<!-- Add E-Book Modal -->
<div class="modal fade" id="addEbookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New E-Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addEbookForm" action="javascript:void(0);" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" class="form-control" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-control" name="category" required>
                                <option value="">Select Category</option>
                                <?php
                                $categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
                                while($category = $categories->fetch_assoc()) {
                                    echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Language</label>
                            <select class="form-control" name="language" required>
                                <option value="">Select Language</option>
                                <option value="English">English</option>
                                <option value="Nepali">Nepali</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cover Image</label>
                        <input type="file" class="form-control" name="cover_image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PDF File</label>
                        <input type="file" class="form-control" name="pdf_file" accept=".pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save E-Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit E-Book Modal -->
<div class="modal fade" id="editEbookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#ebooksTable').DataTable({
        order: [[1, 'asc']]
    });

    $('#addEbookForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: '/sarathi/admin/ajax/add-ebook.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#addEbookForm')[0].reset();
                    $('#addEbookModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                alert('An error occurred while saving the e-book.');
            }
        });
    });

    $('.edit-ebook').click(function() {
        var id = $(this).data('id');
        $.get('/sarathi/admin/ajax/get-ebook.php', {id: id}, function(response) {
            $('#editEbookModal .modal-content').html(response);
            $('#editEbookModal').modal('show');
        });
    });

    $('.delete-ebook').click(function() {
        if(confirm('Are you sure you want to delete this e-book?')) {
            var id = $(this).data('id');
            $.post('/sarathi/admin/ajax/delete-ebook.php', {id: id}, function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to delete e-book');
                }
            }, 'json');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>