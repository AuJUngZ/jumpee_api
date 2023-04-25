<?php
//create user section
function getBodyCreateUser(string $data): array
{
    $body = json_decode($data, true);
    $email = $body['email'];
    $password = $body['password'];
    $employee_code = $body['employee_code'];
    $first_name = $body['first_name'];
    $last_name = $body['last_name'];
    $nickname = $body['nickname'];
    $role = $body['role'];
    $department = $body['department'];
    return [
        'email' => $email,
        'password' => $password,
        'employee_code' => $employee_code,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'nickname' => $nickname,
        'role' => $role,
        'department' => $department
    ];
}
/**
 * @throws Exception
 */
function addNewUser(object $db, array $body): void
{
    //check if email and employee_code already exists
    $sql = "SELECT * FROM employees WHERE email = :email OR employee_code = :employee_code";
    $stmt = $db->prepare($sql);
    $stmt->execute(['email' => $body['email'], 'employee_code' => $body['employee_code']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result){
        throw new Exception('Email or employee code already exists');
    }else{
        //hash password
        $body['password'] = password_hash($body['password'], PASSWORD_DEFAULT);
        //insert new user
        $sql = "INSERT INTO employees (email, password, employee_code, first_name, last_name, nickname,hire_date, role, department) VALUES (:email, :password, :employee_code, :first_name, :last_name, :nickname,:hire_date, :role, :department)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'email' => $body['email'],
            'password' => $body['password'],
            'employee_code' => $body['employee_code'],
            'first_name' => $body['first_name'],
            'last_name' => $body['last_name'],
            'nickname' => $body['nickname'],
            'hire_date' => date('y-m-d'),
            'role' => $body['role'],
            'department' => $body['department']
        ]);
    }
}


//login section