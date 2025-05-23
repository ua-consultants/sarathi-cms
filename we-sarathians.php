<?php
// Use the provided database connection
$host = 'localhost';  // Your database host 
$dbname = 'u828878874_sarathi_db';  // Your database name 
$username = 'u828878874_sarathi_new';  // Your database username 
$password = '#Sarathi@2025';  // Your database password 

try { 
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

    // Fetch active members using PDO
    $query = "SELECT mp.*, mp.first_name, mp.last_name, mp.email, mp.phone, mp.city, mp.state 
             FROM members mp 
             JOIN membership_applications ma ON mp.application_id = ma.id 
             WHERE mp.status = 'active'"; 
    $stmt = $pdo->query($query);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch filter options using PDO
    $expertise_query = "SELECT DISTINCT area_of_expertise FROM members WHERE status = 'active'";
    $states_query = "SELECT DISTINCT state FROM membership_applications ma 
                     JOIN members mp ON ma.id = mp.id 
                     WHERE mp.status = 'active'";

    $expertise_stmt = $pdo->query($expertise_query);
    $states_stmt = $pdo->query($states_query);
} catch (PDOException $e) { 
    die("Connection failed: " . $e->getMessage()); 
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We Sarathians - Sarathi Cooperative</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .weSarathians {
            padding: 0;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .heading {
            margin-top: 120px;
            padding-top: 40px;
            font-size: 3rem;
            color: #002147;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        .heading::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: #ffd700;
            margin: 1rem auto;
        }

        .content {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }

        .filterBox {
            width: 300px;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .searchInput,
        .filterSelect {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .membersGrid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding-bottom: 2rem;
        }

        .profileCard {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profileCard:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .profileImage {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 1rem;
            border: 3px solid #ffd700;
        }

        .profileImage img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profileCard h3 {
            margin: 0.5rem 0;
            color: #333;
            text-align: center;
        }

        .expertise {
            color: #666;
            margin-bottom: 1rem;
            text-align: center;
        }

        .connectButton {
            background: #0a2b4f;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .connectButton:hover {
            background: #ffd700;
            color: #0a2b4f;
        }

        .knowMore {
            width: 100%;
            text-align: center;
            padding: 0.5rem;
            background: #f5f5f5;
            margin-top: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .knowMore:hover {
            background: #eee;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modalContent {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .closeButton {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .content {
                flex-direction: column;
            }

            .filterBox {
                width: 100%;
            }

            .membersGrid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .heading {
                font-size: 2.5rem;
                margin-top: 100px;
            }

            .membersGrid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="weSarathians">
        <div class="container">
            <h1 class="heading">We Sarathians</h1>
            
            <div class="content">
                <div class="filterBox">
                    <input
                        type="text"
                        placeholder="Search Sarathians..."
                        class="searchInput"
                        id="searchInput"
                    />
                    
                    <select class="filterSelect" id="expertiseFilter">
                        <option value="">Area of Expertise</option>
                        <?php while ($row = $expertise_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['area_of_expertise']); ?>">
                                <?php echo htmlspecialchars($row['area_of_expertise']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <select class="filterSelect" id="stateFilter">
                        <option value="">State</option>
                        <?php while ($row = $states_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['state']); ?>">
                                <?php echo htmlspecialchars($row['state']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <select class="filterSelect" id="seniorityFilter">
                        <option value="">Seniority</option>
                        <option value="L1">L1</option>
                        <option value="L2">L2</option>
                        <option value="L3">L3</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>

                <div class="membersGrid" id="membersGrid">
                    <?php foreach ($members as $member): ?>
                        <div class="profileCard" data-expertise="<?php echo htmlspecialchars($member['area_of_expertise']); ?>" data-state="<?php echo htmlspecialchars($member['state']); ?>" data-seniority="<?php echo htmlspecialchars($member['seniority']); ?>">
                            <div class="profileImage">
                                <img src="/sarathi/uploads/members/<?php echo htmlspecialchars($member['profile_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>" />
                            </div>
                            
                            <h3><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h3>
                            <p class="expertise"><?php echo htmlspecialchars($member['area_of_expertise']); ?></p>
                            
                            <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="connectButton">
                                <i class="fas fa-envelope"></i> Connect
                            </a>
                            
                            <div class="knowMore" onclick="showModal(<?php echo htmlspecialchars(json_encode($member)); ?>)">
                                <i class="fas fa-info-circle"></i> Know More
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Template -->
    <div class="modal" id="memberModal">
        <div class="modalContent">
            <button class="closeButton" onclick="hideModal()">×</button>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // Filter functionality
        const searchInput = document.getElementById('searchInput');
        const expertiseFilter = document.getElementById('expertiseFilter');
        const stateFilter = document.getElementById('stateFilter');
        const seniorityFilter = document.getElementById('seniorityFilter');
        const membersGrid = document.getElementById('membersGrid');

        function filterMembers() {
            const searchTerm = searchInput.value.toLowerCase();
            const expertise = expertiseFilter.value;
            const state = stateFilter.value;
            const seniority = seniorityFilter.value;

            const cards = membersGrid.getElementsByClassName('profileCard');

            Array.from(cards).forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                const cardExpertise = card.dataset.expertise;
                const cardState = card.dataset.state;
                const cardSeniority = card.dataset.seniority;

                const matchesSearch = name.includes(searchTerm);
                const matchesExpertise = !expertise || cardExpertise === expertise;
                const matchesState = !state || cardState === state;
                const matchesSeniority = !seniority || cardSeniority === seniority;

                card.style.display = 
                    matchesSearch && matchesExpertise && matchesState && matchesSeniority 
                    ? 'flex' 
                    : 'none';
            });
        }

        searchInput.addEventListener('input', filterMembers);
        expertiseFilter.addEventListener('change', filterMembers);
        stateFilter.addEventListener('change', filterMembers);
        seniorityFilter.addEventListener('change', filterMembers);

        // Modal functionality
        function showModal(member) {
            const modal = document.getElementById('memberModal');
            const modalContent = document.getElementById('modalContent');

            modalContent.innerHTML = `
                <div class="modalHeader">
                    <img src="/sarathi/uploads/members/${member.profile_image}" 
                         alt="${member.first_name}" />
                    <div>
                        <h2>${member.first_name} ${member.last_name}</h2>
                        <p class="cursive">${member.area_of_expertise}</p>
                    </div>
                </div>

                <div class="modalBody">
                    <section>
                        <h3>Personal Information</h3>
                        <p>Location: ${member.city}, ${member.state}</p>
                        <p>Qualification: ${member.highest_qualification}</p>
                    </section>

                    <section>
                        <h3>Journey</h3>
                        <p>${member.journey || 'No journey information available.'}</p>
                    </section>

                    <a href="mailto:${member.email}" class="connectButton">
                        <i class="fas fa-envelope"></i> Connect with ${member.first_name}
                    </a>
                </div>
            `;

            modal.style.display = 'flex';
        }

        function hideModal() {
            document.getElementById('memberModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('memberModal');
            if (event.target === modal) {
                hideModal();
            }
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>