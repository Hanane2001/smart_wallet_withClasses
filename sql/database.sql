-- Create users table
CREATE TABLE IF NOT EXISTS users(
    idUser INT PRIMARY KEY AUTO_INCREMENT,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create incomes table
CREATE TABLE IF NOT EXISTS incomes(
    idIn INT PRIMARY KEY AUTO_INCREMENT,
    amountIn DECIMAL(10,2) NOT NULL,
    dateIn DATE NOT NULL,
    descriptionIn VARCHAR(250) DEFAULT 'Unknown',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create expenses table
CREATE TABLE IF NOT EXISTS expenses (
    idEx INT PRIMARY KEY AUTO_INCREMENT,
    amountEx DECIMAL(10,2) NOT NULL,
    dateEx DATE NOT NULL,
    descriptionEx VARCHAR(250) DEFAULT 'Unknown',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- CREATE TABLE category (
--     idCat INT PRIMARY KEY AUTO_INCREMENT,
--     nameCat VARCHAR(50) NOT NULL UNIQUE
-- );

-- ALTER TABLE expenses
-- ADD idCat INT,
-- ADD FOREIGN KEY (idCat) REFERENCES category(idCat);


-- insert into table category(namecat) values ("food");
-- insert into table category(namecat) values ("shopping");
-- delete from category where idEx = 1;

-- select e.* from expenses e
-- join category c
-- on e.idCat = c.idCat
-- where namecat = "food";


