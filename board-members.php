<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
include 'includes/head.php';
requireLogin();

$page = 'board-members';

// Handle promote/demote actions
if(isset($_POST['action'])) {
    if($_POST['action'] === 'promote') {
        $memberId = filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT);
        $position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);
        $order = filter_input(INPUT_POST, 'position_order', FILTER_VALIDATE_INT);
        $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);

        $stmt = $conn->prepare("INSERT INTO board_members (member_id, position, position_order, bio) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $memberId, $position, $order, $bio);
        $stmt->execute();
    }
    
    if($_POST['action'] === 'demote') {
        $memberId = filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT);
        $stmt = $conn->prepare("UPDATE board_members SET status = 'inactive' WHERE member_id = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Board Members Management</h1>
        </div>

        <!-- Current Board Members -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-users me-1"></i>
                Current Board Members
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("
                                SELECT bm.*, m.first_name, m.last_name 
                                FROM board_members bm
                                JOIN members m ON bm.member_id = m.id
                                WHERE bm.status = 'active'
                                ORDER BY bm.position_order
                            ");
                            while($member = $stmt->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($member['position']); ?></td>
                                <td><?php echo $member['position_order']; ?></td>
                                <td>
                                    <button class="btn btn-danger btn-sm demote-member" data-id="<?php echo $member['member_id']; ?>">
                                        Remove from Board
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Promote Member Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-plus me-1"></i>
                Promote Member to Board
            </div>
            <div class="card-body">
                <form id="promoteMemberForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Member</label>
                            <select class="form-select" name="member_id" required>
                                <option value="">Choose member...</option>
                                <?php
                                $stmt = $conn->query("
                                    SELECT m.id, m.first_name, m.last_name 
                                    FROM members m
                                    WHERE m.id NOT IN (SELECT member_id FROM board_members WHERE status = 'active')
                                    AND m.status = 'active'
                                ");
                                while($member = $stmt->fetch_assoc()):
                                ?>
                                <option value="<?php echo $member['id']; ?>">
                                    <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" name="position" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="position_order" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Bio</label>
                            <textarea class="form-control" name="bio" rows="4"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Promote to Board</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#promoteMemberForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'promote');

        $.ajax({
            url: 'board-members.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                location.reload();
            }
        });
    });

    $('.demote-member').on('click', function() {
        if(confirm('Are you sure you want to remove this member from the board?')) {
            const memberId = $(this).data('id');
            
            $.ajax({
                url: 'board-members.php',
                method: 'POST',
                data: {
                    action: 'demote',
                    member_id: memberId
                },
                success: function() {
                    location.reload();
                }
            });
        }
    });
});
</script>

