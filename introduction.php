<?php
session_start();


// Security headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("X-Frame-Options: DENY");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-Laban</title>
    <link rel="stylesheet" href="introduction.css">
</head>
<body>
<main>
   <nav class="navbar">
  <div class="nav-left">
    <div class="logo">🍶🎂<strong>B-Laban</strong></div>
  </div>

  <div class="nav-center social-icons">
    <a href="https://web.facebook.com/profile.php?id=61554331319184" target="_blank"><img src="Photos/facebook.jpg" alt="Facebook Logo"></a>
    <a href="https://www.instagram.com/b.laban.eg/" target="_blank"><img src="Photos/instagram.jpg" alt="Instagram Logo"></a>
    <a href="https://www.tiktok.com/@b.laban.egypt" target="_blank"><img src="Photos/tiktok.jpg" alt="TikTok Logo"></a>
    <div class="profile-icon" onclick="toggleDropdown()">
      <img src="Photos/profile.webp" alt="Profile Icon">
    </div>
    <div class="nav-right profile-container">
    
    <div class="dropdown-menu" id="dropdownMenu">
      <a href="profile.html">Profile</a>
      <a href="#" onclick="signOut()">Sign Out</a>
    </div>
  </div>
  </div>
</nav>

    <section class="Content">
        <div class="meat-content">
            <h1 id="h1">Discover a world of creamy dairy products<br> and luscious confections with our brand.</h1>
            <p>Indulge in the finest dairy products, made with love and fresh ingredients</p>
            <h1 id="sub1">About Blaban</h1>
            <P id="sub2">Established in 2021 , Alexandria, Blaban began as a small factory specializing in traditional Egyptian desserts such as rice pudding, couscous, Om Ali, and ice cream. Blaban revolutionized the market with its innovative product, Ashtouta, which quickly became a bestseller and drove rapid growth.</p>

            <img src="Photos/pic1.jpg" alt="Photo 2" class="photo" id="photo2"><br><br>
            <button><a href="menu.php" target="_blank"><i>See Our Products</i></a></button>
        </div>
        <div class="video-container">
          <video   autoplay muted loop id="num1" playsinline>
              <source src="Videos/snapins-ai_3495371936294425110.mp4" type="video/mp4">
              Your browser does not support the video tag.
          </video>
          <video id="num2" autoplay loop muted playsinline>
            <source src="Videos/1.mp4" type="video/mp4">
            Your browser does not support the video tag.
          </video>
          <video id="num3" autoplay loop muted playsinline>
            <source src="Videos/2.mp4" type="video/mp4">
            Your browser does not support the video tag.
          </video>
          <video id="num4" autoplay loop muted playsinline>
            <source src="Videos/3.mp4" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        </div>
        <div class="text-container">
          <h2>Our Story</h2>
          <p>At B-Laban, we are passionate about bringing you the finest dairy products and delectable desserts. Our journey began with a simple mission: to create high-quality, delicious products that delight your taste buds and nourish your body.</p>
          <p>We take pride in sourcing the freshest ingredients and using traditional methods to craft our products. From creamy yogurt to rich desserts, every item is made with love and care.</p>
          <p>Join us on this delicious journey and experience the magic of B-Laban!</p>
        </div></div>
        <div class="photo-container">
          <img src="Photos/cone.jpg" alt="Photo 1" class="photo" id="photo1">
          <img src="Photos/choclete.jpeg" alt="Photo 3" class="photo" id="photo3">
          <img src="Photos/pic3.jpeg" alt="Photo 4" class="photo" id="photo4">
          <img src="Photos/pic1.jpeg" alt="Photo 5" class="photo" id="photo5">
        </div>
        </main>
    
      

    <footer class="footer">
        <div class="footer-content">
          <p>&copy; <span id="year"></span> B-Laban. All rights reserved.</p>
          <p>9 Abbas Helmy Street, Al Merghani, Cairo, Egypt</p>
        </div>
      </footer>
    
      <script src="introduction.js"></script>




</body>
</html>

