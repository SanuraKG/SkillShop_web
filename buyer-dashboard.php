<?php include("header.php");

if (strtolower($userRole) != "buyer") {
    header("Location:home.php");
    exit;
}

$tab = isset($_GET["tab"]) ? $_GET["tab"] : "dashboard";


//Cart data
$cartItems = [];
$subtotal = 0;
$totalDeliveryFee = 0;
if ($tab == "cart") {
    //Delivery fee Calculation (buyer city_id)
    $buyerCItyQ = Database::search(
        "SELECT a.`city_id` FROM `user_profile` up JOIN `address` a ON up.`address_id`=a.`id`
    WHERE up.`user_id`=?",
        "i",
        [$userId]
    );

    $buyerCityId = ($buyerCItyQ && $buyerCItyQ->num_rows > 0) ? $buyerCItyQ->fetch_assoc()["city_id"] : 0;

    //fetch cart items
    $cartItemsQ = Database::search(
        "SELECT c.`id` AS `cart_item_id`,p.*,u.`fname` AS `seller_fname` , u.`lname` AS `seller_lname`,
        sa.`city_id` AS `seller_city_id`,sa.`id` AS `seller_id`
        FROM `cart` c
        JOIN `product` p ON c.`product_id`=p.`id`
        JOIN `user` u ON p.`seller_id`=u.`id`
        LEFT JOIN `user_profile` up ON u.`id`=up.`user_id`
        LEFT JOIN `address` sa ON up.`address_id`=sa.`id`
        WHERE c.`user_id`=?
        ORDER BY c.`created_at` DESC",
        "i",
        [$userId]
    );
    $sellerInCart = [];

    while ($item = $cartItemsQ?->fetch_assoc()) {
        $cartItems[] = $item;
        $subtotal += floatval($item["price"]);

        $sellerId = $item["seller_id"];
        //Already Charged the fee to one product
        if (!isset($sellerInCart[$sellerId])) {
            $deliveryFee = ($item["seller_city_id"] == $buyerCityId && $buyerCityId != 0) ? 200 : 500;
            $totalDeliveryFee += $deliveryFee;
            $sellerInCart[$sellerId] = $deliveryFee;
        }
    }

    $total = $subtotal + $totalDeliveryFee;
}

?>

