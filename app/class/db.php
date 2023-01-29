<?php

namespace app\class;
class db
{
    public $base_url;
    public $connect;
    public $query;
    public $statement;
    public $now;
    public $cur_sym;

    function db()
    {
        if (file_exists(dirname(__DIR__) . '/install/credential.php')) {
            include(dirname(__DIR__) . '/install/credential.php');
            try {

                $this->connect = new PDO("mysql:host=$gdb_host;dbname=$gdb_name", $gdb_user_name, $gdb_password);

                //$this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                session_start();

                $this->base_url = $gbase_url;

                if ($this->if_table_exists()) {
                    if ($this->if_master_exists()) {
                        if ($this->is_store_setup()) {
                            $this->cur_sym = '<span style="font-family: DejaVu Sans;">' . $this->Get_currency_symbol() . '</span>&nbsp;';

                            date_default_timezone_set($this->Get_time_zone());

                            $this->now = date("Y-m-d H:i:s", STRTOTIME(date('h:i:sa')));

                            return true;
                        } else {
                            header('location:' . $gbase_url . 'install/set_up_store.php');
                        }
                    } else {
                        header('location:' . $gbase_url . 'install/set_up_master.php');
                    }
                } else {
                    $this->create_table($gdb_name);
                    header('location:' . $gbase_url . 'install/set_up_master.php');
                }

            } catch (PDOException $e) {
                header('location:' . $gbase_url . 'install/set_up.php');
            }
        } else {
            $dir_array = explode("/", dirname($_SERVER['PHP_SELF']));
            if (end($dir_array) == 'admin') {
                header('location:../install/set_up.php');
            } else {
                header('location:install/set_up.php');
            }
        }
    }

    function execute($data = null)
    {
        $this->statement = $this->connect->prepare($this->query);
        if ($data) {
            $this->statement->execute($data);
        } else {
            $this->statement->execute();
        }
    }

    function row_count()
    {
        return $this->statement->rowCount();
    }

    function statement_result()
    {
        return $this->statement->fetchAll();
    }

    function get_result()
    {
        return $this->connect->query($this->query, PDO::FETCH_ASSOC);
    }

