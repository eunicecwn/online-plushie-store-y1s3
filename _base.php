<?php

session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;   // (??) to check if the key exists in the $_REQUEST array [exist--> value = parameter] [not exist --> value = null]
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    ob_clean(); // Clear any previous output
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
// Set session variable with value
// temp ('info' , 'ken');
// $_SESSION["temp_info"] = 'ken';

// read session variable value
// temp ('info');
// ken 
// $_SESSION["temp_info"]  // removed (unset)
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key) {
    $f = $_FILES[$key] ?? null; //if don't have the key given null
    
    if ($f && $f['error'] == 0) {  //if no error return the object
        return (object)$f;
    }

    return null; //else return null
}

//Upload multiple files
function get_multiple_files($key) {
    // Get the files array from $_FILES
    $files = $_FILES[$key] ?? null;
    
    // Check if we have files and at least one was uploaded
    if (!$files || empty($files['name'][0])) {
        return null;
    }

    $fileList = []; // This will store our valid files
    
    // Loop through each uploaded file
    foreach ($files['name'] as $index => $name) {
        // Check if this specific file has no errors
        if ($files['error'][$index] == 0) {
            // Create a file object with all properties
            $fileList[] = (object) [
                'name' => $name,
                'tmp_name' => $files['tmp_name'][$index],
                'type' => $files['type'][$index],
                'size' => $files['size'][$index]
            ];
        }
    }

    return $fileList;
}


