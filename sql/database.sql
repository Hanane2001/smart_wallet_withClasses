-- Creation de la base de donnees
CREATE DATABASE IF NOT EXISTS Smart_Wallet_Classe;
USE Smart_Wallet_Classe;

DROP TABLE users;
DROP TABLE incomes;
DROP TABLE expenses;
DROP TABLE categories;
-- Table users
CREATE TABLE IF NOT EXISTS users(
    idUser INT PRIMARY KEY AUTO_INCREMENT,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table categories
CREATE TABLE IF NOT EXISTS categories(
    idCat INT PRIMARY KEY AUTO_INCREMENT,
    nameCat VARCHAR(50) NOT NULL,
    typeCat ENUM('income', 'expense') NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(idUser) ON DELETE SET NULL
);

-- Table incomes
CREATE TABLE IF NOT EXISTS incomes(
    idIn INT PRIMARY KEY AUTO_INCREMENT,
    amountIn DECIMAL(10,2) NOT NULL,
    dateIn DATE NOT NULL,
    descriptionIn VARCHAR(250) DEFAULT 'Unknown',
    user_id INT NOT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(idUser) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(idCat) ON DELETE SET NULL
);

-- Table expenses
CREATE TABLE IF NOT EXISTS expenses(
    idEx INT PRIMARY KEY AUTO_INCREMENT,
    amountEx DECIMAL(10,2) NOT NULL,
    dateEx DATE NOT NULL,
    descriptionEx VARCHAR(250) DEFAULT 'Unknown',
    user_id INT NOT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(idUser) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(idCat) ON DELETE SET NULL
);

-- Insertion de catégories par défaut
INSERT INTO categories (nameCat, typeCat) VALUES
('Salary', 'income'),
('Freelance', 'income'),
('Investment', 'income'),
('Gift', 'income'),
('Other Income', 'income'),
('Food', 'expense'),
('Transport', 'expense'),
('Housing', 'expense'),
('Entertainment', 'expense'),
('Healthcare', 'expense'),
('Education', 'expense'),
('Shopping', 'expense'),
('Other Expense', 'expense');