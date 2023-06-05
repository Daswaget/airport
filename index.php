<?php
ini_set('display_errors', 0);
require_once 'sql/migration.php';
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "airport";
$conn = mysqli_connect($servername, $username, $password, $dbname);
abstract class Airplane
{
    public $is_flying;
    public $is_parked;
    public $is_loaded;
    public function __construct($name, $max_speed)
    {
        $this->target = $name;
        $this->name = $name;
        $this->max_speed = $max_speed;
        echo "<p>Название самолёта: " . $name . ", Максимальная скорость: " . $max_speed . "</p>";
    }
    public function Status($name)
    {
        if ($this->is_flying && $this->is_loaded)
            echo "<p>Статус " . $name . ": Полёт, Пассажиры на борту</p>";
        else if ($this->is_parked && $this->is_loaded)
            echo "<p>Статус " . $name . ": Находится на стоянке, Пассажиры на борту</p>";
        else if ($this->is_loaded)
            echo "<p>Статус " . $name . ": Приземлён, Пассажиры на борту</p>";
        else if ($this->is_parked)
            echo "<p>Статус " . $name . ": Находится на стоянке, Пассажиров нет</p>";
        else if ($this->is_flying)
            echo "<p>Статус " . $name . ": Полёт, Пассажиров нет</p>";
        else
            echo "<p>Статус " . $name . ": Приземлён, Пассажиров нет</p>";
    }
    public function Takeoff($name)
    {
        if ($this->is_parked)
            echo "<p>" . $name . " не может взлететь, так как находится на стоянке</p>";
        else if ($this->is_flying)
            echo "<p>" . $name . " уже в небе</p>";
        else {
            echo "<p>" . $name . " взлетает</p>";
            $this->is_flying = true;
        }
    }
    public function Landing($name)
    {
        if ($this->is_flying) {
            echo "<p>" . $name . " приземляется</p>";
            $this->is_flying = false;
        } else
            echo "<p>" . $name . " уже приземлён</p>";
    }
}


class Tu154 extends Airplane
{
    public function __construct()
    {
        $this->transport = true;
    }
}

class MiG extends Airplane
{
    public function __construct()
    {
        $this->transport = false;
    }
    public function Attack($name, $target)
    {
        if (trim($name) == trim($target))
            echo "Атакующий не может уничтожить самого себя";
        else if (trim($name) != "" && trim($target) != "") {
            echo "<p>" . $name . " атакует и уничтожает " . $target . "</p>";
            unset($target);
        } else if ($name != "")
            echo "Атакующий не найден";
        else if ($target != "")
            echo "Цель не обнаружена";
        else
            echo "Атакующий и цель не обнаружены";
    }
}

class Airport
{
    public function __construct($name)
    {
        $this->name = $name;
        echo "<p>Название аэропорта: " . $name . "</p>";
    }

    // Создание самолётов
    public function Create_Tu154_aggregation(Tu154 $tu154)
    {
        $this->$tu154 = $tu154;
    }
    public function Create_MiG_aggregation(MiG $mig)
    {
        $this->$mig = $mig;
    }
    public function Create_Airplane($airplane_type, $airplane_name, $max_speed)
    {
        if (trim($airplane_name) != "" && trim($airplane_type) != "" && trim($max_speed) != "") {
            switch (mb_strtolower($airplane_type)) {
                case "mig":
                    $this->$airplane_name = new MiG($airplane_name, $max_speed);
                    echo "Самолёт " . $airplane_name . " типа MiG и максимальной скоростью " . $max_speed . " Создан<br>";
                    break;
                case "tu154":
                    $this->$airplane_name = new Tu154($airplane_name, $max_speed);
                    echo "Самолёт " . $airplane_name . " типа Tu154 и максимальной скоростью " . $max_speed . " Создан<br>";
                    break;
                case "tu":
                    $this->$airplane_name = new Tu154($airplane_name, $max_speed);
                    echo "Самолёт " . $airplane_name . " типа Tu154 и максимальной скоростью " . $max_speed . " Создан<br>";
                    break;
                default:
                    echo "Самолёта данного вида не найдено";
                    break;
            }
        }
    }

    // Принять самолёт
    public function Landing($airplane_name)
    {
        $this->$airplane_name->Landing($airplane_name);
    }

    // Самолёт взлетает
    public function Takeoff($airplane_name)
    {
        $this->$airplane_name->Takeoff($airplane_name);
    }

    // Атака
    public function Attack($airplane_name, $target)
    {
        $this->$airplane_name->Attack($airplane_name, $target);
    }

