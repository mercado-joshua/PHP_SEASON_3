<?php

#####################################
## IMPORT DATA FROM EXCEL
#####################################

include("connect.php");

require_once 'init.php';

if(isset($_POST["btnAdd"])) {
    $price = $_POST["price"];
    foreach($_POST["product_name"] as $index => $value) {
        $new_product_name = $value;
        $new_price = $price[$index];

        mysqli_query($connect, "INSERT INTO `product` (`product`, `price`) VALUES ('$new_product_name', '$new_price')");

        echo "<script>window.location.href='index.php?notify=Product has been uploaded!'</script>";
    }
}

if(isset($_POST["btnUpload"])) {
    echo "<hr>";
    echo "<table border='1'>";
    echo "<tr>
        <th>Product Name</th>
        <th>Price</th>
    </tr>";

    echo "<form method='POST'>";

    $btnStatus = "ENABLED";
    $filename = $_FILES["file"]["tmp_name"];

    if($_FILES["file"]["size"] > 0) {
        $file = fopen($filename, "r");
        $row = 1;
        $product_name = $price = "";
        $product_nameErr = $priceErr = "";

        while(($data = fgetcsv($file, 10000, ",")) !== FALSE) { // kukunin nya lahat ng data mula sa file

            if($row == 1) {
                $row++;
                continue; // i-ignore nya ung unang row
            }

            if(empty($data[0])) {
                $product_nameErr = "* product name is empty";
                $btnStatus = "DISABLED";
            } else {
                $product_name = $data[0];
            }

            if(empty($data[1])) {
                $product_nameErr = "* product price is empty";
                $btnStatus = "DISABLED";
            } else {
                $price = $data[1];
            }

            echo "<input type='hidden' name='product_name[]' value='{$product_name}'>";
            echo "<input type='hidden' name='price[]' value='{$price}'>";

            echo "<tr>
                <td>{$product_name}</td>
                <td>{$price}</td>
            </tr>";

        }
    }

    echo "<tr>
        <td>{$product_nameErr}<br>{$priceErr}</td>
        <td>
            <input type='submit' {$btnStatus} name='btnAdd' value='Add this product'>
        </td>
    </tr>";
    echo "</form>";
    echo "</table>";
}
?>
<html>
<body>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" value=""><br>
        <input type="submit" name="btnUpload" value="Upload Product">
    </form>

    <br>

    <a href="spreadsheet.php"><button>Export Data to Excel</button></a>
    <a href="blank.php"><button>PDF</button></a>

    <h2>Auto compute</h2>
    <table border="1">
        <tr>
            <th>Product name</th>
            <th>Price</th>
        </tr>
<?php

    #####################################
    ## AUTO COMPUTE PRICE CHECKBOX
    #####################################

    $query = mysqli_query($connect, "SELECT * FROM `product`");
    while($row = mysqli_fetch_assoc($query)) {
        $id_product = $row["id_product"];
        $product = $row["product"];
        $price = $row["price"];
?>
    <tr>
        <th><span><input type="checkbox" id="main_<?php echo $id_product; ?>" name="main_<?php echo $id_product; ?>" value="<?php echo $price; ?>" onClick="test(this);"><?php echo ucfirst($product); ?></span></th>
        <th>P <?php echo number_format($price) . ".00"; ?></th>
    </tr>
<?php
    }
?>
        <form method="POST">
            <?php
                $query = mysqli_query($connect, "SELECT * FROM `product`");
                while($row = mysqli_fetch_assoc($query)) {
                    $id_product = $row["id_product"];
                    $product = $row["product"];
                    $price = $row["price"];
            ?>
                <span style="display:none;" id="get_<?php echo $id_product; ?>">
                    <input type="checkbox" name="id_product[]" value="<?php echo $id_product; ?>">
                    <label><?php echo ucfirst($product) . " - " . number_format($price) . ".00<br>"; ?></label>
                </span>
            <?php
                }
            ?>

            <tr>
                <td>Total</td>
                <td><span id="totalcost"></span></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="btnAvail" value="Avail"></td>
            </tr>
        </form>
    <?php
    if(isset($_POST["btnAvail"])) {

        // https://stackoverflow.com/questions/42683774/page-not-redirecting-after-stripe-checkout
        echo "<form method='POST' action='payment.php'>";
        echo "<table border='1'>";

        echo "<tr>
            <th>Product</th>
            <th>Price</th>
        </tr>";

        $total = 0;
        foreach($_POST["id_product"] as $id_product) {
            $query = mysqli_query($connect, "SELECT * FROM `product` WHERE `id_product` = '$id_product'");
            while($row = mysqli_fetch_assoc($query)) {
                $product = $row["product"];
                $price = $row["price"];

                echo "<tr>
                    <td>{$product}</td>
                    <td>P ".number_format($price).".00</td>
                <tr>";
            }
            $total += $price;
        }

        echo "<tr>
            <td>Total</td>
            <td>P ".number_format($total).".00</td>
        <tr>";
    ?>
        <tr>
            <td></td>
            <td>
                <input type="hidden" name="total" value="<?php echo $total * 100; ?>">
                <script
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="<?php echo $stripe["pub_key"]?>"
                    data-amount="<?php echo $total * 100; ?>"
                    data-name="Joshua Mercado"
                    data-description="Full Payment"
                    data-email="mercado.joshua.web@gmail.com"
                    data-currency="PHP"
                    data-image="image.JPG"
                    data-locale="auto">
                </script>
            </td>
        </tr>
    <?php
        echo "</table>";
        echo "</form>";
    }
    ?>
    </table>

    <script>
        let total = 0;
        function test(item) {
            if(item.checked) {
                total += parseInt(item.value);
            } else {
                total -= parseInt(item.value);
            }

            document.getElementById("totalcost").innerHTML = "P " + total.toLocaleString() + ".00";
        }

        // sabayan pag check ng dalawang checkbox
        function toggleGroup(id) {
            let group = document.getElementById(id);
            let inputs = group.getElementsByTagName("input");
            for(let i = 0; i < inputs.length; i++) {
                inputs[i].checked = (inputs[i].checked ? false : true);
            }
        }

        window.onload = function() {
            <?php
            $query = mysqli_query($connect, "SELECT * FROM `product`");
            while($row = mysqli_fetch_assoc($query)):
                $id_product = $row["id_product"];
                $product = $row["product"];
                $price = $row["price"];
            ?>
            document.getElementById("main_<?php echo $id_product; ?>").onchange = function() {
                toggleGroup("get_<?php echo $id_product; ?>");
            };
            <?php endwhile; ?>
        };
    </script>
</body>
</htmL>