<?php
// Define database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cms_db';

// Establish a connection to the MySQL database
$con = new mysqli($host, $user, $password, $database);

// Check the database connection
if ($con->connect_error) {
    die('Database connection failed: ' . $con->connect_error);
}

// Initialize the response variables
$message = '';
$contacts = [];

// Define the uploads directory path
$uploadsDir = 'uploads/';

// Check if the uploads directory exists, and create it if it does not
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Retrieve form data
    $contactName = $_POST['contactName'] ?? '';
    $contactNumber = $_POST['contactNumber'] ?? '';
    $email = $_POST['email'] ?? '';
    $photo = $_FILES['photo'] ?? null;

    // Perform the appropriate action based on the form submission
    switch ($action) {
        case 'insert':
            // Check if contact already exists
            $stmt = $con->prepare("SELECT * FROM contacts WHERE name = ?");
            $stmt->bind_param("s", $contactName);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = 'Contact already exists with this name.';
            } else {
                // Save the photo if provided
                $photoFileName = '';
                if ($photo && $photo['tmp_name'] && is_uploaded_file($photo['tmp_name'])) {
                    $photoFileName = basename($photo['name']);
                    $destination = $uploadsDir . $photoFileName;
                    // Move the uploaded file to the uploads directory
                    if (move_uploaded_file($photo['tmp_name'], $destination)) {
                        $message = 'Contact added successfully.';
                    } else {
                        $message = 'Failed to upload photo.';
                    }
                }

                // Insert the new contact into the database
                $stmt = $con->prepare("INSERT INTO contacts (name, number, email, photo) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $contactName, $contactNumber, $email, $photoFileName);
                $result = $stmt->execute();

                if ($result) {
                    $message = 'Contact added successfully.';
                } else {
                    $message = 'Failed to add contact.';
                }
                $stmt->close();
            }
            break;

        case 'update':
            // Search for the contact to update
            $stmt = $con->prepare("SELECT * FROM contacts WHERE name = ?");
            $stmt->bind_param("s", $contactName);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Retrieve the existing photo file name
                $existingContact = $result->fetch_assoc();
                $photoFileName = $existingContact['photo'];

                // Update the photo if provided
                if ($photo && $photo['tmp_name'] && is_uploaded_file($photo['tmp_name'])) {
                    $newPhotoFileName = basename($photo['name']);
                    $newDestination = $uploadsDir . $newPhotoFileName;

                    // Move the uploaded file to the uploads directory
                    if (move_uploaded_file($photo['tmp_name'], $newDestination)) {
                        $photoFileName = $newPhotoFileName;
                    } else {
                        $message = 'Failed to upload photo.';
                    }
                }

                // Update contact information
                $stmt = $con->prepare("UPDATE contacts SET number = ?, email = ?, photo = ? WHERE name = ?");
                $stmt->bind_param("ssss", $contactNumber, $email, $photoFileName, $contactName);
                $result = $stmt->execute();

                if ($result) {
                    $message = 'Contact updated successfully.';
                } else {
                    $message = 'Failed to update contact.';
                }
                $stmt->close();
            } else {
                $message = 'Contact not found.';
            }
            break;

        case 'delete':
            // Search for the contact to delete
            $stmt = $con->prepare("DELETE FROM contacts WHERE name = ?");
            $stmt->bind_param("s", $contactName);
            $result = $stmt->execute();

            if ($result) {
                $message = 'Contact deleted successfully.';
            } else {
                $message = 'Failed to delete contact.';
            }
            $stmt->close();
            break;

        case 'search':
            // Search for the contact
            $stmt = $con->prepare("SELECT * FROM contacts WHERE name = ?");
            $stmt->bind_param("s", $contactName);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = 'Contact found.';
                $contacts = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                $message = 'Contact not found.';
            }
            $stmt->close();
            break;

        case 'show_all':
            // Retrieve all contacts from the database
            $stmt = $con->prepare("SELECT * FROM contacts");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                $message = 'All contacts retrieved.';
                $contacts = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                $message = 'Failed to retrieve contacts.';
            }
            $stmt->close();
            break;

        default:
            $message = 'Invalid action.';
            break;
    }
}

// Close the database connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Management System</title>
    <style>
        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        /* Container styles */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Form header */
        h1 {
            text-align: center;
            color: #007bff;
        }

        /* Form group styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
        }

        .button-group button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button-group button:hover {
            background-color: #0056b3;
        }

        #result {
            margin-top: 20px;
        }

        #result div {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>

    <!-- JavaScript to handle the required attribute -->
    <script>
        function handleActionChange(action) {
            const contactNameInput = document.getElementById('contactName');
            const contactNumberInput = document.getElementById('contactNumber');
            const emailInput = document.getElementById('email');
            const photoInput = document.getElementById('photo');

            // Remove required attributes for 'search', 'show_all', 'delete', and 'update' actions
            if (action === 'search' || action === 'show_all' || action === 'delete' || action === 'update') {
                contactNameInput.required = false;
                contactNumberInput.required = false;
                emailInput.required = false;
                photoInput.required = false;
            } else {
                // Restore required attributes for 'insert' action
                contactNameInput.required = true;
                contactNumberInput.required = true;
                emailInput.required = true;
                photoInput.required = true;
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Contact Management System</h1>

        <!-- Form for contact management -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="contactName">Contact Name:</label>
                <input type="text" id="contactName" name="contactName" required>
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact Number:</label>
                <input type="text" id="contactNumber" name="contactNumber">
            </div>
            <div class="form-group">
                <label for="email">Email ID:</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo">
            </div>

            <div class="button-group">
                <button type="submit" name="action" value="insert" onclick="handleActionChange('insert')">Insert</button>
                <button type="submit" name="action" value="update" onclick="handleActionChange('update')">Update</button>
                <button type="submit" name="action" value="delete" onclick="handleActionChange('delete')">Delete</button>
                <button type="submit" name="action" value="search" onclick="handleActionChange('search')">Search</button>
                <button type="submit" name="action" value="show_all" onclick="handleActionChange('show_all')">Show All</button>
                <button type="reset">Reset</button>
            </div>
        </form>

        <div id="result">
            <?php if ($message): ?>
                <div><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if (!empty($contacts)): ?>
                <?php foreach ($contacts as $contact): ?>
                    <div>
                        <strong>Name:</strong> <?php echo htmlspecialchars($contact['name']); ?><br>
                        <strong>Number:</strong> <?php echo htmlspecialchars($contact['number']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($contact['email']); ?><br>
                        <?php if ($contact['photo']): ?>
                            <strong>Photo:</strong> <img src="uploads/<?php echo htmlspecialchars($contact['photo']); ?>" alt="Contact Photo" width="100" height="100">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

