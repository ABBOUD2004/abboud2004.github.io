<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    // التحقق من الحقول
    if (!empty($name) && !empty($email) && !empty($message)) {
        // يمكنك إرسال بريد أو حفظه في قاعدة بيانات - هذا مثال بسيط لحفظه في ملف:
        $data = "Name: $name\nEmail: $email\nMessage: $message\n---\n";
        file_put_contents("messages.txt", $data, FILE_APPEND);

        // إعادة التوجيه أو إظهار رسالة
        echo "<script>alert('Message sent successfully!'); window.location.href='index.html';</script>";
        exit();
    } else {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit();
    }
}
?>
