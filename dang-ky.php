<?php
session_start(); // Bắt đầu phiên làm việc

// Kết nối tới CSDL
$servername = "localhost";
$username = "root";
$password = "";
$database = "qldoanmypham";

$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$message = ""; // Khởi tạo biến thông điệp

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $hoten = $_POST['hoten'];
    $email = $_POST['email']; // Lấy email
    $tendangnhap = $_POST['tendangnhap'];
    $matkhau = $_POST['matkhau'];
    $sdt = $_POST['sdt'];

    // Kiểm tra xem email hoặc tên người dùng đã tồn tại chưa
    $stmt = $conn->prepare("SELECT * FROM nguoidung WHERE tendangnhap = ? OR email = ?");
    $stmt->bind_param("ss", $tendangnhap, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Nếu tên người dùng hoặc email đã tồn tại
        $message = "Tên người dùng hoặc email đã tồn tại!";
    } else {
        // Thêm người dùng mới vào cơ sở dữ liệu
        $stmt = $conn->prepare("INSERT INTO nguoidung (hoten, tendangnhap, matkhau, sdt, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $hoten, $tendangnhap, $matkhau, $sdt, $email);

        if ($stmt->execute()) {
            // Đăng ký thành công
            $_SESSION['tendangnhap'] = $tendangnhap;
            header("Location: home.php"); // Chuyển hướng đến trang chủ sau khi đăng ký thành công
            exit;
        } else {
            $message = "Đăng ký thất bại!";
        }
    }

    $stmt->close();
}

$conn->close();
?>