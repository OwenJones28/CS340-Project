-- Staff
INSERT INTO chocolate_factory.Staff (name, role, email, phone) VALUES
('Kush Patel', 'Manager', 'kush.patel@chocofactory.com', 5551001001),
('Téa Kidder', 'Worker', 'téa.kidder@chocofactory.com', 5551001002),
('Julia Feldhousen', 'Worker', 'julia.feldhousen@chocofactory.com', 5551001003),
('Owen Jones', 'Supplier', 'Owen.Jones@chocofactory.com', 5551001004),
('Eve Patel', 'Worker', 'eve.patel@chocofactory.com', 5551001005),
('Frank Wu', 'Manager', 'frank.wu@chocofactory.com', 5551001006),
('Grace Lin', 'Worker', 'grace.lin@chocofactory.com', 5551001007),
('Henry Ford', 'Worker', 'henry.ford@chocofactory.com', 5551001008),
('Ivy Chen', 'Worker', 'ivy.chen@chocofactory.com', 5551001009),
('Jack Brown', 'Worker', 'jack.brown@chocofactory.com', 5551001010);

-- Distributor
INSERT INTO chocolate_factory.Distributor (name, email, phone, address) VALUES
('Sweet Treats LLC', 'orders@sweettreats.com', 5552001001, '123 Candy Lane'),
('ChocoWholesale', 'contact@chocowholesale.com', 5552001002, '456 Cocoa Blvd'),
('Dessert Depot', 'sales@dessertdepot.com', 5552001003, '789 Sugar Ave'),
('Candy Corner', 'info@candycorner.com', 5552001004, '321 Lollipop St'),
('Sugar Rush', 'hello@sugarrush.com', 5552001005, '654 Sweet Rd'),
('Treat Time', 'order@treattime.com', 5552001006, '987 Fudge Ave'),
('ChocoMart', 'sales@chocomart.com', 5552001007, '246 Truffle Blvd'),
('Delightful Distributors', 'contact@delightful.com', 5552001008, '135 Bonbon Dr'),
('Gourmet Goods', 'orders@gourmetgoods.com', 5552001009, '864 Praline Pl'),
('Bulk Choco', 'bulk@choco.com', 5552001010, '753 Ganache Ct');

-- Ingredient
INSERT INTO chocolate_factory.Ingredient (name) VALUES
('Cocoa Beans'),
('Sugar'),
('Milk Powder'),
('Vanilla Extract'),
('Almonds'),
('Hazelnuts'),
('Coconut'),
('Caramel'),
('Sea Salt'),
('Peanut Butter');

-- Product
INSERT INTO chocolate_factory.Product (type, flavor, weight, inventory, createdBy) VALUES
('Bar', 'Milk Chocolate', 100, 0, 1),
('Bar', 'Dark Chocolate', 100, 0, 2),
('Truffle', 'Hazelnut', 50, 0, 3),
('Bar', 'Almond', 100, 0, 4),
('Bar', 'Coconut', 100, 0, 5),
('Bar', 'Caramel', 100, 0, 6),
('Bar', 'Sea Salt', 100, 0, 7),
('Bar', 'Peanut Butter', 100, 0, 8),
('Truffle', 'Almond', 50, 0, 9),
('Bar', 'White Chocolate', 100, 0, 10);

-- ProductIngredient
INSERT INTO chocolate_factory.ProductIngredient (productId, ingredientId) VALUES
(1, 1), (1, 2), (1, 3), (1, 4),         -- Milk Chocolate Bar
(2, 1), (2, 2), (2, 4),                 -- Dark Chocolate Bar
(3, 1), (3, 2), (3, 3), (3, 6),         -- Hazelnut Truffle
(4, 1), (4, 2), (4, 3), (4, 5),         -- Almond Bar
(5, 1), (5, 2), (5, 3), (5, 7),         -- Coconut Bar
(6, 1), (6, 2), (6, 3), (6, 8),         -- Caramel Bar
(7, 1), (7, 2), (7, 3), (7, 9),         -- Sea Salt Bar
(8, 1), (8, 2), (8, 3), (8, 10),        -- Peanut Butter Bar
(9, 1), (9, 2), (9, 3), (9, 5),         -- Almond Truffle
(10, 1), (10, 2), (10, 3), (10, 4);     -- White Chocolate Bar

-- Batch
INSERT INTO chocolate_factory.Batch (quantity, batchDate, productId) VALUES
(500, '2024-06-01 08:00:00', 1),
(300, '2024-06-02 09:00:00', 2),
(200, '2024-06-03 10:00:00', 3),
(400, '2024-06-04 11:00:00', 4),
(350, '2024-06-05 12:00:00', 5),
(250, '2024-06-06 13:00:00', 6),
(450, '2024-06-07 14:00:00', 7),
(375, '2024-06-08 15:00:00', 8),
(225, '2024-06-09 16:00:00', 9),
(600, '2024-06-10 17:00:00', 10);

-- ProductOrder
INSERT INTO chocolate_factory.ProductOrder (orderDate, quantity, furfilledDate, distributorId, productId) VALUES
('2024-06-11 10:00:00', 100, '2024-06-12', 1, 1),
('2024-06-12 11:00:00', 50, NULL, 2, 2),
('2024-06-13 12:00:00', 75, '2024-06-14', 3, 3),
('2024-06-14 13:00:00', 120, NULL, 4, 4),
('2024-06-15 14:00:00', 90, '2024-06-16', 5, 5),
('2024-06-16 15:00:00', 60, NULL, 6, 6),
('2024-06-17 16:00:00', 110, '2024-06-18', 7, 7),
('2024-06-18 17:00:00', 80, NULL, 8, 8),
('2024-06-19 18:00:00', 130, '2024-06-20', 9, 9),
('2024-06-20 19:00:00', 95, NULL, 10, 10);