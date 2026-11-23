<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accountType = $_POST['accountType'];
    $fullName = $_POST['fullName'];
    $email = strtolower( $_POST['email']);
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // echo $accountType . $fullName . $email . $phone . $password;
    // check empty field
    // if ($fullName =='' || $email == '' || $password == '' || $mobile == '') {
    //     echo json_encode([
    //         "success" => false,
    //         "message" => "All fields are required"
    //     ]);
    //     exit;
    // }

    $errors = [];
    if (!in_array($accountType , ['landlord' , 'tenant'] , true)) {
        $errors [] = 'Please select a valid account type';
    }

    if (strlen($fullName) < 3) {
        $errors[] = 'Full name must conatin at least 3 words';
    }

    if (!filter_var($email , FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address';
    }

    if (strlen($mobile)>10) {
        $errors[] = 'Enter a valid phone number';
    }

    $passwordRegex =
    '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_\-+=])[A-Za-z\d@$!%*?&#^()_\-+=]{8,}$/';

    if (!preg_match($passwordRegex, $password)) {
        $errors[] =
        'Password must have at least 8 characters with uppercase, lowercase, number, and symbol';
    }

    if ($errors) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ',$errors)
        ]);
        exit;
    }

    require_once __DIR__ . '/../../config/db.php';

    try{
        $stmt = $conn->prepare('select user_id from users where email=? limit 1');
        $stmt->bind_param("s" , $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already exists try with another email'
            ]);
            exit;
        }
        $stmt->close();

        $role = $accountType === 'landlord' ? 'owner' : 'tenant';
        $passwordHash = password_hash($password , PASSWORD_DEFAULT);

        $stmt = $conn->prepare('insert into users (full_name , email , password_hash , phone , role)  values(?,?,?,?,?)');

        $stmt->bind_param("sssss" , $fullName , $email , $passwordHash , $phone , $role);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully'
        ]);

       
    } catch (mysqli_sql_exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Signup failed ',
            'error' => $e->getMessage()
        ]);
    }

}
?>