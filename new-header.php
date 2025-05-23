<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }

        .headerContent {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .logo img {
            height: 65px;
            width: auto;
        }

        .nav {
            display: flex;
            justify-content: center;
            flex: 1;
            margin-right: -105px;
        }

        .navList {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            margin: 0;
            padding: 0;
            list-style: none;
            justify-content: center;
            margin-right: 50px;
        }

        .navList span {
            font-family: 'Dancing Script', cursive;
            font-size: 1rem;
            font-weight: 700;
            color: #0a2b4f;
        }

        .navList > li {
            position: relative;
            padding: 1rem 0;
            cursor: pointer;
        }

        .megaMenu {
            display: none;
            position: absolute;
            left: 50%;
            top: 100%;
            width: auto;
            min-width: 200px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            transform: translateX(-50%);
            border-radius: 4px;
            z-index: 1001;
        }

        .navList > li:hover .megaMenu {
            display: block;
        }

        .aboutList {
            width: 200px;
            list-style: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 0;
            margin: 0 auto;
        }

        .aboutList li {
            color: black;
            border: none;
            padding: 0.5rem;
        }

        .aboutList li a {
            color: #0a2b4f;
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
            transition: background-color 0.3s ease;
        }

        .aboutList li a:hover {
            color: #ffd700;
            transform: scale(1.1);
        }
        .gridList {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            padding: 0;
            margin: 0 auto;
        }

        .iconGroup {
            flex: 0 0 200px;
            display: flex;
            gap: 1.5rem;
            justify-content: flex-end;
            align-items: center;
        }

        .searchContainer {
            position: relative;
            display: flex;
            align-items: center;
        }

        .searchInput {
            position: absolute;
            right: 100%;
            width: 0;
            padding: 0.5rem;
            border: 1px solid #eee;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            opacity: 0;
        }

        .searchInput.active {
            width: 200px;
            opacity: 1;
            margin-right: 0.5rem;
        }

        .icon {
            font-size: 1.25rem;
            color: #0a2b4f;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .icon:hover {
            color: #ffd700;
            transform: scale(1.1);
        }

        /* Mobile Menu Button */
        .menuToggle {
            display: none;
            font-size: 1.5rem;
            color: #0a2b4f;
            cursor: pointer;
            margin-right: 1rem;
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .headerContent {
                padding: 0.5rem;
            }

            .navList {
                gap: 1.5rem;
            }

            .gridList {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .menuToggle {
                display: block;
            }

            .nav {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                flex-direction: column;
                padding: 2rem;
                transition: left 0.3s ease;
            }

            .nav.active {
                left: 0;
            }

            .navList {
                flex-direction: column;
                width: 100%;
                gap: 2rem;
            }

            .navList > li {
                width: 100%;
                text-align: center;
                cursor: pointer;
            }

            .megaMenu {
                display: none;
                position: static;
                transform: none;
                box-shadow: none;
                width: 100%;
                margin-top: 1rem;
                padding: 1rem 0;
                background: rgba(245, 245, 245, 0.98);
            }

            .megaMenu.active {
                display: block;
                overflow: scroll;
            }

            .gridList {
                grid-template-columns: 1fr;
                overflow: scroll;
            }

            .iconGroup {
                width: 100%;
                justify-content: center;
                margin-top: 2rem;
            }

            .searchContainer {
                width: 100%;
                justify-content: center;
            }

            .searchInput.active {
                position: static;
                width: 100%;
                opacity: 1;
                margin: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="headerContent">
            <div class="logo">
                <a href="/">
                    <img src="../assets/images/logo.png" alt="Sarathi Cooperative Logo">
                </a>
            </div>
            
            <i class="fas fa-bars menuToggle" onclick="toggleMenu()"></i>
            
            <div class="nav">
                <ul class="navList">
                    <li>
                        Sarathi <span>At Glance</span>
                        <div class="megaMenu">
                            <ul class="aboutList">
                                <li><a href="/we-sarathians">We Sarathians</a></li>
                                <li><a href="/leadership">Leadership</a></li>
                                <li><a href="/become-a-sarathian">Become a Sarathian</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        Industries <span>We Cater</span>
                        <div class="megaMenu">
                            <ul class="gridList">
                                <?php
                                $industries = [
                                    'Aerospace & Defence', 'Agriculture', 'Automotive', 'Consumer Products',
                                    'Education', 'Energy', 'Financial Services', 'Health Care Industry',
                                    'Industrial Goods', 'Insurance Industry', 'Principal Investors & Private Equity', 'Public Sector',
                                    'Retail Industry', 'Technology Media and Telecommunications', 'Transportation and Logistics',
                                    'Travel and Tourism'
                                ];
                                foreach ($industries as $industry) {
                                    echo "<li>$industry</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </li>

                    <li>
                        Services <span>We Perform</span>
                        <div class="megaMenu">
                            <ul class="gridList">
                                <?php
                                $services = [
                                    'Advisory', 'Climate Change and Sustainability', 'Cost Management', 'Digital Technology Data',
                                    'Innovation Strategy', 'Marketing & Sales', 'Risk Management & Compliance', 'Social Impact',
                                    'Organizational Strategy', 'International Business', 'Manufacturing', 'Tax'
                                ];
                                foreach ($services as $service) {
                                    echo "<li>$service</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </li>

                    <li>
                        Memories <span>We Make</span>
                        <div class="megaMenu">
                            <ul class="aboutList">
                                <?php
                                $memories = [
                                    'Testimonials', 'Blogs', 'Library', 'Achievements'
                                ];
                                foreach ($memories as $item) {
                                    echo "<li><a href='" . strtolower(str_replace(' ', '-', $item)) . "'>$item</a></li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </li>
                </ul>
                <!-- Add the icons inside mobile nav
                <div class="mobileIcons">
                    <div class="searchContainer">
                        <input type="text" class="searchInput" placeholder="Search...">
                        <i class="fas fa-search icon" onclick="toggleSearch()"></i>
                    </div>
                    <i class="fas fa-user icon"></i>
                </div> -->
                </div>
                
                <!-- Move the iconGroup to show only on desktop -->
                <div class="iconGroup desktop-only">
                    <div class="searchContainer">
                        <input type="text" class="searchInput" placeholder="Search...">
                        <i class="fas fa-search icon" onclick="toggleSearch()"></i>
                    </div>
                    <i class="fas fa-user icon"></i>
                </div>
                
                <style>
                    .mobileIcons {
                        display: none;
                        margin-top: 2rem;
                        padding-top: 1rem;
                        border-top: 1px solid rgba(10, 43, 79, 0.1);
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 1rem;
                    }

                    @media (max-width: 768px) {
                        .desktop-only {
                            display: none;
                        }

                        .mobileIcons {
                            display: flex;
                        }

                        .searchInput.active {
                            position: relative;
                            right: 20%;
                            width: 200px;
                            max-width: 300px;
                            margin: 0 auto;
                            opacity: 1;
                        }

                        .searchContainer {
                            width: auto;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                    }
                </style>
            </nav>
        </div>
    </header>

    <script>
        function toggleSearch() {
            const searchInput = document.querySelector('.searchInput');
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) {
                searchInput.focus();
            }
        }

        function toggleMenu() {
            const nav = document.querySelector('.nav');
            nav.classList.toggle('active');
        }

        function toggleSearch() {
            const searchInput = document.querySelector('.searchInput');
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) {
                searchInput.focus();
            }
        }

        // Handle mobile menu item clicks
        document.querySelectorAll('.navList > li').forEach(item => {
            item.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    const megaMenu = this.querySelector('.megaMenu');
                    if (megaMenu) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Check if this mega menu is already active
                        const isActive = megaMenu.classList.contains('active');
                        
                        // Close all mega menus first
                        document.querySelectorAll('.megaMenu').forEach(menu => {
                            menu.classList.remove('active');
                        });
                        
                        // If the clicked menu wasn't active, open it
                        if (!isActive) {
                            megaMenu.classList.add('active');
                        }
                    }
                }
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.querySelector('.nav');
            const menuToggle = document.querySelector('.menuToggle');
            const megaMenus = document.querySelectorAll('.megaMenu');
            
            // If click is outside nav and menu toggle
            if (!nav.contains(event.target) && !menuToggle.contains(event.target)) {
                nav.classList.remove('active');
                megaMenus.forEach(menu => {
                    menu.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>