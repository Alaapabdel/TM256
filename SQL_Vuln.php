<?php
// --- DATABASE CONNECTION ---
$host = "localhost";
$user = "app_user";       // User from Task 2
$pass = "strong_password"; // Password from Task 2
$db   = "webapp_db";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$vuln_msg = "";
$sec_msg = "";

// 1. PROCESS VULNERABLE FORM (Bad Code)
if (isset($_POST['vuln_input'])) {
    $input = $_POST['vuln_input'];
    
    // VULNERABILITY: Direct string concatenation
    $sql = "SELECT * FROM users WHERE username = '$input'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $vuln_msg = "SUCCESS! You logged in as admin.";
    } else {
        $vuln_msg = "Login Failed.";
    }
}

// 2. PROCESS SECURE FORM (Good Code)
if (isset($_POST['sec_input'])) {
    $input = $_POST['sec_input'];

    // MITIGATION: Prepared Statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sec_msg = "SUCCESS! You logged in as admin.";
    } else {
        $sec_msg = "Login Failed (Attack Blocked).";
    }
}
?>

<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; padding: 20px;">

    <h1>Task 4: Mitigation Demo</h1>
    <p>Use the cheat code: <b>' OR '1'='1</b></p>

    <div style="border: 2px solid red; padding: 20px; background: #ffe6e6;">
        <h2 style="color: red;">1. Vulnerable Login</h2>
        <form method="post">
            Username: <input type="text" name="vuln_input">
            <button type="submit">Login</button>
        </form>
        <h3>Result: <?php echo $vuln_msg; ?></h3>
    </div>

    <br><br>

    <div style="border: 2px solid green; padding: 20px; background: #e6ffe6;">
        <h2 style="color: green;">2. Secure Login (Fixed)</h2>
        <form method="post">
            Username: <input type="text" name="sec_input">
            <button type="submit">Login</button>
        </form>
        <h3>Result: <?php echo $sec_msg; ?></h3>
    </div>

</body>
</html>
