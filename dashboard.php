<?php
session_start();
require 'database.php';

// Dekho ki user logged in hai ya nhi
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

try {
  $user_id = $_SESSION['user_id'];
  // Fetch user data
  $stmt = $conn->prepare("SELECT * FROM Users WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC); //associative array dega user me
  // echo "<pre>";
  // print_r($user);
  // echo "</pre>";

  if (!$user) {
    die("User not found.");
  }

  // qualifications ek array me nikal lo
  $stmt = $conn->prepare("SELECT qualification FROM Qualifications WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $qualifications = $stmt->fetchAll(PDO::FETCH_COLUMN);
  // echo "<pre>";
  // print_r($qualifications);
  // echo "</pre>";

  // experiences ek array me nikal lo
  $stmt = $conn->prepare("SELECT experience FROM Experiences WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $experiences = $stmt->fetchAll(PDO::FETCH_COLUMN); //ek column ka data indexed array me return karta hai

  // echo"<pre>";
  // print_r($experiences);
  // echo"</pre>";

} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Profile</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="./fontawesome/css/all.min.css" />
</head>

<body>
  <div class="profile-container">
    <!-- Profile Form -->
    <form id="profileForm" enctype="multipart/form-data">
      <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>" />

      <!-- Photo container -->
      <div class="form-below">
        <div id="text">
          <h2>Employee Profile</h2>
        </div>

        <!-- Profile Header -->
        <div class="profile-header">
          <!-- Profile Picture -->
          <div class="form-below ">
            <img id="profile-picture" src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture" />
            <input type="file" name="profile_picture" id="profile-photo-upload" accept="image/*" style="display: none"
              readonly />
            <i class="fa-solid fa-pen-to-square edit-icon" id="imageclick"></i>
          </div>

          <!-- Full Name -->
          <div id="center">
            <input type="text" class="inputx editable-field" name="full_name" id="heading"
              value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly required />
            <i class="fa-solid fa-pen-to-square edit-icon""></i>
          </div>

          <!-- Email (Non-Editable) -->
          <p><?php echo $user['email']; ?></p>

          <!-- DOB -->
          <div id="center">

              <label for="date">DOB:</label>
              <input type="date" class="inputx editable-field" name="dob" id="dob"
                value="<?php echo htmlspecialchars($user['dob']); ?>" readonly required />
              <i class="fa-solid fa-pen-to-square edit-icon""></i>
          </div>
        </div>
      </div>

      <!-- Qualifications & Experiences -->
      <div class=" form-side">
                <!-- Qualifications -->
                <div class="profile-section">
                  <h2>Qualifications</h2>
                  <ul id="qualifications-list">
                    <?php foreach ($qualifications as $index => $qualification): ?>
                      <li>
                        <input type="text" class="inputx editable-field" name="qualifications[]"
                          value="<?php echo htmlspecialchars($qualification); ?>" readonly />
                        <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <button class="add-button qualification">Add Qualification</button>
                </div>

                <!-- Experiences -->
                <div class="profile-section">
                  <h2>Experiences</h2>
                  <ul id="experiences-list">
                    <?php foreach ($experiences as $index => $experience): ?>
                      <li>
                        <input type="text" class="inputx editable-field" name="experiences[]"
                          value="<?php echo htmlspecialchars($experience); ?>" readonly />
                        <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <button class="add-button experience">Add Experience</button>
                </div>
          </div>

          <!-- Addresses -->
          <div class="form-side">
            <div class="profile-section">
              <h3>Current Address</h3>
              <ul>
                <li>
                  <input type="text" class="inputx editable-field" name="current_address_line1"
                    value="<?php echo htmlspecialchars($user['current_address_line1']); ?>" readonly />
                  <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                </li>
                <li>
                  <input type="text" class="inputx editable-field" name="current_address_line2"
                    value="<?php echo htmlspecialchars($user['current_address_line2']); ?>" readonly />
                  <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                </li>
                <li>
                  <input type="text" class="inputx editable-field" name="current_city"
                    value="<?php echo htmlspecialchars($user['current_city']); ?>" readonly />
                  <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                </li>
                <li>
                  <input type="text" class="inputx editable-field" name="current_state"
                    value="<?php echo htmlspecialchars($user['current_state']); ?>" readonly />
                  <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                </li>
              </ul>
            </div>

            <div class="profile-section">
              <h3>Permanent Address</h3>
              <ul>
                <li>
                  <input type="text" class="inputx editable-field" name="permanent_address_line1"
                    value="<?php echo htmlspecialchars($user['permanent_address_line1']); ?>" readonly />
                  <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                </li>
                <li>
                  <input type="text" class="inputx editable-field" name="permanent_address_line2"
                    value="<?php echo htmlspecialchars($user['permanent_address_line2']); ?>" readonly />
                  <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                </li>
                <li>
                  <input type="text" class="inputx editable-field" name="permanent_city"
                    value="<?php echo htmlspecialchars($user['permanent_city']); ?>" readonly />
                  <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                </li>
                <li>
                  <input type="text" class="inputx editable-field" name="permanent_state"
                    value="<?php echo htmlspecialchars($user['permanent_state']); ?>" readonly />
                  <i class="fa-solid fa-pen-to-square edit-icon" style="display: none;"></i>
                </li>
              </ul>
            </div>
          </div>
    </form>
  </div>
  <script src="./jquery.js"></script>
  <script src="dashboard.js"></script>
</body>

</html>