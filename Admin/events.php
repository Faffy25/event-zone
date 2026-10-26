<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Organizer/login.php');
    exit();
}
$conn = mysqli_connect('localhost', 'root', '', 'event_zone');
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT r.roleName FROM users u JOIN user_role ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.roleID WHERE u.id = $user_id LIMIT 1");
$row = mysqli_fetch_assoc($result);
if (!$row || $row['roleName'] !== 'Admin') {
    die('Access denied. admins only.');
}
$events = mysqli_query($conn, "SELECT event_id, event_name, start_datetime, end_datetime, event_location, status FROM events ORDER BY start_datetime DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events | Admin</title>
    <style>
        body { 
            font-family: Poppins, sans-serif; 
            background: #f4f4f4; 
            margin: 0; 
        }
        .header { 
            background: #fff; 
            color: #333;
            padding: 24px 0; 
            text-align: center; 
            border-bottom: 1px solid #e0e0e0; 
        }
        .container { 
            max-width: 1000px; 
            margin: 40px auto; 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px #0001; 
            padding: 36px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            background: #fff; 
        }
        th, td { 
            padding: 14px 10px; 
            border-bottom: 1px solid #eee; 
            text-align: left; 
        }
        th { 
            background: #f7f7f7; 
            color: #555; 
            font-weight: 600; 
        }
        tr:hover { 
            background: #f4f4f4; 
        }
        .actions a { 
            margin-right: 10px; 
            text-decoration: none; 
            color: #fff; 
            background: #ca8f82; 
            padding: 8px 20px; 
            border-radius: 5px; 
            font-size: 1em; 
            border: none; 
            transition: background 0.2s, color 0.2s; 
        }
        .actions a.delete { 
            background: #c0392b; 
        }
        .actions a:hover { 
            background: #81251BFF;  
        }
        .nav { 
            margin-bottom: 20px; 
        }
        .nav a { 
            text-decoration: none; 
            color: #ca8f82; 
            margin-right: 20px; 
            font-weight: bold;
        }
        .nav a:hover { 
            color: #A47065FF;
            font-weight: normal;
             
        }
        @media (max-width: 700px) {
            .container { padding: 18px; }
            th, td { padding: 10px 4px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Events</h1>
    </div>
    <div class="container">
        <div class="nav">
            <a href="index.php"> Back to Dashboard</a>
        </div>
        <table>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Start</th> 
                <th>End</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($events)): ?>
            <tr>
                <td><?php echo $row['event_name']; ?></td>
                <td><?php echo $row['event_location']; ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($row['start_datetime'])); ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($row['end_datetime'])); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td class="actions">
                    <a href="editEvent.php?id=<?php echo $row['event_id']; ?>" class="edit">Edit</a>
                    <a href="deleteEvent.php?id=<?php echo $row['event_id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html> 