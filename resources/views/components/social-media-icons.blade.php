<!-- resources/views/components/social-media-icons.blade.php -->

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<div class="icons-row">
    <div class="social-media-icons">
        <a href="https://facebook.com/yourprofile" target="_blank">
            <i class="fab fa-facebook"></i> <!-- Font Awesome icon for Facebook -->
        </a>
        <a href="https://twitter.com/yourprofile" target="_blank">
            <i class="fab fa-twitter"></i> <!-- Font Awesome icon for Twitter -->
        </a>
        <a href="https://instagram.com/yourprofile" target="_blank">
            <i class="fab fa-instagram"></i> <!-- Font Awesome icon for Instagram -->
        </a>
    </div>
    <div class="extra-icons">
        <a href="#" id="language-selector">
            <i class="fas fa-globe"></i> <!-- Font Awesome icon for Language -->
        </a>
        <a href="#" id="search-button">
            <i class="fas fa-search"></i> <!-- Font Awesome icon for Search -->
        </a>
        <a href="#" id="shopping-cart">
            <i class="fas fa-shopping-cart"></i> <!-- Font Awesome icon for Shopping Cart -->
        </a>
        <a href="#" id="notification-icon">
            <i class="fas fa-bell"></i> <!-- Font Awesome icon for Notifications -->
        </a>
    </div>
</div>

<style>
    .icons-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .social-media-icons a, .extra-icons a {
        margin: 0 10px;
        color: #000; /* Set the color to black */
        text-decoration: none;
    }

    .social-media-icons i, .extra-icons i {
        font-size: 20px; /* Adjust the size as per your requirement */
    }

    .extra-icons {
        display: flex;
        align-items: center;
    }
</style>
