<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RETRO KEYS HOME</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<style>
  .bg-dark-blue { background-color: #1b3a57; }
  .text-light-blue { color: #5fa4e2; }
  .btn-dark-blue { background-color: #2a4b6d; color: #ffffff; }
  .btn-dark-blue:hover { background-color: #3b5877; }
</style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark-blue fixed-top">
  <div class="container">
    <a class="navbar-brand" href="landing.php"><img src="logoo.png" width="100px" height="100px"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="landing.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="products.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logoutPage.php">Logout</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="cart.php">
            <i class="material-icons">shopping_cart</i>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<br><br>
<br><br>
<div class="container mt-5 pt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 text-center">
      <div class="card shadow-sm p-4" style="background-color: #1e2a3b; color: #ffffff;">
        <?php if(isset($_SESSION['cName'])): ?>
		
          <h1 class="text-light-blue">Welcome To RetroKeys Shop!, <?php echo htmlspecialchars($_SESSION['cName']); ?></h1>
        <?php else: ?>
          <h1 class="text-light-blue">Welcome To RetroKeys Shop!</h1>
        <?php endif; ?>
        <p class="text-muted">Explore our vast selection of bricks, hollow blocks, and more.</p>
        <a href="products.php" class="btn btn-dark-blue">Browse Products</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
