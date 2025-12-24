 <aside class="col-md-3 col-lg-2 sidebar p-4 d-flex flex-column">

     <h4 class="logo mb-4 text-center">Admin Panel</h4>

     <!-- Navigation -->
     <ul class="nav flex-column gap-2 flex-grow-1">
         <li class="nav-item">
             <a class="nav-link active" href="./">
                 <i class="bi bi-speedometer2 me-2"></i> Dashboard
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link" href="product-orders.php">
                 <i class="bi bi-bag-check me-2"></i> Product Orders
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link" href="print-orders.php">
                 <i class="bi bi-printer me-2"></i> Print Orders
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link" href="products.php">
                 <i class="bi bi-box-seam me-2"></i> Products
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link" href="#">
                 <i class="bi bi-people me-2"></i> Customers
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link" href="#">
                 <i class="bi bi-bar-chart me-2"></i> Reports
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link" href="settings.php">
                 <i class="bi bi-gear me-2"></i> Settings
             </a>
         </li>
     </ul>

     <!-- Profile Dropdown -->
     <div class="profile-dropdown dropdown">
         <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
             data-bs-toggle="dropdown">
             <img src="https://i.pravatar.cc/40" class="rounded-circle me-2" alt="" />
             <div>
                 <div class="fw-semibold">Admin User</div>
                 <small>Administrator</small>
             </div>
         </a>

         <ul class="dropdown-menu dropdown-menu-dark shadow mt-2">
             <li>
                 <a class="dropdown-item" href="#">
                     <i class="bi bi-person me-2"></i> Profile
                 </a>
             </li>
             <li>
                 <hr class="dropdown-divider">
             </li>
             <li>
                 <a class="dropdown-item text-danger" href="#">
                     <i class="bi bi-box-arrow-right me-2"></i> Logout
                 </a>
             </li>
         </ul>
     </div>

 </aside>