    function is_login()
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }

    function is_master_user()
    {
        if (isset($_SESSION['user_type'])) {
            if ($_SESSION["user_type"] == 'Master') {
                return true;
            }
            return false;
        }
        return false;
    }

    function Get_user_name()
    {
        $this->query = "
		SELECT user_name FROM user_ims 
		WHERE user_id = '" . $_SESSION['user_id'] . "'
		";
        $user_name = '';
        $result = $this->get_result();
        foreach ($result as $row) {
            $user_name = $row["user_name"];
        }
        return $user_name;
    }

    function convert_data($string, $action = 'encrypt')
    {
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'AA74CDCC2BBRT935136HH7B63C27'; // user define private key
        $secret_iv = '5fgf5HJ5g27'; // user define secret key
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16); // sha256 is hash_hmac_algo
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    function clean_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function fill_category()
    {
        $this->query = "
		SELECT * FROM category_ims 
		WHERE category_status = 'Enable' 
		ORDER BY category_name ASC
		";

        $result = $this->get_result();

        $output = '<option value="">Select Category</option>';

        foreach ($result as $row) {
            $output .= '<option value="' . $row["category_id"] . '">' . $row["category_name"] . '</option>';
        }
        return $output;
    }

    function fill_location_rack()
    {
        $this->query = "
		SELECT * FROM location_rack_ims 
		WHERE location_rack_status = 'Enable' 
		ORDER BY location_rack_name ASC
		";

        $result = $this->get_result();

        $output = '<option value="">Select Location Rack</option>';

        foreach ($result as $row) {
            $output .= '<option value="' . $row["location_rack_id"] . '">' . $row["location_rack_name"] . '</option>';
        }
        return $output;
    }

    function fill_supplier()
    {
        $this->query = "
		SELECT * FROM supplier_ims 
		WHERE supplier_status = 'Enable' 
		ORDER BY supplier_name ASC
		";

        $result = $this->get_result();

        $output = '';

        foreach ($result as $row) {
            $output .= '<option value="' . $row["supplier_id"] . '">' . $row["supplier_name"] . '</option>';
        }
        return $output;
    }

    function fill_company()
    {
        $this->query = "
		SELECT * FROM item_manufacuter_company_ims 
		WHERE company_status = 'Enable' 
		ORDER BY company_name ASC
		";

        $result = $this->get_result();

        $output = '';

        foreach ($result as $row) {
            $output .= '<option value="' . $row["item_manufacuter_company_id"] . '">' . $row["company_name"] . '</option>';
        }
        return $output;
    }

    function fill_tax()
    {
        $this->query = "
		SELECT * FROM tax_ims 
		WHERE tax_status = 'Enable' 
		ORDER BY tax_name ASC
		";

        $result = $this->get_result();

        //$output = '<option value="">Select Tax</option>';

        $output = '';

        foreach ($result as $row) {
            $output .= '<option value="' . $row["tax_id"] . '">' . $row["tax_name"] . ' ' . $row["tax_percentage"] . '</option>';
        }
        return $output;
    }

    function fill_item()
    {
        $this->query = "
		SELECT * FROM item_ims 
		WHERE item_status = 'Enable' 
		ORDER BY item_name ASC
		";

        $result = $this->get_result();

        $output = '';

        foreach ($result as $row) {
            $output .= '<option value="' . $row["item_id"] . '">' . $row["item_name"] . '</option>';
        }
        return $output;
    }

    function get_product_array()
    {
        $this->query = "
		SELECT * FROM item_purchase_ims 
		INNER JOIN item_ims 
		ON item_ims.item_id =  item_purchase_ims.item_id 
		WHERE item_purchase_ims.item_purchase_status = 'Enable' 
		AND item_ims.item_status = 'Enable' 
		AND item_ims.item_available_quantity > 0 
		ORDER BY item_ims.item_name ASC
		";

        $result = $this->get_result();

        $output = '';

        foreach ($result as $row) {
            $output .= '<option value="' . $row["item_purchase_id"] . '">' . $row["item_name"] . ' | Batch No. - ' . $row["item_batch_no"] . '</option>';
        }
        return $output;
    }

    function Get_Product_company_code($item_manufacuter_company_id)
    {
        $this->query = "
		SELECT company_short_name FROM item_manufacuter_company_ims 
		WHERE item_manufacuter_company_id = '$item_manufacuter_company_id'
		";

        $result = $this->get_result();

        $output = '';

        foreach ($result as $row) {
            $output = $row["company_short_name"];
        }

        return $output;

    }

    function Get_category_name($category_id)
    {
        $this->query = "
		SELECT category_name FROM category_ims 
		WHERE category_id = '$category_id'
		";

        $result = $this->get_result();

        $output = '';

        foreach ($result as $row) {
            $output = $row["category_name"];
        }

        return $output;

    }

    function Get_tax_field()
    {
        $output = '';

        $this->query = "
		SELECT * FROM tax_ims 
		WHERE tax_status = 'Enable' 
		ORDER BY tax_name ASC
		";

        $result = $this->get_result();

        foreach ($result as $row) {
            $output .= '
			<tr>
				<td colspan="6" align="right" class="store_tax_per" data-taxper="' . $row["tax_percentage"] . '" data-taxid="' . $row["tax_id"] . '">
					' . $row["tax_name"] . ' @ ' . $row["tax_percentage"] . '%
					<input type="hidden" name="order_tax_name[]" value="' . $row["tax_name"] . '" />
					<input type="hidden" name="order_tax_percentage[]" value="' . $row["tax_percentage"] . '" />
				</td>
				<td colspan="2" id="tax' . $row["tax_id"] . '"></td>
			</tr>
			';
        }
        return $output;
    }

    function Get_order_tax_percentage($order_id)
    {
        $order_tax_percentage = '';
        $this->query = "
		SELECT order_tax_percentage FROM order_ims 
		WHERE order_id = '" . $order_id . "'
		";
        $result = $this->get_result();
        foreach ($result as $row) {
            $order_tax_percentage = $row['order_tax_percentage'];
        }
        return $order_tax_percentage;
    }

    function Get_total_no_of_product()
    {
        $this->query = "
		SELECT COUNT(item_id) AS Total FROM item_ims 
		WHERE item_status = 'Enable' 
		AND item_available_quantity > 0 
		";

        $result = $this->get_result();

        $output = 0;

        foreach ($result as $row) {
            $output = $row["Total"];
        }

        return $output;
    }

    function Get_total_product_purchase()
    {
        $this->query = "
		SELECT SUM(item_purchase_total_cost) AS Total FROM item_purchase_ims 
		WHERE item_purchase_status = 'Enable'
		";

        $result = $this->get_result();

        $output = 0;

        foreach ($result as $row) {
            $output = $row["Total"];
        }

        return $output;
    }

    function Get_total_product_sale()
    {
        $this->query = "
		SELECT SUM(order_total_amount) AS Total FROM order_ims 
		WHERE order_status = 'Enable'
		";

        $result = $this->get_result();

        $output = 0;

        foreach ($result as $row) {
            $output = $row["Total"];
        }

        return $output;
    }

    function Count_outstock_product()
    {
        $this->query = "
		SELECT COUNT(item_id) AS Total FROM item_ims 
		WHERE item_status = 'Enable' 
		AND item_available_quantity <= 0 
		";

        $result = $this->get_result();

        $output = 0;

        foreach ($result as $row) {
            $output = $row["Total"];
        }

        return $output;
    }

    function Get_last_fifteen_day_date()
    {
        $output = array();
        for ($i = 0; $i < 15; $i++) {
            $output['month_date'][] = date('M d', strtotime("-" . $i . " days"));
            $output['date'][] = date('d', strtotime("-" . $i . " days"));
        }
        return $output;
    }

    function Get_last_fifteen_day_product_sale_data()
    {
        $last_fifteen_date = $this->Get_last_fifteen_day_date();

        $data = array();

        foreach ($last_fifteen_date['date'] as $date) {
            $this->query = "
			SELECT SUM(order_total_amount) AS Total FROM order_ims 
			WHERE order_status = 'Enable' 
			AND DAY(order_added_on) = '" . $date . "'
			";

            $this->execute();

            if ($this->row_count() > 0) {
                foreach ($this->statement_result() as $row) {
                    $data[] = floatval($row['Total']);
                }
            }
        }

        return $data;
    }

    function Get_last_six_month_name()
    {
        $output = array();
        for ($i = 0; $i < 6; $i++) {
            $output['month_name'][] = date('F', strtotime("-" . $i . " month"));
            $output['month_number'][] = date('m', strtotime("-" . $i . " month"));
        }
        return $output;
    }

    function Get_last_six_month_medicine_stock_data()
    {
        $last_six_month = $this->Get_last_six_month_name();

        $data = array();

        foreach (array_reverse($last_six_month['month_number']) as $month) {
            $date = "" . date('Y') . "-" . $month . "-01";
            $last_date = date("Y-m-t", strtotime($date));
            $this->query = "
			SELECT COUNT(medicine_id) AS Total FROM item_ims 
			WHERE medicine_available_quantity > medicine_pack_qty 
			AND medicine_add_datetime <= '" . $last_date . "' 
			AND medicine_status = 'Enable'
			";

            foreach ($this->get_result() as $row) {
                $data[] = floatval($row['Total']);
            }

        }
        return $data;
    }

    function Get_user_name_from_id($user_id)
    {
        $this->query = "
		SELECT user_name FROM user_ims 
		WHERE user_id = '" . $user_id . "'
		";
        $user_name = '';
        $result = $this->get_result();
        foreach ($result as $row) {
            $user_name = $row["user_name"];
        }
        return $user_name;
    }

    function if_table_exists()
    {
        $this->query = "
		SHOW TABLES
		";

        $this->execute();

        if ($this->row_count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function if_master_exists()
    {
        $this->query = "
		SELECT * FROM user_ims 
		WHERE user_type = 'Master' 
		AND user_status = 'Enable'
		";

        $this->execute();

        if ($this->row_count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function is_store_setup()
    {
        $this->query = "
		SELECT * FROM store_ims 
		";

        $this->execute();

        if ($this->row_count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function Get_product_name($item_id, $item_purchase_id)
    {
        $this->query = "
		SELECT * FROM item_ims 
		INNER JOIN item_manufacuter_company_ims 
		ON item_manufacuter_company_ims.item_manufacuter_company_id = item_ims.item_manufactured_by 
		WHERE item_ims.item_id = '$item_id'
		";

        $result = $this->get_result();

        $data = array();

        foreach ($result as $row) {
            $data['item_name'] = $row['item_name'];
            $data['company_short_name'] = $row['company_short_name'];
        }

        $this->query = "
		SELECT * FROM item_purchase_ims 
		WHERE item_purchase_id = '$item_purchase_id'
		";

        $result = $this->get_result();

        foreach ($result as $row) {
            $data['item_batch_no'] = $row['item_batch_no'];
            $data['expiry_date'] = $row['item_expired_month'] . ' / ' . $row["item_expired_year"];
            $data['item_sale_price_per_unit'] = $row["item_sale_price_per_unit"];
        }

        return $data;
    }

    function Get_currency_symbol()
    {
        $this->query = "
		SELECT store_currency FROM store_ims 
		LIMIT 1
		";

        $result = $this->get_result();

        $currency_code = '';

        $currency_symbol = '';

        foreach ($result as $row) {
            $currency_code = $row["store_currency"];
        }

        $data = $this->currency_array();

        foreach ($data as $row) {
            if ($row["code"] == $currency_code) {
                $currency_symbol = $row["symbol"];
            }
        }

        return $currency_symbol;
    }

    function Get_time_zone()
    {
        $this->query = "
		SELECT store_timezone FROM store_ims 
		LIMIT 1
		";

        $result = $this->get_result();

        $timezone = '';

        foreach ($result as $row) {
            $timezone = $row["store_timezone"];
        }
        return $timezone;
    }

    function create_table($database)
    {
        /*$this->query = "
            CREATE TABLE category_ims (
              category_id int(11) NOT NULL,
              category_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              category_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              category_datetime datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE item_ims (
              item_id int(11) NOT NULL,
              item_name varchar(250) COLLATE utf8_unicode_ci NOT NULL,
              item_manufactured_by int(11) NOT NULL,
              item_category int(11) NOT NULL,
              item_available_quantity int(11) NOT NULL,
              item_location_rack int(11) NOT NULL,
              item_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              item_add_datetime datetime NOT NULL,
              item_update_datetime datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE item_manufacuter_company_ims (
              item_manufacuter_company_id int(11) NOT NULL,
              company_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              company_short_name varchar(30) COLLATE utf8_unicode_ci NOT NULL,
              company_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              company_added_datetime datetime NOT NULL,
              company_updated_datetime datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE item_purchase_ims (
              item_purchase_id int(11) NOT NULL,
              item_id int(11) NOT NULL,
              supplier_id int(11) NOT NULL,
              item_batch_no varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              item_purchase_qty int(11) NOT NULL,
              available_quantity int(11) NOT NULL,
              item_purchase_price_per_unit decimal(12,2) NOT NULL,
              item_purchase_total_cost decimal(12,2) NOT NULL,
              item_manufacture_month varchar(30) COLLATE utf8_unicode_ci NOT NULL,
              item_manufacture_year int(5) NOT NULL,
              item_expired_month varchar(30) COLLATE utf8_unicode_ci NOT NULL,
              item_expired_year int(5) NOT NULL,
              item_sale_price_per_unit decimal(12,2) NOT NULL,
              item_purchase_datetime datetime NOT NULL,
              item_purchase_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              item_purchase_enter_by int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE location_rack_ims (
              location_rack_id int(11) NOT NULL,
              location_rack_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              location_rack_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              location_rack_datetime datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE order_ims (
              order_id int(11) NOT NULL,
              buyer_name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              order_total_amount decimal(12,2) NOT NULL,
              order_created_by int(11) NOT NULL,
              order_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              order_added_on datetime NOT NULL,
              order_updated_on datetime NOT NULL,
              order_tax_name varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              order_tax_percentage varchar(100) COLLATE utf8_unicode_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE order_item_ims (
              order_item_id int(11) NOT NULL,
              order_id int(11) NOT NULL,
              item_id int(11) NOT NULL,
              item_purchase_id int(11) NOT NULL,
              item_quantity int(11) NOT NULL,
              item_price decimal(12,2) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE store_ims (
              store_id int(11) NOT NULL,
              store_name varchar(250) COLLATE utf8_unicode_ci NOT NULL,
              store_address tinytext COLLATE utf8_unicode_ci NOT NULL,
              store_contact_no varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              store_email_address varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              store_timezone varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              store_currency varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              store_added_on datetime NOT NULL,
              store_updated_on datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE supplier_ims (
              supplier_id int(11) NOT NULL,
              supplier_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              supplier_address tinytext COLLATE utf8_unicode_ci NOT NULL,
              supplier_contact_no varchar(15) COLLATE utf8_unicode_ci NOT NULL,
              supplier_email varchar(150) COLLATE utf8_unicode_ci NOT NULL,
              supplier_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              supplier_datetime datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE tax_ims (
              tax_id int(11) NOT NULL,
              tax_name varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              tax_percentage decimal(4,2) NOT NULL,
              tax_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              tax_added_on datetime NOT NULL,
              tax_updated_on datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            CREATE TABLE user_ims (
              user_id int(11) NOT NULL,
              user_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              user_email varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              user_password varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              user_type enum('Master','User') COLLATE utf8_unicode_ci NOT NULL,
              user_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
              user_created_on datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ALTER TABLE category_ims
                  ADD PRIMARY KEY (category_id);
            ALTER TABLE item_ims
                  ADD PRIMARY KEY (item_id);
              ALTER TABLE item_manufacuter_company_ims
                  ADD PRIMARY KEY (item_manufacuter_company_id);
              ALTER TABLE item_purchase_ims
                  ADD PRIMARY KEY (item_purchase_id);
              ALTER TABLE location_rack_ims
                  ADD PRIMARY KEY (location_rack_id);
              ALTER TABLE order_ims
                  ADD PRIMARY KEY (order_id);
              ALTER TABLE order_item_ims
                  ADD PRIMARY KEY (order_item_id);
              ALTER TABLE store_ims
                  ADD PRIMARY KEY (store_id);
              ALTER TABLE supplier_ims
                  ADD PRIMARY KEY (supplier_id);
              ALTER TABLE tax_ims
                  ADD PRIMARY KEY (tax_id);
              ALTER TABLE user_ims
                  ADD PRIMARY KEY (user_id);
              ALTER TABLE category_ims
                  MODIFY category_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
              ALTER TABLE item_ims
                  MODIFY item_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
              ALTER TABLE item_manufacuter_company_ims
                  MODIFY item_manufacuter_company_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
              ALTER TABLE item_purchase_ims
                  MODIFY item_purchase_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
              ALTER TABLE location_rack_ims
                  MODIFY location_rack_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
              ALTER TABLE order_ims
                  MODIFY order_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
              ALTER TABLE order_item_ims
                  MODIFY order_item_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
              ALTER TABLE store_ims
                  MODIFY store_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
              ALTER TABLE supplier_ims
                  MODIFY supplier_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
              ALTER TABLE tax_ims
                  MODIFY tax_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
              ALTER TABLE user_ims
                  MODIFY user_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
        ";*/

        $this->query = "
    		CREATE TABLE category_ims (
			  category_id int(11) NOT NULL,
			  category_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
			  category_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  category_datetime datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO category_ims (category_id, category_name, category_status, category_datetime) VALUES
			(1, 'Screws', 'Enable', '2022-05-09 18:55:19'),
			(2, 'Nails', 'Enable', '2022-05-14 15:21:45'),
			(3, 'Nuts Bolts', 'Enable', '2022-05-14 15:22:07'),
			(4, 'Washers', 'Enable', '2022-05-14 15:22:21'),
			(5, 'Anchors', 'Enable', '2022-05-14 15:22:33'),
			(6, 'Rivet', 'Enable', '2022-05-14 15:22:44');
			CREATE TABLE item_ims (
			  item_id int(11) NOT NULL,
			  item_name varchar(250) COLLATE utf8_unicode_ci NOT NULL,
			  item_manufactured_by int(11) NOT NULL,
			  item_category int(11) NOT NULL,
			  item_available_quantity int(11) NOT NULL,
			  item_location_rack int(11) NOT NULL,
			  item_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  item_add_datetime datetime NOT NULL,
			  item_update_datetime datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO item_ims (item_id, item_name, item_manufactured_by, item_category, item_available_quantity, item_location_rack, item_status, item_add_datetime, item_update_datetime) VALUES
			(2, 'Anchor Fastener SS', 1, 1, 1850, 1, 'Enable', '2022-05-10 17:24:39', '2022-05-10 17:41:41'),
			(3, 'Stainless Steel Kicker Bolt 4 Inch', 1, 1, 2490, 1, 'Enable', '2022-05-13 16:10:22', '2022-05-13 16:10:22'),
			(4, 'Self Drilling Screws', 9, 1, 1000, 1, 'Enable', '2022-05-14 15:47:19', '2022-05-14 15:47:19'),
			(5, 'Machine Screws', 7, 1, 1000, 1, 'Enable', '2022-05-14 15:47:41', '2022-05-14 15:47:41'),
			(6, 'Self Tapping Screws', 10, 1, 1000, 1, 'Enable', '2022-05-14 15:48:35', '2022-05-14 15:48:35'),
			(7, 'Tapping Screw', 3, 1, 1000, 1, 'Enable', '2022-05-14 15:49:09', '2022-05-14 15:49:09'),
			(8, 'Headless Screws', 4, 1, 1000, 1, 'Enable', '2022-05-14 15:49:30', '2022-05-14 15:49:30'),
			(9, 'Wire Nails', 4, 2, 1000, 2, 'Enable', '2022-05-14 15:50:21', '2022-05-14 15:50:21'),
			(10, 'Steel Nail', 8, 2, 1000, 2, 'Enable', '2022-05-14 15:50:43', '2022-05-14 15:50:43'),
			(11, 'Stainless Steel Plain Wire Nails', 8, 2, 1000, 2, 'Enable', '2022-05-14 15:51:07', '2022-05-14 15:51:07'),
			(12, 'Industrial Steel Wire Nails', 6, 2, 1000, 2, 'Enable', '2022-05-14 15:51:42', '2022-05-14 15:51:42'),
			(13, 'S S U Nails', 5, 2, 1000, 2, 'Enable', '2022-05-14 15:52:26', '2022-05-14 15:52:26'),
			(14, 'Hexagon Fit Bolts', 2, 3, 1000, 3, 'Enable', '2022-05-14 15:53:17', '2022-05-14 15:53:17'),
			(15, 'Hex Flange Bolts', 9, 3, 990, 3, 'Enable', '2022-05-14 15:53:40', '2022-05-14 15:53:40'),
			(16, 'High Tensile Bolt', 7, 3, 980, 3, 'Enable', '2022-05-14 15:54:00', '2022-05-14 15:54:00'),
			(17, 'Metal Nuts', 10, 3, 1000, 3, 'Enable', '2022-05-14 15:54:43', '2022-05-14 15:54:43'),
			(18, 'Pal Nuts', 3, 3, 1000, 3, 'Enable', '2022-05-14 15:55:03', '2022-05-14 15:55:03'),
			(19, 'Indented Hex Washer Head', 3, 4, 1000, 4, 'Enable', '2022-05-14 15:56:02', '2022-05-14 15:56:02'),
			(20, 'Medium Split Lock Washers', 4, 4, 1000, 4, 'Enable', '2022-05-14 15:56:26', '2022-05-14 15:56:26'),
			(21, 'Plain Washer', 8, 4, 1000, 4, 'Enable', '2022-05-14 15:56:46', '2022-05-14 15:56:46'),
			(22, 'Backup Washers', 8, 4, 1000, 4, 'Enable', '2022-05-14 15:57:08', '2022-05-14 15:57:08'),
			(23, 'Mild Steel Washers', 6, 4, 1000, 4, 'Enable', '2022-05-14 15:57:28', '2022-05-14 15:57:28'),
			(24, 'Anchor Bolts', 6, 5, 1000, 5, 'Enable', '2022-05-14 15:58:58', '2022-05-14 15:58:58'),
			(25, 'Anchor Bolt Sleeve', 5, 5, 1000, 5, 'Enable', '2022-05-14 15:59:18', '2022-05-14 15:59:18'),
			(26, 'Concrete Anchors', 2, 5, 1000, 5, 'Enable', '2022-05-14 15:59:39', '2022-05-14 15:59:39'),
			(27, 'Sleeve Anchors', 9, 5, 1000, 5, 'Enable', '2022-05-14 16:00:02', '2022-05-14 16:00:02'),
			(28, 'Anchor Nuts', 7, 5, 1000, 5, 'Enable', '2022-05-14 16:00:24', '2022-05-14 16:00:24'),
			(29, 'Pop Rivets Fasteners', 7, 6, 1000, 6, 'Enable', '2022-05-14 16:05:31', '2022-05-14 16:05:31'),
			(30, 'Avlock Interlock Rivet Fastener', 10, 6, 1000, 6, 'Enable', '2022-05-14 16:06:06', '2022-05-14 16:06:06'),
			(31, 'Gi Pop Rivets Fasteners', 4, 6, 1000, 6, 'Enable', '2022-05-14 16:08:03', '2022-05-14 16:08:03'),
			(32, 'Irrigation Rivet Fastener', 1, 6, 1000, 6, 'Enable', '2022-05-14 16:08:36', '2022-05-14 16:08:36'),
			(33, 'Diana Rivet Fasteners', 6, 6, 1000, 6, 'Enable', '2022-05-14 16:09:02', '2022-05-14 16:09:02');
			CREATE TABLE item_manufacuter_company_ims (
			  item_manufacuter_company_id int(11) NOT NULL,
			  company_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
			  company_short_name varchar(30) COLLATE utf8_unicode_ci NOT NULL,
			  company_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  company_added_datetime datetime NOT NULL,
			  company_updated_datetime datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO item_manufacuter_company_ims (item_manufacuter_company_id, company_name, company_short_name, company_status, company_added_datetime, company_updated_datetime) VALUES
			(1, 'Dyson Corp', 'DYC', 'Enable', '2022-05-10 15:32:12', '2022-05-10 15:35:27'),
			(2, 'TorqBolt Inc', 'TBI', 'Enable', '2022-05-14 15:30:54', '2022-05-14 15:30:54'),
			(3, 'Big Bolt Nut', 'BBN', 'Enable', '2022-05-14 15:31:19', '2022-05-14 15:31:19'),
			(4, 'Boltport Fasteners', 'BPF', 'Enable', '2022-05-14 15:31:39', '2022-05-14 15:31:39'),
			(5, 'Kova Fastners', 'KVN', 'Enable', '2022-05-14 15:32:08', '2022-05-14 15:32:08'),
			(6, 'Kaloti Bolt', 'KLB', 'Enable', '2022-05-14 15:32:36', '2022-05-14 15:32:36'),
			(7, 'Ananka Fastners', 'ANF', 'Enable', '2022-05-14 15:33:13', '2022-05-14 15:33:13'),
			(8, 'Caliber Enterprise', 'CLE', 'Enable', '2022-05-14 15:33:33', '2022-05-14 15:33:33'),
			(9, 'ABS Fastners', 'ABS', 'Enable', '2022-05-14 15:34:25', '2022-05-14 15:34:25'),
			(10, 'Arasna Industries', 'ARS', 'Enable', '2022-05-14 15:36:10', '2022-05-14 15:36:10');
			CREATE TABLE item_purchase_ims (
			  item_purchase_id int(11) NOT NULL,
			  item_id int(11) NOT NULL,
			  supplier_id int(11) NOT NULL,
			  item_batch_no varchar(200) COLLATE utf8_unicode_ci NOT NULL,
			  item_purchase_qty int(11) NOT NULL,
			  available_quantity int(11) NOT NULL,
			  item_purchase_price_per_unit decimal(12,2) NOT NULL,
			  item_purchase_total_cost decimal(12,2) NOT NULL,
			  item_manufacture_month varchar(30) COLLATE utf8_unicode_ci NOT NULL,
			  item_manufacture_year int(5) NOT NULL,
			  item_expired_month varchar(30) COLLATE utf8_unicode_ci NOT NULL,
			  item_expired_year int(5) NOT NULL,
			  item_sale_price_per_unit decimal(12,2) NOT NULL,
			  item_purchase_datetime datetime NOT NULL,
			  item_purchase_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  item_purchase_enter_by int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO item_purchase_ims (item_purchase_id, item_id, supplier_id, item_batch_no, item_purchase_qty, available_quantity, item_purchase_price_per_unit, item_purchase_total_cost, item_manufacture_month, item_manufacture_year, item_expired_month, item_expired_year, item_sale_price_per_unit, item_purchase_datetime, item_purchase_status, item_purchase_enter_by) VALUES
			(1, 2, 1, 'AFSS88', 1000, 850, '1.50', '1500.00', '04', 2022, '12', 2025, '2.30', '2022-05-12 16:24:01', 'Enable', 1),
			(2, 3, 1, 'SSKB4I', 2500, 2490, '10.25', '25625.00', '04', 2022, '03', 2026, '12.50', '2022-05-13 16:12:02', 'Enable', 1),
			(3, 25, 1, 'ABSJAN2022', 1000, 1000, '20.00', '20000.00', '01', 2022, '12', 2025, '25.00', '2022-05-14 16:26:26', 'Enable', 1),
			(4, 24, 5, 'ABFEB2022', 1000, 1000, '19.00', '19000.00', '02', 2022, '01', 2025, '24.00', '2022-05-14 16:27:42', 'Enable', 1),
			(5, 2, 3, 'AFSMAR2022', 1000, 1000, '35.00', '35000.00', '03', 2022, '02', 2025, '40.00', '2022-05-14 16:28:42', 'Enable', 1),
			(6, 28, 4, 'ANAP2022', 1000, 1000, '34.00', '34000.00', '04', 2022, '03', 2025, '39.00', '2022-05-14 16:29:29', 'Enable', 1),
			(7, 30, 2, 'AIRFMY2022', 1000, 1000, '10.00', '10000.00', '05', 2022, '04', 2024, '13.00', '2022-05-14 16:30:35', 'Enable', 1),
			(8, 22, 1, 'BWJAN2022', 1000, 1000, '0.50', '500.00', '01', 2022, '12', 2024, '0.75', '2022-05-14 16:31:51', 'Enable', 1),
			(9, 26, 5, 'CAFAB2022', 1000, 1000, '25.00', '25000.00', '02', 2022, '01', 2025, '29.00', '2022-05-14 16:32:59', 'Enable', 1),
			(10, 33, 3, 'DRFMAR2022', 1000, 1000, '5.00', '5000.00', '03', 2022, '02', 2025, '8.00', '2022-05-14 16:34:35', 'Enable', 1),
			(11, 32, 4, 'IRFAPR2022', 1000, 1000, '5.00', '5000.00', '04', 2022, '03', 2025, '8.50', '2022-05-14 16:35:45', 'Enable', 1),
			(12, 31, 2, 'GPRFMY2022', 1000, 1000, '8.00', '8000.00', '05', 2025, '04', 2025, '11.50', '2022-05-14 16:36:59', 'Enable', 1),
			(13, 29, 1, 'PRFJAN2022', 1000, 1000, '1.30', '1300.00', '01', 2022, '12', 2024, '1.50', '2022-05-14 16:38:08', 'Enable', 1),
			(14, 27, 5, 'SAMY2022', 1000, 1000, '13.00', '13000.00', '05', 2022, '04', 2025, '17.00', '2022-05-14 16:39:34', 'Enable', 1),
			(15, 23, 5, 'MSWJAN2022', 1000, 1000, '5.00', '5000.00', '01', 2022, '12', 2024, '7.50', '2022-05-14 16:40:59', 'Enable', 1),
			(16, 21, 4, 'PWFEB2022', 1000, 1000, '1.00', '1000.00', '02', 2022, '01', 2025, '1.35', '2022-05-14 16:42:10', 'Enable', 1),
			(17, 20, 4, 'MSLWMAR2022', 1000, 1000, '1.75', '1750.00', '03', 2022, '02', 2025, '2.15', '2022-05-14 16:43:26', 'Enable', 1),
			(18, 19, 2, 'IHWHMY2022', 1000, 1000, '12.00', '12000.00', '05', 2025, '04', 2025, '15.75', '2022-05-14 16:44:52', 'Enable', 1),
			(19, 18, 1, 'PNJAN2022', 1000, 1000, '2.10', '2100.00', '01', 2022, '12', 2024, '2.65', '2022-05-14 16:45:48', 'Enable', 1),
			(20, 17, 5, 'MNMAR2022', 1000, 1000, '5.00', '5000.00', '03', 2022, '02', 2025, '6.15', '2022-05-14 16:47:15', 'Enable', 1),
			(21, 16, 4, 'HTBJAN2022', 1000, 980, '12.00', '12000.00', '01', 2022, '12', 2024, '15.65', '2022-05-14 16:53:21', 'Enable', 1),
			(22, 15, 2, 'HFBFEB2022', 1000, 990, '5.00', '5000.00', '02', 2022, '01', 2025, '6.35', '2022-05-14 16:55:25', 'Enable', 1),
			(23, 14, 1, 'HFBMAR2022', 1000, 1000, '15.00', '15000.00', '03', 2022, '02', 2024, '18.00', '2022-05-14 16:56:25', 'Enable', 1),
			(24, 13, 5, 'SSUNAPR2022', 1000, 1000, '2.75', '2750.00', '04', 2022, '03', 2025, '3.35', '2022-05-14 16:58:11', 'Enable', 1),
			(25, 12, 3, 'ISWNMY2022', 1000, 1000, '0.50', '500.00', '05', 2022, '04', 2025, '0.65', '2022-05-14 16:59:14', 'Enable', 1),
			(26, 11, 4, 'SSPWNJAN2022', 1000, 1000, '0.60', '600.00', '01', 2022, '12', 2025, '0.75', '2022-05-14 17:00:08', 'Enable', 1),
			(27, 10, 2, 'SNFEB2022', 1000, 1000, '0.80', '800.00', '02', 2022, '01', 2025, '1.15', '2022-05-14 17:01:38', 'Enable', 1),
			(28, 9, 1, 'WNAPR2022', 1000, 1000, '0.25', '250.00', '04', 2022, '03', 2025, '0.40', '2022-05-14 17:02:41', 'Enable', 1),
			(29, 8, 5, 'HSMY2022', 1000, 1000, '1.30', '1300.00', '05', 2022, '04', 2025, '1.55', '2022-05-14 17:04:01', 'Enable', 1),
			(30, 7, 4, 'PSJAN2022', 1000, 1000, '5.50', '5500.00', '01', 2022, '12', 2024, '6.25', '2022-05-14 17:05:00', 'Enable', 1),
			(31, 6, 2, 'STSFEB2022', 1000, 1000, '2.50', '2500.00', '02', 2022, '01', 2025, '3.10', '2022-05-14 17:06:30', 'Enable', 1),
			(32, 5, 1, 'MSMAR2022', 1000, 1000, '50.00', '50000.00', '03', 2022, '02', 2025, '58.00', '2022-05-14 17:07:52', 'Enable', 1),
			(33, 4, 4, 'SDSMY2022', 1000, 1000, '2.10', '2100.00', '05', 2022, '04', 2025, '2.35', '2022-05-14 17:10:24', 'Enable', 1);
			CREATE TABLE location_rack_ims (
			  location_rack_id int(11) NOT NULL,
			  location_rack_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
			  location_rack_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  location_rack_datetime datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO location_rack_ims (location_rack_id, location_rack_name, location_rack_status, location_rack_datetime) VALUES
			(1, 'Location Rack - A', 'Enable', '2022-05-10 15:20:37'),
			(2, 'Location Rack - B', 'Enable', '2022-05-14 15:23:02'),
			(3, 'Location Rack - C', 'Enable', '2022-05-14 15:23:22'),
			(4, 'Location Rack - D', 'Enable', '2022-05-14 15:23:33'),
			(5, 'Location Rack - E', 'Enable', '2022-05-14 15:29:52'),
			(6, 'Location Rack - F', 'Enable', '2022-05-14 15:30:18');
			CREATE TABLE order_ims (
			  order_id int(11) NOT NULL,
			  buyer_name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  order_total_amount decimal(12,2) NOT NULL,
			  order_created_by int(11) NOT NULL,
			  order_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  order_added_on datetime NOT NULL,
			  order_updated_on datetime NOT NULL,
			  order_tax_name varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  order_tax_percentage varchar(100) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO order_ims (order_id, buyer_name, order_total_amount, order_created_by, order_status, order_added_on, order_updated_on, order_tax_name, order_tax_percentage) VALUES
			(3, 'Donna Hubber', '407.10', 1, 'Enable', '2022-05-13 15:52:26', '2022-05-13 16:03:12', 'CGST, SGST', '9.00, 9.00'),
			(5, 'Jatin Yadav', '149.57', 1, 'Enable', '2022-05-13 16:23:40', '2022-05-13 16:25:04', 'CGST, SGST', '9.00, 9.00'),
			(6, 'Jayesh Bhai', '444.27', 1, 'Enable', '2022-05-14 17:12:22', '2022-05-14 17:12:22', 'CGST, SGST', '9.00, 9.00');
			CREATE TABLE order_item_ims (
			  order_item_id int(11) NOT NULL,
			  order_id int(11) NOT NULL,
			  item_id int(11) NOT NULL,
			  item_purchase_id int(11) NOT NULL,
			  item_quantity int(11) NOT NULL,
			  item_price decimal(12,2) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO order_item_ims (order_item_id, order_id, item_id, item_purchase_id, item_quantity, item_price) VALUES
			(4, 0, 2, 1, 100, '2.30'),
			(6, 3, 2, 1, 150, '2.30'),
			(10, 5, 3, 2, 10, '12.50'),
			(11, 6, 16, 21, 20, '15.65'),
			(12, 6, 15, 22, 10, '6.35');
			CREATE TABLE store_ims (
			  store_id int(11) NOT NULL,
			  store_name varchar(250) COLLATE utf8_unicode_ci NOT NULL,
			  store_address tinytext COLLATE utf8_unicode_ci NOT NULL,
			  store_contact_no varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  store_email_address varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  store_timezone varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  store_currency varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  store_added_on datetime NOT NULL,
			  store_updated_on datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			CREATE TABLE supplier_ims (
			  supplier_id int(11) NOT NULL,
			  supplier_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
			  supplier_address tinytext COLLATE utf8_unicode_ci NOT NULL,
			  supplier_contact_no varchar(15) COLLATE utf8_unicode_ci NOT NULL,
			  supplier_email varchar(150) COLLATE utf8_unicode_ci NOT NULL,
			  supplier_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  supplier_datetime datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO supplier_ims (supplier_id, supplier_name, supplier_address, supplier_contact_no, supplier_email, supplier_status, supplier_datetime) VALUES
			(1, 'Acme Fastners', '30, Baroda Co Operative Estate, TP 13, Chhani Jakatnaka', '9632574531', 'acmefastners@gmail.com', 'Enable', '2022-05-10 16:10:26'),
			(2, 'Sapani Fastners', '7, Bahulkar Chambers', '8521479630', 'sapanifastners@gmail.com', 'Enable', '2022-05-14 15:37:53'),
			(3, 'KB Fastners', '1 Jay Malhar Nivas', '8539517520', 'kbfastners@gmail.com', 'Enable', '2022-05-14 15:38:42'),
			(4, 'NK Fastners', 'Shahjanand Complex', '7539518520', 'nkfastners@gmail.com', 'Enable', '2022-05-14 15:39:36'),
			(5, 'BM Fastners', '1 Jay Malhar Nivas', '9517538630', 'bmfastners@gmail.com', 'Enable', '2022-05-14 15:40:29');
			CREATE TABLE tax_ims (
			  tax_id int(11) NOT NULL,
			  tax_name varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  tax_percentage decimal(4,2) NOT NULL,
			  tax_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  tax_added_on datetime NOT NULL,
			  tax_updated_on datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO tax_ims (tax_id, tax_name, tax_percentage, tax_status, tax_added_on, tax_updated_on) VALUES
			(1, 'SGST', '9.00', 'Enable', '2022-05-10 18:13:13', '2022-05-10 18:27:39'),
			(2, 'CGST', '9.00', 'Enable', '2022-05-10 18:29:44', '2022-05-10 18:29:44');
			CREATE TABLE user_ims (
			  user_id int(11) NOT NULL,
			  user_name varchar(200) COLLATE utf8_unicode_ci NOT NULL,
			  user_email varchar(200) COLLATE utf8_unicode_ci NOT NULL,
			  user_password varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  user_type enum('Master','User') COLLATE utf8_unicode_ci NOT NULL,
			  user_status enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
			  user_created_on datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			ALTER TABLE category_ims
			  ADD PRIMARY KEY (category_id);
			ALTER TABLE item_ims
			  ADD PRIMARY KEY (item_id);
			ALTER TABLE item_manufacuter_company_ims
			  ADD PRIMARY KEY (item_manufacuter_company_id);
			ALTER TABLE item_purchase_ims
			  ADD PRIMARY KEY (item_purchase_id);
			ALTER TABLE location_rack_ims
			  ADD PRIMARY KEY (location_rack_id);
			ALTER TABLE order_ims
			  ADD PRIMARY KEY (order_id);
			ALTER TABLE order_item_ims
			  ADD PRIMARY KEY (order_item_id);
			ALTER TABLE store_ims
			  ADD PRIMARY KEY (store_id);
			ALTER TABLE supplier_ims
			  ADD PRIMARY KEY (supplier_id);
			ALTER TABLE tax_ims
			  ADD PRIMARY KEY (tax_id);
			ALTER TABLE user_ims
			  ADD PRIMARY KEY (user_id);
			ALTER TABLE category_ims
			  MODIFY category_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
			ALTER TABLE item_ims
			  MODIFY item_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
			ALTER TABLE item_manufacuter_company_ims
			  MODIFY item_manufacuter_company_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
			ALTER TABLE item_purchase_ims
			  MODIFY item_purchase_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
			ALTER TABLE location_rack_ims
			  MODIFY location_rack_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
			ALTER TABLE order_ims
			  MODIFY order_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
			ALTER TABLE order_item_ims
			  MODIFY order_item_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
			ALTER TABLE store_ims
			  MODIFY store_id int(11) NOT NULL AUTO_INCREMENT;
			ALTER TABLE supplier_ims
			  MODIFY supplier_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
			ALTER TABLE tax_ims
			  MODIFY tax_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
			ALTER TABLE user_ims
			  MODIFY user_id int(11) NOT NULL AUTO_INCREMENT;
    	";

        $this->execute();
    }

    function currency_array()
    {
        $currencies = array(
            array('code' => 'ALL',
                'countryname' => 'Albania',
                'name' => 'Albanian lek',
                'symbol' => 'L'),

            array('code' => 'AFN',
                'countryname' => 'Afghanistan',
                'name' => 'Afghanistan Afghani',
                'symbol' => '&#1547;'),

            array('code' => 'ARS',
                'countryname' => 'Argentina',
                'name' => 'Argentine Peso',
                'symbol' => '&#36;'),

            array('code' => 'AWG',
                'countryname' => 'Aruba',
                'name' => 'Aruban florin',
                'symbol' => '&#402;'),

            array('code' => 'AUD',
                'countryname' => 'Australia',
                'name' => 'Australian Dollar',
                'symbol' => '&#65;&#36;'),

            array('code' => 'AZN',
                'countryname' => 'Azerbaijan',
                'name' => 'Azerbaijani Manat',
                'symbol' => '&#8380;'),

            array('code' => 'BSD',
                'countryname' => 'The Bahamas',
                'name' => 'Bahamas Dollar',
                'symbol' => '&#66;&#36;'),

            array('code' => 'BBD',
                'countryname' => 'Barbados',
                'name' => 'Barbados Dollar',
                'symbol' => '&#66;&#100;&#115;&#36;'),

            array('code' => 'BDT',
                'countryname' => 'People\'s Republic of Bangladesh',
                'name' => 'Bangladeshi taka',
                'symbol' => '&#2547;'),

            array('code' => 'BYN',
                'countryname' => 'Belarus',
                'name' => 'Belarus Ruble',
                'symbol' => '&#66;&#114;'),

            array('code' => 'BZD',
                'countryname' => 'Belize',
                'name' => 'Belize Dollar',
                'symbol' => '&#66;&#90;&#36;'),

            array('code' => 'BMD',
                'countryname' => 'British Overseas Territory of Bermuda',
                'name' => 'Bermudian Dollar',
                'symbol' => '&#66;&#68;&#36;'),

            array('code' => 'BOP',
                'countryname' => 'Bolivia',
                'name' => 'Boliviano',
                'symbol' => '&#66;&#115;'),

            array('code' => 'BAM',
                'countryname' => 'Bosnia and Herzegovina',
                'name' => 'Bosnia-Herzegovina Convertible Marka',
                'symbol' => '&#75;&#77;'),

            array('code' => 'BWP',
                'countryname' => 'Botswana',
                'name' => 'Botswana pula',
                'symbol' => '&#80;'),

            array('code' => 'BGN',
                'countryname' => 'Bulgaria',
                'name' => 'Bulgarian lev',
                'symbol' => '&#1083;&#1074;'),

            array('code' => 'BRL',
                'countryname' => 'Brazil',
                'name' => 'Brazilian real',
                'symbol' => '&#82;&#36;'),

            array('code' => 'BND',
                'countryname' => 'Sultanate of Brunei',
                'name' => 'Brunei dollar',
                'symbol' => '&#66;&#36;'),

            array('code' => 'KHR',
                'countryname' => 'Cambodia',
                'name' => 'Cambodian riel',
                'symbol' => '&#6107;'),

            array('code' => 'CAD',
                'countryname' => 'Canada',
                'name' => 'Canadian dollar',
                'symbol' => '&#67;&#36;'),

            array('code' => 'KYD',
                'countryname' => 'Cayman Islands',
                'name' => 'Cayman Islands dollar',
                'symbol' => '&#36;'),

            array('code' => 'CLP',
                'countryname' => 'Chile',
                'name' => 'Chilean peso',
                'symbol' => '&#36;'),

            array('code' => 'CNY',
                'countryname' => 'China',
                'name' => 'Chinese Yuan Renminbi',
                'symbol' => '&#165;'),

            array('code' => 'COP',
                'countryname' => 'Colombia',
                'name' => 'Colombian peso',
                'symbol' => '&#36;'),

            array('code' => 'CRC',
                'countryname' => 'Costa Rica',
                'name' => 'Costa Rican colón',
                'symbol' => '&#8353;'),

            array('code' => 'HRK',
                'countryname' => 'Croatia',
                'name' => 'Croatian kuna',
                'symbol' => '&#107;&#110;'),

            array('code' => 'CUP',
                'countryname' => 'Cuba',
                'name' => 'Cuban peso',
                'symbol' => '&#8369;'),

            array('code' => 'CZK',
                'countryname' => 'Czech Republic',
                'name' => 'Czech koruna',
                'symbol' => '&#75;&#269;'),

            array('code' => 'DKK',
                'countryname' => 'Denmark, Greenland, and the Faroe Islands',
                'name' => 'Danish krone',
                'symbol' => '&#107;&#114;'),

            array('code' => 'DOP',
                'countryname' => 'Dominican Republic',
                'name' => 'Dominican peso',
                'symbol' => '&#82;&#68;&#36;'),

            array('code' => 'XCD',
                'countryname' => 'Antigua and Barbuda, Commonwealth of Dominica, Grenada, Montserrat, St. Kitts and Nevis, Saint Lucia and St. Vincent and the Grenadines',
                'name' => 'Eastern Caribbean dollar',
                'symbol' => '&#36;'),

            array('code' => 'EGP',
                'countryname' => 'Egypt',
                'name' => 'Egyptian pound',
                'symbol' => '&#163;'),

            array('code' => 'SVC',
                'countryname' => 'El Salvador',
                'name' => 'Salvadoran colón',
                'symbol' => '&#36;'),

            array('code' => 'EEK',
                'countryname' => 'Estonia',
                'name' => 'Estonian kroon',
                'symbol' => '&#75;&#114;'),

            array('code' => 'EUR',
                'countryname' => 'European Union, Italy, Belgium, Bulgaria, Croatia, Cyprus, Czechia, Denmark, Estonia, Finland, France, Germany, Greece, Hungary, Ireland, Latvia, Lithuania, Luxembourg, Malta, Netherlands, Poland, Portugal, Romania, Slovakia, Slovenia, Spain, Sweden',
                'name' => 'Euro',
                'symbol' => '&#8364;'),

            array('code' => 'FKP',
                'countryname' => 'Falkland Islands',
                'name' => 'Falkland Islands (Malvinas) Pound',
                'symbol' => '&#70;&#75;&#163;'),

            array('code' => 'FJD',
                'countryname' => 'Fiji',
                'name' => 'Fijian dollar',
                'symbol' => '&#70;&#74;&#36;'),

            array('code' => 'GHC',
                'countryname' => 'Ghana',
                'name' => 'Ghanaian cedi',
                'symbol' => '&#71;&#72;&#162;'),

            array('code' => 'GIP',
                'countryname' => 'Gibraltar',
                'name' => 'Gibraltar pound',
                'symbol' => '&#163;'),

            array('code' => 'GTQ',
                'countryname' => 'Guatemala',
                'name' => 'Guatemalan quetzal',
                'symbol' => '&#81;'),

            array('code' => 'GGP',
                'countryname' => 'Guernsey',
                'name' => 'Guernsey pound',
                'symbol' => '&#81;'),

            array('code' => 'GYD',
                'countryname' => 'Guyana',
                'name' => 'Guyanese dollar',
                'symbol' => '&#71;&#89;&#36;'),

            array('code' => 'HNL',
                'countryname' => 'Honduras',
                'name' => 'Honduran lempira',
                'symbol' => '&#76;'),

            array('code' => 'HKD',
                'countryname' => 'Hong Kong',
                'name' => 'Hong Kong dollar',
                'symbol' => '&#72;&#75;&#36;'),

            array('code' => 'HUF',
                'countryname' => 'Hungary',
                'name' => 'Hungarian forint',
                'symbol' => '&#70;&#116;'),

            array('code' => 'ISK',
                'countryname' => 'Iceland',
                'name' => 'Icelandic króna',
                'symbol' => '&#237;&#107;&#114;'),

            array('code' => 'INR',
                'countryname' => 'India',
                'name' => 'Indian rupee',
                'symbol' => '&#8377;'),

            array('code' => 'IDR',
                'countryname' => 'Indonesia',
                'name' => 'Indonesian rupiah',
                'symbol' => '&#82;&#112;'),

            array('code' => 'IRR',
                'countryname' => 'Iran',
                'name' => 'Iranian rial',
                'symbol' => '&#65020;'),

            array('code' => 'IMP',
                'countryname' => 'Isle of Man',
                'name' => 'Manx pound',
                'symbol' => '&#163;'),

            array('code' => 'ILS',
                'countryname' => 'Israel, Palestinian territories of the West Bank and the Gaza Strip',
                'name' => 'Israeli Shekel',
                'symbol' => '&#8362;'),

            array('code' => 'JMD',
                'countryname' => 'Jamaica',
                'name' => 'Jamaican dollar',
                'symbol' => '&#74;&#36;'),

            array('code' => 'JPY',
                'countryname' => 'Japan',
                'name' => 'Japanese yen',
                'symbol' => '&#165;'),

            array('code' => 'JEP',
                'countryname' => 'Jersey',
                'name' => 'Jersey pound',
                'symbol' => '&#163;'),

            array('code' => 'KZT',
                'countryname' => 'Kazakhstan',
                'name' => 'Kazakhstani tenge',
                'symbol' => '&#8376;'),

            array('code' => 'KPW',
                'countryname' => 'North Korea',
                'name' => 'North Korean won',
                'symbol' => '&#8361;'),

            array('code' => 'KPW',
                'countryname' => 'South Korea',
                'name' => 'South Korean won',
                'symbol' => '&#8361;'),

            array('code' => 'KGS',
                'countryname' => 'Kyrgyz Republic',
                'name' => 'Kyrgyzstani som',
                'symbol' => '&#1083;&#1074;'),

            array('code' => 'LAK',
                'countryname' => 'Laos',
                'name' => 'Lao kip',
                'symbol' => '&#8365;'),

            array('code' => 'LAK',
                'countryname' => 'Laos',
                'name' => 'Latvian lats',
                'symbol' => '&#8364;'),

            array('code' => 'LVL',
                'countryname' => 'Laos',
                'name' => 'Latvian lats',
                'symbol' => '&#8364;'),

            array('code' => 'LBP',
                'countryname' => 'Lebanon',
                'name' => 'Lebanese pound',
                'symbol' => '&#76;&#163;'),

            array('code' => 'LRD',
                'countryname' => 'Liberia',
                'name' => 'Liberian dollar',
                'symbol' => '&#76;&#68;&#36;'),

            array('code' => 'LTL',
                'countryname' => 'Lithuania',
                'name' => 'Lithuanian litas',
                'symbol' => '&#8364;'),

            array('code' => 'MKD',
                'countryname' => 'North Macedonia',
                'name' => 'Macedonian denar',
                'symbol' => '&#1076;&#1077;&#1085;'),

            array('code' => 'MYR',
                'countryname' => 'Malaysia',
                'name' => 'Malaysian ringgit',
                'symbol' => '&#82;&#77;'),

            array('code' => 'MUR',
                'countryname' => 'Mauritius',
                'name' => 'Mauritian rupee',
                'symbol' => '&#82;&#115;'),

            array('code' => 'MXN',
                'countryname' => 'Mexico',
                'name' => 'Mexican peso',
                'symbol' => '&#77;&#101;&#120;&#36;'),

            array('code' => 'MNT',
                'countryname' => 'Mongolia',
                'name' => 'Mongolian tögrög',
                'symbol' => '&#8366;'),

            array('code' => 'MZN',
                'countryname' => 'Mozambique',
                'name' => 'Mozambican metical',
                'symbol' => '&#77;&#84;'),

            array('code' => 'NAD',
                'countryname' => 'Namibia',
                'name' => 'Namibian dollar',
                'symbol' => '&#78;&#36;'),

            array('code' => 'NPR',
                'countryname' => 'Federal Democratic Republic of Nepal',
                'name' => 'Nepalese rupee',
                'symbol' => '&#82;&#115;&#46;'),

            array('code' => 'ANG',
                'countryname' => 'Curaçao and Sint Maarten',
                'name' => 'Netherlands Antillean guilder',
                'symbol' => '&#402;'),

            array('code' => 'NZD',
                'countryname' => 'New Zealand, the Cook Islands, Niue, the Ross Dependency, Tokelau, the Pitcairn Islands',
                'name' => 'New Zealand dollar',
                'symbol' => '&#36;'),

            array('code' => 'NIO',
                'countryname' => 'Nicaragua',
                'name' => 'Nicaraguan córdoba',
                'symbol' => '&#67;&#36;'),

            array('code' => 'NGN',
                'countryname' => 'Nigeria',
                'name' => 'Nigerian naira',
                'symbol' => '&#8358;'),

            array('code' => 'NOK',
                'countryname' => 'Norway and its dependent territories',
                'name' => 'Norwegian krone',
                'symbol' => '&#107;&#114;'),

            array('code' => 'OMR',
                'countryname' => 'Oman',
                'name' => 'Omani rial',
                'symbol' => '&#65020;'),

            array('code' => 'PKR',
                'countryname' => 'Pakistan',
                'name' => 'Pakistani rupee',
                'symbol' => '&#82;&#115;'),

            array('code' => 'PAB',
                'countryname' => 'Panama',
                'name' => 'Panamanian balboa',
                'symbol' => '&#66;&#47;&#46;'),

            array('code' => 'PYG',
                'countryname' => 'Paraguay',
                'name' => 'Paraguayan Guaraní',
                'symbol' => '&#8370;'),

            array('code' => 'PEN',
                'countryname' => 'Peru',
                'name' => 'Sol',
                'symbol' => '&#83;&#47;&#46;'),

            array('code' => 'PHP',
                'countryname' => 'Philippines',
                'name' => 'Philippine peso',
                'symbol' => '&#8369;'),

            array('code' => 'PLN',
                'countryname' => 'Poland',
                'name' => 'Polish złoty',
                'symbol' => '&#122;&#322;'),

            array('code' => 'QAR',
                'countryname' => 'State of Qatar',
                'name' => 'Qatari Riyal',
                'symbol' => '&#65020;'),

            array('code' => 'RON',
                'countryname' => 'Romania',
                'name' => 'Romanian leu (Leu românesc)',
                'symbol' => '&#76;'),

            array('code' => 'RUB',
                'countryname' => 'Russian Federation, Abkhazia and South Ossetia, Donetsk and Luhansk',
                'name' => 'Russian ruble',
                'symbol' => '&#8381;'),

            array('code' => 'SHP',
                'countryname' => 'Saint Helena, Ascension and Tristan da Cunha',
                'name' => 'Saint Helena pound',
                'symbol' => '&#163;'),

            array('code' => 'SAR',
                'countryname' => 'Saudi Arabia',
                'name' => 'Saudi riyal',
                'symbol' => '&#65020;'),

            array('code' => 'RSD',
                'countryname' => 'Serbia',
                'name' => 'Serbian dinar',
                'symbol' => '&#100;&#105;&#110;'),

            array('code' => 'SCR',
                'countryname' => 'Seychelles',
                'name' => 'Seychellois rupee',
                'symbol' => '&#82;&#115;'),

            array('code' => 'SGD',
                'countryname' => 'Singapore',
                'name' => 'Singapore dollar',
                'symbol' => '&#83;&#36;'),

            array('code' => 'SBD',
                'countryname' => 'Solomon Islands',
                'name' => 'Solomon Islands dollar',
                'symbol' => '&#83;&#73;&#36;'),

            array('code' => 'SOS',
                'countryname' => 'Somalia',
                'name' => 'Somali shilling',
                'symbol' => '&#83;&#104;&#46;&#83;&#111;'),

            array('code' => 'ZAR',
                'countryname' => 'South Africa',
                'name' => 'South African rand',
                'symbol' => '&#82;'),

            array('code' => 'LKR',
                'countryname' => 'Sri Lanka',
                'name' => 'Sri Lankan rupee',
                'symbol' => '&#82;&#115;'),

            array('code' => 'SEK',
                'countryname' => 'Sweden',
                'name' => 'Swedish krona',
                'symbol' => '&#107;&#114;'),


            array('code' => 'CHF',
                'countryname' => 'Switzerland',
                'name' => 'Swiss franc',
                'symbol' => '&#67;&#72;&#102;'),

            array('code' => 'SRD',
                'countryname' => 'Suriname',
                'name' => 'Suriname Dollar',
                'symbol' => '&#83;&#114;&#36;'),

            array('code' => 'SYP',
                'countryname' => 'Syria',
                'name' => 'Syrian pound',
                'symbol' => '&#163;&#83;'),

            array('code' => 'TWD',
                'countryname' => 'Taiwan',
                'name' => 'New Taiwan dollar',
                'symbol' => '&#78;&#84;&#36;'),

            array('code' => 'THB',
                'countryname' => 'Thailand',
                'name' => 'Thai baht',
                'symbol' => '&#3647;'),


            array('code' => 'TTD',
                'countryname' => 'Trinidad and Tobago',
                'name' => 'Trinidad and Tobago dollar',
                'symbol' => '&#84;&#84;&#36;'),

            array('code' => 'TRY',
                'countryname' => 'Turkey, Turkish Republic of Northern Cyprus',
                'name' => 'Turkey Lira',
                'symbol' => '&#8378;'),

            array('code' => 'TVD',
                'countryname' => 'Tuvalu',
                'name' => 'Tuvaluan dollar',
                'symbol' => '&#84;&#86;&#36;'),

            array('code' => 'UAH',
                'countryname' => 'Ukraine',
                'name' => 'Ukrainian hryvnia',
                'symbol' => '&#8372;'),

            array('code' => 'GBP',
                'countryname' => 'United Kingdom, Jersey, Guernsey, the Isle of Man, Gibraltar, South Georgia and the South Sandwich Islands, the British Antarctic Territory, and Tristan da Cunha',
                'name' => 'Pound sterling',
                'symbol' => '&#163;'),

            array('code' => 'UGX',
                'countryname' => 'Uganda',
                'name' => 'Ugandan shilling',
                'symbol' => '&#85;&#83;&#104;'),

            array('code' => 'USD',
                'countryname' => 'United States',
                'name' => 'United States dollar',
                'symbol' => '&#36;'),

            array('code' => 'UYU',
                'countryname' => 'Uruguayan',
                'name' => 'Peso Uruguayolar',
                'symbol' => '&#36;&#85;'),

            array('code' => 'UZS',
                'countryname' => 'Uzbekistan',
                'name' => 'Uzbekistani soʻm',
                'symbol' => '&#1083;&#1074;'),

            array('code' => 'VEF',
                'countryname' => 'Venezuela',
                'name' => 'Venezuelan bolívar',
                'symbol' => '&#66;&#115;'),

            array('code' => 'VND',
                'countryname' => 'Vietnam',
                'name' => 'Vietnamese dong (Đồng)',
                'symbol' => '&#8363;'),

            array('code' => 'VND',
                'countryname' => 'Yemen',
                'name' => 'Yemeni rial',
                'symbol' => '&#65020;'),

            array('code' => 'ZWD',
                'countryname' => 'Zimbabwe',
                'name' => 'Zimbabwean dollar',
                'symbol' => '&#90;&#36;'),
        );

        return $currencies;
    }

    function Currency_list()
    {
        $html = '
			<option value="">Select Currency</option>
		';
        $data = $this->currency_array();
        foreach ($data as $row) {
            $html .= '<option value="' . $row["code"] . '">' . $row["name"] . '</option>';
        }
        return $html;
    }

    function Timezone_list()
    {
        $timezones = array(
            'America/Adak' => '(GMT-10:00) America/Adak (Hawaii-Aleutian Standard Time)',
            'America/Atka' => '(GMT-10:00) America/Atka (Hawaii-Aleutian Standard Time)',
            'America/Anchorage' => '(GMT-9:00) America/Anchorage (Alaska Standard Time)',
            'America/Juneau' => '(GMT-9:00) America/Juneau (Alaska Standard Time)',
            'America/Nome' => '(GMT-9:00) America/Nome (Alaska Standard Time)',
            'America/Yakutat' => '(GMT-9:00) America/Yakutat (Alaska Standard Time)',
            'America/Dawson' => '(GMT-8:00) America/Dawson (Pacific Standard Time)',
            'America/Ensenada' => '(GMT-8:00) America/Ensenada (Pacific Standard Time)',
            'America/Los_Angeles' => '(GMT-8:00) America/Los_Angeles (Pacific Standard Time)',
            'America/Tijuana' => '(GMT-8:00) America/Tijuana (Pacific Standard Time)',
            'America/Vancouver' => '(GMT-8:00) America/Vancouver (Pacific Standard Time)',
            'America/Whitehorse' => '(GMT-8:00) America/Whitehorse (Pacific Standard Time)',
            'Canada/Pacific' => '(GMT-8:00) Canada/Pacific (Pacific Standard Time)',
            'Canada/Yukon' => '(GMT-8:00) Canada/Yukon (Pacific Standard Time)',
            'Mexico/BajaNorte' => '(GMT-8:00) Mexico/BajaNorte (Pacific Standard Time)',
            'America/Boise' => '(GMT-7:00) America/Boise (Mountain Standard Time)',
            'America/Cambridge_Bay' => '(GMT-7:00) America/Cambridge_Bay (Mountain Standard Time)',
            'America/Chihuahua' => '(GMT-7:00) America/Chihuahua (Mountain Standard Time)',
            'America/Dawson_Creek' => '(GMT-7:00) America/Dawson_Creek (Mountain Standard Time)',
            'America/Denver' => '(GMT-7:00) America/Denver (Mountain Standard Time)',
            'America/Edmonton' => '(GMT-7:00) America/Edmonton (Mountain Standard Time)',
            'America/Hermosillo' => '(GMT-7:00) America/Hermosillo (Mountain Standard Time)',
            'America/Inuvik' => '(GMT-7:00) America/Inuvik (Mountain Standard Time)',
            'America/Mazatlan' => '(GMT-7:00) America/Mazatlan (Mountain Standard Time)',
            'America/Phoenix' => '(GMT-7:00) America/Phoenix (Mountain Standard Time)',
            'America/Shiprock' => '(GMT-7:00) America/Shiprock (Mountain Standard Time)',
            'America/Yellowknife' => '(GMT-7:00) America/Yellowknife (Mountain Standard Time)',
            'Canada/Mountain' => '(GMT-7:00) Canada/Mountain (Mountain Standard Time)',
            'Mexico/BajaSur' => '(GMT-7:00) Mexico/BajaSur (Mountain Standard Time)',
            'America/Belize' => '(GMT-6:00) America/Belize (Central Standard Time)',
            'America/Cancun' => '(GMT-6:00) America/Cancun (Central Standard Time)',
            'America/Chicago' => '(GMT-6:00) America/Chicago (Central Standard Time)',
            'America/Costa_Rica' => '(GMT-6:00) America/Costa_Rica (Central Standard Time)',
            'America/El_Salvador' => '(GMT-6:00) America/El_Salvador (Central Standard Time)',
            'America/Guatemala' => '(GMT-6:00) America/Guatemala (Central Standard Time)',
            'America/Knox_IN' => '(GMT-6:00) America/Knox_IN (Central Standard Time)',
            'America/Managua' => '(GMT-6:00) America/Managua (Central Standard Time)',
            'America/Menominee' => '(GMT-6:00) America/Menominee (Central Standard Time)',
            'America/Merida' => '(GMT-6:00) America/Merida (Central Standard Time)',
            'America/Mexico_City' => '(GMT-6:00) America/Mexico_City (Central Standard Time)',
            'America/Monterrey' => '(GMT-6:00) America/Monterrey (Central Standard Time)',
            'America/Rainy_River' => '(GMT-6:00) America/Rainy_River (Central Standard Time)',
            'America/Rankin_Inlet' => '(GMT-6:00) America/Rankin_Inlet (Central Standard Time)',
            'America/Regina' => '(GMT-6:00) America/Regina (Central Standard Time)',
            'America/Swift_Current' => '(GMT-6:00) America/Swift_Current (Central Standard Time)',
            'America/Tegucigalpa' => '(GMT-6:00) America/Tegucigalpa (Central Standard Time)',
            'America/Winnipeg' => '(GMT-6:00) America/Winnipeg (Central Standard Time)',
            'Canada/Central' => '(GMT-6:00) Canada/Central (Central Standard Time)',
            'Canada/East-Saskatchewan' => '(GMT-6:00) Canada/East-Saskatchewan (Central Standard Time)',
            'Canada/Saskatchewan' => '(GMT-6:00) Canada/Saskatchewan (Central Standard Time)',
            'Chile/EasterIsland' => '(GMT-6:00) Chile/EasterIsland (Easter Is. Time)',
            'Mexico/General' => '(GMT-6:00) Mexico/General (Central Standard Time)',
            'America/Atikokan' => '(GMT-5:00) America/Atikokan (Eastern Standard Time)',
            'America/Bogota' => '(GMT-5:00) America/Bogota (Colombia Time)',
            'America/Cayman' => '(GMT-5:00) America/Cayman (Eastern Standard Time)',
            'America/Coral_Harbour' => '(GMT-5:00) America/Coral_Harbour (Eastern Standard Time)',
            'America/Detroit' => '(GMT-5:00) America/Detroit (Eastern Standard Time)',
            'America/Fort_Wayne' => '(GMT-5:00) America/Fort_Wayne (Eastern Standard Time)',
            'America/Grand_Turk' => '(GMT-5:00) America/Grand_Turk (Eastern Standard Time)',
            'America/Guayaquil' => '(GMT-5:00) America/Guayaquil (Ecuador Time)',
            'America/Havana' => '(GMT-5:00) America/Havana (Cuba Standard Time)',
            'America/Indianapolis' => '(GMT-5:00) America/Indianapolis (Eastern Standard Time)',
            'America/Iqaluit' => '(GMT-5:00) America/Iqaluit (Eastern Standard Time)',
            'America/Jamaica' => '(GMT-5:00) America/Jamaica (Eastern Standard Time)',
            'America/Lima' => '(GMT-5:00) America/Lima (Peru Time)',
            'America/Louisville' => '(GMT-5:00) America/Louisville (Eastern Standard Time)',
            'America/Montreal' => '(GMT-5:00) America/Montreal (Eastern Standard Time)',
            'America/Nassau' => '(GMT-5:00) America/Nassau (Eastern Standard Time)',
            'America/New_York' => '(GMT-5:00) America/New_York (Eastern Standard Time)',
            'America/Nipigon' => '(GMT-5:00) America/Nipigon (Eastern Standard Time)',
            'America/Panama' => '(GMT-5:00) America/Panama (Eastern Standard Time)',
            'America/Pangnirtung' => '(GMT-5:00) America/Pangnirtung (Eastern Standard Time)',
            'America/Port-au-Prince' => '(GMT-5:00) America/Port-au-Prince (Eastern Standard Time)',
            'America/Resolute' => '(GMT-5:00) America/Resolute (Eastern Standard Time)',
            'America/Thunder_Bay' => '(GMT-5:00) America/Thunder_Bay (Eastern Standard Time)',
            'America/Toronto' => '(GMT-5:00) America/Toronto (Eastern Standard Time)',
            'Canada/Eastern' => '(GMT-5:00) Canada/Eastern (Eastern Standard Time)',
            'America/Caracas' => '(GMT-4:-30) America/Caracas (Venezuela Time)',
            'America/Anguilla' => '(GMT-4:00) America/Anguilla (Atlantic Standard Time)',
            'America/Antigua' => '(GMT-4:00) America/Antigua (Atlantic Standard Time)',
            'America/Aruba' => '(GMT-4:00) America/Aruba (Atlantic Standard Time)',
            'America/Asuncion' => '(GMT-4:00) America/Asuncion (Paraguay Time)',
            'America/Barbados' => '(GMT-4:00) America/Barbados (Atlantic Standard Time)',
            'America/Blanc-Sablon' => '(GMT-4:00) America/Blanc-Sablon (Atlantic Standard Time)',
            'America/Boa_Vista' => '(GMT-4:00) America/Boa_Vista (Amazon Time)',
            'America/Campo_Grande' => '(GMT-4:00) America/Campo_Grande (Amazon Time)',
            'America/Cuiaba' => '(GMT-4:00) America/Cuiaba (Amazon Time)',
            'America/Curacao' => '(GMT-4:00) America/Curacao (Atlantic Standard Time)',
            'America/Dominica' => '(GMT-4:00) America/Dominica (Atlantic Standard Time)',
            'America/Eirunepe' => '(GMT-4:00) America/Eirunepe (Amazon Time)',
            'America/Glace_Bay' => '(GMT-4:00) America/Glace_Bay (Atlantic Standard Time)',
            'America/Goose_Bay' => '(GMT-4:00) America/Goose_Bay (Atlantic Standard Time)',
            'America/Grenada' => '(GMT-4:00) America/Grenada (Atlantic Standard Time)',
            'America/Guadeloupe' => '(GMT-4:00) America/Guadeloupe (Atlantic Standard Time)',
            'America/Guyana' => '(GMT-4:00) America/Guyana (Guyana Time)',
            'America/Halifax' => '(GMT-4:00) America/Halifax (Atlantic Standard Time)',
            'America/La_Paz' => '(GMT-4:00) America/La_Paz (Bolivia Time)',
            'America/Manaus' => '(GMT-4:00) America/Manaus (Amazon Time)',
            'America/Marigot' => '(GMT-4:00) America/Marigot (Atlantic Standard Time)',
            'America/Martinique' => '(GMT-4:00) America/Martinique (Atlantic Standard Time)',
            'America/Moncton' => '(GMT-4:00) America/Moncton (Atlantic Standard Time)',
            'America/Montserrat' => '(GMT-4:00) America/Montserrat (Atlantic Standard Time)',
            'America/Port_of_Spain' => '(GMT-4:00) America/Port_of_Spain (Atlantic Standard Time)',
            'America/Porto_Acre' => '(GMT-4:00) America/Porto_Acre (Amazon Time)',
            'America/Porto_Velho' => '(GMT-4:00) America/Porto_Velho (Amazon Time)',
            'America/Puerto_Rico' => '(GMT-4:00) America/Puerto_Rico (Atlantic Standard Time)',
            'America/Rio_Branco' => '(GMT-4:00) America/Rio_Branco (Amazon Time)',
            'America/Santiago' => '(GMT-4:00) America/Santiago (Chile Time)',
            'America/Santo_Domingo' => '(GMT-4:00) America/Santo_Domingo (Atlantic Standard Time)',
            'America/St_Barthelemy' => '(GMT-4:00) America/St_Barthelemy (Atlantic Standard Time)',
            'America/St_Kitts' => '(GMT-4:00) America/St_Kitts (Atlantic Standard Time)',
            'America/St_Lucia' => '(GMT-4:00) America/St_Lucia (Atlantic Standard Time)',
            'America/St_Thomas' => '(GMT-4:00) America/St_Thomas (Atlantic Standard Time)',
            'America/St_Vincent' => '(GMT-4:00) America/St_Vincent (Atlantic Standard Time)',
            'America/Thule' => '(GMT-4:00) America/Thule (Atlantic Standard Time)',
            'America/Tortola' => '(GMT-4:00) America/Tortola (Atlantic Standard Time)',
            'America/Virgin' => '(GMT-4:00) America/Virgin (Atlantic Standard Time)',
            'Antarctica/Palmer' => '(GMT-4:00) Antarctica/Palmer (Chile Time)',
            'Atlantic/Bermuda' => '(GMT-4:00) Atlantic/Bermuda (Atlantic Standard Time)',
            'Atlantic/Stanley' => '(GMT-4:00) Atlantic/Stanley (Falkland Is. Time)',
            'Brazil/Acre' => '(GMT-4:00) Brazil/Acre (Amazon Time)',
            'Brazil/West' => '(GMT-4:00) Brazil/West (Amazon Time)',
            'Canada/Atlantic' => '(GMT-4:00) Canada/Atlantic (Atlantic Standard Time)',
            'Chile/Continental' => '(GMT-4:00) Chile/Continental (Chile Time)',
            'America/St_Johns' => '(GMT-3:-30) America/St_Johns (Newfoundland Standard Time)',
            'Canada/Newfoundland' => '(GMT-3:-30) Canada/Newfoundland (Newfoundland Standard Time)',
            'America/Araguaina' => '(GMT-3:00) America/Araguaina (Brasilia Time)',
            'America/Bahia' => '(GMT-3:00) America/Bahia (Brasilia Time)',
            'America/Belem' => '(GMT-3:00) America/Belem (Brasilia Time)',
            'America/Buenos_Aires' => '(GMT-3:00) America/Buenos_Aires (Argentine Time)',
            'America/Catamarca' => '(GMT-3:00) America/Catamarca (Argentine Time)',
            'America/Cayenne' => '(GMT-3:00) America/Cayenne (French Guiana Time)',
            'America/Cordoba' => '(GMT-3:00) America/Cordoba (Argentine Time)',
            'America/Fortaleza' => '(GMT-3:00) America/Fortaleza (Brasilia Time)',
            'America/Godthab' => '(GMT-3:00) America/Godthab (Western Greenland Time)',
            'America/Jujuy' => '(GMT-3:00) America/Jujuy (Argentine Time)',
            'America/Maceio' => '(GMT-3:00) America/Maceio (Brasilia Time)',
            'America/Mendoza' => '(GMT-3:00) America/Mendoza (Argentine Time)',
            'America/Miquelon' => '(GMT-3:00) America/Miquelon (Pierre & Miquelon Standard Time)',
            'America/Montevideo' => '(GMT-3:00) America/Montevideo (Uruguay Time)',
            'America/Paramaribo' => '(GMT-3:00) America/Paramaribo (Suriname Time)',
            'America/Recife' => '(GMT-3:00) America/Recife (Brasilia Time)',
            'America/Rosario' => '(GMT-3:00) America/Rosario (Argentine Time)',
            'America/Santarem' => '(GMT-3:00) America/Santarem (Brasilia Time)',
            'America/Sao_Paulo' => '(GMT-3:00) America/Sao_Paulo (Brasilia Time)',
            'Antarctica/Rothera' => '(GMT-3:00) Antarctica/Rothera (Rothera Time)',
            'Brazil/East' => '(GMT-3:00) Brazil/East (Brasilia Time)',
            'America/Noronha' => '(GMT-2:00) America/Noronha (Fernando de Noronha Time)',
            'Atlantic/South_Georgia' => '(GMT-2:00) Atlantic/South_Georgia (South Georgia Standard Time)',
            'Brazil/DeNoronha' => '(GMT-2:00) Brazil/DeNoronha (Fernando de Noronha Time)',
            'America/Scoresbysund' => '(GMT-1:00) America/Scoresbysund (Eastern Greenland Time)',
            'Atlantic/Azores' => '(GMT-1:00) Atlantic/Azores (Azores Time)',
            'Atlantic/Cape_Verde' => '(GMT-1:00) Atlantic/Cape_Verde (Cape Verde Time)',
            'Africa/Abidjan' => '(GMT+0:00) Africa/Abidjan (Greenwich Mean Time)',
            'Africa/Accra' => '(GMT+0:00) Africa/Accra (Ghana Mean Time)',
            'Africa/Bamako' => '(GMT+0:00) Africa/Bamako (Greenwich Mean Time)',
            'Africa/Banjul' => '(GMT+0:00) Africa/Banjul (Greenwich Mean Time)',
            'Africa/Bissau' => '(GMT+0:00) Africa/Bissau (Greenwich Mean Time)',
            'Africa/Casablanca' => '(GMT+0:00) Africa/Casablanca (Western European Time)',
            'Africa/Conakry' => '(GMT+0:00) Africa/Conakry (Greenwich Mean Time)',
            'Africa/Dakar' => '(GMT+0:00) Africa/Dakar (Greenwich Mean Time)',
            'Africa/El_Aaiun' => '(GMT+0:00) Africa/El_Aaiun (Western European Time)',
            'Africa/Freetown' => '(GMT+0:00) Africa/Freetown (Greenwich Mean Time)',
            'Africa/Lome' => '(GMT+0:00) Africa/Lome (Greenwich Mean Time)',
            'Africa/Monrovia' => '(GMT+0:00) Africa/Monrovia (Greenwich Mean Time)',
            'Africa/Nouakchott' => '(GMT+0:00) Africa/Nouakchott (Greenwich Mean Time)',
            'Africa/Ouagadougou' => '(GMT+0:00) Africa/Ouagadougou (Greenwich Mean Time)',
            'Africa/Sao_Tome' => '(GMT+0:00) Africa/Sao_Tome (Greenwich Mean Time)',
            'Africa/Timbuktu' => '(GMT+0:00) Africa/Timbuktu (Greenwich Mean Time)',
            'America/Danmarkshavn' => '(GMT+0:00) America/Danmarkshavn (Greenwich Mean Time)',
            'Atlantic/Canary' => '(GMT+0:00) Atlantic/Canary (Western European Time)',
            'Atlantic/Faeroe' => '(GMT+0:00) Atlantic/Faeroe (Western European Time)',
            'Atlantic/Faroe' => '(GMT+0:00) Atlantic/Faroe (Western European Time)',
            'Atlantic/Madeira' => '(GMT+0:00) Atlantic/Madeira (Western European Time)',
            'Atlantic/Reykjavik' => '(GMT+0:00) Atlantic/Reykjavik (Greenwich Mean Time)',
            'Atlantic/St_Helena' => '(GMT+0:00) Atlantic/St_Helena (Greenwich Mean Time)',
            'Europe/Belfast' => '(GMT+0:00) Europe/Belfast (Greenwich Mean Time)',
            'Europe/Dublin' => '(GMT+0:00) Europe/Dublin (Greenwich Mean Time)',
            'Europe/Guernsey' => '(GMT+0:00) Europe/Guernsey (Greenwich Mean Time)',
            'Europe/Isle_of_Man' => '(GMT+0:00) Europe/Isle_of_Man (Greenwich Mean Time)',
            'Europe/Jersey' => '(GMT+0:00) Europe/Jersey (Greenwich Mean Time)',
            'Europe/Lisbon' => '(GMT+0:00) Europe/Lisbon (Western European Time)',
            'Europe/London' => '(GMT+0:00) Europe/London (Greenwich Mean Time)',
            'Africa/Algiers' => '(GMT+1:00) Africa/Algiers (Central European Time)',
            'Africa/Bangui' => '(GMT+1:00) Africa/Bangui (Western African Time)',
            'Africa/Brazzaville' => '(GMT+1:00) Africa/Brazzaville (Western African Time)',
            'Africa/Ceuta' => '(GMT+1:00) Africa/Ceuta (Central European Time)',
            'Africa/Douala' => '(GMT+1:00) Africa/Douala (Western African Time)',
            'Africa/Kinshasa' => '(GMT+1:00) Africa/Kinshasa (Western African Time)',
            'Africa/Lagos' => '(GMT+1:00) Africa/Lagos (Western African Time)',
            'Africa/Libreville' => '(GMT+1:00) Africa/Libreville (Western African Time)',
            'Africa/Luanda' => '(GMT+1:00) Africa/Luanda (Western African Time)',
            'Africa/Malabo' => '(GMT+1:00) Africa/Malabo (Western African Time)',
            'Africa/Ndjamena' => '(GMT+1:00) Africa/Ndjamena (Western African Time)',
            'Africa/Niamey' => '(GMT+1:00) Africa/Niamey (Western African Time)',
            'Africa/Porto-Novo' => '(GMT+1:00) Africa/Porto-Novo (Western African Time)',
            'Africa/Tunis' => '(GMT+1:00) Africa/Tunis (Central European Time)',
            'Africa/Windhoek' => '(GMT+1:00) Africa/Windhoek (Western African Time)',
            'Arctic/Longyearbyen' => '(GMT+1:00) Arctic/Longyearbyen (Central European Time)',
            'Atlantic/Jan_Mayen' => '(GMT+1:00) Atlantic/Jan_Mayen (Central European Time)',
            'Europe/Amsterdam' => '(GMT+1:00) Europe/Amsterdam (Central European Time)',
            'Europe/Andorra' => '(GMT+1:00) Europe/Andorra (Central European Time)',
            'Europe/Belgrade' => '(GMT+1:00) Europe/Belgrade (Central European Time)',
            'Europe/Berlin' => '(GMT+1:00) Europe/Berlin (Central European Time)',
            'Europe/Bratislava' => '(GMT+1:00) Europe/Bratislava (Central European Time)',
            'Europe/Brussels' => '(GMT+1:00) Europe/Brussels (Central European Time)',
            'Europe/Budapest' => '(GMT+1:00) Europe/Budapest (Central European Time)',
            'Europe/Copenhagen' => '(GMT+1:00) Europe/Copenhagen (Central European Time)',
            'Europe/Gibraltar' => '(GMT+1:00) Europe/Gibraltar (Central European Time)',
            'Europe/Ljubljana' => '(GMT+1:00) Europe/Ljubljana (Central European Time)',
            'Europe/Luxembourg' => '(GMT+1:00) Europe/Luxembourg (Central European Time)',
            'Europe/Madrid' => '(GMT+1:00) Europe/Madrid (Central European Time)',
            'Europe/Malta' => '(GMT+1:00) Europe/Malta (Central European Time)',
            'Europe/Monaco' => '(GMT+1:00) Europe/Monaco (Central European Time)',
            'Europe/Oslo' => '(GMT+1:00) Europe/Oslo (Central European Time)',
            'Europe/Paris' => '(GMT+1:00) Europe/Paris (Central European Time)',
            'Europe/Podgorica' => '(GMT+1:00) Europe/Podgorica (Central European Time)',
            'Europe/Prague' => '(GMT+1:00) Europe/Prague (Central European Time)',
            'Europe/Rome' => '(GMT+1:00) Europe/Rome (Central European Time)',
            'Europe/San_Marino' => '(GMT+1:00) Europe/San_Marino (Central European Time)',
            'Europe/Sarajevo' => '(GMT+1:00) Europe/Sarajevo (Central European Time)',
            'Europe/Skopje' => '(GMT+1:00) Europe/Skopje (Central European Time)',
            'Europe/Stockholm' => '(GMT+1:00) Europe/Stockholm (Central European Time)',
            'Europe/Tirane' => '(GMT+1:00) Europe/Tirane (Central European Time)',
            'Europe/Vaduz' => '(GMT+1:00) Europe/Vaduz (Central European Time)',
            'Europe/Vatican' => '(GMT+1:00) Europe/Vatican (Central European Time)',
            'Europe/Vienna' => '(GMT+1:00) Europe/Vienna (Central European Time)',
            'Europe/Warsaw' => '(GMT+1:00) Europe/Warsaw (Central European Time)',
            'Europe/Zagreb' => '(GMT+1:00) Europe/Zagreb (Central European Time)',
            'Europe/Zurich' => '(GMT+1:00) Europe/Zurich (Central European Time)',
            'Africa/Blantyre' => '(GMT+2:00) Africa/Blantyre (Central African Time)',
            'Africa/Bujumbura' => '(GMT+2:00) Africa/Bujumbura (Central African Time)',
            'Africa/Cairo' => '(GMT+2:00) Africa/Cairo (Eastern European Time)',
            'Africa/Gaborone' => '(GMT+2:00) Africa/Gaborone (Central African Time)',
            'Africa/Harare' => '(GMT+2:00) Africa/Harare (Central African Time)',
            'Africa/Johannesburg' => '(GMT+2:00) Africa/Johannesburg (South Africa Standard Time)',
            'Africa/Kigali' => '(GMT+2:00) Africa/Kigali (Central African Time)',
            'Africa/Lubumbashi' => '(GMT+2:00) Africa/Lubumbashi (Central African Time)',
            'Africa/Lusaka' => '(GMT+2:00) Africa/Lusaka (Central African Time)',
            'Africa/Maputo' => '(GMT+2:00) Africa/Maputo (Central African Time)',
            'Africa/Maseru' => '(GMT+2:00) Africa/Maseru (South Africa Standard Time)',
            'Africa/Mbabane' => '(GMT+2:00) Africa/Mbabane (South Africa Standard Time)',
            'Africa/Tripoli' => '(GMT+2:00) Africa/Tripoli (Eastern European Time)',
            'Asia/Amman' => '(GMT+2:00) Asia/Amman (Eastern European Time)',
            'Asia/Beirut' => '(GMT+2:00) Asia/Beirut (Eastern European Time)',
            'Asia/Damascus' => '(GMT+2:00) Asia/Damascus (Eastern European Time)',
            'Asia/Gaza' => '(GMT+2:00) Asia/Gaza (Eastern European Time)',
            'Asia/Istanbul' => '(GMT+2:00) Asia/Istanbul (Eastern European Time)',
            'Asia/Jerusalem' => '(GMT+2:00) Asia/Jerusalem (Israel Standard Time)',
            'Asia/Nicosia' => '(GMT+2:00) Asia/Nicosia (Eastern European Time)',
            'Asia/Tel_Aviv' => '(GMT+2:00) Asia/Tel_Aviv (Israel Standard Time)',
            'Europe/Athens' => '(GMT+2:00) Europe/Athens (Eastern European Time)',
            'Europe/Bucharest' => '(GMT+2:00) Europe/Bucharest (Eastern European Time)',
            'Europe/Chisinau' => '(GMT+2:00) Europe/Chisinau (Eastern European Time)',
            'Europe/Helsinki' => '(GMT+2:00) Europe/Helsinki (Eastern European Time)',
            'Europe/Istanbul' => '(GMT+2:00) Europe/Istanbul (Eastern European Time)',
            'Europe/Kaliningrad' => '(GMT+2:00) Europe/Kaliningrad (Eastern European Time)',
            'Europe/Kiev' => '(GMT+2:00) Europe/Kiev (Eastern European Time)',
            'Europe/Mariehamn' => '(GMT+2:00) Europe/Mariehamn (Eastern European Time)',
            'Europe/Minsk' => '(GMT+2:00) Europe/Minsk (Eastern European Time)',
            'Europe/Nicosia' => '(GMT+2:00) Europe/Nicosia (Eastern European Time)',
            'Europe/Riga' => '(GMT+2:00) Europe/Riga (Eastern European Time)',
            'Europe/Simferopol' => '(GMT+2:00) Europe/Simferopol (Eastern European Time)',
            'Europe/Sofia' => '(GMT+2:00) Europe/Sofia (Eastern European Time)',
            'Europe/Tallinn' => '(GMT+2:00) Europe/Tallinn (Eastern European Time)',
            'Europe/Tiraspol' => '(GMT+2:00) Europe/Tiraspol (Eastern European Time)',
            'Europe/Uzhgorod' => '(GMT+2:00) Europe/Uzhgorod (Eastern European Time)',
            'Europe/Vilnius' => '(GMT+2:00) Europe/Vilnius (Eastern European Time)',
            'Europe/Zaporozhye' => '(GMT+2:00) Europe/Zaporozhye (Eastern European Time)',
            'Africa/Addis_Ababa' => '(GMT+3:00) Africa/Addis_Ababa (Eastern African Time)',
            'Africa/Asmara' => '(GMT+3:00) Africa/Asmara (Eastern African Time)',
            'Africa/Asmera' => '(GMT+3:00) Africa/Asmera (Eastern African Time)',
            'Africa/Dar_es_Salaam' => '(GMT+3:00) Africa/Dar_es_Salaam (Eastern African Time)',
            'Africa/Djibouti' => '(GMT+3:00) Africa/Djibouti (Eastern African Time)',
            'Africa/Kampala' => '(GMT+3:00) Africa/Kampala (Eastern African Time)',
            'Africa/Khartoum' => '(GMT+3:00) Africa/Khartoum (Eastern African Time)',
            'Africa/Mogadishu' => '(GMT+3:00) Africa/Mogadishu (Eastern African Time)',
            'Africa/Nairobi' => '(GMT+3:00) Africa/Nairobi (Eastern African Time)',
            'Antarctica/Syowa' => '(GMT+3:00) Antarctica/Syowa (Syowa Time)',
            'Asia/Aden' => '(GMT+3:00) Asia/Aden (Arabia Standard Time)',
            'Asia/Baghdad' => '(GMT+3:00) Asia/Baghdad (Arabia Standard Time)',
            'Asia/Bahrain' => '(GMT+3:00) Asia/Bahrain (Arabia Standard Time)',
            'Asia/Kuwait' => '(GMT+3:00) Asia/Kuwait (Arabia Standard Time)',
            'Asia/Qatar' => '(GMT+3:00) Asia/Qatar (Arabia Standard Time)',
            'Europe/Moscow' => '(GMT+3:00) Europe/Moscow (Moscow Standard Time)',
            'Europe/Volgograd' => '(GMT+3:00) Europe/Volgograd (Volgograd Time)',
            'Indian/Antananarivo' => '(GMT+3:00) Indian/Antananarivo (Eastern African Time)',
            'Indian/Comoro' => '(GMT+3:00) Indian/Comoro (Eastern African Time)',
            'Indian/Mayotte' => '(GMT+3:00) Indian/Mayotte (Eastern African Time)',
            'Asia/Tehran' => '(GMT+3:30) Asia/Tehran (Iran Standard Time)',
            'Asia/Baku' => '(GMT+4:00) Asia/Baku (Azerbaijan Time)',
            'Asia/Dubai' => '(GMT+4:00) Asia/Dubai (Gulf Standard Time)',
            'Asia/Muscat' => '(GMT+4:00) Asia/Muscat (Gulf Standard Time)',
            'Asia/Tbilisi' => '(GMT+4:00) Asia/Tbilisi (Georgia Time)',
            'Asia/Yerevan' => '(GMT+4:00) Asia/Yerevan (Armenia Time)',
            'Europe/Samara' => '(GMT+4:00) Europe/Samara (Samara Time)',
            'Indian/Mahe' => '(GMT+4:00) Indian/Mahe (Seychelles Time)',
            'Indian/Mauritius' => '(GMT+4:00) Indian/Mauritius (Mauritius Time)',
            'Indian/Reunion' => '(GMT+4:00) Indian/Reunion (Reunion Time)',
            'Asia/Kabul' => '(GMT+4:30) Asia/Kabul (Afghanistan Time)',
            'Asia/Aqtau' => '(GMT+5:00) Asia/Aqtau (Aqtau Time)',
            'Asia/Aqtobe' => '(GMT+5:00) Asia/Aqtobe (Aqtobe Time)',
            'Asia/Ashgabat' => '(GMT+5:00) Asia/Ashgabat (Turkmenistan Time)',
            'Asia/Ashkhabad' => '(GMT+5:00) Asia/Ashkhabad (Turkmenistan Time)',
            'Asia/Dushanbe' => '(GMT+5:00) Asia/Dushanbe (Tajikistan Time)',
            'Asia/Karachi' => '(GMT+5:00) Asia/Karachi (Pakistan Time)',
            'Asia/Oral' => '(GMT+5:00) Asia/Oral (Oral Time)',
            'Asia/Samarkand' => '(GMT+5:00) Asia/Samarkand (Uzbekistan Time)',
            'Asia/Tashkent' => '(GMT+5:00) Asia/Tashkent (Uzbekistan Time)',
            'Asia/Yekaterinburg' => '(GMT+5:00) Asia/Yekaterinburg (Yekaterinburg Time)',
            'Indian/Kerguelen' => '(GMT+5:00) Indian/Kerguelen (French Southern & Antarctic Lands Time)',
            'Indian/Maldives' => '(GMT+5:00) Indian/Maldives (Maldives Time)',
            'Asia/Calcutta' => '(GMT+5:30) Asia/Calcutta (India Standard Time)',
            'Asia/Colombo' => '(GMT+5:30) Asia/Colombo (India Standard Time)',
            'Asia/Kolkata' => '(GMT+5:30) Asia/Kolkata (India Standard Time)',
            'Asia/Katmandu' => '(GMT+5:45) Asia/Katmandu (Nepal Time)',
            'Antarctica/Mawson' => '(GMT+6:00) Antarctica/Mawson (Mawson Time)',
            'Antarctica/Vostok' => '(GMT+6:00) Antarctica/Vostok (Vostok Time)',
            'Asia/Almaty' => '(GMT+6:00) Asia/Almaty (Alma-Ata Time)',
            'Asia/Bishkek' => '(GMT+6:00) Asia/Bishkek (Kirgizstan Time)',
            'Asia/Dacca' => '(GMT+6:00) Asia/Dacca (Bangladesh Time)',
            'Asia/Dhaka' => '(GMT+6:00) Asia/Dhaka (Bangladesh Time)',
            'Asia/Novosibirsk' => '(GMT+6:00) Asia/Novosibirsk (Novosibirsk Time)',
            'Asia/Omsk' => '(GMT+6:00) Asia/Omsk (Omsk Time)',
            'Asia/Qyzylorda' => '(GMT+6:00) Asia/Qyzylorda (Qyzylorda Time)',
            'Asia/Thimbu' => '(GMT+6:00) Asia/Thimbu (Bhutan Time)',
            'Asia/Thimphu' => '(GMT+6:00) Asia/Thimphu (Bhutan Time)',
            'Indian/Chagos' => '(GMT+6:00) Indian/Chagos (Indian Ocean Territory Time)',
            'Asia/Rangoon' => '(GMT+6:30) Asia/Rangoon (Myanmar Time)',
            'Indian/Cocos' => '(GMT+6:30) Indian/Cocos (Cocos Islands Time)',
            'Antarctica/Davis' => '(GMT+7:00) Antarctica/Davis (Davis Time)',
            'Asia/Bangkok' => '(GMT+7:00) Asia/Bangkok (Indochina Time)',
            'Asia/Ho_Chi_Minh' => '(GMT+7:00) Asia/Ho_Chi_Minh (Indochina Time)',
            'Asia/Hovd' => '(GMT+7:00) Asia/Hovd (Hovd Time)',
            'Asia/Jakarta' => '(GMT+7:00) Asia/Jakarta (West Indonesia Time)',
            'Asia/Krasnoyarsk' => '(GMT+7:00) Asia/Krasnoyarsk (Krasnoyarsk Time)',
            'Asia/Phnom_Penh' => '(GMT+7:00) Asia/Phnom_Penh (Indochina Time)',
            'Asia/Pontianak' => '(GMT+7:00) Asia/Pontianak (West Indonesia Time)',
            'Asia/Saigon' => '(GMT+7:00) Asia/Saigon (Indochina Time)',
            'Asia/Vientiane' => '(GMT+7:00) Asia/Vientiane (Indochina Time)',
            'Indian/Christmas' => '(GMT+7:00) Indian/Christmas (Christmas Island Time)',
            'Antarctica/Casey' => '(GMT+8:00) Antarctica/Casey (Western Standard Time (Australia))',
            'Asia/Brunei' => '(GMT+8:00) Asia/Brunei (Brunei Time)',
            'Asia/Choibalsan' => '(GMT+8:00) Asia/Choibalsan (Choibalsan Time)',
            'Asia/Chongqing' => '(GMT+8:00) Asia/Chongqing (China Standard Time)',
            'Asia/Chungking' => '(GMT+8:00) Asia/Chungking (China Standard Time)',
            'Asia/Harbin' => '(GMT+8:00) Asia/Harbin (China Standard Time)',
            'Asia/Hong_Kong' => '(GMT+8:00) Asia/Hong_Kong (Hong Kong Time)',
            'Asia/Irkutsk' => '(GMT+8:00) Asia/Irkutsk (Irkutsk Time)',
            'Asia/Kashgar' => '(GMT+8:00) Asia/Kashgar (China Standard Time)',
            'Asia/Kuala_Lumpur' => '(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Time)',
            'Asia/Kuching' => '(GMT+8:00) Asia/Kuching (Malaysia Time)',
            'Asia/Macao' => '(GMT+8:00) Asia/Macao (China Standard Time)',
            'Asia/Macau' => '(GMT+8:00) Asia/Macau (China Standard Time)',
            'Asia/Makassar' => '(GMT+8:00) Asia/Makassar (Central Indonesia Time)',
            'Asia/Manila' => '(GMT+8:00) Asia/Manila (Philippines Time)',
            'Asia/Shanghai' => '(GMT+8:00) Asia/Shanghai (China Standard Time)',
            'Asia/Singapore' => '(GMT+8:00) Asia/Singapore (Singapore Time)',
            'Asia/Taipei' => '(GMT+8:00) Asia/Taipei (China Standard Time)',
            'Asia/Ujung_Pandang' => '(GMT+8:00) Asia/Ujung_Pandang (Central Indonesia Time)',
            'Asia/Ulaanbaatar' => '(GMT+8:00) Asia/Ulaanbaatar (Ulaanbaatar Time)',
            'Asia/Ulan_Bator' => '(GMT+8:00) Asia/Ulan_Bator (Ulaanbaatar Time)',
            'Asia/Urumqi' => '(GMT+8:00) Asia/Urumqi (China Standard Time)',
            'Australia/Perth' => '(GMT+8:00) Australia/Perth (Western Standard Time (Australia))',
            'Australia/West' => '(GMT+8:00) Australia/West (Western Standard Time (Australia))',
            'Australia/Eucla' => '(GMT+8:45) Australia/Eucla (Central Western Standard Time (Australia))',
            'Asia/Dili' => '(GMT+9:00) Asia/Dili (Timor-Leste Time)',
            'Asia/Jayapura' => '(GMT+9:00) Asia/Jayapura (East Indonesia Time)',
            'Asia/Pyongyang' => '(GMT+9:00) Asia/Pyongyang (Korea Standard Time)',
            'Asia/Seoul' => '(GMT+9:00) Asia/Seoul (Korea Standard Time)',
            'Asia/Tokyo' => '(GMT+9:00) Asia/Tokyo (Japan Standard Time)',
            'Asia/Yakutsk' => '(GMT+9:00) Asia/Yakutsk (Yakutsk Time)',
            'Australia/Adelaide' => '(GMT+9:30) Australia/Adelaide (Central Standard Time (South Australia))',
            'Australia/Broken_Hill' => '(GMT+9:30) Australia/Broken_Hill (Central Standard Time (South Australia/New South Wales))',
            'Australia/Darwin' => '(GMT+9:30) Australia/Darwin (Central Standard Time (Northern Territory))',
            'Australia/North' => '(GMT+9:30) Australia/North (Central Standard Time (Northern Territory))',
            'Australia/South' => '(GMT+9:30) Australia/South (Central Standard Time (South Australia))',
            'Australia/Yancowinna' => '(GMT+9:30) Australia/Yancowinna (Central Standard Time (South Australia/New South Wales))',
            'Antarctica/DumontDUrville' => '(GMT+10:00) Antarctica/DumontDUrville (Dumont-d\'Urville Time)',
            'Asia/Sakhalin' => '(GMT+10:00) Asia/Sakhalin (Sakhalin Time)',
            'Asia/Vladivostok' => '(GMT+10:00) Asia/Vladivostok (Vladivostok Time)',
            'Australia/ACT' => '(GMT+10:00) Australia/ACT (Eastern Standard Time (New South Wales))',
            'Australia/Brisbane' => '(GMT+10:00) Australia/Brisbane (Eastern Standard Time (Queensland))',
            'Australia/Canberra' => '(GMT+10:00) Australia/Canberra (Eastern Standard Time (New South Wales))',
            'Australia/Currie' => '(GMT+10:00) Australia/Currie (Eastern Standard Time (New South Wales))',
            'Australia/Hobart' => '(GMT+10:00) Australia/Hobart (Eastern Standard Time (Tasmania))',
            'Australia/Lindeman' => '(GMT+10:00) Australia/Lindeman (Eastern Standard Time (Queensland))',
            'Australia/Melbourne' => '(GMT+10:00) Australia/Melbourne (Eastern Standard Time (Victoria))',
            'Australia/NSW' => '(GMT+10:00) Australia/NSW (Eastern Standard Time (New South Wales))',
            'Australia/Queensland' => '(GMT+10:00) Australia/Queensland (Eastern Standard Time (Queensland))',
            'Australia/Sydney' => '(GMT+10:00) Australia/Sydney (Eastern Standard Time (New South Wales))',
            'Australia/Tasmania' => '(GMT+10:00) Australia/Tasmania (Eastern Standard Time (Tasmania))',
            'Australia/Victoria' => '(GMT+10:00) Australia/Victoria (Eastern Standard Time (Victoria))',
            'Australia/LHI' => '(GMT+10:30) Australia/LHI (Lord Howe Standard Time)',
            'Australia/Lord_Howe' => '(GMT+10:30) Australia/Lord_Howe (Lord Howe Standard Time)',
            'Asia/Magadan' => '(GMT+11:00) Asia/Magadan (Magadan Time)',
            'Antarctica/McMurdo' => '(GMT+12:00) Antarctica/McMurdo (New Zealand Standard Time)',
            'Antarctica/South_Pole' => '(GMT+12:00) Antarctica/South_Pole (New Zealand Standard Time)',
            'Asia/Anadyr' => '(GMT+12:00) Asia/Anadyr (Anadyr Time)',
            'Asia/Kamchatka' => '(GMT+12:00) Asia/Kamchatka (Petropavlovsk-Kamchatski Time)'
        );

        $html = '<option value="">Select Timezone</option>';
        foreach ($timezones as $keys => $values) {
            $html .= '<option value="' . $keys . '">' . $values . '</option>';
        }

        return $html;
    }


}

?>