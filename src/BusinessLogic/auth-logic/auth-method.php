<?php

use App\Application\Settings\SettingInterface;
use Firebase\JWT\JWT;
use Slim\App;

//create user section
function getBodyCreateUser(string $data): array
{
    $body = json_decode($data, true);
    $emailOrUsername = $body['emailOrUsername'];
    $password = $body['password'];
    $employee_code = $body['employee_code'];
    $first_name = $body['first_name'];
    $last_name = $body['last_name'];
    $nickname = $body['nickname'];
    $hire_date = $body['hire_date'];
    $role = $body['role'];
    $department = $body['department'];
    $level = $body['level'];
    return [
        'emailOrUsername' => $emailOrUsername,
        'password' => $password,
        'employee_code' => $employee_code,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'nickname' => $nickname,
        'hire_date' => $hire_date,
        'role' => $role,
        'department' => $department,
        'level' => $level
    ];
}

/**
 * @throws Exception
 */
function addNewUser(object $db, array $body): void
{
    //check if email and employee_code already exists
    $sql = "SELECT * FROM employees WHERE emailOrUsername = :emailOrUsername OR employee_code = :employee_code";
    $stmt = $db->prepare($sql);
    $stmt->execute(['emailOrUsername' => $body['emailOrUsername'], 'employee_code' => $body['employee_code']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        throw new Exception('Email Username or employee code already exists');
    } else {
        //hash password
        $body['password'] = password_hash($body['password'], PASSWORD_DEFAULT);
        //insert new user
        $sql = "INSERT INTO employees (emailOrUsername, password, employee_code, first_name, last_name, nickname,hire_date, role, department, level) VALUES (:emailOrUsername, :password, :employee_code, :first_name, :last_name, :nickname,:hire_date, :role, :department, :level)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'emailOrUsername' => $body['emailOrUsername'],
            'password' => $body['password'],
            'employee_code' => $body['employee_code'],
            'first_name' => $body['first_name'],
            'last_name' => $body['last_name'],
            'nickname' => $body['nickname'],
            'hire_date' => $body['hire_date'],
            'role' => $body['role'],
            'department' => $body['department'],
            'level' => $body['level']
        ]);

        //get employee_id
        $sql = "SELECT id FROM employees WHERE emailOrUsername = :emailOrUsername";
        $stmt = $db->prepare($sql);
        $stmt->execute(['emailOrUsername' => $body['emailOrUsername']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $employee_id = $result['id'];
        //create row for image
        createRowForImage($db, $employee_id);
    }
}

function createRowForImage(object $db, int $employee_id): void
{
    $sql = "INSERT INTO employees_images (employee_id, image_name) VALUES (:employee_id, :image_name)";
    $stmt = $db->prepare($sql);
    $stmt->execute(['employee_id' => $employee_id, 'image_name' => 'default.png']);
}


//login section
function getBodyLogin(string $data): array
{
    $body = json_decode($data, true);
    $emailOrUsername = $body['emailOrUsername'];
    $password = $body['password'];
    return [
        'emailOrUsername' => $emailOrUsername,
        'password' => $password
    ];
}

/**
 * @throws Exception
 */
function verifyPassword(object $db, array $body, App $app): array
{
    $result = selectUser($body['emailOrUsername'], $db);
    if ($result) {
        if (password_verify($body['password'], $result['password'])) {
            $key = $app->getContainer()->get(SettingInterface::class)->getSettings('key_jwt');
            $result['token'] = generateToken($result, $key);
            return $result;
        } else {
            throw new Exception('Username or Password is incorrect');
        }
    } else {
        throw new Exception('User not found');
    }
}

;

function generateToken(array $body, string $key): string
{
    $payload = [
        'role' => $body['role'],
        'emailOrUsername' => $body['emailOrUsername'],
        'employee_id' => $body['id'],
    ];
    $token = JWT::encode($payload, $key, 'HS256');
    return $token;
}


//update password section
function getBodyUpdatePassword(string $data): array
{
    $body = json_decode($data, true);
    $emailOrUsername = $body['emailOrUsername'];
    $oldPassword = $body['oldPassword'];
    $newPassword = $body['newPassword'];
    return [
        'emailOrUsername' => $emailOrUsername,
        'oldPassword' => $oldPassword,
        'newPassword' => $newPassword
    ];
}

/**
 * @throws Exception
 */
function updatePassword(object $db, array $body): void
{
    $result = selectUser($body['emailOrUsername'], $db);
    if ($result) {
        if (password_verify($body['oldPassword'], $result['password'])) {
            $body['newPassword'] = password_hash($body['newPassword'], PASSWORD_DEFAULT);
            $sql = "UPDATE employees SET password = :newPassword WHERE emailOrUsername = :emailOrUsername";
            $stmt = $db->prepare($sql);
            $stmt->execute(['newPassword' => $body['newPassword'], 'emailOrUsername' => $body['emailOrUsername']]);
        } else {
            throw new Exception('Username or Password is incorrect');
        }
    } else {
        throw new Exception('User not found');
    }
}

//Utils
function selectUser(string $emailOUsername, object $db)
{
    $sql = "SELECT * FROM employees WHERE emailOrUsername = :emailOrUsername";
    $stmt = $db->prepare($sql);
    $stmt->execute(['emailOrUsername' => $emailOUsername]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}