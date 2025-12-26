<?php
require_once 'Income.php';
require_once 'Expense.php';

class Dashboard {
    private $income;
    private $expense;
    private $userId;
    
    public function __construct($userId) {
        $this->income = new Income();
        $this->expense = new Expense();
        $this->userId = $userId;
    }

    public function getBalance() {
        $totalIncome = $this->income->getTotal($this->userId);
        $totalExpense = $this->expense->getTotal($this->userId);
        return $totalIncome - $totalExpense;
    }

    public function getCurrentMonthStats() {
        $month = date('m');
        $year = date('Y');
        
        return [
            'income' => $this->income->getTotal($this->userId, $month, $year),
            'expense' => $this->expense->getTotal($this->userId, $month, $year),
            'balance' => $this->income->getTotal($this->userId, $month, $year) - $this->expense->getTotal($this->userId, $month, $year)
        ];
    }
    
    public function getRecentTransactions($limit = 5) {
        $recentIncomes = $this->income->getAll($this->userId, $limit);
        $recentExpenses = $this->expense->getAll($this->userId, $limit);
        
        $transactions = [];
        
        foreach ($recentIncomes as $income) {
            $transactions[] = [
                'type' => 'income',
                'date' => $income['dateIn'],
                'description' => $income['descriptionIn'],
                'amount' => $income['amountIn'],
                'category' => $income['category_name'] ?? 'Uncategorized'
            ];
        }
        
        foreach ($recentExpenses as $expense) {
            $transactions[] = [
                'type' => 'expense',
                'date' => $expense['dateEx'],
                'description' => $expense['descriptionEx'],
                'amount' => $expense['amountEx'],
                'category' => $expense['category_name'] ?? 'Uncategorized'
            ];
        }

        usort($transactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($transactions, 0, $limit);
    }

    public function getChartData($year = null) {
        $year = $year ?? date('Y');
        
        $monthlyIncomes = $this->income->getMonthlyTotal($this->userId, $year);
        $monthlyExpenses = $this->expense->getMonthlyTotal($this->userId, $year);

        $data = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'income' => array_fill(0, 12, 0),
            'expense' => array_fill(0, 12, 0)
        ];
        
        foreach ($monthlyIncomes as $item) {
            $data['income'][$item['month'] - 1] = floatval($item['total']);
        }
        
        foreach ($monthlyExpenses as $item) {
            $data['expense'][$item['month'] - 1] = floatval($item['total']);
        }
        
        return $data;
    }

    public function getExpensesByCategory($month = null, $year = null) {
        return $this->expense->getCategoryTotal($this->userId, $month, $year);
    }
}