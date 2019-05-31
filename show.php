<?php

$servername = "localhost";
$username = "root";
$password = "mysql";
$db_name = "hakki_ozturk";

// Try to connect to localhost
$connection = mysqli_connect($servername, $username, $password)
	or die("Couldnt connect localhost! " . mysqli_error());

// Select database to work on
mysqli_select_db($connection, $db_name);
mysqli_set_charset($connection, 'utf8mb4');

if (isset($_REQUEST['select']))
{
	$select = $_REQUEST['select'];
	if ($select == "districts")
	{
		$sql_query = "SELECT district_name FROM districts";
		$result = mysqli_query($connection, $sql_query);

		$row = mysqli_fetch_row($result);
		$options = "$row[0]";
		while (($row = mysqli_fetch_row($result)))
		{
			$options .= ",$row[0]";
		}

		echo $options;
	}
	elseif ($select == "cities")
	{
		$district = urldecode($_REQUEST['district']);
		$sql_query = "SELECT city_name FROM cities WHERE district_id=(SELECT district_id FROM districts WHERE district_name='$district')";
		$result = mysqli_query($connection, $sql_query);

		$row = mysqli_fetch_row($result);
		$options = "$row[0]";
		while (($row = mysqli_fetch_row($result)))
		{
			$options .= ",$row[0]";
		}

		echo $options;
	}
	elseif ($select == "markets")
	{
		$sql_query = "SELECT DISTINCT market_name FROM markets";
		$result = mysqli_query($connection, $sql_query);

		$row = mysqli_fetch_row($result);
		$options = "$row[0]";
		while (($row = mysqli_fetch_row($result)))
		{
			$options .= ",$row[0]";
		}

		echo $options;
	}
	elseif ($select == "salesmans")
	{
		$market = urldecode($_REQUEST['market']);
		$sql_query = "SELECT salesman_name FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE market_name='$market')";
		$result = mysqli_query($connection, $sql_query);

		$row = mysqli_fetch_row($result);
		$options = "$row[0]";
		while (($row = mysqli_fetch_row($result)))
		{
			$options .= ",$row[0]";
		}

		echo $options;
	}
	elseif ($select == "customers") 
	{
		$market = urldecode($_REQUEST['market']);
		$sql_query = "SELECT customer_name FROM customers WHERE customer_id IN (SELECT customer_id FROM sales WHERE salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE market_name='$market')))";
		$result = mysqli_query($connection, $sql_query);

		$row = mysqli_fetch_row($result);
		$options = "$row[0]";
		while (($row = mysqli_fetch_row($result)))
		{
			$options .= ",$row[0]";
		}

		echo $options;
	}
}

