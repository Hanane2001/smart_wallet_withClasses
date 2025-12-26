<?php
require_once '../Classes/Income.php';
require_once '../Classes/Category.php';
require_once '../auth/AuthCheck.php';

$userId = checkAuth();
$income = new Income();
$category = new Category();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list.php?error=no_id");
    exit();
}

$incomeData = $income->getById($id, $userId);
if (!$incomeData) {
    header("Location: list.php?error=not_found");
    exit();
}

$incomeCategories = $category->getByType('income', $userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = $income->update($id,$_POST['amountIn'],$_POST['dateIn'],$_POST['descriptionIn'],$_POST['category_id'] ?? null,$userId);
    if ($success) {
        header("Location: list.php?message=income_updated");
        exit();
    } else {
        header("Location: edit.php?id=$id&error=update_failed");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Income - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <nav class="bg-blue-600 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-wallet text-white text-2xl"></i>
                    <span class="text-white text-xl font-bold">SmartBudget</span>
                </div>
                <div id="navLinks" class="hidden md:flex space-x-6">
                    <a href="../dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                    <a href="list.php" class="text-white font-bold">Incomes</a>
                    <a href="../expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                    <a href="../auth/logout.php" class="text-white hover:text-blue-200">Logout</a>
                </div>
                <button id="menu_tougle" class="md:hidden text-white"><i class="fas fa-bars text-2xl"></i></button>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="list.php" class="text-blue-500 hover:text-blue-600"><i class="fas fa-arrow-left mr-2"></i>Back to Incomes</a>
            </div>

            <!-- Afficher les erreurs -->
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <p>
                        <?php 
                        $errors = [
                            'update_failed' => 'Failed to update income. Please try again.',
                            'not_found' => 'Income not found.',
                            'no_id' => 'No income ID provided.'
                        ];
                        echo $errors[$_GET['error']] ?? 'An error occurred!';
                        ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow p-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Income</h1>
                
                <form method="POST">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-gray-700 mb-2">Amount ($)</label>
                            <input type="number" step="0.01" name="amountIn" required value="<?php echo htmlspecialchars($incomeData['amountIn']); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Date</label>
                            <input type="date" name="dateIn" required value="<?php echo htmlspecialchars($incomeData['dateIn']); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Category</label>
                            <select name="category_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Category</option>
                                <?php foreach ($incomeCategories as $cat): ?>
                                    <option value="<?php echo $cat['idCat']; ?>" 
                                        <?php echo ($incomeData['category_id'] == $cat['idCat']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nameCat']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Description</label>
                            <input type="text" name="descriptionIn" value="<?php echo htmlspecialchars($incomeData['descriptionIn']); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition">Update Income</button>
                            <a href="list.php" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script src="../assets/js/main.js"></script>
</body>
</html>