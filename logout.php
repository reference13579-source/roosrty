<?php
session_start();
session_destroy();

// Очищаем localStorage через JavaScript
echo "<script>
    localStorage.removeItem('yandexUser');
    localStorage.removeItem('googleUser');
    window.location.href = '/';
</script>";
?>