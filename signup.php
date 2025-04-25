<?php
// Include the database connection file
include 'database.php';
//sari values get krlo post array se
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $full_name = trim($_POST['fullName']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['repassword'];
  $dob = $_POST['dateOfBirth'];
  $permanent_address_line1 = trim($_POST['permanentAddressLine1']);
  $permanent_address_line2 = trim($_POST['permanentAddressLine2']);
  $permanent_city = trim($_POST['permanentCity']);
  $permanent_state = trim($_POST['permanentState']);
  $current_address_line1 = trim($_POST['currentAddressLine1']);
  $current_address_line2 = trim($_POST['currentAddressLine2']);
  $current_city = trim($_POST['currentCity']);
  $current_state = trim($_POST['currentState']);
  $profile_picture = null;

  if (empty($full_name) || empty($email) || empty($password) || empty($dob)) {
    echo "<script>alert('All fields are required!');</script>";
    exit();
  }

  if ($password !== $confirm_password) {
    echo "<script>alert('Passwords do not match!');</script>";
    exit();
  }

  // check karo ki email phle se exist to nhi krta
  $sql = "SELECT email FROM Users WHERE email = ?";
  $result = $conn->prepare($sql);
  $result->execute([$email]);

  if ($result->rowCount() > 0) {
    echo "<script>alert('Email already exists. Please use a different email.');</script>";
    exit();
  }

  // Profile Picture ko upload 
  if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === 0) {
    $target_folder = "uploads/"; // Folder jisme files store hongi
    $target_file = $target_folder . basename($_FILES['profilePicture']['name']); // Full file path

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $target_file)) {
      $profile_picture = $target_file; // Update the profile picture with the uploaded file path
    } else {
      echo "<script>alert('Error uploading profile picture.');</script>";
      exit();
    }
  }

  // password ko security ke liye hash kro
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Insert user data into the Users table
  try {

    $sql = "INSERT INTO Users (
        full_name, email, password, dob, profile_picture,
        permanent_address_line1, permanent_address_line2, permanent_city, permanent_state,
        current_address_line1, current_address_line2, current_city, current_state
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $result = $conn->prepare($sql);

    $result->execute([
      $full_name,
      $email,
      $hashed_password,
      $dob,
      $profile_picture,
      $permanent_address_line1,
      $permanent_address_line2,
      $permanent_city,
      $permanent_state,
      $current_address_line1,
      $current_address_line2,
      $current_city,
      $current_state
    ]);


    // Get the last inserted user ID
    $user_id = $conn->lastInsertId();//primary key

    // Inserting qualification into DB
    if (!empty($_POST['qualifications'])) {
      $sql = "INSERT INTO Qualifications (user_id, qualification) VALUES (?, ?)";
      //we can use ? placeholders and pass values as an array in execute().
      $result = $conn->prepare($sql);
      foreach ($_POST['qualifications'] as $qualification) {
        $result->execute([$user_id, $qualification]);
      }
    }

    // Inserting experiences into DB
    if (!empty($_POST['experiences'])) {
      $sql = "INSERT INTO Experiences (user_id, experience) VALUES (:user_id, :experience)";
      //Instead of bindParam(), we can directly pass an associative array to execute().
      $result = $conn->prepare($sql);
      foreach ($_POST['experiences'] as $experience) {
        $result->execute([
          ':user_id' => $user_id,
          ':experience' => $experience
        ]);
      }
    }
    //3rd method is bindParam() or bindValue()----bindValue() binds values directly, whereas bindParam() binds variables by reference.

    echo "<script>alert('User registered successfully');</script>";
    // header("Location: login.php"); // Redirect to login page
    exit();
  } catch (PDOException $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up - Employee Management System</title>
  <link rel="stylesheet" href="signup.css" />
</head>

<body>
  <div class="heading">
    <h2>Employee Registration Form</h2>
  </div>

  <div class="signup-container">
    <form action="signup.php" id="signupForm" method="post" enctype="multipart/form-data">
      <!-- Full Name and Date of Birth (Side by Side) -->
      <div class="form-side">
        <div class="form-row">
          <div class="form-group">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullName" placeholder="John Doe" required />
          </div>
          <div class="form-group">
            <label for="dateOfBirth">Date of Birth</label>
            <input type="date" id="dateOfBirth" name="dateOfBirth" placeholder="dd/mm/yyyy" />
          </div>
        </div>

        <!-- Profile Picture -->
        <div class="form-below">

          <img src="./uploads/userprofile.jpg" alt="Profile Picture" id="profile-img" width="100" height="100" />

          <input type="file" id="profilePicture" name="profilePicture" accept="image/*" hidden />
          <label for="profilePicture" id="upload-btn">Upload Profile Pic</label>
        </div>
      </div>

      <!-- Email and Password (Side by Side) -->
      <div class="form-row">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="john@doe.com" required />
        </div>

        <div class="form-half">
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
            <div id="text">Use A-Z, a-z, 0-9, !@#$%^&* in password</div>
          </div>
          <div class="form-group">
            <label for="repassword">Re-Password</label>
            <input type="password" id="repassword" name="repassword" required />
          </div>
        </div>
      </div>

      <!-- Qualifications -->
      <div class="form-row">
        <div class="form-group">
          <label for="qualifications">Add your Qualifications</label>
          <div id="qualifications">

            <div class="qualification-item">
              <div id="text" class="qualification-text">Qualification 1</div>
              <div id="qualifications">
                <input type="text" name="qualifications[]" required />
              </div>
            </div>

          </div>

          <button type="button" id="addQualification" class="add-button">
            Add Qualification
          </button>
        </div>
      </div>

      <!-- Experiences -->
      <div class="form-row">
        <div class="form-group">
          <label for="experiences">Add your Experiences</label>
          <divdiv id="experiences">

            <div class="experience-item">
              <div id="text">Experiences 1</div>
              <div id="experiences">
                <input type="text" name="experiences[]" required />
              </div>
            </div>

        </div>

        <button type="button" id="addExperience" class="add-button">
          Add Experience
        </button>
      </div>


      <!-- Permanent Address -->
      <div class="form-row">
        <div class="form-group">
          <label for="permanentAddress">Permanent Address</label>

          <input type="text" id="permanentAddressLine1" name="permanentAddressLine1" placeholder="Line 1" required />
          <input type="text" id="permanentAddressLine2" name="permanentAddressLine2" placeholder="Line 2" required />
          <div class="form-half">
            <div class="form-group">
              <label for="city">City</label>
              <input type="text" id="permanentCity" name="permanentCity" placeholder="City" required />
            </div>

            <div class="form-group">
              <label for="state" class="state">State</label>
              <select id="permanentState" name="permanentState" required>
                <option value="HR">Haryana</option>
                <option value="HP">Himachal Pradesh</option>
                <option value="PB">Punjab</option>
                <option value="RJ">Rajasthan</option>
                <option value="UT">Uttarakhand</option>
                <option value="UP">Uttar Pradesh</option>
                <option value="CH">Chandigarh</option>
                <option value="DL">Delhi</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Current Address -->
      <div class="form-row">
        <div class="form-group">
          <label for="currentAddress">Current Address</label>

          <input type="text" id="currentAddressLine1" name="currentAddressLine1" placeholder="Line 1" />
          <input type="text" id="currentAddressLine2" name="currentAddressLine2" placeholder="Line 2" />
          <div class="form-half">
            <div class="form-group">
              <label for="city">City</label>
              <input type="text" id="currentCity" name="currentCity" placeholder="City" required />
            </div>

            <div class="form-group">
              <label for="state" class="state">State</label>
              <select id="currentState" name="currentState" required>
                <option value="HR">Haryana</option>
                <option value="HP">Himachal Pradesh</option>
                <option value="PB">Punjab</option>
                <option value="RJ">Rajasthan</option>
                <option value="UT">Uttarakhand</option>
                <option value="UP">Uttar Pradesh</option>
                <option value="CH">Chandigarh</option>
                <option value="DL">Delhi</option>
              </select>
            </div>
          </div>
        </div>
      </div>


      <!-- Submit Button -->
      <button type="submit" class="submit-button" name="submit">Sign Up</button>
    </form>
  </div>
  <script src="./jquery.js"></script>
  <script src="signup.js"></script>


</body>

</html>