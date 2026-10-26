<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Organizer/login.html');
    exit();
}
$conn = mysqli_connect('localhost', 'root', '', 'event_zone');
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT r.roleName FROM users u JOIN user_role ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.roleID WHERE u.id = $user_id LIMIT 1");
if (!$result) {
    die('Query error: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($result);
if (!$row || $row['roleName'] !== 'Admin') {
    die('Access denied. admins only.');
}

$event_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM events"))[0];
$user_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$event_goers_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM event_goers"))[0];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EventZone</title>
    <style>
        
        body { 
            font-family: Poppins, sans-serif;
            background: #f4f4f4; 
            margin: 0; 
        }




        .header { 
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px 40px;
            background: #fff; 
            color: #333; 
            border-bottom: 1px solid #e0e0e0; 
        }
        .header-logo {
            height: auto;
            width: 150px;
            vertical-align: middle;
            position: absolute;
            left: 40px;
        }
        .header-title {
            font-size: 2em;
            color: #ca8f82;
            letter-spacing: 2px;
        }
        .header-logout {
            position: absolute;
            right: 40px;
            background: #fff;
            color: #ca8f82;
            padding: 8px 18px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #ca8f82;
            transition: background 0.2s, color 0.2s;
        }
        .header-logout:hover {
            background: #ca8f82;
            color: #fff;
        }
        
        .container { 
            max-width: 900px; 
            margin: 40px auto; 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px #0001; 
            padding: 36px; 
        }
        
        .stats { 
            display: flex; 
            justify-content: space-around; 
            margin-bottom: 36px; 
        }
        .stat { 
            background: #f7f7f7; 
            border-radius: 10px; 
            padding: 28px 48px; 
            text-align: center; 
            min-width: 160px; 
            box-shadow: 0 1px 4px #00000008; 
        }
        .stat h2 { 
            margin: 0 0 10px 0; 
            color: #555; 
            font-size: 2.5em; 
            font-weight: 600; 
        }
        .stat div { 
            color: #888; 
            font-size: 1.1em; 
        }
        .stat:hover h2 { 
            color: #ca8f82; 
        }
        
        

        .nav { 
            display: flex; 
            justify-content: center; 
            gap: 36px; 
            margin-top: 24px; 
        }
        
        .nav a { 
            text-decoration: none; 
            color: #fff; 
            background: #ca8f82; 
            padding: 14px 32px; 
            border-radius: 7px; 
            font-weight: bold; 
            font-size: 1.1em; 
            transition: background 0.2s, color 0.2s; 
            border: none; 
        }
        .nav a:hover { 
            background:  #A47065FF; 
        }

        @media (max-width: 700px) 
        {
            .stats { 
                flex-direction: column; 
                gap: 18px; 
            }
            .container { 
                padding: 18px; 
            }
            .header {
                padding: 12px 10px;
            }
            .header-logo {
                left: 10px;
                height: 36px;
            }
            .header-title {
                font-size: 1.2em;
            }
            .header-logout {
                right: 10px;
                padding: 6px 12px;
                font-size: 0.95em;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="../projectImages/eventZoneLogo.png" alt="Event Zone Logo" class="header-logo">
        <span class="header-title">Dashboard</span>
        <a href="../Organizer/logout.php" class="header-logout">Logout</a>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const menuToggle = document.getElementById('menu-toggle');
            const navMenu = document.getElementById('nav-menu-mobile');
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    this.querySelector('i').classList.toggle('fa-bars');
                    this.querySelector('i').classList.toggle('fa-times');
                });
            }
        });
    </script>
    <div class="container">
        <div class="stats">
            <div class="stat">
                <h2><?php echo $event_count; ?></h2>
                <div>Events</div>
            </div>
            <div class="stat">
                <h2><?php echo $user_count; ?></h2>
                <div>Users</div>
            </div>
            <div class="stat">
                <h2><?php echo $event_goers_count; ?></h2>
                <div>Registrations</div>
            </div>
            
           
        </div>
        <div class="nav">
            <a href="events.php">Manage Events</a>
            <a href="users.php">Manage Users</a>
            <a href="registrations.php">Manage Registrations</a>
        </div>
    </div>
</body>
</html> 