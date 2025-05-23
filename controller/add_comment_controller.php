<?php
require_once "../config.php";
try {
    if (
        !isTokenValid($_POST['token']) ||
        !$_POST['user_comment'] || !$_POST['art_id'] ||
        $_POST['user_comment'] != $_SESSION['id']
    ) {
        session_destroy();
        header("Location: ../view/login.php?message_code=connect_error&status=error");
        exit();
    }
    if ($_SESSION['blocked']) {
        throw new Exception("unauthorized");
    }
    if (empty($_POST['comment_content'])) {
        throw new Exception("form_error");
    }
    // Nettoyage des données
    $today = date('Y-m-d');
    $content = ucfirst(trim($_POST['comment_content']));
    $_SESSION["temp_com"] = $content;
    $article = filter_var($_POST['art_id'], FILTER_VALIDATE_INT);
    $author = filter_var($_POST['user_comment'], FILTER_VALIDATE_INT);
    $sql = "INSERT INTO comment (com_posted_at, com_content, com_fk_art_id, com_fk_user_id) 
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $verif = $stmt->execute([$today, $content, $article, $author]);
    if (!$verif) {
        throw new Exception("server_error");
    }
    unset($_SESSION["temp_com"]);
    header("Location: ../view/read_article.php?id=" . $article . "&message_code=comment_added&status=success");
    exit();
} catch (Exception $e) {
    $error_code = urlencode($e->getMessage());
    header("Location: ../view/read_article.php?id=" . $article . "&message_code=" . $error_code . "&status=error");
    exit();
}
