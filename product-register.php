<?php

if (!isset($_SESSION)) {
    session_start();
}



//Check remember me tocken exists
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true) {
    require_once "Process/auth-check.php";
}

$loggedIn = isset($_SESSION["logged_in"]) ? $_SESSION["logged_in"] : false;
$userRole = isset($_SESSION["active_account_type"]) ? $_SESSION["active_account_type"] : "";


if (!$loggedIn) {
    header("Location: index.php");
    exit;
}


//need to logged in and need to be a seller to access the product-register
if (strtolower($userRole) != "seller") {
    header("Location: home.php");
    exit;
}


require "header.php";


//fetch Categories form database
$categoryResult = Database::search(
    "SELECT `id`,`name` FROM `category` ORDER BY `name` "
);

$categories = [];
if ($categoryResult && $categoryResult->num_rows > 0) {
    while ($category = $categoryResult->fetch_assoc()) {
        $categories[] = $category;
    }
}


$successMsg = isset($_GET["success"]) ? $_GET["success"] : "";
$errorMsg = isset($_GET["error"]) ? $_GET["error"] : "";

?>
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header  -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Register New Product</h1>
            <p class="text-lg text-gray-600">Create a new product listing to sell your skills</p>
        </div>

        <!-- Success/Error Message  -->
        <?php if (!empty($successMsg)):   ?>
            <div id="alertBox" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-800"><strong>Success!</strong>Your product has been registerd successfully</p>
            </div>
        <?php endif; ?>

        <?php if(!empty($errorMsg)):   ?>
            <div id="alertBox" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800"><strong>Error : </strong><?php echo $errorMsg; ?></p>
            </div>
        <?php endif; ?>


        <!-- Registration Form  -->
        <div class="bg-white rouneded-lg shadow-md overflow-hidden">
            <form id="productForm" enctype="multipart/form-data" class="p-8" action="">
                <!-- product Title  -->
                <div class="mb-6">
                    <label for="productTitle" class="block text-sm font-medium text-gray-700 mb-2">
                        Product Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="productTitle" name="productTitle" placeholder="Enter Product Title"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" maxlength="150" />
                    <p class="text-xs text-gray-500 mt-1">Max 100 Charactors</p>
                </div>
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Product Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="5" placeholder="Describe your product in detail.."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" maxlength="1000"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Max 1000 Charactors</p>
                </div>

                <!-- Two Coloumn Layout  -->

                <div class="grid grid-cols md:grid-cols-2 gap-6 mb-6">
                    <!-- Category  -->
                    <div class="">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500
                        focus:border-transparent outline-none transition">
                            <option value="0">Select a Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category["id"]; ?>"><?php echo $category["name"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Price  -->

                    <div class="">
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (Rs) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-600">Rs.</span>
                            <input type="number" id="price" name="price" placeholder="0.00"
                                class="w-full pl-8 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500
                        focus:border-transparent outline-none transition" />
                        </div>
                    </div>

                </div>

                <!-- Two Coloum layout  -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <!-- Level  -->
                    <div class="">
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                            Level <span class="text-red-500">*</span>
                        </label>
                        <select name="level" id="level"
                            class="w-full pl-8 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500
                        focus:border-transparent outline-none transition">
                            <option value="0">Select Level</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced </option>
                        </select>

                    </div>

                    <!-- Status  -->
                    <div class="">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status"
                            class="w-full pl-8 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500
                        focus:border-transparent outline-none transition">
                            <option value="0">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>

                        </select>

                    </div>

                </div>


                <!-- Product Image  -->

                <div class="mb-8">
                    <label for="productImage" class="block text-sm font-medium text-gray-700 mb-2">
                        Product Image <span class="text-red-500">*</span>
                    </label>

                    <!-- Upload Area  -->
                    <div id="uploadArea" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 0 0-4 4v20a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V20m-6-8l-6-6m0 0l-6 6m6-6v12"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <p class="text-gray-700 font-medium mb-1">Drag and drop your image here</p>
                        <p class="text-gray-500 text-sm mb-4">or Click to select a file</p>
                        <p class="text-gray-400 text-xs">PNG, JPG up to 5MB</p>
                    </div>

                    <input type="file" id="productImage" name="productImage" accept="image/*" class="hidden" />

                    <!-- Image Preview  -->
                    <div class="mt-6 hidden" id="imagePreview">
                        <img src="" alt="Preview" id="previewImg" class="max-w-xs g-auto rounded-lg shadow-md mb-4" />
                        <button type="button" id="removeImage" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Remove Image</button>
                    </div>
                </div>

                <!-- Error Messgae  -->
                <div class="mb-6 p-4 rounded-lg-hidden" id="formMessage"></div>

                <!-- Submit Button  -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:scale-[1.02] active:scale-95">
                        Register Product
                    </button>

                    <a href="seller-dashboard.php" class="flex-1 bg-gray-200 text-gray-800 font-semibold py-3 rounded-lg hover:bg-gray-300 transition-colors text-center">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>


