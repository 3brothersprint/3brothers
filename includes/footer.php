
    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="row gy-4">
          <!-- Brand -->
          <div class="col-md-4 text-center text-md-start">
            <h5 class="footer-title">3 Brothers Print Services</h5>
            <p class="footer-text">
              Professional printing solutions and educational supplies you can
              trust.
            </p>

            <!-- Social Media -->
            <div class="footer-social mt-3">
              <a href="#" aria-label="Facebook">
                <i class="bi bi-facebook"></i>
              </a>
              <a href="#" aria-label="Instagram">
                <i class="bi bi-instagram"></i>
              </a>
              <a href="#" aria-label="Twitter">
                <i class="bi bi-twitter-x"></i>
              </a>
              <a href="#" aria-label="WhatsApp">
                <i class="bi bi-whatsapp"></i>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div class="col-md-4 text-center">
            <h6 class="footer-heading">Quick Links</h6>
            <ul class="footer-links list-unstyled">
              <li><a href="#">Home</a></li>
              <li><a href="#">Services</a></li>
              <li><a href="#">Products</a></li>
              <li><a href="#">About Us</a></li>
            </ul>
          </div>

          <!-- Contact -->
          <div class="col-md-4 text-center text-md-end">
            <h6 class="footer-heading">Contact Us</h6>
            <p class="footer-text mb-1">📞 +63 900 000 0000</p>
            <p class="footer-text mb-1">✉️ info@3brothersprint.com</p>
            <p class="footer-text">📍 Your City, Philippines</p>
          </div>
        </div>

        <hr class="footer-divider" />

        <div class="text-center small">
          © 2025 3 Brothers Print Services & Educational Supplies · All Rights
          Reserved
        </div>
      </div>
    </footer>
<!-- JavaScript -->
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<?php
if(isset($_SESSION['message'])){
  ?>
    alertify.set('notifier','position', 'top-center');
    alertify.success('$_SESSION['message']');
  <?php
}
?>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>