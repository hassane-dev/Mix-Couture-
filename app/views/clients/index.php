<?php
use App\Core\Session;

// $clients variable is passed from ClientController::index()
$clients = $data['clients'] ?? []; // Ensure $clients is an array, even if empty from controller $data array

$pageTitle = 'Manage Clients';
$activeNav = 'clients';
require_once VIEWS_PATH . 'layout/header.php';
?>

<!-- Page specific content starts -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2"><?php echo htmlspecialchars($pageTitle); ?></h1>
    <a href="<?php echo BASE_URL; ?>/clients/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Client
    </a>
</div>

<?php /* Flash messages are now handled by header.php
$successMessage = Session::getFlash('success');
if ($successMessage): ?>
    <div class="alert alert-success" role="alert">
        <?php echo htmlspecialchars($successMessage); ?>
    </div>
<?php endif; ?>

<?php $errorMessage = Session::getFlash('error');
if ($errorMessage): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($errorMessage); ?>
    </div>
<?php endif; */?>

<div class="card">
    <div class="card-header">
        Client List
    </div>
    <div class="card-body">
        <?php if (empty($clients)): ?>
            <p class="text-center">No clients found. <a href="<?php echo BASE_URL; ?>/clients/create">Add one now!</a></p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Registered At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['ClientID']); ?></td>
                                <td><?php echo htmlspecialchars($client['FullName']); ?></td>
                                <td><?php echo htmlspecialchars($client['Email']); ?></td>
                                <td><?php echo htmlspecialchars($client['PhoneNumber']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($client['Address'] ?? 'N/A')); ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($client['CreatedAt']))); ?></td>
                                <td class="table-actions">
                                    <a href="<?php echo BASE_URL; ?>/clients/edit/<?php echo $client['ClientID']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo BASE_URL; ?>/clients/destroy/<?php echo $client['ClientID']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this client? This action might be irreversible.');" style="display: inline-block;">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Page specific content ends -->

<?php require_once VIEWS_PATH . 'layout/footer.php'; ?>
