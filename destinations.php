<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Destinations - Travel Website</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
  <h2>All Destinations</h2>
  <div class="row">
    <?php
    $result = $conn->query("SELECT * FROM destinations");
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-md-3 mb-3">
                <div class="card">
                  <img src="assets/images/'.$row['image'].'" class="card-img-top" style="height:150px; object-fit:cover;">
                  <div class="card-body">
                    <h5>'.$row['country'].'</h5>
                    <a href="destination.php?id='.$row['id'].'" class="btn btn-sm btn-primary">Explore</a>
                  </div>
                </div>
              </div>';
    }
    ?>
  </div>
</div>
</body>
</html>