    // Поставить самолёт на стоянку
    public function Park($airplane_name)
    {
        if (trim($airplane_name) != "") {
            if ($this->$airplane_name->is_flying)
                echo "<p>" . $airplane_name . " не может уйти на стоянку, так как находится в небе</p>";
            else if ($this->$airplane_name->is_parked)
                echo "<p>" . $airplane_name . " уже находится на стоянке</p>";
            else {
                echo "<p>" . $airplane_name . " ушёл на стоянку</p>";
                $this->$airplane_name->is_parked = true;
            }
        }
    }

    // Подготовить самолёт к взлёту
    public function Ready($airplane_name)
    {
        if (trim($airplane_name) != "") {
            if ($this->$airplane_name->is_flying)
                echo "<p>" . $airplane_name . " не может подготовится к взлёту, так как находится в небе</p>";
            else if ($this->$airplane_name->is_parked) {
                echo "<p>" . $airplane_name . " готов взлетать</p>";
                $this->$airplane_name->is_parked = false;
            } else
                echo "<p>" . $airplane_name . " уже подготовлен к взлёту</p>";
        }
    }

    // Посадить пассажиров
    public function Load($airplane_name)
    {
        if (trim($airplane_name) != "") {
            if (!$this->$airplane_name->transport)
                echo "<p>" . $airplane_name . " невозможно засадить пассажирами, так как самолёт не является пассажирским</p>";
            else if ($this->$airplane_name->is_loaded && $this->$airplane_name->is_parked)
                echo "<p>" . $airplane_name . " уже имеет пассажиров</p>";
            else if ($this->$airplane_name->is_parked) {
                echo "<p>" . $airplane_name . " теперь засажен пассажирами</p>";
                $this->$airplane_name->is_loaded = true;
            } else if ($this->$airplane_name->is_flying)
                echo "<p>" . $airplane_name . " невозможно засадить пассажирами, так как самолёт находится в небе</p>";
            else
                echo "<p>" . $airplane_name . " невозможно засадить пассажирами, так как не находится на стоянке</p>";
        }
    }

    // Посадить пассажиров
    public function Unload($airplane_name)
    {
        if (trim($airplane_name) != "") {
            if ($this->$airplane_name->is_loaded && $this->$airplane_name->is_parked) {
                echo "<p>" . $airplane_name . " освободился от пассажиров</p>";
                $this->$airplane_name->is_loaded = false;
            } else if ($this->$airplane_name->is_parked)
                echo "<p>" . $airplane_name . " уже пуст</p>";
            else if ($this->$airplane_name->is_flying)
                echo "<p>Пассажиры не могут выйти из " . $airplane_name . ", так как самолёт находится в небе</p>";
            else
                echo "<p>Пассажиры не могут выйти из " . $airplane_name . ", так как самолёт не находится на стоянке</p>";
        }
    }

    public function Status($airplane_name)
    {
        $this->$airplane_name->Status($airplane_name);
    }

    public function Is_flying_sql($airplane_name) {
        $this->$airplane_name->is_flying = true;
    }
    public function Is_parked_sql($airplane_name) {
        $this->$airplane_name->is_parked = true;
    }
    public function Is_loaded_sql($airplane_name) {
        $this->$airplane_name->is_loaded = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Airport</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.1.0.js"></script>
</head>
<body>
    <br />
    <form method="post">
        <input type="text" name="text" class="button" placeholder="Название самолёта..." />
        <input type="submit" name="Takeoff" class="button" value="Взлёт" />
        <input type="submit" name="Landing" class="button" value="Посадка" />
        <input type="submit" name="Ready" class="button" value="Готовность взлетать" />
        <input type="submit" name="Park" class="button" value="Отправить на стоянку" />
        <input type="submit" name="Load" class="button" value="Посадить пассажиров" />
        <input type="submit" name="Unload" class="button" value="Высадить пассажиров" />
        <input type="submit" name="Status" class="button" value="Статус" />
    </form>
    <br />
    <form method="post">
        <input type="text" name="name" class="button" placeholder="Название самолёта..." />
        <input type="text" name="type" class="button" placeholder="Тип (MiG или Tu154)" />
        <input type="text" name="speed" class="button" placeholder="Скорость" />
        <input type="submit" name="CreateAirplane" class="button" value="Создать самолёт" />
    </form>
    <br />
    <form method="post">
        <input type="text" name="attacker" class="button" placeholder="Атакующий (MiG)" />
        <input type="text" name="target" class="button" placeholder="Цель" />
        <input type="submit" name="Attack" class="button" value="Атаковать" />
    </form>

    <?php

        //$Tu154_1 = new Tu154("Tu154_1", 150);
        //$Tu154_1->Takeoff();
        //$Tu154_1->Is_flying();

        //$Tu154_2 = new Tu154("Tu154_2", 150);
        //$Tu154_2->Landing();
        //$Tu154_2->Is_flying();

        //$MiG_1 = new MiG("MiG_1", 200);
        //$MiG_1->Takeoff();
        //$MiG_1->Is_flying();
        //$MiG_1->Attack();

        //$MiG_2 = new MiG("MiG_2", 200);
        //$MiG_2->Is_flying();

        $Airport_1 = new Airport("Airport");


        $sql = "SELECT *
                FROM airplanes";

        $result = mysqli_query($conn, $sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $airplane_name = $row["name"];
                $airplane_type = $row["type"];
                $airplane_speed = $row["speed"];
                $airplane_is_flying = $row["is_flying"];
                $airplane_is_parked = $row["is_parked"];
                $airplane_is_loaded = $row["is_loaded"];
                echo $airplane_name;
                echo $airplane_is_flying;
                echo $airplane_is_parked;
                echo $airplane_is_loaded;
                $Airport_1->Create_Airplane("$airplane_type", "$airplane_name", $airplane_speed);
                if ($airplane_is_flying == 1) {
                    $Airport_1->Is_flying_sql($airplane_name);
                } else {
                if ($airplane_is_parked == 1) {
                    $Airport_1->Is_parked_sql($airplane_name);
                }
                if ($airplane_is_loaded == 1) {
                    $Airport_1->Is_loaded_sql($airplane_name);
                }
                }
                $Airport_1->Status($airplane_name);
                echo "<br>";
            }
        }

