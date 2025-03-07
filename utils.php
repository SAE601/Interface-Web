<?php
    function getPageName() {
        $url = $_SERVER['REQUEST_URI'];

        if (preg_match('/\/([a-zA-Z0-9_-]+)\.php/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    function checkAndRedirect() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_url'] = getPageName();
            header('Location: index.php');
            exit;
        }
    }
?>