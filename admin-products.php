<?php


session_start();
if (!isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin-login.php");
    exit();
}

require_once "db/connection.php";

$query = "SELECT p.*,c.`name` AS `category_name`,u.`fname`,u.`lname`
FROM `product` p
 JOIN `category` c ON p.`category_id`=c.`id`
 JOIN `user` u  ON p.`seller_id`=u.`id`
ORDER BY u.`created_at` DESC";

$products = Database::search($query);


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/skg-02.png" type="images/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
    <title>Admin Productts | Skillshop</title>
</head>

<body class="bg-slate-50 min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">

        <!-- Sidebar  -->
        <aside class="w-full md:w-64 bg-slate-900 text-white flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-blue-500">SkillShop</h1>
                <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">Admin Control</p>
            </div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="admin-dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-600 rounded-xl text-sm font-medium transition-colors">
                    <span>📊 </span> Dashboard
                </a>
                <a href="admin-users.php" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-600 rounded-xl text-sm font-medium transition-colors">
                    <span>👤 </span> User Managment
                </a>
                <a href="admin-products.php" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20">
                    <span>👜 </span> Product Manager
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-600 rounded-xl text-sm font-medium transition-colors">
                    <span>📜 </span> Transactions
                </a>
            </nav>
            <div class="mt-auto p-6 border-t border-slate-800">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center font-bold">A</div>
                    <div class="">
                        <p class="text-sm font-bold"><?= $_SESSION["admin_fname"]; ?></p>
                        <p class="text-[10px] text-slate-400 uppercase">Adminstrator</p>
                    </div>
                </div>
                <a href="Process/adminLogoutProcess.php" class="block w-full text-center py-2 bg-slate-800 hover:bg-red-900/40
                hover:text-red-400 rounded-lg text-xs font-bold transition-all border border-slate-70">Logout</a>
            </div>
        </aside>

        <!-- Main Content  -->
        <main class="flex-1 overflow-y-auto">

            <!-- Top Nav  -->
            <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center sticky top-0 z-30">
                <h2 class="text-xl font-extrabold text-slate-900">Manage Products</h2>
                <div class="flex items-center gap-4">
                    <input type="text" id="prodSearch" onkeyup="filterProducts();" placeholder="Search Products..."
                        class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus-ring-4
              focus:ring-blue-500/10 outline-none" />
                </div>
            </header>
            <div class="p-8">
                <div class="grid grid-cols-1 gap-6" id="productsGrid">
                    <?php while ($prod = $products->fetch_assoc()): ?>
                        <div class="product-item bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col
                                   lg:flex-row gap-6 items-center transition-all hover:shadow-md">
                            <img src="<?= $prod["image_url"] ?>" class="w-24 h-24 rounded-xl object-cover bg-slate-100  
                                  flex-shrink-0" onclick="viewProduct(<?= $prod['id']; ?>)" />
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black uppercase
                                     rounded-md"><?= $prod["category_name"] ?></span>
                                    <span id="status-<?= $prod["id"]; ?>" class="text-[10px] font-bold uppercase <?= ($prod["status"] == "active") ? "text-green-500" : "text-red-500";  ?>">
                                        <?= $prod["status"]; ?>
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 prod-title cursor-pointer" onclick="viewProduct(<?= $prod['id']; ?>);"><?= $prod["title"]; ?></h3>
                                <p class="text-xs text-slate-900 prod-seller">By<?= $prod["fname"] . " " . $prod["lname"]; ?></p>
                            </div>


                            <div class="text-center lg:text-right flex-shrink-0">
                                <p class="text-sm font-medium text-slate-400 uppercase tracking-widest mb-1">Price</p>
                                <p class="text-xl font-black text-slate-900 ">Rs. <?= number_format($prod["price"], 2) ?></p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <button onclick="viewProduct(<?= $prod['id']; ?>)" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 transition-all ">View Details</button>
                                <button onclick="toggleProductStatus(<?= $prod['id']; ?>); " id="btn-<?= $prod['id']; ?>"
                                    class="px-6 py-2.5 rounded-xl text-xs font-bold transition-all <?= ($prod["status"] ==  "active") ? "bg-red-50 text-red-500 hover:bg-red-600 hover:text-white"
                                                                                                        : "bg-green-600 text-white hover:bg-green-700"; ?>">
                                    <?= ($prod["status"] == "active") ? "Block Product" : "Unblock Product"; ?></button>
                            </div>


                        </div>
                    <?php endwhile; ?>
                </div>

            </div>

    </div>
    </main>

    </div>

    <!-- Prouduct View Model  -->
    <div id="productModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeProductModal();"></div>
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-2xl relative z-10 animate-in fade-in zoom-in duration-300">
                <button onclick="closeProductModal();" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 transition-colors">X</button>
                <div id="productModalContent" class="p-8">
                    <!-- Loading State  -->
                    <div class="flex justify-center py-20">
                        <div class="animate-spin rounded-full j-12 w-12 border-blue-500 border-t-transparent">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script>
        function closeProductModal() {
            document.getElementById("productModal").classList.add("hidden");
        }



        async function viewProduct(id) {
            const modal = document.getElementById("productModal");
            const content = document.getElementById("productModalContent");
            modal.classList.remove("hidden");


            content.innerHTML = '<div class = "flex justify-center py-20" ><div class ="animate-spin rounded-full j-12 w-12 border-blue-500 border-t-transparent" >< /div> </div>';

            try {

                const res = await fetch(`Process/admin/getProductDetails.php?id=$(id)`);
                const data = await res.json();

                if (data.success) {

                    const p = data.product; 

                    content.innerHTML = `
            
             <div class="flex flex-col md:flex-row gap-8">
        <div class="w-full md:w-1/3">
            <img src="${p.image_url}" class="w-full aspect-square rounded-2xl object-cover shadow-lg bg-slate-50" />
            <div class="mt-4 p-4 bg-slate-50 rounded-2xl">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Status</p>
                <span class="px-3 py-1 text-[10px] font-bold rounded-fill uppercase ${p.status =='active' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}">${p.status}</span>

            </div>
        </div>
        <div class="flex-1">
    <span class="px-2 py-0.5 bg-blue-50  text-blue-600 text-[10px] font-black uppercase rounded-md mb-2 inline-block">${p.category_name}</span>
    <h3 class="text-2xl font-black text-slate-900 leading-tight mb-2">${p.title}</h3>
    <div class="flex items-center gap-2 mb-6">
        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center font-bold text-slate-400 text-xs">
            ${p.fname[0]}
        </div>
        <div class="">
            <p class="text-xs font-bold text-slate-900">By ${p.fname} ${p.lname}</p>
            <p class="text-[10px] text-slate-400">${p.seller_email}</p>
        </div>
    </div>
    <div class="space-y-4">
        <div class="">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Description</p>
            <p class="text-sm text-slate-600 leading-relaxed">${p.description}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
            <div class="">
                 <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">price</p>
                <p class="text-lg font-black text-slate-600 ">Rs . ${parseFloat(p.price).toLocaleString()}</p>
            </div>
                 <div class="">
                 <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Levels</p>
                <p class="text-lg font-black text-slate-900 leading-relaxed">${p.level}</p>
            </div>
        </div>
    </div>


</div>

    </div>

            
            
            `

                } else {
                    content.innerHTML = `<p class="text-center text-red-500 font-bold py-10">${data.message}</p>`;
                }

            } catch (err) {
                alert("View Product Function:" + err);
            }


        }






        function filterProducts() {
            const filter = document.getElementById('prodSearch').value.toLowerCase();
            const items = document.querySelectorAll('.product-item');

            items.forEach(item => {
                const title = item.querySelector('.prod-title').innerText.toLowerCase();
                const seller = item.querySelector('.prod-seller').innerText.toLowerCase();
                if (title.includes(filter) || seller.includes(filter)) {
                    item.style.display = "flex";
                } else {
                    item.style.display = "none";
                }
            });
        }

        async function toggleProductStatus(prodId) {
            const btn = document.getElementById('btn-' + prodId);
            const statusSpan = document.getElementById('status-' + prodId);

            btn.disabled = true;
            const fd = new FormData();
            fd.append('id', prodId);

            try {
                const res = await fetch('process/toggleProductStatus.php', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    if (data.newStatus == 'active') {
                        statusSpan.innerText = 'active';
                        statusSpan.className = 'text-[10px] font-bold uppercase text-green-500';
                        btn.innerText = 'Block Product';
                        btn.className = 'px-6 py-2.5 rounded-xl text-xs font-bold transition-all bg-red-50 text-red-600 hover:bg-red-600 hover:text-white';
                    } else {
                        statusSpan.innerText = 'blocked';
                        statusSpan.className = 'text-[10px] font-bold uppercase text-red-500';
                        btn.innerText = 'Unblock Product';
                        btn.className = 'px-6 py-2.5 rounded-xl text-xs font-bold transition-all bg-green-600 text-white hover:bg-green-700';
                    }
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.disabled = false;
            }
        }
    </script>


</body>

</html>