// Crop, resize and save photo
function save_photo($f, $folder, $width = 500, $height = 500) {
    $photo = uniqid() . '.jpg';
    
    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

// Is money?
function is_money($value) {
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

/*
^\
-? = negative optional
\d+ = can be grow many digit
(\.\d{1,2})?  =  it can be one or two digit decimal
$/'
*/

function fetchValue($db, $sql, $params = []) {
    $stm = $db->prepare($sql);
    $stm->execute($params);
    return $stm->fetchColumn();
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

// Generate <input type='search'>
function html_search($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='text'>
function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input textarea>
function html_textarea($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <checkbox>
function html_checkbox($name, $value = 1, $checked = false, $attrs = '') {
    $checkedAttr = $checked ? ' checked' : '';
    return sprintf(
        '<input type="checkbox" name="%s" value="%s"%s %s>',
        encode($name),
        encode($value),
        $checkedAttr,
        $attrs
    );
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate table headers <th>
/*$fields = [
    'id'         => 'Id',  key point to value
    'name'       => 'Name',
    'gender'     => 'Gender',
    'program_id' => 'Program',
];
 */
function table_headers($fields, $sort, $dir, $href = '') {  //href is optional by default there is empty
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction (ascending / descending)
        $c = '';    // Default class
        
        if ($k == $sort){
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";   // echo one table header
    }
}

function table_headers2($fields, $sort, $dir, $queryString) {
    $html = '';
    foreach ($fields as $field => $label) {
        // Determine new direction and css class
        $newDir = ($sort == $field && $dir == 'asc') ? 'desc' : 'asc';
        $class = ($sort == $field) ? $dir : '';

        // Build query string URL
        $url = "?sort=$field&dir=$newDir&$queryString";

        // Add inline link with class
        $html .= "<a href=\"$url\" class=\"sort-link $class\">$label</a>";
    }
    return $html;
}


// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '') {
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

//generate product id
function generateProductID() {
    return 'PROD-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

//generate voucher id
function generateVoucherID() {
    return 'VOUCHER-' . strtoupper(substr(md5(uniqid()), 0, 5));
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key) {
    global $_err;     // unable access if dont have put the global
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";  //if got any error message ? 
    }
    else {
        echo '<span></span>'; //empty span
    }
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

//connect database
// Global PDO object (database name , port=3306(default))
$_db = new PDO('mysql:dbname=online_shopping', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,  // PDO::FETCH_OBJ  --> object mode
]);

// Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// ============================================================================
// Security Functions
// ============================================================================

// Restore user object from session
$_user = $_SESSION['_user'] ?? null;

/**
 * Store user data in session and redirect
 */
function login($user, $url = null) {
    $_SESSION['_user'] = $user;
    redirect($url);
}

/**
 * Remove user data from session and redirect
 */
function logout($url = 'index.php') {
    if (isset($_SESSION['_user'])) {
        sync_cart_to_db($_SESSION['_user']->id);
    }
    unset($_SESSION['_user']);
    redirect($url);
}

/**
 * Authorization check
 * @param string ...$roles Required roles (empty means any authenticated user)
 */
function auth(...$roles) {
    global $_user;
    
    // If user not logged in
    if ($_user === null) {
        redirect('login.php');
    }
    
    // If roles specified but user role not in list
    if (!empty($roles) && !in_array($_user->role, $roles)) {
        redirect('unauthorized.php');
    }
}
function current_user() {
    return $_SESSION['user'] ?? null;
}

// User roles constants
define('ROLE_ADMIN', 'Admin');
define('ROLE_STAFF', 'Staff');
define('ROLE_MEMBER', 'Member');

/**
 * Check if email is valid format
 */
function is_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
}

/**
 * Generate password input field
 */
function html_password($key, $attr = '') {
    echo "<input type='password' id='$key' name='$key' $attr>";
}

// ============================================================================
// Cart Function
// ============================================================================

// Get shopping cart
function get_cart()
{
    global $_db;

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }

    $cart = [];
    foreach ($_SESSION['cart'] as $productID => $quantity) {
        // Fetch product details including price and name
        $stmt = $_db->prepare("SELECT productID, productName, price FROM product WHERE productID = :productID");
        $stmt->execute(['productID' => $productID]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Fetch the first image from the gallery
            $imageStmt = $_db->prepare("SELECT imageName FROM gallery WHERE productID = :productID ORDER BY imageID ASC LIMIT 1");
            $imageStmt->execute(['productID' => $productID]);
            $imageRow = $imageStmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($imageRow['imageName'])) {
                $imageNames = explode(',', $imageRow['imageName']);
                $firstImage = trim($imageNames[0]);
                $imageURL = "../productimages/" . $firstImage . ".jpg";
            } else {
                $imageURL = "/images/placeholder.png";
            }

            // Add product details to the cart
            $cart[$productID] = [
                'id'       => $product['productID'],
                'name'     => $product['productName'],
                'price'    => (float) $product['price'],
                'image'    => $imageURL,
                'quantity' => (int) $quantity,
            ];
        }
    }

    return $cart;
}

// Set shopping cart
function set_cart($cart = [])
{
    $_SESSION['cart'] = $cart;
}

// Add product to cart
function add_to_cart($id, $quantity = 1) {
    global $_db;

    // Fetch product details from the database
    $stmt = $_db->prepare("SELECT productID, productName, price FROM product WHERE productID = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        return false; // Return false if product not found
    }

    // Fetch the cover image from the gallery
    $imageStmt = $_db->prepare("SELECT imageName FROM gallery WHERE productID = ? AND is_cover = 1 LIMIT 1");
    $imageStmt->execute([$id]);
    $imageRow = $imageStmt->fetch(PDO::FETCH_ASSOC);

    // Check if an image exists
    $imageURL = (!empty($imageRow['imageName']) && file_exists("../productimages/" . trim($imageRow['imageName'])))
        ? "../productimages/" . trim($imageRow['imageName'])
        : "/images/placeholder.png";

    // Initialize cart if not set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add to session cart
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$id] = [
            'id'       => $product['productID'],
            'name'     => $product['productName'],
            'price'    => (float) $product['price'],
            'image'    => $imageURL,
            'quantity' => (int) $quantity,
        ];
    }

    // Sync to DB if logged in
    if (isset($_SESSION['_user'])) {
        sync_cart_to_db($_SESSION['_user']->id);
    }
    
    return true;
}

// Update update_cart function
function update_cart($productID, $quantity) {
    global $_db;
    
    // Validate quantity
    $quantity = max(1, min(100, (int)$quantity));
    
    // Update session cart first
    if (isset($_SESSION['cart'][$productID])) {
        $_SESSION['cart'][$productID]['quantity'] = $quantity;
    }
    
    // Update database if logged in
    if (isset($_SESSION['_user'])) {
        try {
            $_db->beginTransaction();
            
            $stmt = $_db->prepare("
                INSERT INTO member_carts (memberID, productID, quantity) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = ?
            ");
            $stmt->execute([
                $_SESSION['_user']->id,
                $productID,
                $quantity,
                $quantity
            ]);
            
            $_db->commit();
            
            // Reload cart from DB to ensure sync
            load_cart_from_db($_SESSION['_user']->id);
        } catch (Exception $e) {
            $_db->rollBack();
            error_log("Update cart failed: " . $e->getMessage());
        }
    }
    
    // Ensure all product details are preserved
    if (isset($_SESSION['cart'][$productID])) {
        // Prepare the query to fetch product details
        $stmt = $_db->prepare("SELECT productName, price FROM product WHERE productID = ?");
        $stmt->execute([$productID]);
        
        // Check if the product was found
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // If product found, update the session cart
            $_SESSION['cart'][$productID]['name'] = $product['productName'];
            $_SESSION['cart'][$productID]['price'] = (float)$product['price'];
        } else {
            // Handle the case where product is not found (optional)
            error_log("Product not found: productID " . $productID);
        }
    }
}

// Update remove_from_cart function
function remove_from_cart($id) {
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    // Sync to DB if logged in
    if (isset($_SESSION['_user'])) {
        global $_db;
        $_db->prepare("DELETE FROM member_carts WHERE memberID = ? AND productID = ?")
           ->execute([$_SESSION['_user']->id, $id]);
    }
}

// Clear the cart
function clear_cart() {
    $_SESSION['cart'] = [];
    
    // Clear DB cart if logged in
    if (isset($_SESSION['_user'])) {
        global $_db;
        $_db->prepare("DELETE FROM member_carts WHERE memberID = ?")
           ->execute([$_SESSION['_user']->id]);
    }
}

function get_cart_count()
{
    return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}


function get_cart_with_images() {
    global $_db;
    
    $cart = [];
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productID => $item) {
            // Ensure required fields exist
            if (!isset($item['name']) || !isset($item['price'])) {
                $stmt = $_db->prepare("SELECT productName, price FROM product WHERE productID = ?");
                $stmt->execute([$productID]);
                if ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $item['name'] = $product['productName'];
                    $item['price'] = (float)$product['price'];
                }
            }
            
            // Get image
            $imageStm = $_db->prepare("SELECT imageName FROM gallery WHERE productID = ?  AND is_cover=1 LIMIT 1");
            $imageStm->execute([$productID]);
            $imageRow = $imageStm->fetch(PDO::FETCH_ASSOC);
            
            $imageURL = "/images/placeholder.png";
            if (!empty($imageRow['imageName'])) {
                $imagePath = "../productImage/" . trim($imageRow['imageName']);
                if (file_exists($imagePath)) {
                    $imageURL = $imagePath;
                }
            }
            
            $cart[] = [
                'id' => $productID,
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'image' => $imageURL
            ];
        }
    }
    return $cart;
}
function sync_cart_to_db($memberID) {
   global $_db;
   
   if (!isset($_SESSION['cart'])){
       return;
   }

   $_db->beginTransaction();
   try {
       // First clear existing cart items
       $_db->prepare("DELETE FROM member_carts WHERE memberID = ?")
          ->execute([$memberID]);

       // Insert current cart items if cart is not empty
       if (!empty($_SESSION['cart'])) {
           $stm = $_db->prepare("INSERT INTO member_carts (memberID, productID, quantity) VALUES (?, ?, ?)");
           foreach ($_SESSION['cart'] as $productID => $item) {
               $quantity = is_array($item) ? $item['quantity'] : $item;
               $stm->execute([$memberID, $productID, $quantity]);
           }
       }
       $_db->commit();
   } catch (Exception $e) {
       $_db->rollBack();
       error_log("Cart sync failed: " . $e->getMessage());
   }
}

