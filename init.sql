-- DROP DATABASE cs340_jonesow;
-- CREATE DATABASE cs340_jonesow;

DROP TRIGGER IF EXISTS cs340_jonesow.AddToInventory;
DROP TRIGGER IF EXISTS cs340_jonesow.OrderPlacedAsFulfilled;
DROP TRIGGER IF EXISTS cs340_jonesow.OrderFulfilled;
DROP FUNCTION IF EXISTS cs340_jonesow.GetMostPopular;
DROP TABLE IF EXISTS cs340_jonesow.ProductIngredient;
DROP TABLE IF EXISTS cs340_jonesow.Batch;
DROP TABLE IF EXISTS cs340_jonesow.ProductOrder;
DROP TABLE IF EXISTS cs340_jonesow.Product;
DROP TABLE IF EXISTS cs340_jonesow.Ingredient;
DROP TABLE IF EXISTS cs340_jonesow.Staff;
DROP TABLE IF EXISTS cs340_jonesow.Distributor;


CREATE TABLE cs340_jonesow.Staff (
	staffId INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone DECIMAL(10) NOT NULL,
    PRIMARY KEY (staffId)
);

CREATE TABLE cs340_jonesow.Distributor (
	distributorId INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone DECIMAL(10) NOT NULL,
    address VARCHAR(255) NOT NULL,
    PRIMARY KEY (distributorId)
);

CREATE TABLE cs340_jonesow.Product (
	productId INT NOT NULL AUTO_INCREMENT,
    type VARCHAR(255) NOT NULL,
    flavor VARCHAR(255) NOT NULL,
    weight INT NOT NULL,
    inventory INT NOT NULL DEFAULT 0,
    createdBy INT,
    PRIMARY KEY (productId),
    FOREIGN KEY (createdBy)
		REFERENCES cs340_jonesow.Staff(staffId)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE cs340_jonesow.ProductOrder (
	orderId INT NOT NULL AUTO_INCREMENT,
    orderDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    quantity INT NOT NULL,
    fulfilledDate DATETIME,
    distributorId INT,
    productId INT,
    PRIMARY KEY (orderId),
    FOREIGN KEY (distributorId)
		REFERENCES cs340_jonesow.Distributor(distributorId)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (productId)
		REFERENCES cs340_jonesow.Product(productId)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE cs340_jonesow.Ingredient (
	ingredientId INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (ingredientId)
);

CREATE TABLE cs340_jonesow.ProductIngredient (
	productId INT NOT NULL,
    ingredientId INT NOT NULL,
    PRIMARY KEY (productId, ingredientId),
    FOREIGN KEY (productId)
		REFERENCES cs340_jonesow.Product(productId)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
	FOREIGN KEY (ingredientId)
		REFERENCES cs340_jonesow.Ingredient(ingredientId)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE cs340_jonesow.Batch (
	batchId INT NOT NULL AUTO_INCREMENT,
    quantity INT NOT NULL DEFAULT 0,
    batchDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	productId INT,
    PRIMARY KEY (batchId),
    FOREIGN KEY (productId)
		REFERENCES cs340_jonesow.Product(productId)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

DELIMITER $$
CREATE TRIGGER cs340_jonesow.AddToInventory
AFTER INSERT ON cs340_jonesow.Batch FOR EACH ROW
BEGIN
	UPDATE cs340_jonesow.Product SET inventory = inventory + NEW.quantity WHERE productId = NEW.productId;
END$$

CREATE TRIGGER cs340_jonesow.OrderPlacedAsFulfilled
AFTER INSERT ON cs340_jonesow.ProductOrder FOR EACH ROW
BEGIN
	IF NEW.fulfilledDate IS NOT NULL THEN
		UPDATE cs340_jonesow.Product SET inventory = inventory - NEW.quantity WHERE productId = NEW.productId;
    END IF;
END$$

CREATE TRIGGER cs340_jonesow.OrderFulfilled
AFTER UPDATE ON cs340_jonesow.ProductOrder FOR EACH ROW
BEGIN
	IF OLD.fulfilledDate IS NULL AND NEW.fulfilledDate IS NOT NULL THEN
		UPDATE cs340_jonesow.Product SET inventory = inventory - NEW.quantity WHERE productId = NEW.productId;
    END IF;
END$$

CREATE FUNCTION cs340_jonesow.GetMostPopular()
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
	DECLARE mostPopular INT;
    SELECT p.productId
		INTO mostPopular
		FROM cs340_jonesow.Product AS P
        JOIN (
			SELECT productId, SUM(quantity) AS total_quantity
				FROM cs340_jonesow.ProductOrder
                GROUP BY productId
                ORDER BY total_quantity DESC
                LIMIT 1
		) AS q ON p.productId = q.productId;
	RETURN mostPopular;
END$$

CREATE FUNCTION cs340_jonesow.GetMostPopularProductByDistributor(dId INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE mostPopularProduct INT;

    SELECT productId
    INTO mostPopularProduct
    FROM cs340_jonesow.ProductOrder
    WHERE distributorId = dId
    GROUP BY productId
    ORDER BY SUM(quantity) DESC
    LIMIT 1;

    RETURN mostPopularProduct;
END$$
DELIMITER ;