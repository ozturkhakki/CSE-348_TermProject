<?php

$servername = "localhost";
$username = "root";
$password = "mysql";
$db_name = "hakki_ozturk";

// Try to connect to localhost
$connection = mysqli_connect($servername, $username, $password)
	or die("Couldnt connect localhost! " . mysqli_error());

$action = $_REQUEST['action'];

if ($action)
{
	if ($action == "delete")
	{
		$sql_query = "DROP DATABASE IF EXISTS " . $db_name;
		$result = mysqli_query($connection, $sql_query);

		echo "DB Deleted!";
	}
	elseif ($action == "install")
	{
		// Create database
		$sql_query = "DROP DATABASE IF EXISTS " . $db_name;
		$result = mysqli_query($connection, $sql_query);
		$sql_query = "CREATE DATABASE " . $db_name . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
		$result = mysqli_query($connection, $sql_query) 
			or die("Couldnt create db " . $db_name);

		// Select database to work on
		mysqli_select_db($connection, $db_name);
		mysqli_set_charset($connection, 'utf8mb4');

		// CREATE TABLES
		// Create districts table
		$sql_query = "CREATE TABLE districts (
				district_id int NOT NULL AUTO_INCREMENT,
				district_name varchar(48) NOT NULL,
				PRIMARY KEY (district_id)
			)";
		mysqli_query($connection, $sql_query);

		// Create cities table
		$sql_query = "CREATE TABLE cities (
				city_id int NOT NULL AUTO_INCREMENT,
				city_name varchar(48) NOT NULL,
				district_id int NOT NULL,
				PRIMARY KEY (city_id),
				FOREIGN KEY (district_id) REFERENCES districts(district_id)
			)";
		mysqli_query($connection, $sql_query);

		// Create markets table
		$sql_query = "CREATE TABLE markets (
				market_id int NOT NULL AUTO_INCREMENT,
				market_name varchar(48) NOT NULL,
				city_id int NOT NULL,
				PRIMARY KEY (market_id),
				FOREIGN KEY (city_id) REFERENCES cities(city_id)
			)";
		mysqli_query($connection, $sql_query);

		// Create salesmans table
		$sql_query = "CREATE TABLE salesmans (
				salesman_id int NOT NULL AUTO_INCREMENT,
				salesman_name varchar(96) NOT NULL,
				salesman_salary double DEFAULT 0,
				salesman_hiredate date NOT NULL,
				market_id int,
				PRIMARY KEY (salesman_id),
				FOREIGN KEY (market_id) REFERENCES markets(market_id)
			)";
		mysqli_query($connection, $sql_query);

		// Create customers table
		$sql_query = "CREATE TABLE customers (
				customer_id int NOT NULL AUTO_INCREMENT,
				customer_name varchar(96) NOT NULL,
				city_id int NOT NULL,
				PRIMARY KEY (customer_id),
				FOREIGN KEY (city_id) REFERENCES cities(city_id)
			)";
		mysqli_query($connection, $sql_query);

		// Create products table
		$sql_query = "CREATE TABLE products (
				product_id int NOT NULL AUTO_INCREMENT,
				product_name varchar(96) NOT NULL,
				product_price double NOT NULL,
				PRIMARY KEY (product_id)
			)";
		mysqli_query($connection, $sql_query);

		// Create sales table
		$sql_query = "CREATE TABLE sales (
				sale_id int NOT NULL AUTO_INCREMENT,
				sale_date date NOT NULL,
				product_id int,
				salesman_id int,
				customer_id int,
				PRIMARY KEY (sale_id),
				FOREIGN KEY (product_id) REFERENCES products(product_id),
				FOREIGN KEY (salesman_id) REFERENCES salesmans(salesman_id),
				FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
			)";
		mysqli_query($connection, $sql_query);

		// ADD ROWS TO TABLES
		// Add districts to table
		$handle = NULL;
		$csv_file = "csv_files/districts.csv";
		if (!(file_exists($csv_file) && is_readable($csv_file)))
		{
			die("File cannot be read: $csv_file");
		}

		if (!($handle = fopen($csv_file, "r")))
		{
			die("File cannot be opened: $csv_file");
		}

		$line = fgetcsv($handle); // to get rid of the header
		while (($line = fgetcsv($handle, 512, ',')) !== FALSE)
		{
			$sql_query = "INSERT INTO 
					districts (district_name) 
					VALUES('$line[1]')";
			mysqli_query($connection, $sql_query);
		}
		fclose($handle);
		$handle = NULL;
		
		// Add cities to table
		$csv_file = "csv_files/cities.csv";
		if (!(file_exists($csv_file) && is_readable($csv_file)))
		{
			die("File cannot be read: $csv_file");
		}

		if (!($handle = fopen($csv_file, "r")))
		{
			die("File cannot be opened: $csv_file");
		}

		$city_count = 0;
		$line = fgetcsv($handle); // to get rid of the header
		while (($line = fgetcsv($handle, 512, ',')) !== FALSE)
		{
			$sql_query = "INSERT INTO 
					cities (city_name, district_id) 
					VALUES('$line[1]', (SELECT district_id FROM districts WHERE district_name='$line[2]'))";
			mysqli_query($connection, $sql_query);

			$city_count++;
		}
		fclose($handle);
		$handle = NULL;

		// Add markets to an array
		$csv_file = "csv_files/markets.csv";
		if (!(file_exists($csv_file) && is_readable($csv_file)))
		{
			die("File cannot be read: " . $csv_file);
		}

		if (!($handle = fopen($csv_file, "r")))
		{
			die("File cannot be opened: " . $csv_file);
		}

		$market_array = array();
		$line = fgetcsv($handle); // to get rid of the header
		while (($line = fgetcsv($handle, 512, ',')) !== FALSE)
		{
			$market_array = array_merge($market_array, array($line[1]));
		}
		fclose($handle);
		$handle = NULL;

		for ($i = 1; $i <= $city_count; $i++)
		{
			$j = 0;
			while ($j < 5)
			{
				$random_num = rand(0, 9);
				$sql_query = "SELECT * FROM markets WHERE market_name='$market_array[$random_num]' AND city_id=$i";
				$result = mysqli_query($connection, $sql_query);
				if ($result->num_rows !== 0) continue;

				$sql_query = "INSERT INTO 
						markets (market_name, city_id) 
						VALUES('$market_array[$random_num]', $i)";
				mysqli_query($connection, $sql_query);
				$j++;
			}
		}

		// Add products to table
		$csv_file = "csv_files/products.csv";
		if (!(file_exists($csv_file) && is_readable($csv_file)))
		{
			die("File cannot be read: $csv_file");
		}

		if (!($handle = fopen($csv_file, "r")))
		{
			die("File cannot be opened: $csv_file");
		}

		$product_count = 0;
		$line = fgetcsv($handle); // to get rid of the header
		while (($line = fgetcsv($handle, 512, ',')) !== FALSE)
		{
			$random_price = rand(0, 20);
			if ($random_price == 0)
				$random_price_dot = rand(1, 100);
			else
				$random_price_dot = rand(1, 100);

			$random_price = $random_price + ($random_price_dot / 100);
			$sql_query = "INSERT INTO 
					products (product_name, product_price) 
					VALUES('$line[1]', $random_price)";
			mysqli_query($connection, $sql_query);

			$product_count++;
		}
		fclose($handle);
		$handle = NULL;

		// Add names to an array
		$csv_file = "csv_files/names.csv";
		if (!(file_exists($csv_file) && is_readable($csv_file)))
		{
			die("File cannot be read: " . $csv_file);
		}

		if (!($handle = fopen($csv_file, "r")))
		{
			die("File cannot be opened: " . $csv_file);
		}

		$names_array = array();
		$line = fgetcsv($handle); // to get rid of the header
		while (($line = fgetcsv($handle, 512, ',')) !== FALSE)
		{
			$names_array = array_merge($names_array, array($line[1]));
		}
		fclose($handle);
		$handle = NULL;

		// Add surnames to an array
		$csv_file = "csv_files/surnames.csv";
		if (!(file_exists($csv_file) && is_readable($csv_file)))
		{
			die("File cannot be read: " . $csv_file);
		}

		if (!($handle = fopen($csv_file, "r")))
		{
			die("File cannot be opened: " . $csv_file);
		}

		$surnames_array = array();
		$line = fgetcsv($handle); // to get rid of the header
		while (($line = fgetcsv($handle, 512, ',')) !== FALSE)
		{
			$surnames_array = array_merge($surnames_array, array($line[1]));
		}
		fclose($handle);
		$handle = NULL;

		// Add customer names to table
		$which_city = 0;
		for ($i = 1; $i <= 1620; $i++)
		{
			while (TRUE)
			{
				$random_num = rand(0, sizeof($names_array) - 1);
				$random_num2 = rand(0, sizeof($surnames_array) - 1);
				$sql_query = "SELECT * FROM customers WHERE customer_name='$names_array[$random_num] $surnames_array[$random_num2]'";
				$result = mysqli_query($connection, $sql_query);
				if ($result->num_rows === 0) break;
			}

			if ($i % 20 === 1) $which_city++;

			$sql_query = "INSERT INTO 
					customers (customer_name, city_id) 
					VALUES('$names_array[$random_num] $surnames_array[$random_num2]', $which_city)";
			mysqli_query($connection, $sql_query);
		}

		// Add salesman names to table
		$which_market = 0;
		for ($i = 1; $i <= 1215; $i++)
		{
			while (TRUE)
			{
				$random_num = rand(0, sizeof($names_array) - 1);
				$random_num2 = rand(0, sizeof($surnames_array) - 1);
				$sql_query = "SELECT * FROM customers WHERE customer_name='$names_array[$random_num] $surnames_array[$random_num2]'";
				$result = mysqli_query($connection, $sql_query);
				if ($result->num_rows === 0)
				{
					$sql_query = "SELECT * FROM salesmans WHERE salesman_name='$names_array[$random_num] $surnames_array[$random_num2]'";
					$result = mysqli_query($connection, $sql_query);
					if ($result->num_rows === 0) break;
				}
			}

			if ($i % 3 === 1) $which_market++;

			$random_salary = rand(18, 54) * 100;
			$random_day = rand(1, 28);
			$random_month = rand(1, 12);
			$random_year = rand(1998, 2018);

			$sql_query = "INSERT INTO 
					salesmans (salesman_name, salesman_salary, salesman_hiredate, market_id) 
					VALUES('$names_array[$random_num] $surnames_array[$random_num2]', $random_salary, '$random_year-$random_month-$random_day', $which_market)";
			mysqli_query($connection, $sql_query);
		}

		// Add sales to the table
		for ($i = 1; $i <= 1620; $i++)
		{
			$random_sales = rand(0, 5);
			for ($j = 0; $j < $random_sales; $j++)
			{
				$random_day = rand(1, 28);
				$random_month = rand(1, 12);
				$random_year = rand(2016, 2018);

				// find random values for this sale
				$random_product = rand(1, $product_count);
				$random_salesman = rand(1, 15) - 1;
				$sql_query = "SELECT salesman_id FROM salesmans WHERE market_id IN (SELECT market_id FROM markets WHERE city_id=(SELECT city_id FROM customers WHERE customer_id=$i)) ORDER BY salesman_id LIMIT $random_salesman, 1";
				$result = mysqli_query($connection, $sql_query);
				$random_salesman_id = mysqli_fetch_row($result)[0];

				// make the query
				$sql_query = "INSERT INTO 
					sales (sale_date, product_id, salesman_id, customer_id) 
					VALUES('$random_year-$random_month-$random_day', $random_product, $random_salesman_id, $i)";
				mysqli_query($connection, $sql_query);
			}
		}


		echo "DB Installed!";
	}
}

// Close the connection
mysqli_close($connection);

?>