/**
* Load cart from database for logged-in members
*/
function load_cart_from_db($memberID) {
   global $_db;
   
   $stm = $_db->prepare("
       SELECT c.productID, c.quantity, p.productName, p.price 
       FROM member_carts c
       JOIN product p ON c.productID = p.productID
       WHERE c.memberID = ?
   ");
   $stm->execute([$memberID]);
   
   $_SESSION['cart'] = [];
   while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
       $_SESSION['cart'][$row['productID']] = [
           'id' => $row['productID'],
           'quantity' => (int)$row['quantity'],
           'name' => $row['productName'],
           'price' => (float)$row['price']
       ];
   }
}

function calculate_subtotal($cart) {
    return array_reduce($cart, function($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);
}

function generate_order_id(): string
{
    // Use a shorter timestamp: Date (YYMMDD) + Hour and Minute (HHMM)
    $timestamp = date('ymdHi'); // 8 characters: YYMMDDHHMM
    $randomNumber = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // 3 characters

    $orderID = 'ORD-' . $timestamp . $randomNumber;

    // Ensure the total length is within the 20-character limit
    if (strlen($orderID) > 20) {
        $orderID = substr($orderID, 0, 20); // Trim to 20 characters
    }

    return $orderID;
}

// ============================================================================
// Wishlist Function
// ============================================================================

/**
 * Get wishlist count
 */
function get_wishlist_count(): int
{
    if (!isset($_SESSION['wishlist'])) {
        return 0;
    }
    return count($_SESSION['wishlist']);
}

/**
 * Add to wishlist with DB sync
 */
function add_to_wishlist($productID) {
    global $_db;
    $memberID = $_SESSION['_user']->id ?? null;
    
    if (!$memberID || !$productID) return false;
    
    $stmt = $_db->prepare("INSERT IGNORE INTO member_wishlist (memberID, productID) VALUES (?, ?)");
    return $stmt->execute([$memberID, $productID]);
}

/**
 * Remove from wishlist with DB sync
 */
function remove_from_wishlist(string $productID): bool
{
    global $_db;

    if (isset($_SESSION['wishlist'])) {
        $index = array_search($productID, $_SESSION['wishlist'], true);
        if ($index !== false) {
            unset($_SESSION['wishlist'][$index]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        }
    }

    // Sync to DB if logged in
    if (isset($_SESSION['_user'])) {
        try {
            $stmt = $_db->prepare("DELETE FROM member_wishlist WHERE memberID = ? AND productID = ?");
            $stmt->execute([$_SESSION['_user']->id, $productID]);
            return true;
        } catch (PDOException $e) {
            error_log("Wishlist DB Error: " . $e->getMessage());
            return false;
        }
    }

    return true;
}

/**
 * Load wishlist from DB when user logs in
 */
function load_wishlist_from_db(string $memberID): bool
{
    global $_db;

    try {
        $stmt = $_db->prepare("SELECT productID FROM member_wishlist WHERE memberID = ?");
        $stmt->execute([$memberID]);

        $_SESSION['wishlist'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['wishlist'][] = $row['productID'];
        }
        return true;
    } catch (PDOException $e) {
        error_log("Wishlist Load Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get wishlist items with product details
 */
function get_wishlist(): array
{
    global $_db;

    $wishlist = [];

    if (isset($_SESSION['wishlist']) && !empty($_SESSION['wishlist'])) {
        // Prepare statements for better performance
        $productStmt = $_db->prepare("
            SELECT p.productID, p.productName, p.price, g.imageName 
            FROM product p
            LEFT JOIN gallery g ON p.productID = g.productID AND g.is_cover = 1
            WHERE p.productID = ?
        ");

        foreach ($_SESSION['wishlist'] as $productID) {
            try {
                $productStmt->execute([$productID]);
                $product = $productStmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $imageURL = "/images/placeholder.png";
                    if (!empty($product['imageName'])) {
                        $imagePath = "../productImage/" . trim($product['imageName']);
                        if (file_exists($imagePath)) {
                            $imageURL = str_replace('../', '/', $imagePath);
                        }
                    }

                    $wishlist[] = [
                        'id'    => $product['productID'],
                        'name'  => htmlspecialchars($product['productName']),
                        'price' => (float)$product['price'],
                        'image' => htmlspecialchars($imageURL)
                    ];
                }
            } catch (PDOException $e) {
                error_log("Wishlist Product Error: " . $e->getMessage());
                continue;
            }
        }
    }

    return $wishlist;
}

/**
 * Sync session wishlist to database
 */
function sync_wishlist_to_db(int $memberID): bool
{
    global $_db;

    if (!isset($_SESSION['wishlist'])) {
        return true;
    }

    try {
        $_db->beginTransaction();

        // First clear existing wishlist items
        $_db->prepare("DELETE FROM member_wishlist WHERE memberID = ?")
            ->execute([$memberID]);

        // Insert current wishlist items
        if (!empty($_SESSION['wishlist'])) {
            $stmt = $_db->prepare("INSERT INTO member_wishlist (memberID, productID) VALUES (?, ?)");
            foreach ($_SESSION['wishlist'] as $productID) {
                $stmt->execute([$memberID, $productID]);
            }
        }

        $_db->commit();
        return true;
    } catch (PDOException $e) {
        $_db->rollBack();
        error_log("Wishlist Sync Error: " . $e->getMessage());
        return false;
    }
}

// ============================================================================
// Email Function
// ============================================================================

function get_mail()
{
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'kawaii.plushiesweb@gmail.com';
    $m->Password = 'snmd djos yhgb csxw'; // app password
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, '🎀 KAWAII');

    return $m;
}


// ============================================================================
// Global Constants and Variables
// ============================================================================

// category array
$_categoryType = [
    'Animals' => 'Animals',
    'Best for Gift' => 'Best for Gift',
    'Flowers' => 'Flowers',
    'Food & Drinks' => 'Food & Drinks',
    'Ocean & Sea Life' => 'Ocean & Sea Life',
];


// Add this near the top of _base.php with other utility functions
function handle_db_error($e, $message = 'Database error') {
    global $_user;
    error_log($message . ': ' . $e->getMessage());
    temp('error', 'A database error occurred. Please try again later.');
    if ($_user->role === ROLE_ADMIN) {
        temp('error_details', $e->getMessage());
    }
    return false;
}

function handle_exception($e, $message = 'An error occurred') {
    global $_user;
    error_log($message . ': ' . $e->getMessage());
    temp('error', $message);
    if ($_user->role === ROLE_ADMIN) {
        temp('error_details', $e->getMessage());
    }
    return false;
}

?>