if (isset($_REQUEST['action']))
{
	$action = $_REQUEST['action'];
	if ($action == "show_markets_sale_count")
	{
		$city = urldecode($_REQUEST['city']);
		$sql_query = "SELECT market_id,market_name FROM markets WHERE city_id=(SELECT city_id FROM cities WHERE city_name='$city')";
		$result = mysqli_query($connection, $sql_query);

		// query once then loop
		$row = mysqli_fetch_row($result);
		$sql_query = "SELECT * FROM sales WHERE salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id=$row[0])";
		$result_market = mysqli_query($connection, $sql_query);

		$options_labels = "$row[1]";
		$options_datas = "$result_market->num_rows";
		while (($row = mysqli_fetch_row($result)))
		{
			$sql_query = "SELECT * FROM sales WHERE salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id=$row[0])";
			$result_market = mysqli_query($connection, $sql_query);

			$options_labels .= ",$row[1]";
			$options_datas .= ",$result_market->num_rows";
		}

		echo $options_labels . ";" . $options_datas;
	}
	elseif ($action == "show_market_product_sales")
	{
		$market = urldecode($_REQUEST['market']);

		$sql_query = "SELECT * FROM products";
		$result = mysqli_query($connection, $sql_query);

		$row = mysqli_fetch_row($result);
		$sql_query = "SELECT * FROM sales WHERE product_id=$row[0] AND salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE market_name='$market'))";
		$result_sales = mysqli_query($connection, $sql_query);

		$labels .= "$row[1]";
		$sell_num .= "$result_sales->num_rows";

		while (($row = mysqli_fetch_row($result)))
		{
			$sql_query = "SELECT * FROM sales WHERE product_id=$row[0] AND salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE market_name='$market'))";
			$result_sales = mysqli_query($connection, $sql_query);

			$labels .= ",$row[1]";
			$sell_num .= ",$result_sales->num_rows";
		}

		echo $labels . ";" . $sell_num;
	}
	elseif ($action == "show_market_salesman_sales")
	{
		$market = urldecode($_REQUEST['market']);

		$sql_query = "SELECT salesman_id, salesman_name FROM salesmans";
		$result = mysqli_query($connection, $sql_query);

		$row = mysqli_fetch_row($result);
		$sql_query = "SELECT * FROM sales WHERE product_id=$row[0] AND salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE market_name='$market'))";
		$result_sales = mysqli_query($connection, $sql_query);

		$labels .= "$row[1]";
		$sell_num .= "$result_sales->num_rows";

		while (($row = mysqli_fetch_row($result)))
		{
			$sql_query = "SELECT * FROM sales WHERE salesman_id=$row[0]";
			$result_sales = mysqli_query($connection, $sql_query);

			$labels .= ",$row[1]";
			$sell_num .= ",$result_sales->num_rows";
		}

		echo $labels . ";" . $sell_num;
	}
	elseif ($action == "show_salesman_product_sales")
	{
		$salesman = urldecode($_REQUEST['salesman']);

		$sql_query = "SELECT product_id, product_name FROM products";
		$result = mysqli_query($connection, $sql_query);

		$first_item = 1;
		while (($row = mysqli_fetch_row($result)))
		{
			$sql_query = "SELECT * FROM sales WHERE product_id=$row[0] AND salesman_id=(SELECT salesman_id FROM salesmans WHERE salesman_name='$salesman')";
			$result_sales = mysqli_query($connection, $sql_query);

			if ($result_sales->num_rows == 0) continue;

			if ($first_item == 1)
			{
				$products = "$row[1]";
				$sell_num = "$result_sales->num_rows";
				$first_item = 0;
			}
			else
			{
				$products .= ",$row[1]";
				$sell_num .= ",$result_sales->num_rows";
			}
		}

		echo $products . ";" . $sell_num;
	}
	elseif ($action == "show_customer_purchases")
	{
		$market = urldecode($_REQUEST['market']);
		$customer = urldecode($_REQUEST['customer']);

		$sql_query = "SELECT product_id, sale_date FROM sales WHERE salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE market_name='$market')) AND customer_id=(SELECT customer_id FROM customers WHERE customer_name='$customer')";
		$result = mysqli_query($connection, $sql_query);

		// Create the first item and then loop
		$row = mysqli_fetch_row($result);
		
		$sql_query = "SELECT product_name, product_price FROM products WHERE product_id=$row[0]";
		$result_product = mysqli_query($connection, $sql_query);

		$row_product = mysqli_fetch_row($result_product);

		$product_names = $row_product[0];
		$product_prices = $row_product[1];
		$sale_date = $row[1];

		while (($row = mysqli_fetch_row($result)))
		{
			$sql_query = "SELECT product_name, product_price FROM products WHERE product_id=$row[0]";
			$result_product = mysqli_query($connection, $sql_query);

			$row_product = mysqli_fetch_row($result_product);
			$product_names .= "," . $row_product[0];
			$product_prices .= "," . $row_product[1];

			$sale_date .= "," . $row[1];
		}

		echo $product_names . ";" . $product_prices . ";" . $sale_date;
	}
	elseif ($action == "show_allsales_eachdistrict")
	{
		$sql_query = "SELECT district_id, district_name FROM districts";
		$result = mysqli_query($connection, $sql_query);

		// Handle the first item then loop
		$row = mysqli_fetch_row($result);
		$sql_query = "SELECT * FROM sales WHERE salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE city_id IN (SELECT city_id FROM cities WHERE district_id=$row[0])))";
		$result_sales = mysqli_query($connection, $sql_query);

		$district_name = $row[1];
		$district_sell = $result_sales->num_rows;
		while (($row = mysqli_fetch_row($result)))
		{
			$sql_query = "SELECT * FROM sales WHERE salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE city_id IN (SELECT city_id FROM cities WHERE district_id=$row[0])))";
			$result_sales = mysqli_query($connection, $sql_query);

			$district_name .= "," . $row[1];
			$district_sell .= "," . $result_sales->num_rows;
		}

		echo $district_name . ";" . $district_sell;
	}
	elseif ($action = "show_allsales_eachmarket")
	{
		$sql_query = "SELECT DISTINCT market_name FROM markets";
		$result = mysqli_query($connection, $sql_query);

		// Handle the first item then loop
		$row = mysqli_fetch_row($result);
		$sql_query = "SELECT * FROM sales WHERE salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE market_name='$row[0]'))";
		$result_sales = mysqli_query($connection, $sql_query);

		$market_name = $row[0];
		$market_sell = $result_sales->num_rows;
		while (($row = mysqli_fetch_row($result)))
		{
			$sql_query = "SELECT * FROM sales WHERE salesman_id IN (SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE market_name='$row[0]'))";
			$result_sales = mysqli_query($connection, $sql_query);

			$market_name .= "," . $row[0];
			$market_sell .= "," . $result_sales->num_rows;
		}

		echo $market_name . ";" . $market_sell;
	}
}

?>