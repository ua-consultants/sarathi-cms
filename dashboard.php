<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
requireLogin();

$page = 'dashboard';

// Get statistics
$stats = [
    'blogs' => $conn->query("SELECT COUNT(*) as count FROM blogs")->fetch_assoc()['count'],
    'memories' => $conn->query("SELECT COUNT(*) as count FROM memories")->fetch_assoc()['count'],
    'achievements' => $conn->query("SELECT COUNT(*) as count FROM achievements")->fetch_assoc()['count'],
    'members' => $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count']
];

// Get recent blogs
$recent_blogs = $conn->query("SELECT b.*, u.username FROM blogs b 
                            LEFT JOIN users u ON b.author_id = u.id 
                            ORDER BY b.created_at DESC LIMIT 5");

// Get recent members
$recent_members = $conn->query("SELECT * FROM members ORDER BY joined_date DESC LIMIT 5");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="stats-icon bg-primary">
                                    <i class="fas fa-blog text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Total Blogs</h6>
                                <h3 class="mb-0"><?php echo $stats['blogs']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="stats-icon bg-success">
                                    <i class="fas fa-images text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Total Memories</h6>
                                <h3 class="mb-0"><?php echo $stats['memories']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="stats-icon bg-warning">
                                    <i class="fas fa-trophy text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Achievements</h6>
                                <h3 class="mb-0"><?php echo $stats['achievements']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="stats-icon bg-info">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Total Members</h6>
                                <h3 class="mb-0"><?php echo $stats['members']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <!-- Recent Blogs -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Blogs</h5>
                            <a href="blogs/" class="btn btn-primary btn-sm">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php while($blog = $recent_blogs->fetch_assoc()): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($blog['title']); ?></h6>
                                        <small class="text-muted">
                                            By <?php echo htmlspecialchars($blog['username']); ?> | 
                                            <?php echo date('M d, Y', strtotime($blog['created_at'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?php echo $blog['status'] == 'published' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($blog['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Members -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Members</h5>
                            <a href="members/" class="btn btn-primary btn-sm">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php while($member = $recent_members->fetch_assoc()): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h6>
                                        <small class="text-muted">
                                            Joined: <?php echo date('M d, Y', strtotime($member['joined_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?php echo $member['status'] == 'active' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($member['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>