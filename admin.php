<?php
include 'db.php';

// Fetch all enquiries
$result = $conn->query("SELECT * FROM enquiries ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel - Travel Website</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/128/2200/2200326.png" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
  <h2>Admin Panel - Enquiries</h2>

  <?php if ($result->num_rows > 0) { ?>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Message</th>
          <th>Submitted On</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['message']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  <?php } else { ?>
    <p>No enquiries found.</p>
  <?php } ?>
</div>
</body>
</html>
