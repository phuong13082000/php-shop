<?php

@include 'include/config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
};

if (isset($_POST['add_to_wishlist'])) {
    $product_id = $_POST['pid'];
    $product_id = filter_var($product_id, FILTER_SANITIZE_STRING);
    $product_name = $_POST['p_name'];
    $product_name = filter_var($product_name, FILTER_SANITIZE_STRING);
    $product_price = $_POST['p_price'];
    $product_price = filter_var($product_price, FILTER_SANITIZE_STRING);
    $product_image = $_POST['p_image'];
    $product_image = filter_var($product_image, FILTER_SANITIZE_STRING);

    $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
    $check_wishlist_numbers->execute([$product_name, $user_id]);

    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->execute([$product_name, $user_id]);

    if ($check_wishlist_numbers->rowCount() > 0) {
        $message[] = 'already added to wishlist!';
    } elseif ($check_cart_numbers->rowCount() > 0) {
        $message[] = 'already added to cart!';
    } else {
        $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
        $insert_wishlist->execute([$user_id, $product_id, $product_name, $product_price, $product_image]);
        $message[] = 'added to wishlist!';
    }

}

if (isset($_POST['add_to_cart'])) {

    $product_id = $_POST['pid'];
    $product_id = filter_var($product_id, FILTER_SANITIZE_STRING);
    $product_name = $_POST['p_name'];
    $product_name = filter_var($product_name, FILTER_SANITIZE_STRING);
    $product_price = $_POST['p_price'];
    $product_price = filter_var($product_price, FILTER_SANITIZE_STRING);
    $product_image = $_POST['p_image'];
    $product_image = filter_var($product_image, FILTER_SANITIZE_STRING);
    $product_qty = $_POST['p_qty'];
    $product_qty = filter_var($product_qty, FILTER_SANITIZE_STRING);

    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->execute([$product_name, $user_id]);

    if ($check_cart_numbers->rowCount() > 0) {
        $message[] = 'already added to cart!';
    } else {

        $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
        $check_wishlist_numbers->execute([$product_name, $user_id]);

        if ($check_wishlist_numbers->rowCount() > 0) {
            $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
            $delete_wishlist->execute([$product_name, $user_id]);
        }

        $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
        $insert_cart->execute([$user_id, $product_id, $product_name, $product_price, $product_qty, $product_image]);
        $message[] = 'added to cart!';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quick View</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'include/header.php' ?>

<section class="quick-view">
    <h1 class="title">Quick View</h1>

    <?php
    $product_id = $_GET['pid'];
    $select_products = $conn->prepare("SELECT * FROM 'products' WHERE id = ?");
    $select_products->execute([$product_id]);
    if ($select_products->rowCount() > 0) {
        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <form action="" class="box" method="POST">
                <div class="price">
                    $<span>
                        <?= $fetch_products['price'] ?>
                    </span>-
                </div>
                <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                <div class="name"><?= $fetch_products['name']; ?></div>
                <div class="details"><?= $fetch_products['details']; ?></div>
                <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
                <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
                <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
                <input type="number" min="1" value="1" name="p_qty" class="qty">
                <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
                <input type="submit" value="add to cart" class="btn" name="add_to_cart">
            </form>
            <?php
        }
    } else {
        echo '<p class="empty">no products added yet!</p>';
    }
    ?>
</section>

<?php include 'include/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
