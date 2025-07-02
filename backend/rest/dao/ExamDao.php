<?php

class ExamDao {

    private $connection;

    /**
     * constructor of dao class
     */
    public function __construct() {
    try {
        $servername = "localhost";     
        $username   = "root"; 
        $password   = "root"; 
        $dbname     = "exam"; 
        $port       = "3306";          

        $this->connection = new PDO(
            "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}


    /** TODO
     * Implement DAO method used to get customer information
     */
    public function get_customers(){
      $stmt = $this->connection->query("SELECT * FROM customers");
        $customers = $stmt->fetchAll();
        return $customers;

    }

    /** TODO
     * Implement DAO method used to get customer meals
     */
    public function get_customer_meals($customer_id) {
       $stmt = $this->connection->prepare("
        SELECT f.name AS food_name, f.brand AS food_brand, m.created_at AS meal_date
        FROM meals m
        JOIN foods f ON m.food_id = f.id
        WHERE m.customer_id = :customer_id
    ");
    $stmt->execute([':customer_id' => $customer_id]);
    return $stmt->fetchAll();
    }

    /** TODO
     * Implement DAO method used to save customer data
     */
    public function add_customer($data){
         $stmt = $this->connection->prepare(
            "INSERT INTO customers (first_name, last_name, birth_date)
             VALUES (:first_name, :last_name, :birth_date)"
        );
        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':birth_date' => $data['birth_date']
        ]);
        echo "Customer added successfully.";
    }

    /** TODO
     * Implement DAO method used to get foods report
     */
    public function get_foods_report(){
       $foodsStmt = $this->connection->query("SELECT * FROM foods");
    $foods = $foodsStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($foods as &$food) {
        $sql = "
            SELECT n.name, fn.quantity
            FROM food_nutrients fn
            JOIN nutrients n ON fn.nutrient_id = n.id
            WHERE fn.food_id = :food_id
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['food_id' => $food['id']]);
        $nutrients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $food['energy'] = 0;
        $food['protein'] = 0;
        $food['fat'] = 0;
        $food['fiber'] = 0;
        $food['carbs'] = 0;

        
        foreach ($nutrients as $nutrient) {
            $name = strtolower($nutrient['name']);
            if (in_array($name, ['energy', 'protein', 'fat', 'fiber', 'carb'])) {
                $food[$name] = (float)$nutrient['quantity'];
            }
        }
    }
    return $foods;
}
}
$dao = new ExamDao();
//$customers = $dao->get_customers();
//echo "<pre>";
//print_r($customers);
//echo "</pre>";


//$dao->add_customer([
//    'first_name' => 'Amna',
//    'last_name'  => 'Mutap',
//    'birth_date' => '2004-01-18',
//    'status'     => 'active'
//]);


//print_r($dao->get_customer_meals(1));


//print_r($dao->get_foods_report());
?>
