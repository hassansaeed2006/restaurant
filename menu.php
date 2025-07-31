<?php
require_once 'db_connect.php';
session_start();

$products = [];

// Get all products from database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

$image_mappings = [
    'strawberry mambo' => 'mambo.jpeg',
    'habba dubai' => 'dubai.jpeg',
    'Dubai Crepe' => 'crepe.jpeg',
    'Qashtouta' => 'qashtota.webp',
    'sweet Koshari' => 'koshary.jpeg',
    'Plain Rice Pudding' => 'rice.jpeg',
    'LOQA' => 'LOQA.jpeg',
    'Farawlita dubai' => 'farawlita dubai.jpeg',
    'Cheese Bomb' => 'bomb.jpeg',
    'Mini Kabsa' => 'Mini Kabsa.jpeg'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - B-Laban</title>
    <link rel="stylesheet" href="menu.css">
</head>
<body>
    <nav class="navbar">
        <a href="introduction.php"><img src="Photos/بلبن.png" alt="b-laban" id="b-laban-logo"></a>
        <a href="cart.html"><img src="Photos/cart.png" alt="cart" id="cart-icon"></a>
    </nav>

    <div class="menu-container">
        <?php foreach ($products as $index => $product): ?>
        <div class="menu-item">
            <img src="Photos/<?php echo htmlspecialchars($image_mappings[$product['name']] ?? 'default.jpg'); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 <?php if ($product['name'] === 'Dubai Crepe') echo 'id="crepe"'; ?>>
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><?php echo htmlspecialchars($product['name']); ?> with toppings</p>
            <button class="btn" onclick="openPopup('popup<?php echo $index + 1; ?>')">View More</button>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="overlay" onclick="closePopup()"></div>

    <div id="popups">
        <?php foreach ($products as $index => $product): ?>
        <div class="popup" id="popup<?php echo $index + 1; ?>">
            <h3>Customize <?php echo htmlspecialchars($product['name']); ?></h3>
            <h3 style="color: red;">*PRICE : <?php echo number_format($product['price'], 2); ?> $</h3>
            <form onsubmit="submitOrder(event, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>)">
                <?php
                $toppings = [];
                switch(strtolower($product['name'])) {
                    case 'strawberry mambo':
                        $toppings = ['Pistachio', 'Kinder', 'Chocolate', 'Mango Instead of Strawberry'];
                        break;
                    case 'habba dubai':
                        $toppings = ['Nuts', 'Kinder', 'Chocolate'];
                        break;
                    case 'dubai crepe':
                        $toppings = ['Nutella', 'Nuts', 'Basbousa', 'Qeshta', 'Louts Powder', 'Louts sauce', 'Honey'];
                        break;
                    case 'qashtouta':
                        $toppings = ['Lotus Pudding Rice', 'Lotus Dish', 'Caramel', 'Mango Plate', 'Super Luxe', 'Rice Pudding With Nuts', 'Rice Pudding Mango', 'Nuts plate'];
                        break;
                    case 'sweet koshari':
                        $toppings = ['Pistachio', 'Pistachio Lotus', 'Oreo Nutella', 'Kinder', 'Nutella', 'Mango', 'Lotus', 'Lotus & pistachio'];
                        break;
                    case 'plain rice pudding':
                        $toppings = ['Qeshta And Nuts', 'Nuts', 'CHOKLALITA RICE', 'Belgian Lotus Pudding Rice'];
                        break;
                    case 'loqa':
                        $toppings = ['Louts', 'Pistachio', 'Mango', 'Kinder', 'Chocolate'];
                        break;
                    default:
                        $toppings = ['Nutella', 'Nuts', 'Basbousa', 'Qeshta', 'Louts Powder', 'Louts sauce', 'Honey'];
                }
                foreach ($toppings as $topping): ?>
                    <label>
                        <input type="radio" name="topping<?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($topping); ?>">
                        <?php echo htmlspecialchars($topping); ?>
                    </label><br>
                <?php endforeach; ?>
                <br>
                <button type="submit" class="btn">Order Now</button>
                <button type="button" class="btn" onclick="closePopup()">Cancel</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
    function openPopup(id) {
        document.getElementById(id).style.display = 'block';
        document.querySelector('.overlay').style.display = 'block';
    }

    function closePopup() {
        document.querySelectorAll('.popup').forEach(p => p.style.display = 'none');
        document.querySelector('.overlay').style.display = 'none';
    }

    function submitOrder(event, productName, price) {
        event.preventDefault();
        const form = event.target;
        const toppingInput = form.querySelector('input[type="radio"]:checked');
        const topping = toppingInput ? toppingInput.value : '';

        fetch('cart_operations.php?action=add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_name: productName,
                price: price,
                topping: topping,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Item added to cart!');
                closePopup();
            } else {
                alert('Failed to add to cart: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while adding to cart.');
        });
    }
    </script>
</body>
</html>