    //if (isset($_POST['Create_Airplane']))
    //    $Airport_1->Load("$obj");
    if (isset($_POST['Takeoff']) && $_POST['text']) {
        $name = trim($_POST['text']);
        $Airport_1->Takeoff("$name");
        $sql = "UPDATE airplanes SET is_flying='1' WHERE name='$name' AND is_parked='0'";
        header("refresh: 1");

    } else if (isset($_POST['Landing']) && $_POST['text']) {
        $name = trim($_POST['text']);
        $Airport_1->Landing("$name");
        $sql = "UPDATE airplanes SET is_flying='0' WHERE name='$name' AND is_parked='0'";
        header("refresh: 1");

    } else if (isset($_POST['Ready']) && $_POST['text']) {
        $name = trim($_POST['text']);
        $Airport_1->Ready("$name");
        $sql = "UPDATE airplanes SET is_parked='0' WHERE name='$name' AND is_flying='0'";
        header("refresh: 1");

    } else if (isset($_POST['Park']) && $_POST['text']) {
        $name = trim($_POST['text']);
        $Airport_1->Park("$name");
        $sql = "UPDATE airplanes SET is_parked='1' WHERE name='$name' AND is_flying='0'";
        header("refresh: 1");

    } else if (isset($_POST['Load']) && $_POST['text']) {
        $name = trim($_POST['text']);
        $Airport_1->Load("$name");
        $sql = "UPDATE airplanes SET is_loaded='1' WHERE name='$name' AND is_flying='0' AND type='Tu154'";
        header("refresh: 1");

    } else if (isset($_POST['Unload']) && $_POST['text']) {
        $name = trim($_POST['text']);
        $Airport_1->Unload("$name");
        $sql = "UPDATE airplanes SET is_loaded='0' WHERE name='$name' AND is_flying='0'";
        header("refresh: 1");

    } else if (isset($_POST['Status']) && $_POST['text']) {
        $name = trim($_POST['text']);
        $Airport_1->Status($name);
        header("refresh: 1");

    } else if (isset($_POST['Attack']) && $_POST['attacker'] && $_POST['target']) {
        $attacker = trim($_POST['attacker']);
        $target = trim($_POST['target']);
        $Airport_1->Attack($attacker, $target);
        $sql = "DELETE FROM airplanes WHERE name='$target'";
        header("refresh: 1");

    } else if (isset($_POST['CreateAirplane']) && $_POST['name'] != "" && trim($_POST['type']) != "" && trim($_POST['speed']) != "") {
        $name = trim($_POST['name']);
        $type = trim($_POST['type']);
        $speed = trim($_POST['speed']);
        $Airport_1->Create_Airplane("$type", "$name", $speed);
        switch (mb_strtolower($type)) {
                    case "mig":
                        $sql = "INSERT INTO airplanes(name, type, speed) VALUES('$name', 'MiG', '$speed')";
                        break;
                    case "tu154":
                        $sql = "INSERT INTO airplanes(name, type, speed) VALUES('$name', 'Tu154', '$speed')";
                        break;
                    case "tu":
                        $sql = "INSERT INTO airplanes(name, type, speed) VALUES('$name', 'Tu154', '$speed')";
                        break;
                    }
        header("refresh: 1");
    } else
        echo "Введете данные";
    mysqli_query($conn, $sql);
    mysqli_close($conn);
    ?>
</body>
</html>