<div class="min-h-screen bg-gray-50">

    <!-- Tab Navigation -->
    <div class="bg-white border-b sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex gap-8">
            <a href="?tab=dashboard" class="py-4 font-medium border-b-2 <?php echo $tab == "dashboard" ? "border-blue-600 text-blue-600" : "border-transparent text-gray-600 hover:text-gray-900"; ?>">
                Dashboard
            </a>
            <a href="?tab=cart" class="py-4 font-medium border-b-2 <?php echo $tab == "cart" ? "border-blue-600 text-blue-600" : "border-transparent text-gray-600 hover:text-gray-900"; ?>">
                Public Store
            </a>

        </div>
    </div>


    <!-- Dashboard Tab  -->
    <?php if ($tab == "dashboard"): ?>
        <section class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <h2 class="text-3xl font-bold text-gray-900">Buyer DashBoard</h2>
                <p class="text-gray-600">Manage your learning Journey</p>
            </div>

        </section>
    <?php elseif ($tab == "cart"): ?>
        <section class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <h2 class="text-3xl font-bold text-gray-900">Shopping Cart</h2>
                <p class="text-gray-600">Manage your items before checkout</p>

            </div>
        </section>
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <?php if (empty($cartItems)): ?>
                <div class="bg-white rounded-2xl border border-slate-100 p-16 text-center shadow-sm">
                    <div class="text-6xl mb-6">🛒</div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Your Cart is empty</h2>
                    <p class="text-slate-500 mb-8 max-w-sm mx-auto">Explore our wide range of skills and start your leading journey today</p>
                    <a href="search-products.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700
                     hover transition-all shadow active:scale-95 ">Browse Skills</a>
                </div>
            <?php else: ?>
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="flex-1 space-y-4">
                        <?php foreach ($cartItems as $item):
                            $sellerName = $item["seller_fname"] . " " . $item["seller_lname"];
                        ?>
                            <!-- Cart Item  -->
                            <a href="product-view.php?id=<?= $item["id"]; ?>" id="cart-item-<?= $item["cart_item_id"]; ?>" class="bg-white rounded-2xl border border-slate-100 p-4 flex gap-4 shadow-sm hover:shadow-md transition-shadow group">
                                <div class="w-24 h-24 rounded-xl overflow-hidden flex-shrink-0 bg-slate-100">
                                    <?php if ($item["image_url"]): ?>
                                        <img src="<?= $item["image_url"]; ?>" class="w-full h-full object-cover" />
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-3xl">📚</div>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <div class="">
                                            <h3 class="font-bold text-gray-900 e leading-tight group-hover:text-blue-600 transition-colors
                                        line-clamp-1"><?= $item["title"]; ?></h3>
                                            <p class="text-sm text-slate-500 mt-0.5" ><?= $sellerName; ?></p>
                                        </div>
                                        <button onclick="removeItem(<?= $item['cart_item_id'] ?>, event);" class="text-slate-300 hover:text-rose-500 p-1 transition-colors"
                                            title="Remove">🗑️</button>
                                    </div>
                                    <div class="flex items-center gap-3 mt-4">
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600"><?= $item["level"] ?></span>
                                        <div class="text-lg font-extrabold text-blue-600 ml-auto">Rs.<?= number_format($item["price"], 2); ?></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>

                    </div>
                    <!-- Summery Column  -->
                    <aside class="lg:w-96 flex-shrink-0">
                        <div class="bg-white rounded-2xl border-slate-100 p-6 shadow-xl sticky top-24">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Order Summery</h2>

                            <div class="space-y-4 pb-6 border-b border-slate-100">
                                <div class="flex justify-between text-slate-600">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-gray-900" id="subtotal">Rs.<?= number_format($subtotal, 2) ?> </span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <div class="flex items-center gap-1.5">
                                        <span>Course Document Delivery Fee</span>
                                        <div class="group relative">

                                            <span class="text-xs cursor-help bg-slate-100 w-4 h-4 rounded-full flex items-center
                       justify-center font-bold">?
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-slate-900
text-white text-[10px] rounded shadow-xl opacity-0 inivisible group-hover:opacity-100 group-hover:visible transition-all">
                                                Rs.200 within same city , Rs 500 across cities Charged per seller
                                            </div>



                                        </div>


                                    </div>
                                    <span class="font-bold text-gray-900">Rs. <span id="delivery"><?= number_format($totalDeliveryFee, 2); ?></span></span>
                                </div>

                            </div>
                            <div class="pt-6">
                                <div class="flex justify-between items-center mb-8">
                                    <span class="text-lg font-bold text-gray-900">Total</span>
                                    <span class="text-2xl font-black text-blue-600 font-mono">Rs. <span id="total"><?= number_format($total, 2); ?></span>
                                    </span>
                                </div>

                                <button class="block w-full py-4 text-center bg-gradient-to-r from-blue-600 to-indigo-600
                                hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl trasnition-all active:scale-95 mb-4">
                                    Proceed to Checkout
                                </button>
                                <p class="text-center text-xs text-slate-400">Secure checkout powerd by Payhere</p>
                            </div>
                        </div>

                    </aside>


                </div>
            <?php endif; ?>
        </section>
        <!-- ✅ Replace your toast div with this -->
        <div id="toast"
            style="position:fixed; top:20px; right:20px; z-index:9999; transform:translateY(-120px); transition:transform 0.3s ease; pointer-events:none;">
            <div style="background:#1e293b; color:white; padding:12px 24px; border-radius:16px; display:flex; align-items:center; gap:12px; font-weight:600;">
                <span id="toast-icon">✓</span>
                <span id="toast-msg">Removed from cart</span>
            </div>
        </div>


        <!-- Cart js  -->
        <?php if (!empty($cartItems)): ?>
            <script>
                let tid;

                function shadowToast(msg, icon = "✓") {
                    clearTimeout(tid);
                    const toast = document.getElementById("toast");
                    document.getElementById("toast-msg").innerText = msg;
                    document.getElementById("toast-icon").innerText = icon;
                    toast.style.transform = "translateY(0)"; 
                    tid = setTimeout(() => {
                        toast.style.transform = "translateY(-100px)"; 
                    }, 3000);
                }





                
                async function removeItem(cartItemID, e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!confirm("Remove this item from your cart")) return;

                    const itemEl = document.getElementById(`cart-item-${cartItemID}`);
                    itemEl.style.opacity = '0.5';
                    itemEl.style.pointerEvents = 'none';

                    const formData = new FormData();
                    formData.append('cart_item_id', cartItemID);

                    try {
                        const res = await fetch('Process/removeFromCart.php', {
                            method: "POST",
                            body: formData
                        });

                        const data = await res.json();

                        if (data.success) {

                            itemEl.remove();
                            document.getElementById('subtotal').innerText = parseFloat(data.subtotal).toLocaleString(undefined, {
                                minimumFractionDigits: 2
                            });
                            document.getElementById('delivery').innerText = parseFloat(data.delivery).toLocaleString(undefined, {
                                minimumFractionDigits: 2
                            });
                            document.getElementById('total').innerText = parseFloat(data.total).toLocaleString(undefined, {
                                minimumFractionDigits: 2
                            });

                            const cc = document.getElementById("cart-count");
                            if (cc) {
                                cc.textContent = data.itemCount;
                            }

                            shadowToast('Item removed successfully');

                            //if empty, page reload
                            if (parseFloat(data.itemCount) == 0) window.location.reload();

                        } else {
                            itemEl.style.opacity = '1';
                            itemEl.style.pointerEvents = 'auto';
                            alert(data.message || 'Error Removal! Please try again');
                        }


                    } catch (e) {
                        itemEl.style.opacity = '1';
                        itemEl.style.pointerEvents = 'auto';
                        alert("Something went wrong! Please try again");
                    }
                }
            </script>

        <?php endif; ?>
    <?php endif; ?>

</div>



<?php include("footer.php"); ?>