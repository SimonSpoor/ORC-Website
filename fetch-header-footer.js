
        
        fetch('header.html')
            .then(function (res) {
                if (!res.ok) throw new Error('Failed to load header');
                return res.text();
            })
            .then(function (html) {
                document.getElementById('site-header').innerHTML = html;
                // Initialize menu functionality after header is loaded
                initializeHeaderEvents();
            })
            .catch(function (err) {
                console.warn(err);
            });

        fetch('footer.html')
            .then(function (res) {
                if (!res.ok) throw new Error('Failed to load footer');
                return res.text();
            })
            .then(function (html) {
                document.getElementById('site-footer').innerHTML = html;
            })
            .catch(function (err) {
                console.warn(err);
            });

        fetch('mail_handler.php')
            .then(function (res) {
                if (!res.ok) throw new Error('Failed to load contact form');
                return res.text();
            })
            .then(function (html) {
                document.getElementById('contact-form').innerHTML = html;
            })
            .catch(function (err) {
                console.warn(err);
            });

        // Mobile Menu Toggle Function
        function toggleMenu() {
            const hamburger = document.querySelector('.hamburger-icon');
            const navMenu = document.getElementById('navMenu');

            if (hamburger && navMenu) {
                hamburger.classList.toggle('active');
                navMenu.classList.toggle('active');
            }
        }

        // Initialize header events (dropdown menus for mobile)
        function initializeHeaderEvents() {
            const dropdowns = document.querySelectorAll('.dropdown');

            dropdowns.forEach(function (dropdown) {
                const link = dropdown.querySelector('a');
                link.addEventListener('click', function (e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        dropdown.classList.toggle('active');
                    }
                });
            });

            // Close menu when clicking on a link
            const navLinks = document.querySelectorAll('.navigation a:not(.dropdown > a)');
            navLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    document.querySelector('.hamburger-icon').classList.remove('active');
                    document.getElementById('navMenu').classList.remove('active');
                });
            });
        }

        // Close menu when clicking outside of it
        document.addEventListener('click', function (e) {
            const hamburger = document.querySelector('.hamburger-icon');
            const navMenu = document.getElementById('navMenu');

            if (hamburger && navMenu && !hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });