DROP DATABASE chocolate_factory;
CREATE DATABASE chocolate_factory;

CREATE TABLE chocolate_factory.Staff (
	staffId INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone INT NOT NULL,
    PRIMARY KEY (staffId)
);

CREATE TABLE chocolate_factory.Distributor (
	distributorId INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    PRIMARY KEY (distributorId)
);

CREATE TABLE chocolate_factory.Product (
	productId INT NOT NULL AUTO_INCREMENT,
    type VARCHAR(255) NOT NULL,
    flavor VARCHAR(255) NOT NULL,
    weight INT NOT NULL,
    inventory INT NOT NULL DEFAULT 0,
    createdBy INT,
    PRIMARY KEY (productId),
    FOREIGN KEY (createdBy)
		REFERENCES chocolate_factory.Staff(staffId)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE chocolate_factory.ProductOrder (
	orderId INT NOT NULL AUTO_INCREMENT,
    orderDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    quantity INT NOT NULL,
    furfilledDate DATE,
    distributorId INT,
    productId INT,
    PRIMARY KEY (orderId),
    FOREIGN KEY (distributorId)
		REFERENCES chocolate_factory.Distributor(distributorId)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (productId)
		REFERENCES chocolate_factory.Product(productId)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE chocolate_factory.Ingredient (
	ingredientId INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (ingredientId)
);

CREATE TABLE chocolate_factory.ProductIngredient (
	productId INT NOT NULL,
    ingredientId INT NOT NULL,
    PRIMARY KEY (productId, ingredientId),
    FOREIGN KEY (productId)
		REFERENCES chocolate_factory.Product(productId)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
	FOREIGN KEY (ingredientId)
		REFERENCES chocolate_factory.Ingredient(ingredientId)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE chocolate_factory.Batch (
	batchId INT NOT NULL AUTO_INCREMENT,
    quantity INT NOT NULL DEFAULT 0,
    batchDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	productId INT,
    PRIMARY KEY (batchId),
    FOREIGN KEY (productId)
		REFERENCES chocolate_factory.Product(productId)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

DELIMITER $$
CREATE TRIGGER chocolate_factory.AddToInventory
AFTER INSERT ON chocolate_factory.Batch FOR EACH ROW
BEGIN
	UPDATE chocolate_factory.Product SET inventory = inventory + NEW.quantity WHERE productId = NEW.productId;
END$$

CREATE TRIGGER chocolate_factory.OrderPlacedAsFurfilled
AFTER INSERT ON chocolate_factory.ProductOrder FOR EACH ROW
BEGIN
	IF NEW.furfilledDate IS NOT NULL THEN
		UPDATE chocolate_factory.Product SET inventory = inventory - NEW.quantity WHERE productId = NEW.productId;
    END IF;
END$$

CREATE TRIGGER chocolate_factory.OrderFurfilled
AFTER UPDATE ON chocolate_factory.ProductOrder FOR EACH ROW
BEGIN
	IF OLD.furfilledDate IS NULL AND NEW.furfilledDate IS NOT NULL THEN
		UPDATE chocolate_factory.Product SET inventory = inventory - NEW.quantity WHERE productId = NEW.productId;
    END IF;
END$$

CREATE FUNCTION chocolate_factory.GetMostPopular()
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
	DECLARE mostPopular INT;
    SELECT p.productId
		INTO mostPopular
		FROM chocolate_factory.Product AS P
        JOIN (
			SELECT productId, SUM(quantity) AS total_quantity
				FROM chocolate_factory.ProductOrder
                GROUP BY productId
                ORDER BY total_quantity DESC
                LIMIT 1
		) AS q ON p.productId = q.productId;
	RETURN mostPopular;
END$$
DELIMITER ;