<script>
    const uploadArea = document.getElementById("uploadArea");
    const fileInput = document.getElementById("productImage");
    const imagePreview = document.getElementById("imagePreview");
    const previewImg = document.getElementById("previewImg");
    const removeImageBtn = document.getElementById("removeImage");
    const productForm = document.getElementById("productForm");
    const formMessage = document.getElementById("formMessage");


    // upload area Click
    uploadArea.addEventListener("click", () => {
        fileInput.click()
    });


    //File Input Change
    fileInput.addEventListener("change", (e) => handleFileSelect(e.target.files[0]));

    // Drag and Drop 
    uploadArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        uploadArea.classList.add("border-blue-500", "bg-blue-50");
    });

    uploadArea.addEventListener("dragleave", (e) => {
        uploadArea.classList.remove("border-blue-500", "bg-blue-50");
    });

    uploadArea.addEventListener("drop", (e) => {
        e.preventDefault();
        uploadArea.classList.remove("border-blue-500", "bg-blue-50");
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(files[0]);
            fileInput.files = dataTransfer.files;

            handleFileSelect(files[0]);
        }
    });


    function handleFileSelect(file) {

        //Validation file type 
        if (!file.type.startsWith("image/")) {
            showError("Please select a valid image file!");
            return;
        }


        //Validate File Size (5MB)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            showError("Image size must be less than 5MB");
            return;
        }

        //Create Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            imagePreview.classList.remove("hidden");
            uploadArea.classList.add("hidden");
            formMessage.classList.add("hidden");
        };
        reader.readAsDataURL(file);

    }


    //Remove Image 
    removeImageBtn.addEventListener("click", () => {
        fileInput.value = "";
        imagePreview.classList.add("hidden");
        uploadArea.classList.remove("hidden");
        previewImg.src = "";
    });



    //Form Sbmition
    productForm.addEventListener("submit",async (e) => {
        e.preventDefault();

        const productTitle = document.getElementById("productTitle").value;
        const description = document.getElementById("description").value;
        const category = document.getElementById("category").value;
        const price = parseFloat(document.getElementById("price").value);
        const level = document.getElementById("level").value;
        const status = document.getElementById("status").value;
        const image = fileInput.files[0];


        if (!productTitle) {
            showError("Product title is required");

        } else if (productTitle.length > 150) {
            showError("Product title must be less than 150 charters !");
        } else if (!description) {
            showError("Product description is required");
        } else if (description.length > 1000) {
            showError("Product description must be less than 1000 characters !");
        } else if (category == 0) {
            showError("Please Select a category");
        } else if (!price || price <= 0) {
            showError("Price must be grater than 0");
        } else if (level == 0) {
            showError("Please select a level !");
        } else if (status == 0) {
            showError("Please select a status");
        } else if (!image) {
            showError("Product image is required !");
        } else {

            const formData = new FormData();
            formData.append("productTitle", productTitle);
            formData.append("description", description);
            formData.append("category", category);
            formData.append("price", price);
            formData.append("level", level);
            formData.append("status", status);
            formData.append("productImage", image);

            try {
                const response = await fetch("Process/productRegisterProcess.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.text();

                if (result == "success") {
                    window.location.href = "product-register.php?success=Product Registerd Successfully !";
                } else {
                    showError(result);
                }

            } catch (error) {
                showError("An error occured.Please try again");
                console.error("Error:" + error);
            }


        }

    });

    //Show error message
    function showError(message) {
        formMessage.textContent = message;
        formMessage.className = "p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 mb-6";
        formMessage.classList.remove("hidden");
        window.scrollTo({
            top: formMessage.offsetTop - 100,
            behaviour: "smooth" 
        });
         resetMessage(formMessage);
    }

    //Show success message
    function showSuccess(message) {
        formMessage.textContent = message;
        formMessage.className = "p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 mb-6";
        formMessage.classList.remove("hidden");
        resetMessage(formMessage);

    }


    function resetMessage(element) {
        if (element) {
            setTimeout(() => {
                window.location.href = "product-register.php";
            }, 5000);
        }
    }


    //Reset the alert BOX

    const alertBox = document.getElementById("alertBox");
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.display = "none";
        }, 5000);
    }
</script>

<?php require "footer.php" ?>