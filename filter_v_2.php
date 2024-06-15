<?php

//Михаил, доработал Ваш запрос:

$sql = "
SELECT 
	`shop_filters`.`name` AS `filter_name`,
	`shop_filters_params`.`parent_id` AS `filter_id`,
	`shop_filters_params`.`name`,
	`shop_filters_params`.`num_key`,
	`shop_filters_params`.`id`,
	`shop_filters_products`.`product_count`
FROM `shop_filters_params` 
INNER JOIN `shop_filters` ON `shop_filters`.`num_id` = `shop_filters_params`.`parent_id`
LEFT JOIN (
	SELECT 
		`shop_filters_products`.`filter_id`,
		COUNT(`shop_filters_products`.`product_id`) AS product_count
	FROM `shop_filters_products`
	GROUP BY `shop_filters_products`.`filter_id`

) AS `shop_filters_products` ON `shop_filters_products`.`filter_id` = `shop_filters_params`.`num_key`
WHERE 
	`shop_filters`.`lang` = '1' AND 
	`shop_filters_params`.`lang` = '1'
";


// Результат:

Array
(
    [0] => Array
        (
            [filter_name] => Цвет
            [filter_id] => 1
            [name] => красный
            [num_key] => 1
            [id] => 1
            [product_count] => 1
        )

    [1] => Array
        (
            [filter_name] => Цвет
            [filter_id] => 1
            [name] => фиолетовый
            [num_key] => 2
            [id] => 3
            [product_count] => 1
        )

    [2] => Array
        (
            [filter_name] => Цвет
            [filter_id] => 1
            [name] => зеленый
            [num_key] => 3
            [id] => 5
            [product_count] => 1
        )

    [3] => Array
        (
            [filter_name] => Цвет
            [filter_id] => 1
            [name] => Синий
            [num_key] => 4
            [id] => 7
            [product_count] => 1
        )

    [4] => Array
        (
            [filter_name] => Цвет
            [filter_id] => 1
            [name] => светло-серый
            [num_key] => 5
            [id] => 9
            [product_count] => 1
        )

    [5] => Array
        (
            [filter_name] => Цвет
            [filter_id] => 1
            [name] => натуральный
            [num_key] => 6
            [id] => 11
            [product_count] => 1
        )

    [6] => Array
        (
            [filter_name] => Форма корпуса
            [filter_id] => 2
            [name] => Jumbo
            [num_key] => 7
            [id] => 13
            [product_count] => 3
        )

    [7] => Array
        (
            [filter_name] => Форма корпуса
            [filter_id] => 2
            [name] => Dreadnought
            [num_key] => 8
            [id] => 15
            [product_count] => 3
        )

    [8] => Array
        (
            [filter_name] => Страна-производитель
            [filter_id] => 3
            [name] => Китай
            [num_key] => 9
            [id] => 17
            [product_count] => 2
        )

    [9] => Array
        (
            [filter_name] => Страна-производитель
            [filter_id] => 3
            [name] => VTS
            [num_key] => 10
            [id] => 19
            [product_count] => 1
        )

    [10] => Array
        (
            [filter_name] => Страна-производитель
            [filter_id] => 3
            [name] => Bandes
            [num_key] => 11
            [id] => 21
            [product_count] => 2
        )

    [11] => Array
        (
            [filter_name] => Страна-производитель
            [filter_id] => 3
            [name] => Индонезия
            [num_key] => 12
            [id] => 23
            [product_count] => 1
        )

    [12] => Array
        (
            [filter_name] => Материал верхней деки
            [filter_id] => 4
            [name] => Ель
            [num_key] => 13
            [id] => 25
            [product_count] => 2
        )

    [13] => Array
        (
            [filter_name] => Материал верхней деки
            [filter_id] => 4
            [name] => Липа
            [num_key] => 14
            [id] => 27
            [product_count] => 4
        )

)


//--------------------------------------------------------------------


//Доработал свой запрос, упростил:

$sql = "
SELECT 
    `shop_filters`.`name` AS `attribute_name`,
    `shop_filters`.`sort`, 
    GROUP_CONCAT(`shop_filters_params`.`name` ORDER BY `shop_filters_params`.`id` ASC) AS `value`,
    GROUP_CONCAT(`shop_filters_params`.`num_key` ORDER BY `shop_filters_params`.`id` ASC) AS `num_id`,
    GROUP_CONCAT(`shop_filters_products_count`.`product_count` ORDER BY `shop_filters_params`.`id` ASC) AS `product_count`
FROM 
    `shop_filters`
JOIN 
    `shop_filters_params` ON `shop_filters`.`num_id` = `shop_filters_params`.`parent_id`
LEFT JOIN (
    SELECT 
        `shop_filters_products`.`filter_id`,
        COUNT(`shop_filters_products`.`product_id`) AS `product_count`
    FROM 
        `shop_filters_products`
    GROUP BY 
        `shop_filters_products`.`filter_id`
) AS `shop_filters_products_count` ON `shop_filters_params`.`num_key` = `shop_filters_products_count`.`filter_id`
WHERE 
    `shop_filters`.`cat_id` = '1' AND 
    `shop_filters`.`lang` = '1' AND
    `shop_filters_params`.`lang` = '1'
GROUP BY 
    `shop_filters`.`num_id`,  
    `shop_filters`.`name`, 
    `shop_filters`.`sort`
ORDER BY 
    `shop_filters`.`sort`
";


//Результат:

Array
(
    [0] => Array
        (
            [attribute_name] => Цвет
            [sort] => 1
            [value] => красный,фиолетовый,зеленый,Синий,светло-серый,натуральный
            [num_id] => 1,2,3,4,5,6
            [product_count] => 1,1,1,1,1,1
        )

    [1] => Array
        (
            [attribute_name] => Форма корпуса
            [sort] => 2
            [value] => Jumbo,Dreadnought
            [num_id] => 7,8
            [product_count] => 3,3
        )

    [2] => Array
        (
            [attribute_name] => Страна-производитель
            [sort] => 3
            [value] => Китай,VTS,Bandes,Индонезия
            [num_id] => 9,10,11,12
            [product_count] => 2,1,2,1
        )

    [3] => Array
        (
            [attribute_name] => Материал верхней деки
            [sort] => 4
            [value] => Ель,Липа
            [num_id] => 13,14
            [product_count] => 2,4
        )
)




// Мне второй массив больше нравится. Он удобный. А в первом массиве результатов, надо еще циклом проходиться , по каждому ел, формируя новый массив, чтобы удобно его подставить в верстке.
