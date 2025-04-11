<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="recepti.css">
    <link rel="icon" type="image/x-icon" href="/slike/recepti_icon.png">
    <title>Recepti</title>
</head>
<body>

<div class="nav">
    <a class="gumb" href="recepti.php">Domača stran</a>
    <a class="gumb" href="nov_recept.html">Nov recept</a>
    <a class="gumb" href="uredi_recept.php">Uredi recept</a>
</div>

<div id="besedilo">
    <h1 class="naslov">Recepti</h1>
    
    <!--  Search Bar -->
    <input type="text" id="search" placeholder="Išči recept..." onkeyup="najdiRecept()">
    
    <ul id="recepti">
        <?php
        // DEFINE FUNCTIONS
        function readJSON() {
            $filePath = "recepti.json";
        
            if (!file_exists($filePath)) {
                file_put_contents($filePath, json_encode([]));
            }
        
            $data = file_get_contents($filePath);
        
            $decodedData = json_decode($data, true);
        
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "Error decoding JSON: " . json_last_error_msg();
                return [];
            }
        
            return $decodedData;
        }
        
        function toJSON($naslov, $slika, $sestavine, $postopek, $opomba) {
            $podatki = readJSON();
            if($opomba == ""){
                $opomba = "n";
            }
            if($slika == ""){
                $slika = "n";
            }
            $podatki[$naslov] = [
                "slika" => $slika, 
                "sestavine" => $sestavine, 
                "koraki" => $postopek, 
                "opomba" => $opomba
            ];
            ksort($podatki, SORT_NATURAL | SORT_FLAG_CASE);
            file_put_contents("recepti.json", json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        function urediJSON($naslov1, $naslov2, $slika, $sestavine, $postopek, $opomba) {
            $podatki = readJSON();
            if($opomba == ""){
                $opomba = "n";
            }
            if($slika == ""){
                $slika = "n";
            }
            unset($podatki[$naslov1]);
            $podatki[$naslov2] = [
                "slika" => $slika, 
                "sestavine" => $sestavine, 
                "koraki" => $postopek, 
                "opomba" => $opomba
            ];
            ksort($podatki, SORT_NATURAL | SORT_FLAG_CASE);
            file_put_contents("recepti.json", json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        function pridobiPodatke() {
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $naslov = $_POST["naslov"] ?? "";
                $slika = $_POST["slika"] ?? "";
                $sestavinaString = $_POST["sestavine"] ?? "";
                $postopekString = $_POST["postopek"] ?? "";
                $opomba = $_POST["opomba"] ?? "";
        
                $sestavine = array_filter(array_map('trim', explode("\n", $sestavinaString)));
                $postopek = array_filter(array_map('trim', explode("\n", $postopekString)));
        
                toJSON($naslov, $slika, $sestavine, $postopek, $opomba);
            }
        }
        function urediPodatke() {
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $naslov = $_POST["naslov"] ?? "";
                $submit = $_POST['recepti'] ?? "";
                $slika = $_POST["slika"] ?? "";
                $sestavinaString = $_POST["sestavine"] ?? "";
                $postopekString = $_POST["postopek"] ?? "";
                $opomba = $_POST["opomba"] ?? "";
        
                $sestavine = array_filter(array_map('trim', explode("\n", $sestavinaString)));
                $postopek = array_filter(array_map('trim', explode("\n", $postopekString)));
        
                urediJSON($submit, $naslov, $slika, $sestavine, $postopek, $opomba);
            }
        }
        function updateRecepti() {
            $podatki = readJSON();
        
            foreach ($podatki as $naslov => $recept) {
                $slika = $recept['slika'];
                $sestavineList = $recept['sestavine'];
                $korakiList = $recept['koraki'];
                $opomba = $recept['opomba'];
        
                $slikaHTML = strtolower($slika) === "n" ? "" : "<p><img src='$slika' alt='$naslov' style='float:right'></p>";
                $opombaHTML = strtolower($opomba) === "n" ? "" : "<h2 class='manjsiNaslov'>Opombe:</h2><p id='opombe'>$opomba</p>";
        
                $sestavineHTML = "<ul id='sestavine'>\n" . implode("\n", array_map(fn($s) => "<li>" . strip_tags($s, '<b><i><u><br>') . "</li>", $sestavineList)) . "\n</ul>";
                $korakiHTML = "<ol id='koraki'>\n" . implode("\n", array_map(fn($s) => "<li>" . strip_tags($s, '<b><i><u><br>') . "</li>", $korakiList)) . "\n</ol>";
        
                $html = "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <title>$naslov</title>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                    <link rel='icon' type='image/x-icon' href='../slike/recepti_icon.png'>
                    <link rel='stylesheet' href='../recepti.css'>
                </head>
                <body>
                    <div class='nav'>
                        <a class='gumb' href='../recepti.php'>Domača stran</a>
                        <a class='gumb' href='../nov_recept.html'>Nov recept</a>
                        <a class='gumb' href='../uredi_recept.php'>Uredi recept</a>
                    </div>
                    <div id='besedilo'>
                        <h1 id='naslov'>$naslov</h1>
                        $slikaHTML
                        <h2 class='manjsiNaslov'>Sestavine</h2>
                        $sestavineHTML
                        <h2 class='manjsiNaslov'>Postopek:</h2>
                        $korakiHTML
                        $opombaHTML
                    </div>
                </body>
                </html>";
        
                file_put_contents("recepti/$naslov.html", $html);
            }
        }
        //MAIN RUN
        $naslov = $_POST["naslov"] ?? null;
        $submit = $_POST['recepti'] ?? null;
        if ($naslov !== null) {
            if ($submit !== null){
                urediPodatke();
                updateRecepti();
            }else{
                pridobiPodatke();
                updateRecepti();
            }
        }
        $podatki = json_decode(file_get_contents("recepti.json"), true);
        if ($podatki) {
            foreach ($podatki as $naslov => $recept) {
                echo "<li class='recept'><a href='recepti/$naslov.html'>$naslov</a></li>\n";
            }
        } else {
            echo "<li>Ni receptov.</li>";
        }
        ?>
    </ul>
</div>

<script>
function najdiRecept() {
    let query = document.getElementById("search").value.toLowerCase();
    let items = document.querySelectorAll(".recept");

    items.forEach(item => {
        let text = item.textContent.toLowerCase();
        item.style.display = text.includes(query) ? "block" : "none";
    });
}
</script>

</body>
</html>