$dsn = 'mysql:host=localhost;dbname=test2';
$username = 'root';
$password = 'ujcgjlbgjvbkeq';

$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



// >>> 1. Получение фильтров, опций и количество товаров, которые привязаны к каждой опции!


$sql = "
SELECT 
    `shop_filters`.`name` AS `attribute_name`,
    GROUP_CONCAT(`attribute_values`.`name` ORDER BY `attribute_values`.`id` ASC) AS `value`,
    GROUP_CONCAT(`attribute_values`.`num_key` ORDER BY `attribute_values`.`id` ASC) AS `num_id`,
    GROUP_CONCAT(`attribute_values`.`product_count` ORDER BY `attribute_values`.`id` ASC) AS `product_count`
FROM (
    SELECT 
        `shop_filters_params`.`parent_id` AS `filter_id`,
        `shop_filters_params`.`name`,
        `shop_filters_params`.`num_key`,
        `shop_filters_params`.`id`,
        (
            SELECT COUNT(`shop_filters_products`.`product_id`)
            FROM `shop_filters_products`
            WHERE `shop_filters_products`.`filter_id` = `shop_filters_params`.`num_key`
        ) AS `product_count`
    FROM `shop_filters_params`
    WHERE 
        `shop_filters_params`.`lang` = '1'
    GROUP BY 
        `shop_filters_params`.`parent_id`, 
        `shop_filters_params`.`name`, 
        `shop_filters_params`.`num_key`, 
        `shop_filters_params`.`id`
) AS `attribute_values`
JOIN `shop_filters` ON `attribute_values`.`filter_id` = `shop_filters`.`num_id`
WHERE 
    `shop_filters`.`cat_id` = '1' AND 
    `shop_filters`.`lang` = '1'
GROUP BY 
    `shop_filters`.`num_id`,  
    `shop_filters`.`name`;
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$filters = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($filters);

// >>> Результат: <<<

/*
Array
(
    [0] => Array
        (
            [num_id] => 1
            [name] => Цвет
            [values] => синий,красный,зеленый,белый
            [num_key] => 1,2,3,4
            [counts] => 3,1,2,2
        )

    [1] => Array
        (
            [num_id] => 2
            [name] => Размер
            [values] => S,M
            [num_key] => 5,6
            [counts] => 5,3
        )

    [2] => Array
        (
            [num_id] => 3
            [name] => Посадка
            [values] => низкая,средняя,высокая
            [num_key] => 7,8,9
            [counts] => 4,3,1
        )
)
*/



// >>> 2. Получение фильтров, опций если человек выбрал 2 опции: Размер: S, Цвет синий



$sql = "
SELECT 
    `shop_filters`.`name` AS `attribute_name`,
    GROUP_CONCAT(`attribute_values`.`name` ORDER BY `attribute_values`.`id` ASC) AS `value`,
    GROUP_CONCAT(`attribute_values`.`num_key` ORDER BY `attribute_values`.`id` ASC) AS `num_id`,
    GROUP_CONCAT(`attribute_values`.`product_count` ORDER BY `attribute_values`.`id` ASC) AS `product_count`
FROM (
    SELECT 
        `shop_filters_params`.`parent_id` AS `filter_id`,
        `shop_filters_params`.`name`,
        `shop_filters_params`.`num_key`,
        `shop_filters_params`.`id`,
        COUNT(`shop_filters_products`.`product_id`) AS `product_count`
    FROM `shop_filters_params`
    INNER JOIN `shop_filters_products` ON `shop_filters_params`.`num_key` = `shop_filters_products`.`filter_id`
    WHERE 
        `shop_filters_params`.`lang` = '1'
        AND `shop_filters_products`.`product_id` IN (
            SELECT `shop_filters_products`.`product_id`
            FROM `shop_filters_products`
            WHERE `shop_filters_products`.`filter_id` IN (5, 8)
            GROUP BY `shop_filters_products`.`product_id`
            HAVING COUNT(DISTINCT `shop_filters_products`.`filter_id`) = 2
        )
    GROUP BY 
        `shop_filters_params`.`parent_id`, 
        `shop_filters_params`.`name`, 
        `shop_filters_params`.`num_key`, 
        `shop_filters_params`.`id`
) AS `attribute_values`
JOIN `shop_filters` ON `attribute_values`.`filter_id` = `shop_filters`.`num_id`
WHERE 
    `shop_filters`.`cat_id` = '1' AND 
    `shop_filters`.`lang` = '1'
GROUP BY 
    `shop_filters`.`num_id`,  
    `shop_filters`.`name`
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$filters = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($filters);


// >>> Результат: <<<
/*
Array
(
    [0] => Array
        (
            [attribute_name] => Цвет
            [value] => синий
            [num_id] => 1
            [product_count] => 2
        )

    [1] => Array
        (
            [attribute_name] => Размер
            [value] => S
            [num_id] => 5
            [product_count] => 2
        )

    [2] => Array
        (
            [attribute_name] => Посадка
            [value] => средняя
            [num_id] => 8
            [product_count] => 2
        )
)
*/


//>>> 3. Получение товаров, который соответствуют выбранным опциям фильтра! <<<



$sql = "
SELECT * FROM `shop_products` WHERE 
`id` IN (
    SELECT `product_id` FROM `shop_filters_products` WHERE `filter_id` IN ('5', '8')
    GROUP BY `product_id`
    HAVING COUNT(DISTINCT `filter_id`) = 2
)
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$filters = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($filters);

/*
>>> Результат:

Array
(
    [0] => Array
        (
            [id] => 6
            [price] => 60.00
            [cat] => 1
        )

    [1] => Array
        (
            [id] => 7
            [price] => 65.00
            [cat] => 1
        )
)
*/
